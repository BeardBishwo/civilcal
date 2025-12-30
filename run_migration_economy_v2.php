<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Starting Economy Migration V2...\n";

try {
    // 1. Ensure user_resources has all columns
    $columns = [
        'sand' => "ALTER TABLE user_resources ADD COLUMN sand INT DEFAULT 0 AFTER cement",
        'wood_logs' => "ALTER TABLE user_resources ADD COLUMN wood_logs INT DEFAULT 0 AFTER sand",
        'wood_planks' => "ALTER TABLE user_resources ADD COLUMN wood_planks INT DEFAULT 0 AFTER wood_logs",
    ];

    $existingColumns = $pdo->query("SHOW COLUMNS FROM user_resources")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($columns as $col => $sql) {
        if (!in_array($col, $existingColumns)) {
            $pdo->exec($sql);
            echo "Added column: $col\n";
        } else {
            echo "Column $col already exists.\n";
        }
    }

    // 2. Add last_login_reward_at to users if not exists (for daily login logs/sand)
    $userColumns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('last_login_reward_at', $userColumns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN last_login_reward_at DATE NULL AFTER last_login");
        echo "Added last_login_reward_at to users table.\n";
    }

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
