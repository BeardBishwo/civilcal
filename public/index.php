<?php
/**
 * Bishwo Calculator - MVC Entry Point
 * Routes all requests through the MVC system
 */

// Define application constant
define('BISHWO_CALCULATOR', true);

// Load application bootstrap FIRST (defines BASE_PATH)
require_once dirname(__DIR__) . '/app/bootstrap.php';

// Serve theme assets when application is hosted from /public
$requestedPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$themePrefixes = ['/themes/'];
if (defined('APP_BASE') && APP_BASE) {
    $normalizedBase = '/' . ltrim(APP_BASE, '/');
    $themePrefixes[] = rtrim($normalizedBase, '/') . '/themes/';
}

$matchedPrefixLen = null;
foreach ($themePrefixes as $prefix) {
    $len = strlen($prefix);
    if ($len && strncasecmp($requestedPath, $prefix, $len) === 0) {
        $matchedPrefixLen = $len;
        break;
    }
}

if ($matchedPrefixLen !== null) {
    $themesRoot = realpath(BASE_PATH . '/themes');
    $relativePath = substr($requestedPath, $matchedPrefixLen);
    $targetPath = $themesRoot ? realpath($themesRoot . DIRECTORY_SEPARATOR . $relativePath) : false;

    if ($themesRoot && $targetPath && str_starts_with($targetPath, $themesRoot) && is_file($targetPath)) {
        $mimeType = mime_content_type($targetPath) ?: 'application/octet-stream';
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($targetPath));
        readfile($targetPath);
        exit;
    }

    http_response_code(404);
    exit;
}

// Start session (if not already started by bootstrap)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if installation is completed (BASE_PATH now available)
function isInstalled() {
    $storageLock = BASE_PATH . '/storage/installed.lock';
    $legacyLock = BASE_PATH . '/storage/install.lock';
    $configLock = BASE_PATH . '/config/installed.lock';
    $envFile = BASE_PATH . '/.env';
    return file_exists($storageLock) || file_exists($legacyLock) || (file_exists($configLock) && file_exists($envFile));
}

// Redirect to installer if not installed
if (!isInstalled() && !isset($_GET['install'])) {
    header('Location: /install/');
    exit;
}

// If system already installed but installer accessed
if (isInstalled() && isset($_GET['install'])) {
    http_response_code(403);
    echo 'System already installed.';
    exit;
}

// Initialize router
$router = new \App\Core\Router();

// Make router available globally for routes file
$GLOBALS['router'] = $router;

// Load routes
require BASE_PATH . '/app/routes.php';

// Dispatch the request
$router->dispatch();
?>
