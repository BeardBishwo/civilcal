<?php
require 'vendor/autoload.php';

use App\Core\Database;

$db = Database::getInstance();
$stmt = $db->query('SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE "email_%" ORDER BY setting_key');

echo "Email Settings in Database:\n";
echo str_repeat("=", 50) . "\n";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['setting_key'] . ' = ' . $row['setting_value'] . "\n";
}

echo str_repeat("=", 50) . "\n";
echo "Total settings: " . $stmt->rowCount() . "\n";
