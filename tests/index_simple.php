<?php
/**
 * Simple Index Diagnostic - Bishwo Calculator
 * This version bypasses complex dependencies to identify the issue
 */

// Start output buffering
ob_start();

try {
    // Define base path
    define('BASE_PATH', __DIR__ . '/..');
    define('BISHWO_CALCULATOR', true);

    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check installation
    $configFile = BASE_PATH . '/config/installed.lock';
    $envFile = BASE_PATH . '/.env';
    $isInstalled = file_exists($configFile) && file_exists($envFile);

    $output = ob_get_clean();
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Index Diagnostic - Bishwo Calculator</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
            .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .test-item { margin: 10px 0; padding: 10px; background: #f8f9fa; border-left: 4px solid #007bff; }
            .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>🔧 Index Diagnostic Test</h1>
            <p><strong>Test Date:</strong> " . date('Y-m-d H:i:s') . "</p>
            
            <div class='success'>
                <h3>✅ Basic Functionality Tests</h3>
                <ul>
                    <li>✅ PHP Working: " . PHP_VERSION . "</li>
                    <li>✅ Session Started: " . (session_status() === PHP_SESSION_ACTIVE ? 'Yes' : 'No') . "</li>
                    <li>✅ Base Path: " . BASE_PATH . "</li>
                    <li>✅ Installation Status: " . ($isInstalled ? 'Installed' : 'Not Installed') . "</li>
                </ul>
            </div>

            <h3>🧪 Testing File Dependencies</h3>";

    // Test each dependency
    $dependencies = [
        'vendor/autoload.php' => 'Composer Autoloader',
        'themes/default/helpers.php' => 'Theme Helpers',
        'app/Services/ThemeManager.php' => 'Theme Manager',
        'app/Controllers/CalculatorController.php' => 'Calculator Controller'
    ];

    $failedFiles = [];
    $successFiles = [];

    foreach ($dependencies as $file => $description) {
        $fullPath = BASE_PATH . '/' . $file;
        if (file_exists($fullPath)) {
            $successFiles[] = "<li>✅ $description: Available ($file)</li>";
        } else {
            $failedFiles[] = "<li>❌ $description: Missing ($file)</li>";
        }
    }

    if (!empty($successFiles)) {
        echo "<div class='info'><h4>Available Files:</h4><ul>" . implode('', $successFiles) . "</ul></div>";
    }

    if (!empty($failedFiles)) {
        echo "<div class='error'><h4>Missing Files (This is the issue!):</h4><ul>" . implode('', $failedFiles) . "</ul></div>";
    }

    // Test the original index.php logic step by step
    echo "<h3>🔄 Testing Index.php Logic</h3>";
    
    // Test autoloader inclusion
    if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
        try {
            require_once BASE_PATH . '/vendor/autoload.php';
            echo "<div class='success'>✅ Composer Autoloader: Loaded successfully</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Composer Autoloader: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='error'>❌ Composer Autoloader: File not found</div>";
    }

    // Test theme helper inclusion
    if (file_exists(BASE_PATH . '/themes/default/helpers.php')) {
        try {
            require_once BASE_PATH . '/themes/default/helpers.php';
            echo "<div class='success'>✅ Theme Helpers: Loaded successfully</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Theme Helpers: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='error'>❌ Theme Helpers: File not found</div>";
    }

    // Test theme manager inclusion
    if (file_exists(BASE_PATH . '/app/Services/ThemeManager.php')) {
        try {
            require_once BASE_PATH . '/app/Services/ThemeManager.php';
            echo "<div class='success'>✅ Theme Manager: Loaded successfully</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Theme Manager: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='error'>❌ Theme Manager: File not found</div>";
    }

    // Test controller existence
    if (class_exists('App\\Controllers\\CalculatorController')) {
        echo "<div class='success'>✅ Calculator Controller: Class exists</div>";
    } else {
        echo "<div class='error'>❌ Calculator Controller: Class not found</div>";
    }

    echo "<div class='info'>
        <h3>📊 Summary</h3>
        <p><strong>Total Dependencies:</strong> " . count($dependencies) . "</p>
        <p><strong>Available:</strong> " . count($successFiles) . "</p>
        <p><strong>Missing:</strong> " . count($failedFiles) . "</p>
    </div>";

    echo "<div class='test-item'>
        <h3>🎯 Solution</h3>";
    
    if (empty($failedFiles)) {
        echo "<p>✅ All dependencies are available! The original index.php should work.</p>";
        echo "<p><a href='index.php' class='btn'>Try Original Index.php</a></p>";
    } else {
        echo "<p>❌ Missing dependencies are causing the 404 error.</p>";
        echo "<p><strong>To fix this, run:</strong></p>";
        echo "<ol>";
        echo "<li>Navigate to application root: <code>C:\\laragon\\www\\Bishwo_Calculator</code></li>";
        echo "<li>Run: <code>composer install</code> (if composer.json exists)</li>";
        echo "<li>Or manually create missing directories and files</li>";
        echo "</ol>";
    }

    echo "</div>";

    echo "<div class='test-item'>
        <h3>🔗 Direct Access Options</h3>
        <p><a href='test_direct.php' class='btn'>Test Direct Application</a></p>
        <p><a href='simple_test.php' class='btn'>Web Server Test</a></p>
    </div>";

    echo "</div></body></html>";

} catch (Exception $e) {
    $output = ob_get_clean();
    echo "<!DOCTYPE html>
    <html><head><title>Critical Error</title></head>
    <body>
    <h1>Critical Error in Index Diagnostic</h1>
    <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
    <p><strong>File:</strong> " . $e->getFile() . "</p>
    <p><strong>Line:</strong> " . $e->getLine() . "</p>
    <p><a href='simple_test.php'>Back to Web Server Test</a></p>
    </body></html>";
}

?>
