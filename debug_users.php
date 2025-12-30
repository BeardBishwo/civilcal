<?php
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;
$db = Database::getInstance();
$columns = $db->query("DESCRIBE users")->fetchAll();
foreach ($columns as $col) {
    echo $col['Field'] . "\n";
}
