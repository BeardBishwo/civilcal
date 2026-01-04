<?php
/**
 * Migration: Add Complex Negative Marking Fields
 * 
 * Adds support for:
 * - syllabus_settings (Store level-wide configs)
 * - complex negative marking fields in exams and blueprints
 */

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getPdo();

    $conn->beginTransaction();

    // 1. Create syllabus_settings table
    $sql_settings = "
    CREATE TABLE IF NOT EXISTS `syllabus_settings` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `level` VARCHAR(100) NOT NULL,
        `settings` JSON NOT NULL,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_level` (`level`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_settings);
    echo "✅ Created syllabus_settings table\n";

    // 2. Add columns to quiz_exams
    $sql_exams = "
    ALTER TABLE `quiz_exams` 
    ADD COLUMN `negative_marking_unit` ENUM('percent', 'number') DEFAULT 'percent' AFTER `negative_marking_rate`,
    ADD COLUMN `negative_marking_basis` ENUM('per_q', 'fixed') DEFAULT 'per_q' AFTER `negative_marking_unit`;
    ";
    // Check if columns exist first for idempotency
    $check_exams = $conn->query("SHOW COLUMNS FROM `quiz_exams` LIKE 'negative_marking_unit'")->fetch();
    if (!$check_exams) {
        $conn->exec($sql_exams);
        echo "✅ Added complex NEG columns to quiz_exams\n";
    }

    // 3. Add columns to exam_blueprints
    $sql_blueprints = "
    ALTER TABLE `exam_blueprints` 
    ADD COLUMN `negative_marking_unit` ENUM('percent', 'number') DEFAULT 'percent' AFTER `negative_marking_rate`,
    ADD COLUMN `negative_marking_basis` ENUM('per_q', 'fixed') DEFAULT 'per_q' AFTER `negative_marking_unit`;
    ";
    $check_blueprints = $conn->query("SHOW COLUMNS FROM `exam_blueprints` LIKE 'negative_marking_unit'")->fetch();
    if (!$check_blueprints) {
        $conn->exec($sql_blueprints);
        echo "✅ Added complex NEG columns to exam_blueprints\n";
    }

    $conn->commit();
    echo "🎉 Migration 033 Completed Successfully!\n";

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
