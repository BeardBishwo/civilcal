<?php
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $pdo = Database::getInstance()->getPdo();
    
    $sql = "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        subscription_id INT,
        amount DECIMAL(10,2),
        currency VARCHAR(10) DEFAULT 'USD',
        payment_method VARCHAR(50),
        paypal_order_id VARCHAR(255),
        paypal_payer_id VARCHAR(255),
        status VARCHAR(50),
        billing_cycle VARCHAR(20),
        starts_at TIMESTAMP NULL,
        ends_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "✅ Created payments table\n";
} catch (Exception $e) {
    echo "❌ Error creating payments table: " . $e->getMessage() . "\n";
}
