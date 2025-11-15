<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=bishwo_calculator;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Database: bishwo_calculator\n";
    echo "Tables found: " . count($tables) . "\n";
    echo "Tables: " . implode(', ', $tables) . "\n";
    
    // Check users table structure
    $columns = $pdo->query('DESCRIBE users')->fetchAll(PDO::FETCH_ASSOC);
    echo "\nUsers table columns:\n";
    foreach ($columns as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
