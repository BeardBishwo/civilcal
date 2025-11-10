<?php
/**
 * Test Theme Styles Loading
 */

echo "=== THEME STYLES LOADING TEST ===\n\n";

try {
    // Include bootstrap
    require_once 'app/bootstrap.php';
    
    // Create ThemeManager instance
    $themeManager = new \App\Services\ThemeManager();
    
    echo "1. ThemeManager created successfully\n";
    echo "2. Active theme: " . $themeManager->getActiveTheme() . "\n";
    
    // Get theme metadata
    $metadata = $themeManager->getThemeMetadata();
    echo "3. Theme metadata loaded: " . (count($metadata) > 0 ? "YES" : "NO") . "\n";
    
    // Test if theme config has styles
    if (isset($metadata['config']['styles'])) {
        echo "4. Theme config has styles: YES\n";
        echo "5. Configured styles:\n";
        foreach ($metadata['config']['styles'] as $style) {
            echo "   - " . $style . "\n";
            
            // Test each style file
            $fullPath = BASE_PATH . '/themes/' . $themeManager->getActiveTheme() . '/' . $style;
            echo "     File exists: " . (file_exists($fullPath) ? "YES" : "NO") . "\n";
            echo "     Full path: " . $fullPath . "\n";
        }
    } else {
        echo "4. Theme config has styles: NO\n";
    }
    
    // Test loadThemeStyles method
    echo "6. Testing loadThemeStyles method:\n";
    ob_start();
    $themeManager->loadThemeStyles();
    $output = ob_get_clean();
    echo "   Output: " . (!empty($output) ? $output : "No output") . "\n";
    
    // Check if the theme has a theme.json file
    $themeJsonPath = BASE_PATH . '/themes/' . $themeManager->getActiveTheme() . '/theme.json';
    echo "7. Theme JSON path: " . $themeJsonPath . "\n";
    echo "8. Theme JSON exists: " . (file_exists($themeJsonPath) ? "YES" : "NO") . "\n";
    
    if (file_exists($themeJsonPath)) {
        $themeConfig = json_decode(file_get_contents($themeJsonPath), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "9. Theme JSON is valid: YES\n";
            echo "10. Theme styles from JSON:\n";
            if (isset($themeConfig['styles'])) {
                foreach ($themeConfig['styles'] as $style) {
                    echo "    - " . $style . "\n";
                    $fullPath = BASE_PATH . '/themes/' . $themeManager->getActiveTheme() . '/' . $style;
                    echo "      File exists: " . (file_exists($fullPath) ? "YES" : "NO") . "\n";
                }
            }
        } else {
            echo "9. Theme JSON is valid: NO\n";
        }
    }
    
    echo "\n=== ANALYSIS COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
