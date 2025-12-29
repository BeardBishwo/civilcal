<?php
require_once 'app/bootstrap.php';
$db = \App\Core\Database::getInstance();
echo "--- MENUS TABLE DUMP ---\n";
$stmt = $db->query("SELECT * FROM menus");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']} | Slug/Location: {$row['location']} | Name: {$row['name']}\n";
    echo "Items: " . $row['items'] . "\n\n";
}
