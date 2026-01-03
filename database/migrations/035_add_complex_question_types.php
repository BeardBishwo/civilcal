<?php
/**
 * Migration: Add Complex Question Types (Brainstorming Engine)
 * 
 * Objectives:
 * 1. Upgrade `type` column to support MULTI and ORDER (Standardizing ENUMs).
 * 2. Add `correct_answer_json` for storing array-based answers.
 */

if (file_exists(dirname(__DIR__, 3) . '/app/bootstrap.php')) {
    require_once dirname(__DIR__, 3) . '/app/bootstrap.php';
} else {
    require_once dirname(__DIR__) . '/../app/bootstrap.php';
}

use App\Core\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getPdo();

    echo "🔄 Starting Brainstorming Engine Migration...\n";

    // 1. Add correct_answer_json column first (Safe operation)
    // Checking if exists first would be better but direct ALTER is okay in dev if handled.
    // We'll use a try-catch block for column addition or check information_schema, 
    // but simpler to just run and assume valid state or ignore 'Duplicate column' error manually if strict.
    // For now, let's allow it to fail if exists or use a check.
    
    $checkCol = $conn->query("SHOW COLUMNS FROM `quiz_questions` LIKE 'correct_answer_json'");
    if (!$checkCol->fetch()) {
        $sqlAdd = "ALTER TABLE `quiz_questions` ADD COLUMN `correct_answer_json` JSON NULL AFTER `options`";
        $conn->exec($sqlAdd);
        echo "✅ Added column: correct_answer_json\n";
    } else {
        echo "ℹ️ Column correct_answer_json already exists.\n";
    }

    // 2. Normalize 'type' column
    // First, convert to VARCHAR to allow manipulation without constraint errors
    $conn->exec("ALTER TABLE `quiz_questions` MODIFY COLUMN `type` VARCHAR(50) NOT NULL DEFAULT 'MCQ'");
    echo "✅ Converted `type` to VARCHAR for data migration.\n";

    // Migrate old values
    $countMCQ = $conn->exec("UPDATE `quiz_questions` SET `type` = 'MCQ' WHERE `type` = 'mcq_single'");
    $countMulti = $conn->exec("UPDATE `quiz_questions` SET `type` = 'MULTI' WHERE `type` = 'mcq_multi'");
    $countTF = $conn->exec("UPDATE `quiz_questions` SET `type` = 'TF' WHERE `type` = 'true_false'");
    // Note: 'numerical', 'text' might still exist. If we want to keep them, we should allow them in the new ENUM.
    // The prompt requested ENUM('MCQ', 'TF', 'MULTI', 'ORDER'). 
    // If we have 'numerical' or 'text' data, it would violate the new ENUM if we enforce strictness.
    // Let's check if we have any other types.
    
    // For safety, let's keep 'NUMERICAL' and 'TEXT' in the Enum as well to avoid breaking legacy features if they exist.
    // Updating them to Uppercase standard.
    $conn->exec("UPDATE `quiz_questions` SET `type` = 'NUMERICAL' WHERE `type` = 'numerical'");
    $conn->exec("UPDATE `quiz_questions` SET `type` = 'TEXT' WHERE `type` = 'text'");

    echo "✅ Data Migrated: " . ($countMCQ + $countMulti + $countTF) . " rows updated.\n";

    // 3. Apply New ENUM Definition
    // Including NUMERICAL and TEXT for backward compatibility/future proofing as they were in original schema.
    $sqlEnum = "ALTER TABLE `quiz_questions` MODIFY COLUMN `type` ENUM('MCQ', 'TF', 'MULTI', 'ORDER', 'NUMERICAL', 'TEXT') NOT NULL DEFAULT 'MCQ'";
    $conn->exec($sqlEnum);
    echo "✅ Applied new ENUM types.\n";

    echo "🎉 Brainstorming Engine Migration Complete!\n";

} catch (Exception $e) {
    echo "❌ Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
