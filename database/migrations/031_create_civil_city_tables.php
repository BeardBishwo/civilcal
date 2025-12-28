<?php

use App\Core\Database;

class Migration_Create_Civil_City_Tables
{
    public function up()
    {
        $db = new Database();
        $pdo = $db->getPdo();

        // 1. User Resources (The Wallet)
        $sql1 = "
        CREATE TABLE IF NOT EXISTS user_resources (
            user_id INT UNSIGNED PRIMARY KEY,
            bricks INT UNSIGNED DEFAULT 0,
            cement INT UNSIGNED DEFAULT 0,
            steel INT UNSIGNED DEFAULT 0,
            coins INT UNSIGNED DEFAULT 0,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql1);

        // 2. User City Buildings (The Assets)
        $sql2 = "
        CREATE TABLE IF NOT EXISTS user_city_buildings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            building_type VARCHAR(50) NOT NULL COMMENT 'house, road, bridge, tower',
            level INT UNSIGNED DEFAULT 1,
            coordinates VARCHAR(50) DEFAULT '0,0' COMMENT 'x,y',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql2);

        // 3. Transactions Log (Optional but good for history)
        $sql3 = "
        CREATE TABLE IF NOT EXISTS user_resource_logs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            resource_type ENUM('bricks','cement','steel','coins') NOT NULL,
            amount INT NOT NULL COMMENT 'Positive for earn, Negative for spend',
            source VARCHAR(100) NOT NULL COMMENT 'quiz_reward, building_cost, daily_bonus',
            reference_id INT UNSIGNED DEFAULT NULL COMMENT 'e.g., attempt_id or building_id',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql3);

        echo "Civil City Tables Created Successfully.\n";
    }

    public function down()
    {
        $db = new Database();
        $pdo = $db->getPdo();
        $pdo->exec("DROP TABLE IF EXISTS user_resource_logs");
        $pdo->exec("DROP TABLE IF EXISTS user_city_buildings");
        $pdo->exec("DROP TABLE IF EXISTS user_resources");
        echo "Civil City Tables Dropped.\n";
    }
}
