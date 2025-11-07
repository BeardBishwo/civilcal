<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Database.php';

use App\Core\Database;

try {
    $pdo = Database::getInstance()->getPdo();
    
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
    echo "✅ Created email_responses table\n";
} catch (Exception $e) {
    echo "❌ Error creating email_responses table: " . $e->getMessage() . "\n";
}
?>
