<?php
/**
 * Test Router Global Variable Issue
 */

echo "🔍 TESTING ROUTER GLOBAL VARIABLE\n";
echo "=================================\n\n";

// Mock the request data
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';

try {
    // Load the application
    require_once __DIR__ . '/app/bootstrap.php';
    
    // Initialize router
    $router = new \App\Core\Router();
    
    echo "Router created\n";
    echo "Router class: " . get_class($router) . "\n";
    echo "Router routes before global: " . count($router->routes ?? []) . "\n";
    
    // Set global variable
    $GLOBALS['router'] = $router;
    echo "Global variable set\n";
    
    // Test global variable access
    echo "Global router exists: " . (isset($GLOBALS['router']) ? 'YES' : 'NO') . "\n";
    echo "Global router routes: " . count($GLOBALS['router']->routes ?? []) . "\n";
    
    // Test if we can access it directly
    echo "Accessing global router from local scope...\n";
    $localRouter = $GLOBALS['router'];
    echo "Local router routes: " . count($localRouter->routes ?? []) . "\n";
    
    echo "\nLoading routes.php...\n";
    ob_start();
    
    // Include routes file
    include __DIR__ . '/app/routes.php';
    
    $output = ob_get_clean();
    if (!empty($output)) {
        echo "Output from routes.php:\n";
        echo $output;
    }
    
    echo "\nAfter loading routes.php:\n";
    echo "Local router routes: " . count($router->routes ?? []) . "\n";
    echo "Global router routes: " . count($GLOBALS['router']->routes ?? []) . "\n";
    echo "Local variable \$router routes: " . count($router->routes ?? []) . "\n";
    
    // Try to access global router from inside the include
    echo "Testing global access inside include...\n";
    $testGlobal = isset($GLOBALS['router']) ? 'EXISTS' : 'NOT EXISTS';
    echo "Global router from include scope: $testGlobal\n";
    
    if (!empty($GLOBALS['router']->routes)) {
        echo "✅ SUCCESS! Routes loaded: " . count($GLOBALS['router']->routes) . "\n";
        echo "First route: " . $GLOBALS['router']->routes[0]['method'] . " " . $GLOBALS['router']->routes[0]['uri'] . "\n";
    } else {
        echo "❌ Still no routes loaded\n";
    }
    
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🔍 ROUTER GLOBAL TEST COMPLETE\n";
echo "===============================\n";
?>


