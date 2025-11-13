<?php

/**
 * Create Premium Themes Tables Migration
 * 
 * This migration creates the necessary tables for premium theme functionality
 * including theme licenses, user theme settings, and theme installations.
 * 
 * @package Database\Migrations
 * @version 1.0.0
 */

require_once dirname(__DIR__, 2) . '/app/Config/db.php';

try {
    $pdo = get_db();
    
    echo "Starting premium themes migration...\n";
    
    // Create theme_licenses table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS theme_licenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            license_key VARCHAR(255) UNIQUE NOT NULL,
            user_id VARCHAR(255) NULL,
            email VARCHAR(255) NULL,
            plan ENUM('basic', 'premium', 'pro') DEFAULT 'basic',
            status ENUM('active', 'inactive', 'expired', 'suspended') DEFAULT 'active',
            allowed_domains TEXT NULL,
            allowed_users TEXT NULL,
            max_installations INT DEFAULT 1,
            installation_count INT DEFAULT 0,
            expires_at TIMESTAMP NULL,
            last_validated_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            active TINYINT(1) DEFAULT 1,
            INDEX idx_license_key (license_key),
            INDEX idx_user_id (user_id),
            INDEX idx_status (status),
            INDEX idx_expires_at (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✓ Created theme_licenses table\n";
    
    // Create user_theme_settings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_theme_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id VARCHAR(255) NOT NULL,
            theme_name VARCHAR(100) NOT NULL,
            setting_key VARCHAR(100) NOT NULL,
            setting_value LONGTEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_theme_setting (user_id, theme_name, setting_key),
            INDEX idx_user_id (user_id),
            INDEX idx_theme_name (theme_name),
            INDEX idx_setting_key (setting_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✓ Created user_theme_settings table\n";
    
    // Create theme_installations table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS theme_installations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            license_id INT NULL,
            theme_name VARCHAR(100) NOT NULL,
            theme_version VARCHAR(20) NOT NULL,
            user_id VARCHAR(255) NULL,
            domain VARCHAR(255) NOT NULL,
            installation_path VARCHAR(500) NULL,
            status ENUM('installed', 'active', 'inactive', 'error') DEFAULT 'installed',
            customizations JSON NULL,
            installed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            activated_at TIMESTAMP NULL,
            last_accessed_at TIMESTAMP NULL,
            FOREIGN KEY (license_id) REFERENCES theme_licenses(id) ON DELETE SET NULL,
            INDEX idx_license_id (license_id),
            INDEX idx_theme_name (theme_name),
            INDEX idx_user_id (user_id),
            INDEX idx_domain (domain),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✓ Created theme_installations table\n";
    
    // Create theme_updates table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS theme_updates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            theme_name VARCHAR(100) NOT NULL,
            current_version VARCHAR(20) NOT NULL,
            available_version VARCHAR(20) NOT NULL,
            update_type ENUM('security', 'feature', 'bugfix', 'major') NOT NULL,
            changelog TEXT NULL,
            download_url VARCHAR(500) NULL,
            file_size INT NULL,
            is_mandatory TINYINT(1) DEFAULT 0,
            released_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_theme_version (theme_name, available_version),
            INDEX idx_theme_name (theme_name),
            INDEX idx_update_type (update_type),
            INDEX idx_released_at (released_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✓ Created theme_updates table\n";
    
    // Create theme_analytics table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS theme_analytics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            theme_name VARCHAR(100) NOT NULL,
            user_id VARCHAR(255) NULL,
            event_type ENUM('activation', 'deactivation', 'customization', 'feature_used', 'error') NOT NULL,
            event_data JSON NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            domain VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_theme_name (theme_name),
            INDEX idx_user_id (user_id),
            INDEX idx_event_type (event_type),
            INDEX idx_created_at (created_at),
            INDEX idx_domain (domain)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✓ Created theme_analytics table\n";
    
    // Insert default premium theme license (for testing)
    $pdo->exec("
        INSERT IGNORE INTO theme_licenses (
            license_key, 
            user_id, 
            email, 
            plan, 
            status, 
            allowed_domains, 
            allowed_users, 
            max_installations, 
            expires_at
        ) VALUES (
            'PREMIUM-DEV-KEY-12345', 
            'admin', 
            'admin@example.com', 
            'premium', 
            'active', 
            'localhost,127.0.0.1,*', 
            'admin,*', 
            5, 
            DATE_ADD(NOW(), INTERVAL 1 YEAR)
        )
    ");
    
    echo "✓ Inserted default premium license for development\n";
    
    // Insert sample theme update
    $pdo->exec("
        INSERT IGNORE INTO theme_updates (
            theme_name, 
            current_version, 
            available_version, 
            update_type, 
            changelog, 
            download_url, 
            is_mandatory, 
            released_at
        ) VALUES (
            'premium', 
            '1.0.0', 
            '1.1.0', 
            'feature', 
            'Added dark mode toggle and improved calculator skins', 
            'https://api.bishwo-calculator.com/themes/premium/v1.1.0.zip', 
            0, 
            NOW()
        )
    ");
    
    echo "✓ Inserted sample theme update\n";
    
    // Insert initial analytics entry
    $pdo->exec("
        INSERT INTO theme_analytics (
            theme_name, 
            event_type, 
            event_data, 
            domain
        ) VALUES (
            'default', 
            'activation', 
            JSON_OBJECT('message', 'Premium theme system installed', 'version', '1.0.0'), 
            'localhost'
        )
    ");
    
    echo "✓ Inserted initial analytics entry\n";
    
    echo "\n🎉 Premium themes migration completed successfully!\n\n";
    echo "Summary:\n";
    echo "- Created 5 new tables for premium theme functionality\n";
    echo "- Inserted default premium license for development\n";
    echo "- Sample data added for testing\n\n";
    echo "Default Premium License:\n";
    echo "License Key: PREMIUM-DEV-KEY-12345\n";
    echo "Plan: Premium\n";
    echo "Domains: localhost, 127.0.0.1, *\n";
    echo "Expires: " . date('Y-m-d', strtotime('+1 year')) . "\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

