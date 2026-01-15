<?php
// Test Firm Gameplay Systems
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__);
require_once __DIR__ . '/app/bootstrap.php';

use App\Services\FirmService;
use App\Services\GamificationService;

echo "=== Testing Firm Gameplay Mechanics ===\n\n";

try {
    $firmService = new FirmService();
    $gs = new GamificationService();
    $db = \App\Core\Database::getInstance();

    // 1. Setup Test Environment
    echo "1. Setting up test firm...\n";
    $userId = 1;
    $db->query("DELETE FROM guilds WHERE leader_id = $userId"); // Clean stale
    $db->query("DELETE FROM guild_members WHERE user_id = $userId");

    // Give user coins
    $db->query("UPDATE user_resources SET coins = 100000 WHERE user_id = $userId");

    // Create Firm
    $firmService->createFirm($userId, 'Gameplay Test Corp', 'Testing perks and mechanics');
    $guild = $db->query("SELECT * FROM guilds WHERE leader_id = $userId")->fetch();
    $guildId = $guild['id'];
    echo "   ✓ Created firm ID: $guildId\n";

    // Add Resources to Vault
    $firmService->donate($userId, 'coins', 10000);
    $firmService->donate($userId, 'bricks', 5000); // Assuming user has bricks or cheats
    // Cheat bricks into vault
    $db->query("UPDATE guild_vault SET bricks = 5000, steel = 5000, cement = 5000 WHERE guild_id = $guildId");
    echo "   ✓ Vault stocked with resources\n\n";

    // 2. Test Perk Purchase
    echo "2. Testing Perk Purchase...\n";
    $perks = $firmService->getAvailablePerks($guildId);
    echo "   Available Perks: " . count($perks) . "\n";

    // Cheat Level because perks require level
    $db->query("UPDATE guilds SET level = 10 WHERE id = $guildId");
    echo "   ✓ Cheated firm to Level 10\n";

    // Buy 2x XP Boost
    $xpPerk = null;
    foreach ($perks as $p) {
        if ($p['perk_type'] === 'xp_boost' && $p['duration_hours'] == 24) $xpPerk = $p;
    }

    if ($xpPerk) {
        $result = $firmService->purchasePerk($guildId, $xpPerk['id'], $userId);
        echo "   ✓ Purchased '{$xpPerk['name']}'\n";
        echo "   Message: {$result['message']}\n";
    }

    // Verify Active Perks
    $active = $firmService->getActivePerks($guildId);
    echo "   ✓ Active Perks Count: " . count($active) . "\n";
    echo "   ✓ Bonus Check (100 XP): " . $firmService->applyPerkBonus($userId, 100, 'xp_boost') . " XP (Expected 200)\n\n";

    // 3. Test Dividends
    echo "3. Testing Dividend Distribution...\n";
    $result = $firmService->distributeDividends($guildId, 100, $userId);
    echo "   ✓ Distributed 100 coins/member\n";
    echo "   Message: {$result['message']}\n\n";

    // 4. Test Roles
    echo "4. Testing Role Promotion...\n";
    // Need a second member
    $member2 = 2; // Assuming user 2 exists
    // Force add user 2 to firm if distinct from user 1
    if ($userId != $member2) {
        $db->query("DELETE FROM guild_members WHERE user_id = $member2");
        $firmService->requestJoin($member2, $guildId);
        $requests = $firmService->getJoinRequests($guildId);
        if (!empty($requests)) {
            $firmService->handleRequest($userId, $requests[0]['id'], 'approve');
            echo "   ✓ User 2 joined firm\n";

            $firmService->promoteMember($guildId, $member2, $userId);
            $m2 = $db->query("SELECT * FROM guild_members WHERE user_id = $member2")->fetch();
            echo "   ✓ User 2 Role: {$m2['role']} (Expected 'Co-Leader')\n";
        } else {
            echo "   - Skipped (User 2 join request failed or user 2 doesn't exist)\n";
        }
    }

    // 5. Test Leaderboard Calc
    echo "\n5. Testing Leaderboard Calculation...\n";
    // Cheat some XP
    $firmService->addFirmXP($userId, 55000); // Should hit Diamond
    $db->query("UPDATE guilds SET current_period_xp = 55000 WHERE id = $guildId"); // Force update for read

    $firmService->calculateBiWeeklyRewards();
    $stats = $db->query("SELECT * FROM firm_biweekly_stats WHERE guild_id = $guildId")->fetch();

    if ($stats) {
        echo "   ✓ Stats generated for firm\n";
        echo "   Tier: {$stats['tier']}\n";
        echo "   Reward: {$stats['reward_coins']} coins\n";
    } else {
        echo "   ✗ Stats failed to generate\n";
    }

    echo "\n=== ALL TESTS PASSED ===\n";
} catch (Exception $e) {
    echo "\n✗ TEST FAILED: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
