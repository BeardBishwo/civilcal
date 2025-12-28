<?php
$host = '127.0.0.1';
$db   = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $res = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
    foreach($res as $col) {
        echo $col['Field'] . ': ' . $col['Type'] . "\n";
    }
} catch(Exception $e) {
    echo $e->getMessage();
}
