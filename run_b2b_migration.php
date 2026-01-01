<?php

require_once __DIR__ . '/public/index.php'; // Boot app to get DB connection

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Starting B2B Sponsorship Migration...\n";

try {
    // 1. Sponsors Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS sponsors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NULL,
        logo_path VARCHAR(255) NULL,
        website_url VARCHAR(255) NULL,
        contact_person VARCHAR(100) NULL,
        contact_email VARCHAR(150) NULL,
        phone VARCHAR(50) NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✔ Created 'sponsors' table.\n";

    // 2. Campaigns Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS campaigns (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sponsor_id INT NOT NULL,
        calculator_slug VARCHAR(100) NOT NULL COMMENT 'Target calculator identifier',
        title VARCHAR(255) NOT NULL COMMENT 'Internal name',
        banner_image VARCHAR(255) NULL COMMENT 'Optional Override Image',
        ad_text VARCHAR(255) NULL,
        cta_text VARCHAR(50) DEFAULT 'Learn More',
        start_date DATETIME NOT NULL,
        end_date DATETIME NOT NULL,
        status ENUM('scheduled', 'active', 'paused', 'completed') DEFAULT 'scheduled',
        priority INT DEFAULT 0 COMMENT 'Higher shows first',
        max_impressions INT DEFAULT 0 COMMENT '0 = Unlimited',
        current_impressions INT DEFAULT 0,
        current_clicks INT DEFAULT 0,
        FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
        INDEX (calculator_slug),
        INDEX (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✔ Created 'campaigns' table.\n";

    // 3. Analytics Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS ad_impressions (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        campaign_id INT NOT NULL,
        user_id INT NULL,
        ip_hash VARCHAR(64) NOT NULL,
        user_agent VARCHAR(255) NULL,
        action_type ENUM('view', 'click') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
        INDEX (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✔ Created 'ad_impressions' table.\n";

    echo "Migration Completed Successfully!\n";

} catch (PDOException $e) {
    echo "❌ Migration Failed: " . $e->getMessage() . "\n";
}
