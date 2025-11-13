<?php
/**
 * Database Migration: Add Theme Customizations Table
 * 
 * Creates table for storing user theme customizations
 * 
 * @version 1.0.0
 * @author Bishwo Calculator Team
 */

require_once __DIR__ . '/../../app/Config/config.php';

class AddThemeCustomizationsTable
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
                CREATE TABLE IF NOT EXISTS `theme_customizations` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `theme_id` int(11) NOT NULL COMMENT 'Reference to themes table',
                    `customizations_json` longtext COMMENT 'JSON with colors, typography, features, layout',
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_theme_customization` (`theme_id`),
                    FOREIGN KEY (`theme_id`) REFERENCES `themes`(`id`) ON DELETE CASCADE,
                    KEY `idx_updated_at` (`updated_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Theme customizations storage';
            ";
            
            $this->db->exec($sql);
            
            return [
                'success' => true,
                'message' => 'Theme customizations table created successfully'
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
            $sql = "DROP TABLE IF EXISTS `theme_customizations`;";
            $this->db->exec($sql);
            
            return [
                'success' => true,
                'message' => 'Theme customizations table dropped successfully'
            ];
            
        } catch (PDOException $e) {
            error_log("Migration Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Rollback failed: ' . $e->getMessage()
            ];
        }
    }
}

// Run migration if called directly
if (php_sapi_name() === 'cli') {
    $migration = new AddThemeCustomizationsTable();
    $result = $migration->up();
    echo json_encode($result) . PHP_EOL;
}
?>

