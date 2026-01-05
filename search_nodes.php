<?php
require_once __DIR__ . '/app/Core/Database.php';
$db = App\Core\Database::getInstance();
$nodes = $db->query("SELECT * FROM syllabus_nodes WHERE level LIKE '%sub engineer%' LIMIT 5")->fetchAll();
echo json_encode($nodes, JSON_PRETTY_PRINT);
