<?php
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

$db = Database::getInstance();

echo "--- TESTING EXAMCONTROLLER QUERY ---\n";
$categories = $db->query("
    SELECT s.*, count(q.id) as question_count 
    FROM syllabus_nodes s
    JOIN syllabus_nodes level ON s.parent_id = level.id
    JOIN syllabus_nodes course ON level.parent_id = course.id
    JOIN quiz_questions q ON s.id = q.category_id
    WHERE q.is_active = 1
    AND s.is_active = 1
    AND level.is_active = 1
    AND course.is_active = 1
    GROUP BY s.id
    ORDER BY s.title
")->fetchAll();

echo "Total Categories Found: " . count($categories) . "\n\n";

if (count($categories) > 0) {
    foreach ($categories as $cat) {
        echo "ID: {$cat['id']} | Title: {$cat['title']} | Questions: {$cat['question_count']}\n";
    }
} else {
    echo "NO CATEGORIES FOUND.\n";
    
    echo "\n--- CHECKING INDIVIDUAL JOINS ---\n";
    
    $nodes = $db->query("SELECT count(*) as count FROM syllabus_nodes")->fetch();
    echo "Total Syllabus Nodes: {$nodes['count']}\n";
    
    $questions = $db->query("SELECT count(*) as count FROM quiz_questions WHERE is_active = 1")->fetch();
    echo "Total Active Questions: {$questions['count']}\n";
    
    // Check if any question has a category_id
    $linked_q = $db->query("SELECT count(*) as count FROM quiz_questions WHERE category_id IS NOT NULL AND category_id != 0")->fetch();
    echo "Questions with category_id: {$linked_q['count']}\n";
    
    // Check hierarchy for a sample category
    $sample_q = $db->query("SELECT category_id FROM quiz_questions WHERE is_active = 1 LIMIT 1")->fetch();
    if ($sample_q) {
        $cat_id = $sample_q['category_id'];
        echo "Sample Category ID from question: $cat_id\n";
        
        $hierarchy = $db->query("
            SELECT s.title as cat, level.title as level, course.title as course
            FROM syllabus_nodes s
            LEFT JOIN syllabus_nodes level ON s.parent_id = level.id
            LEFT JOIN syllabus_nodes course ON level.parent_id = course.id
            WHERE s.id = :id
        ", ['id' => $cat_id])->fetch();
        
        if ($hierarchy) {
            echo "Hierarchy: {$hierarchy['course']} -> {$hierarchy['level']} -> {$hierarchy['cat']}\n";
        } else {
            echo "Hierarchy NOT FOUND for Cat ID $cat_id\n";
        }
    }
}
