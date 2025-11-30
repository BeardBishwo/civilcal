<?php
// Simulate web environment
$_SERVER['SCRIPT_NAME'] = '/Bishwo_Calculator/index.php';

// Load the application
require_once 'app/bootstrap.php';
require_once 'app/Helpers/functions.php';

// Clear cache to get fresh data
\App\Services\SettingsService::clearCache();

// Get site meta data
$site_meta = get_site_meta();

echo "Header style: " . ($site_meta['header_style'] ?? 'not set') . "\n";
echo "Logo URL: " . ($site_meta['logo'] ?? 'not set') . "\n";
echo "Favicon URL: " . ($site_meta['favicon'] ?? 'not set') . "\n";

// Test app_base_url function
echo "app_base_url(): " . app_base_url() . "\n";
echo "APP_BASE: " . (defined('APP_BASE') ? APP_BASE : 'not defined') . "\n";