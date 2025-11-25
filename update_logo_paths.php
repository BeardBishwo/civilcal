<?php
require_once 'vendor/autoload.php';
require_once 'app/Config/config.php';

$db = \App\Core\Database::getInstance();

// Update site_logo setting
$stmt = $db->prepare("UPDATE settings SET setting_value = '/uploads/settings/logo.png' WHERE setting_key = 'site_logo'");
$stmt->execute();

// Update favicon setting
$stmt = $db->prepare("UPDATE settings SET setting_value = '/uploads/settings/favicon.png' WHERE setting_key = 'favicon'");
$stmt->execute();

echo "Settings updated successfully\n";

// Clear the settings cache
\App\Services\SettingsService::clearCache();

echo "Cache cleared\n";
?>