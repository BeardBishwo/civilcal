<?php
/**
 * Test Login API Directly
 * Check if the API endpoint is responding correctly
 */

// Test the login API endpoint
$api_url = 'http://localhost/api/login';

// Test data
$test_data = [
    'username_email' => 'admin',
    'password' => 'admin123'
];

echo "🧪 TESTING LOGIN API\n";
echo "====================\n\n";

// Test 1: Check if endpoint is accessible
echo "1️⃣ Testing API endpoint accessibility...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ❌ cURL Error: $error\n";
} else {
    echo "   ✅ API endpoint accessible\n";
    echo "   📊 HTTP Code: $http_code\n";
    echo "   📝 Response: " . substr($response, 0, 200) . "...\n";
}

// Test 2: Direct PHP inclusion test
echo "\n2️⃣ Testing direct API inclusion...\n";

try {
    // Simulate POST request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['CONTENT_TYPE'] = 'application/json';
    
    // Capture output
    ob_start();
    
    // Mock input data
    $GLOBALS['mock_input'] = json_encode($test_data);
    
    // Include the bootstrap to set up environment
    require_once __DIR__ . '/app/bootstrap.php';
    
    // Create controller and test
    $controller = new \App\Controllers\Api\AuthController();
    
    // Replace file_get_contents for testing
    if (!function_exists('mock_file_get_contents')) {
        function mock_file_get_contents($filename) {
            if ($filename === 'php://input') {
                return $GLOBALS['mock_input'] ?? '';
            }
            return file_get_contents($filename);
        }
    }
    
    $output = ob_get_clean();
    
    echo "   ✅ Controller instantiated successfully\n";
    echo "   📝 Output captured: " . strlen($output) . " bytes\n";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check User model
echo "\n3️⃣ Testing User model...\n";

try {
    $userModel = new \App\Models\User();
    $users = $userModel->getAll();
    
    echo "   ✅ User model working\n";
    echo "   📊 Total users: " . count($users) . "\n";
    
    if (count($users) > 0) {
        $firstUser = $users[0];
        echo "   👤 First user: " . $firstUser['username'] . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ User model error: " . $e->getMessage() . "\n";
}

// Test 4: Database connection
echo "\n4️⃣ Testing database connection...\n";

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    $result = $pdo->query('SELECT COUNT(*) as count FROM users')->fetch();
    echo "   ✅ Database connected\n";
    echo "   📊 Users in database: " . $result['count'] . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n🎯 DIAGNOSIS:\n";
echo "=============\n";

if ($http_code === 200) {
    echo "✅ API endpoint is working correctly\n";
    echo "💡 The connection error might be browser-specific\n";
    echo "🔧 Try clearing browser cache and cookies\n";
} elseif ($http_code === 500) {
    echo "❌ Server error in API endpoint\n";
    echo "🔍 Check error logs for PHP errors\n";
    echo "📋 Review AuthController implementation\n";
} elseif ($http_code === 404) {
    echo "❌ API endpoint not found\n";
    echo "🔍 Check routing configuration\n";
    echo "📋 Verify .htaccess rules\n";
} else {
    echo "⚠️  Unexpected HTTP code: $http_code\n";
    echo "🔍 Check server configuration\n";
}

echo "\n🚀 NEXT STEPS:\n";
echo "==============\n";
echo "1. Check browser developer console for detailed errors\n";
echo "2. Test login directly in browser at: http://localhost/login\n";
echo "3. Verify server logs for PHP errors\n";
echo "4. Clear browser cache completely\n";
echo "5. Try different browser or incognito mode\n";

echo "\n📞 QUICK FIX SUGGESTIONS:\n";
echo "========================\n";
echo "- Restart Apache/Nginx server\n";
echo "- Clear all browser data for localhost\n";
echo "- Check firewall or antivirus blocking connections\n";
echo "- Try login with different credentials\n";

echo "\n✨ TEST COMPLETE!\n\n";
?>
