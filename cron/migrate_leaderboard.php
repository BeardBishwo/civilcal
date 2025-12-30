<?php
// cron/migrate_leaderboard.php
// Run ONCE to setup cache table
if (php_sapi_name() !== 'cli') die('CLI only');

require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Setting up Leaderboard Cache...\n";

try {
    $sql = "CREATE TABLE IF NOT EXISTS leaderboard_cache (
        id INT PRIMARY KEY AUTO_INCREMENT,
        category VARCHAR(50) NOT NULL,
        period_type VARCHAR(20) NOT NULL,
        period_value VARCHAR(20) NOT NULL,
        top_users JSON NOT NULL,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_lookup (category, period_type, period_value)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    echo "✅ Table 'leaderboard_cache' created/verified.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
