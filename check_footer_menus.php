<?php
require_once 'app/bootstrap.php';
$db = \App\Core\Database::getInstance();
foreach (['footer_1', 'footer_2', 'footer_3', 'footer_4'] as $loc) {
    echo "--- $loc ---\n";
    $stmt = $db->prepare("SELECT * FROM menus WHERE location = :loc");
    $stmt->execute(['loc' => $loc]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "Name: {$row['name']}\n";
        echo "Items: {$row['items']}\n";
    } else {
        echo "NOT FOUND\n";
    }
    echo "\n";
}
echo "--- ALL LOCATIONS ---\n";
$stmt = $db->query("SELECT DISTINCT location FROM menus");
while ($row = $stmt->fetch()) { echo $row['location'] . ", "; }
echo "\n";
