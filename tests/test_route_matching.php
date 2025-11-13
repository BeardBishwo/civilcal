<?php
/**
 * Test Route Matching Logic
 */

echo "🔍 TESTING ROUTE MATCHING\n";
echo "========================\n\n";

// Mock the request data
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';

try {
    // Load the application
    require_once __DIR__ . '/app/bootstrap.php';
    
    // Initialize router
    $router = new \App\Core\Router();
    $GLOBALS['router'] = $router;
    
    // Load routes
    require __DIR__ . '/app/routes.php';
    
    echo "Routes loaded: " . count($router->routes) . "\n";
    
    // Get the request details
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    
    echo "Testing: $method $uri\n\n";
    
    // Test the first few routes
    $routesToTest = array_slice($router->routes, 0, 5);
    
    foreach ($routesToTest as $i => $route) {
        echo "Route " . ($i + 1) . ": " . $route['method'] . " " . $route['uri'] . "\n";
        
        // Test method match
        $methodMatch = $route['method'] === $method;
        echo "  Method match: " . ($methodMatch ? "YES" : "NO") . "\n";
        
        // Test URI pattern
        $pattern = preg_replace('/\{([a-z]+)\}/', '([^/]+)', $route['uri']);
        $pattern = "#^$pattern$#";
        echo "  Pattern: $pattern\n";
        
        $uriMatch = preg_match($pattern, $uri, $matches);
        echo "  URI match: " . ($uriMatch ? "YES" : "NO") . "\n";
        
        if ($uriMatch) {
            echo "  Matches: " . json_encode($matches) . "\n";
        }
        
        $bothMatch = $methodMatch && $uriMatch;
        echo "  Both match: " . ($bothMatch ? "YES" : "NO") . "\n";
        
        if ($bothMatch) {
            echo "  ✅ This route should be called!\n";
        }
        
        echo "\n";
    }
    
    // Now test the dispatch method step by step
    echo "🔍 TESTING DISPATCH STEP BY STEP:\n";
    echo "=================================\n";
    
    foreach ($router->routes as $route) {
        if ($router->matchRoute($route, $uri, $method)) {
            echo "✅ MATCH FOUND: " . $route['method'] . " " . $route['uri'] . " → " . $route['controller'] . "\n";
            echo "Attempting to call route...\n";
            
            // Try to call the route
            try {
                $router->callRoute($route);
            } catch (Exception $e) {
                echo "❌ Exception during route call: " . $e->getMessage() . "\n";
            } catch (Error $e) {
                echo "❌ Error during route call: " . $e->getMessage() . "\n";
            }
            
            break; // Only test the first match
        }
    }
    
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🔍 ROUTE MATCHING TEST COMPLETE\n";
echo "================================\n";
?>


