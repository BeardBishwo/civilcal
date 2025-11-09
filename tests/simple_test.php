<?php
/**
 * Simple Web Server Test
 * This file tests if the web server can serve basic PHP files
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Web Server Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #cce5ff; color: #004085; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .step { margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff; }
    </style>
</head>
<body>";

echo "<h1>🔧 Web Server Diagnostic Test</h1>";

// Test 1: Basic PHP functionality
echo "<div class='success'>";
echo "<h3>✅ Test 1: PHP is Working</h3>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'CLI') . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
echo "</div>";

// Test 2: File system access
echo "<div class='success'>";
echo "<h3>✅ Test 2: File System Access</h3>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
echo "<p>Parent Directory: " . dirname(__DIR__) . "</p>";

$parentFiles = [
    'app/Controllers/CalculatorController.php' => 'Application Controller',
    'config/installed.lock' => 'Installation Status',
    '.env' => 'Environment File'
];

foreach ($parentFiles as $file => $description) {
    $fullPath = dirname(__DIR__) . '/' . $file;
    if (file_exists($fullPath)) {
        echo "<p>✅ $description: Available</p>";
    } else {
        echo "<p>❌ $description: Missing</p>";
    }
}
echo "</div>";

// Test 3: Check web server configuration
echo "<div class='info'>";
echo "<h3>📡 Test 3: Web Server Configuration</h3>";
echo "<p>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</p>";
echo "<p>Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";
echo "<p>Server Name: " . ($_SERVER['SERVER_NAME'] ?? 'Not set') . "</p>";
echo "</div>";

// Test 4: Check if application should work
$isInstalled = file_exists(dirname(__DIR__) . '/config/installed.lock');
if ($isInstalled) {
    echo "<div class='success'>";
    echo "<h3>✅ Test 4: Application Status</h3>";
    echo "<p>✅ Bishwo Calculator is INSTALLED and ready to use</p>";
    echo "<p>📅 Installation completed: " . date('Y-m-d H:i:s', filemtime(dirname(__DIR__) . '/config/installed.lock')) . "</p>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<h3>❌ Test 4: Application Status</h3>";
    echo "<p>❌ Application installation incomplete</p>";
    echo "</div>";
}

// Provide solutions
echo "<div class='step'>";
echo "<h3>🎯 Next Steps</h3>";

if ($_SERVER['DOCUMENT_ROOT'] === __DIR__) {
    echo "<p>✅ <strong>Document Root is Correct</strong> - This file is in the document root</p>";
    echo "<p>🔗 <a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Try Accessing the Calculator</a></p>";
} else {
    echo "<p>❌ <strong>Document Root Issue</strong></p>";
    echo "<p>Document root is: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
    echo "<p>Current file is in: " . __DIR__ . "</p>";
    echo "<p><strong>Solution:</strong> Change Laragon document root to this directory</p>";
}

echo "</div>";

// Test .htaccess if it exists
if (file_exists('.htaccess')) {
    echo "<div class='info'>";
    echo "<h3>📄 .htaccess File</h3>";
    echo "<p>✅ .htaccess file exists</p>";
    echo "<pre style='background: #f0f0f0; padding: 10px; overflow: auto;'>";
    echo htmlspecialchars(file_get_contents('.htaccess'));
    echo "</pre>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<h3>📄 .htaccess File</h3>";
    echo "<p>❌ .htaccess file is missing</p>";
    echo "</div>";
}

echo "<div class='step'>";
echo "<h3>🆘 If 404 errors persist:</h3>";
echo "<ol>";
echo "<li><strong>Check Laragon Status:</strong> Ensure Laragon is running (green icon)</li>";
echo "<li><strong>Restart Laragon:</strong> Close and reopen Laragon completely</li>";
echo "<li><strong>Check Port:</strong> Ensure no other software is using port 80</li>";
echo "<li><strong>Try Alternative:</strong> Access via http://127.0.0.1/ instead of localhost</li>";
echo "<li><strong>Direct File:</strong> Access this file directly if other methods fail</li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";
?>
