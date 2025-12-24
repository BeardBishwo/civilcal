-- Enhanced Permalink System Database Schema
-- WordPress-like permalink management for Bishwo Calculator

-- 1. Enhanced calculator_urls table with permalink support
ALTER TABLE calculator_urls 
ADD COLUMN IF NOT EXISTS slug VARCHAR(200) NULL,
ADD COLUMN IF NOT EXISTS permalink_structure VARCHAR(50) DEFAULT 'calculator-only',
ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT 1,
ADD COLUMN IF NOT EXISTS redirect_old_urls BOOLEAN DEFAULT 1,
ADD INDEX idx_slug (slug),
ADD INDEX idx_permalink_structure (permalink_structure);

-- 2. New table for URL mappings and redirects
CREATE TABLE IF NOT EXISTS permalink_mappings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    old_url VARCHAR(500) NOT NULL,
    new_url VARCHAR(500) NOT NULL,
    redirect_type ENUM('301', '302') DEFAULT '301',
    calculator_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_old_url (old_url(255)),
    INDEX idx_new_url (new_url(255)),
    INDEX idx_calculator_id (calculator_id),
    INDEX idx_redirect_type (redirect_type)
);

-- 3. New table for calculator slugs and clean URLs
CREATE TABLE IF NOT EXISTS calculator_slugs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    calculator_id VARCHAR(100) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    category VARCHAR(100),
    subcategory VARCHAR(100),
    full_path VARCHAR(500) NOT NULL,
    is_custom BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_calculator_slug (calculator_id, slug),
    INDEX idx_slug (slug),
    INDEX idx_calculator_id (calculator_id),
    INDEX idx_category (category),
    INDEX idx_subcategory (subcategory)
);

-- 4. New table for permalink settings and configurations
CREATE TABLE IF NOT EXISTS permalink_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'boolean', 'integer', 'json') DEFAULT 'string',
    setting_group VARCHAR(50) DEFAULT 'permalink',
    description TEXT,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_setting_key (setting_key),
    INDEX idx_setting_group (setting_group),
    INDEX idx_is_active (is_active)
);

-- 5. Insert default permalink settings
INSERT INTO permalink_settings (setting_key, setting_value, setting_type, setting_group, description) VALUES
('permalink_structure', 'calculator-only', 'string', 'permalink', 'Current permalink structure'),
('enable_redirects', '1', 'boolean', 'permalink', 'Enable automatic redirects for old URLs'),
('redirect_cache_ttl', '3600', 'integer', 'permalink', 'Redirect cache TTL in seconds'),
('clean_urls_enabled', '1', 'boolean', 'permalink', 'Enable clean URLs (no .php extension)'),
('legacy_support', '1', 'boolean', 'permalink', 'Enable legacy URL support'),
('url_preview_enabled', '1', 'boolean', 'permalink', 'Enable URL preview in admin'),
('bulk_update_enabled', '1', 'boolean', 'permalink', 'Enable bulk URL updates'),
('seo_optimization', '1', 'boolean', 'permalink', 'Enable SEO optimizations for URLs'),
('redirect_logging', '1', 'boolean', 'permalink', 'Log redirect requests'),
('performance_monitoring', '1', 'boolean', 'permalink', 'Monitor permalink performance')
ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    updated_at = CURRENT_TIMESTAMP;

-- 6. Create view for easy permalink management
CREATE OR REPLACE VIEW permalink_calculators_view AS
SELECT 
    cu.calculator_id,
    cu.category,
    cu.subcategory,
    cu.slug,
    cu.permalink_structure,
    cu.is_active,
    cu.redirect_old_urls,
    cs.slug as clean_slug,
    cs.full_path,
    ps.setting_value as current_structure
FROM calculator_urls cu
LEFT JOIN calculator_slugs cs ON cu.calculator_id = cs.calculator_id
LEFT JOIN permalink_settings ps ON ps.setting_key = 'permalink_structure'
WHERE cu.is_active = 1;

-- 7. Indexes for performance optimization
CREATE INDEX idx_permalink_mappings_created_at ON permalink_mappings(created_at);
CREATE INDEX idx_calculator_slugs_updated_at ON calculator_slugs(updated_at);
CREATE INDEX idx_permalink_settings_updated_at ON permalink_settings(updated_at);

-- 8. Trigger to automatically create slug when calculator is added
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS trg_calculator_urls_create_slug
AFTER INSERT ON calculator_urls
FOR EACH ROW
BEGIN
    DECLARE clean_slug VARCHAR(200);
    
    -- Generate clean slug from calculator_id
    SET clean_slug = LOWER(REPLACE(NEW.calculator_id, '_', '-'));
    
    -- Insert into calculator_slugs table
    INSERT INTO calculator_slugs (
        calculator_id, 
        slug, 
        category, 
        subcategory, 
        full_path
    ) VALUES (
        NEW.calculator_id,
        clean_slug,
        NEW.category,
        NEW.subcategory,
        NEW.full_path
    ) ON DUPLICATE KEY UPDATE
        category = NEW.category,
        subcategory = NEW.subcategory,
        full_path = NEW.full_path,
        updated_at = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

-- 9. Trigger to update slug when calculator is updated
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS trg_calculator_urls_update_slug
AFTER UPDATE ON calculator_urls
FOR EACH ROW
BEGIN
    -- Update corresponding slug record
    UPDATE calculator_slugs 
    SET 
        category = NEW.category,
        subcategory = NEW.subcategory,
        full_path = NEW.full_path,
        updated_at = CURRENT_TIMESTAMP
    WHERE calculator_id = NEW.calculator_id;
END$$
DELIMITER ;

-- 10. Function to generate URL based on permalink structure
DELIMITER $$
CREATE FUNCTION IF NOT EXISTS generate_permalink_url(
    p_calculator_id VARCHAR(100),
    p_category VARCHAR(100),
    p_subcategory VARCHAR(100),
    p_structure VARCHAR(50)
) RETURNS VARCHAR(500)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE result_url VARCHAR(500);
    DECLARE clean_slug VARCHAR(200);
    DECLARE base_url VARCHAR(100);
    
    -- Get base URL from settings or use default
    SET base_url = COALESCE((SELECT setting_value FROM permalink_settings WHERE setting_key = 'base_url'), '');
    
    -- Get clean slug
    SET clean_slug = COALESCE(
        (SELECT slug FROM calculator_slugs WHERE calculator_id = p_calculator_id LIMIT 1),
        LOWER(REPLACE(p_calculator_id, '_', '-'))
    );
    
    -- Generate URL based on structure
    CASE p_structure
        WHEN 'full-path' THEN
            SET result_url = CONCAT(base_url, '/modules/', p_category, '/', p_subcategory, '/', p_calculator_id, '.php');
        WHEN 'domain-modules' THEN
            SET result_url = CONCAT(base_url, '/modules/', p_category, '/', p_subcategory, '/', p_calculator_id, '.php');
        WHEN 'domain-category' THEN
            SET result_url = CONCAT(base_url, '/', p_category, '/', p_calculator_id, '.php');
        WHEN 'domain-calculator' THEN
            SET result_url = CONCAT(base_url, '/', p_calculator_id, '.php');
        WHEN 'domain-only' THEN
            SET result_url = CONCAT(base_url, '/', p_calculator_id, '.php');
        WHEN 'clean-urls' THEN
            SET result_url = CONCAT(base_url, '/', clean_slug);
        ELSE
            -- Default to clean URLs
            SET result_url = CONCAT(base_url, '/', clean_slug);
    END CASE;
    
    RETURN result_url;
END$$
DELIMITER ;

-- 11. Procedure to migrate existing calculators to new permalink system
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS migrate_to_permalink_system()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_calculator_id VARCHAR(100);
    DECLARE v_category VARCHAR(100);
    DECLARE v_subcategory VARCHAR(100);
    DECLARE v_full_path VARCHAR(500);
    
    DECLARE calculator_cursor CURSOR FOR 
        SELECT calculator_id, category, subcategory, full_path 
        FROM calculator_urls 
        WHERE slug IS NULL OR permalink_structure IS NULL;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN calculator_cursor;
    
    read_loop: LOOP
        FETCH calculator_cursor INTO v_calculator_id, v_category, v_subcategory, v_full_path;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Update calculator_urls with default values
        UPDATE calculator_urls 
        SET 
            slug = LOWER(REPLACE(v_calculator_id, '_', '-')),
            permalink_structure = 'calculator-only',
            is_active = 1,
            redirect_old_urls = 1
        WHERE calculator_id = v_calculator_id;
        
        -- Insert into calculator_slugs if not exists
        INSERT IGNORE INTO calculator_slugs (
            calculator_id, slug, category, subcategory, full_path
        ) VALUES (
            v_calculator_id,
            LOWER(REPLACE(v_calculator_id, '_', '-')),
            v_category,
            v_subcategory,
            v_full_path
        );
    END LOOP;
    
    CLOSE calculator_cursor;
    
    -- Update current permalink structure setting
    INSERT INTO permalink_settings (setting_key, setting_value, setting_type, setting_group, description)
    VALUES ('permalink_structure', 'calculator-only', 'string', 'permalink', 'Current permalink structure')
    ON DUPLICATE KEY UPDATE setting_value = 'calculator-only';
    
END$$
DELIMITER ;

-- 12. Procedure to create redirects for old URLs
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS create_permalink_redirects()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_calculator_id VARCHAR(100);
    DECLARE v_old_url VARCHAR(500);
    DECLARE v_new_url VARCHAR(500);
    DECLARE v_category VARCHAR(100);
    DECLARE v_subcategory VARCHAR(100);
    DECLARE v_permalink_structure VARCHAR(50);
    
    DECLARE calculator_cursor CURSOR FOR 
        SELECT cu.calculator_id, cu.category, cu.subcategory, cu.permalink_structure, cu.full_path
        FROM calculator_urls cu
        WHERE cu.is_active = 1;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN calculator_cursor;
    
    read_loop: LOOP
        FETCH calculator_cursor INTO v_calculator_id, v_category, v_subcategory, v_permalink_structure, v_old_url;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Generate new URL based on current permalink structure
        SET v_new_url = generate_permalink_url(v_calculator_id, v_category, v_subcategory, v_permalink_structure);
        
        -- Create redirect if URLs are different
        IF v_old_url != v_new_url THEN
            INSERT INTO permalink_mappings (old_url, new_url, redirect_type, calculator_id)
            VALUES (v_old_url, v_new_url, '301', v_calculator_id)
            ON DUPLICATE KEY UPDATE 
                new_url = v_new_url,
                updated_at = CURRENT_TIMESTAMP;
        END IF;
    END LOOP;
    
    CLOSE calculator_cursor;
    
END$$
DELIMITER ;

-- 13. View for admin permalink management
CREATE OR REPLACE VIEW admin_permalink_view AS
SELECT 
    cu.calculator_id,
    cu.category,
    cu.subcategory,
    cu.slug as current_slug,
    cu.permalink_structure,
    cu.is_active,
    cu.redirect_old_urls,
    cs.slug as clean_slug,
    cs.full_path,
    ps.setting_value as current_structure,
    generate_permalink_url(cu.calculator_id, cu.category, cu.subcategory, cu.permalink_structure) as current_url,
    generate_permalink_url(cu.calculator_id, cu.category, cu.subcategory, 'clean-urls') as clean_url,
    generate_permalink_url(cu.calculator_id, cu.category, cu.subcategory, 'domain-only') as domain_only_url,
    generate_permalink_url(cu.calculator_id, cu.category, cu.subcategory, 'domain-calculator') as domain_calculator_url,
    generate_permalink_url(cu.calculator_id, cu.category, cu.subcategory, 'domain-category') as domain_category_url,
    generate_permalink_url(cu.calculator_id, cu.category, cu.subcategory, 'domain-modules') as domain_modules_url,
    generate_permalink_url(cu.calculator_id, cu.category, cu.subcategory, 'full-path') as full_path_url
FROM calculator_urls cu
LEFT JOIN calculator_slugs cs ON cu.calculator_id = cs.calculator_id
LEFT JOIN permalink_settings ps ON ps.setting_key = 'permalink_structure'
WHERE cu.is_active = 1
ORDER BY cu.category, cu.subcategory, cu.calculator_id;