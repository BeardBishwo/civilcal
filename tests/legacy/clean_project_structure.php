<?php
/**
 * Clean Bishwo Calculator Project Structure Generator
 * Excludes vendor folder, shows dependencies, generates markdown report
 */

function generateDirectoryTree($directory, $prefix = '', $isLast = true, &$output = []) {
    // Skip vendor and .git folders
    if (basename($directory) === 'vendor') {
        return;
    }
    
    // Get all items in the directory
    $files = scandir($directory);
    
    // Remove . and .. from the list
    $files = array_diff($files, ['.', '..']);
    
    // Sort files and folders
    sort($files);
    
    $fileCount = count($files);
    $count = 0;
    
    foreach ($files as $file) {
        $count++;
        $path = $directory . DIRECTORY_SEPARATOR . $file;
        $isDirectory = is_dir($path);
        $isLastItem = ($count === $fileCount);
        
        // Handle .git folder specially - just show it as a line without recursing
        if ($file === '.git') {
            if ($isLastItem) {
                $output[] = $prefix . "└── .git";
            } else {
                $output[] = $prefix . "├── .git";
            }
            continue;
        }
        
        // Create the tree line
        if ($isLastItem) {
            $output[] = $prefix . "└── " . $file;
            $newPrefix = $prefix . "    ";
        } else {
            $output[] = $prefix . "├── " . $file;
            $newPrefix = $prefix . "│   ";
        }
        
        // If it's a directory, recurse into it
        if ($isDirectory) {
            generateDirectoryTree($path, $newPrefix, $isLastItem, $output);
        }
    }
}

function countFilesAndDirectories($directory, $excludeVendor = true) {
    $counts = ['files' => 0, 'directories' => 0];
    
    if ($excludeVendor && basename($directory) === 'vendor') {
        return $counts;
    }
    
    $items = scandir($directory);
    $items = array_diff($items, ['.', '..']);
    
    foreach ($items as $item) {
        $path = $directory . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            // Skip .git folder for counting too
            if ($item === '.git') {
                $counts['directories']++;
                continue;
            }
            $counts['directories']++;
            $subCounts = countFilesAndDirectories($path, $excludeVendor);
            $counts['files'] += $subCounts['files'];
            $counts['directories'] += $subCounts['directories'];
        } else {
            $counts['files']++;
        }
    }
    
    return $counts;
}

function getComposerDependencies() {
    $composerPath = __DIR__ . '/../composer.json';
    if (!file_exists($composerPath)) {
        return '';
    }
    
    $composerContent = file_get_contents($composerPath);
    $composer = json_decode($composerContent, true);
    
    if (!$composer || !isset($composer['require'])) {
        return '';
    }
    
    $output = "vendor{\n";
    $output .= "  \"require\": {\n";
    
    $dependencies = $composer['require'];
    // Remove PHP version requirement
    unset($dependencies['php']);
    
    $depCount = count($dependencies);
    $count = 0;
    
    foreach ($dependencies as $package => $version) {
        $count++;
        $isLast = ($count === $depCount);
        $output .= "    \"$package\": \"$version\"" . ($isLast ? "" : ",") . "\n";
    }
    
    $output .= "  }\n";
    $output .= "}\n";
    
    return $output;
}

// Start generating report
echo "🗂️  BISHWO CALCULATOR - CLEAN PROJECT STRUCTURE\n";
echo "==================================================\n";
echo "Generated: " . date('Y-m-d H:i:s') . "\n\n";

$currentDir = __DIR__ . '/..';
$output = [];

// Generate directory tree
echo "bishwo_calculator/\n";
generateDirectoryTree($currentDir, '', true, $output);
echo implode("\n", $output) . "\n";

// Add vendor dependencies
echo "\n";
echo "📦 VENDOR DEPENDENCIES:\n";
echo "======================\n";
$dependencies = getComposerDependencies();
echo $dependencies . "\n";

// Calculate statistics
$counts = countFilesAndDirectories($currentDir, true);
echo "==================================================\n";
echo "📊 STRUCTURE SUMMARY (Excluding Vendor)\n";
echo "==================================================\n";
echo "Total Files: " . $counts['files'] . "\n";
echo "Total Directories: " . $counts['directories'] . "\n";
echo "Total Items: " . ($counts['files'] + $counts['directories']) . "\n";

// Generate markdown report content
$reportContent = "# Bishwo Calculator - Project Structure Report\n\n";
$reportContent .= "**Generated:** " . date('Y-m-d H:i:s') . "\n\n";
$reportContent .= "## Project Structure\n\n";
$reportContent .= "```\n";
$reportContent .= "bishwo_calculator/\n";
$reportContent .= implode("\n", $output) . "\n";
$reportContent .= "```\n\n";
$reportContent .= "## Vendor Dependencies\n\n";
$reportContent .= "```json\n";
$reportContent .= $dependencies;
$reportContent .= "```\n\n";
$reportContent .= "## Statistics\n\n";
$reportContent .= "- **Total Files:** " . $counts['files'] . "\n";
$reportContent .= "- **Total Directories:** " . $counts['directories'] . "\n";
$reportContent .= "- **Total Items:** " . ($counts['files'] + $counts['directories']) . "\n";
$reportContent .= "- **Excludes:** vendor/ folder\n\n";
$reportContent .= "---\n";
$reportContent .= "*This report excludes the vendor folder to provide a clean view of the main project structure.*\n";

// Write to file
$reportFile = __DIR__ . '/../structurereport.md';
file_put_contents($reportFile, $reportContent);

echo "\n📄 Report saved to: structurereport.md\n";
echo "\n🎉 Clean structure generation complete!\n";
?>


