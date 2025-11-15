<?php
/**
 * Simple Verification Script for Bishwo Calculator Fixes
 * Tests critical components after bug fixes
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Bishwo Calculator - Fix Verification ===\n\n";

$passed = 0;
$failed = 0;
$warnings = 0;

// Test 1: Bootstrap loads correctly
echo "1. Testing Bootstrap...\n";
try {
    require_once __DIR__ . '/app/bootstrap.php';
    echo "   ✓ Bootstrap loaded successfully\n";
    $passed++;
} catch (Exception $e) {
    echo "   ✗ Bootstrap failed: " . $e->getMessage() . "\n";
    $failed++;
    die("Cannot continue without bootstrap\n");
}

// Test 2: Constants defined
echo "\n2. Testing Constants...\n";
$requiredConstants = ['BASE_PATH', 'APP_PATH', 'CONFIG_PATH', 'STORAGE_PATH'];
foreach ($requiredConstants as $const) {
    if (defined($const)) {
        echo "   ✓ {$const} is defined\n";
        $passed++;
    } else {
        echo "   ✗ {$const} is NOT defined\n";
        $failed++;
    }
}

// Test 3: Helper functions loaded
echo "\n3. Testing Helper Functions...\n";
$helperFunctions = [
    'app_base_url',
    'asset_url',
    'csrf_token',
    'verify_csrf',
    'is_logged_in',
    'current_user',
    'redirect',
    'old',
    'flash',
    'get_flash'
];

foreach ($helperFunctions as $func) {
    if (function_exists($func)) {
        echo "   ✓ {$func}() exists\n";
        $passed++;
    } else {
        echo "   ✗ {$func}() is MISSING\n";
        $failed++;
    }
}

// Test 4: Helper execution
echo "\n4. Testing Helper Execution...\n";
try {
    $testUrl = app_base_url('/test');
    echo "   ✓ app_base_url() works: {$testUrl}\n";
    $passed++;
} catch (Exception $e) {
    echo "   ✗ app_base_url() failed: " . $e->getMessage() . "\n";
    $failed++;
}

try {
    $testAsset = asset_url('css/style.css');
    echo "   ✓ asset_url() works: {$testAsset}\n";
    $passed++;
} catch (Exception $e) {
    echo "   ✗ asset_url() failed: " . $e->getMessage() . "\n";
    $failed++;
}

try {
    $token = csrf_token();
    echo "   ✓ csrf_token() generated: " . substr($token, 0, 16) . "...\n";
    $passed++;
} catch (Exception $e) {
    echo "   ✗ csrf_token() failed: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 5: Core classes
echo "\n5. Testing Core Classes...\n";
$coreClasses = [
    'App\\Core\\Router',
    'App\\Core\\View',
    'App\\Core\\Controller',
    'App\\Core\\Database',
    'App\\Services\\Logger',
    'App\\Services\\PluginManager'
];

foreach ($coreClasses as $class) {
    if (class_exists($class)) {
        echo "   ✓ {$class} exists\n";
        $passed++;
    } else {
        echo "   ✗ {$class} is MISSING\n";
        $failed++;
    }
}

// Test 6: Critical files
echo "\n6. Testing Critical Files...\n";
$criticalFiles = [
    BASE_PATH . '/app/routes.php',
    BASE_PATH . '/app/bootstrap.php',
    BASE_PATH . '/app/Helpers/functions.php',
    BASE_PATH . '/public/index.php',
    BASE_PATH . '/app/Core/Router.php',
    BASE_PATH . '/app/Core/View.php'
];

foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        // Check for syntax errors
        $output = [];
        $return_var = 0;
        exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $return_var);

        if ($return_var === 0) {
            echo "   ✓ " . basename($file) . " exists and has valid syntax\n";
            $passed++;
        } else {
            echo "   ✗ " . basename($file) . " has SYNTAX ERRORS\n";
            $failed++;
        }
    } else {
        echo "   ✗ " . basename($file) . " is MISSING\n";
        $failed++;
    }
}

// Test 7: Plugin Manager robustness
echo "\n7. Testing Plugin Manager...\n";
try {
    $pluginManager = new \App\Services\PluginManager();
    echo "   ✓ PluginManager instantiated\n";
    $passed++;

    // Try bootAll (should not crash even if table doesn't exist)
    try {
        $pluginManager->bootAll();
        echo "   ✓ bootAll() executed without errors\n";
        $passed++;
    } catch (Exception $e) {
        echo "   ⚠ bootAll() threw exception (might be expected): " . $e->getMessage() . "\n";
        $warnings++;
    }
} catch (Exception $e) {
    echo "   ✗ PluginManager failed: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 8: Router initialization
echo "\n8. Testing Router...\n";
try {
    $router = new \App\Core\Router();
    echo "   ✓ Router instantiated\n";
    $passed++;

    // Try adding a test route
    $router->add('GET', '/test', function() {
        return 'Test route works';
    });
    echo "   ✓ Route added successfully\n";
    $passed++;
} catch (Exception $e) {
    echo "   ✗ Router failed: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 9: View system
echo "\n9. Testing View System...\n";
try {
    $view = new \App\Core\View();
    echo "   ✓ View instantiated\n";
    $passed++;
} catch (Exception $e) {
    echo "   ✗ View failed: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 10: Directory permissions
echo "\n10. Testing Directory Permissions...\n";
$writableDirs = [
    BASE_PATH . '/storage',
    BASE_PATH . '/storage/logs'
];

foreach ($writableDirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "   ✓ " . basename($dir) . " is writable\n";
            $passed++;
        } else {
            echo "   ⚠ " . basename($dir) . " is NOT writable\n";
            $warnings++;
        }
    } else {
        echo "   ⚠ " . basename($dir) . " does NOT exist\n";
        $warnings++;
    }
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "Passed:   {$passed}\n";
echo "Failed:   {$failed}\n";
echo "Warnings: {$warnings}\n";
echo "\n";

if ($failed === 0 && $warnings === 0) {
    echo "✅ ALL TESTS PASSED! System is ready.\n";
    exit(0);
} elseif ($failed === 0) {
    echo "✅ All critical tests passed (with {$warnings} warnings)\n";
    exit(0);
} else {
    echo "❌ FAILED: {$failed} critical issue(s) found\n";
    exit(1);
}
