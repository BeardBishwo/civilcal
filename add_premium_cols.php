<?php
require_once 'app/Core/Database.php';
$db = App\Core\Database::getInstance();
$pdo = $db->getPdo();

try {
    $pdo->exec("ALTER TABLE position_levels ADD COLUMN is_premium TINYINT(1) DEFAULT 0 AFTER is_active");
    $pdo->exec("ALTER TABLE position_levels ADD COLUMN unlock_price INT DEFAULT 0 AFTER is_premium");
    echo "Success: Columns added to position_levels table.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
