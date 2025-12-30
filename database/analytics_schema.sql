-- =====================================================
-- Dashboard Analytics - Database Schema
-- =====================================================

-- Table: analytics_events
-- Stores individual tracking events (page views, calculator usage, etc.)
CREATE TABLE IF NOT EXISTS analytics_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL COMMENT 'Type: page_view, calculator_use, search, download',
    event_category VARCHAR(50) NULL COMMENT 'Category for grouping (e.g., calculator name)',
    event_data JSON NULL COMMENT 'Additional event metadata',
    user_id INT NULL COMMENT 'User ID if logged in',
    session_id VARCHAR(100) NULL COMMENT 'Session identifier',
    ip_address VARCHAR(45) NULL COMMENT 'IPv4 or IPv6 address',
    user_agent TEXT NULL COMMENT 'Browser user agent string',
    referrer VARCHAR(500) NULL COMMENT 'HTTP referrer',
    page_url VARCHAR(500) NULL COMMENT 'Current page URL',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id),
    INDEX idx_session (session_id),
    INDEX idx_category (event_category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: analytics_summary
-- Stores pre-aggregated daily statistics for faster queries
CREATE TABLE IF NOT EXISTS analytics_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL COMMENT 'Summary date',
    metric_type VARCHAR(50) NOT NULL COMMENT 'Type: daily_views, unique_visitors, calculator_uses',
    metric_value INT DEFAULT 0 COMMENT 'Numeric value',
    metric_data JSON NULL COMMENT 'Additional breakdown data',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_metric (date, metric_type),
    INDEX idx_date (date),
    INDEX idx_metric_type (metric_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Sample Data for Testing
-- =====================================================

-- Insert sample page views
INSERT INTO analytics_events (event_type, event_category, page_url, ip_address, created_at) VALUES
('page_view', 'calculator', '/calculators/civil', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('page_view', 'calculator', '/calculators/electrical', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('page_view', 'home', '/', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('calculator_use', 'civil', '/calculators/civil/beam', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('calculator_use', 'electrical', '/calculators/electrical/voltage', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- =====================================================
-- Useful Queries for Analytics
-- =====================================================

-- Get total page views for last 30 days
-- SELECT DATE(created_at) as date, COUNT(*) as views
-- FROM analytics_events
-- WHERE event_type = 'page_view' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
-- GROUP BY DATE(created_at)
-- ORDER BY date DESC;

-- Get most popular calculators
-- SELECT event_category, COUNT(*) as uses
-- FROM analytics_events
-- WHERE event_type = 'calculator_use'
-- GROUP BY event_category
-- ORDER BY uses DESC
-- LIMIT 10;

-- Get unique visitors per day
-- SELECT DATE(created_at) as date, COUNT(DISTINCT ip_address) as unique_visitors
-- FROM analytics_events
-- WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
-- GROUP BY DATE(created_at)
-- ORDER BY date DESC;
