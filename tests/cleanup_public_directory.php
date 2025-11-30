<?php
/**
 * Public Directory Cleanup Script
 * Removes unused files from the public directory to improve security and reduce clutter
 */

echo "=== Public Directory Cleanup Script ===\n";
echo "Removing unused files from public directory...\n\n";

// List of files to remove
$filesToRemove = [
    '.htaccess.bak',
    'admin-users-create.php',
    'check_logo_favicon.php',
    'debug_logo_favicon.php',
    'image_system_diagnostic.php',
    'simple_image_test.php',
    'test-fixed.php',
    'test_images.php',
    'test_theme_images.php'
];

$removedCount = 0;
$failedCount = 0;

foreach ($filesToRemove as $file) {
    $filePath = __DIR__ . '/public/' . $file;
    
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo "✓ Removed: $file\n";
            $removedCount++;
        } else {
            echo "✗ Failed to remove: $file\n";
            $failedCount++;
        }
    } else {
        echo "  File not found (already removed): $file\n";
    }
}

echo "\n=== Cleanup Complete ===\n";
echo "Successfully removed: $removedCount files\n";
echo "Failed to remove: $failedCount files\n";

// Check if procalculator theme assets should be removed
echo "\nChecking procalculator theme assets...\n";
$procalculatorPath = __DIR__ . '/public/assets/themes/procalculator';

if (is_dir($procalculatorPath)) {
    echo "procalculator theme assets found at: public/assets/themes/procalculator\n";
    echo "These assets are associated with the procalculator theme in the database.\n";
    echo "If you want to remove these assets, manually delete the directory:\n";
    echo "  rm -rf public/assets/themes/procalculator\n";
} else {
    echo "procalculator theme assets not found.\n";
}

echo "\nCleanup script completed.\n";
?>