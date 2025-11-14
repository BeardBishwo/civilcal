<?php
/**
 * Fix remaining includes/ references in theme files
 */

echo "=== FIXING REMAINING INCLUDES REFERENCES ===\n\n";

$rootDir = dirname(__DIR__);
$updated = 0;
$errors = 0;

// Files to update - focusing on theme and view files
$patterns = [
    // Theme view files  
    $rootDir . '/themes/default/views/**/*.php',
    $rootDir . '/tests/**/*.php'
];

// Find all PHP files recursively
function findFiles($pattern) {
    $files = [];
    $parts = explode('**', $pattern);
    if (count($parts) === 2) {
        $baseDir = $parts[0];
        $extension = $parts[1];
        
        if (is_dir($baseDir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($baseDir)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile() && fnmatch('*' . $extension, $file->getFilename())) {
                    $files[] = $file->getPathname();
                }
            }
        }
    }
    return $files;
}

$allFiles = [];
foreach ($patterns as $pattern) {
    $allFiles = array_merge($allFiles, findFiles($pattern));
}

foreach ($allFiles as $filepath) {
    $content = file_get_contents($filepath);
    $originalContent = $content;
    
    // Skip if no includes references
    if (strpos($content, 'includes/') === false) {
        continue;
    }
    
    echo "Updating: " . str_replace($rootDir . '/', '', $filepath) . "\n";
    
    // Calculate relative path to root from file location
    $relativePath = str_replace($rootDir . '/', '', $filepath);
    $levels = substr_count($relativePath, '/');
    $backPath = str_repeat('../', $levels);
    
    // Replace various includes patterns
    $replacements = [
        // Direct includes paths
        "require_once 'themes/default/views/partials/header.php';" => "require_once '{$backPath}themes/default/views/partials/header.php';",
        "require_once 'themes/default/views/partials/footer.php';" => "require_once '{$backPath}themes/default/views/partials/footer.php';",
        "require_once 'app/Helpers/functions.php';" => "require_once '{$backPath}app/Helpers/functions.php';",
        "require_once 'app/Config/config.php';" => "require_once '{$backPath}app/Config/config.php';",
        "require_once 'app/Services/Security.php';" => "require_once '{$backPath}app/Services/Security.php';",
        "require_once 'app/Config/ComplianceConfig.php';" => "require_once '{$backPath}app/Config/ComplianceConfig.php';",
        
        // __DIR__ patterns
        "require_once __DIR__ . '/themes/default/views/partials/header.php';" => "require_once dirname(__DIR__, $levels) . '/themes/default/views/partials/header.php';",
        "require_once __DIR__ . '/themes/default/views/partials/footer.php';" => "require_once dirname(__DIR__, $levels) . '/themes/default/views/partials/footer.php';",
        "require_once __DIR__ . '/app/Helpers/functions.php';" => "require_once dirname(__DIR__, $levels) . '/app/Helpers/functions.php';",
        "require_once __DIR__ . '/app/Config/config.php';" => "require_once dirname(__DIR__, $levels) . '/app/Config/config.php';",
        "require_once __DIR__ . '/app/Services/Security.php';" => "require_once dirname(__DIR__, $levels) . '/app/Services/Security.php';",
        "require_once __DIR__ . '/app/Config/ComplianceConfig.php';" => "require_once dirname(__DIR__, $levels) . '/app/Config/ComplianceConfig.php';",
        
        // Error message text updates
        'in <code>app/Config/config.php</code>' => 'in <code>app/Config/config.php</code>',
        
        // Comment updates
        '// includes/back-to-top.php' => '// Back to Top Button Component',
        "'includes/Installer.php'" => "'{$backPath}install/includes/Installer.php'",
        '../install/includes/Installer.php' => dirname(__DIR__, 2) . '/install/includes/Installer.php'
    ];
    
    foreach ($replacements as $old => $new) {
        $content = str_replace($old, $new, $content);
    }
    
    // Write back if changed
    if ($content !== $originalContent) {
        if (file_put_contents($filepath, $content)) {
            $updated++;
            echo "  ✅ Updated\n";
        } else {
            $errors++;
            echo "  ❌ Failed to write\n";
        }
    } else {
        echo "  ⚠️  No changes made\n";
    }
}

echo "\n=== RESULTS ===\n";
echo "Updated: $updated files\n";
echo "Errors: $errors files\n";
echo "✅ Includes reference cleanup complete!\n";
?>




