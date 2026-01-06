<?php
$c = include 'config/database.php';
try {
    $pdo = new PDO('mysql:host='.$c['host'].';dbname='.$c['database'], $c['username'], $c['password']);
    $stmt = $pdo->query("SHOW CREATE TABLE quiz_questions");
    $row = $stmt->fetch(PDO::FETCH_NUM);
    file_put_contents('quiz_questions_schema.txt', $row[1]);
    echo "Schema written to quiz_questions_schema.txt\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
