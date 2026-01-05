<?php
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    $pdo->exec("UPDATE position_levels SET course_id = NULL, education_level_id = NULL");
    echo "✅ position_levels links reset to NULL\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
