<?php
// Seed Daily Missions Data

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
    
    // Check if missions already exist
    $check = $pdo->query("SELECT id FROM daily_missions LIMIT 1")->fetch();
    if (!$check) {
        $missions = [
            ['Solve 5 Questions', 'Correctly answer 5 engineering questions in any mode.', 'solve_questions', 5, 200, 50],
            ['Battle Veteran', 'Complete a multiplayer battle Royale.', 'win_battles', 1, 500, 100]
        ];
        
        $stmt = $pdo->prepare('INSERT INTO daily_missions (title, description, requirement_type, requirement_value, xp_reward, coin_reward) VALUES (?, ?, ?, ?, ?, ?)');
        foreach($missions as $m) {
            $stmt->execute($m);
        }
        echo "Seeded Daily Missions successfully.\n";
    } else {
        echo "Daily Missions already exist.\n";
    }

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
