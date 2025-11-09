<?php
/**
 * Debug Installation Test for Bishwo Calculator
 * This script will test the application installation and provide detailed status
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Debug Installation - Bishwo Calculator</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px; 
            background-color: #f0f0f0; 
        }
        .container { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .test-item { margin: 10px 0; padding: 10px; border-left: 4px solid #007bff; background: #f8f9fa; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { 
            display: inline-block; 
            padding: 12px 24px; 
            background: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 5px; 
            font-weight: bold; 
        }
        .btn:hover { background: #0056b3; }
        .btn.success { background: #28a745; }
        .btn.danger { background: #dc3545; }
    </style>
</head>
<body>";

echo "<div class='container'>";
echo "<h1>🔧 Debug Installation Test - Bishwo Calculator</h1>";
echo "<p><strong>Test Date:</strong> " . date('Y-m-d H:i:s') . "</p>";

// 1. Test PHP Version
echo "<h2>1. PHP Environment</h2>";
$phpVersion = phpversion();
echo "<div class='test-item'>";
echo "<strong>PHP Version:</strong> $phpVersion " . (version_compare($phpVersion, '7.4.0') >= 0 ? "✅" : "❌") . "<br>";
echo "<strong>Memory Limit:</strong> " . ini_get('memory_limit') . "<br>";
echo "<strong>Max Execution Time:</strong> " . ini_get('max_execution_time') . "s<br>";
echo "<strong>Upload Max Filesize:</strong> " . ini_get('upload_max_filesize') . "<br>";
echo "</div>";

// 2. Test File Permissions
echo "<h2>2. File System</h2>";
$basePath = __DIR__;
$filesToCheck = [
    'config/installed.lock' => 'Installation Status',
    '.env' => 'Environment Configuration',
    'public/index.php' => 'Main Application Entry',
    'public/.htaccess' => 'URL Rewriting Rules',
    'app/bootstrap.php' => 'Application Bootstrap',
    'app/Controllers/CalculatorController.php' => 'Main Controller',
    'config/database.php' => 'Database Configuration',
    'vendor/autoload.php' => 'Composer Autoloader'
];

echo "<div class='test-item'>";
foreach ($filesToCheck as $file => $description) {
    $exists = file_exists($basePath . '/' . $file);
    $readable = $exists ? (is_readable($basePath . '/' . $file) ? '✅ Readable' : '❌ Not Readable') : '❌ Missing';
    echo "<strong>$description:</strong> " . ($exists ? '✅ Found' : '❌ Missing') . " ($readable)<br>";
}
echo "</div>";

// 3. Test Directory Permissions
echo "<h2>3. Directory Permissions</h2>";
$dirsToCheck = [
    'storage/' => 'Storage Directory',
    'storage/logs/' => 'Logs Directory',
    'storage/cache/' => 'Cache Directory',
    'storage/app/' => 'App Storage',
    'public/assets/' => 'Public Assets'
];

echo "<div class='test-item'>";
foreach ($dirsToCheck as $dir => $description) {
    $fullPath = $basePath . '/' . $dir;
    $exists = is_dir($fullPath);
    $writable = $exists ? (is_writable($fullPath) ? '✅ Writable' : '❌ Not Writable') : '❌ Missing';
    echo "<strong>$description:</strong> " . ($exists ? '✅ Exists' : '❌ Missing') . " ($writable)<br>";
}
echo "</div>";

// 4. Test .htaccess Configuration
echo "<h2>4. Web Server Configuration</h2>";
$htaccessFile = $basePath . '/public/.htaccess';
if (file_exists($htaccessFile)) {
    $htaccessContent = file_get_contents($htaccessFile);
    $rewriteEnabled = strpos($htaccessContent, 'RewriteEngine On') !== false;
    echo "<div class='test-item'>";
    echo "<strong>public/.htaccess exists:</strong> ✅ Yes<br>";
    echo "<strong>URL Rewriting enabled:</strong> " . ($rewriteEnabled ? '✅ Yes' : '❌ No') . "<br>";
    echo "<strong>File Size:</strong> " . filesize($htaccessFile) . " bytes<br>";
    echo "</div>";
} else {
    echo "<div class='test-item error'>";
    echo "<strong>public/.htaccess:</strong> ❌ Missing<br>";
    echo "</div>";
}

// 5. Test Installation Status
echo "<h2>5. Installation Status</h2>";
$installedLock = $basePath . '/config/installed.lock';
if (file_exists($installedLock)) {
    $installContent = file_get_contents($installedLock);
    echo "<div class='test-item success'>";
    echo "<strong>Installation Status:</strong> ✅ Installed<br>";
    echo "<strong>Installation File:</strong><br><pre>" . htmlspecialchars($installContent) . "</pre>";
    echo "</div>";
} else {
    echo "<div class='test-item warning'>";
    echo "<strong>Installation Status:</strong> ⚠️ Not Installed<br>";
    echo "</div>";
}

// 6. Test Environment Configuration
echo "<h2>6. Environment Configuration</h2>";
$envFile = $basePath . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    echo "<div class='test-item'>";
    echo "<strong>.env file exists:</strong> ✅ Yes<br>";
    echo "<strong>Environment Variables:</strong><br>";
    $envVars = explode("\n", $envContent);
    foreach ($envVars as $line) {
        if (!empty(trim($line)) && !str_starts_with($line, '#')) {
            $parts = explode('=', $line, 2);
            if (count($parts) == 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                if (in_array($key, ['DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD', 'APP_NAME', 'APP_ENV'])) {
                    echo "- $key: " . (empty($value) ? '[empty]' : '[set]') . "<br>";
                }
            }
        }
    }
    echo "</div>";
} else {
    echo "<div class='test-item error'>";
    echo "<strong>.env file:</strong> ❌ Missing<br>";
    echo "</div>";
}

// 7. Test Required PHP Extensions
echo "<h2>7. PHP Extensions</h2>";
$requiredExtensions = ['pdo', 'pdo_mysql', 'openssl', 'curl', 'json', 'mbstring', 'fileinfo'];
$optionalExtensions = ['gd', 'zip', 'xml', 'dom'];

echo "<div class='test-item'>";
echo "<strong>Required Extensions:</strong><br>";
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "- $ext: " . ($loaded ? '✅ Loaded' : '❌ Missing') . "<br>";
}

echo "<br><strong>Optional Extensions:</strong><br>";
foreach ($optionalExtensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "- $ext: " . ($loaded ? '✅ Loaded' : '⚠️ Missing') . "<br>";
}
echo "</div>";

// 8. Generate Test URLs
echo "<h2>8. Access URLs</h2>";
echo "<div class='test-item info'>";
echo "<h3>🔗 Test These URLs in Your Browser:</h3>";
echo "<p><strong>Recommended URL (with fixed .htaccess):</strong><br>";
echo "<a href='./public/' class='btn success' target='_blank'>http://localhost/Bishwo_Calculator/public/</a></p>";
echo "<p><strong>Direct Access (bypasses .htaccess):</strong><br>";
echo "<a href='./public/index.php' class='btn' target='_blank'>http://localhost/Bishwo_Calculator/public/index.php</a></p>";
echo "<p><strong>Installation Panel:</strong><br>";
echo "<a href='./install/' class='btn' target='_blank'>http://localhost/Bishwo_Calculator/install/</a></p>";
echo "</div>";

// 9. Final Status
echo "<h2>9. System Status Summary</h2>";

$issues = [];
$warnings = [];
$success = [];

// Check for issues
if (version_compare($phpVersion, '7.4.0') < 0) {
    $issues[] = "PHP version too old (minimum 7.4.0 required)";
}

if (!file_exists($installedLock)) {
    $issues[] = "Application not properly installed";
}

if (!file_exists($htaccessFile)) {
    $issues[] = "Missing public/.htaccess file";
}

if (!extension_loaded('pdo_mysql')) {
    $issues[] = "PDO MySQL extension missing";
}

if (empty($issues)) {
    echo "<div class='test-item success'>";
    echo "<h3>✅ All Systems Ready</h3>";
    echo "<p>Your Bishwo Calculator installation appears to be working correctly!</p>";
    echo "<p><strong>Next Steps:</strong> Click the URLs above to test the application in your browser.</p>";
    echo "</div>";
} else {
    echo "<div class='test-item error'>";
    echo "<h3>❌ Issues Found</h3>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li>$issue</li>";
    }
    echo "</ul>";
    echo "</div>";
}

echo "<div class='test-item info'>";
echo "<h3>📋 Quick Debug Guide</h3>";
echo "<ol>";
echo "<li><strong>404 Error Fix:</strong> Use http://localhost/Bishwo_Calculator/public/ (with trailing slash)</li>";
echo "<li><strong>Alternative:</strong> Use http://localhost/Bishwo_Calculator/public/index.php</li>";
echo "<li><strong>Web Server:</strong> Ensure mod_rewrite is enabled in Apache</li>";
echo "<li><strong>Document Root:</strong> Ideally set to /public folder for production</li>";
echo "</ol>";
echo "</div>";

echo "</div>";
echo "</body></html>";
?>
