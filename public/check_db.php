<?php
$host = '127.0.0.1';
$db = 'bishwo_calculator';
$user = 'root';
$pass = '';

echo "Testing connection to $db at $host with user '$user'...<br>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "Connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
