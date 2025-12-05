<?php

class CreateSiteSettingsTable
{
    public function up($pdo = null)
    {
        if ($pdo === null) {
            $pdo = \App\Core\Database::getInstance()->getPdo();
        }

        $sql = "CREATE TABLE IF NOT EXISTS site_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(255) NOT NULL UNIQUE,
            setting_value TEXT NULL,
            setting_group VARCHAR(50) DEFAULT 'general',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_group (setting_group)
        )";

        $pdo->exec($sql);

        // Seed default SMTP settings
        $settings = [
            'email_smtp_host' => 'smtp.gmail.com',
            'email_smtp_port' => '587',
            'email_smtp_user' => '',
            'email_smtp_pass' => '',
            'email_smtp_secure' => 'tls',
            'email_from_name' => 'Bishwo Calculator',
            'email_from_address' => 'admin@bishwocalculator.com'
        ];

        $stmt = $pdo->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_group) VALUES (?, ?, 'email')");

        foreach ($settings as $key => $value) {
            $stmt->execute([$key, $value]);
        }
    }
}
