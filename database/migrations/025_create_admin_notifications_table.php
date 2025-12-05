<?php
/**
 * Migration: Create admin_notifications table
 */

require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    $sql = "
    CREATE TABLE IF NOT EXISTS `admin_notifications` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `type` ENUM('info', 'success', 'warning', 'error') NOT NULL DEFAULT 'info',
        `data` JSON NULL DEFAULT NULL,
        `is_read` TINYINT(1) NOT NULL DEFAULT 0,
        `read_at` DATETIME NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        CONSTRAINT `admin_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $db->query($sql);
    
    // Insert sample data for testing
    $sampleSql = "
    INSERT IGNORE INTO `admin_notifications` (`user_id`, `title`, `message`, `type`) VALUES
    (1, 'Welcome!', 'Welcome to the admin panel notifications system.', 'success'),
    (1, 'New User Registered', 'A new user has registered on the platform.', 'info'),
    (1, 'System Update Required', 'Please update your system to the latest version.', 'warning');
    ";
    $db->query($sampleSql);
    
    echo "✅ Admin notifications table created successfully with sample data.\n";
    
} catch (Exception $e) {
    echo "❌ Error creating table: " . $e->getMessage() . "\n";
}
?>
