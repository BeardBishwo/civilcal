<?php
/**
 * Simple Test Script for Bishwo Calculator
 * This script tests basic functionality without external dependencies
 */

echo "<h1>🧮 Bishwo Calculator - System Test</h1>";

// Test 1: Basic PHP functionality
echo "<h2>1. PHP Environment Test</h2>";
echo "✅ PHP Version: " . PHP_VERSION . "<br>";
echo "✅ Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'CLI') . "<br>";
echo "✅ Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "<br>";
echo "✅ Current Directory: " . __DIR__ . "<br>";

// Test 2: File System Access
echo "<h2>2. File System Test</h2>";
$testFiles = [
    'app/Controllers/CalculatorController.php' => 'Main Controller',
    'public/index.php' => 'Public Index',
    'config/installed.lock' => 'Installation Lock',
    '.env' => 'Environment File'
];

foreach ($testFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description: Available<br>";
    } else {
        echo "❌ $description: Missing<br>";
    }
}

// Test 3: Database Connection Test
echo "<h2>3. Database Connection Test</h2>";
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    if (preg_match('/DB_HOST=([^\\n]+)/', $envContent, $matches)) {
        $dbHost = trim($matches[1]);
        echo "📊 Database Host: $dbHost<br>";
        
        // Test MySQL connection
        try {
            $pdo = new PDO("mysql:host=$dbHost;dbname=test", "root", "");
            echo "✅ MySQL Connection: Working<br>";
        } catch (PDOException $e) {
            echo "⚠️ MySQL Connection: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "❌ .env file not found<br>";
}

// Test 4: Application Status
echo "<h2>4. Application Status</h2>";
if (file_exists('config/installed.lock')) {
    $lockContent = file_get_contents('config/installed.lock');
    echo "✅ Application Status: INSTALLED<br>";
    echo "📅 Installation Date: " . date('Y-m-d H:i:s', filemtime('config/installed.lock')) . "<br>";
} else {
    echo "❌ Application Status: NOT INSTALLED<br>";
}

// Test 5: URL Access Information
echo "<h2>5. Access Information</h2>";
echo "<div style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
echo "<h3>Available Access Methods:</h3>";
echo "<ol>";
echo "<li><strong>Method 1:</strong> http://localhost/Bishwo_Calculator/ (with proper document root)</li>";
echo "<li><strong>Method 2:</strong> http://localhost/Bishwo_Calculator/public/ (current attempt)</li>";
echo "<li><strong>Method 3:</strong> http://localhost/Bishwo_Calculator/test_app_functionality.php (this page)</li>";
echo "</ol>";
echo "</div>";

// Test 6: Create simple redirect page
echo "<h2>6. Quick Access Links</h2>";
echo "<p>Since you're having 404 issues, try these direct access methods:</p>";
echo "<a href='public/index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Access Calculator (Direct)</a>";
echo "<br><br>";
echo "<a href='debug_installation.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔧 Debug System</a>";

echo "<h2>7. Next Steps</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
echo "<h3>🔧 If you're still getting 404 errors:</h3>";
echo "<ol>";
echo "<li><strong>Laragon Configuration:</strong> Set Document Root to: " . realpath('.') . "/public</li>";
echo "<li><strong>Alternative Access:</strong> Use http://127.0.0.1/ instead of localhost</li>";
echo "<li><strong>Port Check:</strong> Ensure Laragon is running on port 80 (or 8080)</li>";
echo "<li><strong>Virtual Host:</strong> Create a virtual host pointing to the public directory</li>";
echo "</ol>";
echo "</div>";

echo "<h2>8. Technical Details</h2>";
echo "<pre>";
echo "Current Working Directory: " . getcwd() . "\n";
echo "Server Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not Available') . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not Available') . "\n";
echo "PHP Self: " . $_SERVER['PHP_SELF'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "</pre>";

echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
echo "<h3>✅ Test Completed</h3>";
echo "<p>If you can see this page, PHP is working correctly and the application files are accessible.</p>";
echo "<p>The 404 errors are likely due to web server configuration, not application issues.</p>";
echo "</div>";
?>
