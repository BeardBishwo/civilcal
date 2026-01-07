<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();

foreach (['quiz_exams', 'quiz_questions'] as $table) {
    echo "--- $table ---\n";
    $cols = $db->query("DESCRIBE $table")->fetchAll();
    foreach ($cols as $c) {
        echo "{$c['Field']} ({$c['Type']})\n";
    }
}
