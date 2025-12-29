<?php
require 'vendor/autoload.php';
$db = \App\Core\Database::getInstance()->getPdo();

echo "--- PAGES TABLE ---\n";
$stmt = $db->query("DESCRIBE pages");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\n--- MENUS TABLE ---\n";
$stmt = $db->query("DESCRIBE menus");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\n--- SETTINGS TABLE ---\n";
$stmt = $db->query("DESCRIBE settings");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
