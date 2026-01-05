<?php
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    // Create a temporary table for testing
    $pdo->exec("CREATE TABLE IF NOT EXISTS test_collision (
        id INT AUTO_INCREMENT PRIMARY KEY,
        level VARCHAR(255),
        type VARCHAR(50)
    )");
    
    // Insert a test row
    $pdo->exec("TRUNCATE TABLE test_collision");
    $db->insert('test_collision', ['level' => 'Test', 'type' => 'unit']);
    
    echo "--- Testing Parameter Collision (The Bug) ---\n";
    try {
        // This mimics: update(..., ['level' => null], "level = :level ...", ['level' => 'Test'])
        // Database::update merges data and whereParams.
        // Data: ['level' => null]
        // WhereParams: ['level' => 'Test']
        // Merged: ['level' => 'Test'] (because valid keys overwrite? or data overwrites params?)
        // Actually array_merge($data, $whereParams): existing keys in first array are overwritten by second? 
        // No, array_merge($a, $b): "If the input arrays have the same string keys, then the later value for that key will overwrite the previous one."
        // So 'level' => 'Test' (from whereParams) survives.
        // The query SET clause: `level` = :level.
        // The query WHERE clause: level = :level.
        // Both use :level.
        // Value bound is 'Test'.
        // So SET level = 'Test' WHERE level = 'Test'.
        // This is strictly NOT an error (HY093), it just doesn't update to NULL!
        // WAIT. If I use DIFFERENT values, it fails logic.
        // But the user reported HY093 (Invalid parameter number).
        // This error usually happens when you have :level in SQL but NOT in params, or vice versa.
        
        $db->update('test_collision', ['level' => null], "level = :level", ['level' => 'Test']);
        echo "Collision Query 1 Executed (Unexpected success if expecting HY093?)\n";
        
        // Let's inspect the result
        $row = $db->findOne('test_collision', ['type' => 'unit']);
        echo "Row level is: " . var_export($row['level'], true) . "\n"; 
        // If it's 'Test', then the update failed to set it to NULL.
        
    } catch (\Exception $e) {
        echo "Caught Expected Exception: " . $e->getMessage() . "\n";
    }

    echo "\n--- Testing Fix (Renamed Param) ---\n";
    try {
        $db->update('test_collision', ['level' => null], "level = :target_level", ['target_level' => 'Test']);
        echo "Fix Query Executed Successfully\n";
        
         $row = $db->findOne('test_collision', ['type' => 'unit']);
        echo "Row level is: " . var_export($row['level'], true) . "\n"; 
        // Should be NULL
        
    } catch (\Exception $e) {
        echo "Caught Exception in Fix: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
