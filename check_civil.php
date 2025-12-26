<?php
require 'app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();
$stmt = $db->prepare('SELECT full_path FROM calculator_urls WHERE category="civil" LIMIT 1');
$stmt->execute();
echo "Civil Path: " . $stmt->fetchColumn() . "\n";
