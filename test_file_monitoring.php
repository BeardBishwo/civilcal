<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing file monitoring during backup...\n";
    
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Access private properties and methods
    $reflection = new ReflectionClass($backupService);
    $backupDirProperty = $reflection->getProperty('backupDir');
    $backupDirProperty->setAccessible(true);
    $backupDir = $backupDirProperty->getValue($backupService);
    
    $createDatabaseDumpMethod = $reflection->getMethod('createDatabaseDump');
    $createDatabaseDumpMethod->setAccessible(true);
    
    $addDirectoryToZipMethod = $reflection->getMethod('addDirectoryToZip');
    $addDirectoryToZipMethod->setAccessible(true);
    
    // Generate backup name
    $backupName = 'monitor_test_' . date('Y-m-d_H-i-s') . '.zip';
    $backupPath = $backupDir . '/' . $backupName;
    
    echo "Backup path: " . $backupPath . "\n";
    
    // Create a new zip archive
    $zip = new \ZipArchive();
    echo "Creating zip archive at " . date('H:i:s') . "...\n";
    $zipOpen = $zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    
    echo "Zip open result: " . var_export($zipOpen, true) . "\n";
    echo "File exists after open: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    // Add a test file immediately
    echo "Adding test file at " . date('H:i:s') . "...\n";
    $zip->addFromString('test.txt', 'This is a test file');
    echo "File exists after adding test file: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    // Create database dump
    echo "Creating database dump at " . date('H:i:s') . "...\n";
    $dbDumpPath = $createDatabaseDumpMethod->invoke($backupService);
    echo "Database dump created\n";
    echo "File exists after db dump: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    // Add database dump to zip
    echo "Adding database dump at " . date('H:i:s') . "...\n";
    if (file_exists($dbDumpPath)) {
        $zip->addFile($dbDumpPath, 'database_dump.sql');
    }
    echo "File exists after adding db dump: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    // Add files to zip (this is the step that might be causing issues)
    echo "Adding files to zip at " . date('H:i:s') . "...\n";
    $addDirectoryToZipMethod->invoke($backupService, $zip, BASE_PATH, 'files');
    echo "File exists after adding files: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    // Check file status before closing
    echo "Checking file status before close at " . date('H:i:s') . "...\n";
    echo "File exists: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    if (file_exists($backupPath)) {
        echo "File size: " . filesize($backupPath) . " bytes\n";
        echo "File permissions: " . substr(sprintf('%o', fileperms($backupPath)), -4) . "\n";
    }
    
    // Try to close the zip
    echo "Closing zip at " . date('H:i:s') . "...\n";
    $closeResult = $zip->close();
    echo "Zip close result: " . var_export($closeResult, true) . "\n";
    
    // Check file status after closing
    echo "Checking file status after close at " . date('H:i:s') . "...\n";
    echo "File exists: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    if (file_exists($backupPath)) {
        $backupSize = filesize($backupPath);
        echo "File size: " . $backupSize . " bytes\n";
    }
    
    // Clean up
    if (file_exists($dbDumpPath)) {
        unlink($dbDumpPath);
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}