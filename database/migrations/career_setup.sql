-- Career System Setup

-- 1. Users Table Updates
ALTER TABLE users ADD COLUMN rank_title VARCHAR(50) DEFAULT 'Intern';
ALTER TABLE users ADD COLUMN study_mode ENUM('psc', 'world') DEFAULT 'psc';
ALTER TABLE users ADD COLUMN xp INT DEFAULT 0;

-- 2. Questions Table Updates
-- Using 'quiz_questions' as identified
ALTER TABLE quiz_questions ADD COLUMN target_audience ENUM('universal', 'psc_only', 'world_only') DEFAULT 'universal';
