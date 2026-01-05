<?php
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    $stmt = $pdo->query("SHOW COLUMNS FROM position_levels LIKE 'course_id'");
    $col = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($col) {
        echo "EXISTS";
    } else {
        echo "MISSING";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
