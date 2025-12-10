<?php
/**
 * Setup script to create backups table
 */

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\EnhancedDatabase;

try {
    $db = EnhancedDatabase::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS `backups` (
      `id` varchar(100) NOT NULL PRIMARY KEY,
      `filename` varchar(255) NOT NULL,
      `path` varchar(500) NOT NULL,
      `type` varchar(100) NOT NULL,
      `size` bigint NOT NULL DEFAULT 0,
      `compression` varchar(50) DEFAULT 'medium',
      `status` enum('pending','running','completed','failed') DEFAULT 'pending',
      `duration` decimal(10,2) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      KEY `idx_status` (`status`),
      KEY `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->query($sql);
    
    echo "✅ Backups table created successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error creating backups table: " . $e->getMessage() . "\n";
    exit(1);
}
