<?php

class CreateCompleteSystemTables {
    public function up($pdo = null) {
        // If no PDO provided, try to get it from Database singleton
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        // Users table (if not exists)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) UNIQUE NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'engineer', 'user') DEFAULT 'user',
                first_name VARCHAR(100),
                last_name VARCHAR(100),
                phone VARCHAR(20),
                company VARCHAR(255),
                country VARCHAR(100),
                timezone VARCHAR(50) DEFAULT 'UTC',
                avatar VARCHAR(255),
                is_active BOOLEAN DEFAULT TRUE,
                email_verified BOOLEAN DEFAULT FALSE,
                last_login TIMESTAMP NULL,
                login_count INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // Settings table - check if setting_group column exists before adding it
        try {
            // Check if settings table exists and has setting_group column
            $result = $pdo->query("DESCRIBE settings")->fetchAll(PDO::FETCH_ASSOC);
            $hasSettingGroup = false;
            foreach ($result as $col) {
                if ($col['Field'] === 'setting_group') {
                    $hasSettingGroup = true;
                    break;
                }
            }
            
            // If settings table exists but missing columns, add them
            if (!$hasSettingGroup) {
                $pdo->exec("ALTER TABLE settings ADD COLUMN setting_group VARCHAR(100) DEFAULT 'general' AFTER setting_type");
            }
            if (!in_array('description', array_column($result, 'Field'))) {
                $pdo->exec("ALTER TABLE settings ADD COLUMN description TEXT AFTER setting_group");
            }
            if (!in_array('is_public', array_column($result, 'Field'))) {
                $pdo->exec("ALTER TABLE settings ADD COLUMN is_public BOOLEAN DEFAULT FALSE AFTER description");
            }
        } catch (Exception $e) {
            // Settings table doesn't exist, create it
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS settings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    setting_key VARCHAR(255) UNIQUE NOT NULL,
                    setting_value TEXT,
                    setting_type ENUM('string', 'boolean', 'integer', 'json') DEFAULT 'string',
                    setting_group VARCHAR(100) DEFAULT 'general',
                    description TEXT,
                    is_public BOOLEAN DEFAULT FALSE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");
        }
        
        // User sessions table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                session_token VARCHAR(255) UNIQUE NOT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                expires_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        
        // Login history table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS login_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                success BOOLEAN DEFAULT TRUE,
                failure_reason VARCHAR(255),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        
        // System logs table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS system_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                level ENUM('INFO', 'WARNING', 'ERROR', 'DEBUG') DEFAULT 'INFO',
                message TEXT NOT NULL,
                context JSON,
                ip_address VARCHAR(45),
                user_id INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_level (level),
                INDEX idx_created_at (created_at),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            )
        ");
        
        // Insert default admin user
        $this->insertDefaultData($pdo);
    }
    
    private function insertDefaultData($pdo) {
        // Insert default admin user (password: admin123)
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO users 
            (username, email, password, role, first_name, last_name, is_active, email_verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            'admin', 
            'admin@bishwocalculator.com', 
            $hashedPassword, 
            'admin', 
            'System', 
            'Administrator', 
            true, 
            true
        ]);
        
        // Insert default settings
        $defaultSettings = [
            ['site_name', 'Bishwo Calculator', 'string', 'general', 'Website name'],
            ['site_description', 'Professional Engineering Calculators', 'string', 'general', 'Website description'],
            ['site_url', 'http://localhost/bishwo_calculator', 'string', 'general', 'Website URL'],
            ['admin_email', 'admin@bishwocalculator.com', 'string', 'general', 'Administrator email'],
            ['timezone', 'Asia/Kathmandu', 'string', 'general', 'System timezone'],
            ['items_per_page', '20', 'integer', 'general', 'Items per page in lists'],
            ['theme', 'default', 'string', 'appearance', 'Active theme'],
            ['logo', '/assets/images/logo.png', 'string', 'appearance', 'Website logo'],
            ['smtp_host', 'smtp.gmail.com', 'string', 'email', 'SMTP server host'],
            ['smtp_port', '587', 'integer', 'email', 'SMTP server port'],
            ['smtp_encryption', 'tls', 'string', 'email', 'SMTP encryption'],
            ['enable_registration', '1', 'boolean', 'security', 'Allow user registration'],
            ['max_login_attempts', '5', 'integer', 'security', 'Maximum login attempts before lockout'],
            ['lockout_time', '15', 'integer', 'security', 'Lockout time in minutes'],
            ['maintenance_mode', '0', 'boolean', 'system', 'Maintenance mode']
        ];
        
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO settings 
            (setting_key, setting_value, setting_type, setting_group, description) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
    }
    
    public function down($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        $tables = ['users', 'settings', 'user_sessions', 'login_history', 'system_logs'];
        
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS $table");
        }
    }
}
?>
