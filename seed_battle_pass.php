<?php
// Seed Battle Pass Data

$host = '127.0.0.1';
$db   = 'bishwo_calculator';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Check if season already exists
    $check = $pdo->query("SELECT id FROM battle_pass_seasons WHERE title = 'Civil Uprising'")->fetch();
    if (!$check) {
        $stmt = $pdo->prepare('INSERT INTO battle_pass_seasons (title, start_date, end_date, is_active) VALUES (?, ?, ?, 1)');
        $stmt->execute(['Civil Uprising', '2024-01-01', '2024-12-31']);
        $sid = $pdo->lastInsertId();
        
        $rewards = [
            [1, 'coins', '500', 0],
            [2, 'bricks', '100', 0],
            [3, 'lifeline', '50_50', 0],
            [4, 'coins', '1000', 1],
            [5, 'building', 'house', 1]
        ];
        
        $stmt = $pdo->prepare('INSERT INTO battle_pass_rewards (season_id, level, reward_type, reward_value, is_premium) VALUES (?, ?, ?, ?, ?)');
        foreach($rewards as $r) {
            $stmt->execute([$sid, $r[0], $r[1], $r[2], $r[3]]);
        }
        echo "Seeded Season and Rewards successfully.\n";
    } else {
        echo "Season 'Civil Uprising' already exists.\n";
    }

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
