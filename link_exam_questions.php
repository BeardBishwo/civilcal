<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();

echo "--- LINKING EXAM QUESTIONS ---\n";

// 1. Get all published mock tests
$exams = $db->query("SELECT * FROM quiz_exams WHERE status = 'published' OR status = 'approved'")->fetchAll();
echo "Found " . count($exams) . " exams/mock tests.\n";

foreach ($exams as $exam) {
    echo "Processing Exam: {$exam['title']} (ID: {$exam['id']})\n";
    
    // Check current count
    $current = $db->query("SELECT COUNT(*) FROM quiz_exam_questions WHERE exam_id = :eid", ['eid' => $exam['id']])->fetchColumn();
    if ($current > 0) {
        echo " - Already has $current questions. Skipping.\n";
        continue;
    }
    
    // Find questions for this exam
    // Strategy: Match by category_id first, then education_level_id if category is null
    $questions = [];
    if (!empty($exam['category_id'])) {
        $questions = $db->query("SELECT id FROM quiz_questions WHERE category_id = :cid AND is_active = 1 LIMIT 50", ['cid' => $exam['category_id']])->fetchAll();
    } elseif (!empty($exam['education_level_id'])) {
        $questions = $db->query("SELECT id FROM quiz_questions WHERE education_level_id = :elid AND is_active = 1 LIMIT 50", ['elid' => $exam['education_level_id']])->fetchAll();
    }
    
    if (count($questions) > 0) {
        $linked = 0;
        foreach ($questions as $q) {
            $db->insert('quiz_exam_questions', [
                'exam_id' => $exam['id'],
                'question_id' => $q['id'],
                'order' => $linked + 1
            ]);
            $linked++;
        }
        echo " - Linked $linked questions.\n";
    } else {
        echo " - NO QUESTIONS FOUND for this exam criteria.\n";
    }
}

echo "\n--- SYNCING SYLLABUS NODE COUNTS ---\n";
// Update syllabus_nodes.question_count based on quiz_questions
$db->query("
    UPDATE syllabus_nodes s 
    SET question_count = (SELECT COUNT(*) FROM quiz_questions q WHERE q.category_id = s.id AND q.is_active = 1)
    WHERE type = 'TOPIC' OR type = 'SUBJECT'
");
echo "Syllabus node counts updated.\n";
