<?php
/**
 * Bishwo Calculator - FIXED MVC Entry Point
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
    $configFile = BASE_PATH . '/config/installed.lock';
    $envFile = BASE_PATH . '/.env';
    
    return file_exists($configFile) && file_exists($envFile);
}

// Redirect to installer if not installed
if (!isInstalled() && !isset($_GET['install'])) {
    header('Location: /install/');
    exit;
}

// Initialize router FIRST
$router = new \App\Core\Router();

// Make router available globally for routes file BEFORE loading routes
$GLOBALS['router'] = $router;

// Load routes
require BASE_PATH . '/app/routes.php';

// Dispatch the request
$router->dispatch();
?>


