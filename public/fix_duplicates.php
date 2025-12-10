<?php
require_once __DIR__ . '/../app/Config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $duplicates = ['Civil', 'Electrical', 'Fire', 'Site', 'Structural'];
    
    // Create placeholders for prepared statement
    $placeholders = implode(',', array_fill(0, count($duplicates), '?'));
    
    $sql = "DELETE FROM modules WHERE name IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($duplicates);
    
    echo "Deleted " . $stmt->rowCount() . " duplicate modules.\n";

    echo "\n--- Remaining Modules ---\n";
    $stmt = $pdo->query("SELECT id, name FROM modules ORDER BY name");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        echo "ID: {$row['id']} | Name: {$row['name']}\n";
    }

} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
