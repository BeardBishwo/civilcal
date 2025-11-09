<?php
/**
 * Bishwo Calculator - Installation Test & Debug Script
 * Comprehensive testing of installation functionality
 * 
 * @package BishwoCalculator
 * @version 1.0.0
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Bishwo Calculator - Installation Test & Debug</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .test-section { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: #28a745; }
    .error { color: #dc3545; }
    .warning { color: #ffc107; }
    .info { color: #17a2b8; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
    .test-result { padding: 10px; margin: 5px 0; border-radius: 4px; }
    .test-pass { background: #d4edda; color: #155724; }
    .test-fail { background: #f8d7da; color: #721c24; }
    .test-warning { background: #fff3cd; color: #856404; }
</style>";

// Test 1: PHP Version and Extensions
echo "<div class='test-section'>";
echo "<h2>1. PHP Environment Test</h2>";

$phpVersion = phpversion();
echo "<p>PHP Version: <strong>$phpVersion</strong></p>";

$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'curl', 'gd', 'mbstring', 'openssl'];
$extensionResults = [];

foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    $extensionResults[$ext] = $loaded;
    $status = $loaded ? "✅" : "❌";
    $class = $loaded ? "success" : "error";
    echo "<p class='$class'>$status $ext: " . ($loaded ? "Loaded" : "Missing") . "</p>";
}

// File permissions test
$testDirs = [
    'storage' => '../storage',
    'logs' => '../storage/logs', 
    'cache' => '../storage/cache',
    'sessions' => '../storage/sessions'
];

echo "<h3>Directory Permissions</h3>";
foreach ($testDirs as $name => $dir) {
    $exists = is_dir($dir);
    $writable = is_writable($dir);
    $status = $exists ? ($writable ? "✅" : "⚠️") : "❌";
    $class = $exists ? ($writable ? "success" : "warning") : "error";
    echo "<p class='$class'>$status $name: " . ($exists ? ($writable ? "Writable" : "Not Writable") : "Does not exist") . "</p>";
}
echo "</div>";

// Test 2: PHPMailer Integration
echo "<div class='test-section'>";
echo "<h2>2. PHPMailer Test</h2>";

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        echo "<p class='success'>✅ PHPMailer is available</p>";
        
        // Test PHPMailer instantiation
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            echo "<p class='success'>✅ PHPMailer can be instantiated</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ PHPMailer instantiation failed: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='error'>❌ PHPMailer class not found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ PHPMailer test failed: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 3: Database Connection Test
echo "<div class='test-section'>";
echo "<h2>3. Database Connection Test</h2>";

// Get database config from session or test
$dbConfig = [
    'host' => 'localhost',
    'name' => 'test_db',
    'user' => 'root',
    'pass' => ''
];

// Test with common database names
$testDatabases = ['bishwo_calculator', 'test_db', 'mysql'];

foreach ($testDatabases as $dbName) {
    try {
        $pdo = new PDO("mysql:host={$dbConfig['host']};dbname={$dbName}", $dbConfig['user'], $dbConfig['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Test query
        $stmt = $pdo->query("SELECT VERSION() as version");
        $result = $stmt->fetch();
        
        echo "<p class='success'>✅ Database connection successful to: $dbName</p>";
        echo "<p class='info'>MySQL Version: " . $result['version'] . "</p>";
        
        // Check for existing tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "<p class='info'>Found " . count($tables) . " tables in database</p>";
        } else {
            echo "<p class='warning'>⚠️ No tables found in database</p>";
        }
        
        break; // Stop on first successful connection
        
    } catch (PDOException $e) {
        echo "<p class='error'>❌ Database connection failed to $dbName: " . $e->getMessage() . "</p>";
    }
}
echo "</div>";

// Test 4: Session Test
echo "<div class='test-section'>";
echo "<h2>4. Session Test</h2>";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['test'] = 'Installation test';
if (isset($_SESSION['test'])) {
    echo "<p class='success'>✅ Session functionality working</p>";
    echo "<p class='info'>Session ID: " . session_id() . "</p>";
} else {
    echo "<p class='error'>❌ Session functionality not working</p>";
}
echo "</div>";

// Test 5: File System Test
echo "<div class='test-section'>";
echo "<h2>5. File System Test</h2>";

$testFiles = [
    'autoload.php' => '../vendor/autoload.php',
    'install.css' => 'assets/css/install.css',
    'install.js' => 'assets/js/install.js',
    'banner.jpg' => 'assets/images/banner.jpg'
];

foreach ($testFiles as $name => $file) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "<p class='success'>✅ $name: Found ($size bytes)</p>";
    } else {
        echo "<p class='error'>❌ $name: Missing</p>";
    }
}
echo "</div>";

// Test 6: AJAX Endpoint Test
echo "<div class='test-section'>";
echo "<h2>6. AJAX Endpoint Test</h2>";

$ajaxFile = 'ajax/test-email.php';
if (file_exists($ajaxFile)) {
    echo "<p class='success'>✅ Email test endpoint exists</p>";
    
    // Check if file is readable
    if (is_readable($ajaxFile)) {
        echo "<p class='success'>✅ Email test endpoint is readable</p>";
    } else {
        echo "<p class='error'>❌ Email test endpoint is not readable</p>";
    }
} else {
    echo "<p class='error'>❌ Email test endpoint missing</p>";
}
echo "</div>";

// Test 7: Installation Steps Test
echo "<div class='test-section'>";
echo "<h2>7. Installation Steps Test</h2>";

try {
    require_once 'includes/Installer.php';
    $installer = new Installer();
    $steps = ['welcome', 'requirements', 'permissions', 'database', 'admin', 'email', 'finish'];

    foreach ($steps as $step) {
        try {
            $output = $installer->renderStep($step);
            $length = strlen($output);
            echo "<p class='success'>✅ Step '$step': Generated ($length characters)</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Step '$step': Failed - " . $e->getMessage() . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Installer class test failed: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 8: Environment Configuration Test
echo "<div class='test-section'>";
echo "<h2>8. Environment Configuration Test</h2>";

$envFile = '../.env';
if (file_exists($envFile)) {
    echo "<p class='success'>✅ .env file exists</p>";
    $envContent = file_get_contents($envFile);
    echo "<p class='info'>Size: " . strlen($envContent) . " characters</p>";
} else {
    echo "<p class='warning'>⚠️ .env file not found (this is normal for new installation)</p>";
}
echo "</div>";

// Summary
echo "<div class='test-section'>";
echo "<h2>📋 Test Summary</h2>";
echo "<p>All tests completed. Please check the results above for any issues.</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Fix any PHP extension issues</li>";
echo "<li>Ensure database is accessible</li>";
echo "<li>Verify file permissions for storage directories</li>";
echo "<li>Test SMTP configuration with real credentials</li>";
echo "<li>Run full installation process</li>";
echo "</ul>";
echo "</div>";
?>
