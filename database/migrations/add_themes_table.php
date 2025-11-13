<?php
/**
 * Database Migration: Add Themes Table
 * 
 * Creates the themes table for modular theme management
 * 
 * @version 1.0.0
 * @author Bishwo Calculator Team
 */

// Include configuration to access database constants
require_once __DIR__ . '/../../app/Config/config.php';

class AddThemesTable
{
    private $db;
    
    public function __construct()
    {
        $this->db = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, 
            DB_USER, 
            DB_PASS
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function up()
    {
        try {
            $sql = "
                CREATE TABLE IF NOT EXISTS `themes` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(100) NOT NULL COMMENT 'Theme folder name',
                    `display_name` varchar(150) NOT NULL COMMENT 'Human readable name',
                    `version` varchar(20) NOT NULL DEFAULT '1.0.0',
                    `author` varchar(100) NOT NULL DEFAULT 'Unknown',
                    `description` text COMMENT 'Theme description',
                    `status` enum('active','inactive','deleted') NOT NULL DEFAULT 'inactive',
                    `is_premium` tinyint(1) NOT NULL DEFAULT 0,
                    `price` decimal(10,2) DEFAULT 0.00,
                    `config_json` longtext COMMENT 'Complete theme configuration',
                    `file_size` int(11) DEFAULT NULL COMMENT 'Theme package size in bytes',
                    `checksum` varchar(64) DEFAULT NULL COMMENT 'File integrity checksum',
                    `screenshot_path` varchar(255) DEFAULT NULL COMMENT 'Theme screenshot path',
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    `activated_at` timestamp NULL DEFAULT NULL COMMENT 'When last activated',
                    `usage_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Times activated',
                    `settings_json` longtext COMMENT 'User customizations',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_theme_name` (`name`),
                    KEY `idx_status` (`status`),
                    KEY `idx_is_premium` (`is_premium`),
                    KEY `idx_created_at` (`created_at`),
                    CONSTRAINT `chk_status` CHECK (`status` IN ('active', 'inactive', 'deleted'))
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Theme management table';
            ";
            
            $this->db->exec($sql);
            
            // Insert ProCalculator theme as premium
            $this->insertProCalculatorTheme();
            
            // Insert default themes
            $this->insertDefaultThemes();
            
            return [
                'success' => true,
                'message' => 'Themes table created successfully',
                'themes_inserted' => 3
            ];
            
        } catch (PDOException $e) {
            error_log("Migration Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage()
            ];
        }
    }
    
    public function down()
    {
        try {
            $this->db->exec("DROP TABLE IF EXISTS `themes`");
            return [
                'success' => true,
                'message' => 'Themes table dropped successfully'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Rollback failed: ' . $e->getMessage()
            ];
        }
    }
    
    private function insertProCalculatorTheme()
    {
        $proCalculatorConfig = json_encode([
            'name' => 'ProCalculator - Premium $100K Theme',
            'version' => '1.0.0',
            'author' => 'Bishwo Calculator Team',
            'premium' => true,
            'price' => 100000,
            'features' => [
                'dark_mode' => true,
                'glassmorphism' => true,
                'animations' => true,
                'premium_ui' => true,
                'social_login' => true,
                'two_factor' => true,
                'user_dashboard' => true,
                'calculation_history' => true,
                'favorites' => true,
                'export_pdf' => true
            ],
            'colors' => [
                'primary' => '#1a1a2e',
                'secondary' => '#16213e',
                'accent' => '#0f4c75',
                'premium' => '#3f72af',
                'gold' => '#f093fb',
                'platinum' => '#667eea'
            ]
        ]);
        
        $stmt = $this->db->prepare("
            INSERT INTO `themes` 
            (`name`, `display_name`, `version`, `author`, `description`, `status`, `is_premium`, `price`, `config_json`, `created_at`) 
            VALUES 
            ('procalculator', 'ProCalculator - Premium $100K Theme', '1.0.0', 'Bishwo Calculator Team', 'Ultra-premium $100,000 quality theme with modern glassmorphism design, advanced authentication, professional dashboard, and comprehensive user management features.', 'inactive', 1, 100000.00, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            `display_name` = VALUES(`display_name`),
            `config_json` = VALUES(`config_json`),
            `updated_at` = NOW()
        ");
        
        $stmt->execute([$proCalculatorConfig]);
    }
    
    private function insertDefaultThemes()
    {
        // Default theme
        $defaultConfig = json_encode([
            'name' => 'Default Theme',
            'version' => '1.0.0',
            'author' => 'Bishwo Calculator Team',
            'styles' => ['css/theme.css', 'css/header.css', 'css/footer.css'],
            'scripts' => ['js/main.js', 'js/header.js']
        ]);
        
        // Professional theme
        $professionalConfig = json_encode([
            'name' => 'Professional Theme',
            'version' => '1.2.0',
            'author' => 'Bishwo Calculator Team',
            'styles' => ['css/theme.css', 'css/professional.css'],
            'scripts' => ['js/main.js', 'js/professional.js']
        ]);
        
        $stmt1 = $this->db->prepare("
            INSERT INTO `themes` 
            (`name`, `display_name`, `version`, `author`, `description`, `status`, `is_premium`, `price`, `config_json`, `created_at`) 
            VALUES 
            ('default', 'Default Theme', '1.0.0', 'Bishwo Calculator Team', 'Clean and professional default theme for Bishwo Calculator', 'active', 0, 0.00, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            `display_name` = VALUES(`display_name`),
            `updated_at` = NOW()
        ");
        
        $stmt2 = $this->db->prepare("
            INSERT INTO `themes` 
            (`name`, `display_name`, `version`, `author`, `description`, `status`, `is_premium`, `price`, `config_json`, `created_at`) 
            VALUES 
            ('professional', 'Professional Theme', '1.2.0', 'Bishwo Calculator Team', 'Professional theme with enhanced styling and features', 'inactive', 0, 0.00, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            `display_name` = VALUES(`display_name`),
            `updated_at` = NOW()
        ");
        
        $stmt1->execute([$defaultConfig]);
        $stmt2->execute([$professionalConfig]);
    }
}

// Run migration if called directly
if (php_sapi_name() === 'cli' || basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    $migration = new AddThemesTable();
    $result = $migration->up();
    echo json_encode($result, JSON_PRETTY_PRINT);
}

