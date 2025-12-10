<?php
require_once __DIR__ . '/../app/bootstrap.php';

$config = require CONFIG_PATH . '/database.php';
$db = new PDO(
    "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
    $config['username'],
    $config['password']
);

echo "Database Module Status:\n";
echo str_repeat("=", 50) . "\n";

$result = $db->query('SELECT name, is_active FROM modules ORDER BY name');
$active = 0;
$inactive = 0;

while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $status = $row['is_active'] ? 'ACTIVE' : 'INACTIVE';
    echo sprintf("%-25s => %s\n", $row['name'], $status);
    
    if ($row['is_active']) {
        $active++;
    } else {
        $inactive++;
    }
}

echo str_repeat("=", 50) . "\n";
echo "Total Active: $active\n";
echo "Total Inactive: $inactive\n";
