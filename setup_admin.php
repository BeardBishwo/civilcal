<?php
require_once 'app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance();
    
    echo "=== Checking users table ===\n";
    $result = $db->query("SELECT * FROM users");
    $users = $result->fetchAll(\PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "[WARNING] No users exist in database!\n";
        echo "Creating default admin user...\n";
        
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("
            INSERT INTO users (username, email, password, role, first_name, last_name, is_active, email_verified)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            'admin',
            'admin@bishwocalculator.com',
            $hashedPassword,
            'admin',
            'System',
            'Administrator',
            true,
            true
        ]);
        
        echo "[SUCCESS] Admin user created!\n";
        $userId = $db->lastInsertId();
        echo "User ID: $userId\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    } else {
        echo "Found " . count($users) . " users:\n";
        foreach ($users as $user) {
            echo "- ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, Role: {$user['role']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}
?>
