<?php
// Set APP_BASE manually for testing
define('APP_BASE', '/Bishwo_Calculator');

// Include the functions file
require_once 'app/Helpers/functions.php';

// Test the app_base_url function
echo "APP_BASE: " . APP_BASE . "\n";
echo "app_base_url(): " . app_base_url() . "\n";
echo "app_base_url('uploads/settings/logo.png'): " . app_base_url('uploads/settings/logo.png') . "\n";

// Test get_site_meta function
$site_meta = get_site_meta();
echo "Site meta logo: " . $site_meta['logo'] . "\n";