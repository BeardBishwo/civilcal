<?php

use App\Core\Database;

class Migration_Create_Question_Reports_Table {
    public function up() {
        $db = Database::getInstance();
        $sql = "CREATE TABLE IF NOT EXISTS `question_reports` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `question_id` BIGINT(20) UNSIGNED NOT NULL,
            `user_id` INT(11) UNSIGNED NOT NULL,
            `issue_type` ENUM('typo', 'wrong_answer', 'confusing', 'other') DEFAULT 'other',
            `description` TEXT,
            `status` ENUM('pending', 'resolved', 'ignored') DEFAULT 'pending',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `question_id` (`question_id`),
            KEY `user_id` (`user_id`),
            KEY `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        try {
            $db->query($sql);
            echo "Table question_reports created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table: " . $e->getMessage() . "\n";
        }
    }

    public function down() {
        $db = Database::getInstance();
        $db->query("DROP TABLE IF EXISTS `question_reports`");
    }
}

// Execute if run directly
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    require_once __DIR__ . '/../../app/Core/Database.php';
    // Mock for CLI
    if (!class_exists('App\Core\Database')) {
        // Simple mock if needed, or rely on autoloader if configured
    }
    $migration = new Migration_Create_Question_Reports_Table();
    $migration->up();
}
