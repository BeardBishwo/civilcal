<?php
/**
 * Migration: Add contest_id to staging for direct injection
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

    echo "🔄 Adding contest_id to Staging Table...\n";

    $checkCol = $conn->query("SHOW COLUMNS FROM `question_import_staging` LIKE 'contest_id'");
    if (!$checkCol->fetch()) {
        $conn->exec("ALTER TABLE `question_import_staging` ADD COLUMN `contest_id` BIGINT UNSIGNED NULL AFTER `batch_id`");
        echo "✅ Added column: contest_id\n";
    }

    echo "🎉 Staging Table Updated!\n";

} catch (Exception $e) {
    echo "❌ Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
