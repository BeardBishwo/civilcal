<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Updating user_resource_logs Enum...\n";

try {
    $pdo->exec("ALTER TABLE user_resource_logs MODIFY COLUMN resource_type ENUM('bricks', 'cement', 'steel', 'coins', 'sand', 'wood_logs', 'wood_planks') NOT NULL");
    echo "Enum updated successfully!\n";
} catch (Exception $e) {
    echo "Failed to update Enum: " . $e->getMessage() . "\n";
}
