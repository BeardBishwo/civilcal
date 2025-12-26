<?php
require 'app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();
$stmt = $db->query("SELECT calculator_id FROM calculator_urls WHERE category='structural'");
$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Structural IDs in DB:\n";
foreach($ids as $id) echo " - $id\n";

$stmt = $db->query("SELECT calculator_id FROM calculator_urls WHERE category='site'");
$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "\nSite IDs in DB:\n";
foreach($ids as $id) echo " - $id\n";
