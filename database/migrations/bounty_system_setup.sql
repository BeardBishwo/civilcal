-- Create bounty_requests table
CREATE TABLE IF NOT EXISTS bounty_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    bounty_amount INT NOT NULL,
    status ENUM('open', 'filled', 'cancelled') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create bounty_submissions table
CREATE TABLE IF NOT EXISTS bounty_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bounty_id INT NOT NULL,
    uploader_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    admin_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending', -- Admin check
    client_status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending', -- Client check
    rejection_reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bounty_id) REFERENCES bounty_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
