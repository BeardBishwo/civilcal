<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing exact steps from BackupService...\n";
    
    // Replicate the exact steps from BackupService::createBackup()
    
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
    
    // Generate backup name (same as in createBackup)
    $backupName = 'exact_steps_test_' . date('Y-m-d_H-i-s') . '.zip';
    $backupPath = $backupDir . '/' . $backupName;
    
    echo "Backup path: " . $backupPath . "\n";
    echo "Backup directory exists: " . (is_dir($backupDir) ? 'Yes' : 'No') . "\n";
    echo "Backup directory writable: " . (is_writable($backupDir) ? 'Yes' : 'No') . "\n";
    
    // Create a new zip archive (same as in createBackup)
    $zip = new \ZipArchive();
    echo "Creating zip archive...\n";
    $zipOpen = $zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    
    echo "Zip open result: " . var_export($zipOpen, true) . "\n";
    
    if ($zipOpen !== true) {
        throw new Exception("Cannot create backup file at: {$backupPath}");
    }
    
    // Check if file exists immediately after opening
    echo "File exists after open: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    // Add database dump (same as in createBackup)
    echo "Creating database dump...\n";
    $dbDumpPath = $createDatabaseDumpMethod->invoke($backupService);
    echo "Database dump created at: " . $dbDumpPath . "\n";
    
    if (file_exists($dbDumpPath)) {
        echo "Adding database dump to zip...\n";
        $zip->addFile($dbDumpPath, 'database_dump.sql');
        echo "Added database dump to zip\n";
    }
    
    // Add files (same as in createBackup)
    echo "Adding files to zip...\n";
    $addDirectoryToZipMethod->invoke($backupService, $zip, BASE_PATH, 'files');
    echo "Added files to zip\n";
    
    // Check if file still exists before closing
    echo "File exists before close: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    // Close the zip (same as in createBackup)
    echo "Closing zip...\n";
    $closeResult = $zip->close();
    echo "Zip close result: " . var_export($closeResult, true) . "\n";
    
    // Check if file exists after closing
    echo "File exists after close: " . (file_exists($backupPath) ? 'Yes' : 'No') . "\n";
    
    if (file_exists($backupPath)) {
        $backupSize = filesize($backupPath);
        echo "File size: " . $backupSize . " bytes\n";
    }
    
    // Clean up the temporary dump file
    if (file_exists($dbDumpPath)) {
        unlink($dbDumpPath);
        echo "Cleaned up temporary database dump\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}