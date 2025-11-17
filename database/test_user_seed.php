<?php
require_once __DIR__ . '/../app/bootstrap.php';

echo "=======================================================\n";
echo "     Bishwo Calculator - Test User Seeding           \n";
echo "=======================================================\n\n";

try {
    $pdo = \App\Core\Database::getInstance()->getPdo();
    
    // Check if test user already exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute(['uniquebishwo@gmail.com']);
    $existingUser = $checkStmt->fetch();
    
    if ($existingUser) {
        echo "✅ Test user already exists: uniquebishwo@gmail.com\n";
        echo "⚠️  Skipping user creation to avoid duplicates\n\n";
    } else {
        echo "👤 Creating test user: uniquebishwo@gmail.com\n";
        
        // Create the test user that TestSprite expects
        $stmt = $pdo->prepare("
            INSERT INTO users (
                username, email, password, first_name, last_name, 
                role, is_admin, is_active, email_verified, terms_agreed, 
                terms_agreed_at, marketing_emails, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $password = password_hash('testpassword123', PASSWORD_DEFAULT);
        $result = $stmt->execute([
            'testuser',                    // username
            'uniquebishwo@gmail.com',      // email
            $password,                     // password
            'Test',                        // first_name
            'User',                        // last_name
            'admin',                       // role
            1,                            // is_admin
            1,                            // is_active
            1,                            // email_verified
            1,                            // terms_agreed
            date('Y-m-d H:i:s'),          // terms_agreed_at
            1                             // marketing_emails
        ]);
        
        if ($result) {
            $userId = $pdo->lastInsertId();
            echo "✅ Test user created successfully!\n";
            echo "   User ID: $userId\n";
            echo "   Email: uniquebishwo@gmail.com\n";
            echo "   Password: testpassword123\n";
            echo "   Role: admin\n";
            echo "   Admin status: true\n\n";
        } else {
            echo "❌ Failed to create test user\n\n";
        }
    }
    
    // Also create a regular user for testing
    $checkStmt2 = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt2->execute(['regular@example.com']);
    $existingUser2 = $checkStmt2->fetch();
    
    if (!$existingUser2) {
        echo "👤 Creating regular test user: regular@example.com\n";
        
        $stmt2 = $pdo->prepare("
            INSERT INTO users (
                username, email, password, first_name, last_name, 
                role, is_admin, is_active, email_verified, terms_agreed, 
                terms_agreed_at, marketing_emails, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $password2 = password_hash('regularpassword123', PASSWORD_DEFAULT);
        $result2 = $stmt2->execute([
            'regularuser',               // username
            'regular@example.com',       // email
            $password2,                  // password
            'Regular',                   // first_name
            'User',                      // last_name
            'user',                      // role
            0,                          // is_admin
            1,                          // is_active
            1,                          // email_verified
            1,                          // terms_agreed
            date('Y-m-d H:i:s'),        // terms_agreed_at
            1                           // marketing_emails
        ]);
        
        if ($result2) {
            $userId2 = $pdo->lastInsertId();
            echo "✅ Regular user created successfully!\n";
            echo "   User ID: $userId2\n";
            echo "   Email: regular@example.com\n";
            echo "   Password: regularpassword123\n";
            echo "   Role: user\n\n";
        } else {
            echo "❌ Failed to create regular user\n\n";
        }
    } else {
        echo "✅ Regular test user already exists: regular@example.com\n\n";
    }
    
    // Display current user count
    $userCount = $pdo->query("SELECT COUNT(*) as total FROM users")->fetch()['total'];
    echo "📊 Total users in database: $userCount\n\n";
    
    // Show all users for verification
    echo "📋 All users in the system:\n";
    $users = $pdo->query("SELECT id, username, email, role, is_admin, is_active FROM users ORDER BY id")->fetchAll();
    foreach ($users as $user) {
        echo "   ID: {$user['id']} | Username: {$user['username']} | Email: {$user['email']} | Role: {$user['role']} | Admin: {$user['is_admin']} | Active: {$user['is_active']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📁 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=======================================================\n";
echo "                USER SEEDING COMPLETE                  \n";
echo "=======================================================\n";
?>
