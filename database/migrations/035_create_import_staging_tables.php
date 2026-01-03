<?php

use App\Core\Database;

class CreateImportStagingTables {
    
    public function up() {
        $db = Database::getInstance();
        $pdo = $db->getPdo();
        
        try {
            // 1. Add content_hash to quiz_questions if it doesn't exist
            $columns = $db->query("SHOW COLUMNS FROM quiz_questions LIKE 'content_hash'")->fetchAll();
            if (empty($columns)) {
                $sql = "ALTER TABLE quiz_questions ADD COLUMN content_hash CHAR(64) NULL AFTER content";
                $pdo->exec($sql);
                echo "Added column 'content_hash' to 'quiz_questions'.\n";
                
                $sql = "ALTER TABLE quiz_questions ADD INDEX idx_content_hash (content_hash)";
                $pdo->exec($sql);
                echo "Added index 'idx_content_hash' to 'quiz_questions'.\n";
            }

            // 2. Create question_import_staging table
            $sql = "CREATE TABLE IF NOT EXISTS question_import_staging (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                batch_id VARCHAR(50) NOT NULL,        -- e.g., 'import_2026_01_03_admin'
                uploader_id INT UNSIGNED NOT NULL DEFAULT 1, -- Default admin for now
                
                -- The Content
                syllabus_node_id INT UNSIGNED NULL,   -- Mapped from the Excel 'Category' column
                question_text TEXT,
                options JSON,                         -- Stores Option A, B, C, D
                correct_answer VARCHAR(255),
                explanation TEXT,
                practical_mode TINYINT(1) DEFAULT 0,
                
                -- The Duplicate Logic
                content_hash CHAR(64) NOT NULL,
                is_duplicate BOOLEAN DEFAULT FALSE,
                duplicate_match_id BIGINT UNSIGNED NULL, -- Links to the EXISTING question ID if found
                
                -- The Admin Decision
                status ENUM('pending', 'approved', 'rejected', 'merged') DEFAULT 'pending',
                admin_decision ENUM('waiting', 'overwrite', 'skip', 'create_new') DEFAULT 'waiting',
                
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                
                INDEX idx_batch (batch_id),
                INDEX idx_hash_staging (content_hash)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            
            $pdo->exec($sql);
            echo "Created table 'question_import_staging'.\n";

        } catch (Exception $e) {
            echo "Error in CreateImportStagingTables: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}
