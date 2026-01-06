<?php
/**
 * Enterprise Exam System Migration
 * 
 * Adds question_type and difficulty_constraint columns to syllabus_nodes
 * Updates question_stream_map with priority and is_primary columns
 * 
 * Run: php scripts/migrate_enterprise_exam_system.php
 */

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

echo "=== Enterprise Exam System Migration ===\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    // Start transaction
    $pdo->beginTransaction();
    
    echo "[1/4] Checking syllabus_nodes table...\n";
    
    // Check if columns already exist
    $stmt = $pdo->query("SHOW COLUMNS FROM syllabus_nodes LIKE 'question_type'");
    if ($stmt->rowCount() > 0) {
        echo "   ⚠ Column 'question_type' already exists. Skipping.\n";
    } else {
        echo "   → Adding 'question_type' column...\n";
        $pdo->exec("
            ALTER TABLE syllabus_nodes 
            ADD COLUMN question_type VARCHAR(20) DEFAULT 'any' 
            COMMENT 'Allowed question types: mcq_single, true_false, multi_select, subjective, any'
        ");
        echo "   ✓ Added 'question_type' column\n";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM syllabus_nodes LIKE 'difficulty_constraint'");
    if ($stmt->rowCount() > 0) {
        echo "   ⚠ Column 'difficulty_constraint' already exists. Skipping.\n";
    } else {
        echo "   → Adding 'difficulty_constraint' column...\n";
        $pdo->exec("
            ALTER TABLE syllabus_nodes 
            ADD COLUMN difficulty_constraint VARCHAR(20) DEFAULT 'any'
            COMMENT 'Difficulty constraint: easy, medium, hard, mixed, any'
        ");
        echo "   ✓ Added 'difficulty_constraint' column\n";
    }
    
    echo "\n[2/4] Checking question_stream_map table...\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM question_stream_map LIKE 'priority'");
    if ($stmt->rowCount() > 0) {
        echo "   ⚠ Column 'priority' already exists. Skipping.\n";
    } else {
        echo "   → Adding 'priority' column...\n";
        $pdo->exec("
            ALTER TABLE question_stream_map 
            ADD COLUMN priority INT DEFAULT 1
            COMMENT 'Priority for weighted selection when multiple mappings exist'
        ");
        echo "   ✓ Added 'priority' column\n";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM question_stream_map LIKE 'is_primary'");
    if ($stmt->rowCount() > 0) {
        echo "   ⚠ Column 'is_primary' already exists. Skipping.\n";
    } else {
        echo "   → Adding 'is_primary' column...\n";
        $pdo->exec("
            ALTER TABLE question_stream_map 
            ADD COLUMN is_primary TINYINT(1) DEFAULT 0
            COMMENT 'Marks the primary syllabus association'
        ");
        echo "   ✓ Added 'is_primary' column\n";
    }
    
    echo "\n[3/4] Adding indexes for performance...\n";
    
    // Check if index exists before creating
    $stmt = $pdo->query("SHOW INDEX FROM syllabus_nodes WHERE Key_name = 'idx_question_type'");
    if ($stmt->rowCount() > 0) {
        echo "   ⚠ Index 'idx_question_type' already exists. Skipping.\n";
    } else {
        $pdo->exec("CREATE INDEX idx_question_type ON syllabus_nodes(question_type)");
        echo "   ✓ Created index on question_type\n";
    }
    
    $stmt = $pdo->query("SHOW INDEX FROM syllabus_nodes WHERE Key_name = 'idx_difficulty'");
    if ($stmt->rowCount() > 0) {
        echo "   ⚠ Index 'idx_difficulty' already exists. Skipping.\n";
    } else {
        $pdo->exec("CREATE INDEX idx_difficulty ON syllabus_nodes(difficulty_constraint)");
        echo "   ✓ Created index on difficulty_constraint\n";
    }
    
    $stmt = $pdo->query("SHOW INDEX FROM question_stream_map WHERE Key_name = 'idx_priority'");
    if ($stmt->rowCount() > 0) {
        echo "   ⚠ Index 'idx_priority' already exists. Skipping.\n";
    } else {
        $pdo->exec("CREATE INDEX idx_priority ON question_stream_map(priority)");
        echo "   ✓ Created index on priority\n";
    }
    
    echo "\n[4/4] Setting default values for existing records...\n";
    
    $updated = $pdo->exec("
        UPDATE syllabus_nodes 
        SET question_type = 'any', difficulty_constraint = 'any' 
        WHERE question_type IS NULL OR difficulty_constraint IS NULL
    ");
    echo "   ✓ Updated $updated existing records with default values\n";
    
    // Commit transaction only if we started one
    if ($pdo->inTransaction()) {
        $pdo->commit();
    }
    
    echo "\n✅ Migration completed successfully!\n\n";
    echo "Summary:\n";
    echo "  - Added 'question_type' and 'difficulty_constraint' to syllabus_nodes\n";
    echo "  - Added 'priority' and 'is_primary' to question_stream_map\n";
    echo "  - Created performance indexes\n";
    echo "  - Set default values for existing records\n\n";
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "   All changes have been rolled back.\n\n";
    exit(1);
}
