<?php
/**
 * Debug Test File - Hands-On Practice
 * Use this file to learn VS Code debugging with breakpoints
 *
 * Instructions:
 * 1. Open this file in VS Code
 * 2. Set breakpoints by clicking left of line numbers (lines 14, 21, 32, 38)
 * 3. Press Ctrl+Shift+D (Debug Panel)
 * 4. Select "Launch currently open script"
 * 5. Press F5 to start debugging
 * 6. Use F10 to step through code
 */

require_once __DIR__ . '/app/bootstrap.php';

echo "=================================================================\n";
echo "           DEBUGGING PRACTICE - BISHWO CALCULATOR                \n";
echo "=================================================================\n\n";

// TEST 1: Basic Variables (Set breakpoint here - Line 21)
echo "TEST 1: Variables\n";
echo "-----------------------------------------------------------------\n";
$appName = "Bishwo Calculator";
$version = "1.0";
$isActive = true;
$debugMode = true;

echo "App Name: $appName\n";
echo "Version: $version\n";
echo "Active: " . ($isActive ? "Yes" : "No") . "\n";
echo "Debug Mode: " . ($debugMode ? "Enabled" : "Disabled") . "\n\n";

// TEST 2: Arrays (Set breakpoint here - Line 38)
echo "TEST 2: Configuration Array\n";
echo "-----------------------------------------------------------------\n";
$config = [
    'environment' => 'development',
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'bishwo_db'
    ],
    'features' => [
        'debugging' => true,
        'caching' => false,
        'analytics' => true
    ]
];

echo "Configuration loaded:\n";
print_r($config);
echo "\n";

// TEST 3: Functions (Set breakpoint here - Line 59)
echo "TEST 3: Function Debugging\n";
echo "-----------------------------------------------------------------\n";

function calculateSum($a, $b) {
    // Breakpoint inside function to see parameters
    $result = $a + $b;
    echo "  Calculating: $a + $b = $result\n";
    return $result;
}

function calculateProduct($x, $y) {
    // Another function to practice Step Into (F11)
    $result = $x * $y;
    echo "  Calculating: $x × $y = $result\n";
    return $result;
}

$sum = calculateSum(10, 20);
$product = calculateProduct(5, 8);
echo "Sum Result: $sum\n";
echo "Product Result: $product\n\n";

// TEST 4: Helper Functions (Set breakpoint here - Line 85)
echo "TEST 4: Helper Functions\n";
echo "-----------------------------------------------------------------\n";

if (function_exists('app_base_url')) {
    $homeUrl = app_base_url('/');
    $testUrl = app_base_url('/test');
    $adminUrl = app_base_url('admin/dashboard');

    echo "✓ app_base_url() is working!\n";
    echo "  Home URL: $homeUrl\n";
    echo "  Test URL: $testUrl\n";
    echo "  Admin URL: $adminUrl\n";
} else {
    echo "✗ app_base_url() not found!\n";
}
echo "\n";

if (function_exists('asset_url')) {
    $cssUrl = asset_url('css/style.css');
    $jsUrl = asset_url('js/app.js');

    echo "✓ asset_url() is working!\n";
    echo "  CSS URL: $cssUrl\n";
    echo "  JS URL: $jsUrl\n";
} else {
    echo "✗ asset_url() not found!\n";
}
echo "\n";

if (function_exists('csrf_token')) {
    $token = csrf_token();
    echo "✓ csrf_token() is working!\n";
    echo "  Token (first 16 chars): " . substr($token, 0, 16) . "...\n";
} else {
    echo "✗ csrf_token() not found!\n";
}
echo "\n";

// TEST 5: Loops (Practice Step Over)
echo "TEST 5: Loop Debugging\n";
echo "-----------------------------------------------------------------\n";
$numbers = [1, 2, 3, 4, 5];
$total = 0;

echo "Calculating sum of array: " . implode(', ', $numbers) . "\n";
foreach ($numbers as $num) {
    $total += $num;
    echo "  Current total: $total\n";
}
echo "Final total: $total\n\n";

// TEST 6: Conditional Logic
echo "TEST 6: Conditional Debugging\n";
echo "-----------------------------------------------------------------\n";

function checkStatus($value) {
    if ($value > 100) {
        return "High";
    } elseif ($value > 50) {
        return "Medium";
    } else {
        return "Low";
    }
}

$testValues = [25, 75, 150];
foreach ($testValues as $val) {
    $status = checkStatus($val);
    echo "Value $val: Status = $status\n";
}
echo "\n";

// TEST 7: Exception Handling
echo "TEST 7: Exception Handling\n";
echo "-----------------------------------------------------------------\n";

try {
    // Safe operation
    $result = 100 / 5;
    echo "✓ Division successful: 100 / 5 = $result\n";

    // This would cause error (uncomment to test)
    // $errorResult = 100 / 0;

} catch (Exception $e) {
    echo "✗ Error caught: " . $e->getMessage() . "\n";
}
echo "\n";

// TEST 8: Class Instantiation
echo "TEST 8: Class Debugging\n";
echo "-----------------------------------------------------------------\n";

try {
    // Test Router class
    if (class_exists('App\\Core\\Router')) {
        $router = new App\Core\Router();
        echo "✓ Router class instantiated\n";
    } else {
        echo "✗ Router class not found\n";
    }

    // Test View class
    if (class_exists('App\\Core\\View')) {
        $view = new App\Core\View();
        echo "✓ View class instantiated\n";
    } else {
        echo "✗ View class not found\n";
    }

    // Test Database
    if (class_exists('App\\Core\\Database')) {
        $db = App\Core\Database::getInstance();
        echo "✓ Database connection established\n";
    } else {
        echo "✗ Database class not found\n";
    }

} catch (Exception $e) {
    echo "✗ Class instantiation error: " . $e->getMessage() . "\n";
}
echo "\n";

// TEST 9: Constants
echo "TEST 9: Constants & Environment\n";
echo "-----------------------------------------------------------------\n";

$constants = [
    'BASE_PATH' => defined('BASE_PATH') ? BASE_PATH : 'Not defined',
    'APP_PATH' => defined('APP_PATH') ? APP_PATH : 'Not defined',
    'CONFIG_PATH' => defined('CONFIG_PATH') ? CONFIG_PATH : 'Not defined',
    'STORAGE_PATH' => defined('STORAGE_PATH') ? STORAGE_PATH : 'Not defined'
];

foreach ($constants as $name => $value) {
    $status = ($value !== 'Not defined') ? '✓' : '✗';
    echo "$status $name: $value\n";
}
echo "\n";

// TEST 10: Performance Measurement
echo "TEST 10: Performance Measurement\n";
echo "-----------------------------------------------------------------\n";

$startTime = microtime(true);
$startMemory = memory_get_usage();

// Simulate some work
for ($i = 0; $i < 10000; $i++) {
    $dummy = $i * 2;
}

$endTime = microtime(true);
$endMemory = memory_get_usage();

$executionTime = ($endTime - $startTime) * 1000;
$memoryUsed = ($endMemory - $startMemory) / 1024;

echo "Execution Time: " . round($executionTime, 2) . " ms\n";
echo "Memory Used: " . round($memoryUsed, 2) . " KB\n";
echo "Peak Memory: " . round(memory_get_peak_usage() / 1024 / 1024, 2) . " MB\n\n";

// FINAL SUMMARY
echo "=================================================================\n";
echo "                    DEBUG TEST COMPLETE!                         \n";
echo "=================================================================\n\n";

echo "✅ All tests completed successfully!\n\n";

echo "Debugging Tips:\n";
echo "  • Set breakpoints by clicking left of line numbers\n";
echo "  • F10 = Step Over (next line)\n";
echo "  • F11 = Step Into (enter function)\n";
echo "  • F5 = Continue to next breakpoint\n";
echo "  • Shift+F5 = Stop debugging\n";
echo "  • Hover over variables to see their values\n";
echo "  • Check Variables panel on the left\n";
echo "  • Use Watch panel to track specific variables\n\n";

echo "Next Steps:\n";
echo "  1. Try setting breakpoints at different lines\n";
echo "  2. Practice using F10, F11, and F5\n";
echo "  3. Inspect variables in the Variables panel\n";
echo "  4. Add expressions to the Watch panel\n";
echo "  5. Try debugging a web request (see START_DEBUGGING_NOW.md)\n\n";

echo "Happy Debugging! 🐛🔍\n";
echo "\n=================================================================\n";
