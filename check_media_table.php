<?php
require 'vendor/autoload.php';
$db = \App\Core\Database::getInstance()->getPdo();
$stmt = $db->query("SHOW TABLES LIKE 'media'");
echo ($stmt->rowCount() > 0 ? "exists" : "missing") . "\n";
