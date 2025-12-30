<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$pdo = $db->getPdo();
$stmt = $pdo->query("DESCRIBE user_resource_logs");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
