<?php
require_once __DIR__ . '/../app/bootstrap.php';

$config = require CONFIG_PATH . '/database.php';
$db = new PDO(
    "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
    $config['username'],
    $config['password']
);

$result = $db->query('DESCRIBE modules');
echo "Modules table structure:\n";
echo str_repeat("=", 50) . "\n";
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
