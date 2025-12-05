<?php
require_once 'app/bootstrap.php';

echo "Testing user lookup...\n";

try {
    $userModel = new \App\Models\User();
    
    // Try to find the test user
    $user = $userModel->findByUsername('uniquebishwo@gmail.com');
    
    if ($user) {
        echo "✅ User found!\n";
        echo "User ID: " . $user->id . "\n";
        echo "Username: " . $user->username . "\n";
        echo "Email: " . $user->email . "\n";
    } else {
        echo "❌ User not found\n";
        
        // Let's try to create a test user
        echo "Creating test user...\n";
        $testUser = [
            'username' => 'uniquebishwo@gmail.com',
            'email' => 'uniquebishwo@gmail.com',
            'password' => password_hash('c9PU7XAsAADYk_A', PASSWORD_DEFAULT),
            'first_name' => 'Test',
            'last_name' => 'User',
            'role' => 'admin',
            'is_admin' => 1
        ];
        
        $userId = $userModel->create($testUser);
        echo "✅ Test user created with ID: " . $userId . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ User lookup failed: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "Testing complete.\n";
?>