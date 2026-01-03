<?php
// tests/verify_daily_quest.php

require_once __DIR__ . '/../app/bootstrap.php';

use App\Services\Quiz\DailyQuizService;
use App\Services\Quiz\StreakService;
use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "🔍 Starting Daily Quest Verification...\n";

// 1. Test Daily Gen
echo "\n--- Testing Auto-Generation ---\n";
$dailyInfo = new DailyQuizService();
$dailyInfo->autoGenerateWeek();

$count = $pdo->query("SELECT COUNT(*) FROM daily_quiz_schedule")->fetchColumn();
echo "✅ Scheduled Quizzes: $count (Expected >= 7)\n";

if ($count > 0) {
    $sample = $pdo->query("SELECT * FROM daily_quiz_schedule LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    echo "   Sample Date: " . $sample['date'] . "\n";
    echo "   Questions JSON: " . substr($sample['questions'], 0, 50) . "...\n";
} else {
    echo "❌ No quizzes generated. Do you have Syllabus Parts and Questions in DB?\n";
}

// 2. Test Streak Logic
echo "\n--- Testing Streak Logic ---\n";
$streakService = new StreakService();
$testUserId = 99999; // Test User
$pdo->exec("DELETE FROM user_streaks WHERE user_id = $testUserId");
// Mock User existence if needed, but foreign key might fail. 
// Assuming checking logic doesn't require users table entry unless FK constraint active.
// FK constraint IS active: CONSTRAINT `fk_streaks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
// So we need a valid user.
$validUser = $pdo->query("SELECT id FROM users LIMIT 1")->fetchColumn();

if ($validUser) {
    echo "   Using Test User ID: $validUser\n";
    
    // Reset streak for this user
    $pdo->exec("DELETE FROM user_streaks WHERE user_id = $validUser");

    // Day 1
    $result1 = $streakService->processVictory($validUser, 50);
    echo "   Day 1: " . ($result1['streak'] == 1 ? 'PASS' : 'FAIL') . " (Multiplier: {$result1['multiplier']}x)\n";

    // Simulate "Yesterday" was last activity to test increment
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $pdo->prepare("UPDATE user_streaks SET last_activity_date = ? WHERE user_id = ?")->execute([$yesterday, $validUser]);

    // Day 2 (Today)
    $result2 = $streakService->processVictory($validUser, 50);
    echo "   Day 2: " . ($result2['streak'] == 2 ? 'PASS' : 'FAIL') . " (Multiplier: {$result2['multiplier']}x)\n";

} else {
    echo "⚠️ No users found in DB to test FK constraints.\n";
}

echo "\n🎉 Verification Complete!\n";
