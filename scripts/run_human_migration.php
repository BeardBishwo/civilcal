<?php
require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

try {
    echo "=== Human Elements Migration Runner ===\n\n";
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    // 1. Create Tables directly if not exist (PDO::exec works fine for CREATE TABLE usually)
    $sqlCreate = "
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            message VARCHAR(255) NOT NULL,
            link VARCHAR(255) DEFAULT NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (user_id)
        );

        CREATE TABLE IF NOT EXISTS library_reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_id INT NOT NULL,
            reviewer_id INT NOT NULL,
            rating INT NOT NULL,
            comment TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_review (file_id, reviewer_id)
        );

        CREATE TABLE IF NOT EXISTS library_reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_id INT NOT NULL,
            reporter_id INT NOT NULL,
            reason VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_report (file_id, reporter_id)
        );
    ";
    
    // Split create statements just in case
    foreach(explode(';', $sqlCreate) as $stmt) {
        if(trim($stmt)) $pdo->exec(trim($stmt));
    }
    echo "Tables checked/created.\n";

    // 2. Add Columns (using manual checks for safety in PHP)
    
    // library_files: report_count
    $check = $pdo->query("SHOW COLUMNS FROM library_files LIKE 'report_count'");
    if($check->rowCount() == 0) {
        $pdo->exec("ALTER TABLE library_files ADD COLUMN report_count INT DEFAULT 0");
        echo "Added report_count to library_files.\n";
    }

    // users: referral_code
    $checkRef = $pdo->query("SHOW COLUMNS FROM users LIKE 'referral_code'");
    if($checkRef->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users 
            ADD COLUMN referral_code VARCHAR(50) UNIQUE DEFAULT NULL, 
            ADD COLUMN referred_by INT DEFAULT NULL, 
            ADD COLUMN quiz_solved_count INT DEFAULT 0");
        echo "Added referral columns to users.\n";
    }

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
