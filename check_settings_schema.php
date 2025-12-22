<?php
require_once 'app/bootstrap.php';
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $stmt = $db->query("DESCRIBE settings");
    $schema = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($schema, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
