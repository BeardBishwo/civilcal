<?php
require_once __DIR__ . '/app/Core/Database.php';
$db = App\Core\Database::getInstance();
$levels = $db->query("SELECT DISTINCT level FROM syllabus_nodes")->fetchAll();
echo json_encode($levels, JSON_PRETTY_PRINT);
