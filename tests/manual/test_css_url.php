<?php
require_once __DIR__ . '/app/bootstrap.php';

$themeManager = new \App\Services\ThemeManager();

echo "ThemeManager Test\n";
echo "=================\n\n";

echo "Active Theme: " . $themeManager->getActiveTheme() . "\n\n";

$cssUrl = $themeManager->themeUrl('assets/css/theme.css?v=123');
echo "Generated CSS URL:\n";
echo $cssUrl . "\n\n";

// Check if theme-assets.php exists
$publicProxy = __DIR__ . '/public/theme-assets.php';
echo "theme-assets.php exists: " . (file_exists($publicProxy) ? 'YES' : 'NO') . "\n";

// Check if CSS file exists
$cssFile = __DIR__ . '/themes/default/assets/css/theme.css';
echo "theme.css exists: " . (file_exists($cssFile) ? 'YES' : 'NO') . "\n";

// Check DOCUMENT_ROOT
echo "\nServer Info:\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'not set') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "\n";
echo "BASE_PATH: " . BASE_PATH . "\n";
?>
