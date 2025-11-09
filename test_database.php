<?php
/**
 * Test Database Connection
 * Simple script to test if the database connection works
 */

define('BASE_PATH', __DIR__);

echo "<h2>Database Connection Test</h2>";

// Test .env file reading
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    echo "✅ .env file found<br>";
    $envVars = parse_ini_file($envFile);
    
    if ($envVars) {
        echo "✅ .env file parsed successfully<br>";
        echo "<h3>Database Configuration:</h3>";
        echo "Host: " . ($envVars['DB_HOST'] ?? 'NOT SET') . "<br>";
        echo "Database: " . ($envVars['DB_DATABASE'] ?? 'NOT SET') . "<br>";
        echo "Username: " . ($envVars['DB_USERNAME'] ?? 'NOT SET') . "<br>";
        echo "Password: " . (empty($envVars['DB_PASSWORD']) ? 'EMPTY' : 'SET') . "<br>";
        
        // Test connection
        if (isset($envVars['DB_HOST'], $envVars['DB_DATABASE'], $envVars['DB_USERNAME'])) {
            try {
                $dsn = "mysql:host={$envVars['DB_HOST']};dbname={$envVars['DB_DATABASE']}";
                $pdo = new PDO($dsn, $envVars['DB_USERNAME'], $envVars['DB_PASSWORD'] ?? '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                echo "<h3>Connection Test:</h3>";
                echo "✅ <strong>Database connection successful!</strong><br>";
                echo "Connected to: {$envVars['DB_DATABASE']}@{$envVars['DB_HOST']}<br>";
                
                // Test a simple query
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                $result = $stmt->fetch();
                echo "Users table has: " . $result['count'] . " records<br>";
                
            } catch (Exception $e) {
                echo "<h3>Connection Test:</h3>";
                echo "❌ <strong>Database connection failed:</strong><br>";
                echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
            }
        } else {
            echo "❌ Database configuration incomplete in .env file<br>";
        }
    } else {
        echo "❌ Failed to parse .env file<br>";
    }
} else {
    echo "❌ .env file not found<br>";
}

echo "<h3>Test Complete</h3>";
echo "<a href='install_test_installation.php'>Back to Debug Tool</a> | ";
echo "<a href='public/index.php'>Go to Application</a>";
?>
