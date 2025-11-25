<?php

/**
 * Migration: Set default logo and favicon
 * Sets the default logo and favicon for the SaaS script
 */

class SetDefaultLogoFavicon {
    
    public function up($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        try {
            // Update or insert default site_logo setting
            $stmt = $pdo->prepare("
                INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, setting_category, description, is_public, default_value, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    setting_value = VALUES(setting_value),
                    setting_type = VALUES(setting_type),
                    setting_group = VALUES(setting_group),
                    setting_category = VALUES(setting_category),
                    description = VALUES(description),
                    is_public = VALUES(is_public),
                    default_value = VALUES(default_value),
                    display_order = VALUES(display_order)
            ");
            
            // Update site_logo in general group
            $stmt->execute([
                'site_logo', 
                '/uploads/settings/logo.png', 
                'image', 
                'general', 
                'site_identity', 
                'Site logo', 
                1, 
                '/uploads/settings/logo.png', 
                6
            ]);
            
            // Update favicon in general group
            $stmt->execute([
                'favicon', 
                '/uploads/settings/favicon.png', 
                'image', 
                'general', 
                'site_identity', 
                'Site favicon', 
                1, 
                '/uploads/settings/favicon.png', 
                7
            ]);
            
            // Update logo in appearance group
            $stmt->execute([
                'logo', 
                '/uploads/settings/logo.png', 
                'image', 
                'appearance', 
                'branding', 
                'Site logo', 
                1, 
                '/uploads/settings/logo.png', 
                2
            ]);
            
            // Update favicon in appearance group
            $stmt->execute([
                'favicon', 
                '/uploads/settings/favicon.png', 
                'image', 
                'appearance', 
                'branding', 
                'Site favicon', 
                1, 
                '/uploads/settings/favicon.png', 
                3
            ]);
            
            echo "✓ Default logo and favicon settings updated successfully\n";
            
        } catch (Exception $e) {
            echo "Error updating default logo and favicon settings: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    public function down($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        try {
            // Revert to previous default values
            $stmt = $pdo->prepare("
                UPDATE settings 
                SET setting_value = ?, default_value = ?
                WHERE setting_key = ? AND setting_group = ?
            ");
            
            // Revert site_logo in general group
            $stmt->execute([
                '/assets/images/logo.png',
                '/assets/images/logo.png',
                'site_logo',
                'general'
            ]);
            
            // Revert favicon in general group
            $stmt->execute([
                '/assets/images/favicon.ico',
                '/assets/images/favicon.ico',
                'favicon',
                'general'
            ]);
            
            // Revert logo in appearance group
            $stmt->execute([
                '/assets/images/logo.png',
                '/assets/images/logo.png',
                'logo',
                'appearance'
            ]);
            
            // Revert favicon in appearance group
            $stmt->execute([
                '/assets/images/favicon.ico',
                '/assets/images/favicon.ico',
                'favicon',
                'appearance'
            ]);
            
            echo "✓ Default logo and favicon settings reverted successfully\n";
            
        } catch (Exception $e) {
            echo "Error reverting default logo and favicon settings: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}