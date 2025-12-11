<?php
// Run notifications migration
require_once __DIR__ . '/../app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "Running notifications migration...\n";
    
    $sql = file_get_contents(__DIR__ . '/migrations/create_notifications_tables.sql');
    
    $pdo->exec($sql);
    
    echo "✓ Notifications tables created successfully!\n";
    echo "✓ Default preferences inserted for existing users\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
