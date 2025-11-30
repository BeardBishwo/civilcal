<?php
require 'app/Config/config.php';
require 'app/Core/Database.php';
require 'app/Services/SettingsService.php';
require 'app/Helpers/functions.php';

// Clear the cache to ensure we get fresh data
\App\Services\SettingsService::clearCache();

// Test the app_base_url function
echo "APP_BASE: " . (defined('APP_BASE') ? APP_BASE : 'Not defined') . "\n";

// Test what get_site_meta returns
$site_meta = get_site_meta();
echo "Site Meta Logo: " . ($site_meta["logo"] ?? 'Not set') . "\n";

// Test the app_base_url function directly
$test_path = "/uploads/settings/logo.png";
$full_url = app_base_url($test_path);
echo "Full URL: " . $full_url . "\n";