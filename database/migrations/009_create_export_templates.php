<?php

class CreateExportTemplatesTable
{
    public function up($pdo)
    {

        // Check if table needs to be created
        try {
            $pdo->query("DESCRIBE export_templates");
        } catch (Exception $e) {
            // Table doesn't exist, create it without foreign key initially
            $sql = "CREATE TABLE IF NOT EXISTS export_templates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                template_name VARCHAR(255) NOT NULL,
                template_type ENUM('pdf', 'excel', 'csv', 'json') NOT NULL,
                template_config JSON NOT NULL,
                is_public BOOLEAN DEFAULT FALSE,
                is_default BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_type (user_id, template_type)
            ) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $pdo->exec($sql);
        }
        
        // Skip template inserts during migration to avoid FK constraint issues
        // These can be created via API or admin panel instead
    }

    public function down($pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS export_templates");
    }
}
