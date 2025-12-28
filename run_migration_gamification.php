<?php
// Configuration
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

    // 1. User Resources (The Wallet)
    $sql1 = "
    CREATE TABLE IF NOT EXISTS user_resources (
        user_id INT UNSIGNED PRIMARY KEY,
        bricks INT UNSIGNED DEFAULT 0,
        cement INT UNSIGNED DEFAULT 0,
        steel INT UNSIGNED DEFAULT 0,
        coins INT UNSIGNED DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    // FOREIGN KEY removed for raw runner safety unless we confirm 'users' ID type
    // Assuming users.id is INT UNSIGNED from previous steps. 
    
    $pdo->exec($sql1);
    echo "Table 'user_resources' created/verified.\n";

    // 2. User City Buildings
    $sql2 = "
    CREATE TABLE IF NOT EXISTS user_city_buildings (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        building_type VARCHAR(50) NOT NULL COMMENT 'house, road, bridge, tower',
        level INT UNSIGNED DEFAULT 1,
        coordinates VARCHAR(50) DEFAULT '0,0' COMMENT 'x,y',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sql2);
    echo "Table 'user_city_buildings' created/verified.\n";

    // 3. Transactions Log
    $sql3 = "
    CREATE TABLE IF NOT EXISTS user_resource_logs (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        resource_type ENUM('bricks','cement','steel','coins') NOT NULL,
        amount INT NOT NULL COMMENT 'Positive for earn, Negative for spend',
        source VARCHAR(100) NOT NULL COMMENT 'quiz_reward, building_cost, daily_bonus',
        reference_id INT UNSIGNED DEFAULT NULL COMMENT 'e.g., attempt_id or building_id',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sql3);
    echo "Table 'user_resource_logs' created/verified.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
