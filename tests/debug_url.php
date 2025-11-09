<?php
/**
 * URL Debug version to see what URL is being processed
 */

// Define application constant
define('BISHWO_CALCULATOR', true);

echo "🔍 URL DEBUG<br>";
echo "=============<br><br>";

// Load application bootstrap
$bootstrapPath = dirname(__DIR__) . '/app/bootstrap.php';
if (file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize router
$router = new \App\Core\Router();
$GLOBALS['router'] = $router;

// Load routes
$routesPath = BASE_PATH . '/app/routes.php';
if (file_exists($routesPath)) {
    require $routesPath;
}

// Check what URL is being processed
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Parsed path: " . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . "<br>";
echo "Request method: " . $_SERVER['REQUEST_METHOD'] . "<br><br>";

// Show first few routes for comparison
echo "Sample routes:<br>";
for ($i = 0; $i < min(5, count($router->routes)); $i++) {
    $route = $router->routes[$i];
    echo "Route " . ($i+1) . ": {$route['method']} {$route['uri']} -> {$route['controller']}<br>";
}
echo "<br>";

// Manually test route matching
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

echo "Testing route matching for: $method $uri<br><br>";

$matchFound = false;
foreach ($router->routes as $i => $route) {
    // Convert route URI to regex pattern
    $pattern = preg_replace('/\{([a-z]+)\}/', '([^/]+)', $route['uri']);
    $pattern = "#^$pattern$#";
    
    $methodMatch = $route['method'] === $method;
    $uriMatch = preg_match($pattern, $uri, $matches);
    
    if ($methodMatch && $uriMatch) {
        echo "✅ MATCH FOUND: Route " . ($i+1) . " - {$route['method']} {$route['uri']} -> {$route['controller']}<br>";
        $matchFound = true;
        break;
    } else {
        echo "❌ No match: Route " . ($i+1) . " - {$route['method']} {$route['uri']} (Pattern: $pattern)<br>";
        if ($i >= 10) { // Limit output
            echo "... (showing first 10 routes)<br>";
            break;
        }
    }
}

if (!$matchFound) {
    echo "<br>❌ NO ROUTE MATCHES FOUND - This explains the 404!<br>";
    
    // Check if it's the root path
    if ($uri === '/' || $uri === '') {
        echo "🔍 Root path detected. Let's check if there's a root route...<br>";
        
        // Look specifically for root routes
        foreach ($router->routes as $i => $route) {
            if ($route['uri'] === '/' && $route['method'] === 'GET') {
                echo "✅ Found root route: {$route['method']} {$route['uri']} -> {$route['controller']}<br>";
                break;
            }
        }
    }
}
?>
