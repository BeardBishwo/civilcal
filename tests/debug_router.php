<?php
/**
 * Router Debug Test
 * Debug the routing issue
 */

echo "🔍 ROUTER DEBUG TEST\n";
echo "====================\n\n";

// Mock the request data
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';

// Start output buffering to capture any errors
ob_start();

try {
    // Load the application
    require_once __DIR__ . '/app/bootstrap.php';
    
    // Initialize router
    $router = new \App\Core\Router();
    $GLOBALS['router'] = $router;
    
    // Load routes
    require __DIR__ . '/app/routes.php';
    
    // Get the request details
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    
    echo "🔍 REQUEST DETAILS:\n";
    echo "Method: $method\n";
    echo "URI: $uri\n";
    echo "Total routes loaded: " . count($router->routes ?? []) . "\n\n";
    
    // Show first few routes for debugging
    echo "📋 FIRST FEW ROUTES:\n";
    $routesToShow = array_slice($router->routes ?? [], 0, 5);
    foreach ($routesToShow as $i => $route) {
        echo ($i + 1) . ". " . $route['method'] . " " . $route['uri'] . " → " . $route['controller'] . "\n";
    }
    
    // Check for the home route specifically
    echo "\n🎯 CHECKING HOME ROUTE:\n";
    $homeRoute = $router->routes[0] ?? null; // First route should be '/'
    
    if ($homeRoute) {
        echo "Home route found: " . $homeRoute['method'] . " " . $homeRoute['uri'] . " → " . $homeRoute['controller'] . "\n";
        
        // Test the matching logic
        $pattern = preg_replace('/\{([a-z]+)\}/', '([^/]+)', $homeRoute['uri']);
        $pattern = "#^$pattern$#";
        
        echo "Pattern: $pattern\n";
        echo "URI to match: $uri\n";
        echo "Method match: " . ($homeRoute['method'] === $method ? 'YES' : 'NO') . "\n";
        echo "Pattern match: " . (preg_match($pattern, $uri) ? 'YES' : 'NO') . "\n";
        
        if (preg_match($pattern, $uri, $matches)) {
            echo "Regex matches: " . json_encode($matches) . "\n";
        }
    }
    
    // Try to dispatch
    echo "\n🚀 ATTEMPTING DISPATCH:\n";
    $router->dispatch();
    
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Get any output
$output = ob_get_clean();
if (!empty($output)) {
    echo "\n📤 OUTPUT CAPTURED:\n";
    echo $output;
}

echo "\n🔍 ROUTER DEBUG COMPLETE\n";
echo "========================\n";
?>


