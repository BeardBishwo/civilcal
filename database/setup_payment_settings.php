<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

echo "Creating payment_settings table...\n";

try {
    $db = Database::getInstance()->getPdo();
    
    $sql = file_get_contents(__DIR__ . '/payment_settings_table.sql');
    $db->exec($sql);
    
    echo "✓ Payment settings table created successfully!\n";
    echo "✓ Default payment gateways inserted\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
