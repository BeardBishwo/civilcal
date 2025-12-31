-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id),
    INDEX (is_read)
);

-- Library Reviews Table
CREATE TABLE IF NOT EXISTS library_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (file_id, reviewer_id),
    INDEX (file_id)
);

-- Library Reports Table
CREATE TABLE IF NOT EXISTS library_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_id INT NOT NULL,
    reporter_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_report (file_id, reporter_id)
);

-- Add Columns to library_files for reporting
SET @exist := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'library_files' AND column_name = 'report_count');
SET @sql := IF(@exist = 0, 'ALTER TABLE library_files ADD COLUMN report_count INT DEFAULT 0', 'SELECT "Column report_count already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;

-- Add Referral Columns to users
SET @exist2 := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'referral_code');
SET @sql2 := IF(@exist2 = 0, 'ALTER TABLE users ADD COLUMN referral_code VARCHAR(50) UNIQUE DEFAULT NULL, ADD COLUMN referred_by INT DEFAULT NULL, ADD COLUMN quiz_solved_count INT DEFAULT 0', 'SELECT "Referral columns already exist"');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
