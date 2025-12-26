<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();
$stmt = $db->prepare("DELETE FROM calculator_urls WHERE slug = 'structural'");
$stmt->execute();
echo "Deleted rogue structural entry\n";
