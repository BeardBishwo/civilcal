<?php

class CreateExportTemplatesTable
{
    public function up($pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS export_templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            template_name VARCHAR(255) NOT NULL,
            template_type ENUM('pdf', 'excel', 'csv', 'json') NOT NULL,
            template_config JSON NOT NULL,
            is_public BOOLEAN DEFAULT FALSE,
            is_default BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_type (user_id, template_type)
        ) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        
        // Create default templates
        $defaultTemplates = [
            [
                'template_name' => 'Professional PDF Report',
                'template_type' => 'pdf',
                'template_config' => [
                    'include_logo' => true,
                    'include_header' => true,
                    'include_footer' => true,
                    'include_timestamp' => true,
                    'color_scheme' => 'professional',
                    'font_size' => 'medium',
                    'page_size' => 'a4',
                    'orientation' => 'portrait'
                ],
                'is_default' => true,
                'is_public' => true
            ],
            [
                'template_name' => 'Excel Data Sheet',
                'template_type' => 'excel',
                'template_config' => [
                    'include_formulas' => true,
                    'include_charts' => false,
                    'auto_format' => true,
                    'freeze_panes' => true
                ],
                'is_default' => true,
                'is_public' => true
            ],
            [
                'template_name' => 'Simple CSV Export',
                'template_type' => 'csv',
                'template_config' => [
                    'delimiter' => ',',
                    'include_headers' => true,
                    'utf8_bom' => true
                ],
                'is_default' => true,
                'is_public' => true
            ],
            [
                'template_name' => 'Developer JSON',
                'template_type' => 'json',
                'template_config' => [
                    'include_metadata' => true,
                    'pretty_print' => true,
                    'include_timestamp' => true
                ],
                'is_default' => true,
                'is_public' => true
            ]
        ];

        // Check if default templates already exist
        $checkStmt = $pdo->prepare("SELECT id FROM export_templates WHERE template_name = ? AND is_default = 1");
        
        foreach ($defaultTemplates as $template) {
            $checkStmt->execute([$template['template_name']]);
            $existing = $checkStmt->fetch();
            
            if (!$existing) {
                $insertStmt = $pdo->prepare("
                    INSERT INTO export_templates (
                        user_id, template_name, template_type, template_config, 
                        is_public, is_default
                    ) VALUES (0, ?, ?, ?, ?, ?)
                ");
                
                $insertStmt->execute([
                    $template['template_name'],
                    $template['template_type'],
                    json_encode($template['template_config']),
                    $template['is_public'],
                    $template['is_default']
                ]);
            }
        }
    }

    public function down($pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS export_templates");
    }
}
