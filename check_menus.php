<?php
require_once 'app/bootstrap.php';
$db = \App\Core\Database::getInstance();
echo "--- MENUS ---\n";
$stmt = $db->query("SELECT * FROM menus");
while ($row = $stmt->fetch()) {
    echo "ID: {$row['id']} | Slug: {$row['slug']} | Name: {$row['name']}\n";
    $stmt2 = $db->prepare("SELECT * FROM menu_items WHERE menu_id = :id ORDER BY order_index ASC");
    $stmt2->execute(['id' => $row['id']]);
    while ($item = $stmt2->fetch()) {
        echo "  - {$item['label']} ({$item['url']})\n";
    }
}
