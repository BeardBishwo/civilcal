<?php
require_once 'app/bootstrap.php';
$db = App\Core\Database::getInstance();

echo "--- CHECKING IS_ACTIVE STATUS ---\n";
$nodes = $db->query("SELECT id, title, is_active, parent_id FROM syllabus_nodes")->fetchAll();
$inactive_count = 0;
$active_count = 0;
foreach ($nodes as $n) {
    if ($n['is_active'] == 0) $inactive_count++;
    else $active_count++;
}
echo "Active: $active_count, Inactive: $inactive_count\n";

echo "\n--- SAMPLE INACTIVE NODES ---\n";
$inactive = $db->query("SELECT id, title, parent_id FROM syllabus_nodes WHERE is_active = 0 LIMIT 10")->fetchAll();
foreach ($inactive as $n) {
    echo "ID: {$n['id']}, Title: {$n['title']}, Parent: {$n['parent_id']}\n";
}

echo "\n--- UPDATING ALL NODES TO ACTIVE ---\n";
$db->query("UPDATE syllabus_nodes SET is_active = 1");
echo "All syllabus nodes set to active=1.\n";

echo "\n--- CHECKING QUIZ_QUESTIONS STATUS ---\n";
$q_active = $db->query("UPDATE quiz_questions SET is_active = 1");
echo "All quiz questions set to active=1.\n";
