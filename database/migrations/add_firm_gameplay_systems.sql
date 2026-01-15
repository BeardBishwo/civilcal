-- Firm Gameplay Systems - Database Schema
-- Run this migration to add all required tables and columns

-- 1. Firm Perks (Buffs that can be purchased)
CREATE TABLE IF NOT EXISTS firm_perks (
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_perk_type (perk_type),
    INDEX idx_min_level (min_firm_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Active Perk Purchases
CREATE TABLE IF NOT EXISTS firm_perk_purchases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guild_id INT NOT NULL,
    perk_id INT NOT NULL,
    purchased_by INT NOT NULL,
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT 1,
    FOREIGN KEY (guild_id) REFERENCES guilds(id) ON DELETE CASCADE,
    FOREIGN KEY (perk_id) REFERENCES firm_perks(id),
    FOREIGN KEY (purchased_by) REFERENCES users(id),
    INDEX idx_guild_active (guild_id, is_active),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bi-Weekly Stats (for leaderboard)
CREATE TABLE IF NOT EXISTS firm_biweekly_stats (
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
    UNIQUE KEY unique_period (guild_id, period_start),
    FOREIGN KEY (guild_id) REFERENCES guilds(id) ON DELETE CASCADE,
    INDEX idx_period (period_start, period_end),
    INDEX idx_tier (tier)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Monthly Bonus Tracking
CREATE TABLE IF NOT EXISTS firm_monthly_rankings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guild_id INT NOT NULL,
    month_start DATE NOT NULL,
    total_xp INT DEFAULT 0,
    rank_position INT,
    bonus_coins INT DEFAULT 0,
    is_finalized BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_month (guild_id, month_start),
    FOREIGN KEY (guild_id) REFERENCES guilds(id) ON DELETE CASCADE,
    INDEX idx_month (month_start),
    INDEX idx_rank (rank_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Vault Spending Audit Log
CREATE TABLE IF NOT EXISTS firm_vault_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guild_id INT NOT NULL,
    transaction_type ENUM('deposit', 'perk_purchase', 'dividend', 'other') NOT NULL,
    resource_type ENUM('coins', 'bricks', 'steel', 'cement') NOT NULL,
    amount INT NOT NULL,
    balance_after INT NOT NULL,
    performed_by INT NOT NULL,
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guild_id) REFERENCES guilds(id) ON DELETE CASCADE,
    FOREIGN KEY (performed_by) REFERENCES users(id),
    INDEX idx_guild_date (guild_id, created_at),
    INDEX idx_type (transaction_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Add new columns to guilds table
ALTER TABLE guilds 
ADD COLUMN IF NOT EXISTS max_members INT DEFAULT 10,
ADD COLUMN IF NOT EXISTS co_leaders JSON DEFAULT NULL,
ADD COLUMN IF NOT EXISTS total_xp_earned BIGINT DEFAULT 0,
ADD COLUMN IF NOT EXISTS current_period_xp INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS last_xp_reset DATE DEFAULT NULL,
ADD COLUMN IF NOT EXISTS catch_up_multiplier DECIMAL(3,2) DEFAULT 1.00;

-- 7. Seed initial perks
INSERT INTO firm_perks (name, description, perk_type, boost_multiplier, duration_hours, cost_coins, min_firm_level, icon) VALUES
('2x XP Boost (24h)', 'All members earn double XP for 24 hours', 'xp_boost', 2.00, 24, 5000, 3, 'zap'),
('1.5x XP Boost (7 days)', 'All members earn 50% bonus XP for 7 days', 'xp_boost', 1.50, 168, 15000, 5, 'trending-up'),
('2x Coin Boost (24h)', 'All members earn double coins for 24 hours', 'coin_boost', 2.00, 24, 5000, 3, 'dollar-sign'),
('1.5x Coin Boost (7 days)', 'All members earn 50% bonus coins for 7 days', 'coin_boost', 1.50, 168, 15000, 5, 'coins'),
('Resource Boost (24h)', 'All members earn 50% more resources for 24 hours', 'resource_boost', 1.50, 24, 3000, 1, 'package'),
('Member Cap +10', 'Permanently increase member capacity by 10', 'member_cap', 1.00, 0, 0, 10, 'users')
ON DUPLICATE KEY UPDATE name=name;

-- Update the last member cap perk cost
UPDATE firm_perks SET cost_bricks = 10000, cost_steel = 5000 WHERE name = 'Member Cap +10';
