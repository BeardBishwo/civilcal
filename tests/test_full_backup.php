<?php
require_once 'app/bootstrap.php';
require_once 'app/Services/BackupService.php';

try {
    echo "Testing full backup process...\n";
    
    // Create backup service
    $backupService = new \App\Services\BackupService();
    
    // Access the private properties and methods using reflection
    $reflection = new ReflectionClass($backupService);
    $backupDirProperty = $reflection->getProperty('backupDir');
    $backupDirProperty->setAccessible(true);
    $backupDir = $backupDirProperty->getValue($backupService);
    
    $createDatabaseDumpMethod = $reflection->getMethod('createDatabaseDump');
    $createDatabaseDumpMethod->setAccessible(true);
    
    $addDirectoryToZipMethod = $reflection->getMethod('addDirectoryToZip');
    $addDirectoryToZipMethod->setAccessible(true);
    
    // Generate backup name
    $backupName = 'full_test_backup_' . date('Y-m-d_H-i-s') . '.zip';
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
    
    // Add database dump (like the service would do)
    echo "Creating database dump...\n";
    $dbDumpPath = $createDatabaseDumpMethod->invoke($backupService);
    echo "Database dump created at: " . $dbDumpPath . "\n";
    echo "Database dump exists: " . (file_exists($dbDumpPath) ? 'Yes' : 'No') . "\n";
    
    if (file_exists($dbDumpPath)) {
        $zip->addFile($dbDumpPath, 'database_dump.sql');
        echo "Added database dump to zip\n";
    }
    
    // Add files (like the service would do)
    echo "Adding files to zip...\n";
    $addDirectoryToZipMethod->invoke($backupService, $zip, BASE_PATH, 'files');
    echo "Added files to zip\n";
    
    echo "Closing zip...\n";
    $zip->close();
    
    echo "Zip closed\n";
    
    // Clean up the temporary dump file
    if (file_exists($dbDumpPath)) {
        unlink($dbDumpPath);
        echo "Cleaned up temporary database dump\n";
    }
    
    // Check if file exists
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