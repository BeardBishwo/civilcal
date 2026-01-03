<?php
/**
 * Migration: Create Syllabus Engine Tables
 * 
 * This migration creates the enhanced syllabus system with:
 * - syllabus_nodes: Recursive tree structure (Papers → Parts → Sections → Units)
 * - exam_blueprints: Exam recipe headers
 * - blueprint_rules: Question distribution rules
 * - question_stream_map: Multi-context question mapping (one question, multiple difficulty levels)
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

    $conn->beginTransaction();

    // 1. Syllabus Nodes (Recursive Tree Structure)
    $sql_syllabus_nodes = "
    CREATE TABLE IF NOT EXISTS `syllabus_nodes` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `parent_id` BIGINT(20) UNSIGNED NULL COMMENT 'NULL for root nodes (Papers)',
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL,
        `type` ENUM('paper', 'part', 'section', 'unit') NOT NULL,
        `description` TEXT NULL,
        `order` INT(11) DEFAULT 0,
        `level` VARCHAR(50) NULL COMMENT 'e.g., Level 4, Level 5, Level 7',
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `parent_id` (`parent_id`),
        KEY `type` (`type`),
        KEY `level` (`level`),
        UNIQUE KEY `unique_node` (`parent_id`, `slug`),
        CONSTRAINT `fk_syllabus_nodes_parent` FOREIGN KEY (`parent_id`) 
            REFERENCES `syllabus_nodes` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_syllabus_nodes);
    echo "✅ Created syllabus_nodes\n";

    // 2. Exam Blueprints (Exam Recipes)
    $sql_exam_blueprints = "
    CREATE TABLE IF NOT EXISTS `exam_blueprints` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL,
        `description` TEXT NULL,
        `level` VARCHAR(50) NULL COMMENT 'Target exam level (Level 4, Level 5, etc.)',
        `total_questions` INT(11) DEFAULT 50,
        `total_marks` INT(11) DEFAULT 100,
        `duration_minutes` INT(11) DEFAULT 60,
        `negative_marking_rate` DECIMAL(5,2) DEFAULT 0.00,
        `wildcard_percentage` DECIMAL(5,2) DEFAULT 10.00 COMMENT 'Out-of-syllabus question %',
        `is_active` TINYINT(1) DEFAULT 1,
        `created_by` INT(11) UNSIGNED NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`),
        KEY `level` (`level`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_exam_blueprints);
    echo "✅ Created exam_blueprints\n";

    // 3. Blueprint Rules (Question Distribution)
    $sql_blueprint_rules = "
    CREATE TABLE IF NOT EXISTS `blueprint_rules` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `blueprint_id` BIGINT(20) UNSIGNED NOT NULL,
        `syllabus_node_id` BIGINT(20) UNSIGNED NOT NULL COMMENT 'Target section/unit',
        `questions_required` INT(11) NOT NULL DEFAULT 10,
        `difficulty_distribution` JSON NULL COMMENT 'e.g., {\"easy\": 3, \"medium\": 5, \"hard\": 2}',
        `order` INT(11) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `blueprint_id` (`blueprint_id`),
        KEY `syllabus_node_id` (`syllabus_node_id`),
        CONSTRAINT `fk_blueprint_rules_blueprint` FOREIGN KEY (`blueprint_id`) 
            REFERENCES `exam_blueprints` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_blueprint_rules_node` FOREIGN KEY (`syllabus_node_id`) 
            REFERENCES `syllabus_nodes` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_blueprint_rules);
    echo "✅ Created blueprint_rules\n";

    // 4. Question Stream Map (Multi-Context Questions)
    $sql_question_stream_map = "
    CREATE TABLE IF NOT EXISTS `question_stream_map` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `question_id` INT(11) UNSIGNED NOT NULL,
        `stream` VARCHAR(100) NOT NULL COMMENT 'e.g., Level 4 Sub-Engineer, Level 7 Officer',
        `difficulty_in_stream` TINYINT(1) DEFAULT 3 COMMENT '1=Easy, 5=Hard within this stream',
        `syllabus_node_id` BIGINT(20) UNSIGNED NULL COMMENT 'Which syllabus unit this belongs to',
        `is_practical` TINYINT(1) DEFAULT 0 COMMENT 'For World Mode filtering',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `question_id` (`question_id`),
        KEY `stream` (`stream`),
        KEY `syllabus_node_id` (`syllabus_node_id`),
        KEY `is_practical` (`is_practical`),
        UNIQUE KEY `unique_question_stream` (`question_id`, `stream`),
        CONSTRAINT `fk_qsm_question` FOREIGN KEY (`question_id`) 
            REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_qsm_syllabus_node` FOREIGN KEY (`syllabus_node_id`) 
            REFERENCES `syllabus_nodes` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_question_stream_map);
    echo "✅ Created question_stream_map\n";

    $conn->commit();
    echo "🎉 Syllabus Engine Migration Completed Successfully!\n";

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo "❌ Error in migration: " . $e->getMessage() . "\n";
    exit(1);
}
?>
