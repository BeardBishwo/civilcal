<?php
require_once __DIR__ . "/app/Config/config.php";
require_once __DIR__ . "/app/Core/Database.php";
use App\Core\Database;
try {
    $db = Database::getInstance();
    $stmt = $db->getPdo()->query("SHOW CREATE TABLE users");
    $row = $stmt->fetch(PDO::FETCH_NUM);
    echo "--- SCHEMA ---\n";
    echo ($row[1] ?? 'NOT FOUND') . "\n";
    echo "--- RECENT USERS ---\n";
    $stmt = $db->getPdo()->query("SELECT id, username, email, created_at FROM users ORDER BY id DESC LIMIT 5");
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($r);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
