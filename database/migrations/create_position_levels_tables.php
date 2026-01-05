<?php
/**
 * Migration: Create Position Levels Tables
 * Creates position_levels table and question_position_levels junction table
 */

require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "Creating position_levels table...\n";
    
    // Create position_levels table
    $sql1 = "CREATE TABLE IF NOT EXISTS position_levels (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        description TEXT,
        level_number INT DEFAULT 0,
        color VARCHAR(50),
        icon VARCHAR(100),
        order_index INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_active (is_active),
        INDEX idx_order (order_index),
        INDEX idx_level_number (level_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql1);
    echo "✓ position_levels table created successfully\n";
    
    echo "Creating question_position_levels junction table...\n";
    
    // Create junction table for many-to-many relationship
    $sql2 = "CREATE TABLE IF NOT EXISTS question_position_levels (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_id INT NOT NULL,
        position_level_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_question (question_id),
        INDEX idx_position_level (position_level_id),
        UNIQUE KEY unique_question_level (question_id, position_level_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql2);
    echo "✓ question_position_levels junction table created successfully\n";
    
    // Insert default position levels
    echo "Inserting default position levels...\n";
    
    $defaultLevels = [
        ['title' => 'Level 4 (Assistant)', 'slug' => 'level-4-assistant', 'level_number' => 4, 'color' => '#10b981', 'icon' => 'fa-user', 'order_index' => 1],
        ['title' => 'Level 5 (Sub-Engineer)', 'slug' => 'level-5-sub-engineer', 'level_number' => 5, 'color' => '#3b82f6', 'icon' => 'fa-user-tie', 'order_index' => 2],
        ['title' => 'Level 6 (Engineer)', 'slug' => 'level-6-engineer', 'level_number' => 6, 'color' => '#8b5cf6', 'icon' => 'fa-user-graduate', 'order_index' => 3],
        ['title' => 'Level 7 (Senior)', 'slug' => 'level-7-senior', 'level_number' => 7, 'color' => '#f59e0b', 'icon' => 'fa-user-shield', 'order_index' => 4],
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO position_levels (title, slug, level_number, color, icon, order_index, is_active)
        VALUES (?, ?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE 
            title = VALUES(title),
            level_number = VALUES(level_number),
            color = VALUES(color),
            icon = VALUES(icon)
    ");
    
    foreach ($defaultLevels as $level) {
        $stmt->execute([
            $level['title'],
            $level['slug'],
            $level['level_number'],
            $level['color'],
            $level['icon'],
            $level['order_index']
        ]);
    }
    
    echo "✓ Default position levels inserted successfully\n";
    echo "\n✅ Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
