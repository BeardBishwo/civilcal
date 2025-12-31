<?php
require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

try {
    echo "=== Watermark DB Migration Runner ===\n\n";
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    // Check if column exists to avoid error
    $check = $pdo->query("SHOW COLUMNS FROM bounty_submissions LIKE 'preview_path'");
    if($check->rowCount() > 0) {
        echo "Column 'preview_path' already exists.\n";
    } else {
        $sql = file_get_contents(__DIR__ . '/../database/migrations/add_bounty_preview_column.sql');
        $pdo->exec($sql);
        echo "Column 'preview_path' added successfully.\n";
    }

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
