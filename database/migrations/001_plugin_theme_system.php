<?php
// Database Migration for Plugin & Theme System
// Run this script to create the required database tables

class PluginThemeMigration {
    
    public function up() {
        // Create plugins table
        $pluginsTable = "CREATE TABLE IF NOT EXISTS plugins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            type ENUM('calculator', 'theme', 'integration') DEFAULT 'calculator',
            description TEXT,
            version VARCHAR(20) DEFAULT '1.0.0',
            author VARCHAR(255),
            author_url VARCHAR(255),
            
            -- Plugin Files
            plugin_path VARCHAR(500),
            main_file VARCHAR(255),
            
            -- Status
            is_active BOOLEAN DEFAULT FALSE,
            is_core BOOLEAN DEFAULT FALSE,
            
            -- Configuration
            settings JSON,
            requirements JSON,
            
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_slug (slug),
            INDEX idx_type (type),
            INDEX idx_active (is_active)
        )";
        
        // Create themes table
        $themesTable = "CREATE TABLE IF NOT EXISTS themes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            description TEXT,
            version VARCHAR(20) DEFAULT '1.0.0',
            author VARCHAR(255),
            
            -- Theme Files
            theme_path VARCHAR(500),
            screenshot VARCHAR(255),
            
            -- Status
            is_active BOOLEAN DEFAULT FALSE,
            is_default BOOLEAN DEFAULT FALSE,
            
            -- Styles
            styles JSON,
            settings JSON,
            
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            INDEX idx_slug (slug),
            INDEX idx_active (is_active)
        )";
        
        return [
            'plugins' => $pluginsTable,
            'themes' => $themesTable
        ];
    }
    
    public function down() {
        return [
            "DROP TABLE IF EXISTS themes",
            "DROP TABLE IF EXISTS plugins"
        ];
    }
}
?>
