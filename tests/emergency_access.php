<?php
/**
 * Emergency Access Script for Bishwo Calculator
 * This bypasses all URL rewriting and provides direct access
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Bishwo Calculator - Emergency Access</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .url { background: #e9ecef; padding: 10px 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; border: 2px solid #007bff; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
    </style>
</head>
<body>";

echo "<div class='container'>";
echo "<h1>🚨 Bishwo Calculator - Emergency Access</h1>";

// Check current path
$currentPath = __DIR__;
echo "<div class='step'>";
echo "<h3>📍 Current Path Analysis</h3>";
echo "<p><strong>Current Directory:</strong> $currentPath</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</p>";
echo "<p><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";
echo "<p><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "</p>";
echo "</div>";

// Check if we're in the right directory
$expectedFiles = [
    '../config/installed.lock' => 'Installation lock',
    '../app/Controllers/CalculatorController.php' => 'Main controller',
    '../.env' => 'Environment file',
    '../public/index.php' => 'Public index'
];

echo "<div class='step'>";
echo "<h3>🔍 File Structure Check</h3>";
foreach ($expectedFiles as $file => $description) {
    $exists = file_exists($file);
    $status = $exists ? '✅' : '❌';
    echo "<p>$status $description: " . ($exists ? 'Found' : 'Missing') . " ($file)</p>";
}
echo "</div>";

// Check if application is installed
$lockFile = '../config/installed.lock';
if (file_exists($lockFile)) {
    $lockContent = file_get_contents($lockFile);
    echo "<div class='success'>";
    echo "<h3>✅ Application Status</h3>";
    echo "<p><strong>Installation:</strong> Completed</p>";
    echo "<p><strong>Lock File Content:</strong></p>";
    echo "<pre>" . htmlspecialchars($lockContent) . "</pre>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<h3>❌ Application Status</h3>";
    echo "<p><strong>Installation:</strong> Not found or incomplete</p>";
    echo "</div>";
}

// Provide direct access solutions
echo "<div class='warning'>";
echo "<h3>🔧 Immediate Solutions</h3>";
echo "<p>Since all URLs are showing 404, here are the direct access methods:</p>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>Method 1: Direct File Access (Always Works)</h3>";
echo "<p>These URLs bypass URL rewriting completely:</p>";

$baseUrl = 'http://localhost/Bishwo_Calculator';
echo "<div class='url'>$baseUrl/public/index.php</div>";
echo "<div class='url'>$baseUrl/public/test_direct.php</div>";
echo "<div class='url'>$baseUrl/debug_installation.php</div>";
echo "<div class='url'>$baseUrl/public/.htaccess</div>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>Method 2: Laragon Virtual Host Setup</h3>";
echo "<p>To use <code>http://localhost/</code> you need to set up a virtual host:</p>";
echo "<ol>";
echo "<li>Laragon Menu → Tools → Path → Change Document Root</li>";
echo "<li>Set to: <code>$currentPath/public</code></li>";
echo "<li>Click OK and restart Laragon</li>";
echo "</ol>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>Method 3: Alternative Web Server Paths</h3>";
echo "<p>Try accessing via different paths:</p>";
echo "<div class='url'>http://localhost/Bishwo_Calculator/</div>";
echo "<div class='url'>http://127.0.0.1/Bishwo_Calculator/public/</div>";
echo "<div class='url'>http://localhost:8080/Bishwo_Calculator/public/</div>";
echo "</div>";

// Create a simple test page
echo "<div class='step'>";
echo "<h3>Test This Emergency Page</h3>";
echo "<p>If you can see this page, PHP is working. Now try:</p>";
echo "<div class='url'>http://localhost/Bishwo_Calculator/emergency.php</div>";
echo "<p>This will show you the application working without URL rewriting.</p>";
echo "</div>";

echo "</div>";
echo "</body></html>";

// Save this as emergency access page
?>
