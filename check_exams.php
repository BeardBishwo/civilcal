<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();

$exams = $db->query("SELECT id, title, type, status, (SELECT COUNT(*) FROM quiz_exam_questions WHERE exam_id = quiz_exams.id) as q_count FROM quiz_exams")->fetchAll();

echo "ID | Title | Type | Status | Q Count\n";
echo str_repeat("-", 60) . "\n";
foreach ($exams as $e) {
    echo "{$e['id']} | {$e['title']} | {$e['type']} | {$e['status']} | {$e['q_count']}\n";
}
