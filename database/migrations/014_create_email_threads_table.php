<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Database.php';

use App\Core\Database;

try {
    $pdo = Database::getInstance()->getPdo();
    
    $sql = "CREATE TABLE IF NOT EXISTS email_threads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        from_email VARCHAR(255) NOT NULL,
        from_name VARCHAR(255) NOT NULL,
        subject VARCHAR(500) NOT NULL,
        message TEXT NOT NULL,
        category ENUM('contact', 'support', 'report', 'general') DEFAULT 'general',
        status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
        priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
        assigned_to INT NULL,
        response_count INT DEFAULT 0,
        last_response_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_status (status),
        INDEX idx_category (category),
        INDEX idx_priority (priority)
    )";
    
    $pdo->exec($sql);
    echo "✅ Created email_threads table\n";
} catch (Exception $e) {
    echo "❌ Error creating email_threads table: " . $e->getMessage() . "\n";
}
?>
