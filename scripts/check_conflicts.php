<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();

echo "=== POSITION LEVELS TABLE ===\n";
$posLevels = $db->query('SELECT * FROM position_levels')->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($posLevels) . "\n";
if (count($posLevels) > 0) {
    print_r($posLevels);
}

echo "\n=== SYLLABUS NODES (Course/Education/Position) ===\n";
$nodes = $db->query('SELECT id, title, type, parent_id FROM syllabus_nodes WHERE type IN ("course", "education_level", "position") ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
foreach ($nodes as $node) {
    echo "[{$node['id']}] {$node['title']} (Type: {$node['type']}) -> Parent: {$node['parent_id']}\n";
}

echo "\n=== POTENTIAL CONFLICTS ===\n";
// Check if there are any position_levels that might conflict with syllabus position nodes
$syllabusPositions = $db->query('SELECT title FROM syllabus_nodes WHERE type = "position"')->fetchAll(PDO::FETCH_COLUMN);
echo "Syllabus Positions: " . implode(', ', $syllabusPositions) . "\n";

if (count($posLevels) > 0) {
    echo "⚠️ WARNING: position_levels table has data that might conflict with syllabus structure\n";
} else {
    echo "✅ No conflicts: position_levels table is empty\n";
}
