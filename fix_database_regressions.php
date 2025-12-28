<?php
$host = '127.0.0.1';
$db   = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    // Add status column to quiz_categories if missing
    $cols = $pdo->query("DESCRIBE quiz_categories")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('status', $cols)) {
        $pdo->exec("ALTER TABLE quiz_categories ADD COLUMN status TINYINT DEFAULT 1 AFTER `order` ");
        echo "Column 'status' added to quiz_categories.\n";
    } else {
        echo "Column 'status' already exists in quiz_categories.\n";
    }

    echo "Database regressions fixed.\n";
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
