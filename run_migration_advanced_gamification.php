<?php
// Raw PDO migration for Advanced Gamification Features

$host = '127.0.0.1';
$db   = 'bishwo_calculator';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected to Database.\n";

    // 1. Lifelines Inventory
    $sql1 = "
    CREATE TABLE IF NOT EXISTS user_lifelines (
        user_id INT UNSIGNED NOT NULL,
        lifeline_type ENUM('50_50', 'ai_hint', 'freeze_time') NOT NULL,
        quantity INT UNSIGNED DEFAULT 0,
        PRIMARY KEY (user_id, lifeline_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sql1);
    echo "Table 'user_lifelines' created/verified.\n";

    // 2. Battle Pass Seasons
    $sql2 = "
    CREATE TABLE IF NOT EXISTS battle_pass_seasons (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        is_active TINYINT(1) DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sql2);
    echo "Table 'battle_pass_seasons' created/verified.\n";

    // 3. Battle Pass Rewards
    $sql3 = "
    CREATE TABLE IF NOT EXISTS battle_pass_rewards (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        season_id INT UNSIGNED NOT NULL,
        level INT UNSIGNED NOT NULL,
        reward_type ENUM('bricks', 'cement', 'steel', 'coins', 'lifeline', 'building') NOT NULL,
        reward_value VARCHAR(100) NOT NULL,
        is_premium TINYINT(1) DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sql3);
    echo "Table 'battle_pass_rewards' created/verified.\n";

    // 4. User Battle Pass Progress
    $sql4 = "
    CREATE TABLE IF NOT EXISTS user_battle_pass (
        user_id INT UNSIGNED NOT NULL,
        season_id INT UNSIGNED NOT NULL,
        current_xp INT UNSIGNED DEFAULT 0,
        current_level INT UNSIGNED DEFAULT 1,
        is_premium_unlocked TINYINT(1) DEFAULT 0,
        claimed_rewards JSON DEFAULT NULL COMMENT 'Array of reward IDs',
        PRIMARY KEY (user_id, season_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sql4);
    echo "Table 'user_battle_pass' created/verified.\n";

    // 5. Daily Missions
    $sql5 = "
    CREATE TABLE IF NOT EXISTS daily_missions (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        requirement_type VARCHAR(50) NOT NULL COMMENT 'solve_questions, win_battles',
        requirement_value INT NOT NULL,
        xp_reward INT UNSIGNED DEFAULT 100,
        coin_reward INT UNSIGNED DEFAULT 50
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sql5);
    echo "Table 'daily_missions' created/verified.\n";

    // 6. User Mission Progress
    $sql6 = "
    CREATE TABLE IF NOT EXISTS user_mission_progress (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        mission_id INT UNSIGNED NOT NULL,
        current_value INT DEFAULT 0,
        is_completed TINYINT(1) DEFAULT 0,
        is_claimed TINYINT(1) DEFAULT 0,
        mission_date DATE NOT NULL,
        UNIQUE KEY user_mission_date (user_id, mission_id, mission_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sql6);
    echo "Table 'user_mission_progress' created/verified.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
