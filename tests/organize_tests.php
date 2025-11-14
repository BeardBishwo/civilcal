<?php
/**
 * Test Organization Script
 * Organizes all test files into proper directory structure
 */

echo "🗂️ ORGANIZING TEST FILES\n";
echo "========================\n\n";

$baseDir = __DIR__;
$organized = 0;
$errors = 0;

// Define file organization rules
$organizationRules = [
    // Installation tests
    'installation' => [
        'install*',
        'installation*',
        'comprehensive_installation*',
        'laragon_setup*',
        'emergency_*',
        'debug_installation*'
    ],
    
    // Payment tests
    'payment' => [
        'payment*',
        'quick_payment*',
        'saas_system*'
    ],
    
    // Theme tests
    'theme' => [
        'theme*',
        'premium*',
        'css*',
        'assets*',
        'homepage*',
        'activate_default_theme*',
        'check_theme*',
        'verify_css*',
        'test_css*',
        'serve_css*'
    ],
    
    // Routing tests
    'routing' => [
        'router*',
        'route*',
        'routing*',
        'debug_router*',
        'debug_routing*',
        'debug_url*',
        'correct_access_urls*'
    ],
    
    // Email tests
    'email' => [
        'email*'
    ],
    
    // Database tests
    'database' => [
        'database*',
        'db_*',
        'fix_database*'
    ],
    
    // Server tests
    'server' => [
        'debug_*',
        'http_500*',
        'emergency_*',
        'system_verification*',
        'comprehensive_test*',
        'mvc_*',
        'file_system*',
        'web_application*'
    ],
    
    // Legacy/Archive
    'legacy' => [
        'oindex*',
        'index_*',
        'final*',
        'FINAL*',
        'PRIORITY*',
        'Final*',
        'fix_*',
        'clean_*',
        'cleanup*',
        'missing_*',
        'simple_*',
        'basic_*',
        'quick_*',
        'critical_*',
        'renderPartial*',
        'check_methods*',
        'write_test*',
        'test.php',
        'test.html'
    ]
];

// Get all files in tests directory
$files = glob($baseDir . '/*');

foreach ($files as $file) {
    $filename = basename($file);
    
    // Skip directories and special files
    if (is_dir($file) || $filename === 'organize_tests.php' || $filename === 'README.md' || $filename === 'test_runner.php') {
        continue;
    }
    
    $moved = false;
    
    // Try to match file to organization rules
    foreach ($organizationRules as $category => $patterns) {
        foreach ($patterns as $pattern) {
            if (fnmatch($pattern, $filename)) {
                $targetDir = $baseDir . '/' . $category;
                
                // Create directory if it doesn't exist
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                $targetPath = $targetDir . '/' . $filename;
                
                // Check if file already exists
                if (file_exists($targetPath)) {
                    echo "⚠️ Skipping $filename - already exists in $category/\n";
                } else {
                    if (rename($file, $targetPath)) {
                        echo "✅ Moved $filename → $category/\n";
                        $organized++;
                    } else {
                        echo "❌ Failed to move $filename\n";
                        $errors++;
                    }
                }
                
                $moved = true;
                break 2; // Break out of both loops
            }
        }
    }
    
    // If no rule matched, put in legacy
    if (!$moved) {
        $targetDir = $baseDir . '/legacy';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        $targetPath = $targetDir . '/' . $filename;
        
        if (file_exists($targetPath)) {
            echo "⚠️ Skipping $filename - already exists in legacy/\n";
        } else {
            if (rename($file, $targetPath)) {
                echo "📦 Moved $filename → legacy/ (no specific category)\n";
                $organized++;
            } else {
                echo "❌ Failed to move $filename\n";
                $errors++;
            }
        }
    }
}

echo "\n📊 ORGANIZATION SUMMARY\n";
echo "======================\n";
echo "✅ Files organized: $organized\n";
echo "❌ Errors: $errors\n";

// Show final directory structure
echo "\n📁 FINAL DIRECTORY STRUCTURE\n";
echo "============================\n";

$directories = glob($baseDir . '/*', GLOB_ONLYDIR);
sort($directories);

foreach ($directories as $dir) {
    $dirName = basename($dir);
    $fileCount = count(glob($dir . '/*')) - count(glob($dir . '/*', GLOB_ONLYDIR));
    echo "📂 $dirName/ ($fileCount files)\n";
    
    // Show first few files as examples
    $files = array_slice(glob($dir . '/*'), 0, 3);
    foreach ($files as $file) {
        if (is_file($file)) {
            echo "   └── " . basename($file) . "\n";
        }
    }
    
    $totalFiles = count(glob($dir . '/*'));
    if ($totalFiles > 3) {
        echo "   └── ... and " . ($totalFiles - 3) . " more files\n";
    }
    echo "\n";
}

echo "✨ Test organization complete!\n";
?>
