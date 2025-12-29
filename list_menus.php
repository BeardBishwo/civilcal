<?php
require_once 'app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$stmt = $db->query("SELECT id, location, name FROM menus");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']} | Location: {$row['location']} | Name: {$row['name']}\n";
}
