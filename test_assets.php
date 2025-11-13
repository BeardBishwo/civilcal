<?php
/**
 * Asset Path Testing
 */

echo "=== ASSET PATH TEST ===\n\n";

// Test file existence
$testFiles = [
    'themes/default/assets/css/home.css',
    'themes/default/assets/css/theme.css', 
    'themes/default/assets/css/footer.css',
    'themes/default/assets/js/tilt.js',
    'themes/default/assets/js/back-to-top.js'
];

$basePath = __DIR__;
echo "Base Path: $basePath\n\n";

foreach ($testFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    $exists = file_exists($fullPath);
    $size = $exists ? filesize($fullPath) : 0;
    
    echo "$file:\n";
    echo "  Full path: $fullPath\n";
    echo "  Exists: " . ($exists ? "YES" : "NO") . "\n";
    if ($exists) {
        echo "  Size: " . number_format($size) . " bytes\n";
        echo "  URL: http://{$_SERVER['HTTP_HOST']}/$file\n";
    }
    echo "\n";
}

// Test direct URL access
echo "=== DIRECT URL TEST ===\n";
echo "Try these URLs directly:\n";
foreach ($testFiles as $file) {
    if (file_exists($basePath . '/' . $file)) {
        echo "http://{$_SERVER['HTTP_HOST']}/$file\n";
    }
}

echo "\n=== SERVER INFO ===\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'unknown') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'unknown') . "\n";
?>
