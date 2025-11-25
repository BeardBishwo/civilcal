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

echo "<!DOCTYPE html>
<html>
<head>
    <title>Logo Display Debug</title>
</head>
<body>
    <h1>Logo Display Debug</h1>
    
    <h2>Current Settings</h2>
    <p>Header Style: " . htmlspecialchars($header_style) . "</p>
    <p>Logo URL: " . htmlspecialchars($logo) . "</p>
    <p>Logo Text: " . htmlspecialchars($logo_text) . "</p>
    
    <h2>Simulated Header</h2>
    <div style='border: 1px solid #ccc; padding: 20px;'>
        <a href='#' style='text-decoration: none; display: flex; align-items: center; gap: 12px;'>
            <img src='" . htmlspecialchars($logo) . "' 
                 alt='Logo' 
                 style='" . ($header_style === 'text_only' ? 'display: none;' : '') . " max-width: 200px; height: auto;'
                 onerror='console.log(\"Logo failed to load\"); this.style.display=\"none\"; this.parentNode.querySelector(\".logo-text\").style.display=\"block\";'
                 onload='console.log(\"Logo loaded successfully\");'>
            <span class='logo-text' style='" . ($header_style === 'logo_only' ? 'display: none;' : 'display: block;') . " font-weight: 700; font-size: 1.5rem; color: #2d3748;'>" . htmlspecialchars($logo_text) . "</span>
        </a>
    </div>
    
    <h2>Direct Image Test</h2>
    <img src='" . htmlspecialchars($logo) . "' alt='Direct Logo' style='max-width: 200px; height: auto;'>
    
    <script>
        // Check if image loads
        const img = new Image();
        img.onload = function() {
            console.log('Image loaded successfully in test');
        };
        img.onerror = function() {
            console.log('Image failed to load in test');
        };
        img.src = '" . htmlspecialchars($logo) . "';
    </script>
</body>
</html>";