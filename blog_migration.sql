-- Blog Columns Migration for quiz_questions
-- Run this to add blog functionality

-- Add columns
ALTER TABLE quiz_questions ADD COLUMN slug VARCHAR(255) UNIQUE;
ALTER TABLE quiz_questions ADD COLUMN is_published_as_blog TINYINT(1) DEFAULT 0;
ALTER TABLE quiz_questions ADD COLUMN blog_published_at DATETIME;
ALTER TABLE quiz_questions ADD COLUMN view_count INT DEFAULT 0;
ALTER TABLE quiz_questions ADD COLUMN share_count INT DEFAULT 0;

-- Add indexes
CREATE INDEX idx_slug ON quiz_questions(slug);
CREATE INDEX idx_blog_published ON quiz_questions(is_published_as_blog, blog_published_at);
CREATE INDEX idx_view_count ON quiz_questions(view_count DESC);

-- Verify
SELECT COLUMN_NAME, DATA_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'quiz_questions' 
AND COLUMN_NAME IN ('slug', 'is_published_as_blog', 'blog_published_at', 'view_count', 'share_count');
