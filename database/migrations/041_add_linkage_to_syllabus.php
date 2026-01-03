<?php
/**
 * Migration: Add Linkage and Weightage to Syllabus
 * 
 * Adds:
 * - linked_category_id: Link to Stream
 * - linked_subject_id: Link to Subject
 * - linked_topic_id: Link to Topic
 * - questions_weight: Number of questions for this node
 */

if (file_exists(dirname(__DIR__, 3) . '/app/bootstrap.php')) {
    require_once dirname(__DIR__, 3) . '/app/bootstrap.php';
} else {
    require_once dirname(__DIR__) . '/../app/bootstrap.php';
}

use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();

    echo "🚀 Adding Linkage Columns to 'syllabus_nodes'...\n";

    // Add columns if not exist
    $cols = [
        "ALTER TABLE syllabus_nodes ADD COLUMN linked_category_id INT(11) UNSIGNED NULL AFTER level",
        "ALTER TABLE syllabus_nodes ADD COLUMN linked_subject_id INT(11) UNSIGNED NULL AFTER linked_category_id",
        "ALTER TABLE syllabus_nodes ADD COLUMN linked_topic_id INT(11) UNSIGNED NULL AFTER linked_subject_id",
        "ALTER TABLE syllabus_nodes ADD COLUMN questions_weight INT DEFAULT 0 AFTER linked_topic_id"
    ];

    foreach ($cols as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            echo "ℹ️ Column might already exist: " . $e->getMessage() . "\n";
        }
    }

    // Add Indexes
    try { $pdo->exec("ALTER TABLE syllabus_nodes ADD INDEX idx_linked_cat (linked_category_id)"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE syllabus_nodes ADD INDEX idx_linked_sub (linked_subject_id)"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE syllabus_nodes ADD INDEX idx_linked_top (linked_topic_id)"); } catch (Exception $e) {}

    echo "✅ Schema updated for Linkage and Weightage.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
