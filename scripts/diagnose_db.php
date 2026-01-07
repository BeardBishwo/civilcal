<?php
require 'app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance()->getPdo();
    
    echo "--- USERS COLUMNS (FIRST 30) ---\n";
    $stmt = $db->query("DESCRIBE users");
    $i = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($i >= 30) break;
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
        $i++;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
