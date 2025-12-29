<?php
require_once 'app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$results = [];
foreach (['footer_1', 'footer_2', 'footer_3', 'footer_4'] as $loc) {
    $stmt = $db->prepare("SELECT * FROM menus WHERE location = :loc");
    $stmt->execute(['loc' => $loc]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $results[$loc] = [
            'name' => $row['name'],
            'items' => json_decode($row['items'], true)
        ];
    }
}
echo json_encode($results, JSON_PRETTY_PRINT);
