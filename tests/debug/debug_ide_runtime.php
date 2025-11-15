<?php
/**
 * IDE Debugger Runtime Test Script
 * Comprehensive debugging and profiling for Bishwo Calculator
 *
 * This script tests all critical components with detailed error tracking,
 * memory profiling, execution timing, and stack trace analysis.
 */

// Start output buffering to capture all output
ob_start();

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start timing
$startTime = microtime(true);
$startMemory = memory_get_usage();

// Debug log array
$debugLog = [];
$errors = [];
$warnings = [];
$passes = [];

/**
 * Debug logger function
 */
function debugLog($message, $type = 'INFO', $data = null) {
    global $debugLog;
    $debugLog[] = [
        'time' => microtime(true),
        'memory' => memory_get_usage(),
        'type' => $type,
        'message' => $message,
        'data' => $data,
        'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
    ];
}

/**
 * Test runner with error catching
 */
function runTest($testName, $callback) {
    global $errors, $warnings, $passes;

    debugLog("Starting test: {$testName}", 'TEST_START');

    try {
        $result = $callback();
        if ($result === false) {
            $errors[] = $testName;
            debugLog("Test FAILED: {$testName}", 'ERROR');
            return false;
        } else {
            $passes[] = $testName;
            debugLog("Test PASSED: {$testName}", 'SUCCESS');
            return true;
        }
    } catch (Throwable $e) {
        $errors[] = $testName;
        debugLog("Test EXCEPTION: {$testName}", 'EXCEPTION', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

echo "=================================================================\n";
echo "           IDE DEBUGGER RUNTIME TEST - BISHWO CALCULATOR         \n";
echo "=================================================================\n\n";

echo "Starting comprehensive debugging session...\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "s\n\n";

// =================================================================
// TEST 1: Bootstrap Loading
// =================================================================
echo "TEST 1: Bootstrap Loading\n";
echo str_repeat("-", 65) . "\n";

$bootstrapTest = runTest('Bootstrap Loading', function() {
    debugLog('Attempting to load bootstrap');

    if (!file_exists(__DIR__ . '/app/bootstrap.php')) {
        debugLog('Bootstrap file not found', 'ERROR');
        echo "✗ Bootstrap file not found\n";
        return false;
    }

    require_once __DIR__ . '/app/bootstrap.php';

    if (!defined('BASE_PATH')) {
        debugLog('BASE_PATH not defined after bootstrap', 'ERROR');
        echo "✗ BASE_PATH not defined\n";
        return false;
    }

    echo "✓ Bootstrap loaded successfully\n";
    echo "  BASE_PATH: " . BASE_PATH . "\n";
    echo "  APP_PATH: " . (defined('APP_PATH') ? APP_PATH : 'NOT DEFINED') . "\n";
    echo "  Memory after bootstrap: " . round(memory_get_usage() / 1024 / 1024, 2) . " MB\n";

    return true;
});

echo "\n";

// =================================================================
// TEST 2: Constants and Configuration
// =================================================================
echo "TEST 2: Constants and Configuration\n";
echo str_repeat("-", 65) . "\n";

$constantsTest = runTest('Constants Check', function() {
    $requiredConstants = [
        'BASE_PATH',
        'APP_PATH',
        'CONFIG_PATH',
        'STORAGE_PATH'
    ];

    $allDefined = true;
    foreach ($requiredConstants as $const) {
        if (defined($const)) {
            echo "✓ {$const} = " . constant($const) . "\n";
            debugLog("Constant {$const} defined", 'INFO', constant($const));
        } else {
            echo "✗ {$const} is NOT defined\n";
            debugLog("Constant {$const} NOT defined", 'ERROR');
            $allDefined = false;
        }
    }

    return $allDefined;
});

echo "\n";

// =================================================================
// TEST 3: Helper Functions
// =================================================================
echo "TEST 3: Helper Functions\n";
echo str_repeat("-", 65) . "\n";

$helpersTest = runTest('Helper Functions', function() {
    $requiredFunctions = [
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

    $allExist = true;
    foreach ($requiredFunctions as $func) {
        if (function_exists($func)) {
            echo "✓ {$func}() exists\n";
            debugLog("Function {$func} exists", 'INFO');
        } else {
            echo "✗ {$func}() is MISSING\n";
            debugLog("Function {$func} NOT found", 'ERROR');
            $allExist = false;
        }
    }

    // Test execution of key helpers
    if (function_exists('app_base_url')) {
        $testUrl = app_base_url('/test');
        echo "  → app_base_url('/test') = {$testUrl}\n";
        debugLog('app_base_url test', 'INFO', $testUrl);
    }

    if (function_exists('csrf_token')) {
        $token = csrf_token();
        echo "  → csrf_token() = " . substr($token, 0, 16) . "...\n";
        debugLog('csrf_token test', 'INFO', substr($token, 0, 16));
    }

    return $allExist;
});

echo "\n";

// =================================================================
// TEST 4: Core Classes Autoloading
// =================================================================
echo "TEST 4: Core Classes Autoloading\n";
echo str_repeat("-", 65) . "\n";

$classesTest = runTest('Core Classes', function() {
    $coreClasses = [
        'App\\Core\\Router',
        'App\\Core\\View',
        'App\\Core\\Controller',
        'App\\Core\\Database',
        'App\\Services\\Logger',
        'App\\Services\\PluginManager',
        'App\\Services\\ThemeManager'
    ];

    $allExist = true;
    foreach ($coreClasses as $class) {
        if (class_exists($class)) {
            echo "✓ {$class} exists\n";
            debugLog("Class {$class} exists", 'INFO');

            // Try to get reflection info
            try {
                $reflection = new ReflectionClass($class);
                $methods = count($reflection->getMethods());
                echo "  → Methods: {$methods}\n";
                debugLog("Class {$class} methods", 'INFO', $methods);
            } catch (Exception $e) {
                debugLog("Reflection failed for {$class}", 'WARNING', $e->getMessage());
            }
        } else {
            echo "✗ {$class} NOT found\n";
            debugLog("Class {$class} NOT found", 'ERROR');
            $allExist = false;
        }
    }

    return $allExist;
});

echo "\n";

// =================================================================
// TEST 5: Class Instantiation
// =================================================================
echo "TEST 5: Class Instantiation & Method Testing\n";
echo str_repeat("-", 65) . "\n";

$instantiationTest = runTest('Class Instantiation', function() {
    $success = true;

    // Test Router
    try {
        $router = new \App\Core\Router();
        echo "✓ Router instantiated\n";

        // Test adding a route
        $router->add('GET', '/debug-test', function() {
            return 'Debug test route';
        });
        echo "  → Test route added successfully\n";
        debugLog('Router test', 'SUCCESS', 'Route added');
    } catch (Throwable $e) {
        echo "✗ Router instantiation failed: " . $e->getMessage() . "\n";
        debugLog('Router instantiation failed', 'ERROR', $e->getMessage());
        $success = false;
    }

    // Test View
    try {
        $view = new \App\Core\View();
        echo "✓ View instantiated\n";
        debugLog('View test', 'SUCCESS');
    } catch (Throwable $e) {
        echo "✗ View instantiation failed: " . $e->getMessage() . "\n";
        debugLog('View instantiation failed', 'ERROR', $e->getMessage());
        $success = false;
    }

    // Test ThemeManager
    try {
        $themeManager = new \App\Services\ThemeManager();
        echo "✓ ThemeManager instantiated\n";

        // Test URL generation
        $testUrl = $themeManager->themeUrl('assets/css/test.css');
        echo "  → themeUrl test: {$testUrl}\n";
        debugLog('ThemeManager test', 'SUCCESS', $testUrl);
    } catch (Throwable $e) {
        echo "✗ ThemeManager instantiation failed: " . $e->getMessage() . "\n";
        debugLog('ThemeManager instantiation failed', 'ERROR', $e->getMessage());
        $success = false;
    }

    // Test Database
    try {
        $db = \App\Core\Database::getInstance();
        echo "✓ Database connection established\n";
        debugLog('Database test', 'SUCCESS');
    } catch (Throwable $e) {
        echo "✗ Database connection failed: " . $e->getMessage() . "\n";
        debugLog('Database connection failed', 'ERROR', $e->getMessage());
        // Don't fail the test if DB is not configured yet
    }

    return $success;
});

echo "\n";

// =================================================================
// TEST 6: File System & Permissions
// =================================================================
echo "TEST 6: File System & Permissions\n";
echo str_repeat("-", 65) . "\n";

$filesystemTest = runTest('File System', function() {
    $criticalPaths = [
        BASE_PATH . '/storage' => 'writable',
        BASE_PATH . '/storage/logs' => 'writable',
        BASE_PATH . '/themes/default/assets/css' => 'readable',
        BASE_PATH . '/themes/default/views' => 'readable',
        BASE_PATH . '/public/index.php' => 'readable',
        BASE_PATH . '/app/routes.php' => 'readable'
    ];

    $allOk = true;
    foreach ($criticalPaths as $path => $requirement) {
        if (!file_exists($path)) {
            echo "✗ NOT FOUND: " . basename($path) . "\n";
            debugLog("Path not found: {$path}", 'ERROR');
            $allOk = false;
            continue;
        }

        $isDir = is_dir($path);
        $permissions = substr(sprintf('%o', fileperms($path)), -4);

        if ($requirement === 'writable' && !is_writable($path)) {
            echo "✗ NOT WRITABLE: " . basename($path) . " ({$permissions})\n";
            debugLog("Path not writable: {$path}", 'ERROR', $permissions);
            $allOk = false;
        } elseif ($requirement === 'readable' && !is_readable($path)) {
            echo "✗ NOT READABLE: " . basename($path) . " ({$permissions})\n";
            debugLog("Path not readable: {$path}", 'ERROR', $permissions);
            $allOk = false;
        } else {
            echo "✓ " . basename($path) . " ({$permissions})\n";
            debugLog("Path OK: {$path}", 'INFO', $permissions);
        }
    }

    return $allOk;
});

echo "\n";

// =================================================================
// TEST 7: CSS/JS Asset Loading
// =================================================================
echo "TEST 7: CSS/JS Asset Loading\n";
echo str_repeat("-", 65) . "\n";

$assetsTest = runTest('CSS/JS Assets', function() {
    $cssPath = BASE_PATH . '/themes/default/assets/css';
    $jsPath = BASE_PATH . '/themes/default/assets/js';

    // Check CSS files
    $cssFiles = ['theme.css', 'footer.css', 'home.css', 'back-to-top.css', 'logo-enhanced.css'];
    $cssCount = 0;

    echo "CSS Files:\n";
    foreach ($cssFiles as $file) {
        $fullPath = $cssPath . '/' . $file;
        if (file_exists($fullPath)) {
            $size = filesize($fullPath);
            $sizeKb = round($size / 1024, 2);
            echo "  ✓ {$file} ({$sizeKb} KB)\n";
            debugLog("CSS file found: {$file}", 'INFO', $sizeKb . ' KB');
            $cssCount++;
        } else {
            echo "  ✗ {$file} NOT FOUND\n";
            debugLog("CSS file missing: {$file}", 'ERROR');
        }
    }

    // Check JS directory
    echo "\nJavaScript:\n";
    if (is_dir($jsPath)) {
        $jsFiles = array_diff(scandir($jsPath), ['.', '..']);
        echo "  ✓ JS directory exists (" . count($jsFiles) . " files)\n";
        debugLog("JS directory", 'INFO', count($jsFiles) . ' files');
    } else {
        echo "  ✗ JS directory NOT FOUND\n";
        debugLog("JS directory missing", 'WARNING');
    }

    return $cssCount > 0;
});

echo "\n";

// =================================================================
// TEST 8: Routes File Validation
// =================================================================
echo "TEST 8: Routes File Validation\n";
echo str_repeat("-", 65) . "\n";

$routesTest = runTest('Routes File', function() {
    $routesFile = BASE_PATH . '/app/routes.php';

    if (!file_exists($routesFile)) {
        echo "✗ Routes file not found\n";
        debugLog("Routes file not found", 'ERROR');
        return false;
    }

    // Check syntax
    $output = [];
    $return_var = 0;
    exec("php -l " . escapeshellarg($routesFile) . " 2>&1", $output, $return_var);

    if ($return_var === 0) {
        echo "✓ Routes file has valid syntax\n";
        debugLog("Routes file syntax valid", 'SUCCESS');
    } else {
        echo "✗ Routes file has SYNTAX ERRORS:\n";
        foreach ($output as $line) {
            echo "  {$line}\n";
        }
        debugLog("Routes file syntax error", 'ERROR', $output);
        return false;
    }

    // Check file size
    $size = filesize($routesFile);
    $sizeKb = round($size / 1024, 2);
    echo "  → File size: {$sizeKb} KB\n";
    debugLog("Routes file size", 'INFO', $sizeKb . ' KB');

    return true;
});

echo "\n";

// =================================================================
// TEST 9: Session Handling
// =================================================================
echo "TEST 9: Session Handling\n";
echo str_repeat("-", 65) . "\n";

$sessionTest = runTest('Session Handling', function() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "✓ Session is active\n";
        echo "  → Session ID: " . substr(session_id(), 0, 16) . "...\n";
        debugLog("Session active", 'INFO', session_id());
    } else {
        echo "! Session not started (normal for CLI)\n";
        debugLog("Session not active", 'INFO');
    }

    // Test CSRF token generation
    if (function_exists('csrf_token')) {
        $token = csrf_token();
        echo "✓ CSRF token generated: " . substr($token, 0, 16) . "...\n";
        debugLog("CSRF token generated", 'INFO', substr($token, 0, 16));
    }

    return true;
});

echo "\n";

// =================================================================
// TEST 10: Memory & Performance Profiling
// =================================================================
echo "TEST 10: Memory & Performance Profiling\n";
echo str_repeat("-", 65) . "\n";

$endMemory = memory_get_usage();
$endTime = microtime(true);
$memoryUsed = $endMemory - $startMemory;
$timeElapsed = $endTime - $startTime;
$peakMemory = memory_get_peak_usage();

echo "Memory Usage:\n";
echo "  → Start: " . round($startMemory / 1024 / 1024, 2) . " MB\n";
echo "  → End: " . round($endMemory / 1024 / 1024, 2) . " MB\n";
echo "  → Used: " . round($memoryUsed / 1024 / 1024, 2) . " MB\n";
echo "  → Peak: " . round($peakMemory / 1024 / 1024, 2) . " MB\n";

echo "\nExecution Time:\n";
echo "  → Total: " . round($timeElapsed * 1000, 2) . " ms\n";
echo "  → Average per test: " . round(($timeElapsed / count($debugLog)) * 1000, 2) . " ms\n";

debugLog("Performance profiling complete", 'INFO', [
    'memory_used' => $memoryUsed,
    'time_elapsed' => $timeElapsed,
    'peak_memory' => $peakMemory
]);

echo "\n";

// =================================================================
// SUMMARY
// =================================================================
echo "=================================================================\n";
echo "                        TEST SUMMARY                             \n";
echo "=================================================================\n\n";

$totalTests = count($passes) + count($errors);
$successRate = $totalTests > 0 ? round((count($passes) / $totalTests) * 100, 2) : 0;

echo "Tests Executed: {$totalTests}\n";
echo "Passed: " . count($passes) . " ✓\n";
echo "Failed: " . count($errors) . " ✗\n";
echo "Success Rate: {$successRate}%\n\n";

if (count($errors) > 0) {
    echo "Failed Tests:\n";
    foreach ($errors as $error) {
        echo "  ✗ {$error}\n";
    }
    echo "\n";
}

// =================================================================
// DEBUG LOG EXPORT
// =================================================================
echo "=================================================================\n";
echo "                     DEBUG LOG SUMMARY                           \n";
echo "=================================================================\n\n";

$logFile = BASE_PATH . '/storage/logs/debug_ide_' . date('Y-m-d_H-i-s') . '.json';
$logDir = dirname($logFile);

if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

$debugData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'summary' => [
        'total_tests' => $totalTests,
        'passed' => count($passes),
        'failed' => count($errors),
        'success_rate' => $successRate,
        'memory_used' => $memoryUsed,
        'time_elapsed' => $timeElapsed,
        'peak_memory' => $peakMemory
    ],
    'passes' => $passes,
    'errors' => $errors,
    'log' => $debugLog
];

if (file_put_contents($logFile, json_encode($debugData, JSON_PRETTY_PRINT))) {
    echo "✓ Debug log saved to: {$logFile}\n";
} else {
    echo "✗ Failed to save debug log\n";
}

// =================================================================
// STACK TRACE ANALYSIS
// =================================================================
echo "\n";
echo "=================================================================\n";
echo "                   STACK TRACE ANALYSIS                          \n";
echo "=================================================================\n\n";

$errorLogs = array_filter($debugLog, function($log) {
    return $log['type'] === 'ERROR' || $log['type'] === 'EXCEPTION';
});

if (count($errorLogs) > 0) {
    echo "Errors detected in execution:\n\n";
    foreach ($errorLogs as $log) {
        echo "Error: {$log['message']}\n";
        echo "Type: {$log['type']}\n";
        if (isset($log['data'])) {
            echo "Details: " . print_r($log['data'], true) . "\n";
        }
        echo str_repeat("-", 65) . "\n";
    }
} else {
    echo "✓ No critical errors detected in execution flow\n";
}

// =================================================================
// RECOMMENDATIONS
// =================================================================
echo "\n";
echo "=================================================================\n";
echo "                     RECOMMENDATIONS                             \n";
echo "=================================================================\n\n";

if (count($errors) === 0) {
    echo "✅ All tests passed! System is working correctly.\n\n";
    echo "Next steps:\n";
    echo "  1. Clear browser cache and test pages manually\n";
    echo "  2. Check browser console (F12) for any runtime errors\n";
    echo "  3. Verify CSS/JS loading in browser network tab\n";
    echo "  4. Test user registration and login flows\n";
} else {
    echo "⚠️  Issues detected. Recommended actions:\n\n";

    foreach ($errors as $error) {
        echo "  • Fix: {$error}\n";
    }

    echo "\n";
    echo "Review the debug log file for detailed information:\n";
    echo "  {$logFile}\n";
}

echo "\n";
echo "=================================================================\n";
echo "                    DEBUG SESSION COMPLETE                       \n";
echo "=================================================================\n";

// Get buffered output
$output = ob_get_clean();

// Display output
echo $output;

// Save output to file
$outputFile = BASE_PATH . '/storage/logs/debug_output_' . date('Y-m-d_H-i-s') . '.txt';
file_put_contents($outputFile, $output);

echo "\n✓ Debug output saved to: {$outputFile}\n";
