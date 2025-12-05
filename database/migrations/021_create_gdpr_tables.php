<?php

class CreateGDPRTables {
    
    public function up($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        // GDPR consent logs
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS gdpr_consents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                consent_type VARCHAR(100) NOT NULL,
                consent_given BOOLEAN DEFAULT FALSE,
                consent_version VARCHAR(20),
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user (user_id),
                INDEX idx_type (consent_type),
                INDEX idx_created (created_at),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Data export requests
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS data_export_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                request_type ENUM('export', 'delete') DEFAULT 'export',
                status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
                file_path VARCHAR(255) NULL,
                error_message TEXT NULL,
                ip_address VARCHAR(45),
                expires_at TIMESTAMP NULL,
                completed_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user (user_id),
                INDEX idx_status (status),
                INDEX idx_created (created_at),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Activity logs for audit trail
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                action VARCHAR(100) NOT NULL,
                entity_type VARCHAR(100) NULL,
                entity_id INT NULL,
                description TEXT,
                old_values JSON NULL,
                new_values JSON NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user (user_id),
                INDEX idx_action (action),
                INDEX idx_entity (entity_type, entity_id),
                INDEX idx_created (created_at),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Cookie preferences
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS cookie_preferences (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                session_id VARCHAR(255) NULL,
                necessary BOOLEAN DEFAULT TRUE,
                functional BOOLEAN DEFAULT FALSE,
                analytics BOOLEAN DEFAULT FALSE,
                marketing BOOLEAN DEFAULT FALSE,
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user (user_id),
                INDEX idx_session (session_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        echo "✓ GDPR compliance tables created successfully\n";
    }
    
    public function down($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        $pdo->exec("DROP TABLE IF EXISTS cookie_preferences");
        $pdo->exec("DROP TABLE IF EXISTS activity_logs");
        $pdo->exec("DROP TABLE IF EXISTS data_export_requests");
        $pdo->exec("DROP TABLE IF EXISTS gdpr_consents");
    }
}
