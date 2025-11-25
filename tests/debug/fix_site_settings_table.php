<?php
require 'vendor/autoload.php';

use App\Core\Database;

$db = Database::getInstance();

try {
    echo "Adding missing columns to site_settings table...\n\n";

    // Check if columns exist first
    $columns = $db->query("SHOW COLUMNS FROM site_settings")->fetchAll(PDO::FETCH_ASSOC);
    $columnNames = array_column($columns, 'Field');

    if (!in_array('setting_group', $columnNames)) {
        $db->query("ALTER TABLE site_settings ADD COLUMN setting_group VARCHAR(50) DEFAULT 'general' AFTER setting_value");
        echo "✅ Added setting_group column\n";
    } else {
        echo "ℹ️  setting_group column already exists\n";
    }

    if (!in_array('created_at', $columnNames)) {
        $db->query("ALTER TABLE site_settings ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER setting_group");
        echo "✅ Added created_at column\n";
    } else {
        echo "ℹ️  created_at column already exists\n";
    }

    // Update existing rows
    $db->query("UPDATE site_settings SET setting_group = 'general' WHERE setting_group IS NULL OR setting_group = ''");
    echo "✅ Updated existing rows\n";

    echo "\n✅ Table structure updated successfully!\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
