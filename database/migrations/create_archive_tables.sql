-- Create Archive Tables for Historical Data
-- This migration creates archive tables to store old data
-- Keeps main tables fast by moving historical records

-- ============================================
-- QUIZ ATTEMPTS ARCHIVE
-- ============================================

-- Archive table for quiz attempts older than 1 year
CREATE TABLE IF NOT EXISTS quiz_attempts_archive LIKE quiz_attempts;

-- Add index on completed_at for archive queries
ALTER TABLE quiz_attempts_archive
ADD INDEX idx_completed_at (completed_at);

-- ============================================
-- QUIZ ATTEMPT ANSWERS ARCHIVE
-- ============================================

-- Archive table for attempt answers
CREATE TABLE IF NOT EXISTS quiz_attempt_answers_archive LIKE quiz_attempt_answers;

-- Add index on created_at for archive queries
ALTER TABLE quiz_attempt_answers_archive
ADD INDEX idx_created_at (created_at);

-- ============================================
-- ACTIVITY LOGS ARCHIVE
-- ============================================

-- Archive table for old activity logs
CREATE TABLE IF NOT EXISTS activity_logs_archive LIKE activity_logs;

-- Add index on created_at for archive queries
ALTER TABLE activity_logs_archive
ADD INDEX idx_created_at (created_at);

-- ============================================
-- USER RESOURCE LOGS ARCHIVE
-- ============================================

-- Archive table for old resource transaction logs
CREATE TABLE IF NOT EXISTS user_resource_logs_archive LIKE user_resource_logs;

-- Add index on created_at for archive queries
ALTER TABLE user_resource_logs_archive
ADD INDEX idx_created_at (created_at);

-- ============================================
-- ARCHIVING PROCEDURE
-- ============================================

DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS archive_old_data()
BEGIN
    DECLARE archived_attempts INT DEFAULT 0;
    DECLARE archived_answers INT DEFAULT 0;
    DECLARE archived_activity INT DEFAULT 0;
    DECLARE archived_resources INT DEFAULT 0;
    
    -- Archive quiz attempts older than 1 year
    INSERT INTO quiz_attempts_archive
    SELECT * FROM quiz_attempts
    WHERE completed_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)
    AND status = 'completed';
    
    SET archived_attempts = ROW_COUNT();
    
    -- Delete archived attempts from main table
    DELETE FROM quiz_attempts
    WHERE completed_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)
    AND status = 'completed';
    
    -- Archive activity logs older than 6 months
    INSERT INTO activity_logs_archive
    SELECT * FROM activity_logs
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
    
    SET archived_activity = ROW_COUNT();
    
    -- Delete archived activity logs
    DELETE FROM activity_logs
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
    
    -- Archive resource logs older than 6 months
    INSERT INTO user_resource_logs_archive
    SELECT * FROM user_resource_logs
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
    
    SET archived_resources = ROW_COUNT();
    
    -- Delete archived resource logs
    DELETE FROM user_resource_logs
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
    
    -- Log archiving results
    SELECT 
        archived_attempts AS 'Quiz Attempts Archived',
        archived_activity AS 'Activity Logs Archived',
        archived_resources AS 'Resource Logs Archived',
        NOW() AS 'Archived At';
END$$

DELIMITER ;

-- ============================================
-- USAGE INSTRUCTIONS
-- ============================================

-- To run archiving manually:
-- CALL archive_old_data();

-- To schedule monthly archiving (add to crontab):
-- 0 2 1 * * mysql -u root -p calculator_db -e "CALL archive_old_data();"
