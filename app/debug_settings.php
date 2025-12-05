<?php
// Debug script to check logo and favicon settings
require_once 'app/Helpers/functions.php';

echo "<h1>Debug Logo & Favicon Settings</h1>";

// Test SettingsService directly
echo "<h2>SettingsService Results:</h2>";
try {
    \ = \App\Services\SettingsService::get('site_logo');
    \ = \App\Services\SettingsService::get('favicon');
    \ = \App\Services\SettingsService::get('site_name');
    
    echo "<p>Site Logo: " . (\ ?: 'NOT SET') . "</p>";
    echo "<p>Favicon: " . (\ ?: 'NOT SET') . "</p>";
    echo "<p>Site Name: " . (\ ?: 'NOT SET') . "</p>";
    
    // Test file existence for uploaded files
    if (\ && strpos(\, '/uploads/') === 0) {
        \ = \['DOCUMENT_ROOT'] . \;
        echo "<p>Logo file exists: " . (file_exists(\) ? 'YES' : 'NO') . "</p>";
        echo "<p>Logo path: " . \ . "</p>";
    }
    
    if (\ && strpos(\, '/uploads/') === 0) {
        \ = \['DOCUMENT_ROOT'] . \;
        echo "<p>Favicon file exists: " . (file_exists(\) ? 'YES' : 'NO') . "</p>";
        echo "<p>Favicon path: " . \ . "</p>";
    }
    
} catch (Exception \) {
    echo "<p style='color: red;'>SettingsService error: " . \->getMessage() . "</p>";
}

// Test get_site_meta function
echo "<h2>get_site_meta() Results:</h2>";
try {
    \ = get_site_meta();
    echo "<pre>" . print_r(\, true) . "</pre>";
} catch (Exception \) {
    echo "<p style='color: red;'>get_site_meta() error: " . \->getMessage() . "</p>";
}

// Check database connection
echo "<h2>Database Check:</h2>";
try {
    \ = \App\Core\Database::getInstance();
    \ = \->query("SELECT COUNT(*) as count FROM settings WHERE setting_key IN ('site_logo', 'favicon', 'site_name')");
    \ = \->fetch();
    echo "<p>Settings in database: " . (\['count'] ?? 0) . "</p>";
    
    // Show actual settings
    \ = \->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('site_logo', 'favicon', 'site_name')");
    \ = \->fetchAll();
    echo "<h3>Current Settings:</h3>";
    foreach (\ as \) {
        echo "<p><strong>" . \['setting_key'] . ":</strong> " . \['setting_value'] . "</p>";
    }
    
} catch (Exception \) {
    echo "<p style='color: red;'>Database error: " . \->getMessage() . "</p>";
}

// Check default files
echo "<h2>Default Files Check:</h2>";
\themes/default/assets/images/logo.png = 'themes/default/assets/images/logo.png';
\themes/default/assets/images/favicon.png = 'themes/default/assets/images/favicon.png';

echo "<p>Default logo exists: " . (file_exists(\themes/default/assets/images/logo.png) ? 'YES' : 'NO') . "</p>";
echo "<p>Default favicon exists: " . (file_exists(\themes/default/assets/images/favicon.png) ? 'YES' : 'NO') . "</p>";

if (file_exists(\themes/default/assets/images/logo.png)) {
    echo "<p><img src='\" . \themes/default/assets/images/logo.png . \"' style='max-width: 100px; border: 1px solid #ccc;' alt='Default Logo'></p>";
}

if (file_exists(\themes/default/assets/images/favicon.png)) {
    echo "<p><img src='\" . \themes/default/assets/images/favicon.png . \"' style='max-width: 32px; border: 1px solid #ccc;' alt='Default Favicon'></p>";
}
?>
