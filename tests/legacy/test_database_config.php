<?php
/**
 * Database Configuration Verification Script
 * Tests the fixed database configuration to ensure it's working properly
 */

echo "=== Bishwo Calculator Database Configuration Verification ===\n\n";

// Test 1: Check if .env file exists and load it
echo "1. Testing .env file loading...\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "✓ .env file found\n";
    
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $dbConfig = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (in_array($name, ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'])) {
            $dbConfig[$name] = $value;
        }
    }
    
    echo "✓ Database configuration from .env:\n";
    echo "   - DB_HOST: " . ($dbConfig['DB_HOST'] ?? 'NOT SET') . "\n";
    echo "   - DB_DATABASE: " . ($dbConfig['DB_DATABASE'] ?? 'NOT SET') . "\n";
    echo "   - DB_USERNAME: " . ($dbConfig['DB_USERNAME'] ?? 'NOT SET') . "\n";
    echo "   - DB_PASSWORD: " . (!empty($dbConfig['DB_PASSWORD']) ? '***' : 'NOT SET') . "\n";
} else {
    echo "✗ .env file not found\n";
    exit(1);
}

// Test 2: Check config/database.php
echo "\n2. Testing config/database.php...\n";
$configFile = __DIR__ . '/config/database.php';
if (file_exists($configFile)) {
    echo "✓ config/database.php file found\n";
    
    try {
        // Simulate the include to check if it loads properly
        $config = include $configFile;
        if (is_array($config)) {
            echo "✓ config/database.php loads as array\n";
            echo "   - Database: " . ($config['database'] ?? 'NOT SET') . "\n";
            echo "   - Host: " . ($config['host'] ?? 'NOT SET') . "\n";
            echo "   - Username: " . ($config['username'] ?? 'NOT SET') . "\n";
        } else {
            echo "✗ config/database.php does not return an array\n";
            exit(1);
        }
    } catch (Exception $e) {
        echo "✗ Error loading config/database.php: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "✗ config/database.php file not found\n";
    exit(1);
}

// Test 3: Test database connection
echo "\n3. Testing database connection...\n";
try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['DB_HOST']};dbname={$dbConfig['DB_DATABASE']}",
        $dbConfig['DB_USERNAME'],
        $dbConfig['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test with a simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    
    if ($result['test'] == 1) {
        echo "✓ Database connection successful\n";
    }
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "\nPossible solutions:\n";
    echo "- Check if MySQL server is running\n";
    echo "- Verify database credentials in .env file\n";
    echo "- Create the database if it doesn't exist\n";
    exit(1);
}

// Test 4: Test legacy Database class
echo "\n4. Testing legacy Database class...\n";
require_once __DIR__ . '/app/Core/DatabaseLegacy.php';
try {
    $legacyDb = new Database();
    $conn = $legacyDb->getConnection();
    if ($conn) {
        echo "✓ Legacy Database class connection successful\n";
    } else {
        echo "✗ Legacy Database class connection failed\n";
    }
} catch (Exception $e) {
    echo "✗ Legacy Database class error: " . $e->getMessage() . "\n";
}

// Test 5: Test modern Database class
echo "\n5. Testing modern Database class...\n";
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database as ModernDatabase;
try {
    $modernDb = ModernDatabase::getInstance();
    echo "✓ Modern Database class instantiation successful\n";
} catch (Exception $e) {
    echo "✗ Modern Database class error: " . $e->getMessage() . "\n";
}

echo "\n=== Configuration Verification Complete ===\n";
echo "If all tests passed, your database configuration is working properly.\n";
echo "You can now run the installation process.\n";
?>



