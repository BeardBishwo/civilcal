<?php
require_once 'app/bootstrap.php';

try {
    echo "=== Testing Admin Login Flow ===\n";
    
    // Test with different admin users
    $adminUsers = [
        ['username' => 'admin', 'password' => 'admin123'],
        ['username' => 'admin_demo', 'password' => 'password123'],
        ['username' => 'uniquebishwo', 'password' => 'password123']
    ];
    
    foreach ($adminUsers as $testUser) {
        echo "\n--- Testing user: {$testUser['username']} ---\n";
        
        $result = \App\Core\Auth::login($testUser['username'], $testUser['password']);
        
        if ($result['success']) {
            echo "✓ Login SUCCESS\n";
            $user = $result['user'];
            echo "  ID: {$user->id}\n";
            echo "  Username: {$user->username}\n";
            echo "  Email: {$user->email}\n";
            echo "  Role: {$user->role}\n";
            
            // Test isAdmin
            $isAdmin = \App\Core\Auth::isAdmin();
            echo "  Auth::isAdmin(): " . ($isAdmin ? 'YES' : 'NO') . "\n";
            
            // Test cookie auth
            echo "  Cookie check: " . (isset($_COOKIE['auth_token']) ? 'YES' : 'NO') . "\n";
            
            break; // Stop on first successful login
        } else {
            echo "✗ Login FAILED: " . ($result['message'] ?? 'Unknown error') . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>