<?php
/**
 * Isolated Database class test
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 ISOLATED DATABASE TEST\n";
echo "========================\n\n";

echo "Step 1: Setting up BASE_PATH...\n";
define('BASE_PATH', __DIR__);
echo "✅ BASE_PATH: " . BASE_PATH . "\n\n";

echo "Step 2: Including Database class directly...\n";
$databaseFile = BASE_PATH . '/app/Core/Database.php';
echo "Database file: {$databaseFile}\n";
echo "File exists: " . (file_exists($databaseFile) ? 'YES' : 'NO') . "\n\n";

if (!file_exists($databaseFile)) {
    echo "❌ Database file missing\n";
    exit(1);
}

require_once $databaseFile;

echo "Step 3: Testing PDOCompat class...\n";
$pdoCompatFile = BASE_PATH . '/app/Core/PDOCompat.php';
if (file_exists($pdoCompatFile)) {
    require_once $pdoCompatFile;
    echo "✅ PDOCompat loaded\n\n";
} else {
    echo "⚠️ PDOCompat not found (optional)\n\n";
}

echo "Step 4: Testing Database class loading...\n";
try {
    $classExists = class_exists('\App\Core\Database');
    echo "✅ Database class exists: " . ($classExists ? 'YES' : 'NO') . "\n\n";
} catch (Exception $e) {
    echo "❌ Database class check failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "Step 5: Testing database config loading...\n";
try {
    $configFile = BASE_PATH . '/config/database.php';
    $config = include $configFile;
    echo "✅ Config loaded\n";
    echo "Host: " . $config['host'] . "\n";
    echo "Database: " . $config['database'] . "\n";
    echo "Username: " . $config['username'] . "\n";
    echo "Password: " . ($config['password'] ? '***' : '(empty)') . "\n\n";
} catch (Exception $e) {
    echo "❌ Config loading failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "Step 6: Testing Database constructor directly...\n";
try {
    echo "Starting Database constructor at: " . date('H:i:s') . "\n";
    $startTime = microtime(true);
    
    // Create a reflection to see if we can access the constructor
    $reflection = new ReflectionClass('\App\Core\Database');
    echo "✅ Database class is reflectable\n";
    
    // Try to get the constructor
    $constructor = $reflection->getConstructor();
    if ($constructor) {
        echo "✅ Constructor found\n";
        echo "Constructor visibility: " . ($constructor->isPrivate() ? 'PRIVATE' : 'PUBLIC/PROTECTED') . "\n";
    } else {
        echo "⚠️ No constructor found (using default)\n";
    }
    
    // Try to create instance using reflection
    $constructor = $reflection->getConstructor();
    if ($constructor && $constructor->isPrivate()) {
        echo "⚠️ Constructor is private, singleton pattern detected\n";
        
        // Test the singleton method
        echo "Testing getInstance() method...\n";
        $instance = \App\Core\Database::getInstance();
        echo "✅ getInstance() returned: " . get_class($instance) . "\n";
    } else {
        echo "Attempting to create instance directly...\n";
        $instance = new \App\Core\Database();
        echo "✅ Direct instantiation successful\n";
    }
    
    $endTime = microtime(true);
    $creationTime = round(($endTime - $startTime) * 1000, 2);
    echo "Creation time: {$creationTime}ms\n\n";
    
} catch (Exception $e) {
    echo "❌ Database constructor test failed: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
    echo "📍 Time elapsed: " . round((microtime(true) - $startTime) * 1000, 2) . "ms\n";
    echo "📍 Trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}

echo "Step 7: Testing PDO connection access...\n";
try {
    $pdo = $instance->getPdo();
    echo "✅ PDO object retrieved\n";
    echo "PDO class: " . get_class($pdo) . "\n";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "✅ Test query successful: " . $result['test'] . "\n\n";
    
} catch (Exception $e) {
    echo "❌ PDO access test failed: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n\n";
    exit(1);
}

echo "🎉 Isolated Database test completed successfully!\n";
?>


