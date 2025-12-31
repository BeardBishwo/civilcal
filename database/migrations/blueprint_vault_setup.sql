-- Add coins column to users if not exists
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'coins');
SET @sql := IF (@exist = 0, 'ALTER TABLE users ADD COLUMN coins INT DEFAULT 0 AFTER role', 'SELECT "Column coins already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create library_files table
CREATE TABLE IF NOT EXISTS library_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uploader_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    file_type ENUM('cad', 'excel', 'pdf', 'image', 'doc', 'other') NOT NULL,
    file_size_kb INT DEFAULT 0,
    price_coins INT DEFAULT 50,
    downloads_count INT DEFAULT 0,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_note VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create user_transactions table
CREATE TABLE IF NOT EXISTS user_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount INT NOT NULL, -- Positive for earning, Negative for spending
    type ENUM('upload_reward', 'download_cost', 'admin_adjustment', 'referral', 'purchase') NOT NULL,
    reference_id INT DEFAULT NULL, -- ID of library_file or other related entity
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
