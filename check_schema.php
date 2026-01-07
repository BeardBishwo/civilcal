<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$res = $db->query('DESCRIBE quiz_exams')->fetchAll();
echo "Field | Type\n";
echo str_repeat("-", 30) . "\n";
foreach($res as $r) {
    echo "{$r['Field']} | {$r['Type']}\n";
}
