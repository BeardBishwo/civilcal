<?php
require_once __DIR__ . "/app/Config/config.php";
require_once __DIR__ . "/app/Core/Database.php";
require_once __DIR__ . "/app/Models/User.php";
use App\Models\User;

try {
    $userModel = new User();
    $testUsername = 'testuser_' . time();
    $testEmail = 'testuser_' . time() . '@example.com';
    
    echo "Creating user: $testUsername\n";
    $userId = $userModel->create([
        'username' => $testUsername,
        'email' => $testEmail,
        'password' => password_hash('password', PASSWORD_BCRYPT),
        'first_name' => 'Test',
        'last_name' => 'User',
        'role' => 'user',
        'is_active' => 1
    ]);
    
    echo "User created with ID: $userId\n";
    
    $db = \App\Core\Database::getInstance();
    $stmt = $db->getPdo()->prepare("SELECT id, username, created_at, updated_at FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Verification Data:\n";
    print_r($user);
    
    if (!empty($user['created_at']) && !empty($user['updated_at'])) {
        echo "SUCCESS: Timestamps are populated.\n";
    } else {
        echo "FAILURE: Timestamps are empty.\n";
    }

    echo "Testing EmailManager...\n";
    require_once __DIR__ . "/app/Services/EmailManager.php";
    $emailManager = new EmailManager();
    $emailResult = $emailManager->sendNewAccountEmail($testEmail, 'Test User', $testUsername, 'password', 'http://localhost/login');
    echo "Email result: " . ($emailResult ? "SUCCESS (or queued)" : "FAILURE") . "\n";
    
    // Clean up
    $db->getPdo()->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
    echo "Cleanup: Deleted test user.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
