<?php

/**
 * Enterprise Reliability Verification Script
 * Focus: JSON Exam Engine & High Concurrency Simulation
 * Usage: php tests/verify_exam_engine.php
 */

define('fp', 'C:\laragon\www\Bishwo_Calculator');
require fp . '/vendor/autoload.php';

// Mock HEAD/Session
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/test';
$_SERVER['SCRIPT_NAME'] = '/index.php'; // Mock script name for View/ThemeManager
$_SERVER['HTTP_HOST'] = 'localhost';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', fp);
}

if(session_status() === PHP_SESSION_NONE) session_start();

// Load Core
$config = require fp . '/app/config/config.php';
$db = \App\Core\Database::getInstance();

echo "---------------------------------------------------\n";
echo "🚀 ENTERPRISE EXAM ENGINE VERIFICATION\n";
echo "---------------------------------------------------\n";

// 1. Setup Environment
// Create a Dummy User
$timestamp = time();
$email = 'tester_' . $timestamp . '@example.com';
$username = 'Tester_' . $timestamp;
$db->getPdo()->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, 'password', 'user')")
    ->execute([$username, $email]);
$userId = $db->getPdo()->lastInsertId();
$_SESSION['user_id'] = $userId;
echo "✅ Created Test User ID: $userId\n";

// Create a Dummy Exam
$slug = 'test-exam-' . time();
$db->getPdo()->prepare("INSERT INTO quiz_exams (title, slug, duration_minutes, mode, shuffle_questions) VALUES (?, ?, 60, 'exam', 1)")
    ->execute(['Load Test Exam', $slug]);
$examId = $db->getPdo()->lastInsertId();
echo "✅ Created Test Exam ID: $examId ($slug)\n";

// Create Dummy Questions
$qIds = [];
for($i=1; $i<=5; $i++) {
    $content = json_encode(['text' => "Question $i"]);
    $options = json_encode([
        ['text' => 'A', 'is_correct' => 1],
        ['text' => 'B', 'is_correct' => 0]
    ]);
    
    $db->getPdo()->prepare("INSERT INTO quiz_questions (type, content, options, default_marks) VALUES ('mcq_single', ?, ?, 1)")
        ->execute([$content, $options]);
    $qIds[] = $db->getPdo()->lastInsertId();
}

// Link Questions to Exam
foreach($qIds as $idx => $qid) {
    // Insert using plain SQL for speed in test script
    $db->getPdo()->prepare("INSERT INTO quiz_exam_questions (exam_id, question_id, `order`) VALUES (?, ?, ?)")
        ->execute([$examId, $qid, $idx]);
}
echo "✅ Linked 5 Questions to Exam\n";

// DEBUG: Check Schema
$stmtSchema = $db->getPdo()->query("DESCRIBE quiz_exam_questions");
echo "\n--- SCHEMA: quiz_exam_questions ---\n";
foreach ($stmtSchema->fetchAll(\PDO::FETCH_ASSOC) as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}
echo "-----------------------------------\n";

// DEBUG: Check daily_missions Schema
try {
    $stmtSchema = $db->getPdo()->query("DESCRIBE daily_missions");
    echo "\n--- SCHEMA: daily_missions ---\n";
    foreach ($stmtSchema->fetchAll(\PDO::FETCH_ASSOC) as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    echo "-----------------------------------\n";
} catch (\Exception $e) {
    echo "❌ daily_missions table likely missing: " . $e->getMessage() . "\n";
}

// 2. Instantiate Controller
require_once fp . '/app/Services/GamificationService.php';
require_once fp . '/app/Services/NonceService.php';
require_once fp . '/app/Services/SecurityMonitor.php';
require_once fp . '/app/Controllers/Quiz/ExamEngineController.php';

// Mock Controller to capture redirects instead of exiting
class TestExamController extends \App\Controllers\Quiz\ExamEngineController {
    public $redirectUrl;
    public function redirect($url) {
        $this->redirectUrl = $url;
        // Don't exit
    }
    public function view($path, $data = []) {
        // Return data for inspection
        return $data;
    }
}

$controller = new TestExamController();

// 3. Test START (Should create JSON)
echo "\n[TEST 1] Starting Exam...\n";
try {
    $controller->start($slug);
} catch (\Exception $e) {
    echo "❌ Exception during start(): " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    die();
}
preg_match('/\/quiz\/room\/(\d+)/', $controller->redirectUrl, $matches);
$attemptId = $matches[1] ?? null;

if (!$attemptId) {
    die("❌ Failed to get Attempt ID from redirect URL: " . $controller->redirectUrl);
}
echo "✅ Exam Started. Attempt ID: $attemptId\n";

// Verify JSON Existence
$jsonPath = fp . '/storage/app/exams/' . $attemptId . '.json';
if (file_exists($jsonPath)) {
    echo "✅ JSON Cache File Found: $jsonPath\n";
    $json = json_decode(file_get_contents($jsonPath), true);
    if (count($json['questions']) === 5) {
        echo "✅ JSON contains correct question count (5)\n";
    } else {
        echo "❌ JSON question count mismatch!\n";
    }
} else {
    die("❌ JSON Cache File NOT FOUND!\n");
}

// 4. Test SAVE ANSWER (Should update JSON, NOT DB)
echo "\n[TEST 2] Saving Answers to JSON...\n";
// Simulate POST
$_POST = [
    'attempt_id' => $attemptId,
    'question_id' => $qIds[0], // Correct Answer is index 0
    'selected_options' => 0 
];
// Mock output handling for saveAnswer which usually echoes/exits
ob_start();
try {
    $controller->saveAnswer();
} catch (\Exception $e) { } // Catch exit if any
$output = ob_get_clean();
echo "   API Response: " . substr($output, 0, 50) . "...\n";

// Check JSON again
$json = json_decode(file_get_contents($jsonPath), true);
if (isset($json['answers'][$qIds[0]]) && $json['answers'][$qIds[0]] == 0) {
    echo "✅ Answer saved in JSON correctly.\n";
} else {
    echo "❌ Answer NOT found in JSON!\n";
}

// Check DB (Should be EMPTY for this attempt-question combo)
$stmt = $db->getPdo()->prepare("SELECT * FROM quiz_attempt_answers WHERE attempt_id = ?");
$stmt->execute([$attemptId]);
if ($stmt->rowCount() === 0) {
    echo "✅ DB `quiz_attempt_answers` is validly EMPTY (Zero-Write Strategy confirmed).\n";
} else {
    echo "❌ DB writes detected! High Concurrency Strategy FAILED.\n";
    print_r($stmt->fetchAll());
}

// 5. Test SUBMIT (Should Sync to DB)
echo "\n[TEST 3] Submitting Exam...\n";
$_POST = [
    'attempt_id' => $attemptId,
    'nonce' => (new \App\Services\NonceService())->generate($userId, 'quiz')['nonce']
];

ob_start();
$controller->submit();
ob_end_clean();

// Check DB Now
$stmt = $db->getPdo()->prepare("SELECT COUNT(*) FROM quiz_attempt_answers WHERE attempt_id = ?");
$stmt->execute([$attemptId]);
$count = $stmt->fetchColumn();

// We expect 5 rows (1 for each question in the exam, regardless of whether it was answered)
$expectedRows = 5; 

if ($count == $expectedRows) {
    echo "✅ DB Row Count Verified (Got $count rows, covering all 5 questions).\n";
} else {
    echo "❌ DB Row Count Mismatch (Expected $expectedRows, Got $count)\n";
}

// Check Status
$stmt = $db->getPdo()->prepare("SELECT status, score FROM quiz_attempts WHERE id = ?");
$stmt->execute([$attemptId]);
$attempt = $stmt->fetch();
if ($attempt['status'] == 'completed') {
    echo "✅ Attempt marked COMPLETED.\n";
    echo "   Final Score: " . $attempt['score'] . "\n";
} else {
    echo "❌ Attempt status not updated!\n";
}

// Check File Cleanup
if (!file_exists($jsonPath)) {
    echo "✅ JSON Cache File deleted (Cleanup successful).\n";
} else {
    echo "⚠️ JSON File still exists (Cleanup failed or not implemented?)\n";
}

echo "\n---------------------------------------------------\n";
echo "🎉 ENTERPRISE SYSTEM VERIFICATION COMPLETE\n";
echo "---------------------------------------------------\n";
