<?php
require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

try {
    echo "=== Bounty System Migration Runner ===\n\n";
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    echo "Connected to database.\n";

    $sqlFile = __DIR__ . '/../database/migrations/bounty_system_setup.sql';
    if (!file_exists($sqlFile)) {
        die("Migration file not found: $sqlFile\n");
    }

    $sql = file_get_contents($sqlFile);
    
    $pdo->exec($sql);
    echo "Bounty tables created successfully.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
