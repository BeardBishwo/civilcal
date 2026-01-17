<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

echo "Adding scheduled_at to global_notifications...\n";
try {
    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM global_notifications LIKE 'scheduled_at'");
    if ($stmt->rowCount() == 0) {
        $db->query("ALTER TABLE global_notifications ADD COLUMN scheduled_at DATETIME NULL DEFAULT NULL AFTER expires_at");
        echo "Column 'scheduled_at' added successfully.\n";
    } else {
        echo "Column 'scheduled_at' already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Migration Complete.\n";
