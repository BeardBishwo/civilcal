<?php
require_once __DIR__ . '/../../app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance();
    
    echo "=== Setting up demo users ===\n\n";
    
    // Check if users already exist
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    $count = $result->fetch(\PDO::FETCH_ASSOC)['count'];
    
    if ($count > 0) {
        echo "Found $count existing users. Checking for test users...\n";
    }
    
    // Test users to create
    $testUsers = [
        [
            'username' => 'admin',
            'email' => 'admin@bishwocalculator.com',
            'password' => 'admin123',
            'role' => 'admin',
            'first_name' => 'System',
            'last_name' => 'Administrator'
        ],
        [
            'username' => 'uniquebishwo',
            'email' => 'uniquebishwo@gmail.com',
            'password' => 'SecurePass123!',
            'role' => 'user',
            'first_name' => 'Bishwo',
            'last_name' => 'User'
        ],
        [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'TestPass123!',
            'role' => 'user',
            'first_name' => 'Test',
            'last_name' => 'User'
        ]
    ];
    
    foreach ($testUsers as $user) {
        // Check if user exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$user['email'], $user['username']]);
        $existing = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($existing) {
            echo "✓ User '{$user['username']}' already exists (ID: {$existing['id']})\n";
            continue;
        }
        
        // Create user
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt = $db->prepare("
            INSERT INTO users (username, email, password, role, first_name, last_name, is_active, email_verified, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 1, 1, NOW())
        ");
        
        $stmt->execute([
            $user['username'],
            $user['email'],
            $hashedPassword,
            $user['role'],
            $user['first_name'],
            $user['last_name']
        ]);
        
        $userId = $db->lastInsertId();
        echo "✓ Created user '{$user['username']}' (ID: $userId)\n";
        echo "  Email: {$user['email']}\n";
        echo "  Password: {$user['password']}\n";
        echo "  Role: {$user['role']}\n\n";
    }
    
    // Display final count
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    $finalCount = $result->fetch(\PDO::FETCH_ASSOC)['count'];
    
    echo "\n=== Summary ===\n";
    echo "Total users in database: $finalCount\n";
    echo "\n[SUCCESS] Demo users setup complete!\n";
    
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
