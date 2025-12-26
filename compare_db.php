<?php
require 'app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();

echo "Checking Structural vs Civil paths:\n";

$stmt = $db->prepare('SELECT * FROM calculator_urls WHERE category="civil" LIMIT 1');
$stmt->execute();
$civil = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($civil);

$stmt = $db->prepare('SELECT * FROM calculator_urls WHERE category="structural" LIMIT 1');
$stmt->execute();
$struct = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($struct);
