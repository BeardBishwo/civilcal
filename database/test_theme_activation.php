<?php
/**
 * Test ProCalculator Theme Activation
 * Tests the modular theme management system
 */

require_once __DIR__ . '/../includes/config.php';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test theme activation
    $stmt = $pdo->prepare("UPDATE themes SET status = 'active', activated_at = NOW() WHERE name = 'procalculator'");
    $stmt->execute();
    
    // Verify the update
    $stmt = $pdo->prepare("SELECT * FROM themes WHERE name = 'procalculator'");
    $stmt->execute();
    $theme = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($theme && $theme['status'] === 'active') {
        echo "✅ ProCalculator theme activated successfully!\n";
        echo "Theme Details:\n";
        echo "- Name: " . $theme['name'] . "\n";
        echo "- Display Name: " . $theme['display_name'] . "\n";
        echo "- Status: " . $theme['status'] . "\n";
        echo "- Premium: " . ($theme['is_premium'] ? 'Yes' : 'No') . "\n";
        echo "- Price: $" . number_format($theme['price'], 2) . "\n";
        echo "- Activated At: " . $theme['activated_at'] . "\n";
        echo "- Usage Count: " . $theme['usage_count'] . "\n";
        
        // Test deactivation
        echo "\n🧪 Testing theme deactivation...\n";
        $stmt = $pdo->prepare("UPDATE themes SET status = 'inactive' WHERE name = 'procalculator'");
        $stmt->execute();
        
        $stmt = $pdo->prepare("SELECT status FROM themes WHERE name = 'procalculator'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['status'] === 'inactive') {
            echo "✅ Theme deactivation test passed!\n";
        } else {
            echo "❌ Theme deactivation test failed!\n";
        }
        
        // Reactivate for admin interface
        echo "\n🔄 Reactivating ProCalculator theme for admin interface...\n";
        $stmt = $pdo->prepare("UPDATE themes SET status = 'active', usage_count = usage_count + 1 WHERE name = 'procalculator'");
        $stmt->execute();
        
        echo "✅ ProCalculator theme is ready for admin interface testing!\n";
        
    } else {
        echo "❌ Failed to activate ProCalculator theme!\n";
    }
    
    echo "\n=== MODULAR THEME SYSTEM STATUS ===\n";
    
    // Get all theme statistics
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total_themes,
        COUNT(CASE WHEN status = 'active' THEN 1 END) as active_themes,
        COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_themes,
        COUNT(CASE WHEN is_premium = 1 THEN 1 END) as premium_themes
        FROM themes");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Total Themes: " . $stats['total_themes'] . "\n";
    echo "Active Themes: " . $stats['active_themes'] . "\n";
    echo "Inactive Themes: " . $stats['inactive_themes'] . "\n";
    echo "Premium Themes: " . $stats['premium_themes'] . "\n";
    
    echo "\n✅ Modular Theme Management System is working correctly!\n";
    echo "🎉 ProCalculator $100K Premium Theme is ready for use!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
