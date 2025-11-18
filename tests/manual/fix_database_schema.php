<?php
require_once 'app/bootstrap.php';

try {
    echo "=== Fixing Database Schema ===\n";
    
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    // Add missing columns to users table
    $columns = [
        'login_count' => 'ALTER TABLE users ADD COLUMN login_count INT DEFAULT 0',
        'last_login' => 'ALTER TABLE users ADD COLUMN last_login DATETIME NULL',
        'is_admin' => 'ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0',
        'subscription_id' => 'ALTER TABLE users ADD COLUMN subscription_id VARCHAR(100) NULL',
        'subscription_status' => 'ALTER TABLE users ADD COLUMN subscription_status VARCHAR(50) NULL',
        'subscription_ends_at' => 'ALTER TABLE users ADD COLUMN subscription_ends_at DATETIME NULL',
        'notification_preferences' => 'ALTER TABLE users ADD COLUMN notification_preferences TEXT NULL',
        'email_notifications' => 'ALTER TABLE users ADD COLUMN email_notifications TINYINT(1) DEFAULT 1',
        'calculation_privacy' => 'ALTER TABLE users ADD COLUMN calculation_privacy VARCHAR(20) DEFAULT "private"',
        'updated_at' => 'ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ];
    
    foreach ($columns as $columnName => $sql) {
        try {
            // Check if column exists
            $checkSql = "SHOW COLUMNS FROM users LIKE '{$columnName}'";
            $result = $pdo->query($checkSql);
            
            if ($result->rowCount() == 0) {
                $pdo->exec($sql);
                echo "✓ Added column '{$columnName}'\n";
            } else {
                echo "- Column '{$columnName}' already exists\n";
            }
        } catch (Exception $e) {
            echo "✗ Error with column '{$columnName}': " . $e->getMessage() . "\n";
        }
    }
    
    // Update existing admin users to have is_admin = 1
    echo "\n--- Updating existing admin users ---\n";
    $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE role = 'admin'");
    $result = $stmt->execute();
    echo "✓ Updated admin flag for admin users\n";
    
    // Create missing tables
    echo "\n--- Creating missing tables ---\n";
    
    // Create user_sessions table
    $createSessions = "
        CREATE TABLE IF NOT EXISTS user_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            session_token VARCHAR(255) UNIQUE NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            expires_at DATETIME NOT NULL,
            last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_session_token (session_token),
            INDEX idx_user_id (user_id),
            INDEX idx_expires_at (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $pdo->exec($createSessions);
    echo "✓ Created/verified user_sessions table\n";
    
    // Create login_history table
    $createLoginHistory = "
        CREATE TABLE IF NOT EXISTS login_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            success TINYINT(1) NOT NULL DEFAULT 0,
            failure_reason VARCHAR(255),
            login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_login_time (login_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $pdo->exec($createLoginHistory);
    echo "✓ Created/verified login_history table\n";
    
    echo "\n=== Database schema fix completed! ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>