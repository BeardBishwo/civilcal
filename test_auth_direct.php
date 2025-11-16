<?php
/**
 * Direct login test - bypass HTTP routing to test PHP directly
 */
require_once 'app/bootstrap.php';

// Test with admin user
$testPayload = [
    'username_email' => 'admin',
    'password' => 'admin123'
];

echo "=== Direct API Login Test ===\n";
echo "Payload: " . json_encode($testPayload) . "\n\n";

try {
    $db = \App\Core\Database::getInstance();
    $user = \App\Models\User::findByUsername('admin');
    
    if ($user) {
        echo "[OK] User 'admin' found\n";
        echo "User ID: {$user->id}\n";
        echo "Username: {$user->username}\n";
        echo "Email: {$user->email}\n\n";
        
        if (password_verify('admin123', $user->password)) {
            echo "[OK] Password verification successful\n\n";
            
            // Try to insert session
            $pdo = $db->getPdo();
            $sessionToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            echo "Attempting to insert session token...\n";
            $stmt = $pdo->prepare("
                INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user->id,
                $sessionToken,
                '127.0.0.1',
                'PHPTest',
                $expiresAt,
            ]);
            
            echo "[OK] Session inserted successfully\n";
            echo "[SUCCESS] Login flow works correctly!\n";
        } else {
            echo "[ERROR] Password verification failed\n";
        }
    } else {
        echo "[ERROR] User 'admin' not found\n";
    }
    
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
?>
