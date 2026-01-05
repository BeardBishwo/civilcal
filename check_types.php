<?php
require_once __DIR__ . '/app/Core/Database.php';
$db = App\Core\Database::getInstance();
$types = $db->query("SELECT DISTINCT type FROM syllabus_nodes")->fetchAll();
echo json_encode($types, JSON_PRETTY_PRINT);
