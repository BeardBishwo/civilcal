<?php
/**
 * Add user's personal account to the database
 */

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/app/bootstrap.php';

echo "👤 ADDING USER ACCOUNT\n";
echo "======================\n\n";

try {
    $userModel = new \App\Models\User();
    
    // User's credentials
    $username = 'uniquebishwo';
    $email = 'uniquebishwo@gmail.com';
    $password = 'c9PU7XAsAADYk_A';
    
    echo "📝 Creating account for:\n";
    echo "   👤 Username: $username\n";
    echo "   📧 Email: $email\n";
    echo "   🔑 Password: " . str_repeat('*', strlen($password)) . "\n\n";
    
    // Check if user already exists
    $existingUser = $userModel::findByUsername($username);
    if ($existingUser) {
        echo "⚠️ User already exists! Updating password...\n";
        
        // Update existing user's password
        $db = \App\Core\Database::getInstance();
        $pdo = $db->getPdo();
        
        $stmt = $pdo->prepare("UPDATE users SET password = ?, email = ? WHERE username = ?");
        $result = $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $email,
            $username
        ]);
        
        if ($result) {
            echo "✅ User account updated successfully!\n";
        } else {
            echo "❌ Failed to update user account\n";
        }
    } else {
        echo "🆕 Creating new user account...\n";
        
        // Create new user
        $result = $userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'first_name' => 'Bishwo',
            'last_name' => 'User',
            'role' => 'admin',
            'is_active' => 1,
            'email_verified' => 1,
            'is_admin' => 1
        ]);
        
        if ($result) {
            echo "✅ User account created successfully!\n";
            echo "🆔 User ID: $result\n";
        } else {
            echo "❌ Failed to create user account\n";
        }
    }
    
    echo "\n🎯 ACCOUNT DETAILS:\n";
    echo "==================\n";
    echo "Username: $username\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
    echo "Role: Admin\n";
    echo "Status: Active\n\n";
    
    echo "🔐 LOGIN CREDENTIALS:\n";
    echo "====================\n";
    echo "You can now login with either:\n";
    echo "• Username: $username\n";
    echo "• Email: $email\n";
    echo "• Password: $password\n\n";
    
    // Test the login
    echo "🧪 TESTING LOGIN...\n";
    $testUser = $userModel::findByUsername($username);
    if ($testUser && password_verify($password, $testUser->password)) {
        echo "✅ Login test successful!\n";
        echo "👤 Found user: " . $testUser->username . " (" . $testUser->email . ")\n";
    } else {
        echo "❌ Login test failed!\n";
    }
    
    echo "\n✨ ACCOUNT SETUP COMPLETE!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
