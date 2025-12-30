<?php
// cron/update_leaderboard.php
// Run HOURLY via Cron
if (php_sapi_name() !== 'cli') die('CLI only');

// Shared Hosting Optimization
ini_set('memory_limit', '64M');
set_time_limit(30);

require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Updating Leaderboard Cache: " . date('Y-m-d H:i:s') . "\n";

// Categories to cache
$categories = [
    'global' => 0, // category_id = 0 for Global
    // 'tycoons' => net worth logic (TODO)
];

// Periods
$periods = [
    'weekly' => date('Y-W'),
    'monthly' => date('Y-m'),
    'yearly' => date('Y')
];

foreach ($periods as $pType => $pVal) {
    foreach ($categories as $catName => $catId) {
        echo "Processing $catName ($pType - $pVal)... ";
        
        // Fetch Top 100
        $sql = "
            SELECT l.user_id, l.total_score, l.tests_taken, l.accuracy_avg, u.username, u.avatar
            FROM quiz_leaderboard_aggregates l
            JOIN users u ON l.user_id = u.id
            WHERE l.period_type = :ptype 
            AND l.period_value = :pval 
            AND l.category_id = :cat
            ORDER BY l.total_score DESC
            LIMIT 100
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'ptype' => $pType,
            'pval' => $pVal,
            'cat' => $catId
        ]);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Upsert Cache
        $json = json_encode($results);
        
        $upsert = "
            INSERT INTO leaderboard_cache (category, period_type, period_value, top_users)
            VALUES (:cat, :ptype, :pval, :json)
            ON DUPLICATE KEY UPDATE top_users = :json2, last_updated = NOW()
        ";
        
        $stmtUpsert = $pdo->prepare($upsert);
        $stmtUpsert->execute([
            'cat' => $catName,
            'ptype' => $pType,
            'pval' => $pVal,
            'json' => $json,
            'json2' => $json
        ]);
        
        echo "Cached " . count($results) . " users.\n";
    }
}

// Special Category: The Tycoons (Net Worth)
// Coins=1, Logs=2, Sand=3, Bricks=5, Cement=10, Steel=50, Planks=8
echo "Processing Tycoons (Global)... ";

$sqlTycoon = "
    SELECT u.id as user_id, u.username, u.full_name, u.avatar,
    (
        r.coins * 1 + 
        r.wood_logs * 2 + 
        r.sand * 3 + 
        r.bricks * 5 + 
        r.cement * 10 + 
        r.steel * 50 + 
        r.wood_planks * 8
    ) as total_score,
    0 as tests_taken, 0 as accuracy_avg
    FROM user_resources r
    JOIN users u ON r.user_id = u.id
    ORDER BY total_score DESC
    LIMIT 100
";

$stmt = $pdo->query($sqlTycoon);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$json = json_encode($results);

// Tycoons are always Current (Real-time snapshot basically, but cached hourly)
// We store it as 'tycoons' category, period_type='all_time', period_value='current'
// Or simpler: category='tycoons', period_type='weekly', period_value=CURRENT_WEEK
// Let's stick to the loops if we want history, but resources are stateful, not periodic diffs.
// So Tycoons is best as a "Current Standing".
// Let's use period_type='global', period_value='now' for simplicity in this specific cache.
// OR just reuse the loop structure for "Weekly Tycoons" doesn't make sense unless we track delta.
// We will store it as: category='tycoons', period_type='all_time', period_value='current'

$upsert = "
    INSERT INTO leaderboard_cache (category, period_type, period_value, top_users)
    VALUES ('tycoons', 'all_time', 'current', :json)
    ON DUPLICATE KEY UPDATE top_users = :json2, last_updated = NOW()
";

$stmtUpsert = $pdo->prepare($upsert);
$stmtUpsert->execute(['json' => $json, 'json2' => $json]);

echo "Cached " . count($results) . " Tycoons.\n";

// Special Category: The Geniuses (Most Hard Questions Solved)
echo "Processing Geniuses (Global)... ";

$sqlGenius = "
    SELECT u.id as user_id, u.username, u.full_name, u.avatar,
    COUNT(a.id) as total_score,
    0 as tests_taken, 0 as accuracy_avg
    FROM quiz_attempt_answers a
    JOIN quiz_questions q ON a.question_id = q.id
    JOIN quiz_attempts qa ON a.attempt_id = qa.id
    JOIN users u ON qa.user_id = u.id
    WHERE a.is_correct = 1 
    AND (q.difficulty_level = 'hard' OR q.difficulty_level >= 3)
    GROUP BY u.id
    ORDER BY total_score DESC
    LIMIT 100
";

$stmt = $pdo->query($sqlGenius);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$json = json_encode($results);

$upsert = "
    INSERT INTO leaderboard_cache (category, period_type, period_value, top_users)
    VALUES ('geniuses', 'all_time', 'current', :json)
    ON DUPLICATE KEY UPDATE top_users = :json2, last_updated = NOW()
";

$stmtUpsert = $pdo->prepare($upsert);
$stmtUpsert->execute(['json' => $json, 'json2' => $json]);

echo "Cached " . count($results) . " Geniuses.\n";

echo "Leaderboard Cache Updated.\n";
