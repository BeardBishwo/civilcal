<?php
/**
 * Bishwo Calculator - MVC Entry Point
 * Routes all requests through the MVC system
 */

// Define application constant
define('BISHWO_CALCULATOR', true);

// Load application bootstrap FIRST (defines BASE_PATH)
require_once dirname(__DIR__) . '/app/bootstrap.php';

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
