<?php

class EnhanceSettingsTable {
    
    public function up($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        try {
            // Check if settings table exists
            $result = $pdo->query("SHOW TABLES LIKE 'settings'")->fetch();
            
            if ($result) {
                // Table exists, check and add missing columns
                $columns = $pdo->query("DESCRIBE settings")->fetchAll(PDO::FETCH_COLUMN);
                
                if (!in_array('setting_category', $columns)) {
                    $pdo->exec("ALTER TABLE settings ADD COLUMN setting_category VARCHAR(100) DEFAULT NULL AFTER setting_group");
                }
                
                if (!in_array('validation_rules', $columns)) {
                    $pdo->exec("ALTER TABLE settings ADD COLUMN validation_rules JSON AFTER is_public");
                }
                
                if (!in_array('default_value', $columns)) {
                    $pdo->exec("ALTER TABLE settings ADD COLUMN default_value TEXT AFTER validation_rules");
                }
                
                if (!in_array('display_order', $columns)) {
                    $pdo->exec("ALTER TABLE settings ADD COLUMN display_order INT DEFAULT 0 AFTER default_value");
                }
                
                if (!in_array('is_editable', $columns)) {
                    $pdo->exec("ALTER TABLE settings ADD COLUMN is_editable BOOLEAN DEFAULT TRUE AFTER is_public");
                }
                
                // Update setting_type enum to include more types - Check current values first
                $currentTypes = $pdo->query("SELECT DISTINCT setting_type FROM settings")->fetchAll(PDO::FETCH_COLUMN);
                
                // Only update if no incompatible types exist
                $validTypes = ['string', 'text', 'boolean', 'integer', 'float', 'json', 'color', 'image', 'file', 'email', 'url', 'textarea', 'select', 'multiselect'];
                $canUpdate = true;
                
                foreach ($currentTypes as $type) {
                    if (!in_array($type, $validTypes) && !empty($type)) {
                        // Update invalid types to 'string' first
                        $pdo->exec("UPDATE settings SET setting_type = 'string' WHERE setting_type = '$type' OR setting_type = ''");
                    }
                }
                
                // Now safe to alter the column
                $pdo->exec("ALTER TABLE settings MODIFY COLUMN setting_type ENUM('string', 'text', 'boolean', 'integer', 'float', 'json', 'color', 'image', 'file', 'email', 'url', 'textarea', 'select', 'multiselect') DEFAULT 'string'");
                
                // Add indexes for better performance
                $indexes = $pdo->query("SHOW INDEX FROM settings WHERE Key_name = 'idx_group'")->fetch();
                if (!$indexes) {
                    $pdo->exec("CREATE INDEX idx_group ON settings(setting_group)");
                }
                
                $indexes = $pdo->query("SHOW INDEX FROM settings WHERE Key_name = 'idx_category'")->fetch();
                if (!$indexes) {
                    $pdo->exec("CREATE INDEX idx_category ON settings(setting_category)");
                }
                
                echo "✓ Settings table enhanced successfully\n";
            }
            
            // Insert comprehensive default settings
            $this->insertDefaultSettings($pdo);
            
        } catch (Exception $e) {
            echo "Error enhancing settings table: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    private function insertDefaultSettings($pdo) {
        $defaultSettings = [
            // General Settings
            ['site_name', 'Bishwo Calculator', 'string', 'general', 'site_identity', 'Website name displayed in header and title', 1, 0, '1'],
            ['site_tagline', 'Professional Engineering Calculators', 'string', 'general', 'site_identity', 'Short description of your site', 1, 0, '2'],
            ['site_description', 'Advanced engineering calculation tools for civil, electrical, mechanical, and HVAC engineers', 'textarea', 'general', 'site_identity', 'Detailed site description for SEO', 1, 0, '3'],
            ['site_url', 'http://localhost', 'url', 'general', 'site_identity', 'Main URL of your website', 1, 0, '4'],
            ['admin_email', 'admin@bishwocalculator.com', 'email', 'general', 'site_identity', 'Administrator email address', 1, 0, '5'],
            ['site_logo', '/uploads/settings/logo.png', 'image', 'general', 'site_identity', 'Site logo', 1, 0, '6'],
            ['favicon', '/uploads/settings/favicon.png', 'image', 'general', 'site_identity', 'Site favicon', 1, 0, '7'],
            ['timezone', 'Asia/Kathmandu', 'select', 'general', 'regional', 'System timezone', 1, '{"options": ["Asia/Kathmandu", "UTC", "America/New_York", "Europe/London", "Asia/Tokyo"]}', 0, '8'],
            ['date_format', 'Y-m-d', 'select', 'general', 'regional', 'Date format', 1, '{"options": ["Y-m-d", "d/m/Y", "m/d/Y", "d-m-Y"]}', 0, '9'],
            ['time_format', 'H:i:s', 'select', 'general', 'regional', 'Time format', 1, '{"options": ["H:i:s", "h:i A", "H:i"]}', 0, '10'],
            ['items_per_page', '20', 'integer', 'general', 'display', 'Items per page in lists', 1, '{"min": 10, "max": 100}', 0, '11'],
            ['default_language', 'en', 'select', 'general', 'regional', 'Default language', 1, '{"options": ["en", "ne", "hi"]}', 0, '12'],
            
            // Appearance Settings
            ['theme', 'default', 'select', 'appearance', 'theme', 'Active theme', 1, 0, '1'],
            ['logo', '/uploads/settings/logo.png', 'image', 'appearance', 'branding', 'Site logo', 1, 0, '2'],
            ['favicon', '/uploads/settings/favicon.png', 'image', 'appearance', 'branding', 'Site favicon', 1, 0, '3'],
            ['primary_color', '#4361ee', 'color', 'appearance', 'colors', 'Primary brand color', 1, 0, '4'],
            ['secondary_color', '#3a0ca3', 'color', 'appearance', 'colors', 'Secondary brand color', 1, 0, '5'],
            ['accent_color', '#7209b7', 'color', 'appearance', 'colors', 'Accent color', 1, 0, '6'],
            ['success_color', '#10b981', 'color', 'appearance', 'colors', 'Success message color', 1, 0, '7'],
            ['warning_color', '#f59e0b', 'color', 'appearance', 'colors', 'Warning message color', 1, 0, '8'],
            ['danger_color', '#ef4444', 'color', 'appearance', 'colors', 'Error message color', 1, 0, '9'],
            ['font_heading', 'Inter', 'select', 'appearance', 'typography', 'Heading font', 1, '{"options": ["Inter", "Roboto", "Open Sans", "Poppins", "Lato"]}', 0, '10'],
            ['font_body', 'Inter', 'select', 'appearance', 'typography', 'Body text font', 1, '{"options": ["Inter", "Roboto", "Open Sans", "Poppins", "Lato"]}', 0, '11'],
            ['container_width', '1200', 'integer', 'appearance', 'layout', 'Container max width (px)', 1, '{"min": 960, "max": 1920}', 0, '12'],
            ['enable_dark_mode', '1', 'boolean', 'appearance', 'theme', 'Enable dark mode', 1, 0, '13'],
            ['custom_css', '', 'textarea', 'appearance', 'advanced', 'Custom CSS code', 1, 0, '14'],
            ['custom_js', '', 'textarea', 'appearance', 'advanced', 'Custom JavaScript code', 1, 0, '15'],
            
            // Email Settings
            ['smtp_enabled', '0', 'boolean', 'email', 'smtp', 'Enable SMTP email', 1, 0, '1'],
            ['smtp_host', 'smtp.gmail.com', 'string', 'email', 'smtp', 'SMTP server host', 0, 0, '2'],
            ['smtp_port', '587', 'integer', 'email', 'smtp', 'SMTP server port', 0, '{"min": 1, "max": 65535}', 0, '3'],
            ['smtp_username', '', 'string', 'email', 'smtp', 'SMTP username', 0, 0, '4'],
            ['smtp_password', '', 'string', 'email', 'smtp', 'SMTP password', 0, 0, '5'],
            ['smtp_encryption', 'tls', 'select', 'email', 'smtp', 'SMTP encryption', 0, '{"options": ["tls", "ssl", "none"]}', 0, '6'],
            ['from_name', 'Bishwo Calculator', 'string', 'email', 'sender', 'From name in emails', 1, 0, '7'],
            ['from_email', 'noreply@bishwocalculator.com', 'email', 'email', 'sender', 'From email address', 1, 0, '8'],
            ['email_footer', 'Thank you for using Bishwo Calculator', 'textarea', 'email', 'templates', 'Email footer text', 1, 0, '9'],
            
            // Security Settings
            ['enable_registration', '1', 'boolean', 'security', 'authentication', 'Allow user registration', 1, 0, '1'],
            ['require_email_verification', '1', 'boolean', 'security', 'authentication', 'Require email verification', 1, 0, '2'],
            ['max_login_attempts', '5', 'integer', 'security', 'authentication', 'Max login attempts before lockout', 1, '{"min": 3, "max": 10}', 0, '3'],
            ['lockout_time', '15', 'integer', 'security', 'authentication', 'Lockout duration (minutes)', 1, '{"min": 5, "max": 60}', 0, '4'],
            ['password_min_length', '8', 'integer', 'security', 'passwords', 'Minimum password length', 1, '{"min": 6, "max": 20}', 0, '5'],
            ['require_strong_password', '1', 'boolean', 'security', 'passwords', 'Require strong passwords', 1, 0, '6'],
            ['enable_2fa', '0', 'boolean', 'security', 'authentication', 'Enable two-factor authentication', 1, 0, '7'],
            ['session_lifetime', '120', 'integer', 'security', 'sessions', 'Session lifetime (minutes)', 1, '{"min": 30, "max": 1440}', 0, '8'],
            ['enable_captcha', '1', 'boolean', 'security', 'spam_protection', 'Enable CAPTCHA', 1, 0, '9'],
            ['captcha_type', 'recaptcha', 'select', 'security', 'spam_protection', 'CAPTCHA type', 1, '{"options": ["recaptcha", "hcaptcha", "turnstile"]}', 0, '10'],
            
            // Privacy & GDPR
            ['enable_cookie_consent', '1', 'boolean', 'privacy', 'gdpr', 'Enable cookie consent banner', 1, 0, '1'],
            ['cookie_consent_text', 'We use cookies to improve your experience', 'textarea', 'privacy', 'gdpr', 'Cookie consent message', 1, 0, '2'],
            ['privacy_policy_url', '/privacy-policy', 'url', 'privacy', 'legal', 'Privacy policy URL', 1, 0, '3'],
            ['terms_of_service_url', '/terms-of-service', 'url', 'privacy', 'legal', 'Terms of service URL', 1, 0, '4'],
            ['enable_analytics', '1', 'boolean', 'privacy', 'tracking', 'Enable analytics tracking', 1, 0, '5'],
            ['analytics_provider', 'google', 'select', 'privacy', 'tracking', 'Analytics provider', 1, '{"options": ["google", "matomo", "plausible", "none"]}', 0, '6'],
            ['analytics_id', '', 'string', 'privacy', 'tracking', 'Analytics tracking ID', 1, 0, '7'],
            ['data_retention_days', '365', 'integer', 'privacy', 'gdpr', 'Data retention period (days)', 1, '{"min": 30, "max": 3650}', 0, '8'],
            
            // Performance Settings
            ['enable_cache', '1', 'boolean', 'performance', 'caching', 'Enable caching', 1, 0, '1'],
            ['cache_driver', 'file', 'select', 'performance', 'caching', 'Cache driver', 1, '{"options": ["file", "redis", "memcached"]}', 0, '2'],
            ['cache_lifetime', '3600', 'integer', 'performance', 'caching', 'Cache lifetime (seconds)', 1, '{"min": 60, "max": 86400}', 0, '3'],
            ['enable_minification', '1', 'boolean', 'performance', 'optimization', 'Enable CSS/JS minification', 1, 0, '4'],
            ['enable_compression', '1', 'boolean', 'performance', 'optimization', 'Enable GZIP compression', 1, 0, '5'],
            ['enable_lazy_loading', '1', 'boolean', 'performance', 'optimization', 'Enable image lazy loading', 1, 0, '6'],
            
            // System Settings
            ['maintenance_mode', '0', 'boolean', 'system', 'status', 'Enable maintenance mode', 1, 0, '1'],
            ['maintenance_message', 'Site is under maintenance. Please check back soon.', 'textarea', 'system', 'status', 'Maintenance mode message', 1, 0, '2'],
            ['debug_mode', '0', 'boolean', 'system', 'development', 'Enable debug mode', 1, 0, '3'],
            ['enable_error_logging', '1', 'boolean', 'system', 'logging', 'Enable error logging', 1, 0, '4'],
            ['log_level', 'error', 'select', 'system', 'logging', 'Log level', 1, '{"options": ["debug", "info", "warning", "error"]}', 0, '5'],
            ['backup_frequency', 'weekly', 'select', 'system', 'backup', 'Automatic backup frequency', 1, '{"options": ["daily", "weekly", "monthly", "disabled"]}', 0, '6'],
            
            // API Settings
            ['enable_api', '1', 'boolean', 'api', 'general', 'Enable API access', 1, 0, '1'],
            ['api_rate_limit', '100', 'integer', 'api', 'limits', 'API rate limit (requests/hour)', 1, '{"min": 10, "max": 10000}', 0, '2'],
            ['require_api_key', '1', 'boolean', 'api', 'security', 'Require API key for requests', 1, 0, '3'],
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO settings 
            (setting_key, setting_value, setting_type, setting_group, setting_category, description, is_public, validation_rules, default_value, display_order) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                setting_type = VALUES(setting_type),
                setting_group = VALUES(setting_group),
                setting_category = VALUES(setting_category),
                description = VALUES(description),
                is_public = VALUES(is_public),
                validation_rules = VALUES(validation_rules),
                default_value = VALUES(default_value),
                display_order = VALUES(display_order)
        ");
        
        foreach ($defaultSettings as $setting) {
            try {
                $stmt->execute($setting);
            } catch (Exception $e) {
                // Skip if already exists
                continue;
            }
        }
        
        echo "✓ Default settings inserted/updated successfully\n";
    }
    
    public function down($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        // Remove added columns
        $pdo->exec("ALTER TABLE settings DROP COLUMN IF EXISTS setting_category");
        $pdo->exec("ALTER TABLE settings DROP COLUMN IF EXISTS validation_rules");
        $pdo->exec("ALTER TABLE settings DROP COLUMN IF EXISTS default_value");
        $pdo->exec("ALTER TABLE settings DROP COLUMN IF EXISTS display_order");
        $pdo->exec("ALTER TABLE settings DROP COLUMN IF EXISTS is_editable");
    }
}
