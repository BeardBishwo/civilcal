<?php
// Debug script to check logo and favicon settings
require_once 'app/Helpers/functions.php';

echo "<h1>Debug Logo & Favicon Settings</h1>";

// Test SettingsService directly
echo "<h2>SettingsService Results:</h2>";
try {
    $siteLogo = \App\Services\SettingsService::get('site_logo');
    $favicon = \App\Services\SettingsService::get('favicon');
    $siteName = \App\Services\SettingsService::get('site_name');
    
    echo "<p>Site Logo: " . ($siteLogo ?: 'NOT SET') . "</p>";
    echo "<p>Favicon: " . ($favicon ?: 'NOT SET') . "</p>";
    echo "<p>Site Name: " . ($siteName ?: 'NOT SET') . "</p>";
    
    // Test file existence for uploaded files
    if ($siteLogo && strpos($siteLogo, '/uploads/') === 0) {
        $logoPath = $_SERVER['DOCUMENT_ROOT'] . $siteLogo;
        echo "<p>Logo file exists: " . (file_exists($logoPath) ? 'YES' : 'NO') . "</p>";
        echo "<p>Logo path: " . $logoPath . "</p>";
    }
    
    if ($favicon && strpos($favicon, '/uploads/') === 0) {
        $faviconPath = $_SERVER['DOCUMENT_ROOT'] . $favicon;
        echo "<p>Favicon file exists: " . (file_exists($faviconPath) ? 'YES' : 'NO') . "</p>";
        echo "<p>Favicon path: " . $faviconPath . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>SettingsService error: " . $e->getMessage() . "</p>";
}

// Test get_site_meta function
echo "<h2>get_site_meta() Results:</h2>";
try {
    $siteMeta = get_site_meta();
    echo "<pre>" . print_r($siteMeta, true) . "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>get_site_meta() error: " . $e->getMessage() . "</p>";
}

// Check database connection
echo "<h2>Database Check:</h2>";
try {
    $db = \App\Core\Database::getInstance();
    $stmt = $db->query("SELECT COUNT(*) as count FROM settings WHERE setting_key IN ('site_logo', 'favicon', 'site_name')");
    $result = $stmt->fetch();
    echo "<p>Settings in database: " . ($result['count'] ?? 0) . "</p>";
    
    // Show actual settings
    $stmt = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('site_logo', 'favicon', 'site_name')");
    $settings = $stmt->fetchAll();
    echo "<h3>Current Settings:</h3>";
    foreach ($settings as $setting) {
        echo "<p><strong>" . $setting['setting_key'] . ":</strong> " . $setting['setting_value'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}

// Check default files
echo "<h2>Default Files Check:</h2>";
$defaultLogoPath = 'themes/default/assets/images/logo.png';
$defaultFaviconPath = 'themes/default/assets/images/favicon.png';

echo "<p>Default logo exists: " . (file_exists($defaultLogoPath) ? 'YES' : 'NO') . "</p>";
echo "<p>Default favicon exists: " . (file_exists($defaultFaviconPath) ? 'YES' : 'NO') . "</p>";

if (file_exists($defaultLogoPath)) {
    echo "<p><img src='" . $defaultLogoPath . "' style='max-width: 100px; border: 1px solid #ccc;' alt='Default Logo'></p>";
}

if (file_exists($defaultFaviconPath)) {
    echo "<p><img src='" . $defaultFaviconPath . "' style='max-width: 32px; border: 1px solid #ccc;' alt='Default Favicon'></p>";
}
?>
