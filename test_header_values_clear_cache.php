<?php
require 'app/Config/config.php';
require 'app/Core/Database.php';
require 'app/Services/SettingsService.php';
require 'app/Helpers/functions.php';

// Clear the cache to ensure we get fresh data
\App\Services\SettingsService::clearCache();

$site_meta = get_site_meta();
$logo = $site_meta["logo"] ?? app_base_url("public/theme-assets.php?path=default/assets/images/logo.png");
$logo_text = $site_meta["logo_text"] ?? (\App\Services\SettingsService::get('site_name', 'Bishwo Calculator') ?: 'Bishwo Calculator');
$header_style = $site_meta["header_style"] ?? "logo_only";

echo "Logo: " . $logo . "\n";
echo "Logo Text: " . $logo_text . "\n";
echo "Header Style: " . $header_style . "\n";

// Test the conditions
echo "Should hide logo (text_only): " . ($header_style === 'text_only' ? 'YES' : 'NO') . "\n";
echo "Should hide text (logo_only): " . ($header_style === 'logo_only' ? 'YES' : 'NO') . "\n";