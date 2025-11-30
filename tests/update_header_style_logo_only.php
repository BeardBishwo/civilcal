<?php
require 'app/Config/config.php';
require 'app/Core/Database.php';
require 'app/Services/SettingsService.php';

try {
    $db = App\Core\Database::getInstance();
    $stmt = $db->prepare('UPDATE settings SET setting_value = ? WHERE setting_key = ?');
    $stmt->execute(['logo_only', 'header_style']);
    
    echo "Header style updated to 'logo_only'\n";
    
    // Verify the update
    $stmt = $db->prepare('SELECT setting_key, setting_value FROM settings WHERE setting_key = ?');
    $stmt->execute(['header_style']);
    $result = $stmt->fetch();
    
    if ($result) {
        echo "Verification:\n";
        echo "Key: " . $result['setting_key'] . "\n";
        echo "Value: " . $result['setting_value'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}