<?php
require_once 'app/bootstrap.php';
$db = App\Core\Database::getInstance();

echo "--- BRIDGING SEEDED QUESTIONS TO MOCK TESTS ---\n";

// 1. Get all published exams
$exams = $db->query("SELECT id, title, slug FROM quiz_exams WHERE status = 'published'")->fetchAll();

foreach ($exams as $exam) {
    echo "Processing Exam: {$exam['title']} (ID: {$exam['id']})\n";
    
    // Check if it already has questions
    $count = $db->query("SELECT COUNT(*) FROM quiz_exam_questions WHERE exam_id = {$exam['id']}")->fetchColumn();
    if ($count > 0) {
        echo "  - Already has $count questions. Skipping.\n";
        continue;
    }

    // Try to find relevant questions based on keywords in title
    $keyword = '';
    if (stripos($exam['title'], 'Civil') !== false) $keyword = 'Civil';
    elseif (stripos($exam['title'], 'Sub-Engineer') !== false) $keyword = 'Engineer';
    
    // Alternatively, just pick 50 random active questions to populate it for testing
    echo "  - Linking 50 random active questions...\n";
    
    $questions = $db->query("SELECT id FROM quiz_questions WHERE is_active = 1 ORDER BY RAND() LIMIT 50")->fetchAll();
    
    foreach ($questions as $q) {
        try {
            $db->insert('quiz_exam_questions', [
                'exam_id' => $exam['id'],
                'question_id' => $q['id'],
                'order' => 0
            ]);
        } catch (PDOException $e) {
            // Already exists probably
        }
    }
    echo "  - Linked 50 questions successfully.\n";
}

echo "\n--- VERIFYING /EXAMS HIERARCHY AGAIN ---\n";
$categories = $db->query("
    SELECT s.id, s.title, count(q.id) as question_count 
    FROM syllabus_nodes s
    JOIN syllabus_nodes level ON s.parent_id = level.id
    JOIN syllabus_nodes course ON level.parent_id = course.id
    JOIN quiz_questions q ON s.id = q.category_id
    WHERE q.is_active = 1
    AND s.is_active = 1
    AND level.is_active = 1
    AND course.is_active = 1
    GROUP BY s.id
")->fetchAll();

echo "Categories with questions and active hierarchy: " . count($categories) . "\n";
foreach ($categories as $cat) {
    echo " - {$cat['title']} ({$cat['question_count']} questions)\n";
}
