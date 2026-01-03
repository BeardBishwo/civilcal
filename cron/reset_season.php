<?php
// CRON SCRIPT: Season Reset
// Run Schedule: Yearly (e.g. April 14th / Baisakh 1st)

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $currentYear = date('Y') - 1; // Closing the previous year's season

    echo "Starting Season Reset for Year: $currentYear\n";

    // 1. Identify Winners (Top 3)
    $winners = $db->query(
        "SELECT id, season_xp FROM users ORDER BY season_xp DESC LIMIT 3"
    )->fetchAll();

    if ($winners) {
        $badges = ['gold', 'silver', 'bronze'];
        foreach ($winners as $index => $winner) {
            $rank = $index + 1;
            $badge = $badges[$index] ?? 'participation';
            
            // Archive to Hall of Fame
            $db->query(
                "INSERT INTO hall_of_fame (user_id, season_year, rank_position, final_xp, badge_awarded) VALUES (?, ?, ?, ?, ?)",
                [$winner['id'], $currentYear, $rank, $winner['season_xp'], $badge]
            );

            // Grant Trophy to Wardrobe
            $trophyKey = "trophy_{$currentYear}_{$badge}";
            $db->query(
                "INSERT INTO user_wardrobe (user_id, item_type, item_key) VALUES (?, 'trophy', ?)",
                [$winner['id'], $trophyKey]
            );

            echo "   - Archived Rank #$rank (User ID: {$winner['id']}) with {$winner['season_xp']} XP.\n";
        }
    }

    // 2. Reset Season XP
    $db->query("UPDATE users SET season_xp = 0");
    echo "   - Reset season_xp for all users.\n";

    echo "Season Reset Complete! 🏆\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
