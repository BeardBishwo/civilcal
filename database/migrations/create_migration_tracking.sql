-- Migration Tracking System
-- Creates a migrations table to track executed migrations
-- Ensures idempotency (safe to run migrations multiple times)

CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration_name VARCHAR(255) UNIQUE NOT NULL,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    execution_time_ms INT DEFAULT 0,
    status ENUM('success', 'failed') DEFAULT 'success',
    error_message TEXT NULL,
    INDEX idx_executed_at (executed_at),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert record for this migration
INSERT IGNORE INTO migrations (migration_name) VALUES ('create_migration_tracking.sql');
