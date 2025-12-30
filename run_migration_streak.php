<?php
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

$db = Database::getInstance();

try {
    // Add login_streak if not exists
    $columns = $db->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('login_streak', $columns)) {
        $db->query("ALTER TABLE users ADD COLUMN login_streak INT DEFAULT 0 AFTER last_login_reward_at");
        echo "Added login_streak column to users table.\n";
    } else {
        echo "login_streak column already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
