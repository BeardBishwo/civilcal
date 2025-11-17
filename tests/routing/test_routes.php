<?php
require_once 'app/bootstrap.php';

$router = new \App\Core\Router();
$GLOBALS['router'] = $router;

// Load routes
require 'app/routes.php';

// Check for /api/login route
echo "=== Route Check ===\n";
$apiLoginRoute = null;
foreach ($router->routes as $route) {
    if ($route['uri'] === '/api/login' && $route['method'] === 'POST') {
        $apiLoginRoute = $route;
        break;
    }
}

if ($apiLoginRoute) {
    echo "[OK] /api/login POST route registered\n";
    echo "Controller: {$apiLoginRoute['controller']}\n";
    echo "Middleware: " . implode(', ', $apiLoginRoute['middleware']) . "\n";
} else {
    echo "[ERROR] /api/login POST route NOT found\n\n";
    echo "All registered routes:\n";
    foreach ($router->routes as $route) {
        if (strpos($route['uri'], '/api') === 0) {
            echo "  {$route['method']} {$route['uri']} -> {$route['controller']}\n";
        }
    }
}

// Try to manually match a test request
echo "\n=== Manual Route Matching Test ===\n";
$_SERVER['REQUEST_URI'] = '/api/login';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['SCRIPT_NAME'] = '/index.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$basePath = $router->getBasePath();

echo "URI: $uri\n";
echo "Method: $method\n";
echo "Base Path: " . ($basePath ?? 'null') . "\n";

foreach ($router->routes as $route) {
    if ($route['uri'] === '/api/login' && $route['method'] === 'POST') {
        $matches = $router->matchRoute($route, $uri, $method);
        if ($matches !== false) {
            echo "[SUCCESS] Route matched!\n";
        } else {
            echo "[FAILED] Route did not match\n";
            echo "Expected pattern: {$route['uri']}\n";
            echo "Got URI: $uri\n";
        }
    }
}
?>
