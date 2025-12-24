<?php
/**
 * Simple test script to verify permalink functionality
 */

echo "<h1>Permalink System Test</h1>";

// Test 1: Check if UrlHelper exists and works
if (class_exists('\App\Helpers\UrlHelper')) {
    echo "✅ UrlHelper class exists<br>";
    
    // Test available structures
    $structures = \App\Helpers\UrlHelper::getAvailableStructures();
    echo "✅ Available permalink structures: " . count($structures) . "<br>";
    
    foreach ($structures as $key => $info) {
        echo "  - $key: {$info['label']}<br>";
    }
    
    // Test URL generation
    $testUrl = \App\Helpers\UrlHelper::calculator('concrete-volume');
    echo "✅ Test URL generated: $testUrl<br>";
    
} else {
    echo "❌ UrlHelper class not found<br>";
}

// Test 2: Check if SettingsController has permalinks method
if (class_exists('\App\Controllers\Admin\SettingsController')) {
    $controller = new \App\Controllers\Admin\SettingsController();
    if (method_exists($controller, 'permalinks')) {
        echo "✅ SettingsController::permalinks method exists<br>";
    } else {
        echo "❌ SettingsController::permalinks method not found<br>";
    }
} else {
    echo "❌ SettingsController class not found<br>";
}

// Test 3: Check if permalink view exists
$viewPath = __DIR__ . '/themes/admin/views/settings/permalinks.php';
if (file_exists($viewPath)) {
    echo "✅ Permalink view file exists<br>";
} else {
    echo "❌ Permalink view file not found<br>";
}

// Test 4: Check if route exists
$routesFile = __DIR__ . '/app/routes.php';
if (file_exists($routesFile)) {
    $routesContent = file_get_contents($routesFile);
    if (strpos($routesContent, 'permalinks') !== false) {
        echo "✅ Permalink route found in routes.php<br>";
    } else {
        echo "❌ Permalink route not found in routes.php<br>";
    }
} else {
    echo "❌ routes.php file not found<br>";
}

echo "<h2>Test Complete!</h2>";
echo "<p>If all tests pass, the permalink system should be working properly.</p>";
echo "<p>Access the permalink settings at: <a href='/admin/settings/permalinks'>/admin/settings/permalinks</a></p>";
?>