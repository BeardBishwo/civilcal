<?php
$host = '127.0.0.1';
$db   = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $res = $pdo->query("SHOW CREATE TABLE users")->fetch(PDO::FETCH_ASSOC);
    echo $res['Create Table'];
} catch(Exception $e) {
    echo $e->getMessage();
}
