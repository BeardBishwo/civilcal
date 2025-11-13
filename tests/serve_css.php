<?php
// CSS File Server for themes
$file = $_GET['file'] ?? '';
$cssPath = __DIR__ . '/themes/default/assets/css/' . basename($file);

if (!$file || !file_exists($cssPath)) {
    http_response_code(404);
    exit('CSS file not found');
}

// Set proper headers
header('Content-Type: text/css');
header('Cache-Control: max-age=31536000');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($cssPath)) . ' GMT');

// Serve the CSS file
readfile($cssPath);
?>


