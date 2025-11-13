<?php
// Simple Database Test
// This file can be accessed via web browser to test database connection

echo "<h2>Bishwo Calculator - Database Connection Test</h2>";

// Load .env file manually
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    die("<p>❌ .env file not found at: $envFile</p>");
}

$envVars = parse_ini_file($envFile);
if (!$envVars) {
    die("<p>❌ Could not parse .env file</p>");
}

echo "<h3>Configuration from .env file:</h3>";
echo "<ul>";
echo "<li>Host: " . ($envVars['DB_HOST'] ?? 'NOT SET') . "</li>";
echo "<li>Database: " . ($envVars['DB_DATABASE'] ?? 'NOT SET') . "</li>";
echo "<li>Username: " . ($envVars['DB_USERNAME'] ?? 'NOT SET') . "</li>";
echo "<li>Password: " . (isset($envVars['DB_PASSWORD']) ? '***SET***' : 'NOT SET') . "</li>";
echo "</ul>";

// Test connection with current config
echo "<h3>Connection Test:</h3>";

if (!isset($envVars['DB_HOST'], $envVars['DB_DATABASE'], $envVars['DB_USERNAME'])) {
    echo "<p>❌ Missing required database configuration in .env file</p>";
} else {
    try {
        $dsn = "mysql:host={$envVars['DB_HOST']};dbname={$envVars['DB_DATABASE']}";
        echo "<p>Attempting connection with DSN: $dsn</p>";
        
        $pdo = new PDO($dsn, $envVars['DB_USERNAME'], $envVars['DB_PASSWORD'] ?? '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p>✅ <strong>Connection successful!</strong></p>";
        
        // Test if we can run a simple query
        $stmt = $pdo->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = '{$envVars['DB_DATABASE']}'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>📊 Database has " . $result['table_count'] . " tables</p>";
        
    } catch (Exception $e) {
        echo "<p>❌ <strong>Connection failed:</strong> " . $e->getMessage() . "</p>";
        
        echo "<h4>Common Solutions:</h4>";
        echo "<ul>";
        echo "<li>Check if MySQL service is running in Laragon</li>";
        echo "<li>Verify database '{$envVars['DB_DATABASE']}' exists in phpMyAdmin</li>";
        echo "<li>Check if user '{$envVars['DB_USERNAME']}' exists and has permissions</li>";
        echo "<li>Verify password is correct</li>";
        echo "</ul>";
    }
}

// Test standard Laragon configurations
echo "<h3>Testing Common Laragon Configurations:</h3>";
$testConfigs = [
    ['host' => 'localhost', 'db' => 'uniquebishwo', 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'db' => 'uniquebishwo', 'user' => 'uniquebishwo', 'pass' => 'c9PU7XAsAADYk_A'],
    ['host' => 'localhost', 'db' => 'bishwo_calculator', 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'db' => 'bishwo_calculator', 'user' => 'root', 'pass' => 'c9PU7XAsAADYk_A'],
];

foreach ($testConfigs as $i => $config) {
    echo "<p>Test " . ($i+1) . ": {$config['user']}@{$config['host']}/{$config['db']}...";
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['db']}";
        $pdo = new PDO($dsn, $config['user'], $config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo " ✅ <strong>SUCCESS!</strong></p>";
        
        // Check if this looks like our app database
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (count($tables) > 0) {
            echo "<p>&nbsp;&nbsp;📋 Found " . count($tables) . " tables: " . implode(', ', array_slice($tables, 0, 5)) . "</p>";
        }
        break;
    } catch (Exception $e) {
        echo " ❌ " . $e->getMessage() . "</p>";
    }
}

echo "<h3>Quick Links:</h3>";
echo "<p><a href='install_test_installation.php'>🔧 Installation Debug Tool</a> | ";
echo "<a href='public/index.php'>🚀 Try Main Application</a> | ";
echo "<a href='install/index.php'>📋 Installation Wizard</a></p>";
?>


