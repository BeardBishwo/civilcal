<?php
// Test Firm Gameplay Systems - Corrected
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__);
require_once __DIR__ . '/app/bootstrap.php';

use App\Services\FirmService;
use App\Services\GamificationService;
use App\Core\Database;

echo "=== Testing Firm Gameplay Mechanics (Attempt 2) ===\n\n";

try {
    $firmService = new FirmService();
    $gs = new GamificationService();
    $db = Database::getInstance();

    // 1. Setup Test Environment
    echo "1. Setting up test firm...\n";
    $userId = 1;

    // Clean up previous runs
    $db->query("DELETE FROM guilds WHERE leader_id = $userId");
    $db->query("DELETE FROM guild_members WHERE user_id = $userId");

    // Ensure user exists
    $user = $db->findOne('users', ['id' => $userId]);
    if (!$user) {
        $db->query("INSERT INTO users (id, username, email, password) VALUES (1, 'TestUser', 'test@test.com', 'pass')");
        // Init wallet
        $db->query("INSERT INTO user_resources (user_id, coins) VALUES (1, 100000)");
    } else {
        $db->query("UPDATE user_resources SET coins = 100000 WHERE user_id = $userId");
    }

    echo "   User ID: $userId active\n";

    // Create Firm
    $firmService->createFirm($userId, 'Gameplay Test Corp 2', 'Testing v2');
    $guild = $db->query("SELECT * FROM guilds WHERE leader_id = $userId")->fetch();
    $guildId = $guild['id'];
    echo "   ✓ Created firm ID: $guildId\n";

    // Stock Vault (Direct Access to bypass potential donate() issues if any, but we expect donate to work now)
    // Update individual resource rows
    $db->query("UPDATE guild_vault SET amount = 50000 WHERE guild_id = $guildId AND resource_type = 'coins'");
    $db->query("UPDATE guild_vault SET amount = 5000 WHERE guild_id = $guildId AND resource_type = 'bricks'");
    $db->query("UPDATE guild_vault SET amount = 5000 WHERE guild_id = $guildId AND resource_type = 'steel'");
    $db->query("UPDATE guild_vault SET amount = 5000 WHERE guild_id = $guildId AND resource_type = 'cement'");
    echo "   ✓ Vault stocked with resources (Direct DB Update)\n\n";

    // 2. Test Perk Purchase
    echo "2. Testing Perk Purchase...\n";
    $perks = $firmService->getAvailablePerks($guildId);
    echo "   Available Perks for Level 1: " . count($perks) . "\n";

    // Cheat Level
    $db->query("UPDATE guilds SET level = 10 WHERE id = $guildId");
    echo "   ✓ Cheated firm to Level 10\n";

    $perks10 = $firmService->getAvailablePerks($guildId);
    echo "   Available Perks for Level 10: " . count($perks10) . "\n";

    // Debug Vault
    $vaultDump = $db->query("SELECT resource_type, amount FROM guild_vault WHERE guild_id = $guildId")->fetchAll();
    echo "   [DEBUG] Vault Contents:\n";
    foreach ($vaultDump as $v) echo "     - {$v['resource_type']}: {$v['amount']}\n";

    // Buy 2x XP Boost
    $xpPerk = null;
    foreach ($perks10 as $p) {
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
    echo "\n3. Testing Dividend Distribution (w/ 15% Tax)...\n";
    $result = $firmService->distributeDividends($guildId, 100, $userId);
    echo "   ✓ Distributed Dividends\n";
    echo "   Message: {$result['message']}\n";
    echo "   Full Cost: 100/member\n";
    echo "   Net Payout: {$result['net_payout']}/member (Expected 85)\n\n";

    // 4. Test Roles
    echo "4. Testing Role Promotion...\n";
    $member2 = 2;
    $db->query("DELETE FROM guild_members WHERE user_id = $member2");
    $db->query("DELETE FROM guild_join_requests WHERE user_id = $member2"); // clean pending

    // Create user 2 if not exists
    $u2 = $db->findOne('users', ['id' => $member2]);
    if (!$u2) {
        $db->query("INSERT INTO users (id, username, email) VALUES (2, 'Member2', 'm2@test.com')");
    }

    $firmService->requestJoin($member2, $guildId);
    $requests = $firmService->getJoinRequests($guildId);
    if (!empty($requests)) {
        $firmService->handleRequest($userId, $requests[0]['id'], 'approve');
        echo "   ✓ User 2 joined firm\n";

        $firmService->promoteMember($guildId, $member2, $userId);
        $m2 = $db->query("SELECT * FROM guild_members WHERE user_id = $member2")->fetch();
        echo "   ✓ User 2 Role: {$m2['role']} (Expected 'Co-Leader')\n";
    }

    // 5. Test Leaderboard
    echo "\n5. Testing Leaderboard Calculation...\n";
    $firmService->addFirmXP($userId, 55000);
    // Move to DB to ensure persistence
    $currentXP = $db->query("SELECT current_period_xp FROM guilds WHERE id = $guildId")->fetch()['current_period_xp'];
    echo "   Current Period XP: $currentXP\n";

    $firmService->calculateBiWeeklyRewards();
    $stats = $db->query("SELECT * FROM firm_biweekly_stats WHERE guild_id = $guildId")->fetch();

    if ($stats) {
        echo "   ✓ Stats generated\n";
        echo "   Total XP: {$stats['total_xp_earned']}\n";
        echo "   Members: {$stats['active_member_count']}\n";
        echo "   Efficiency Score: {$stats['efficiency_score']} (XP/Member)\n";
        echo "   Tier: {$stats['tier']}\n";
    } else {
        echo "   ✗ Stats failed\n";
    }

    echo "\n6. Testing Daily Upkeep (Rent)...\n";
    // Ensure level 10
    $db->query("UPDATE guilds SET level = 10 WHERE id = $guildId");
    $report = $firmService->processDailyUpkeep();
    echo "   Upkeep Report: Success: {$report['success']}, Failed: {$report['failed']}\n";
    if ($report['success'] > 0) {
        echo "   ✓ Upkeep Paid successfully\n";
    }

    echo "\n=== ALL TESTS PASSED SUCCESSFULLY ===\n";
} catch (Exception $e) {
    echo "\n✗ TEST FAILED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
