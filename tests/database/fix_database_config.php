<?php
// Database Configuration Fix Script
// This will attempt to fix common database configuration issues

echo "<h2>Bishwo Calculator - Database Configuration Fix</h2>";

// Function to try to connect with different configurations
function testConnection($host, $database, $username, $password) {
    try {
        $dsn = "mysql:host=$host;dbname=$database";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (Exception $e) {
        return null;
    }
}

echo "<h3>1. Testing Current .env Configuration</h3>";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envVars = parse_ini_file($envFile);
    $currentConfig = testConnection(
        $envVars['DB_HOST'] ?? 'localhost',
        $envVars['DB_DATABASE'] ?? '',
        $envVars['DB_USERNAME'] ?? '',
        $envVars['DB_PASSWORD'] ?? ''
    );
    
    if ($currentConfig) {
        echo "<p>✅ Current configuration works!</p>";
        echo "<p>📊 Database '{$envVars['DB_DATABASE']}' has " . 
             $currentConfig->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '{$envVars['DB_DATABASE']}'")->fetchColumn() . 
             " tables</p>";
    } else {
        echo "<p>❌ Current configuration failed. Testing alternatives...</p>";
    }
}

echo "<h3>2. Common Laragon Configurations</h3>";

// Test different configurations
$configs = [
    ['host' => 'localhost', 'db' => 'uniquebishwo', 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'db' => 'uniquebishwo', 'user' => 'uniquebishwo', 'pass' => 'c9PU7XAsAADYk_A'],
    ['host' => 'localhost', 'db' => 'bishwo_calculator', 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'db' => 'bishwo_calculator', 'user' => 'root', 'pass' => 'c9PU7XAsAADYk_A'],
    ['host' => 'localhost', 'db' => 'bishwo_calculator', 'user' => 'bishwo_calculator', 'pass' => ''],
];

$workingConfig = null;
foreach ($configs as $i => $config) {
    $pdo = testConnection($config['host'], $config['db'], $config['user'], $config['pass']);
    if ($pdo) {
        echo "<p>✅ Config " . ($i+1) . " works: {$config['user']}@{$config['host']}/{$config['db']}</p>";
        
        // Check what tables exist
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (count($tables) > 0) {
            echo "<p>&nbsp;&nbsp;📋 Contains tables: " . implode(', ', $tables) . "</p>";
        } else {
            echo "<p>&nbsp;&nbsp;📋 Database is empty (no tables)</p>";
        }
        
        if (!$workingConfig) {
            $workingConfig = $config;
        }
    } else {
        echo "<p>❌ Config " . ($i+1) . " failed: {$config['user']}@{$config['host']}/{$config['db']}</p>";
    }
}

echo "<h3>3. Recommended Fix</h3>";

if ($workingConfig) {
    echo "<p>🎯 <strong>Found working configuration!</strong></p>";
    echo "<p>Recommended .env update:</p>";
    echo "<pre>";
    echo "DB_HOST={$workingConfig['host']}\n";
    echo "DB_DATABASE={$workingConfig['db']}\n";
    echo "DB_USERNAME={$workingConfig['user']}\n";
    echo "DB_PASSWORD={$workingConfig['pass']}\n";
    echo "</pre>";
    
    // Offer to update .env
    if (isset($_POST['update_env'])) {
        $newEnvContent = "# Updated " . date('Y-m-d H:i:s') . "\n";
        foreach ($envVars as $key => $value) {
            if (strpos($key, 'DB_') === 0) {
                $newEnvContent .= $key . "=" . $workingConfig[strtolower(str_replace('DB_', '', $key))] . "\n";
            } else {
                $newEnvContent .= $key . "=" . $value . "\n";
            }
        }
        
        if (file_put_contents($envFile, $newEnvContent)) {
            echo "<p>✅ <strong>.env file updated successfully!</strong></p>";
            echo "<p>🔄 <a href='simple_db_test.php'>Test the new configuration</a></p>";
        } else {
            echo "<p>❌ Failed to update .env file</p>";
        }
    } else {
        echo "<form method='post' style='margin: 20px 0;'>";
        echo "<input type='hidden' name='update_env' value='1'>";
        echo "<button type='submit' style='padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer;'>";
        echo "🔧 Update .env file with working configuration";
        echo "</button>";
        echo "</form>";
    }
} else {
    echo "<p>❌ <strong>No working configuration found!</strong></p>";
    echo "<h4>Manual steps required:</h4>";
    echo "<ol>";
    echo "<li>Start MySQL in Laragon</li>";
    echo "<li>Create database 'bishwo_calculator' in phpMyAdmin</li>";
    echo "<li>Create user 'root' with no password (or add your preferred user)</li>";
    echo "<li>Run the installation again to create tables</li>";
    echo "</ol>";
}

echo "<h3>4. Quick Links</h3>";
echo "<p>";
echo "<a href='simple_db_test.php'>🔍 Database Test</a> | ";
echo "<a href='install_test_installation.php'>🔧 Installation Debug</a> | ";
echo "<a href='install/index.php'>📋 Run Installation</a> | ";
echo "<a href='public/index.php'>🚀 Main Application</a>";
echo "</p>";
?>


