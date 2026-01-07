<?php
require_once 'app/bootstrap.php';
$db = App\Core\Database::getInstance();
$stats = $db->query("SELECT type, count(*) as count FROM quiz_questions GROUP BY type")->fetchAll();
print_r($stats);
echo "\nTheory sub-types:\n";
$theory = $db->query("SELECT theory_type, count(*) as count FROM quiz_questions WHERE type='THEORY' GROUP BY theory_type")->fetchAll();
print_r($theory);
