<?php
// Debug Vault Logic Isolated
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/app/bootstrap.php';

use App\Services\FirmService;
use App\Core\Database;

$db = Database::getInstance();
$service = new FirmService();

// 1. Setup Data
$userId = 1;
// Clean
$db->query("DELETE FROM guilds WHERE leader_id = $userId");
$db->query("DELETE FROM guild_vault WHERE guild_id IN (SELECT id FROM guilds WHERE leader_id = $userId)");

// Create
$service->createFirm($userId, 'Debug Corp', 'Debug');
$guild = $db->query("SELECT * FROM guilds WHERE leader_id = $userId")->fetch();
$guildId = $guild['id'];
$db->query("UPDATE guilds SET level = 10 WHERE id = $guildId");

// Set Vault
$db->query("UPDATE guild_vault SET amount = 50000 WHERE guild_id = $guildId AND resource_type = 'coins'");
$db->query("UPDATE guild_vault SET amount = 5000 WHERE guild_id = $guildId AND resource_type = 'bricks'");

echo "Guild ID: $guildId\n";
echo "Vault in DB:\n";
$rows = $db->query("SELECT * FROM guild_vault WHERE guild_id = $guildId")->fetchAll();
foreach ($rows as $r) echo " - {$r['resource_type']}: {$r['amount']}\n";

// Get Perk
$perk = $db->query("SELECT * FROM firm_perks WHERE perk_type='xp_boost' AND duration_hours=24")->fetch();
echo "Perk Cost: {$perk['cost_coins']}\n";

// Try Purchase
try {
    $res = $service->purchasePerk($guildId, $perk['id'], $userId);
    echo "Purchase Result: " . json_encode($res) . "\n";
} catch (Exception $e) {
    echo "Purchase FAILED: " . $e->getMessage() . "\n";
}
