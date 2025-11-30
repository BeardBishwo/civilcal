<?php
require_once 'app/bootstrap.php';
require_once 'app/Controllers/Admin/BackupController.php';

try {
    echo "Testing web backup simulation...\n";
    
    // Simulate JSON input
    $jsonInput = json_encode([
        'include_database' => true,
        'include_files' => true,
        'name' => 'web_test_backup'
    ]);
    
    // Put the JSON data in the input stream
    $inputStream = fopen('php://memory', 'r+');
    fwrite($inputStream, $jsonInput);
    rewind($inputStream);
    
    // Override the input stream for testing
    // Note: This won't actually work in a real test, but let's test the controller logic
    
    // Create backup controller
    $backupController = new \App\Controllers\Admin\BackupController();
    
    // Test accessing private backupService property
    $reflection = new ReflectionClass($backupController);
    $backupServiceProperty = $reflection->getProperty('backupService');
    $backupServiceProperty->setAccessible(true);
    $backupService = $backupServiceProperty->getValue($backupController);
    
    echo "Backup service created\n";
    
    // Test the createBackup method directly with the same parameters
    echo "Creating backup...\n";
    $result = $backupService->createBackup(true, true, 'web_test_backup');
    
    echo "Backup result:\n";
    print_r($result);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}