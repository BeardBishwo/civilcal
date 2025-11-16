<?php
require 'app/bootstrap.php';

$pdo = \App\Core\Database::getInstance()->getPdo();

// Show all tables
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo "=== Tables in database ===\n";
echo implode(', ', $tables) . "\n\n";

// Check users table
echo "=== Users table ===\n";
try {
    $result = $pdo->query('DESCRIBE users')->fetchAll(PDO::FETCH_ASSOC);
    echo "Exists: YES (" . count($result) . " columns)\n";
} catch (Exception $e) {
    echo "Exists: NO\n";
}

// Check settings table
echo "\n=== Settings table ===\n";
try {
    $result = $pdo->query('DESCRIBE settings')->fetchAll(PDO::FETCH_ASSOC);
    echo "Exists: YES (" . count($result) . " columns)\n";
    foreach($result as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Exists: NO\n";
}

// Check user_sessions table
echo "\n=== User Sessions table ===\n";
try {
    $result = $pdo->query('DESCRIBE user_sessions')->fetchAll(PDO::FETCH_ASSOC);
    echo "Exists: YES (" . count($result) . " columns)\n";
    foreach($result as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Exists: NO\n";
}

// Check export_templates table
echo "\n=== Export Templates table ===\n";
try {
    $result = $pdo->query('DESCRIBE export_templates')->fetchAll(PDO::FETCH_ASSOC);
    echo "Exists: YES (" . count($result) . " columns)\n";
} catch (Exception $e) {
    echo "Exists: NO\n";
}
?>
