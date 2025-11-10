<?php
/**
 * Test for missing methods in ThemeManager
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../app/bootstrap.php';

try {
    require_once __DIR__ . '/../app/Services/ThemeManager.php';
    
    echo "✓ ThemeManager class loaded successfully\n";
    
    $themeManager = new \App\Services\ThemeManager();
    echo "✓ ThemeManager instantiated successfully\n";
    
    // Test getThemeMetadata method
    if (method_exists($themeManager, 'getThemeMetadata')) {
        echo "✓ getThemeMetadata method exists\n";
        $metadata = $themeManager->getThemeMetadata();
        echo "✓ getThemeMetadata() returned: " . (is_array($metadata) ? 'array' : gettype($metadata)) . "\n";
    } else {
        echo "✗ getThemeMetadata method is missing\n";
    }
    
    // Test other methods
    $methods = ['getThemeConfig', 'getActiveTheme', 'getAvailableThemes'];
    foreach ($methods as $method) {
        if (method_exists($themeManager, $method)) {
            echo "✓ {$method} method exists\n";
        } else {
            echo "✗ {$method} method is missing\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
