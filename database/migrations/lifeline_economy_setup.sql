-- =====================================================
-- Lifeline Economy & Nepali Support - Database Setup
-- =====================================================

-- Part 1: Convert tables to UTF-8mb4 for Nepali language support
-- This is CRITICAL - without this, Nepali text will show as ??????

ALTER TABLE quiz_exam_questions 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE quiz_questions 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE quiz_exams 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE quiz_categories 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Part 2: Add new resource columns to user_resources table
-- These support the expanded economy (sand, logs, planks)

ALTER TABLE user_resources 
ADD COLUMN IF NOT EXISTS sand INT DEFAULT 0 AFTER coins,
ADD COLUMN IF NOT EXISTS wood_logs INT DEFAULT 0 AFTER sand,
ADD COLUMN IF NOT EXISTS wood_planks INT DEFAULT 0 AFTER wood_logs;

-- Part 3: Verify the changes
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CHARACTER_SET_NAME,
    COLLATION_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'bishwo_calculator'
AND TABLE_NAME IN ('quiz_exam_questions', 'quiz_questions', 'quiz_exams', 'quiz_categories')
AND CHARACTER_SET_NAME IS NOT NULL;

-- Part 4: Show user_resources structure
DESCRIBE user_resources;
