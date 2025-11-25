<?php
// Test file to verify logo and favicon URLs
require_once __DIR__ . '/../../app/Config/config.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

// Start session
session_start();

echo "<!DOCTYPE html>\n";
echo "<html lang='en'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>Logo and Favicon Test</title>\n";
echo "    <style>\n";
echo "        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }\n";
echo "        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }\n";
echo "        h1 { color: #333; border-bottom: 3px solid #4f46e5; padding-bottom: 10px; }\n";
echo "        h2 { color: #4f46e5; margin-top: 30px; }\n";
echo "        .info-box { background: #f0f9ff; border-left: 4px solid #4f46e5; padding: 15px; margin: 15px 0; }\n";
echo "        .success { background: #f0fdf4; border-left-color: #10b981; }\n";
echo "        .error { background: #fef2f2; border-left-color: #ef4444; }\n";
echo "        .warning { background: #fffbeb; border-left-color: #f59e0b; }\n";
echo "        code { background: #e5e7eb; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }\n";
echo "        table { width: 100%; border-collapse: collapse; margin: 20px 0; }\n";
echo "        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }\n";
echo "        th { background: #f9fafb; font-weight: 600; color: #374151; }\n";
echo "        img { max-width: 200px; height: auto; border: 1px solid #e5e7eb; padding: 5px; background: white; }\n";
echo "        .favicon-display { width: 32px; height: 32px; }\n";
echo "        .logo-display { max-height: 60px; width: auto; }\n";
echo "        .visual-test { margin: 20px 0; padding: 20px; background: #f9fafb; border-radius: 8px; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <div class='container'>\n";
echo "        <h1>🔍 Logo and Favicon Test Report</h1>\n";

// Get site meta
$site_meta = get_site_meta();

echo "        <h2>📋 Configuration Values</h2>\n";
echo "        <table>\n";
echo "            <tr><th>Setting</th><th>Value</th></tr>\n";
echo "            <tr><td><strong>APP_BASE</strong></td><td><code>" . htmlspecialchars(APP_BASE) . "</code></td></tr>\n";
echo "            <tr><td><strong>Document Root</strong></td><td><code>" . htmlspecialchars($_SERVER['DOCUMENT_ROOT']) . "</code></td></tr>\n";
echo "            <tr><td><strong>HTTP Host</strong></td><td><code>" . htmlspecialchars($_SERVER['HTTP_HOST']) . "</code></td></tr>\n";
echo "            <tr><td><strong>Request URI</strong></td><td><code>" . htmlspecialchars($_SERVER['REQUEST_URI']) . "</code></td></tr>\n";
echo "        </table>\n";

echo "        <h2>🎨 Site Meta Information</h2>\n";
echo "        <table>\n";
echo "            <tr><th>Key</th><th>Value</th></tr>\n";
echo "            <tr><td><strong>Site Title</strong></td><td>" . htmlspecialchars($site_meta['title'] ?? 'N/A') . "</td></tr>\n";
echo "            <tr><td><strong>Logo Text</strong></td><td>" . htmlspecialchars($site_meta['logo_text'] ?? 'N/A') . "</td></tr>\n";
echo "            <tr><td><strong>Header Style</strong></td><td>" . htmlspecialchars($site_meta['header_style'] ?? 'N/A') . "</td></tr>\n";
echo "            <tr><td><strong>Logo (Raw)</strong></td><td><code>" . htmlspecialchars($site_meta['logo'] ?? 'N/A') . "</code></td></tr>\n";
echo "            <tr><td><strong>Favicon (Raw)</strong></td><td><code>" . htmlspecialchars($site_meta['favicon'] ?? 'N/A') . "</code></td></tr>\n";
echo "        </table>\n";

// Process logo URL
$logoRaw = $site_meta['logo'] ?? '/assets/icons/icon-192.png';
if (!empty($logoRaw) && preg_match('#^https?://#', $logoRaw)) {
    $logo = $logoRaw;
} else {
    $logo = app_base_url(ltrim($logoRaw, '/'));
}

// Process favicon URL
$faviconRaw = $site_meta['favicon'] ?? '/assets/icons/favicon.ico';
if (!empty($faviconRaw) && preg_match('#^https?://#', $faviconRaw)) {
    $favicon = $faviconRaw;
} else {
    $favicon = app_base_url(ltrim($faviconRaw, '/'));
}

echo "        <h2>🔗 Generated URLs</h2>\n";
echo "        <table>\n";
echo "            <tr><th>Type</th><th>Generated URL</th></tr>\n";
echo "            <tr><td><strong>Logo URL</strong></td><td><code>" . htmlspecialchars($logo) . "</code></td></tr>\n";
echo "            <tr><td><strong>Favicon URL</strong></td><td><code>" . htmlspecialchars($favicon) . "</code></td></tr>\n";
echo "        </table>\n";

// Check file existence
$logoPath = __DIR__ . '/public' . $logoRaw;
$faviconPath = __DIR__ . '/public' . $faviconRaw;

echo "        <h2>📁 File System Check</h2>\n";
echo "        <table>\n";
echo "            <tr><th>File</th><th>Path</th><th>Exists</th><th>Size</th></tr>\n";

$logoExists = file_exists($logoPath);
$logoSize = $logoExists ? filesize($logoPath) : 0;
$logoClass = $logoExists ? 'success' : 'error';

echo "            <tr class='" . $logoClass . "'>\n";
echo "                <td><strong>Logo</strong></td>\n";
echo "                <td><code>" . htmlspecialchars($logoPath) . "</code></td>\n";
echo "                <td>" . ($logoExists ? '✅ Yes' : '❌ No') . "</td>\n";
echo "                <td>" . ($logoExists ? number_format($logoSize / 1024, 2) . ' KB' : 'N/A') . "</td>\n";
echo "            </tr>\n";

$faviconExists = file_exists($faviconPath);
$faviconSize = $faviconExists ? filesize($faviconPath) : 0;
$faviconClass = $faviconExists ? 'success' : 'error';

echo "            <tr class='" . $faviconClass . "'>\n";
echo "                <td><strong>Favicon</strong></td>\n";
echo "                <td><code>" . htmlspecialchars($faviconPath) . "</code></td>\n";
echo "                <td>" . ($faviconExists ? '✅ Yes' : '❌ No') . "</td>\n";
echo "                <td>" . ($faviconExists ? number_format($faviconSize / 1024, 2) . ' KB' : 'N/A') . "</td>\n";
echo "            </tr>\n";
echo "        </table>\n";

echo "        <h2>🎭 Visual Test</h2>\n";
echo "        <div class='visual-test'>\n";
echo "            <h3>Logo Display:</h3>\n";
if ($logoExists) {
    echo "            <img src='" . htmlspecialchars($logo) . "' alt='Logo' class='logo-display' onerror='this.style.border=\"3px solid red\"; this.alt=\"Failed to load logo\"'>\n";
    echo "            <p>✅ Logo file exists and should display above</p>\n";
} else {
    echo "            <p class='error'>❌ Logo file does not exist at: <code>" . htmlspecialchars($logoPath) . "</code></p>\n";
}

echo "            <h3>Favicon Display:</h3>\n";
if ($faviconExists) {
    echo "            <img src='" . htmlspecialchars($favicon) . "' alt='Favicon' class='favicon-display' onerror='this.style.border=\"3px solid red\"; this.alt=\"Failed to load favicon\"'>\n";
    echo "            <p>✅ Favicon file exists and should display above</p>\n";
    echo "            <p><em>Note: Check your browser tab for the favicon icon</em></p>\n";
} else {
    echo "            <p class='error'>❌ Favicon file does not exist at: <code>" . htmlspecialchars($faviconPath) . "</code></p>\n";
}
echo "        </div>\n";

echo "        <h2>🔧 Logo Settings</h2>\n";
if (isset($site_meta['logo_settings']) && is_array($site_meta['logo_settings'])) {
    echo "        <table>\n";
    echo "            <tr><th>Setting</th><th>Value</th></tr>\n";
    foreach ($site_meta['logo_settings'] as $key => $value) {
        echo "            <tr><td><strong>" . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . "</strong></td><td>" . htmlspecialchars(is_bool($value) ? ($value ? 'true' : 'false') : $value) . "</td></tr>\n";
    }
    echo "        </table>\n";
} else {
    echo "        <p class='warning'>⚠️ No logo settings found</p>\n";
}

echo "        <h2>✅ Recommendations</h2>\n";
echo "        <div class='info-box'>\n";

if ($logoExists && $faviconExists) {
    echo "            <p><strong>✅ All checks passed!</strong></p>\n";
    echo "            <ul>\n";
    echo "                <li>Logo and favicon files exist</li>\n";
    echo "                <li>URLs are being generated correctly</li>\n";
    echo "                <li>Files should display properly on your website</li>\n";
    echo "            </ul>\n";
} else {
    echo "            <p><strong>⚠️ Action Required:</strong></p>\n";
    echo "            <ul>\n";
    if (!$logoExists) {
        echo "                <li>❌ Upload a logo file to: <code>public/assets/icons/icon-192.png</code></li>\n";
    }
    if (!$faviconExists) {
        echo "                <li>❌ Upload a favicon file to: <code>public/assets/icons/favicon.ico</code></li>\n";
    }
    echo "            </ul>\n";
}

echo "        </div>\n";

echo "        <h2>📝 Additional Information</h2>\n";
echo "        <div class='info-box'>\n";
echo "            <ul>\n";
echo "                <li><strong>Favicon Format:</strong> Should be .ico format (32x32 pixels recommended)</li>\n";
echo "                <li><strong>Logo Format:</strong> PNG recommended (transparent background works best)</li>\n";
echo "                <li><strong>Logo Size:</strong> Current setting: " . htmlspecialchars($site_meta['logo_settings']['logo_height'] ?? '40px') . "</li>\n";
echo "                <li><strong>Alternative:</strong> You can change logo/favicon paths in <code>app/db/site_meta.json</code></li>\n";
echo "            </ul>\n";
echo "        </div>\n";

echo "        <hr style='margin: 30px 0;'>\n";
echo "        <p style='text-align: center; color: #6b7280;'><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
