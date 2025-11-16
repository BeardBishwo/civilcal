<?php

class CreateEmailResponsesTable {
    public function up($pdo = null) {
        if ($pdo === null) {
            $pdo = \App\Core\Database::getInstance()->getPdo();
        }
        
        $sql = "CREATE TABLE IF NOT EXISTS email_responses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            thread_id INT NOT NULL,
            user_id INT NULL,
            message TEXT NOT NULL,
            is_internal_note BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (thread_id) REFERENCES email_threads(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )";
        
        $pdo->exec($sql);
    }
}
?>
