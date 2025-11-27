<?php
require_once 'app/bootstrap.php';

use App\Core\Database;

echo "Creating IP tracking table...\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    $sql = "
        CREATE TABLE IF NOT EXISTS user_ip_addresses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            first_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_ip_address (ip_address),
            UNIQUE KEY unique_user_ip (user_id, ip_address)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($sql);
    echo "Table user_ip_addresses created successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>