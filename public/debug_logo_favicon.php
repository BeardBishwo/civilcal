<?php
// Load the autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load configuration
require_once dirname(__DIR__) . '/app/Config/config.php';
require_once dirname(__DIR__) . '/app/Helpers/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Get site meta
$site_meta = get_site_meta();

// Get raw values
$site_logo = \App\Services\SettingsService::get('site_logo');
$favicon = \App\Services\SettingsService::get('favicon');

echo "<h1>Logo and Favicon Debug Information</h1>\n";
echo "<p><strong>Site Meta Array:</strong></p>\n";
echo "<pre>" . htmlspecialchars(print_r($site_meta, true)) . "</pre>\n";

echo "<p><strong>Raw Settings Values:</strong></p>\n";
echo "<ul>\n";
echo "<li>site_logo setting: " . htmlspecialchars($site_logo) . "</li>\n";
echo "<li>favicon setting: " . htmlspecialchars($favicon) . "</li>\n";
echo "</ul>\n";

echo "<p><strong>Constructed URLs:</strong></p>\n";
echo "<ul>\n";
echo "<li>Logo URL: " . htmlspecialchars($site_meta['logo'] ?? 'N/A') . "</li>\n";
echo "<li>Favicon URL: " . htmlspecialchars($site_meta['favicon'] ?? 'N/A') . "</li>\n";
echo "</ul>\n";

echo "<p><strong>File Existence Check:</strong></p>\n";
$publicDir = dirname(__DIR__) . '/public';
$logoPath = $publicDir . ($site_meta['logo'] ?? '/assets/images/logo.png');
$faviconPath = $publicDir . ($site_meta['favicon'] ?? '/assets/icons/favicon.ico');

echo "<ul>\n";
echo "<li>Logo file path: " . htmlspecialchars($logoPath) . " - " . (file_exists($logoPath) ? 'EXISTS' : 'NOT FOUND') . "</li>\n";
echo "<li>Favicon file path: " . htmlspecialchars($faviconPath) . " - " . (file_exists($faviconPath) ? 'EXISTS' : 'NOT FOUND') . "</li>\n";
echo "</ul>\n";

echo "<p><strong>Direct Access Test:</strong></p>\n";
echo "<ul>\n";
echo "<li><a href='" . htmlspecialchars(app_base_url('uploads/settings/logo.png')) . "'>Test Logo Link</a></li>\n";
echo "<li><a href='" . htmlspecialchars(app_base_url('uploads/settings/favicon.png')) . "'>Test Favicon Link</a></li>\n";
echo "</ul>\n";

echo "<p><strong>Image Display Test:</strong></p>\n";
echo "<div style='display:flex; gap:20px;'>\n";
echo "  <div>\n";
echo "    <h3>Logo</h3>\n";
echo "    <img src='" . htmlspecialchars(app_base_url('uploads/settings/logo.png')) . "' alt='Logo' style='max-width:200px; border:1px solid #ccc;'>\n";
echo "    <p>URL: " . htmlspecialchars(app_base_url('uploads/settings/logo.png')) . "</p>\n";
echo "  </div>\n";
echo "  <div>\n";
echo "    <h3>Favicon</h3>\n";
echo "    <img src='" . htmlspecialchars(app_base_url('uploads/settings/favicon.png')) . "' alt='Favicon' style='max-width:200px; border:1px solid #ccc;'>\n";
echo "    <p>URL: " . htmlspecialchars(app_base_url('uploads/settings/favicon.png')) . "</p>\n";
echo "  </div>\n";
echo "</div>\n";

// Test if images are accessible
$logoUrl = app_base_url('uploads/settings/logo.png');
$faviconUrl = app_base_url('uploads/settings/favicon.png');

// Get file sizes
$logoPath = dirname(__DIR__) . '/public/uploads/settings/logo.png';
$faviconPath = dirname(__DIR__) . '/public/uploads/settings/favicon.png';

echo "<p><strong>Image Accessibility Test:</strong></p>\n";
echo "<ul>\n";
echo "<li>Logo accessible: " . (file_get_contents($logoUrl) !== false ? 'YES' : 'NO') . " (Size: " . (file_exists($logoPath) ? filesize($logoPath) . ' bytes' : 'N/A') . ")</li>\n";
echo "<li>Favicon accessible: " . (file_get_contents($faviconUrl) !== false ? 'YES' : 'NO') . " (Size: " . (file_exists($faviconPath) ? filesize($faviconPath) . ' bytes' : 'N/A') . ")</li>\n";
echo "</ul>\n";
?>