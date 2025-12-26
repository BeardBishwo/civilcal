<?php
require 'app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();
$stmt = $db->query("SELECT id, slug, title FROM pages");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\nChecking modules table:\n";
$stmt = $db->query("SELECT * FROM modules");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
