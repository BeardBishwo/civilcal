<?php
// Simple test to check application access
echo "<h2>Bishwo Calculator - Application Test</h2>";

// Check installation status
$configFile = __DIR__ . '/config/installed.lock';
$envFile = __DIR__ . '/.env';

echo "<h3>Installation Status:</h3>";
echo "<p>Config file exists: " . (file_exists($configFile) ? "✅ YES" : "❌ NO") . "</p>";
echo "<p>Env file exists: " . (file_exists($envFile) ? "✅ YES" : "❌ NO") . "</p>";

// Check if we can load the database config
if (file_exists($envFile)) {
    $envVars = parse_ini_file($envFile);
    echo "<p>Database config: " . ($envVars['DB_DATABASE'] ?? 'NOT SET') . "</p>";
}

// Test direct application execution
echo "<h3>Application Test:</h3>";
echo "<p><a href='public/index.php'>🔗 Try Public Index Directly</a></p>";
echo "<p><a href='install/index.php'>📋 Installation Wizard</a></p>";
echo "<p><a href='install_test_installation.php'>🔧 Installation Debug</a></p>";

// Check if there are any error logs
$logFile = __DIR__ . '/storage/logs/error.log';
if (file_exists($logFile)) {
    echo "<h3>Recent Error Log:</h3>";
    $logs = file_get_contents($logFile);
    echo "<pre>" . htmlspecialchars($logs) . "</pre>";
} else {
    echo "<p>No error log found.</p>";
}
?>


