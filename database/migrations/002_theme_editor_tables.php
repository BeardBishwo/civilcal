<?php
/**
 * Theme Editor Database Migration
 * Creates tables for theme template and customization system
 */

class CreateThemeEditorTables
{
    public function up()
    {
        $sql = "
        -- Theme Templates Table
        CREATE TABLE IF NOT EXISTS theme_templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            category ENUM('professional', 'modern', 'dark', 'minimal', 'corporate', 'creative') DEFAULT 'professional',
            description TEXT,
            thumbnail VARCHAR(500),
            layout_config JSON,
            color_palette JSON,
            typography JSON,
            components JSON,
            custom_css JSON,
            custom_js JSON,
            assets JSON,
            is_active BOOLEAN DEFAULT FALSE,
            is_custom BOOLEAN DEFAULT TRUE,
            is_template BOOLEAN DEFAULT FALSE,
            sort_order INT DEFAULT 0,
            settings JSON,
            created_by INT,
            updated_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id),
            FOREIGN KEY (updated_by) REFERENCES users(id)
        );

        -- Theme Customizations Table
        CREATE TABLE IF NOT EXISTS theme_customizations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            theme_id INT NOT NULL,
            user_id INT NOT NULL,
            custom_css TEXT,
            custom_js TEXT,
            settings JSON,
            overrides JSON,
            is_active BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (theme_id) REFERENCES theme_templates(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_theme_user (theme_id, user_id)
        );

        -- Theme Versions Table (for version control)
        CREATE TABLE IF NOT EXISTS theme_versions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            theme_id INT NOT NULL,
            version VARCHAR(50) NOT NULL,
            changes JSON,
            changelog TEXT,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (theme_id) REFERENCES theme_templates(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id)
        );

        -- Editor Sessions Table (for auto-save)
        CREATE TABLE IF NOT EXISTS editor_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            entity_type VARCHAR(50) DEFAULT 'theme',
            entity_id INT,
            data JSON,
            autosave_data JSON,
            last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            INDEX idx_user_entity (user_id, entity_type),
            INDEX idx_last_activity (last_activity)
        );

        -- Theme Color Palettes (preset palettes)
        CREATE TABLE IF NOT EXISTS theme_color_palettes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            description TEXT,
            colors JSON,
            gradients JSON,
            is_public BOOLEAN DEFAULT TRUE,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id)
        );

        -- Theme Font Families
        CREATE TABLE IF NOT EXISTS theme_font_families (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            description TEXT,
            font_stack JSON,
            category ENUM('serif', 'sans-serif', 'monospace', 'display', 'handwriting') DEFAULT 'sans-serif',
            google_fonts_url VARCHAR(500),
            is_google_font BOOLEAN DEFAULT FALSE,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );

        -- Editor Audit Log
        CREATE TABLE IF NOT EXISTS editor_audit_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            action VARCHAR(50) NOT NULL,
            entity_type VARCHAR(50) NOT NULL,
            entity_id INT NOT NULL,
            changes JSON,
            old_data JSON,
            new_data JSON,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            INDEX idx_entity (entity_type, entity_id),
            INDEX idx_created_at (created_at)
        );
        ";
        
        return $sql;
    }

    public function down()
    {
        $sql = "
        DROP TABLE IF EXISTS editor_audit_log;
        DROP TABLE IF EXISTS theme_font_families;
        DROP TABLE IF EXISTS theme_color_palettes;
        DROP TABLE IF EXISTS editor_sessions;
        DROP TABLE IF EXISTS theme_versions;
        DROP TABLE IF EXISTS theme_customizations;
        DROP TABLE IF EXISTS theme_templates;
        ";
        
        return $sql;
    }
}
