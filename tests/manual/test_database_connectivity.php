<?php
// Database Connectivity Test Script
echo "=== Database Connectivity Test ===\n\n";

// Load environment variables from .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "✓ .env file found\n";
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
} else {
    echo "❌ .env file not found\n";
}

// Database configuration
$host = $_ENV['DB_HOST'] ?? 'localhost';
$database = $_ENV['DB_DATABASE'] ?? 'bishwo_calculator';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

echo "\nDatabase Configuration:\n";
echo "Host: $host\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "Password: " . (empty($password) ? '(empty)' : '***') . "\n";

// Test database connection
try {
    echo "\n=== Testing Database Connection ===\n";
    
    $dsn = "mysql:host=$host; charset=utf8mb4";
    echo "Connecting to MySQL server at $host...\n";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    echo "✓ Successfully connected to MySQL server\n";
    
    // Test if database exists
    echo "\n=== Testing Database Existence ===\n";
    try {
        $pdo->exec("USE `$database`");
        echo "✓ Database '$database' exists and accessible\n";
        
        // Get database info
        $result = $pdo->query("SELECT VERSION() as version");
        $dbVersion = $result->fetch();
        echo "MySQL Version: " . $dbVersion['version'] . "\n";
        
        // List tables
        echo "\n=== Database Tables ===\n";
        $tables = $pdo->query("SHOW TABLES")->fetchAll();
        if (empty($tables)) {
            echo "No tables found in database '$database'\n";
        } else {
            echo "Found " . count($tables) . " table(s):\n";
            foreach ($tables as $table) {
                $tableName = array_values($table)[0];
                echo "- $tableName\n";
                
                // Check table structure for key tables
                if (in_array($tableName, ['users', 'user_sessions', 'calculators'])) {
                    echo "  Checking structure of $tableName...\n";
                    $columns = $pdo->query("DESCRIBE `$tableName`")->fetchAll();
                    foreach ($columns as $column) {
                        echo "    " . $column['Field'] . " (" . $column['Type'] . ")\n";
                    }
                }
            }
        }
        
    } catch (PDOException $e) {
        echo "❌ Database '$database' does not exist or is not accessible: " . $e->getMessage() . "\n";
        echo "You may need to create the database first.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your database configuration and ensure MySQL is running.\n";
}

echo "\n=== Test Complete ===\n";