<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Get backup list
    $backups = $backupService->getBackupList();
    
    echo "Backup list:\n";
    foreach ($backups as $backup) {
        echo "Name: " . $backup['name'] . "\n";
        echo "Size: " . ($backup['size_formatted'] ?? 'Unknown') . "\n";
        echo "Date: " . $backup['date'] . "\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}