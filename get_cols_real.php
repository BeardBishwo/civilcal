<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();

$stmt = $db->query("SELECT * FROM quiz_exams LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    echo implode(", ", array_keys($row)) . "\n";
} else {
    echo "NO ROWS FOUND\n";
    // try describe again
    $cols = $db->query("DESCRIBE quiz_exams")->fetchAll(PDO::FETCH_ASSOC);
    foreach($cols as $c) echo $c['Field'] . "\n";
}
