<?php

namespace App\Database\Migrations;

use App\Core\Database;

class CreateLeaderboardTable
{
    public function up()
    {
        $db = new Database();
        $pdo = $db->getPdo();
        
        $sql = "
        CREATE TABLE IF NOT EXISTS `quiz_leaderboard_aggregates` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `user_id` BIGINT UNSIGNED NOT NULL,
            `period_type` ENUM('weekly', 'monthly', 'yearly') NOT NULL,
            `period_value` VARCHAR(20) NOT NULL COMMENT 'YY-WW, YYYY-MM, or YYYY',
            `category_id` INT UNSIGNED NULL COMMENT 'For category-wise ranking',
            `total_score` DECIMAL(10,2) DEFAULT 0,
            `tests_taken` INT UNSIGNED DEFAULT 0,
            `accuracy_avg` DECIMAL(5,2) DEFAULT 0,
            `rank_current` INT UNSIGNED DEFAULT NULL,
            `rank_previous` INT UNSIGNED DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `unique_lb_aggregate` (`user_id`, `period_type`, `period_value`, `category_id`),
            KEY `idx_ranking` (`period_type`, `period_value`, `total_score` DESC),
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        try {
            $pdo->exec($sql);
            echo "Migrated: quiz_leaderboard_aggregates table created successfully.\n";
        } catch (\PDOException $e) {
            echo "Migration Failed: " . $e->getMessage() . "\n";
        }
    }

    public function down()
    {
        $db = new Database();
        $pdo = $db->getPdo();
        $pdo->exec("DROP TABLE IF EXISTS `quiz_leaderboard_aggregates`");
        echo "Dropped quiz_leaderboard_aggregates table.\n";
    }
}
