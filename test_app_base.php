<?php
require_once 'app/bootstrap.php';

echo "APP_BASE: " . (defined('APP_BASE') ? APP_BASE : 'Not defined') . "\n";
echo "APP_BASE from env: " . (getenv('APP_BASE') ?: 'Not set') . "\n";

// Test app_base_url function
echo "app_base_url(): " . app_base_url() . "\n";
echo "app_base_url('test'): " . app_base_url('test') . "\n";

// Test with site meta
$site_meta = get_site_meta();
echo "Site meta logo: " . $site_meta['logo'] . "\n";