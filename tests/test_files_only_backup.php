<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing files-only backup...\n";
    
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Test creating a backup with files only (no database)
    echo "Creating backup with files only...\n";
    $result = $backupService->createBackup(false, true, 'files_only_test');
    
    echo "Backup result:\n";
    print_r($result);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}