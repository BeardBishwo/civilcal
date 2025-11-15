<?php

/**
 * Migration: Create images table
 * Stores references to uploaded images for admin and users
 */

return [
    'up' => function() {
        $sql = "
        CREATE TABLE IF NOT EXISTS images (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NULL,
            image_type VARCHAR(50) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            filename VARCHAR(255) NOT NULL,
            path VARCHAR(500) NOT NULL,
            file_size INT NOT NULL,
            mime_type VARCHAR(50) NOT NULL,
            width INT NULL,
            height INT NULL,
            is_admin BOOLEAN DEFAULT FALSE,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_image_type (image_type),
            INDEX idx_is_admin (is_admin),
            INDEX idx_uploaded_at (uploaded_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        return $sql;
    },
    
    'down' => function() {
        return "DROP TABLE IF EXISTS images;";
    }
];
