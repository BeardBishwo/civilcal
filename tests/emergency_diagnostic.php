<?php
/**
 * Emergency Diagnostic Script
 * Comprehensive testing to identify HTTP 500 error root cause
 */

// Enable maximum error reporting for diagnostics
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../debug/emergency_diagnostic.log');

echo "<h1>Bishwo Calculator - Emergency Diagnostic</h1>";
echo "<p>Starting comprehensive diagnostic check...</p>";
echo "<hr>";

// Test 1: File System and Permissions
echo "<h2>1. File System & Permissions Test</h2>";
$testResults = [];

// Check critical directories
$criticalDirs = [
    '../app' => 'Application directory',
    '../config' => 'Configuration directory', 
    '../storage' => 'Storage directory',
    '../themes' => 'Themes directory',
    '../public' => 'Public directory',
    '../debug' => 'Debug directory'
];

foreach ($criticalDirs as $dir => $description) {
    if (is_dir($dir)) {
        $readable = is_readable($dir);
        $writable = is_writable($dir);
        $testResults[] = "✓ $description: Found (readable: " . ($readable ? "YES" : "NO") . ", writable: " . ($writable ? "YES" : "NO") . ")";
    } else {
        $testResults[] = "✗ $description: MISSING";
    }
}

// Check critical files
$criticalFiles = [
    '../app/bootstrap.php' => 'Application bootstrap',
    '../config/app.php' => 'Application config',
    '../config/database.php' => 'Database config',
    '../app/routes.php' => 'Routes file',
    '../public/index.php' => 'Public index',
    '../.env' => 'Environment file',
    '../config/installed.lock' => 'Installation lock'
];

foreach ($criticalFiles as $file => $description) {
    if (file_exists($file)) {
        $readable = is_readable($file);
        $testResults[] = "✓ $description: Found (readable: " . ($readable ? "YES" : "NO") . ")";
    } else {
        $testResults[] = "✗ $description: MISSING";
    }
}

foreach ($testResults as $result) {
    echo $result . "<br>";
}

echo "<hr>";

// Test 2: PHP Version and Extensions
echo "<h2>2. PHP Environment Test</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server API: " . php_sapi_name() . "<br>";

// Check critical extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mysqli', 'json', 'session'];
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "Extension '$ext': " . ($loaded ? "✓ Loaded" : "✗ Missing") . "<br>";
}

echo "<hr>";

// Test 3: Database Connection
echo "<h2>3. Database Connection Test</h2>";

try {
    // Check if database config exists
    if (file_exists('../config/database.php')) {
        $dbConfig = require_once '../config/database.php';
        echo "✓ Database config loaded<br>";
        
        // Test PDO connection
        if (isset($dbConfig['pdo'])) {
            $pdo = new PDO(
                $dbConfig['pdo']['dsn'],
                $dbConfig['pdo']['username'],
                $dbConfig['pdo']['password'],
                $dbConfig['pdo']['options']
            );
            echo "✓ PDO connection successful<br>";
            
            // Test a simple query
            $stmt = $pdo->query("SELECT 1 as test");
            $result = $stmt->fetch();
            echo "✓ Database query test successful: " . $result['test'] . "<br>";
        } else {
            echo "✗ PDO configuration missing<br>";
        }
    } else {
        echo "✗ Database config file missing<br>";
    }
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 4: Application Bootstrap
echo "<h2>4. Application Bootstrap Test</h2>";

try {
    // Test bootstrap loading
    define('BASE_PATH', dirname(__DIR__));
    define('APP_PATH', BASE_PATH . '/app');
    define('CONFIG_PATH', BASE_PATH . '/config');
    define('STORAGE_PATH', BASE_PATH . '/storage');
    
    echo "✓ Application paths defined<br>";
    
    // Test autoloader
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = APP_PATH . '/';
        
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require $file;
        }
    });
    
    echo "✓ Autoloader registered<br>";
    
    // Test config loading
    $appConfig = require_once CONFIG_PATH . '/app.php';
    $dbConfig = require_once CONFIG_PATH . '/database.php';
    echo "✓ Configuration loaded<br>";
    
    // Test session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo "✓ Session started<br>";
    
} catch (Exception $e) {
    echo "✗ Bootstrap failed: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 5: Core Classes Loading
echo "<h2>5. Core Classes Test</h2>";

$coreClasses = [
    'App\\Core\\Router' => 'Router class',
    'App\\Core\\Controller' => 'Controller base class',
    'App\\Core\\View' => 'View class',
    'App\\Core\\Database' => 'Database class',
    'App\\Controllers\\HomeController' => 'HomeController class'
];

foreach ($coreClasses as $className => $description) {
    if (class_exists($className)) {
        echo "✓ $description: Loaded<br>";
    } else {
        echo "✗ $description: NOT FOUND<br>";
    }
}

echo "<hr>";

// Test 6: Router System
echo "<h2>6. Router System Test</h2>";

try {
    $router = new \App\Core\Router();
    echo "✓ Router instantiated<br>";
    
    // Test route definition
    $router->add('GET', '/test', 'TestController@test');
    echo "✓ Route added successfully<br>";
    
    // Test base path detection
    $basePath = $router->getBasePath();
    echo "Base path detected: " . ($basePath ? $basePath : "None") . "<br>";
    
} catch (Exception $e) {
    echo "✗ Router test failed: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 7: ProCalculator Theme Verification
echo "<h2>7. ProCalculator Theme Test</h2>";

$themeViews = [
    '../themes/procalculator/views/home/index.php' => 'Home Index',
    '../themes/procalculator/views/home/features.php' => 'Home Features', 
    '../themes/procalculator/views/home/pricing.php' => 'Home Pricing',
    '../themes/procalculator/views/home/about.php' => 'Home About',
    '../themes/procalculator/views/home/contact.php' => 'Home Contact'
];

foreach ($themeViews as $viewFile => $description) {
    if (file_exists($viewFile)) {
        echo "✓ $description: Found<br>";
    } else {
        echo "✗ $description: MISSING<br>";
    }
}

echo "<hr>";

// Test 8: HomeController Test
echo "<h2>8. HomeController Test</h2>";

try {
    // Test controller instantiation
    $controller = new \App\Controllers\HomeController();
    echo "✓ HomeController instantiated successfully<br>";
    
    // Test if methods exist
    $methods = ['index', 'features', 'pricing', 'about', 'contact'];
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "✓ Method '$method' exists<br>";
        } else {
            echo "✗ Method '$method' missing<br>";
        }
    }
    
} catch (Exception $e) {
    echo "✗ HomeController test failed: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 9: Error Simulation
echo "<h2>9. Error Simulation Test</h2>";

// Try to simulate a request to see what would happen
try {
    // Simulate request
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['SCRIPT_NAME'] = '/bishwo_calculator/public/index.php';
    
    echo "Simulating home page request...<br>";
    
    // Test if routing would work
    $router = new \App\Core\Router();
    $routes = require '../app/routes.php';
    echo "✓ Routes file loaded<br>";
    
} catch (Exception $e) {
    echo "✗ Simulation failed: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 10: Installation Status
echo "<h2>10. Installation Status</h2>";

$installStatus = [
    'Config installed.lock' => file_exists('../config/installed.lock'),
    'Environment file (.env)' => file_exists('../.env'),
    'Application bootstrap' => file_exists('../app/bootstrap.php'),
    'Routes configuration' => file_exists('../app/routes.php')
];

foreach ($installStatus as $check => $status) {
    echo "$check: " . ($status ? "✓ Complete" : "✗ Incomplete") . "<br>";
}

echo "<hr>";
echo "<h2>Diagnostic Complete</h2>";
echo "<p>Check the results above to identify the issue causing the HTTP 500 error.</p>";
echo "<p>Review any ✗ marked items as these indicate potential problems.</p>";
echo "<p>Emergency log saved to: debug/emergency_diagnostic.log</p>";
?>


