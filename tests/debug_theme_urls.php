<?php
require_once __DIR__ . '/app/Config/config.php';
require_once __DIR__ . '/app/Helpers/functions.php';

echo "=== THEME URL DEBUG ===\n\n";

try {
    $themeManager = new \App\Services\ThemeManager();
    
    echo "ThemeManager Base URL: " . $themeManager->getBaseUrl() . "\n\n";
    
    $cssFiles = ['theme.css', 'footer.css', 'back-to-top.css', 'home.css'];
    echo "Generated CSS URLs:\n";
    foreach ($cssFiles as $css) {
        $url = $themeManager->themeUrl('assets/css/' . $css);
        echo "  $css → $url\n";
        
        // Check if file exists
        $filePath = __DIR__ . '/themes/default/assets/css/' . $css;
        $exists = file_exists($filePath) ? "✅ EXISTS" : "❌ MISSING";
        echo "    File: $filePath → $exists\n\n";
    }
    
    echo "Testing direct URLs (copy these to browser):\n";
    foreach ($cssFiles as $css) {
        echo "http://bishwo_calculator.test/themes/default/assets/css/$css\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>


