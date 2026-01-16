<?php
require_once 'app/bootstrap.php';
try {
    $db = \App\Core\Database::getInstance();

    // Check existing categories
    $stmt = $db->query('SELECT id, title, parent_id FROM syllabus_nodes WHERE type = \'category\' ORDER BY title');
    $categories = $stmt->fetchAll();
    echo 'Existing categories:' . PHP_EOL;
    foreach ($categories as $cat) {
        echo '- ID ' . $cat['id'] . ': ' . $cat['title'] . ' (parent: ' . $cat['parent_id'] . ')' . PHP_EOL;
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>