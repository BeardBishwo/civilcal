<?php
require_once 'app/Config/config.php';
require_once 'app/Config/db.php';

try {
    $pdo = get_db();
    if ($pdo) {
        echo "Database connection successful\n";
        
        // Check if site_settings table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'site_settings'");
        if ($stmt->rowCount() > 0) {
            echo "site_settings table exists\n";
            
            // Get email settings
            $stmt = $pdo->query('SELECT * FROM site_settings WHERE setting_key LIKE "email_%"');
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($settings)) {
                echo "Current email settings:\n";
                foreach ($settings as $setting) {
                    echo "  " . $setting['setting_key'] . " = " . $setting['setting_value'] . "\n";
                }
            } else {
                echo "No email settings found in database\n";
            }
        } else {
            echo "site_settings table does not exist\n";
        }
    } else {
        echo "Database connection failed\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>