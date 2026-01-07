<?php
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

$db = Database::getInstance();

$tables = ['quiz_exams', 'quiz_exam_questions', 'quiz_questions', 'syllabus_nodes'];

foreach ($tables as $table) {
    echo "--- SCHEMA FOR $table ---\n";
    $cols = $db->query("SHOW COLUMNS FROM $table")->fetchAll();
    foreach ($cols as $c) {
        echo "{$c['Field']} ({$c['Type']})\n";
    }
    echo "\n";
}
