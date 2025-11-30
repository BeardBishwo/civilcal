<?php
require 'app/Config/config.php';
require 'app/Core/Database.php';

try {
    $db = App\Core\Database::getInstance();
    $stmt = $db->prepare('SELECT setting_key, setting_value FROM settings WHERE setting_key = ?');
    $stmt->execute(['site_logo']);
    $result = $stmt->fetch();
    
    if ($result) {
        echo "Site logo setting found:\n";
        echo "Key: " . $result['setting_key'] . "\n";
        echo "Value: " . $result['setting_value'] . "\n";
    } else {
        echo "Site logo setting not found in database\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}