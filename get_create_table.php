<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();

foreach (['quiz_exams', 'quiz_questions'] as $table) {
    echo "--- CREATE TABLE $table ---\n";
    $res = $db->query("SHOW CREATE TABLE $table")->fetch();
    echo $res['Create Table'] . "\n\n";
}
