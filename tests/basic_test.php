<?php
// Simple test to verify theme system is working
session_start();
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/themes/default/helpers.php';

echo "Testing Bishwo Calculator Theme System...<br><br>";

try {
    $themeManager = new App\Services\ThemeManager();
    echo "✅ ThemeManager created successfully<br>";
    echo "✅ Active theme: " . $themeManager->getActiveTheme() . "<br>";
    echo "✅ Theme name: " . $themeManager->getThemeMetadata()['name'] . "<br>";
    echo "✅ CSS asset URL: " . $themeManager->getThemeAsset('css/theme.css') . "<br>";
    echo "✅ Category style (civil): " . $themeManager->getCategoryStyle('civil') . "<br>";
    echo "✅ Available themes: " . count($themeManager->getAvailableThemes()) . " found<br><br>";
    
    echo "🎉 Theme System is working perfectly!<br>";
    echo "🌐 Visit: <strong>http://localhost/bishwo_calculator/</strong> to see your website<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>
