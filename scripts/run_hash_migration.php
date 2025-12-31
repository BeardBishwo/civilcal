<?php
require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

try {
    echo "=== Hash Column Migration Runner ===\n\n";
    $db = Database::getInstance();
    $pdo = $db->getPdo();

    $sql = file_get_contents(__DIR__ . '/../database/migrations/add_file_hash_columns.sql');
    
    // Split by statement if needed, or simple exec if PDO supports multi requests. 
    // PDO::exec might fail on multiple statements depending on config.
    // Let's safe split or just run blindly if simple.
    // The previous dynamic SQL requires prepared statements which can be tricky in batch.
    // Let's simplify and use PHP check instead of MySQL IF block for simplicity in runner.
    
    // Check library_files
    $check = $pdo->query("SHOW COLUMNS FROM library_files LIKE 'file_hash'");
    if($check->rowCount() == 0) {
        $pdo->exec("ALTER TABLE library_files ADD COLUMN file_hash CHAR(64) NOT NULL AFTER id, ADD INDEX (file_hash)");
        echo "Added file_hash to library_files.\n";
    } else {
        echo "library_files already has file_hash.\n";
    }

    // Check bounty_submissions
    $check2 = $pdo->query("SHOW COLUMNS FROM bounty_submissions LIKE 'file_hash'");
    if($check2->rowCount() == 0) {
        $pdo->exec("ALTER TABLE bounty_submissions ADD COLUMN file_hash CHAR(64) NOT NULL AFTER id, ADD INDEX (file_hash)");
        echo "Added file_hash to bounty_submissions.\n";
    } else {
        echo "bounty_submissions already has file_hash.\n";
    }

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
