<?php
/**
 * Bishwo Calculator - MVC Entry Point
 * Routes all requests through the MVC system
 */

// Define application constant
define('BISHWO_CALCULATOR', true);

// Load application bootstrap FIRST (defines BASE_PATH)
$bootstrapPath = dirname(__DIR__) . '/app/bootstrap.php';
require_once $bootstrapPath;

// Start session (if not already started by bootstrap)
// Start session safely
\App\Services\Security::startSession();
\App\Services\Security::setSecureHeaders();
\App\Services\Security::enforceHttps();


// Initialize router
$router = new \App\Core\Router();

// Make router available globally for routes file
$GLOBALS['router'] = $router;

// Check if this is a calculator URL request
if (isset($_GET['calculator'])) {
    require_once BASE_PATH . '/app/Router/CalculatorRouter.php';
    $calculatorRouter = new \App\Router\CalculatorRouter();
    if ($calculatorRouter->handleRequest($_GET['calculator'])) {
        exit; // Calculator was found and executed
    }
    // If calculator not found, continue to normal routing (will show 404)
}

// Load routes
require BASE_PATH . '/app/routes.php';

// Dispatch the request
$router->dispatch();
?>
