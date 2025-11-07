<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Database.php';

use App\Core\Database;

try {
    $pdo = Database::getInstance()->getPdo();
    
    $sql = "CREATE TABLE IF NOT EXISTS shares (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        calculation_id INT NOT NULL,
        share_type ENUM('public', 'private', 'team') DEFAULT 'public',
        share_token VARCHAR(255) UNIQUE NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        is_active BOOLEAN DEFAULT TRUE,
        view_count INT DEFAULT 0,
        like_count INT DEFAULT 0,
        comment_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (calculation_id) REFERENCES calculation_history(id) ON DELETE CASCADE,
        INDEX idx_share_token (share_token),
        INDEX idx_user_id (user_id)
    )";
    
    $pdo->exec($sql);
    echo "✅ Created shares table\n";
} catch (Exception $e) {
    echo "❌ Error creating shares table: " . $e->getMessage() . "\n";
}
?>
