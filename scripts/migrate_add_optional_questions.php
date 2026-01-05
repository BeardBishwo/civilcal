<?php
/**
 * Migration: Add question_optional and question_marks_each columns to syllabus_nodes
 * Run this file once to update the database schema
 */

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    echo "Starting migration: Add question_optional and question_marks_each columns...\n";
    
    // Check if columns exist
    $columns = $db->query("SHOW COLUMNS FROM syllabus_nodes LIKE 'question_optional'")->fetchAll();
    
    if (empty($columns)) {
        echo "Adding question_optional column...\n";
        $db->query("ALTER TABLE syllabus_nodes ADD COLUMN question_optional INT DEFAULT 0 AFTER question_count");
        echo "✓ question_optional column added successfully\n";
    } else {
        echo "✓ question_optional column already exists\n";
    }
    
    // Check if question_marks_each exists
    $columns2 = $db->query("SHOW COLUMNS FROM syllabus_nodes LIKE 'question_marks_each'")->fetchAll();
    
    if (empty($columns2)) {
        echo "Adding question_marks_each column...\n";
        $db->query("ALTER TABLE syllabus_nodes ADD COLUMN question_marks_each DECIMAL(5,2) DEFAULT 0.00 AFTER question_optional");
        echo "✓ question_marks_each column added successfully\n";
    } else {
        echo "✓ question_marks_each column already exists\n";
    }
    
    echo "\n✅ Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
