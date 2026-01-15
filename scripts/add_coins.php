<?php
// Add coins to user for testing
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bishwo_calculator', 'root', '');

echo "Adding 10,000 coins to user 1...\n";

// Check if user_resources exists
$stmt = $pdo->query("SELECT * FROM user_resources WHERE user_id = 1");
$exists = $stmt->fetch();

if ($exists) {
    $pdo->query("UPDATE user_resources SET coins = 10000 WHERE user_id = 1");
    echo "✓ Updated existing record\n";
} else {
    $pdo->query("INSERT INTO user_resources (user_id, coins, bricks, cement, steel) VALUES (1, 10000, 0, 0, 0)");
    echo "✓ Created new record\n";
}

// Verify
$stmt = $pdo->query("SELECT coins FROM user_resources WHERE user_id = 1");
$result = $stmt->fetch();
echo "User 1 now has " . $result['coins'] . " coins\n";
