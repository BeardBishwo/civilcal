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

echo "<h2>Debug Information</h2>\n";
echo "<p>Logo URL: " . htmlspecialchars($logo) . "</p>\n";
echo "<p>Logo Text: " . htmlspecialchars($logo_text) . "</p>\n";
echo "<p>Header Style: " . htmlspecialchars($header_style) . "</p>\n";

echo "<h2>Simulated Header Output</h2>\n";
echo "<div style='border: 1px solid #ccc; padding: 10px;'>\n";
echo "  <a href='#' style='text-decoration: none; display: flex; align-items: center; gap: 12px;'>\n";

// Simulate the logo image
$logoDisplay = ($header_style === 'text_only') ? 'display: none;' : '';
echo "    <img src='" . htmlspecialchars($logo) . "' alt='Logo' style='" . $logoDisplay . " max-width: 100px; height: 40px; object-fit: contain;'>\n";

// Simulate the logo text
$textDisplay = ($header_style === 'logo_only') ? 'display: none;' : 'display: block;';
echo "    <span style='" . $textDisplay . " font-weight: 700; font-size: 1.5rem; color: #2d3748;'>" . htmlspecialchars($logo_text) . "</span>\n";

echo "  </a>\n";
echo "</div>\n";

echo "<h2>Expected Behavior</h2>\n";
if ($header_style === 'logo_only') {
    echo "<p>Only the logo image should be visible, and the text should be hidden.</p>\n";
} elseif ($header_style === 'text_only') {
    echo "<p>Only the text should be visible, and the logo image should be hidden.</p>\n";
} else {
    echo "<p>Both logo and text should be visible.</p>\n";
}