<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();
$stmt = $db->getPdo()->query("SELECT id, title, type, parent_id FROM syllabus_nodes ORDER BY id ASC");
$nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Node Dump:\n";
foreach ($nodes as $node) {
    echo sprintf(
        "[%d] %s (Type: %s) -> Parent: %s\n",
        $node['id'],
        $node['title'],
        $node['type'],
        $node['parent_id'] ?: 'NULL'
    );
}
