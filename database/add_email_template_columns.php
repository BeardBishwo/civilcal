<?php

/**
 * Add missing columns to email_templates table
 */

require_once __DIR__ . '/../app/Config/config.php';
require_once __DIR__ . '/../app/Config/db.php';
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = App\Core\Database::getInstance();
    $pdo = $db->getPdo();

    echo "Adding columns to email_templates table...\n";

    // Add description column
    try {
        $pdo->exec("ALTER TABLE email_templates ADD COLUMN description VARCHAR(1000) NULL AFTER category");
        echo "✅ Added 'description' column\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "⚠️  Column 'description' already exists\n";
        } else {
            throw $e;
        }
    }

    // Add variables column
    try {
        $pdo->exec("ALTER TABLE email_templates ADD COLUMN variables JSON NULL AFTER description");
        echo "✅ Added 'variables' column\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "⚠️  Column 'variables' already exists\n";
        } else {
            throw $e;
        }
    }

    echo "\n✅ Schema update completed successfully!\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
