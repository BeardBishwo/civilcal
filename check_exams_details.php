<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();

$exams = $db->query("SELECT id, title, category_id, education_level_id, course_id, status FROM quiz_exams WHERE id IN (1, 2, 16)")->fetchAll();

print_r($exams);
