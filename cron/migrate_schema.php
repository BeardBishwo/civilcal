<?php
// cron/migrate_schema.php
// Run ONCE to setup extra columns for automation
if (php_sapi_name() !== 'cli') die('CLI only');

require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Migrating Database Schema for Automation...\n";

function addColumn($pdo, $table, $column, $definition) {
    try {
        $check = $pdo->query("SHOW COLUMNS FROM $table LIKE '$column'");
        if ($check->rowCount() == 0) {
            $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
            echo "✅ Added column '$column' to table '$table'.\n";
        } else {
            echo "ℹ️ Column '$column' already exists in '$table'.\n";
        }
    } catch (PDOException $e) {
        echo "❌ Error adding '$column' to '$table': " . $e->getMessage() . "\n";
    }
}

// 1. User Resources: Daily Tracking
addColumn($pdo, 'user_resources', 'daily_ads_watched', 'INT DEFAULT 0');
addColumn($pdo, 'user_resources', 'daily_login_claimed', 'TINYINT(1) DEFAULT 0');

// 2. Leaderboard Aggregates: Rank Tracking
// Note: These might be redundant if we rely solely on cache, but good for real-time fallback
addColumn($pdo, 'quiz_leaderboard_aggregates', 'rank_current', 'INT NULL');
addColumn($pdo, 'quiz_leaderboard_aggregates', 'rank_previous', 'INT NULL');

// 3. Security Nonces Table (Ensure it exists)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS security_nonces (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nonce VARCHAR(64) NOT NULL,
        user_id INT NULL,
        action VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        used_at TIMESTAMP NULL,
        INDEX idx_nonce (nonce),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✅ Table 'security_nonces' verified.\n";
} catch (PDOException $e) {
    echo "❌ Error creating 'security_nonces': " . $e->getMessage() . "\n";
}

echo "Schema Migration Complete.\n";
