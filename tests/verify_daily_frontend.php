<?php
// tests/verify_daily_frontend.php

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Services\Quiz\DailyQuizService;
use App\Services\Quiz\StreakService;

// MOCK SESSION
$_SESSION['user_id'] = 99999;
$_SESSION['user'] = ['stream_id' => null];

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "🔍 Starting Daily Quest Frontend Simulation...\n";

// 1. Setup Test Data
$date = date('Y-m-d');
$testUserId = 99999;
$pdo->exec("DELETE FROM daily_quiz_attempts WHERE user_id = $testUserId");
$pdo->exec("DELETE FROM user_streaks WHERE user_id = $testUserId");
$pdo->exec("DELETE FROM quiz_attempts WHERE user_id = $testUserId AND exam_id IN (SELECT id FROM quiz_exams WHERE slug = 'daily-quest')");

// Ensure Daily Quiz exists for today
$dailyService = new DailyQuizService();
$dailyService->createDailyQuiz($date, null);
$dailyQuiz = $dailyService->getQuizForUser($date, null);

if (!$dailyQuiz) die("❌ Failed to ensure Daily Quiz exists.\n");
echo "✅ Daily Quiz for today exists (ID: {$dailyQuiz['id']}).\n";

// Ensure 'daily-quest' exam exists
$stmt = $pdo->prepare("SELECT * FROM quiz_exams WHERE slug = 'daily-quest'");
$stmt->execute();
$exam = $stmt->fetch();
if (!$exam) die("❌ 'daily-quest' exam placeholder missing.\n");
echo "✅ 'daily-quest' exam placeholder exists (ID: {$exam['id']}).\n";

// 2. SIMULATE START (Controller Logic)
echo "\n--- Simulating Start ---\n";

// Create Attempt DB
$sql = "INSERT INTO quiz_attempts (user_id, exam_id, status, started_at) VALUES (:uid, :eid, 'ongoing', NOW())";
$stmtInsert = $pdo->prepare($sql);
$stmtInsert->execute(['uid' => $testUserId, 'eid' => $exam['id']]);
$attemptId = $pdo->lastInsertId();
echo "✅ Created quiz_attempt ID: $attemptId\n";

// Initialize Cache (Mocking initializeCache method)
$storagePath = __DIR__ . '/../storage/app/exams/';
if (!is_dir($storagePath)) mkdir($storagePath, 0777, true);

$questionIds = json_decode($dailyQuiz['questions'], true);
$questions = [];
if (!empty($questionIds)) {
    $placeholders = str_repeat('?,', count($questionIds) - 1) . '?';
    $sqlQ = "SELECT id, type, content FROM quiz_questions WHERE id IN ($placeholders)";
    $stmtQ = $pdo->prepare($sqlQ);
    $stmtQ->execute($questionIds);
    $questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);
}

$data = [
    'attempt_id' => $attemptId,
    'user_id' => $testUserId,
    'exam' => $exam,
    'questions' => $questions, // Simplified for test
    'answers' => [],
    'start_time' => time(),
    'daily_quiz_id' => $dailyQuiz['id']
];

$jsonFile = $storagePath . $attemptId . '.json';
file_put_contents($jsonFile, json_encode($data));

if (file_exists($jsonFile)) {
    echo "✅ JSON Cache created at $jsonFile\n";
    $readBack = json_decode(file_get_contents($jsonFile), true);
    if (isset($readBack['daily_quiz_id']) && $readBack['daily_quiz_id'] == $dailyQuiz['id']) {
        echo "✅ JSON Cache contains correct daily_quiz_id.\n";
    } else {
        echo "❌ JSON Cache MISSING daily_quiz_id!\n";
    }
} else {
    echo "❌ Failed to create JSON Cache.\n";
}

// 3. SIMULATE SUBMIT (Controller Logic)
echo "\n--- Simulating Submit ---\n";

// Mock Streak Service logic
$streakService = new StreakService();

// Mock "Process Submission"
// Read JSON
$cacheData = json_decode(file_get_contents($jsonFile), true);

if (isset($cacheData['daily_quiz_id'])) {
    echo "   Processing Daily Quest submission...\n";
    
    // Call Streak Service
    // We need a valid user in DB for FK.
    // If 99999 doesn't exist in users table, this will fail.
    // Let's check if 99999 exists, if not, use a real user ID for this part ONLY if safe.
    // OR create a dummy user.
    $checkUser = $pdo->query("SELECT id FROM users WHERE id = $testUserId")->fetch();
    if (!$checkUser) {
        // Create dummy user
        $pdo->exec("INSERT INTO users (id, username, email, password, role) VALUES ($testUserId, 'test_daily_bot', 'bot@test.com', 'hash', 'user')");

        echo "   (Created dummy user $testUserId)\n";
    }

    try {
        $streakRes = $streakService->processVictory($testUserId, 50);
        echo "✅ Streak Processed. Multiplier: " . $streakRes['multiplier'] . "x\n";

        // Record Daily Attempt
        $dailyService->recordAttempt($testUserId, $cacheData['daily_quiz_id'], 100, $streakRes['total_coins']); // Mock Score 100
        echo "✅ Daily Attempt Recorded in DB.\n";

        // Check DB
        $chk = $dailyService->checkAttempt($testUserId, $date);
        if ($chk) {
            echo "✅ Verified: checkAttempt() returns true.\n";
            echo "   Coins Earned: " . $chk['coins_earned'] . "\n";
        } else {
            echo "❌ Verification Failed: checkAttempt() returned false.\n";
        }

    } catch (Exception $e) {
        echo "❌ Error in Submit Logic: " . $e->getMessage() . "\n";
    }

} else {
    echo "❌ Daily Quiz ID missing in cache data (Submit simulation skipped).\n";
}

// Cleanup
unlink($jsonFile);
// $pdo->exec("DELETE FROM users WHERE id = $testUserId"); // Optional: keep for debugging

echo "\n🎉 Frontend Simulation Complete!\n";
