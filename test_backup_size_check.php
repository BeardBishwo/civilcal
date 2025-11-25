<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing backup size check...\n";
    
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Access the private properties using reflection
    $reflection = new ReflectionClass($backupService);
    $backupDirProperty = $reflection->getProperty('backupDir');
    $backupDirProperty->setAccessible(true);
    $backupDir = $backupDirProperty->getValue($backupService);
    
    $maxBackupSizeProperty = $reflection->getProperty('maxBackupSize');
    $maxBackupSizeProperty->setAccessible(true);
    $maxBackupSize = $maxBackupSizeProperty->getValue($backupService);
    
    echo "Backup directory: " . $backupDir . "\n";
    echo "Max backup size: " . $maxBackupSize . " bytes (" . ($maxBackupSize / (1024*1024)) . " MB)\n";
    
    // Check existing backup files
    $files = glob($backupDir . '/*.zip');
    echo "Existing backup files:\n";
    foreach ($files as $file) {
        $fileName = basename($file);
        $fileSize = filesize($file);
        echo "- $fileName: " . $fileSize . " bytes (" . ($fileSize / (1024*1024)) . " MB)\n";
        
        if ($fileSize > $maxBackupSize) {
            echo "  WARNING: File exceeds max size!\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}