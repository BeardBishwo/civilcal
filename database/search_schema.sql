-- =====================================================
-- Global Search - Database Schema
-- =====================================================

CREATE TABLE IF NOT EXISTS search_index (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    type VARCHAR(50) NOT NULL COMMENT 'page, calculator, post, setting, user',
    url VARCHAR(255) NOT NULL,
    entity_id INT NULL COMMENT 'ID of the original entity',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FULLTEXT(title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
