<?php
require 'app/Config/config.php';
require 'app/Core/Database.php';
require 'app/Services/SettingsService.php';
require 'app/Helpers/functions.php';

$site_meta = get_site_meta();
$logo = $site_meta["logo"] ?? app_base_url("public/theme-assets.php?path=default/assets/images/logo.png");

echo "Logo URL: " . $logo . "\n";
echo "Full URL: " . app_base_url($logo) . "\n";
echo "Direct URL: " . $logo . "\n";
echo "Web Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Project URL: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n";
echo "Logo Variable: " . $logo . "\n";

// Check if file exists
$fullPath = __DIR__ . '/public' . $logo;
echo "Full Path: " . $fullPath . "\n";
echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";

// Try another path
$fullPath2 = __DIR__ . $logo;
echo "Alternative Path: " . $fullPath2 . "\n";
echo "File exists (alt): " . (file_exists($fullPath2) ? 'YES' : 'NO') . "\n";

// Test image URL
echo "Test Image URL: " . app_base_url($logo) . "\n";