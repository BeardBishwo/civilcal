<?php
// Simple application test
echo "<h2>Bishwo Calculator - Application Test</h2>";

echo "<p>✅ PHP is working</p>";

echo "<h3>System Check:</h3>";
echo "<p>Base Path: " . dirname(__DIR__) . "</p>";

echo "<h3>File Check:</h3>";
$files = [
    'index.php' => 'Root index',
    'public/index.php' => 'Public index',
    'config/installed.lock' => 'Installation lock',
    '.env' => 'Environment file'
];

foreach ($files as $file => $desc) {
    $fullPath = dirname(__FILE__) . '/' . $file;
    if (file_exists($fullPath)) {
        echo "<p>✅ $desc exists</p>";
    } else {
        echo "<p>❌ $desc missing</p>";
    }
}

echo "<h3>Database Test:</h3>";
$envFile = dirname(__FILE__) . '/.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    try {
        $pdo = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']}", $env['DB_USERNAME'], $env['DB_PASSWORD'] ?? '');
        echo "<p>✅ Database connection working</p>";
    } catch (Exception $e) {
        echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>Quick Links:</h3>";
echo "<p><a href='install/index.php'>🔧 Installation</a> | ";
echo "<a href='install_test_installation.php'>🔍 System Test</a> | ";
echo "<a href='simple_db_test.php'>💾 Database Test</a></p>";
?>
