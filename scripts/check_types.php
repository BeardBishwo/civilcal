<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();
$stmt = $db->getPdo()->query("SELECT DISTINCT type FROM syllabus_nodes");
$types = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Existing Types:\n";
foreach ($types as $type) {
    echo "- $type\n";
}
