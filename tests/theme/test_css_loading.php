<?php
/**
 * Test CSS Loading Issue
 */

echo "=== CSS LOADING ISSUE ANALYSIS ===\n\n";

try {
    // Include bootstrap
    require_once 'app/bootstrap.php';
    
    // Create View instance
    $view = new \App\Core\View();
    
    echo "1. View object created successfully\n";
    
    // Create ThemeManager instance
    $themeManager = new \App\Services\ThemeManager();
    echo "2. ThemeManager created successfully\n";
    
    // Check active theme
    $activeTheme = $themeManager->getActiveTheme();
    echo "3. Active theme: " . $activeTheme . "\n";
    
    // Test theme URL generation
    $cssUrl = $themeManager->themeUrl('assets/css/procalculator-premium.css');
    echo "4. CSS URL: " . $cssUrl . "\n";
    
    // Test if CSS file exists
    $cssFilePath = BASE_PATH . '/themes/' . $activeTheme . '/assets/css/procalculator-premium.css';
    echo "5. CSS file path: " . $cssFilePath . "\n";
    echo "6. CSS file exists: " . (file_exists($cssFilePath) ? "YES" : "NO") . "\n";
    
    // Test alternative CSS path (procalculator subfolder)
    $altCssPath = BASE_PATH . '/themes/' . $activeTheme . '/assets/css/procalculator/procalculator';
    echo "7. Alternative CSS path: " . $altCssPath . "\n";
    echo "8. Alternative CSS exists: " . (file_exists($altCssPath) ? "YES" : "NO") . "\n";
    
    // List CSS files in the directory
    $cssDir = BASE_PATH . '/themes/' . $activeTheme . '/assets/css/';
    echo "9. CSS directory: " . $cssDir . "\n";
    
    if (is_dir($cssDir)) {
        $files = scandir($cssDir);
        echo "10. CSS files in directory:\n";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "    - " . $file . "\n";
            }
        }
    }
    
    echo "\n=== ANALYSIS COMPLETE ===\n";
    echo "The issue is that the theme.json references 'css/procalculator-premium.css' but the file is actually at 'css/procalculator/'\n";
    echo "SOLUTION: Update the theme.json or the CSS loading path\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>


