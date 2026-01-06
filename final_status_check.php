<?php
$c = include 'config/database.php';
try {
    $pdo = new PDO('mysql:host='.$c['host'].';dbname='.$c['database'], $c['username'], $c['password']);
    
    $tables = ['question_import_staging', 'quiz_questions', 'question_stream_map', 'question_position_levels'];
    
    foreach ($tables as $t) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $t");
        echo "$t count: " . $stmt->fetchColumn() . "\n";
    }
    
    echo "\n=== quiz_questions SCHEMA ===\n";
    $stmt = $pdo->query("SHOW CREATE TABLE quiz_questions");
    $row = $stmt->fetch(PDO::FETCH_NUM);
    echo $row[1] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
