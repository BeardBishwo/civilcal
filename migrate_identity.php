<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();

try {
    echo "Starting Civil Identity System Migration...\n";

    // 1. Alter Users Table
    echo "1. Adding columns to users table...\n";
    try {
        $db->query("ALTER TABLE users ADD COLUMN total_xp INT DEFAULT 0");
        echo "   - Added total_xp\n";
    } catch (Exception $e) { echo "   - total_xp may already exist or error: " . $e->getMessage() . "\n"; }

    try {
        $db->query("ALTER TABLE users ADD COLUMN season_xp INT DEFAULT 0");
        echo "   - Added season_xp\n";
    } catch (Exception $e) { echo "   - season_xp may already exist or error: " . $e->getMessage() . "\n"; }

    try {
        $db->query("ALTER TABLE users ADD COLUMN avatar_id VARCHAR(50) DEFAULT 'avatar_starter_mascot'");
        echo "   - Added avatar_id\n";
    } catch (Exception $e) { echo "   - avatar_id may already exist or error: " . $e->getMessage() . "\n"; }

    try {
        $db->query("ALTER TABLE users ADD COLUMN frame_id VARCHAR(50) DEFAULT NULL");
        echo "   - Added frame_id\n";
    } catch (Exception $e) { echo "   - frame_id may already exist or error: " . $e->getMessage() . "\n"; }

    // 2. Create User Wardrobe Table
    echo "2. Creating user_wardrobe table...\n";
    $sqlWardrobe = "CREATE TABLE IF NOT EXISTS user_wardrobe (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        item_type ENUM('avatar', 'frame', 'trophy') NOT NULL,
        item_key VARCHAR(50) NOT NULL,
        obtained_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_item (user_id, item_type)
    )";
    $db->query($sqlWardrobe);
    echo "   - user_wardrobe table ready.\n";

    // 3. Create Hall of Fame Table
    echo "3. Creating hall_of_fame table...\n";
    $sqlHoF = "CREATE TABLE IF NOT EXISTS hall_of_fame (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        season_year INT NOT NULL,
        rank_position INT NOT NULL,
        final_xp INT NOT NULL,
        badge_awarded VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_season (season_year)
    )";
    $db->query($sqlHoF);
    echo "   - hall_of_fame table ready.\n";
    
    // 4. Populate Initial Wardrobe for Existing Users
    echo "4. Granting Starter Avatars to all users...\n";
    $users = $db->query("SELECT id FROM users")->fetchAll();
    $starters = ['avatar_starter_rookie_male', 'avatar_starter_rookie_female', 'avatar_starter_mascot'];
    
    $count = 0;
    foreach ($users as $u) {
        foreach ($starters as $item) {
            // Check availability to avoid duplicates if re-run
            $check = $db->query("SELECT id FROM user_wardrobe WHERE user_id = ? AND item_key = ?", [$u['id'], $item])->fetch();
            if (!$check) {
                $db->query("INSERT INTO user_wardrobe (user_id, item_type, item_key) VALUES (?, 'avatar', ?)", [$u['id'], $item]);
                $count++;
            }
        }
    }
    echo "   - Granted $count starter items to " . count($users) . " users.\n";

    echo "Migration Complete! 🚀\n";

} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
}
