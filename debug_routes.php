<?php
define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/app/bootstrap.php';

$db = \App\Core\Database::getInstance()->getPdo();

// Query the view or table
$stmt = $db->query("SELECT * FROM calculator_urls WHERE calculator_id = 'concrete-volume' LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);

print_r($row);
