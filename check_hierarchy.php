<?php
require_once 'app/bootstrap.php';
try {
    $db = \App\Core\Database::getInstance();

    // Check syllabus_nodes structure
    $stmt = $db->query('DESCRIBE syllabus_nodes');
    $columns = $stmt->fetchAll();
    echo 'syllabus_nodes table structure:' . PHP_EOL;
    foreach ($columns as $col) {
        echo '- ' . $col['Field'] . ' (' . $col['Type'] . ')' . PHP_EOL;
    }
    echo PHP_EOL;

    // Check existing data
    $stmt = $db->query('SELECT type, COUNT(*) as count FROM syllabus_nodes GROUP BY type');
    $types = $stmt->fetchAll();
    echo 'Existing syllabus node types:' . PHP_EOL;
    foreach ($types as $type) {
        echo '- ' . $type['type'] . ': ' . $type['count'] . PHP_EOL;
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>