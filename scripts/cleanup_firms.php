<?php
// Clean up existing firm memberships for testing
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bishwo_calculator', 'root', '');

echo "Cleaning up firm data for user 1...\n\n";

// Check current status
$stmt = $pdo->query("SELECT * FROM guild_members WHERE user_id = 1");
$member = $stmt->fetch();

if ($member) {
    echo "User 1 is in guild " . $member['guild_id'] . "\n";

    // Delete membership
    $pdo->query("DELETE FROM guild_members WHERE user_id = 1");
    echo "✓ Removed guild membership\n\n";
} else {
    echo "User 1 is not in any guild\n\n";
}

// Check for any guilds created by user 1
$stmt = $pdo->query("SELECT * FROM guilds WHERE leader_id = 1");
$guilds = $stmt->fetchAll();

if ($guilds) {
    echo "Found " . count($guilds) . " guild(s) led by user 1:\n";
    foreach ($guilds as $guild) {
        echo "  - Guild ID " . $guild['id'] . ": " . $guild['name'] . "\n";

        // Delete guild members
        $pdo->query("DELETE FROM guild_members WHERE guild_id = " . $guild['id']);

        // Delete guild vault
        $pdo->query("DELETE FROM guild_vault WHERE guild_id = " . $guild['id']);

        // Delete guild
        $pdo->query("DELETE FROM guilds WHERE id = " . $guild['id']);
    }
    echo "✓ Cleaned up all guilds\n\n";
}

echo "User 1 is now ready to create a new firm!\n";
