<?php
/**
 * Database Connection Test
 * Tests different database connection scenarios
 */

echo "<h2>Database Connection Test</h2>";

// Test 1: Current .env configuration
echo "<h3>Test 1: Current .env Configuration</h3>";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envVars = parse_ini_file($envFile);
    if ($envVars && isset($envVars['DB_HOST'], $envVars['DB_DATABASE'], $envVars['DB_USERNAME'])) {
        try {
            $dsn = "mysql:host={$envVars['DB_HOST']};dbname={$envVars['DB_DATABASE']}";
            $pdo = new PDO($dsn, $envVars['DB_USERNAME'], $envVars['DB_PASSWORD'] ?? '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "✅ Connection successful with .env config<br>";
        } catch (Exception $e) {
            echo "❌ Connection failed: " . $e->getMessage() . "<br>";
        }
    }
}

// Test 2: Standard Laragon configuration
echo "<h3>Test 2: Standard Laragon Configuration (root/empty password)</h3>";
$testConfigs = [
    ['host' => 'localhost', 'db' => 'uniquebishwo', 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'db' => 'uniquebishwo', 'user' => 'uniquebishwo', 'pass' => 'c9PU7XAsAADYk_A'],
    ['host' => 'localhost', 'db' => 'bishwo_calculator', 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'db' => 'bishwo_calculator', 'user' => 'root', 'pass' => 'c9PU7XAsAADYk_A'],
];

foreach ($testConfigs as $i => $config) {
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['db']}";
        $pdo = new PDO($dsn, $config['user'], $config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ Test " . ($i+1) . ": Connected successfully<br>";
        echo "&nbsp;&nbsp;→ Host: {$config['host']}, DB: {$config['db']}, User: {$config['user']}<br>";
        break;
    } catch (Exception $e) {
        echo "❌ Test " . ($i+1) . ": Failed - " . $e->getMessage() . "<br>";
    }
}

// Test 3: Check if database exists
echo "<h3>Test 3: Database Existence Check</h3>";
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Available databases:<br>";
    foreach ($databases as $db) {
        if (strpos(strtolower($db), 'bishwo') !== false || strpos(strtolower($db), 'unique') !== false) {
            echo "🎯 <strong>$db</strong><br>";
        } else {
            echo "&nbsp;&nbsp;$db<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Cannot list databases: " . $e->getMessage() . "<br>";
}

echo "<h3>Next Steps</h3>";
echo "If none of the tests work, you may need to:<br>";
echo "1. Create the database manually in phpMyAdmin<br>";
echo "2. Update the .env file with correct credentials<br>";
echo "3. Check if MySQL service is running in Laragon<br>";
echo "<br>";
echo "<a href='install_test_installation.php'>Back to Debug Tool</a> | ";
echo "<a href='public/index.php'>Try Application</a>";
?>
