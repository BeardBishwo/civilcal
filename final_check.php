<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();

echo "--- PORTAL EXAMS (PUBLISHED) ---\n";
$exams = $db->query("SELECT id, title, status, (SELECT COUNT(*) FROM quiz_exam_questions WHERE exam_id = quiz_exams.id) as q_count FROM quiz_exams WHERE status = 'published'")->fetchAll();
foreach ($exams as $e) {
    echo "ID: {$e['id']}, Title: {$e['title']}, Q Count: {$e['q_count']}\n";
}

echo "\n--- SYLLABUS HUB (TOP LEVEL) ---\n";
$cats = $db->query("SELECT id, title FROM syllabus_nodes WHERE parent_id IS NULL AND is_active = 1")->fetchAll();
foreach ($cats as $c) {
    $subCount = $db->query("SELECT COUNT(*) FROM syllabus_nodes WHERE parent_id = :id", ['id' => $c['id']])->fetchColumn();
    echo "ID: {$c['id']}, Title: {$c['title']}, Children: $subCount\n";
}

echo "\n--- SUB-ENGINEER CHECK ---\n";
$sub = $db->query("SELECT * FROM syllabus_nodes WHERE title LIKE '%Sub-Engineer%'")->fetch();
if ($sub) {
    echo "Sub-Engineer found (ID: {$sub['id']}, Parent: {$sub['parent_id']}, Active: {$sub['is_active']})\n";
    // Get parent name
    $parent = $db->query("SELECT title FROM syllabus_nodes WHERE id = :id", ['id' => $sub['parent_id']])->fetchColumn();
    echo "Parent Title: $parent\n";
} else {
    echo "Sub-Engineer NOT FOUND in syllabus_nodes\n";
}
