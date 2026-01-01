<?php
define('BASE_PATH', __DIR__ . '/..');
require_once __DIR__ . '/../app/Config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Running Viewer Migration...\n";
    
    // Check if column exists first to avoid error
    $colCheck = $pdo->query("SHOW COLUMNS FROM library_files LIKE 'preview_path'");
    if ($colCheck->rowCount() == 0) {
        $pdo->exec("ALTER TABLE library_files ADD COLUMN preview_path VARCHAR(255) DEFAULT NULL AFTER file_path");
        echo "Added preview_path column.\n";
    } else {
        echo "preview_path column already exists.\n";
    }

    // Create referrals table
    $pdo->exec("CREATE TABLE IF NOT EXISTS referrals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        inviter_id INT NOT NULL,
        new_user_id INT NOT NULL,
        status ENUM('pending', 'completed') DEFAULT 'pending', 
        reward_paid TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (inviter_id) REFERENCES users(id),
        FOREIGN KEY (new_user_id) REFERENCES users(id)
    )");
    echo "Referrals table checked/created.\n";

    echo "Migration Complete.\n";

} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}
?>
