<?php
// Test script to verify system status page is working
require_once 'app/bootstrap.php';

use App\Services\SystemMonitoringService;

echo "Testing System Status Page Components\n";
echo "====================================\n\n";

// Test 1: Check if SystemMonitoringService is working
echo "1. Testing SystemMonitoringService...\n";
try {
    $monitoringService = new SystemMonitoringService();
    $systemHealth = $monitoringService->getSystemHealth();
    
    echo "   ✓ SystemMonitoringService instantiated successfully\n";
    echo "   ✓ getSystemHealth() returned data\n";
    echo "   ✓ Keys in systemHealth: " . implode(', ', array_keys($systemHealth)) . "\n";
    
    // Check if all expected keys are present
    $expectedKeys = ['server', 'database', 'storage', 'application', 'security'];
    $missingKeys = array_diff($expectedKeys, array_keys($systemHealth));
    
    if (empty($missingKeys)) {
        echo "   ✓ All expected health categories present\n";
    } else {
        echo "   ✗ Missing health categories: " . implode(', ', $missingKeys) . "\n";
    }
    
    // Check a few sample values
    echo "   ✓ Server status: " . ($systemHealth['server']['status'] ?? 'unknown') . "\n";
    echo "   ✓ Database status: " . ($systemHealth['database']['status'] ?? 'unknown') . "\n";
    
} catch (Exception $e) {
    echo "   ✗ Error testing SystemMonitoringService: " . $e->getMessage() . "\n";
}

echo "\n2. Testing CSS file...\n";
$cssFile = BASE_PATH . '/themes/admin/assets/css/admin.css';
if (file_exists($cssFile)) {
    echo "   ✓ Main CSS file exists\n";
    
    $cssContent = file_get_contents($cssFile);
    
    // Check if key CSS classes exist
    $keyClasses = ['.stats-grid', '.stat-card', '.system-health-grid'];
    foreach ($keyClasses as $class) {
        if (strpos($cssContent, $class) !== false) {
            echo "   ✓ CSS class '$class' found\n";
        } else {
            echo "   ✗ CSS class '$class' not found\n";
        }
    }
} else {
    echo "   ✗ Main CSS file not found\n";
}

echo "\n3. Testing view file...\n";
$viewFile = BASE_PATH . '/themes/admin/views/system-status/index.php';
if (file_exists($viewFile)) {
    echo "   ✓ System status view file exists\n";
    
    $viewContent = file_get_contents($viewFile);
    
    // Check if key elements exist in the view
    $keyElements = ['System Status', '$systemHealth', 'stat-card', 'system-health-grid'];
    foreach ($keyElements as $element) {
        if (strpos($viewContent, $element) !== false) {
            echo "   ✓ View element '$element' found\n";
        } else {
            echo "   ✗ View element '$element' not found\n";
        }
    }
} else {
    echo "   ✗ System status view file not found\n";
}

echo "\n4. Testing routes...\n";
$routesFile = BASE_PATH . '/app/routes.php';
if (file_exists($routesFile)) {
    echo "   ✓ Routes file exists\n";
    
    $routesContent = file_get_contents($routesFile);
    
    // Check if key routes exist
    $keyRoutes = ['Admin\SystemStatusController@index', 'admin/system-status'];
    foreach ($keyRoutes as $route) {
        if (strpos($routesContent, $route) !== false) {
            echo "   ✓ Route component '$route' found\n";
        } else {
            echo "   ✗ Route component '$route' not found\n";
        }
    }
} else {
    echo "   ✗ Routes file not found\n";
}

echo "\nTest completed!\n";
?>