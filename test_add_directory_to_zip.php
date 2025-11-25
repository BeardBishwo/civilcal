<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing addDirectoryToZip method...\n";
    
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Access the private method using reflection
    $reflection = new ReflectionClass($backupService);
    $addDirectoryToZipMethod = $reflection->getMethod('addDirectoryToZip');
    $addDirectoryToZipMethod->setAccessible(true);
    
    // Create a test zip file
    $backupDir = BASE_PATH . '/storage/backups';
    $backupName = 'directory_test_' . date('Y-m-d_H-i-s') . '.zip';
    $backupPath = $backupDir . '/' . $backupName;
    
    echo "Backup path: " . $backupPath . "\n";
    
    // Create a new zip archive
    $zip = new \ZipArchive();
    echo "Creating zip archive...\n";
    $zipOpen = $zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    
    echo "Zip open result: " . var_export($zipOpen, true) . "\n";
    
    if ($zipOpen !== true) {
        throw new Exception("Cannot create backup file at: {$backupPath}");
    }
    
    // Test adding a small directory
    echo "Adding directory to zip...\n";
    $testDir = BASE_PATH . '/app/Config';
    $addDirectoryToZipMethod->invoke($backupService, $zip, $testDir, 'config');
    echo "Added directory to zip\n";
    
    echo "Closing zip...\n";
    $zip->close();
    
    echo "Zip closed\n";
    
    // Check if file exists
    clearstatcache(); // Clear file status cache
    echo "File exists: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    if (file_exists($backupPath)) {
        $backupSize = filesize($backupPath);
        echo "File size: " . $backupSize . " bytes\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}