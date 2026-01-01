<?php
define('BASE_PATH', __DIR__ . '/..');
require_once __DIR__ . '/../app/Config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Running Premium Library Migration...\n";

    // 1. library_files: Add price
    $colCheck = $pdo->query("SHOW COLUMNS FROM library_files LIKE 'price'");
    if ($colCheck->rowCount() == 0) {
        $pdo->exec("ALTER TABLE library_files ADD COLUMN price INT NOT NULL DEFAULT 0");
        echo "Updated library_files table (added price).\n";
    }

    // 2. library_unlocks
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS library_unlocks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            file_id INT NOT NULL,
            cost INT NOT NULL,
            unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (file_id) REFERENCES library_files(id) ON DELETE CASCADE,
            UNIQUE KEY unique_unlock (user_id, file_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Created library_unlocks table.\n";

    echo "Migration Complete.\n";

} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}
?>
