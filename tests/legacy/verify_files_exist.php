<?php
/**
 * Verify All Required Files Exist
 */

echo "=== FILE EXISTENCE VERIFICATION ===\n\n";

$basePath = __DIR__ . '/../';

$files = [
    'CSS Files' => [
        'themes/default/assets/css/theme.css',
        'themes/default/assets/css/footer.css',
        'themes/default/assets/css/back-to-top.css',
        'themes/default/assets/css/home.css',
    ],
    'JS Files' => [
        'themes/default/assets/js/main.js',
        'themes/default/assets/js/header.js',
        'themes/default/assets/js/back-to-top.js',
        'themes/default/assets/js/tilt.js',
    ],
    'View Files' => [
        'themes/default/views/index.php',
        'themes/default/views/partials/header.php',
        'themes/default/views/partials/footer.php',
    ],
    'Config Files' => [
        'themes/default/theme.json',
        'app/Services/ThemeManager.php',
    ]
];

$allOk = true;

foreach ($files as $category => $fileList) {
    echo $category . ":\n";
    echo str_repeat("-", 50) . "\n";
    
    foreach ($fileList as $file) {
        $fullPath = $basePath . $file;
        $exists = file_exists($fullPath);
        $size = $exists ? filesize($fullPath) : 0;
        
        if ($exists) {
            echo "✓ " . $file . " (" . $size . " bytes)\n";
        } else {
            echo "✗ " . $file . " - NOT FOUND\n";
            $allOk = false;
        }
    }
    echo "\n";
}

if ($allOk) {
    echo "=== ALL FILES EXIST ✓ ===\n";
    echo "\nCSS should load correctly when accessing:\n";
    echo "  http://localhost/bishwo_calculator/\n";
} else {
    echo "=== SOME FILES MISSING ✗ ===\n";
}

?>


