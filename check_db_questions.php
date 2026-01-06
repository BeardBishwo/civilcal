<?php
$c = include 'config/database.php';
try {
    $pdo = new PDO('mysql:host='.$c['host'].';dbname='.$c['database'], $c['username'], $c['password']);
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM quiz_questions');
    echo "Total questions in quiz_questions: " . $stmt->fetchColumn() . "\n";
    
    $stmt = $pdo->query('SELECT id, unique_code FROM quiz_questions LIMIT 10');
    echo "Sample Question IDs:\n";
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo " - ID: " . $r['id'] . " (Code: " . ($r['unique_code'] ?: 'none') . ")\n";
    }
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM question_stream_map');
    echo "Total mappings in question_stream_map: " . $stmt->fetchColumn() . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
