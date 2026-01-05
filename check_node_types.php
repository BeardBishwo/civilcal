<?php
require_once __DIR__ . '/app/Core/Database.php';
$db = App\Core\Database::getInstance();
$level = 'sub engineer';
$nodes = $db->query("SELECT DISTINCT type FROM syllabus_nodes WHERE level = :level", ['level' => $level])->fetchAll();
echo json_encode($nodes, JSON_PRETTY_PRINT);
