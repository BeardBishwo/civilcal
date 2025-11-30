<?php
require 'app/Config/config.php';
require 'app/Core/Database.php';

try {
    $db = App\Core\Database::getInstance();
    $stmt = $db->prepare('SELECT setting_key, setting_value FROM settings WHERE setting_key = ?');
    $stmt->execute(['header_style']);
    $result = $stmt->fetch();
    
    if ($result) {
        echo "Header style setting found:\n";
        echo "Key: " . $result['setting_key'] . "\n";
        echo "Value: " . $result['setting_value'] . "\n";
    } else {
        echo "Header style setting not found in database\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}