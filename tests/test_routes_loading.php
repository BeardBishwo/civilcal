<?php
/**
 * Test Route Loading
 */

echo "🔍 TESTING ROUTE LOADING\n";
echo "=======================\n\n";

// Mock the request data
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';

try {
    // Load the application
    require_once __DIR__ . '/app/bootstrap.php';
    
    // Initialize router
    $router = new \App\Core\Router();
    $GLOBALS['router'] = $router;
    
    echo "Router created, global variable set\n";
    
    // Test including routes file
    echo "Including routes.php...\n";
    require __DIR__ . '/app/routes.php';
    echo "Routes file included\n";
    
    // Check if routes were added
    echo "Routes count: " . count($router->routes ?? []) . "\n";
    
    if (!empty($router->routes)) {
        echo "✅ Routes loaded successfully!\n";
        echo "First route: " . $router->routes[0]['method'] . " " . $router->routes[0]['uri'] . "\n";
    } else {
        echo "❌ No routes loaded\n";
        
        // Let's check if the routes file is syntactically valid
        echo "\nChecking routes.php syntax...\n";
        $routesContent = file_get_contents(__DIR__ . '/app/routes.php');
        echo "Routes file size: " . strlen($routesContent) . " bytes\n";
        
        // Look for the add() calls
        $addCalls = [];
        preg_match_all('/\$router->add\([^;]+\);/', $routesContent, $addCalls);
        echo "Found " . count($addCalls[0]) . " add() calls in routes file\n";
        
        if (count($addCalls[0]) > 0) {
            echo "First few add() calls:\n";
            foreach (array_slice($addCalls[0], 0, 3) as $i => $call) {
                echo ($i + 1) . ". " . trim($call) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🔍 ROUTE LOADING TEST COMPLETE\n";
echo "==============================\n";
?>
