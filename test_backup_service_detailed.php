<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing BackupService createBackup method with detailed error reporting...\n";
    
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Test creating a backup with both database and files (default behavior)
    echo "Creating backup with database and files...\n";
    $result = $backupService->createBackup(true, true, 'detailed_test_backup');
    
    echo "Backup result:\n";
    print_r($result);
    
    echo "\nBackup directory contents:\n";
    $files = glob(BASE_PATH . '/storage/backups/*.zip');
    foreach ($files as $file) {
        $fileName = basename($file);
        $fileSize = filesize($file);
        echo "- $fileName: " . $fileSize . " bytes\n";
    }
    
} catch (Exception $e) {
    echo "Exception caught: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "Error caught: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}