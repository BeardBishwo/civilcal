<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing BackupService directly...\n";
    
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Access the private backupDir property using reflection
    $reflection = new ReflectionClass($backupService);
    $backupDirProperty = $reflection->getProperty('backupDir');
    $backupDirProperty->setAccessible(true);
    $backupDir = $backupDirProperty->getValue($backupService);
    
    echo "Backup directory: " . $backupDir . "\n";
    
    // Generate backup name
    $backupName = 'test_service_backup_' . date('Y-m-d_H-i-s') . '.zip';
    $backupPath = $backupDir . '/' . $backupName;
    
    echo "Backup path: " . $backupPath . "\n";
    
    // Test the exact same logic as in createBackup method
    $zip = new \ZipArchive();
    echo "Creating zip archive...\n";
    $zipOpen = $zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    
    echo "Zip open result: " . var_export($zipOpen, true) . "\n";
    
    if ($zipOpen !== true) {
        throw new Exception("Cannot create backup file at: {$backupPath}");
    }
    
    // Add a simple test file to the zip (like the service would do)
    $testContent = "This is a test file for backup service test";
    $zip->addFromString('test.txt', $testContent);
    
    echo "Added test file to zip\n";
    
    $zip->close();
    
    echo "Zip closed\n";
    
    // Check if file exists (like the service does)
    clearstatcache(); // Clear file status cache
    echo "File exists: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    if (!file_exists($backupPath)) {
        echo "ERROR: Backup file was not created successfully at: {$backupPath}\n";
    } else {
        $backupSize = filesize($backupPath);
        echo "File size: " . $backupSize . " bytes\n";
        
        if ($backupSize === false) {
            echo "ERROR: Cannot determine backup file size\n";
        } else {
            echo "SUCCESS: Backup created successfully\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}