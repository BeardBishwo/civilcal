-- Database Constraints: Add Data Integrity Checks
-- This migration adds CHECK constraints to prevent data corruption
-- Requires MySQL 8.0.16+

-- ============================================
-- GAMIFICATION ECONOMY CONSTRAINTS
-- ============================================

-- Prevent negative resource balances
ALTER TABLE user_resources
ADD CONSTRAINT chk_coins_positive CHECK (coins >= 0),
ADD CONSTRAINT chk_bricks_positive CHECK (bricks >= 0),
ADD CONSTRAINT chk_cement_positive CHECK (cement >= 0),
ADD CONSTRAINT chk_steel_positive CHECK (steel >= 0);

-- ============================================
-- QUIZ QUESTION CONSTRAINTS
-- ============================================

-- Ensure valid difficulty levels (1-5)
ALTER TABLE quiz_questions
ADD CONSTRAINT chk_difficulty_range CHECK (difficulty_level BETWEEN 1 AND 5);

-- ============================================
-- QUIZ ATTEMPT CONSTRAINTS
-- ============================================

-- Ensure valid accuracy percentage (0-100)
ALTER TABLE quiz_attempts
ADD CONSTRAINT chk_accuracy_range CHECK (accuracy BETWEEN 0 AND 100);

-- Ensure score is not negative
ALTER TABLE quiz_attempts
ADD CONSTRAINT chk_score_positive CHECK (score >= 0);

-- ============================================
-- QUESTION STREAM MAP CONSTRAINTS
-- ============================================

-- Ensure valid difficulty in stream (1-5)
ALTER TABLE question_stream_map
ADD CONSTRAINT chk_stream_difficulty_range CHECK (difficulty_in_stream BETWEEN 1 AND 5);
