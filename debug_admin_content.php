<?php
/**
 * Debug script to test admin content rendering
 */

// Include necessary files
require_once 'app/Core/View.php';
require_once 'app/Core/Database.php';
require_once 'app/Services/SettingsService.php';

// Mock session for testing
$_SESSION['user'] = [
    'id' => 1,
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@example.com',
    'is_admin' => 1
];

// Mock app_base_url function if not exists
if (!function_exists('app_base_url')) {
    function app_base_url($path = '') {
        $base = 'http://' . $_SERVER['HTTP_HOST'] . str_replace('/debug_admin_content.php', '', $_SERVER['REQUEST_URI']);
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}

// Mock current_user function if not exists
if (!function_exists('current_user')) {
    function current_user() {
        return $_SESSION['user'] ?? [];
    }
}

// Create View instance
$view = new \App\Core\View();

// Test data for dashboard
$testData = [
    'page_title' => 'Test Dashboard',
    'stats' => [
        'total_users' => 1234,
        'active_users' => 850,
        'active_modules' => 12,
        'total_calculations' => 56
    ],
    'recent_activity' => [
        ['user' => 'Test User', 'action' => 'Test Action', 'time' => '2 mins ago']
    ]
];

echo "<h1>Testing Admin Content Rendering</h1>";

// Test 1: Check if view files exist
echo "<h2>1. Checking View Files</h2>";
$viewFiles = [
    'themes/admin/views/dashboard.php',
    'themes/admin/views/dashboard_complex.php',
    'themes/admin/views/configured-dashboard.php',
    'themes/admin/views/performance-dashboard.php',
    'themes/admin/views/system-status.php',
    'themes/admin/views/widget-management.php',
    'themes/admin/views/menu-customization.php',
    'themes/admin/views/settings/backup.php',
    'themes/admin/views/settings/advanced.php'
];

foreach ($viewFiles as $file) {
    $exists = file_exists($file) ? '✅ EXISTS' : '❌ MISSING';
    echo "<p>$file: $exists</p>";
}

// Test 2: Check layout files
echo "<h2>2. Checking Layout Files</h2>";
$layoutFiles = [
    'themes/admin/layouts/main.php',
    'themes/admin/layouts/admin.php',
    'themes/admin/layouts/header.php',
    'themes/admin/layouts/sidebar.php'
];

foreach ($layoutFiles as $file) {
    $exists = file_exists($file) ? '✅ EXISTS' : '❌ MISSING';
    echo "<p>$file: $exists</p>";
}

// Test 3: Check asset files
echo "<h2>3. Checking Asset Files</h2>";
$assetFiles = [
    'themes/admin/assets/css/admin.css',
    'themes/admin/assets/js/admin.js',
    'themes/admin/assets/images/admin-logo.png'
];

foreach ($assetFiles as $file) {
    $exists = file_exists($file) ? '✅ EXISTS' : '❌ MISSING';
    echo "<p>$file: $exists</p>";
}

// Test 4: Test view rendering
echo "<h2>4. Testing View Rendering</h2>";
try {
    ob_start();
    $view->render('admin/dashboard', $testData);
    $output = ob_get_clean();
    
    if (strpos($output, '<!DOCTYPE html') !== false) {
        echo "<p>✅ View renders complete HTML document</p>";
    } else {
        echo "<p>⚠️ View renders partial content</p>";
    }
    
    if (strpos($output, 'admin-sidebar') !== false) {
        echo "<p>✅ Sidebar is included</p>";
    } else {
        echo "<p>❌ Sidebar is missing</p>";
    }
    
    if (strpos($output, 'admin-content') !== false) {
        echo "<p>✅ Content area is included</p>";
    } else {
        echo "<p>❌ Content area is missing</p>";
    }
    
    if (strpos($output, 'Total Users') !== false || strpos($output, '1234') !== false) {
        echo "<p>✅ Dashboard data is rendered</p>";
    } else {
        echo "<p>❌ Dashboard data is missing</p>";
    }
    
    if (strpos($output, 'chart.js') !== false) {
        echo "<p>✅ Chart.js is included</p>";
    } else {
        echo "<p>❌ Chart.js is missing</p>";
    }
    
    // Show first 1000 characters of output for debugging
    echo "<h3>Output Preview (first 1000 chars):</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow: auto;'>";
    echo htmlspecialchars(substr($output, 0, 1000));
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Error rendering view: " . $e->getMessage() . "</p>";
}

// Test 5: Test individual view files
echo "<h2>5. Testing Individual View Files</h2>";
foreach (['dashboard', 'dashboard_complex', 'configured-dashboard'] as $viewName) {
    $viewFile = "themes/admin/views/{$viewName}.php";
    if (file_exists($viewFile)) {
        try {
            ob_start();
            include $viewFile;
            $content = ob_get_clean();
            if (!empty($content)) {
                echo "<p>✅ {$viewName}.php renders content</p>";
            } else {
                echo "<p>⚠️ {$viewName}.php renders empty content</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ {$viewName}.php error: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<h2>6. Testing Chart.js Functions</h2>";
echo "<script>
// Test if Chart.js functions are available
if (typeof Chart !== 'undefined') {
    console.log('✅ Chart.js is loaded');
} else {
    console.log('❌ Chart.js is not loaded');
}

if (typeof createChart === 'function') {
    console.log('✅ createChart function is available');
} else {
    console.log('❌ createChart function is missing');
}

if (typeof showNotification === 'function') {
    console.log('✅ showNotification function is available');
} else {
    console.log('❌ showNotification function is missing');
}
</script>";

echo "<p><strong>Debug complete!</strong> Check the results above to identify any remaining issues.</p>";
?>