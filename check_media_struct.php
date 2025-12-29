<?php
require 'vendor/autoload.php';
$db = \App\Core\Database::getInstance()->getPdo();
$stmt = $db->query("DESCRIBE media");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
