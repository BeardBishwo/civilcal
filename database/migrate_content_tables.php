<?php
/**
 * Migration Script - Add Media, Pages, and Menus Tables
 * Run this script to add content management tables to existing installations
 */

require_once __DIR__ . '/../app/Config/config.php';

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database: " . DB_NAME . "\n\n";
    
    // Create pages table
    echo "Creating pages table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `pages` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(255) NOT NULL,
          `slug` varchar(255) NOT NULL,
          `content` longtext DEFAULT NULL,
          `meta_title` varchar(255) DEFAULT NULL,
          `meta_description` text DEFAULT NULL,
          `template` varchar(100) DEFAULT 'default',
          `status` enum('draft','published','archived') DEFAULT 'draft',
          `author_id` int(11) DEFAULT NULL,
          `published_at` timestamp NULL DEFAULT NULL,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `slug` (`slug`),
          KEY `idx_status` (`status`),
          KEY `idx_author` (`author_id`),
          FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Pages table created successfully\n\n";
    
    // Create menus table
    echo "Creating menus table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `menus` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `location` varchar(100) DEFAULT NULL,
          `items` json DEFAULT NULL,
          `is_active` tinyint(1) DEFAULT 1,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_location` (`location`),
          KEY `idx_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Menus table created successfully\n\n";
    
    // Create media table
    echo "Creating media table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `media` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `original_filename` varchar(255) NOT NULL,
          `filename` varchar(255) NOT NULL,
          `file_path` varchar(500) NOT NULL,
          `file_size` bigint(20) NOT NULL,
          `file_type` varchar(100) DEFAULT NULL,
          `mime_type` varchar(100) DEFAULT NULL,
          `folder` varchar(255) DEFAULT NULL,
          `uploaded_by` int(11) DEFAULT NULL,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_file_type` (`file_type`),
          KEY `idx_uploaded_by` (`uploaded_by`),
          KEY `idx_created_at` (`created_at`),
          FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Media table created successfully\n\n";
    
    echo "===========================================\n";
    echo "Migration completed successfully!\n";
    echo "===========================================\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
