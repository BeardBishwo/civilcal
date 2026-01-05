<?php
require_once __DIR__ . '/app/Core/Database.php';
$db = App\Core\Database::getInstance();
$nodes = $db->query("SELECT id, title, level, type FROM syllabus_nodes ORDER BY id DESC LIMIT 50")->fetchAll();
echo json_encode($nodes, JSON_PRETTY_PRINT);
