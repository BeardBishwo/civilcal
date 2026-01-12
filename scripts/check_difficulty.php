<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();

echo "Checking ACTUAL difficulty levels in database...\n\n";

// Method 1: Check column definition
$result = $db->query("SHOW COLUMNS FROM quiz_questions WHERE Field = 'difficulty_level'")->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo "Column: {$result['Field']}\n";
    echo "Type: {$result['Type']}\n\n";

    // Extract ENUM values
    if (preg_match("/^enum\('(.*)'\)$/", $result['Type'], $matches)) {
        $values = explode("','", $matches[1]);
        echo "✅ FOUND " . count($values) . " DIFFICULTY LEVELS:\n";
        echo str_repeat("=", 50) . "\n";
        foreach ($values as $i => $val) {
            echo ($i + 1) . ". " . strtoupper($val) . "\n";
        }
    } else {
        echo "Type: {$result['Type']} (not ENUM, checking actual values...)\n\n";

        // Method 2: Check distinct values in table
        $distinct = $db->query("SELECT DISTINCT difficulty_level FROM quiz_questions ORDER BY difficulty_level")->fetchAll(PDO::FETCH_COLUMN);
        echo "✅ FOUND " . count($distinct) . " UNIQUE VALUES IN DATABASE:\n";
        echo str_repeat("=", 50) . "\n";
        foreach ($distinct as $i => $val) {
            echo ($i + 1) . ". " . ($val ?? 'NULL') . "\n";
        }
    }
}

echo "\n\nTotal questions: " . $db->query("SELECT COUNT(*) FROM quiz_questions")->fetchColumn() . "\n";
