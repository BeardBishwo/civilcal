<?php
// Migration for Guild Join Requests

$host = '127.0.0.1';
$db   = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    $pdo->exec("CREATE TABLE IF NOT EXISTS guild_join_requests (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        guild_id INT UNSIGNED NOT NULL,
        user_id INT NOT NULL,
        status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(guild_id, user_id),
        FOREIGN KEY (guild_id) REFERENCES guilds(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    echo "Guild Join Requests table created.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
