<?php
/**
 * Final method existence check for ThemeManager
 */

require_once __DIR__ . '/../app/bootstrap.php';

try {
    echo "=== Final ThemeManager Method Check ===\n";
    
    $themeManager = new \App\Services\ThemeManager();
    
    // Check all methods that View.php is trying to call
    $requiredMethods = [
        'renderPartial',
        'renderView', 
        'loadCategoryStyle',
        'setTheme',
        'getThemeAsset',
        'getThemeMetadata',
        'getAvailableThemes',
        'loadThemeStyles',
        'loadThemeScripts',
        'getActiveTheme',
        'getThemeConfig',
        'themeUrl',
        'assetsUrl'
    ];
    
    $allFound = true;
    foreach ($requiredMethods as $method) {
        if (method_exists($themeManager, $method)) {
            echo "✓ $method exists\n";
        } else {
            echo "✗ $method MISSING\n";
            $allFound = false;
        }
    }
    
    if ($allFound) {
        echo "\n🎉 All required methods found! HTTP 500 should be resolved.\n";
    } else {
        echo "\n❌ Some methods are still missing.\n";
    }
    
} catch (Error $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>


