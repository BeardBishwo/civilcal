<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing backup creation with detailed debugging...\n";
    
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Generate backup name
    $backupName = 'test_backup_' . date('Y-m-d_H-i-s') . '.zip';
    $backupPath = BASE_PATH . '/storage/backups/' . $backupName;
    
    echo "Backup path: " . $backupPath . "\n";
    
    // Create a new zip archive
    $zip = new \ZipArchive();
    echo "Creating zip archive...\n";
    $zipOpen = $zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    
    echo "Zip open result: " . var_export($zipOpen, true) . "\n";
    
    if ($zipOpen !== true) {
        echo "Failed to create zip archive\n";
        exit(1);
    }
    
    // Add a simple test file to the zip
    $testContent = "This is a test file for backup";
    $zip->addFromString('test.txt', $testContent);
    
    echo "Added test file to zip\n";
    
    $zip->close();
    
    echo "Zip closed\n";
    
    // Check if file exists
    echo "File exists: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    if (file_exists($backupPath)) {
        $fileSize = filesize($backupPath);
        echo "File size: " . $fileSize . " bytes\n";
    } else {
        echo "File does not exist after creation\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}