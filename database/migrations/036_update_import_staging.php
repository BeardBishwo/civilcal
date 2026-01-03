<?php
/**
 * Migration: Update Staging Table for Brainstorming Engine
 * 
 * Objectives:
 * 1. Add `type` and `correct_answer_json` to `question_import_staging`.
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

    echo "🔄 Updating Staging Table...\n";

    // 1. Add correct_answer_json
    $checkCol = $conn->query("SHOW COLUMNS FROM `question_import_staging` LIKE 'correct_answer_json'");
    if (!$checkCol->fetch()) {
        $conn->exec("ALTER TABLE `question_import_staging` ADD COLUMN `correct_answer_json` JSON NULL AFTER `correct_answer`");
        echo "✅ Added column: correct_answer_json\n";
    }

    // 2. Add type
    $checkType = $conn->query("SHOW COLUMNS FROM `question_import_staging` LIKE 'type'");
    if (!$checkType->fetch()) {
        $conn->exec("ALTER TABLE `question_import_staging` ADD COLUMN `type` VARCHAR(50) DEFAULT 'MCQ' AFTER `question_text`");
        echo "✅ Added column: type\n";
    }

    echo "🎉 Staging Table Updated!\n";

} catch (Exception $e) {
    echo "❌ Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
