<?php
require_once 'app/bootstrap.php';

// Test the exclude patterns logic
$excludePatterns = ['*/storage/backups/*', '*/cache/*'];

// Test some paths
$testPaths = [
    'storage/backups/test.zip',
    'storage/cache/temp.txt',
    'app/Controllers/AdminController.php',
    'public/index.php',
    'storage/logs/app.log'
];

echo "Testing exclude patterns:\n";
foreach ($testPaths as $path) {
    $shouldExclude = false;
    foreach ($excludePatterns as $pattern) {
        if (fnmatch($pattern, $path)) {
            $shouldExclude = true;
            break;
        }
    }
    
    echo "$path: " . ($shouldExclude ? 'EXCLUDED' : 'INCLUDED') . "\n";
}