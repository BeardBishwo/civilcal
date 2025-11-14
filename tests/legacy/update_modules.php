<?php
/**
 * Batch update module file references
 */

echo "=== MODULE UPDATER ===\n\n";

// Find all PHP files in modules directory
$moduleDir = __DIR__ . '/../modules';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($moduleDir));

$updated = 0;
$errors = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filepath = $file->getPathname();
        $content = file_get_contents($filepath);
        
        // Skip if file doesn't contain old includes
        if (strpos($content, "app/Config/config.php") === false) {
            continue;
        }
        
        echo "Updating: " . str_replace(__DIR__ . '/../', '', $filepath) . "\n";
        
        // Count directory levels to determine correct path
        $relativePath = str_replace($moduleDir . '/', '', $filepath);
        $levels = substr_count($relativePath, '/');
        $backPath = str_repeat('../', $levels + 1);
        
        // Update includes paths
        $newContent = str_replace([
            "require_once '../../../app/Config/config.php';",
            "require_once '../../app/Config/config.php';", 
            "require_once '../../../../app/Config/config.php';",
            "require_once '../../../themes/default/views/partials/header.php';",
            "require_once '../../themes/default/views/partials/header.php';",
            "require_once '../../../../themes/default/views/partials/header.php';",
            "require_once '../../../app/Config/db.php';",
            "require_once '../../app/Config/db.php';",
            "require_once '../../../../app/Config/db.php';"
        ], [
            "require_once '{$backPath}app/Config/config.php';",
            "require_once '{$backPath}app/Config/config.php';",
            "require_once '{$backPath}app/Config/config.php';", 
            "require_once '{$backPath}themes/default/views/partials/header.php';",
            "require_once '{$backPath}themes/default/views/partials/header.php';",
            "require_once '{$backPath}themes/default/views/partials/header.php';",
            "require_once '{$backPath}app/Config/db.php';",
            "require_once '{$backPath}app/Config/db.php';",
            "require_once '{$backPath}app/Config/db.php';"
        ], $content);
        
        if ($newContent !== $content) {
            if (file_put_contents($filepath, $newContent)) {
                $updated++;
                echo "  ✅ Updated\n";
            } else {
                $errors++;
                echo "  ❌ Failed to write\n";
            }
        } else {
            echo "  ⚠️  No changes needed\n";
        }
    }
}

echo "\n=== RESULTS ===\n";
echo "Updated: $updated files\n";
echo "Errors: $errors files\n";
echo "✅ Module update complete!\n";
?>



