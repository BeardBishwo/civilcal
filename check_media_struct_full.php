<?php
require 'vendor/autoload.php';
$db = \App\Core\Database::getInstance()->getPdo();
$stmt = $db->query("DESCRIBE media");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $row) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
