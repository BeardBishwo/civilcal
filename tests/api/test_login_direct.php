<?php
/**
 * Test Login Logic Directly (No Web Server)
 */

echo "🔐 TESTING LOGIN LOGIC DIRECTLY\n";
echo "===============================\n\n";

try {
    // Define constant to prevent bootstrap issues
    if (!defined('BISHWO_CALCULATOR')) {
        define('BISHWO_CALCULATOR', true);
    }
    
    // Include necessary files
    require_once __DIR__ . '/app/bootstrap.php';
    
    echo "✅ Bootstrap loaded successfully\n";
    
    // Test User model
    echo "🔍 Testing User model...\n";
    $userModel = new \App\Models\User();
    echo "✅ User model instantiated\n";
    
    // Get all users to see what's available
    $users = $userModel->getAll();
    echo "📊 Found " . count($users) . " users\n";
    
    if (count($users) > 0) {
        echo "\n👥 Available users:\n";
        foreach ($users as $user) {
            echo "   - {$user->username} ({$user->email})\n";
        }
    }
    
    // Test finding a user
    echo "\n🔍 Testing user lookup...\n";
    $testUsername = 'uniquebishwo'; // From the list we saw earlier
    
    $user = $userModel::findByUsername($testUsername);
    
    if ($user) {
        echo "✅ Found user: {$user->username}\n";
        echo "📧 Email: {$user->email}\n";
        echo "🔑 Has password hash: " . (isset($user->password) ? 'Yes' : 'No') . "\n";
        
        // Test password verification
        echo "\n🔐 Testing password verification...\n";
        $testPassword = 'password123'; // Common default password
        
        if (password_verify($testPassword, $user->password)) {
            echo "✅ Password verification successful!\n";
            
            // Simulate successful login
            echo "\n🎉 LOGIN SIMULATION:\n";
            echo "   User ID: {$user->id}\n";
            echo "   Username: {$user->username}\n";
            echo "   Email: {$user->email}\n";
            echo "   Full Name: " . ($user->first_name ?? '') . ' ' . ($user->last_name ?? '') . "\n";
            
        } else {
            echo "❌ Password verification failed\n";
            echo "💡 Trying other common passwords...\n";
            
            $commonPasswords = ['admin123', 'password', '123456', 'admin', 'test123'];
            foreach ($commonPasswords as $pwd) {
                if (password_verify($pwd, $user->password)) {
                    echo "✅ Password '$pwd' works!\n";
                    break;
                }
            }
        }
    } else {
        echo "❌ User '$testUsername' not found\n";
        
        // Try other usernames
        $testUsernames = ['admin', 'engineer', 'demo'];
        foreach ($testUsernames as $username) {
            $user = $userModel::findByUsername($username);
            if ($user) {
                echo "✅ Found user: $username\n";
                break;
            }
        }
    }
    
    echo "\n🔧 CREATING TEST LOGIN FUNCTION:\n";
    
    function testLogin($username, $password) {
        try {
            $userModel = new \App\Models\User();
            $user = $userModel::findByUsername($username);
            
            if (!$user) {
                return ['success' => false, 'error' => 'User not found'];
            }
            
            if (!password_verify($password, $user->password)) {
                return ['success' => false, 'error' => 'Invalid password'];
            }
            
            return [
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'full_name' => ($user->first_name ?? '') . ' ' . ($user->last_name ?? '')
                ]
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    // Test the login function
    echo "\n🧪 Testing login function:\n";
    
    $testCases = [
        ['uniquebishwo', 'password123'],
        ['admin', 'admin123'],
        ['engineer@engicalpro.com', 'password123'],
        ['demo', 'demo123']
    ];
    
    foreach ($testCases as [$username, $password]) {
        echo "\n👤 Testing: $username / $password\n";
        $result = testLogin($username, $password);
        
        if ($result['success']) {
            echo "   ✅ Login successful!\n";
            echo "   👤 User: {$result['user']['username']} ({$result['user']['email']})\n";
        } else {
            echo "   ❌ Login failed: {$result['error']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "📋 Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✨ Test complete!\n";
?>
