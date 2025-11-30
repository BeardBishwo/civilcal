<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing database-only backup...\n";
    
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Test creating a backup with database only (no files)
    echo "Creating backup with database only...\n";
    $result = $backupService->createBackup(true, false, 'database_only_test');
    
    echo "Backup result:\n";
    print_r($result);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}