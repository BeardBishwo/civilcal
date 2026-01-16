<?php

/**
 * Migration: Add Foreign Key Constraint to Word Bank Table
 *
 * Objectives:
 * 1. Ensure data integrity by linking category_id to syllabus_nodes.id
 * 2. Prevent orphaned records
 */

if (file_exists(dirname(__DIR__, 2) . '/app/bootstrap.php')) {
    require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
} else {
    require_once dirname(__DIR__) . '/../app/bootstrap.php';
}

use App\Core\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getPdo();

    echo "🔄 Updating `word_bank.category_id` column type...\n";

    // First, change column type to match syllabus_nodes.id (bigint unsigned)
    $sql1 = "ALTER TABLE `word_bank` MODIFY COLUMN `category_id` BIGINT UNSIGNED NULL;";
    $conn->exec($sql1);
    echo "✅ Column type updated.\n";

    echo "🔄 Adding foreign key constraint to `word_bank` table...\n";

    // Add foreign key constraint
    $sql2 = "ALTER TABLE `word_bank`
            ADD CONSTRAINT `fk_word_bank_category_id`
            FOREIGN KEY (`category_id`)
            REFERENCES `syllabus_nodes`(`id`)
            ON DELETE SET NULL
            ON UPDATE CASCADE;";

    $conn->exec($sql2);
    echo "✅ Foreign key constraint added to `word_bank.category_id`.\n";

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "✅ Migration completed successfully.\n";