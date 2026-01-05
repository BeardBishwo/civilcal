<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bishwo_calculator;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update Question Counts
    echo "Updating Question Counts...\n";
    $sql = "UPDATE quiz_exams e SET question_count = (SELECT COUNT(*) FROM quiz_exam_questions eq WHERE eq.exam_id = e.id)";
    $pdo->exec($sql);
    echo "Updated counts.\n";
    
    // Verify
    $stmt = $pdo->query("SELECT title, question_count FROM quiz_exams");
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($exams as $e) {
        echo "Ex: {$e['title']} - {$e['question_count']}\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
