<?php
// Check and fix charset issues, then create tables
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Firm Gameplay Migration (Fixed) ===\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bishwo_calculator', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check guilds table charset
    echo "Checking guilds table...\n";
    $stmt = $pdo->query("SHOW CREATE TABLE guilds");
    $create = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Guilds table found\n\n";

    // Create tables WITHOUT foreign keys first
    echo "1. Creating firm_perks (no FK)...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS firm_perks (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        perk_type ENUM('xp_boost', 'coin_boost', 'resource_boost', 'member_cap', 'custom') NOT NULL,
        boost_multiplier DECIMAL(3,2) DEFAULT 1.00,
        duration_hours INT DEFAULT 24,
        cost_coins INT DEFAULT 0,
        cost_bricks INT DEFAULT 0,
        cost_steel INT DEFAULT 0,
        cost_cement INT DEFAULT 0,
        min_firm_level INT DEFAULT 1,
        icon VARCHAR(50) DEFAULT 'gift',
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Created\n\n";

    echo "2. Creating firm_perk_purchases (no FK)...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS firm_perk_purchases (
        id INT PRIMARY KEY AUTO_INCREMENT,
        guild_id INT NOT NULL,
        perk_id INT NOT NULL,
        purchased_by INT NOT NULL,
        purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NULL,
        is_active BOOLEAN DEFAULT 1
    )");
    echo "✓ Created\n\n";

    echo "3. Creating firm_biweekly_stats (no FK)...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS firm_biweekly_stats (
        id INT PRIMARY KEY AUTO_INCREMENT,
        guild_id INT NOT NULL,
        period_start DATE NOT NULL,
        period_end DATE NOT NULL,
        total_xp_earned INT DEFAULT 0,
        total_quizzes_completed INT DEFAULT 0,
        total_resources_donated INT DEFAULT 0,
        total_members_recruited INT DEFAULT 0,
        average_quiz_score DECIMAL(5,2) DEFAULT 0,
        tier VARCHAR(20) DEFAULT 'Bronze',
        reward_coins INT DEFAULT 0,
        is_finalized BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_period (guild_id, period_start)
    )");
    echo "✓ Created\n\n";

    echo "4. Creating firm_monthly_rankings (no FK)...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS firm_monthly_rankings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        guild_id INT NOT NULL,
        month_start DATE NOT NULL,
        total_xp INT DEFAULT 0,
        rank_position INT,
        bonus_coins INT DEFAULT 0,
        is_finalized BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_month (guild_id, month_start)
    )");
    echo "✓ Created\n\n";

    echo "5. Creating firm_vault_transactions (no FK)...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS firm_vault_transactions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        guild_id INT NOT NULL,
        transaction_type ENUM('deposit', 'perk_purchase', 'dividend', 'other') NOT NULL,
        resource_type ENUM('coins', 'bricks', 'steel', 'cement') NOT NULL,
        amount INT NOT NULL,
        balance_after INT NOT NULL,
        performed_by INT NOT NULL,
        reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Created\n\n";

    // Add columns to guilds
    echo "6. Adding columns to guilds...\n";
    $columns = [
        "max_members INT DEFAULT 10",
        "co_leaders JSON DEFAULT NULL",
        "total_xp_earned BIGINT DEFAULT 0",
        "current_period_xp INT DEFAULT 0",
        "last_xp_reset DATE DEFAULT NULL",
        "catch_up_multiplier DECIMAL(3,2) DEFAULT 1.00"
    ];

    foreach ($columns as $col) {
        $colName = explode(' ', $col)[0];
        try {
            $pdo->exec("ALTER TABLE guilds ADD COLUMN $col");
            echo "✓ Added: $colName\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "- Exists: $colName\n";
            } else {
                echo "✗ Error on $colName: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "\n";

    // Seed perks
    echo "7. Seeding perks...\n";
    $perks = [
        ['2x XP Boost (24h)', 'All members earn double XP for 24 hours', 'xp_boost', 2.00, 24, 5000, 0, 0, 0, 3, 'zap'],
        ['1.5x XP Boost (7 days)', 'All members earn 50% bonus XP for 7 days', 'xp_boost', 1.50, 168, 15000, 0, 0, 0, 5, 'trending-up'],
        ['2x Coin Boost (24h)', 'All members earn double coins for 24 hours', 'coin_boost', 2.00, 24, 5000, 0, 0, 0, 3, 'dollar-sign'],
        ['1.5x Coin Boost (7 days)', 'All members earn 50% bonus coins for 7 days', 'coin_boost', 1.50, 168, 15000, 0, 0, 0, 5, 'coins'],
        ['Resource Boost (24h)', 'All members earn 50% more resources for 24 hours', 'resource_boost', 1.50, 24, 3000, 0, 0, 0, 1, 'package'],
        ['Member Cap +10', 'Permanently increase member capacity by 10', 'member_cap', 1.00, 0, 0, 10000, 5000, 0, 10, 'users']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO firm_perks (name, description, perk_type, boost_multiplier, duration_hours, cost_coins, cost_bricks, cost_steel, cost_cement, min_firm_level, icon) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $added = 0;
    foreach ($perks as $perk) {
        $stmt->execute($perk);
        $added += $stmt->rowCount();
    }

    echo "✓ Added $added new perks\n\n";

    $total = $pdo->query("SELECT COUNT(*) FROM firm_perks")->fetchColumn();
    echo "Total perks available: $total\n\n";

    echo "=== ✓ Migration Complete! ===\n";
} catch (PDOException $e) {
    echo "\n✗ Fatal Error: " . $e->getMessage() . "\n";
    exit(1);
}
