<?php
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
        KEY `idx_ranking` (`period_type`, `period_value`, `total_score` DESC)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    // Note: Foreign key on user_id might fail if users table name differs or if users are deleted.
    // For now, I'll omit explicit FK constraint in this manual run to be safe, or check table name.
    // Assuming 'users' exists as standard.
    // Adding FK in separate alter if needed.
    
    $pdo->exec($sql);
    echo "SUCCESS: Leaderboard Table Created.\n";

} catch (\PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
