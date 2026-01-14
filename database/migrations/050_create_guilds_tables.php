<?php

use App\Core\Database;

class Migration_Create_Guilds_Tables
{
    public function up()
    {
        $db = Database::getInstance();
        $pdo = $db->getPdo();

        // 1. Guilds/Firms Table
        $sql1 = "
        CREATE TABLE IF NOT EXISTS guilds (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE,
            description TEXT,
            leader_id INT UNSIGNED NOT NULL,
            level INT UNSIGNED DEFAULT 1,
            xp INT UNSIGNED DEFAULT 0,
            total_bricks INT UNSIGNED DEFAULT 0,
            total_cement INT UNSIGNED DEFAULT 0,
            total_steel INT UNSIGNED DEFAULT 0,
            total_coins INT UNSIGNED DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_level_xp (level, xp)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql1);

        // 2. Guild Members Table
        $sql2 = "
        CREATE TABLE IF NOT EXISTS guild_members (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            guild_id BIGINT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NOT NULL,
            role ENUM('Leader', 'Officer', 'Member') DEFAULT 'Member',
            contribution_bricks INT UNSIGNED DEFAULT 0,
            contribution_cement INT UNSIGNED DEFAULT 0,
            contribution_steel INT UNSIGNED DEFAULT 0,
            contribution_coins INT UNSIGNED DEFAULT 0,
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (guild_id) REFERENCES guilds(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user (user_id),
            INDEX idx_guild (guild_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql2);

        // 3. Guild Join Requests Table (Optional but useful)
        $sql3 = "
        CREATE TABLE IF NOT EXISTS guild_join_requests (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            guild_id BIGINT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NOT NULL,
            status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
            requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            processed_at TIMESTAMP NULL DEFAULT NULL,
            processed_by INT UNSIGNED NULL,
            FOREIGN KEY (guild_id) REFERENCES guilds(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_guild_status (guild_id, status),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql3);

        echo "Guilds/Firms Tables Created Successfully.\n";
        echo "- guilds table created\n";
        echo "- guild_members table created\n";
        echo "- guild_join_requests table created\n";
    }

    public function down()
    {
        $db = Database::getInstance();
        $pdo = $db->getPdo();
        $pdo->exec("DROP TABLE IF EXISTS guild_join_requests");
        $pdo->exec("DROP TABLE IF EXISTS guild_members");
        $pdo->exec("DROP TABLE IF EXISTS guilds");
        echo "Guilds/Firms Tables Dropped.\n";
    }
}

// Execute if run directly
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    require_once __DIR__ . '/../../app/Core/Database.php';
    $migration = new Migration_Create_Guilds_Tables();
    $migration->up();
}
