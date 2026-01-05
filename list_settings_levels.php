<?php
require_once __DIR__ . '/app/Core/Database.php';
$db = App\Core\Database::getInstance();
$settings = $db->query("SELECT level FROM syllabus_settings")->fetchAll();
echo json_encode($settings, JSON_PRETTY_PRINT);
