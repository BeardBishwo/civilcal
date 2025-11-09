<?php
/**
 * Test HomeController execution specifically
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 TESTING HOMECONTROLLER EXECUTION\n";
echo "==================================\n\n";

// Set up the environment
define('BASE_PATH', __DIR__);

// Load the bootstrap
require_once BASE_PATH . '/app/bootstrap.php';

echo "✅ Bootstrap loaded successfully\n\n";

// Test HomeController instantiation
echo "🔍 Testing HomeController instantiation...\n";
try {
    $controller = new \App\Controllers\HomeController();
    echo "✅ HomeController instantiated successfully\n";
} catch (Exception $e) {
    echo "❌ HomeController instantiation failed: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
    echo "📍 Trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}

echo "\n🔍 Testing index() method call...\n";
try {
    // Capture any output
    ob_start();
    $controller->index();
    $output = ob_get_clean();
    
    echo "✅ index() method called successfully\n";
    echo "📝 Output length: " . strlen($output) . " characters\n";
    
    if (strlen($output) > 0) {
        echo "📄 Output preview (first 500 chars):\n";
        echo substr($output, 0, 500) . "...\n\n";
    } else {
        echo "⚠️  No output generated\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ index() method call failed: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
    echo "📍 Trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}

echo "🔍 Testing if view files exist...\n";

// Check main home view
$homeViewPath = BASE_PATH . '/themes/default/views/home/index.php';
if (file_exists($homeViewPath)) {
    echo "✅ Home index view exists: {$homeViewPath}\n";
} else {
    echo "❌ Home index view missing: {$homeViewPath}\n";
}

// Check layout file
$layoutPath = BASE_PATH . '/themes/default/views/layouts/main.php';
if (file_exists($layoutPath)) {
    echo "✅ Main layout exists: {$layoutPath}\n";
} else {
    echo "❌ Main layout missing: {$layoutPath}\n";
}

// Check theme assets
$themePath = BASE_PATH . '/themes/default/';
if (is_dir($themePath)) {
    echo "✅ Theme directory exists: {$themePath}\n";
} else {
    echo "❌ Theme directory missing: {$themePath}\n";
}

echo "\n🔍 Testing theme configuration...\n";
try {
    $themeConfig = $controller->view->getThemeConfig();
    echo "✅ Theme config loaded: " . ($themeConfig['name'] ?? 'Unknown') . "\n";
    echo "📋 Active theme: " . $controller->view->getActiveTheme() . "\n";
} catch (Exception $e) {
    echo "❌ Theme config loading failed: " . $e->getMessage() . "\n";
}

echo "\n🔍 Testing individual private methods...\n";
try {
    $method = new ReflectionMethod($controller, 'getSystemStats');
    $method->setAccessible(true);
    $stats = $method->invoke($controller);
    echo "✅ getSystemStats() works: " . json_encode($stats) . "\n";
} catch (Exception $e) {
    echo "❌ getSystemStats() failed: " . $e->getMessage() . "\n";
}

try {
    $method = new ReflectionMethod($controller, 'getFeaturedCalculators');
    $method->setAccessible(true);
    $calculators = $method->invoke($controller);
    echo "✅ getFeaturedCalculators() works: " . count($calculators) . " items\n";
} catch (Exception $e) {
    echo "❌ getFeaturedCalculators() failed: " . $e->getMessage() . "\n";
}

echo "\n🎉 HomeController execution test completed!\n";
?>
