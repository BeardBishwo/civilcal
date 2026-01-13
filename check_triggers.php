<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();
$triggers = $db->query("SHOW TRIGGERS")->fetchAll(PDO::FETCH_ASSOC);
$filtered = array_filter($triggers, function ($t) {
    return $t['Table'] === 'syllabus_nodes';
});
file_put_contents('syllabus_nodes_triggers.txt', print_r($filtered, true));
