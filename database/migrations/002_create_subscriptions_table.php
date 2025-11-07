<?php
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $pdo = Database::getInstance()->getPdo();
    
    $sql = "CREATE TABLE IF NOT EXISTS subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price_monthly DECIMAL(10,2) DEFAULT 0,
        price_yearly DECIMAL(10,2) DEFAULT 0,
        features JSON,
        calculator_limit INT DEFAULT 10,
        max_projects INT DEFAULT 5,
        max_team_members INT DEFAULT 1,
        is_active BOOLEAN DEFAULT true,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "✅ Created subscriptions table\n";
} catch (Exception $e) {
    echo "❌ Error creating subscriptions table: " . $e->getMessage() . "\n";
}
