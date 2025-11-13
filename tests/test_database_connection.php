<?php
/**
 * Database connection test
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 DATABASE CONNECTION TEST\n";
echo "==========================\n\n";

echo "Step 1: Setting up BASE_PATH...\n";
define('BASE_PATH', __DIR__);
echo "✅ BASE_PATH: " . BASE_PATH . "\n\n";

echo "Step 2: Loading database config...\n";
$configFile = BASE_PATH . '/config/database.php';
echo "Config file path: {$configFile}\n";
echo "File exists: " . (file_exists($configFile) ? 'YES' : 'NO') . "\n\n";

if (!file_exists($configFile)) {
    echo "❌ Config file missing\n";
    exit(1);
}

try {
    $config = include $configFile;
    echo "✅ Config loaded\n";
    echo "Host: " . $config['host'] . "\n";
    echo "Database: " . $config['database'] . "\n";
    echo "Username: " . $config['username'] . "\n";
    echo "Password: " . ($config['password'] ? '***' : '(empty)') . "\n\n";
} catch (Exception $e) {
    echo "❌ Config loading failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Step 3: Testing MySQL connection directly...\n";
try {
    $startTime = microtime(true);
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']}",
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5 // 5 second timeout
        ]
    );
    $endTime = microtime(true);
    $connectionTime = round(($endTime - $startTime) * 1000, 2);
    
    echo "✅ MySQL connection successful!\n";
    echo "Connection time: {$connectionTime}ms\n\n";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "✅ Test query successful: " . $result['test'] . "\n\n";
    
} catch (PDOException $e) {
    echo "❌ MySQL connection failed: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n\n";
    
    // Try to determine the specific issue
    $errorMsg = $e->getMessage();
    if (strpos($errorMsg, 'Access denied') !== false) {
        echo "🔍 ISSUE: Wrong username/password or insufficient privileges\n";
    } elseif (strpos($errorMsg, 'Unknown database') !== false) {
        echo "🔍 ISSUE: Database '{$config['database']}' doesn't exist\n";
    } elseif (strpos($errorMsg, 'Connection refused') !== false) {
        echo "🔍 ISSUE: MySQL server is not running or not accessible\n";
    } elseif (strpos($errorMsg, 'timeout') !== false) {
        echo "🔍 ISSUE: Connection timeout - server may be down\n";
    } else {
        echo "🔍 ISSUE: Unknown connection problem\n";
    }
    echo "\n";
    exit(1);
}

echo "Step 4: Loading bootstrap for autoloader...\n";
try {
    require_once BASE_PATH . '/app/bootstrap.php';
    echo "✅ Bootstrap loaded for autoloader\n\n";
} catch (Exception $e) {
    echo "❌ Bootstrap loading failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Step 5: Testing Database singleton...\n";
try {
    $startTime = microtime(true);
    $db = \App\Core\Database::getInstance();
    $endTime = microtime(true);
    $singletonTime = round(($endTime - $startTime) * 1000, 2);
    
    echo "✅ Database singleton works!\n";
    echo "Singleton creation time: {$singletonTime}ms\n\n";
    
} catch (Exception $e) {
    echo "❌ Database singleton failed: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n\n";
    exit(1);
}

echo "🎉 Database connection test completed successfully!\n";
?>


