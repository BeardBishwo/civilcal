<?php

class CreateCommentsTable {
    public function up($pdo = null) {
        if ($pdo === null) {
            $pdo = \App\Core\Database::getInstance()->getPdo();
        }
        
        $sql = "CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            share_id INT NOT NULL,
            parent_id INT DEFAULT NULL,
            content TEXT NOT NULL,
            upvotes INT DEFAULT 0,
            downvotes INT DEFAULT 0,
            is_edited BOOLEAN DEFAULT FALSE,
            edited_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (share_id) REFERENCES shares(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
            INDEX idx_share_id (share_id),
            INDEX idx_parent_id (parent_id)
        )";
        
        $pdo->exec($sql);
    }
}
?>
