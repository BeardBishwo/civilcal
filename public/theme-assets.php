<?php
// Secure theme asset proxy to serve files located outside the document root.
$basePath = dirname(__DIR__);
$themesRoot = realpath($basePath . DIRECTORY_SEPARATOR . 'themes');

if (!$themesRoot) {
    http_response_code(500);
    exit('Themes directory missing');
}

$relativePath = $_GET['path'] ?? '';
$relativePath = str_replace("\0", '', $relativePath);
$relativePath = ltrim($relativePath, '/');

if ($relativePath === '') {
    http_response_code(400);
    exit('Missing path');
}

$targetPath = realpath($themesRoot . DIRECTORY_SEPARATOR . $relativePath);

if (!$targetPath || strpos($targetPath, $themesRoot) !== 0 || !is_file($targetPath)) {
    http_response_code(404);
    exit('Asset not found');
}

$mtime = filemtime($targetPath);
$etag = 'W/"' . md5($targetPath . '|' . $mtime . '|' . filesize($targetPath)) . '"';

// Determine MIME type based on file extension (more reliable than mime_content_type)
$ext = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
$mimeTypes = [
    'css' => 'text/css; charset=utf-8',
    'js' => 'application/javascript; charset=utf-8',
    'json' => 'application/json; charset=utf-8',
    'svg' => 'image/svg+xml',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'ico' => 'image/x-icon',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf' => 'font/ttf',
    'eot' => 'application/vnd.ms-fontobject',
];
$mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mimeType);
header('Cache-Control: public, max-age=31536000, immutable');
header('ETag: ' . $etag);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');

if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) ||
    (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $mtime)) {
    http_response_code(304);
    exit;
}

$handle = fopen($targetPath, 'rb');
if (!$handle) {
    http_response_code(500);
    exit('Unable to read asset');
}

while (!feof($handle)) {
    echo fread($handle, 8192);
}

fclose($handle);
