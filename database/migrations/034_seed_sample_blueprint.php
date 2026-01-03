<?php
/**
 * Seed Sample Blueprint: Level 5 First Paper
 * 
 * Creates a complete blueprint for Level 5 Civil Engineering First Paper
 * with proper question distribution rules
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

    // Create Blueprint
    $conn->exec("INSERT INTO exam_blueprints 
        (title, slug, description, level, total_questions, total_marks, duration_minutes, negative_marking_rate, wildcard_percentage, is_active) 
        VALUES 
        ('Level 5 Civil Engineering - First Paper', 'level-5-civil-first-paper', 
        'Standard blueprint for Level 5 First Paper with 10 GK + 10 Management + 30 Technical questions', 
        'Level 5', 50, 100, 90, 0.25, 10.00, 1)");
    
    $blueprintId = $conn->lastInsertId();
    echo "✅ Created Blueprint (ID: $blueprintId)\n";

    // Get syllabus node IDs
    $getNodeId = function($slug) use ($conn) {
        $stmt = $conn->prepare("SELECT id FROM syllabus_nodes WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetchColumn();
    };

    // Rule 1: Section A - General Knowledge (10 questions)
    $sectionAId = $getNodeId('section-a-gk');
    if ($sectionAId) {
        $conn->exec("INSERT INTO blueprint_rules 
            (blueprint_id, syllabus_node_id, questions_required, difficulty_distribution, `order`) 
            VALUES 
            ($blueprintId, $sectionAId, 10, '{\"easy\": 4, \"medium\": 4, \"hard\": 2}', 1)");
        echo "✅ Added Rule 1: GK (10 questions)\n";
    }

    // Rule 2: Section B - Management (10 questions)
    $sectionBId = $getNodeId('section-b-management');
    if ($sectionBId) {
        $conn->exec("INSERT INTO blueprint_rules 
            (blueprint_id, syllabus_node_id, questions_required, difficulty_distribution, `order`) 
            VALUES 
            ($blueprintId, $sectionBId, 10, '{\"easy\": 3, \"medium\": 5, \"hard\": 2}', 2)");
        echo "✅ Added Rule 2: Management (10 questions)\n";
    }

    // Rule 3: Part II - Technical (30 questions from all 5 subjects)
    $part2Id = $getNodeId('part-2-technical');
    if ($part2Id) {
        $conn->exec("INSERT INTO blueprint_rules 
            (blueprint_id, syllabus_node_id, questions_required, difficulty_distribution, `order`) 
            VALUES 
            ($blueprintId, $part2Id, 30, '{\"easy\": 8, \"medium\": 15, \"hard\": 7}', 3)");
        echo "✅ Added Rule 3: Technical (30 questions)\n";
    }

    $conn->commit();
    echo "\n🎉 Sample Blueprint Created Successfully!\n";
    echo "📊 Summary:\n";
    echo "   - Blueprint: Level 5 Civil Engineering - First Paper\n";
    echo "   - Total Questions: 50 (45 from syllabus + 5 wildcard)\n";
    echo "   - Distribution: 10 GK + 10 Mgmt + 30 Technical\n";
    echo "   - Difficulty: Balanced across easy/medium/hard\n";
    echo "   - Wildcard: 10% (5 questions) from practical pool\n";

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
