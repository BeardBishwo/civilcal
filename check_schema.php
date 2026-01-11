<?php
require_once 'app/bootstrap.php';
use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Columns in users table:\n";
    foreach ($columns as $col) {
        echo "- $col\n";
    }

    $has2FA = in_array('two_factor_secret', $columns) && in_array('two_factor_enabled', $columns);
    echo "\n2FA Columns Present: " . ($has2FA ? "YES" : "NO") . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
