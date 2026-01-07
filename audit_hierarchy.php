<?php
require_once 'app/bootstrap.php';
$db = App\Core\Database::getInstance();

echo "--- SYLLABUS NODES COLUMNS ---\n";
$cols = $db->query("DESCRIBE syllabus_nodes")->fetchAll();
foreach ($cols as $c) echo "{$c['Field']} ({$c['Type']})\n";

echo "\n--- SYLLABUS NODES SAMPLE ---\n";
$nodes = $db->query("SELECT * FROM syllabus_nodes LIMIT 10")->fetchAll();
foreach ($nodes as $n) {
    echo "ID: {$n['id']}, Title: {$n['title']}, Parent: {$n['parent_id']}\n";
}

echo "\n--- HIERARCHY TEST ---\n";
$test = $db->query("
    SELECT s.id as cat_id, s.title as cat_title, level.title as level_title, course.title as course_title, count(q.id) as q_count
    FROM syllabus_nodes s
    JOIN syllabus_nodes level ON s.parent_id = level.id
    JOIN syllabus_nodes course ON level.parent_id = course.id
    LEFT JOIN quiz_questions q ON s.id = q.category_id
    GROUP BY s.id
    LIMIT 5
")->fetchAll();

foreach ($test as $t) {
    echo "Cat: {$t['cat_title']} (ID: {$t['cat_id']}), Level: {$t['level_title']}, Course: {$t['course_title']}, Questions: {$t['q_count']}\n";
}

echo "\n--- QUESTION DISTRIBUTION ---\n";
$dist = $db->query("SELECT category_id, count(*) as count FROM quiz_questions GROUP BY category_id LIMIT 10")->fetchAll();
foreach ($dist as $d) {
    echo "Category ID: {$d['category_id']}, Count: {$d['count']}\n";
}
