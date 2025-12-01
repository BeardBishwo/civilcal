<?php
/**
 * Simple test to check if admin view files can render content
 */

// Include necessary files first
require_once 'app/Helpers/functions.php';
require_once 'app/Core/Database.php';
require_once 'app/Services/SettingsService.php';
require_once 'app/Core/View.php';

echo "<h1>Admin Content Rendering Test</h1>";

// Test 1: Check if view files exist and can be included
echo "<h2>1. Testing View File Content Rendering</h2>";

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

foreach ($viewFiles as $viewFile) {
    echo "<h3>Testing: $viewFile</h3>";
    
    if (!file_exists($viewFile)) {
        echo "<p>❌ File does not exist</p>";
        continue;
    }
    
    echo "<p>✅ File exists</p>";
    
    // Mock some common variables that views might need
    $page_title = 'Test Page';
    $stats = ['total_users' => 1234, 'active_users' => 850];
    $breadcrumbs = [['title' => 'Dashboard']];
    
    try {
        // Capture output
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        
        if (!empty($content)) {
            $contentLength = strlen($content);
            echo "<p>✅ Renders content ($contentLength characters)</p>";
            
            // Check for common content indicators
            if (strpos($content, 'div') !== false) {
                echo "<p>✅ Contains HTML elements</p>";
            }
            if (strpos($content, 'card') !== false) {
                echo "<p>✅ Contains card elements</p>";
            }
            if (strpos($content, 'chart') !== false) {
                echo "<p>✅ Contains chart elements</p>";
            }
            
            // Show preview
            echo "<details><summary>Content Preview</summary>";
            echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 200px; overflow: auto;'>";
            echo htmlspecialchars(substr($content, 0, 500));
            echo "</pre></details>";
        } else {
            echo "<p>⚠️ Renders empty content</p>";
        }
    } catch (Error $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    } catch (Exception $e) {
        echo "<p>❌ Exception: " . $e->getMessage() . "</p>";
    }
    echo "<hr>";
}

// Test 2: Check layout file
echo "<h2>2. Testing Layout File</h2>";
$layoutFile = 'themes/admin/layouts/main.php';
if (file_exists($layoutFile)) {
    echo "<p>✅ Layout file exists</p>";
    
    // Check if layout has content placeholder
    $layoutContent = file_get_contents($layoutFile);
    if (strpos($layoutContent, '$content') !== false) {
        echo "<p>✅ Layout has content placeholder</p>";
    } else {
        echo "<p>❌ Layout missing content placeholder</p>";
    }
    
    if (strpos($layoutContent, 'admin-content') !== false) {
        echo "<p>✅ Layout has admin-content area</p>";
    } else {
        echo "<p>❌ Layout missing admin-content area</p>";
    }
    
    if (strpos($layoutContent, 'Chart.js') !== false) {
        echo "<p>✅ Layout includes Chart.js</p>";
    } else {
        echo "<p>❌ Layout missing Chart.js</p>";
    }
} else {
    echo "<p>❌ Layout file missing</p>";
}

// Test 3: Simulate what the View class does
echo "<h2>3. Simulating View Rendering Process</h2>";

$testView = 'themes/admin/views/dashboard.php';
if (file_exists($testView)) {
    echo "<p>Testing view rendering simulation...</p>";
    
    // Mock variables
    $page_title = 'Dashboard Test';
    $stats = [
        'total_users' => 1234,
        'active_users' => 850,
        'active_modules' => 12,
        'total_calculations' => 56
    ];
    
    try {
        // Step 1: Capture view content (what View::render does)
        ob_start();
        include $testView;
        $viewContent = ob_get_clean();
        
        echo "<p>✅ View content captured: " . strlen($viewContent) . " characters</p>";
        
        // Step 2: Mock the layout inclusion
        $content = $viewContent; // This is what View class does
        
        // Check if content would be displayed in layout
        if (!empty($content)) {
            echo "<p>✅ Content is ready for layout</p>";
            
            // Test with layout
            ob_start();
            include $layoutFile;
            $finalOutput = ob_get_clean();
            
            if (strpos($finalOutput, $viewContent) !== false) {
                echo "<p>✅ Content successfully embedded in layout</p>";
            } else {
                echo "<p>⚠️ Content not found in final output</p>";
            }
            
            if (strpos($finalOutput, '<!DOCTYPE html') !== false) {
                echo "<p>✅ Complete HTML document generated</p>";
            }
        } else {
            echo "<p>❌ View content is empty</p>";
        }
        
    } catch (Error $e) {
        echo "<p>❌ Error in rendering: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>4. Common Issues Check</h2>";

// Check for common issues
echo "<h3>Checking for common issues:</h3>";

// Check if views are using functions that might not exist
$viewContent = file_get_contents($testView);
$problematicFunctions = ['app_base_url', 'current_user', 'csrf_token'];

foreach ($problematicFunctions as $func) {
    if (strpos($viewContent, $func) !== false) {
        echo "<p>⚠️ Uses function: $func (may cause issues if not defined)</p>";
    }
}

// Check for PHP syntax errors
echo "<h3>Checking PHP syntax:</h3>";
foreach ($viewFiles as $viewFile) {
    if (file_exists($viewFile)) {
        $output = [];
        $returnCode = 0;
        exec("php -l \"$viewFile\" 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "<p>✅ $viewFile: Valid PHP syntax</p>";
        } else {
            echo "<p>❌ $viewFile: PHP syntax error</p>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
        }
    }
}

echo "<h2>Test Complete!</h2>";
echo "<p>If all tests pass, the issue may be in the controller or routing layer.</p>";
?>