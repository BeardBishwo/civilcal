<?php
require_once 'app/bootstrap.php';

try {
    echo "Testing zip file creation with individual file additions...\n";
    
    $backupDir = BASE_PATH . '/storage/backups';
    $backupName = 'zip_test_' . date('Y-m-d_H-i-s') . '.zip';
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
    
    // Add a simple test file
    echo "Adding test file...\n";
    $testContent = "This is a test file";
    $zip->addFromString('test.txt', $testContent);
    echo "Added test file\n";
    
    // Try to add a few real files
    $testFiles = [
        BASE_PATH . '/app/Config/config.php',
        BASE_PATH . '/public/index.php',
        BASE_PATH . '/.env'
    ];
    
    foreach ($testFiles as $file) {
        if (file_exists($file)) {
            echo "Adding file: " . $file . "\n";
            try {
                $relativePath = substr($file, strlen(BASE_PATH) + 1);
                $zip->addFile($file, 'files/' . $relativePath);
                echo "Successfully added: " . $file . "\n";
            } catch (Exception $e) {
                echo "Failed to add file: " . $file . " - " . $e->getMessage() . "\n";
            }
        } else {
            echo "File does not exist: " . $file . "\n";
        }
    }
    
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