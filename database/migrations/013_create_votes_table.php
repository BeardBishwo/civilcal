<?php

class CreateVotesTable {
    public function up($pdo = null) {
        if ($pdo === null) {
            $pdo = \App\Core\Database::getInstance()->getPdo();
        }
        
        $sql = "CREATE TABLE IF NOT EXISTS votes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            comment_id INT NOT NULL,
            vote_type ENUM('up', 'down') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_comment (user_id, comment_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE
        )";
        
        $pdo->exec($sql);
    }
}
?>
