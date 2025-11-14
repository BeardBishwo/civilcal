<?php
/**
 * Direct API test to bypass routing issues
 */

// Prevent any output before headers
ob_start();

try {
    // Set up the environment like the API would
    define('BISHWO_CALCULATOR', true);
    
    // Don't start sessions or send headers in test mode
    $_ENV['TEST_MODE'] = true;
    
    require_once __DIR__ . '/app/bootstrap.php';
    
    // Clear any output that might have been generated
    ob_clean();
    
    // Test the AuthController directly
    $controller = new \App\Controllers\Api\AuthController();
    
    // Simulate POST data for login
    $_POST = [];
    
    // Create test input
    $testData = [
        'username_email' => 'engineer@engicalpro.com',
        'password' => 'Engineer123!'
    ];
    
    // Mock the php://input
    $GLOBALS['test_input'] = json_encode($testData);
    
    // Capture the output
    ob_start();
    
    // Mock the input stream
    function mock_file_get_contents($filename) {
        if ($filename === 'php://input') {
            return $GLOBALS['test_input'];
        }
        return file_get_contents($filename);
    }
    
    // Test login method
    echo "🧪 DIRECT API TEST\n";
    echo "==================\n\n";
    
    echo "📝 Testing login with: engineer@engicalpro.com\n";
    echo "🔑 Password: Engineer123!\n\n";
    
    // Manually call login logic
    $input = json_decode($GLOBALS['test_input'], true);
    $username = $input['username_email'] ?? '';
    $password = $input['password'] ?? '';
    
    echo "✅ Input parsed successfully\n";
    echo "👤 Username: $username\n";
    echo "🔐 Password: " . str_repeat('*', strlen($password)) . "\n\n";
    
    // Test user lookup
    $userModel = new \App\Models\User();
    $user = $userModel::findByUsername($username);
    
    if ($user) {
        echo "✅ User found in database\n";
        echo "📧 Email: " . $user->email . "\n";
        echo "👤 Username: " . $user->username . "\n";
        
        // Test password verification
        if (password_verify($password, $user->password)) {
            echo "✅ Password verification successful\n";
            echo "🎉 Login should work!\n\n";
            
            echo "🔧 API Response would be:\n";
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email
                ]
            ], JSON_PRETTY_PRINT);
            
        } else {
            echo "❌ Password verification failed\n";
            echo "🔍 Stored hash: " . substr($user->password, 0, 20) . "...\n";
        }
    } else {
        echo "❌ User not found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n\n🎯 DIAGNOSIS:\n";
echo "=============\n";
echo "If login works here but fails in browser:\n";
echo "1. Session/header conflicts in web server\n";
echo "2. .htaccess routing issues\n";
echo "3. Apache configuration problems\n\n";

echo "🔧 SOLUTIONS:\n";
echo "=============\n";
echo "1. Restart Apache server\n";
echo "2. Clear browser cache completely\n";
echo "3. Check Apache error logs\n";
echo "4. Test in incognito/private mode\n\n";

echo "✨ TEST COMPLETE!\n";

// Clean up
ob_end_flush();
?>
