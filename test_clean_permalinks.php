<?php
/**
 * Test Clean Permalink System
 * Verifies that all 5 permalink structures generate clean URLs without .php extensions
 */

require_once 'app/bootstrap.php';

use App\Helpers\UrlHelper;
use App\Core\Database;

echo "=== Clean Permalink System Test ===\n\n";

// Test sample calculator
$sampleId = 'concrete-volume';

// Get all 5 structures
$structures = ['full-path', 'category-calculator', 'subcategory-calculator', 'calculator-only', 'custom'];

$db = Database::getInstance()->getPdo();

echo "Testing URL Generation for Calculator: $sampleId\n";
echo str_repeat("-", 80) . "\n\n";

foreach ($structures as $structure) {
    // Temporarily set structure in database
    $stmt = $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'permalink_structure'");
    $stmt->execute([$structure]);
    
    // Clear cache to force reload
    UrlHelper::clearCache();
    
    // Generate URL
    $url = UrlHelper::calculator($sampleId);
    
    echo "Structure: " . str_pad($structure, 25) . " => ";
    
    // Check if URL contains .php extension
    if (strpos($url, '.php') !== false) {
        echo "❌ FAILED: URL contains .php extension\n";
        echo "   Generated: $url\n";
    } else {
        echo "✅ PASSED: Clean URL\n";
        echo "   Generated: $url\n";
    }
    
    echo "\n";
}

// Test custom pattern
echo "\nTesting Custom Pattern:\n";
echo str_repeat("-", 80) . "\n";

$customPatterns = [
    '{category}/{slug}' => '/civil/concrete-volume',
    '{subcategory}/{slug}' => '/concrete/concrete-volume',
    'calculators/{category}/{slug}' => '/calculators/civil/concrete-volume',
];

foreach ($customPatterns as $pattern => $expected) {
    // Set custom pattern
    $stmt = $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'permalink_structure'");
    $stmt->execute(['custom']);
    
    $stmt = $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'permalink_custom_pattern'");
    $stmt->execute([$pattern]);
    
    UrlHelper::clearCache();
    
    $url = UrlHelper::calculator($sampleId);
    
    echo "Pattern: " . str_pad($pattern, 30) . " => ";
    
    if (strpos($url, '.php') !== false) {
        echo "❌ FAILED: Contains .php\n";
    } else if (strpos($url, $expected) !== false) {
        echo "✅ PASSED\n";
    } else {
        echo "⚠️  WARNING: Unexpected URL\n";
    }
    
    echo "   Generated: $url\n\n";
}

echo "\n=== Test Complete ===\n";
echo "\nNext Steps:\n";
echo "1. Visit http://localhost/Bishwo_Calculator/admin/settings/permalinks\n";
echo "2. Select each permalink structure and verify the preview\n";
echo "3. Test accessing calculators with clean URLs in browser\n";
echo "4. Verify old .php URLs redirect to clean URLs (301)\n";
