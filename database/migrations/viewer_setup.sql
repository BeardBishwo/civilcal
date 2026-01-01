-- Viewer and Referrals Setup

-- 1. Add preview_path to library_files for CAD screenshot requirement
ALTER TABLE library_files ADD COLUMN preview_path VARCHAR(255) DEFAULT NULL AFTER file_path;

-- 2. Referrals Table (Detailed Tracking)
CREATE TABLE IF NOT EXISTS referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inviter_id INT NOT NULL,
    new_user_id INT NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending', -- 'Completed' after 5 quizzes
    reward_paid TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inviter_id) REFERENCES users(id),
    FOREIGN KEY (new_user_id) REFERENCES users(id)
);
