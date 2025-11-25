<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    $backupService = new \App\Services\BackupService();
    
    echo "Testing backup creation...\n";
    
    // Test creating a simple backup without database or files
    $result = $backupService->createBackup(false, false, 'test_backup');
    
    echo "Backup result:\n";
    print_r($result);
    
    echo "\nBackup directory contents:\n";
    $files = glob(BASE_PATH . '/storage/backups/*.zip');
    foreach ($files as $file) {
        echo "- " . basename($file) . " (" . filesize($file) . " bytes)\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}