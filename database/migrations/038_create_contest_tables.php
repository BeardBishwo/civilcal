<?php

use App\Core\Database;

class Migration_Create_Contest_Tables {
    public function up() {
        $db = Database::getInstance();
        
        // 1. THE CONTEST (The Event)
        $sql1 = "CREATE TABLE IF NOT EXISTS `contests` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `image_path` VARCHAR(255),
            `start_time` DATETIME NOT NULL,
            `end_time` DATETIME NOT NULL,
            `entry_fee` INT DEFAULT 0,
            `prize_pool` INT DEFAULT 1000,
            `winner_count` INT DEFAULT 1,
            `is_automated` BOOLEAN DEFAULT FALSE,
            `status` ENUM('upcoming', 'live', 'ended') DEFAULT 'upcoming',
            `questions` JSON NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        // 2. PARTICIPANTS (Who joined?)
        $sql2 = "CREATE TABLE IF NOT EXISTS `contest_participants` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `contest_id` BIGINT UNSIGNED NOT NULL,
            `user_id` INT(11) UNSIGNED NOT NULL,
            `score` INT DEFAULT 0,
            `time_taken` INT DEFAULT 0,
            `rank` INT NULL,
            `is_winner` BOOLEAN DEFAULT FALSE,
            `prize_awarded` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY `contest_id` (`contest_id`),
            KEY `user_id` (`user_id`),
            KEY `is_winner` (`is_winner`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        try {
            $db->query($sql1);
            echo "Table contests created successfully.\n";
            $db->query($sql2);
            echo "Table contest_participants created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating tables: " . $e->getMessage() . "\n";
        }
    }

    public function down() {
        $db = Database::getInstance();
        $db->query("DROP TABLE IF EXISTS `contest_participants`");
        $db->query("DROP TABLE IF EXISTS `contests`");
    }
}

// Execute if run directly
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    require_once __DIR__ . '/../../app/Core/Database.php';
    $migration = new Migration_Create_Contest_Tables();
    $migration->up();
}
