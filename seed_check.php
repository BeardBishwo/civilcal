<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bishwo_calculator;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Count Questions
    $qCount = $pdo->query("SELECT COUNT(*) FROM quiz_questions")->fetchColumn();
    echo "Total Questions: $qCount\n";
    
    // Count Exams
    $eCount = $pdo->query("SELECT COUNT(*) FROM quiz_exams")->fetchColumn();
    echo "Total Exams: $eCount\n";
    
    // Count Links
    $lCount = $pdo->query("SELECT COUNT(*) FROM quiz_exam_questions")->fetchColumn();
    echo "Total Links: $lCount\n";

    if ($qCount > 0 && $eCount > 0 && $lCount == 0) {
        echo "Linking questions to exams...\n";
        $exams = $pdo->query("SELECT id FROM quiz_exams")->fetchAll(PDO::FETCH_COLUMN);
        $questions = $pdo->query("SELECT id FROM quiz_questions LIMIT 50")->fetchAll(PDO::FETCH_COLUMN);
        
        $sql = "INSERT INTO quiz_exam_questions (exam_id, question_id, `order`) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($exams as $eid) {
            $order = 1;
            // Link 5 random questions to each exam
            $keys = array_rand($questions, min(5, count($questions)));
            if (!is_array($keys)) $keys = [$keys];
            
            foreach ($keys as $k) {
                try {
                    $stmt->execute([$eid, $questions[$k], $order++]);
                } catch (Exception $e) { /* Ignore dups */ }
            }
        }
        echo "Seeding Complete.\n";
    } elseif ($qCount == 0) {
        echo "No questions found. Creating dummy questions...\n";
        // Create 10 dummy questions
        $sql = "INSERT INTO quiz_questions (topic_id, type, content, options, correct_answer_json, default_marks, default_negative_marks, difficulty_level, answer_explanation, unique_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        for ($i=1; $i<=10; $i++) {
            $content = "Sample Question $i: What is the correct answer?";
            $options = json_encode(['A' => 'Option A', 'B' => 'Option B', 'C' => 'Option C', 'D' => 'Option D']);
            $correct = json_encode(['B']);
            $code = "Q".uniqid();
            
            $stmt->execute([1, 'multiple_choice', $content, $options, $correct, 1, 0, 1, "Explanation for Q$i", $code]);
        }
        echo "Created 10 dummy questions.\n";
        // Recursively run to link
        // (Just run script again manually or handle logic here - simpler to just re-run conceptually, but I'll add linking logic here too)
        
        $questions = $pdo->query("SELECT id FROM quiz_questions")->fetchAll(PDO::FETCH_COLUMN);
        $exams = $pdo->query("SELECT id FROM quiz_exams")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($exams)) {
             $sqlL = "INSERT INTO quiz_exam_questions (exam_id, question_id, `order`) VALUES (?, ?, ?)";
             $stmtL = $pdo->prepare($sqlL);
             foreach ($exams as $eid) {
                $order = 1;
                foreach ($questions as $qid) {
                    $stmtL->execute([$eid, $qid, $order++]);
                }
             }
             echo "Linked new questions to exams.\n";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
