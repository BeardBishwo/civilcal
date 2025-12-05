<?php
/**
 * Migration: Create Two-Factor Authentication Tables
 * 
 * Creates tables for:
 * - 2FA secrets and backup codes
 * - Login attempts and trusted devices
 * - Activity logs for security auditing
 */

require_once __DIR__ . '/../../app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "Creating Two-Factor Authentication tables...\n\n";
    
    // 1. Add 2FA columns to users table
    echo "1. Adding 2FA columns to users table...\n";
    
    $twoFaColumns = [
        'two_factor_enabled' => "ALTER TABLE users ADD COLUMN two_factor_enabled TINYINT(1) DEFAULT 0 AFTER password",
        'two_factor_secret' => "ALTER TABLE users ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled",
        'two_factor_recovery_codes' => "ALTER TABLE users ADD COLUMN two_factor_recovery_codes TEXT NULL AFTER two_factor_secret",
        'two_factor_confirmed_at' => "ALTER TABLE users ADD COLUMN two_factor_confirmed_at DATETIME NULL AFTER two_factor_recovery_codes"
    ];
    
    foreach ($twoFaColumns as $columnName => $sql) {
        $checkSql = "SHOW COLUMNS FROM users LIKE '$columnName'";
        $result = $pdo->query($checkSql);
        
        if ($result->rowCount() == 0) {
            $pdo->exec($sql);
            echo "   ✓ Added column: $columnName\n";
        } else {
            echo "   - Column exists: $columnName\n";
        }
    }
    
    // 2. Create trusted_devices table
    echo "\n2. Creating trusted_devices table...\n";
    
    $trustedDevicesSql = "
        CREATE TABLE IF NOT EXISTS trusted_devices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            device_name VARCHAR(255) NOT NULL,
            device_fingerprint VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            last_used_at DATETIME,
            trusted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_device_fingerprint (device_fingerprint),
            INDEX idx_expires_at (expires_at),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($trustedDevicesSql);
    echo "   ✓ Created trusted_devices table\n";
    
    // 3. Create login_attempts table (for security monitoring)
    echo "\n3. Creating login_attempts table...\n";
    
    $loginAttemptsSql = "
        CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            email VARCHAR(255),
            ip_address VARCHAR(45),
            user_agent TEXT,
            attempt_type ENUM('password', '2fa', 'recovery_code') DEFAULT 'password',
            success TINYINT(1) DEFAULT 0,
            failure_reason VARCHAR(255),
            attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_email (email),
            INDEX idx_attempted_at (attempted_at),
            INDEX idx_success (success),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($loginAttemptsSql);
    echo "   ✓ Created login_attempts table\n";
    
    // 4. Create user_activity_logs table (for GDPR audit trail)
    echo "\n4. Creating user_activity_logs table...\n";
    
    $activityLogsSql = "
        CREATE TABLE IF NOT EXISTS user_activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            activity_type VARCHAR(100) NOT NULL,
            activity_description TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            metadata JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_activity_type (activity_type),
            INDEX idx_created_at (created_at),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($activityLogsSql);
    echo "   ✓ Created user_activity_logs table\n";
    
    // 5. Create data_export_requests table (for GDPR)
    echo "\n5. Creating data_export_requests table...\n";
    
    $exportRequestsSql = "
        CREATE TABLE IF NOT EXISTS data_export_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            request_type ENUM('export', 'delete') DEFAULT 'export',
            status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
            file_path VARCHAR(255),
            file_size INT,
            expires_at DATETIME,
            requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            completed_at DATETIME NULL,
            error_message TEXT,
            INDEX idx_user_id (user_id),
            INDEX idx_status (status),
            INDEX idx_expires_at (expires_at),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($exportRequestsSql);
    echo "   ✓ Created data_export_requests table\n";
    
    echo "\n✅ Two-Factor Authentication migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
