<?php
require 'app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();
$stmt = $db->prepare('SELECT * FROM pages WHERE slug = "structural"');
$stmt->execute();
$page = $stmt->fetch(PDO::FETCH_ASSOC);
if ($page) {
    echo "Found Page ID: " . $page['id'] . "\n";
    echo "Content excerpt:\n";
    echo substr($page['content'], 0, 500) . "...\n";
    echo "\nLink verify:\n";
    if (strpos($page['content'], 'http://localhost/modules/calculator/') !== false) {
        echo "⚠️ Has legacy links!\n";
    } else {
        echo "✅ No legacy links found.\n";
    }
} else {
    echo "Page not found.\n";
}
