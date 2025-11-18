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

// Initialize router
$router = new \App\Core\Router();

// Make router available globally for routes file
$GLOBALS['router'] = $router;

// Load routes
require BASE_PATH . '/app/routes.php';

// Dispatch the request
$router->dispatch();
?>
