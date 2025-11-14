<?php
/**
 * Direct login test to bypass routing issues
 */

header('Content-Type: application/json');

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/app/bootstrap.php';

echo "🔐 DIRECT LOGIN TEST\n";
echo "===================\n\n";

// Test your credentials
$testCredentials = [
    ['username' => 'uniquebishwo', 'password' => 'c9PU7XAsAADYk_A'],
    ['username' => 'uniquebishwo@gmail.com', 'password' => 'c9PU7XAsAADYk_A'],
    ['username' => 'engineer@engicalpro.com', 'password' => 'Engineer123!'],
    ['username' => 'admin@engicalpro.com', 'password' => 'password']
];

foreach ($testCredentials as $i => $creds) {
    echo ($i + 1) . "️⃣ Testing: " . $creds['username'] . "\n";
    
    try {
        $userModel = new \App\Models\User();
        $user = $userModel::findByUsername($creds['username']);
        
        if ($user) {
            echo "   ✅ User found: " . $user->username . " (" . $user->email . ")\n";
            
            if (password_verify($creds['password'], $user->password)) {
                echo "   ✅ Password correct\n";
                echo "   🎉 LOGIN WOULD SUCCEED\n";
                
                // Simulate successful login response
                $loginResponse = [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'is_admin' => $user->is_admin ?? false
                    ]
                ];
                echo "   📋 API Response: " . json_encode($loginResponse) . "\n";
            } else {
                echo "   ❌ Password incorrect\n";
            }
        } else {
            echo "   ❌ User not found\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "🌐 WEB SERVER TEST:\n";
echo "==================\n";

// Test if we can create a simple API endpoint
$apiContent = '<?php
header("Content-Type: application/json");
echo json_encode(["status" => "working", "timestamp" => date("Y-m-d H:i:s")]);
?>';

file_put_contents(__DIR__ . '/test_api_endpoint.php', $apiContent);
echo "✅ Created test API endpoint: /test_api_endpoint.php\n";
echo "🌐 Test URL: http://localhost/test_api_endpoint.php\n\n";

echo "🔧 RECOMMENDED FIXES:\n";
echo "====================\n";
echo "1. Restart Apache/Laragon server\n";
echo "2. Check .htaccess configuration\n";
echo "3. Verify API routing in routes.php\n";
echo "4. Test with direct API file access\n\n";

echo "✨ TEST COMPLETE!\n";
?>
