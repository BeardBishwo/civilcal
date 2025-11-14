<?php
/**
 * Simple API Test - Isolate the login connection error
 */

echo "🔍 SIMPLE API CONNECTION TEST\n";
echo "=============================\n\n";

// Test 1: Direct access to login API URL
echo "1️⃣ Testing direct API URL access...\n";

$apiUrl = 'http://localhost/api/login';
echo "   🌐 URL: $apiUrl\n";

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode(['username_email' => 'test', 'password' => 'test']),
        'timeout' => 10
    ]
]);

$response = @file_get_contents($apiUrl, false, $context);
$httpCode = 200;
if ($response === false) {
    $httpCode = 500;
    $response = "Connection failed";
}

echo "   📊 HTTP Response Code: $httpCode\n";
echo "   📝 Response Length: " . strlen($response) . " chars\n";
echo "   🔍 Response Preview: " . substr($response, 0, 200) . "\n\n";

// Test 2: Check if .htaccess routing is working
echo "2️⃣ Testing .htaccess routing...\n";

if (file_exists('public/.htaccess')) {
    echo "   ✅ .htaccess exists in public/\n";
    $htaccess = file_get_contents('public/.htaccess');
    if (strpos($htaccess, 'RewriteRule') !== false) {
        echo "   ✅ .htaccess contains rewrite rules\n";
    } else {
        echo "   ❌ .htaccess missing rewrite rules\n";
    }
} else {
    echo "   ❌ .htaccess missing in public/\n";
}

// Test 3: Check if we can load the controller directly
echo "\n3️⃣ Testing direct controller loading...\n";

try {
    // Mimic what the API route should do
    define('BISHWO_CALCULATOR', true);
    require_once __DIR__ . '/app/bootstrap.php';
    
    $controller = new \App\Controllers\Api\AuthController();
    echo "   ✅ AuthController loaded successfully\n";
    
    // Test if User model works
    $userModel = new \App\Models\User();
    echo "   ✅ User model loaded successfully\n";
    
} catch (Exception $e) {
    echo "   ❌ Error loading controller: " . $e->getMessage() . "\n";
    echo "   📍 Error in file: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🎯 DIAGNOSIS:\n";
echo "=============\n";

if ($httpCode === 500) {
    echo "❌ API returning 500 error - server configuration issue\n";
    echo "💡 Possible causes:\n";
    echo "   • Missing or incorrect .htaccess\n";
    echo "   • PHP fatal errors in API code\n";
    echo "   • Wrong file paths in bootstrap\n";
    echo "   • Apache mod_rewrite not enabled\n\n";
    
    echo "🔧 RECOMMENDED FIXES:\n";
    echo "1. Restart Apache server\n";
    echo "2. Check Apache error logs\n";
    echo "3. Test with direct PHP file access\n";
    echo "4. Verify .htaccess configuration\n";
} else {
    echo "✅ API accessible - issue might be in authentication logic\n";
}

echo "\n✨ TEST COMPLETE!\n";
?>
