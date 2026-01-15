<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bishwo_calculator', 'root', '');
    $stmt = $pdo->query('SHOW TABLES');
    while ($row = $stmt->fetch()) {
        echo $row[0] . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
