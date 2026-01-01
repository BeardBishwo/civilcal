<?php
// Standalone Migration
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;

// Attempt to load .env
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (\Throwable $e) {
    echo "Notice: .env not loaded or failed (" . $e->getMessage() . ")\n";
}

// Ensure DB Config constants are defined if missing (Fallback)
if (!defined('DB_HOST')) define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', $_ENV['DB_NAME'] ?? 'bishwo_calculator');
if (!defined('DB_USER')) define('DB_USER', $_ENV['DB_USER'] ?? 'root');
if (!defined('DB_PASS')) define('DB_PASS', $_ENV['DB_PASS'] ?? '');

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Starting Library Migration...\n";

try {
    // 1. Library Files
    $pdo->exec("CREATE TABLE IF NOT EXISTS library_files (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uploader_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NULL,
        file_path VARCHAR(255) NOT NULL,
        preview_path VARCHAR(255) NULL,
        file_type VARCHAR(50) NOT NULL COMMENT 'cad, pdf, excel, etc',
        file_size_kb INT DEFAULT 0,
        price_coins INT DEFAULT 0,
        status ENUM('pending', 'approved', 'rejected', 'flagged') DEFAULT 'pending',
        downloads_count INT DEFAULT 0,
        report_count INT DEFAULT 0,
        admin_note TEXT NULL,
        file_hash VARCHAR(64) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX (status),
        INDEX (file_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✔ Created 'library_files' table.\n";

    // 2. Unlocks
    $pdo->exec("CREATE TABLE IF NOT EXISTS library_unlocks (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        file_id INT NOT NULL,
        cost INT NOT NULL,
        unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (file_id) REFERENCES library_files(id) ON DELETE CASCADE,
        UNIQUE KEY user_file (user_id, file_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✔ Created 'library_unlocks' table.\n";

    // 3. Reviews
    $pdo->exec("CREATE TABLE IF NOT EXISTS library_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        file_id INT NOT NULL,
        reviewer_id INT NOT NULL,
        rating INT CHECK (rating BETWEEN 1 AND 5),
        comment TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (file_id) REFERENCES library_files(id) ON DELETE CASCADE,
        UNIQUE KEY file_reviewer (file_id, reviewer_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✔ Created 'library_reviews' table.\n";

    // 4. Reports
    $pdo->exec("CREATE TABLE IF NOT EXISTS library_reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        file_id INT NOT NULL,
        reporter_id INT NOT NULL,
        reason VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (file_id) REFERENCES library_files(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✔ Created 'library_reports' table.\n";

    echo "Library Migration Completed Successfully!\n";

} catch (PDOException $e) {
    echo "❌ Migration Failed: " . $e->getMessage() . "\n";
}
