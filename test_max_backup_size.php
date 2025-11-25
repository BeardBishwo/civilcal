<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    echo "Current max backup size: " . $backupService->getMaxBackupSizeMB() . " MB\n";
    
    // Set new max backup size
    $backupService->setMaxBackupSize(2048); // 2GB
    
    echo "New max backup size: " . $backupService->getMaxBackupSizeMB() . " MB\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}