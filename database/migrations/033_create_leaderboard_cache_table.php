<?php

namespace App\Database\Migrations;

use App\Core\Database;

class CreateLeaderboardCacheTable
{
    public function up()
    {
        $db = new Database();
        $pdo = $db->getPdo();
        
        $sql = "
        CREATE TABLE IF NOT EXISTS `leaderboard_cache` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `category` VARCHAR(50) NOT NULL,
            `period_type` ENUM('weekly', 'monthly', 'yearly') NOT NULL,
            `period_value` VARCHAR(20) NOT NULL,
            `top_users` LONGTEXT NOT NULL COMMENT 'JSON Cache of top users',
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `unique_cache_key` (`category`, `period_type`, `period_value`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        try {
            $pdo->exec($sql);
            echo "Migrated: leaderboard_cache table created successfully.\n";
        } catch (\PDOException $e) {
            echo "Migration Failed: " . $e->getMessage() . "\n";
        }
    }

    public function down()
    {
        $db = new Database();
        $pdo = $db->getPdo();
        $pdo->exec("DROP TABLE IF EXISTS `leaderboard_cache`");
        echo "Dropped leaderboard_cache table.\n";
    }
}
