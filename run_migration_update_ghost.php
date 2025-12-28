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
    
    // Add column if not exists
    // MySQL 5.7+ supports IF NOT EXISTS in ALTER? No.
    // We check first.
    
    $check = $pdo->query("SHOW COLUMNS FROM `quiz_lobby_participants` LIKE 'last_answered_index'");
    if ($check->rowCount() == 0) {
        $sql = "ALTER TABLE `quiz_lobby_participants` ADD COLUMN `last_answered_index` INT DEFAULT -1 AFTER `current_score`";
        $pdo->exec($sql);
        echo "Added column `last_answered_index`.\n";
    } else {
        echo "Column `last_answered_index` already exists.\n";
    }

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
