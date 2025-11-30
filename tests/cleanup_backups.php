<?php
// Script to clean up test backup files

require_once __DIR__ . '/app/bootstrap.php';

$backupDir = BASE_PATH . '/storage/backups';
$files = glob($backupDir . '/*');

echo "Found " . count($files) . " files in backup directory:\n\n";

$testFiles = [];
foreach ($files as $file) {
    $filename = basename($file);
    // Check if it's a test file based on naming pattern
    if (strpos($filename, 'test') !== false) {
        $testFiles[] = $file;
        echo "- $filename (" . round(filesize($file) / 1024, 2) . " KB)\n";
    }
}

echo "\n" . count($testFiles) . " test backup files found.\n";

if (!empty($testFiles)) {
    echo "\nDo you want to delete these test backup files? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim($line) === 'y') {
        foreach ($testFiles as $file) {
            if (unlink($file)) {
                echo "Deleted: " . basename($file) . "\n";
            } else {
                echo "Failed to delete: " . basename($file) . "\n";
            }
        }
        echo "\nCleanup completed!\n";
    } else {
        echo "Cleanup cancelled.\n";
    }
} else {
    echo "No test backup files found.\n";
}
?>
