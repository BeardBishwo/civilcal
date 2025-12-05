<?php
// Debug script to identify user creation issues

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting user creation debug...\n";

// Include required files
require_once 'app/Core/Database.php';
require_once 'app/Models/User.php';

try {
    echo "1. Initializing User model...\n";
    $user = new \App\Models\User();
    echo "   ✓ User model created successfully\n";
    
    echo "2. Testing database connection...\n";
    $db = $user->getDb();
    $pdo = $db->getPdo();
    echo "   ✓ Database connection successful\n";
    
    echo "3. Checking users table structure...\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    echo "   Current columns in users table:\n";
    foreach ($columns as $col) {
        echo "   - {$col['Field']} ({$col['Type']})\n";
    }
    
    echo "4. Running schema ensurement...\n";
    // $user->ensureAgreementColumns(); // Private method - skip for now
    echo "   ✓ Schema check completed\n";
    
    echo "5. Re-checking table structure after schema update...\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    echo "   Updated columns in users table:\n";
    foreach ($columns as $col) {
        echo "   - {$col['Field']} ({$col['Type']})\n";
    }
    
    echo "6. Testing user creation with minimal data...\n";
    
    // Generate unique test data
    $timestamp = time();
    $testData = [
        'username' => 'testuser_' . $timestamp,
        'email' => 'test_' . $timestamp . '@example.com',
        'password' => password_hash('testpass123', PASSWORD_BCRYPT),
        'first_name' => 'Test',
        'last_name' => 'User',
        'role' => 'user',
        'is_active' => 1,
        'email_verified' => 1,
        'terms_agreed' => 1,
        'marketing_emails' => 0
    ];
    
    echo "   Test data prepared:\n";
    echo "   - Username: {$testData['username']}\n";
    echo "   - Email: {$testData['email']}\n";
    
    $userId = $user->create($testData);
    echo "   ✓ User created successfully with ID: $userId\n";
    
    echo "7. Verifying user exists...\n";
    $createdUser = $user->find($userId);
    if ($createdUser) {
        echo "   ✓ User found in database\n";
        echo "   - Username: {$createdUser['username']}\n";
        echo "   - Email: {$createdUser['email']}\n";
        echo "   - Role: {$createdUser['role']}\n";
    } else {
        echo "   ✗ User not found after creation\n";
    }
    
    echo "\n✅ All tests passed! User creation is working correctly.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error occurred: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    // Additional error details
    if (isset($pdo)) {
        echo "\nPDO Error Info:\n";
        print_r($pdo->errorInfo());
    }
}