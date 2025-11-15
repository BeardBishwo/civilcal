<?php
/**
 * Comprehensive Error Checking Script
 * Tests all critical components of the Bishwo Calculator application
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>\n";
echo "<html><head><title>Bishwo Calculator - Error Check</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }\n";
echo ".section { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }\n";
echo ".success { color: #28a745; font-weight: bold; }\n";
echo ".error { color: #dc3545; font-weight: bold; }\n";
echo ".warning { color: #ffc107; font-weight: bold; }\n";
echo ".info { color: #17a2b8; }\n";
echo "h1 { color: #333; }\n";
echo "h2 { color: #555; border-bottom: 2px solid #007bff; padding-bottom: 5px; }\n";
echo "pre { background: #f8f9fa; padding: 10px; border-left: 3px solid #007bff; overflow-x: auto; }\n";
echo ".test-item { padding: 8px; margin: 5px 0; border-left: 3px solid #ccc; }\n";
echo ".test-item.pass { border-left-color: #28a745; background: #f0fff4; }\n";
echo ".test-item.fail { border-left-color: #dc3545; background: #fff5f5; }\n";
echo ".test-item.warn { border-left-color: #ffc107; background: #fffef0; }\n";
echo "</style></head><body>\n";

echo "<h1>🔍 Bishwo Calculator - System Error Check</h1>\n";
echo "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>\n";

$errors = [];
$warnings = [];
$passes = [];

// ============================================
// 1. Bootstrap & Path Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>1. Bootstrap & Path Configuration</h2>\n";

try {
    if (!defined('BASE_PATH')) {
        require_once __DIR__ . '/app/bootstrap.php';
    }

    $checkPaths = [
        'BASE_PATH' => BASE_PATH ?? null,
        'APP_PATH' => APP_PATH ?? null,
        'CONFIG_PATH' => CONFIG_PATH ?? null,
        'STORAGE_PATH' => STORAGE_PATH ?? null,
    ];

    foreach ($checkPaths as $const => $path) {
        if ($path && is_dir($path)) {
            echo "<div class='test-item pass'>✓ {$const}: {$path}</div>\n";
            $passes[] = "{$const} defined and exists";
        } else {
            echo "<div class='test-item fail'>✗ {$const}: Not defined or doesn't exist</div>\n";
            $errors[] = "{$const} is not properly configured";
        }
    }
} catch (Exception $e) {
    echo "<div class='test-item fail'>✗ Bootstrap Error: " . htmlspecialchars($e->getMessage()) . "</div>\n";
    $errors[] = "Bootstrap failed: " . $e->getMessage();
}

echo "</div>\n";

// ============================================
// 2. Helper Functions Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>2. Helper Functions Availability</h2>\n";

$helperFile = (defined('APP_PATH') ? APP_PATH : __DIR__ . '/app') . '/Helpers/functions.php';
echo "<div class='info'>Helper file path: {$helperFile}</div>\n";

if (file_exists($helperFile)) {
    echo "<div class='test-item pass'>✓ Helper file exists</div>\n";

    // Check if already loaded
    if (!function_exists('app_base_url')) {
        require_once $helperFile;
        echo "<div class='test-item warn'>⚠ Helpers were NOT loaded in bootstrap (loaded now for testing)</div>\n";
        $warnings[] = "Helpers not loaded in bootstrap - will cause errors in views";
    } else {
        echo "<div class='test-item pass'>✓ Helpers already loaded</div>\n";
        $passes[] = "Helpers loaded in bootstrap";
    }

    // Test critical helper functions
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
        'get_flash',
    ];

    foreach ($helperFunctions as $func) {
        if (function_exists($func)) {
            echo "<div class='test-item pass'>✓ Function exists: {$func}()</div>\n";
            $passes[] = "Helper function: {$func}";
        } else {
            echo "<div class='test-item fail'>✗ Function missing: {$func}()</div>\n";
            $errors[] = "Helper function missing: {$func}";
        }
    }

    // Test app_base_url() execution
    try {
        $testUrl = app_base_url('/test');
        echo "<div class='test-item pass'>✓ app_base_url() executed: {$testUrl}</div>\n";
        $passes[] = "app_base_url() works correctly";
    } catch (Exception $e) {
        echo "<div class='test-item fail'>✗ app_base_url() error: " . htmlspecialchars($e->getMessage()) . "</div>\n";
        $errors[] = "app_base_url() execution failed";
    }

} else {
    echo "<div class='test-item fail'>✗ Helper file not found: {$helperFile}</div>\n";
    $errors[] = "Helper file does not exist";
}

echo "</div>\n";

// ============================================
// 3. Configuration Files Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>3. Configuration Files</h2>\n";

$configFiles = [
    'app/Config/config.php',
    'app/Config/app.php',
    'app/Config/db.php',
    'config/app.php',
];

foreach ($configFiles as $configFile) {
    $fullPath = (defined('BASE_PATH') ? BASE_PATH : __DIR__) . '/' . $configFile;
    if (file_exists($fullPath)) {
        echo "<div class='test-item pass'>✓ Config file exists: {$configFile}</div>\n";
        $passes[] = "Config file: {$configFile}";
    } else {
        echo "<div class='test-item warn'>⚠ Config file missing: {$configFile}</div>\n";
        $warnings[] = "Config file missing: {$configFile}";
    }
}

// Check for constants
$configConstants = ['APP_DEBUG', 'APP_BASE', 'APP_URL'];
foreach ($configConstants as $const) {
    if (defined($const)) {
        $value = constant($const);
        $displayValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        echo "<div class='test-item pass'>✓ Constant defined: {$const} = {$displayValue}</div>\n";
        $passes[] = "Constant: {$const}";
    } else {
        echo "<div class='test-item warn'>⚠ Constant not defined: {$const}</div>\n";
        $warnings[] = "Constant not defined: {$const}";
    }
}

echo "</div>\n";

// ============================================
// 4. Core Classes Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>4. Core Classes Availability</h2>\n";

$coreClasses = [
    'App\\Core\\Router',
    'App\\Core\\View',
    'App\\Core\\Controller',
    'App\\Services\\Logger',
    'App\\Services\\PluginManager',
    'App\\Controllers\\HomeController',
    'App\\Controllers\\AuthController',
];

foreach ($coreClasses as $class) {
    if (class_exists($class)) {
        echo "<div class='test-item pass'>✓ Class exists: {$class}</div>\n";
        $passes[] = "Class: {$class}";
    } else {
        echo "<div class='test-item fail'>✗ Class not found: {$class}</div>\n";
        $errors[] = "Class not found: {$class}";
    }
}

echo "</div>\n";

// ============================================
// 5. Database Connection Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>5. Database Connection</h2>\n";

try {
    if (class_exists('App\\Core\\Database')) {
        $db = new App\Core\Database();
        echo "<div class='test-item pass'>✓ Database class instantiated</div>\n";
        $passes[] = "Database connection successful";

        // Check for critical tables
        $tables = ['users', 'settings', 'calculations'];
        foreach ($tables as $table) {
            // This is a simple check, adjust based on your Database class methods
            echo "<div class='test-item info'>ℹ Table check would require query: {$table}</div>\n";
        }
    } else {
        echo "<div class='test-item warn'>⚠ Database class not found</div>\n";
        $warnings[] = "Database class not available";
    }
} catch (Exception $e) {
    echo "<div class='test-item fail'>✗ Database error: " . htmlspecialchars($e->getMessage()) . "</div>\n";
    $errors[] = "Database connection failed";
}

echo "</div>\n";

// ============================================
// 6. Routes File Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>6. Routes Configuration</h2>\n";

$routesFile = (defined('BASE_PATH') ? BASE_PATH : __DIR__) . '/app/routes.php';
if (file_exists($routesFile)) {
    echo "<div class='test-item pass'>✓ Routes file exists: {$routesFile}</div>\n";

    // Check for syntax errors
    $output = [];
    $return_var = 0;
    exec("php -l " . escapeshellarg($routesFile) . " 2>&1", $output, $return_var);

    if ($return_var === 0) {
        echo "<div class='test-item pass'>✓ Routes file has no syntax errors</div>\n";
        $passes[] = "Routes file syntax valid";
    } else {
        echo "<div class='test-item fail'>✗ Routes file has syntax errors:</div>\n";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>\n";
        $errors[] = "Routes file has syntax errors";
    }

    // Check file size and potential issues
    $fileSize = filesize($routesFile);
    echo "<div class='test-item info'>ℹ Routes file size: " . number_format($fileSize) . " bytes</div>\n";

    // Check for common issues in routes
    $routesContent = file_get_contents($routesFile);

    // Check for stray closing tags
    if (preg_match('/\?>\s*\S/', $routesContent)) {
        echo "<div class='test-item warn'>⚠ Warning: Possible content after closing PHP tag</div>\n";
        $warnings[] = "Routes file may have content after closing PHP tag";
    }

    // Check for plugin boot
    if (strpos($routesContent, 'PluginManager') !== false) {
        echo "<div class='test-item info'>ℹ Plugin system integration found in routes</div>\n";

        // Check for table existence guard
        if (strpos($routesContent, 'table_exists') !== false || strpos($routesContent, 'SHOW TABLES') !== false) {
            echo "<div class='test-item pass'>✓ Plugin boot has table existence check</div>\n";
            $passes[] = "Plugin boot protected with table check";
        } else {
            echo "<div class='test-item warn'>⚠ Plugin boot may not check if tables exist</div>\n";
            $warnings[] = "Plugin boot should check for table existence";
        }
    }

} else {
    echo "<div class='test-item fail'>✗ Routes file not found: {$routesFile}</div>\n";
    $errors[] = "Routes file does not exist";
}

echo "</div>\n";

// ============================================
// 7. View System Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>7. View System & Templates</h2>\n";

$viewPaths = [
    'themes/default/views',
    'themes/default/views/layouts',
    'themes/default/views/partials',
    'themes/default/views/auth',
    'app/Views',
    'app/Views/layouts',
];

foreach ($viewPaths as $viewPath) {
    $fullPath = (defined('BASE_PATH') ? BASE_PATH : __DIR__) . '/' . $viewPath;
    if (is_dir($fullPath)) {
        echo "<div class='test-item pass'>✓ View directory exists: {$viewPath}</div>\n";
        $passes[] = "View path: {$viewPath}";
    } else {
        echo "<div class='test-item warn'>⚠ View directory missing: {$viewPath}</div>\n";
        $warnings[] = "View directory missing: {$viewPath}";
    }
}

// Check critical view files
$criticalViews = [
    'themes/default/views/index.php',
    'themes/default/views/layouts/main.php',
    'themes/default/views/auth/login.php',
    'themes/default/views/partials/header.php',
];

foreach ($criticalViews as $viewFile) {
    $fullPath = (defined('BASE_PATH') ? BASE_PATH : __DIR__) . '/' . $viewFile;
    if (file_exists($fullPath)) {
        echo "<div class='test-item pass'>✓ View file exists: {$viewFile}</div>\n";
        $passes[] = "View file: {$viewFile}";
    } else {
        echo "<div class='test-item fail'>✗ Critical view missing: {$viewFile}</div>\n";
        $errors[] = "Critical view missing: {$viewFile}";
    }
}

echo "</div>\n";

// ============================================
// 8. Session & Security Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>8. Session & Security</h2>\n";

if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<div class='test-item pass'>✓ Session is active</div>\n";
    echo "<div class='test-item info'>ℹ Session ID: " . substr(session_id(), 0, 10) . "...</div>\n";
    $passes[] = "Session active";
} else {
    echo "<div class='test-item warn'>⚠ Session not started (normal if not needed yet)</div>\n";
}

// Check CSRF token
if (function_exists('csrf_token')) {
    try {
        $token = csrf_token();
        echo "<div class='test-item pass'>✓ CSRF token generated: " . substr($token, 0, 10) . "...</div>\n";
        $passes[] = "CSRF token generation works";
    } catch (Exception $e) {
        echo "<div class='test-item fail'>✗ CSRF token error: " . htmlspecialchars($e->getMessage()) . "</div>\n";
        $errors[] = "CSRF token generation failed";
    }
}

echo "</div>\n";

// ============================================
// 9. File Permissions Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>9. File Permissions</h2>\n";

$writableDirs = [
    'storage',
    'storage/logs',
    'storage/cache',
    'storage/uploads',
];

foreach ($writableDirs as $dir) {
    $fullPath = (defined('BASE_PATH') ? BASE_PATH : __DIR__) . '/' . $dir;
    if (is_dir($fullPath)) {
        if (is_writable($fullPath)) {
            echo "<div class='test-item pass'>✓ Directory is writable: {$dir}</div>\n";
            $passes[] = "Writable: {$dir}";
        } else {
            echo "<div class='test-item warn'>⚠ Directory not writable: {$dir}</div>\n";
            $warnings[] = "Directory not writable: {$dir}";
        }
    } else {
        echo "<div class='test-item warn'>⚠ Directory missing: {$dir}</div>\n";
        $warnings[] = "Directory missing: {$dir}";
    }
}

echo "</div>\n";

// ============================================
// SUMMARY
// ============================================
echo "<div class='section'>\n";
echo "<h2>📊 Summary</h2>\n";

$totalTests = count($passes) + count($warnings) + count($errors);
echo "<div style='display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0;'>\n";
echo "<div style='text-align: center; padding: 20px; background: #d4edda; border-radius: 5px;'>\n";
echo "<h3 style='margin: 0; color: #155724;'>" . count($passes) . "</h3>\n";
echo "<p style='margin: 5px 0; color: #155724;'>Passed</p>\n";
echo "</div>\n";
echo "<div style='text-align: center; padding: 20px; background: #fff3cd; border-radius: 5px;'>\n";
echo "<h3 style='margin: 0; color: #856404;'>" . count($warnings) . "</h3>\n";
echo "<p style='margin: 5px 0; color: #856404;'>Warnings</p>\n";
echo "</div>\n";
echo "<div style='text-align: center; padding: 20px; background: #f8d7da; border-radius: 5px;'>\n";
echo "<h3 style='margin: 0; color: #721c24;'>" . count($errors) . "</h3>\n";
echo "<p style='margin: 5px 0; color: #721c24;'>Errors</p>\n";
echo "</div>\n";
echo "</div>\n";

if (count($errors) > 0) {
    echo "<h3 class='error'>❌ Critical Errors Found:</h3>\n";
    echo "<ul>\n";
    foreach ($errors as $error) {
        echo "<li class='error'>" . htmlspecialchars($error) . "</li>\n";
    }
    echo "</ul>\n";
}

if (count($warnings) > 0) {
    echo "<h3 class='warning'>⚠️ Warnings:</h3>\n";
    echo "<ul>\n";
    foreach ($warnings as $warning) {
        echo "<li class='warning'>" . htmlspecialchars($warning) . "</li>\n";
    }
    echo "</ul>\n";
}

if (count($errors) === 0 && count($warnings) === 0) {
    echo "<div style='text-align: center; padding: 30px; background: #d4edda; border-radius: 10px; margin: 20px 0;'>\n";
    echo "<h2 style='color: #155724; margin: 0;'>✅ All Checks Passed!</h2>\n";
    echo "<p style='color: #155724;'>The system appears to be properly configured.</p>\n";
    echo "</div>\n";
}

echo "</div>\n";

// ============================================
// RECOMMENDATIONS
// ============================================
if (count($errors) > 0 || count($warnings) > 0) {
    echo "<div class='section'>\n";
    echo "<h2>💡 Recommendations</h2>\n";
    echo "<ul>\n";

    if (in_array("Helpers not loaded in bootstrap - will cause errors in views", $warnings)) {
        echo "<li><strong>Critical:</strong> Add <code>require_once APP_PATH . '/Helpers/functions.php';</code> to <code>app/bootstrap.php</code> before any views are rendered.</li>\n";
    }

    if (count($errors) > 0) {
        echo "<li><strong>Fix all critical errors</strong> before deploying to production.</li>\n";
    }

    if (count($warnings) > 0) {
        echo "<li>Review and address <strong>warnings</strong> to ensure optimal system operation.</li>\n";
    }

    echo "<li>Test all routes, especially: <code>/</code>, <code>/login</code>, <code>/register</code>, <code>/admin</code></li>\n";
    echo "<li>Verify database tables exist: users, settings, calculations, plugins</li>\n";
    echo "<li>Check browser console for any JavaScript/CSS loading errors</li>\n";
    echo "</ul>\n";
    echo "</div>\n";
}

echo "<div style='text-align: center; padding: 20px; color: #666; margin-top: 40px; border-top: 1px solid #ddd;'>\n";
echo "<p>Generated by Bishwo Calculator Error Check Script v1.0</p>\n";
echo "<p><a href='/'>← Back to Home</a> | <a href='javascript:location.reload()'>🔄 Refresh</a></p>\n";
echo "</div>\n";

echo "</body></html>\n";
