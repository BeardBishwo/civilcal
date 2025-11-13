<?php
/**
 * Test Demo Account Credentials
 * Verify the demo accounts exist in database
 */

require_once __DIR__ . '/app/bootstrap.php';

echo "🧪 TESTING DEMO ACCOUNTS\n";
echo "=========================\n\n";

try {
    $userModel = new \App\Models\User();
    
    // Test demo accounts
    $demoAccounts = [
        'engineer@engicalpro.com' => 'Engineer123!',
        'admin@engicalpro.com' => 'password',
        'engineer_demo' => 'Engineer123!',
        'admin' => 'password'
    ];
    
    echo "📊 Available users in database:\n";
    $allUsers = $userModel->getAll();
    foreach ($allUsers as $user) {
        echo "   👤 {$user['username']} ({$user['email']}) - Role: {$user['role']}\n";
    }
    
    echo "\n🔍 Testing demo account credentials:\n";
    
    foreach ($demoAccounts as $username => $password) {
        echo "\n📝 Testing: {$username}\n";
        
        // Try to find user
        $user = $userModel->findByUsername($username);
        
        if ($user) {
            echo "   ✅ User found: {$user->username} ({$user->email})\n";
            echo "   📧 Email: {$user->email}\n";
            echo "   🔑 Role: {$user->role}\n";
            echo "   👑 Is Admin: " . ($user->is_admin ? 'Yes' : 'No') . "\n";
            
            // Test password
            if (password_verify($password, $user->password)) {
                echo "   ✅ Password correct\n";
            } else {
                echo "   ❌ Password incorrect\n";
                echo "   💡 Try other common passwords: admin123, password123, demo123\n";
            }
        } else {
            echo "   ❌ User not found\n";
        }
    }
    
    echo "\n🎯 RECOMMENDATIONS:\n";
    echo "==================\n";
    
    if (count($allUsers) > 0) {
        echo "✅ Database has users - demo accounts should work\n";
        echo "💡 Update demo credentials in login form to match actual users\n";
        echo "🔧 Or create missing demo users in database\n";
    } else {
        echo "❌ No users in database\n";
        echo "💡 Run installer or create demo users manually\n";
    }
    
    echo "\n🚀 QUICK FIX:\n";
    echo "============\n";
    echo "1. Use existing username from list above\n";
    echo "2. Try common passwords: password, admin123, demo123\n";
    echo "3. Update login form demo credentials to match database\n";
    echo "4. Or create new demo users via admin panel\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✨ TEST COMPLETE!\n\n";
?>
