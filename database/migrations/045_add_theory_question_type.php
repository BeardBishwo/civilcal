<?php
/**
 * Migration: Add THEORY question type support
 * Adds theory_type column to distinguish between short (4 marks) and long (8 marks) answers
 */

require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "🚀 Adding THEORY question type support...\n\n";
    
    // 1. Update type ENUM to include THEORY
    echo "1. Updating type ENUM...\n";
    $pdo->exec("
        ALTER TABLE quiz_questions 
        MODIFY COLUMN type ENUM('MCQ','TF','MULTI','ORDER','NUMERICAL','TEXT','THEORY') 
        NOT NULL DEFAULT 'MCQ'
        COMMENT 'Question type'
    ");
    echo "   ✓ Type ENUM updated\n\n";
    
    // 2. Add theory_type column
    echo "2. Adding theory_type column...\n";
    $columns = $db->query("SHOW COLUMNS FROM quiz_questions LIKE 'theory_type'")->fetchAll();
    if (empty($columns)) {
        $pdo->exec("
            ALTER TABLE quiz_questions 
            ADD COLUMN theory_type ENUM('short','long') NULL 
            COMMENT 'short=4 marks (100-150 words), long=8 marks (300-500 words)' 
            AFTER type
        ");
        echo "   ✓ theory_type column added\n\n";
    } else {
        echo "   ℹ theory_type column already exists\n\n";
    }
    
    // 3. Create index for efficient filtering
    echo "3. Creating index...\n";
    try {
        $pdo->exec("CREATE INDEX idx_theory_type ON quiz_questions(type, theory_type)");
        echo "   ✓ Index created\n\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "   ℹ Index already exists\n\n";
        } else {
            throw $e;
        }
    }
    
    // 4. Auto-populate theory_type for existing THEORY questions based on marks
    echo "4. Auto-populating theory_type for existing questions...\n";
    $stmt = $pdo->prepare("
        UPDATE quiz_questions 
        SET theory_type = CASE 
            WHEN default_marks <= 4 THEN 'short'
            ELSE 'long'
        END
        WHERE type = 'THEORY' AND theory_type IS NULL
    ");
    $stmt->execute();
    $result = $stmt->rowCount();
    echo "   ✓ Updated {$result} existing THEORY questions\n\n";
    
    // 5. Update question_import_staging table
    echo "5. Updating staging table...\n";
    $stagingColumns = $db->query("SHOW COLUMNS FROM question_import_staging LIKE 'theory_type'")->fetchAll();
    if (empty($stagingColumns)) {
        $pdo->exec("
            ALTER TABLE question_import_staging 
            ADD COLUMN theory_type ENUM('short','long') NULL 
            COMMENT 'Theory question sub-type' 
            AFTER type
        ");
        echo "   ✓ Staging table updated\n\n";
    } else {
        echo "   ℹ Staging table already has theory_type column\n\n";
    }
    
    // 6. Show statistics
    echo "6. Statistics:\n";
    $stats = $db->query("
        SELECT 
            COUNT(*) as total_theory,
            SUM(CASE WHEN theory_type = 'short' THEN 1 ELSE 0 END) as short_count,
            SUM(CASE WHEN theory_type = 'long' THEN 1 ELSE 0 END) as long_count,
            SUM(CASE WHEN theory_type IS NULL THEN 1 ELSE 0 END) as unclassified
        FROM quiz_questions 
        WHERE type = 'THEORY'
    ")->fetch();
    
    echo "   Total THEORY questions: {$stats['total_theory']}\n";
    echo "   - Short Answer (4 marks): {$stats['short_count']}\n";
    echo "   - Long Answer (8 marks): {$stats['long_count']}\n";
    echo "   - Unclassified: {$stats['unclassified']}\n\n";
    
    echo "✅ Migration completed successfully!\n";
    echo "\nNext steps:\n";
    echo "- Update question creation UI to include Theory tab\n";
    echo "- Add filtering options for short/long theory questions\n";
    echo "- Test import with theory questions\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
