<?php
/**
 * Activate All Modules Script
 * Sets is_active = 1 for all modules in the database
 */

require_once __DIR__ . '/../app/bootstrap.php';

// Get database connection
$config = require CONFIG_PATH . '/database.php';
$db = new PDO(
    "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
    $config['username'],
    $config['password']
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Update all modules to active
    $stmt = $db->prepare("UPDATE modules SET is_active = 1, updated_at = NOW()");
    $stmt->execute();
    
    $count = $stmt->rowCount();
    
    echo "========================================\n";
    echo "✓ Successfully activated $count modules\n";
    echo "========================================\n";
    
    // Show current status
    $result = $db->query("SELECT name, is_active FROM modules ORDER BY name");
    echo "\nCurrent module status:\n";
    echo str_repeat("-", 40) . "\n";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['is_active'] ? '✓ Active' : '✗ Inactive';
        echo sprintf("%-25s %s\n", $row['name'], $status);
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
