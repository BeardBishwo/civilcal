<?php
/**
 * Debug URL Generation
 * Test script to see what URLs are being generated on different domains
 */

require_once __DIR__ . '/app/bootstrap.php';

echo "=== URL DEBUG INFO ===\n\n";

echo "Server Info:\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'not set') . "\n\n";

echo "Constants:\n";
echo "APP_BASE: '" . (defined('APP_BASE') ? APP_BASE : 'not defined') . "'\n";
echo "APP_URL: '" . (defined('APP_URL') ? APP_URL : 'not defined') . "'\n";
echo "BASE_PATH: '" . (defined('BASE_PATH') ? BASE_PATH : 'not defined') . "'\n\n";

// Test ThemeManager
try {
    $themeManager = new \App\Services\ThemeManager();
    echo "ThemeManager URLs:\n";
    echo "Active Theme: " . $themeManager->getActiveTheme() . "\n";
    echo "Theme Base URL: " . $themeManager->themeUrl() . "\n";
    echo "CSS URL: " . $themeManager->themeUrl('assets/css/home.css') . "\n";
    echo "Assets URL: " . $themeManager->assetsUrl('css/home.css') . "\n\n";
} catch (Exception $e) {
    echo "ThemeManager Error: " . $e->getMessage() . "\n\n";
}

// Test theme helpers
if (function_exists('theme_css')) {
    echo "Theme Helper URLs:\n";
    echo "theme_css('home.css'): " . theme_css('home.css') . "\n";
    echo "app_base_url(): " . app_base_url() . "\n";
    echo "app_base_url('test'): " . app_base_url('test') . "\n\n";
}

// Test actual file existence
$testPaths = [
    'themes/default/assets/css/home.css',
    'themes/default/assets/js/tilt.js'
];

echo "File Existence Check:\n";
foreach ($testPaths as $path) {
    $fullPath = BASE_PATH . '/' . $path;
    echo "$path: " . (file_exists($fullPath) ? "EXISTS" : "NOT FOUND") . "\n";
}

echo "\n=== END DEBUG ===\n";
?>
