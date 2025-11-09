<?php
// Test production Apache environment with subdirectory path
// Simulates: http://localhost/bishwo_calculator/public/

// Simulate Apache environment variables
$_SERVER['REQUEST_URI'] = '/bishwo_calculator/public/';
$_SERVER['SCRIPT_NAME'] = '/bishwo_calculator/public/index.php';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Set base path for Router testing
define('BASE_PATH', __DIR__);

// Load necessary files
require_once 'config/database.php';
require_once 'app/Core/Database.php';
require_once 'app/Core/Router.php';
require_once 'app/Core/Controller.php';
require_once 'app/Controllers/HomeController.php';

echo "=== PRODUCTION APACHE SIMULATION TEST ===\n\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n\n";

// Test Router with subdirectory support
$router = new \App\Core\Router();

// Set global variable FIRST (required by routes.php)
$GLOBALS['router'] = $router;

// Test getBasePath() method
$basePath = $router->getBasePath();
echo "Detected Base Path: " . ($basePath ? $basePath : "None") . "\n\n";

// Load routes
require 'app/routes.php';
echo "Routes loaded: " . count($router->routes) . "\n\n";

// Test dispatch with subdirectory path
echo "Testing dispatch with subdirectory URL...\n";
try {
    $router->dispatch();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
