<?php
require 'vendor/autoload.php';
$db = \App\Core\Database::getInstance()->getPdo();
$stmt = $db->query("DESCRIBE settings");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
