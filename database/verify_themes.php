<?php
/**
 * Database Verification Script
 * Checks if ProCalculator theme is registered
 */

require_once __DIR__ . '/../app/Config/config.php';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query themes table
    $stmt = $pdo->query("SELECT name, display_name, status, is_premium, price FROM themes ORDER BY name");
    $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== THEMES IN DATABASE ===\n";
    foreach ($themes as $theme) {
        echo "Name: " . $theme['name'] . "\n";
        echo "Display Name: " . $theme['display_name'] . "\n";
        echo "Status: " . $theme['status'] . "\n";
        echo "Premium: " . ($theme['is_premium'] ? 'Yes' : 'No') . "\n";
        echo "Price: $" . number_format($theme['price'], 2) . "\n";
        echo "---\n";
    }
    
    // Check specifically for ProCalculator
    $stmt = $pdo->prepare("SELECT * FROM themes WHERE name = 'procalculator'");
    $stmt->execute();
    $proCalculator = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($proCalculator) {
        echo "\n✅ ProCalculator theme is registered successfully!\n";
        echo "Theme ID: " . $proCalculator['id'] . "\n";
        echo "Status: " . $proCalculator['status'] . "\n";
        echo "Premium: " . ($proCalculator['is_premium'] ? 'Yes' : 'No') . "\n";
        echo "Price: $" . number_format($proCalculator['price'], 2) . "\n";
    } else {
        echo "\n❌ ProCalculator theme not found!\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
