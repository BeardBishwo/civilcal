<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Services\GamificationService;
use App\Services\RankService;

$userId = 1; // Assuming user 1 exists

echo "--- Gamification Verification ---\n";

$gs = new GamificationService();
$rs = new RankService();

// 1. Check Wallet Init
$gs->initWallet($userId);
$wallet = $gs->getWallet($userId);
echo "Wallet Initialized: " . (isset($wallet['wood_logs']) ? "PASS" : "FAIL") . "\n";

// 2. Check Daily Bonus
$result = $gs->processDailyLoginBonus($userId);
echo "Daily Bonus Processed: " . ($result['success'] ? "YES" : "ALREADY CLAIMED") . "\n";

// 3. Check Crafting Logic
// Give some logs for testing
$pdo = \App\Core\Database::getInstance()->getPdo();
$pdo->exec("UPDATE user_resources SET wood_logs = 1, coins = 10 WHERE user_id = $userId");
$craft = $gs->craftPlanks($userId, 1);
echo "Sawmill Crafting (1 Log -> 4 Planks): " . ($craft['success'] ? "PASS" : "FAIL") . " - " . $craft['message'] . "\n";

// 4. Check Rank Service
$stats = [
    'news_reads_count' => 10,
    'quizzes_completed_count' => 5,
    'calculations_count' => 20
];
// Test for Laborer
$rank1 = $rs->getUserRankData($stats, ['coins' => 0, 'bricks' => 0, 'steel' => 0]);
echo "Rank at 0 Steel: " . $rank1['rank'] . " (Expected: Laborer)\n";

// Test for Chief Engineer (1000 Steel)
$rank2 = $rs->getUserRankData($stats, ['coins' => 0, 'bricks' => 0, 'steel' => 1000]);
echo "Rank at 1000 Steel: " . $rank2['rank'] . " (Expected: Chief Engineer)\n";

echo "--- Verification Complete ---\n";
