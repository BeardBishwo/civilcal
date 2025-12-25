<?php
/**
 * Test PHP Extension Toggle
 * Verifies that the PHP extension setting is respected globally
 */

require_once 'app/bootstrap.php';

use App\Helpers\UrlHelper;
use App\Core\Database;

echo "=== PHP Extension Toggle Test ===\n\n";

$db = Database::getInstance()->getPdo();
$sampleId = 'concrete-volume';

echo "Testing PHP Extension Toggle Functionality\n";
echo str_repeat("=", 80) . "\n\n";

// Test with PHP extension DISABLED (clean URLs)
echo "TEST 1: PHP Extension DISABLED (Clean URLs)\n";
echo str_repeat("-", 80) . "\n";

$stmt = $db->prepare("UPDATE settings SET setting_value = '0' WHERE setting_key = 'permalink_php_extension'");
$stmt->execute();

$structures = ['full-path', 'category-calculator', 'subcategory-calculator', 'calculator-only'];

foreach ($structures as $structure) {
    $stmt = $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'permalink_structure'");
    $stmt->execute([$structure]);
    
    UrlHelper::clearCache();
    $url = UrlHelper::calculator($sampleId);
    
    echo str_pad($structure, 25) . " => ";
    
    if (strpos($url, '.php') !== false) {
        echo "❌ FAILED: Contains .php (should be clean)\n";
    } else {
        echo "✅ PASSED: Clean URL\n";
    }
    echo "   URL: $url\n";
}

echo "\n";

// Test with PHP extension ENABLED
echo "TEST 2: PHP Extension ENABLED (.php URLs)\n";
echo str_repeat("-", 80) . "\n";

$stmt = $db->prepare("UPDATE settings SET setting_value = '1' WHERE setting_key = 'permalink_php_extension'");
$stmt->execute();

foreach ($structures as $structure) {
    $stmt = $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'permalink_structure'");
    $stmt->execute([$structure]);
    
    UrlHelper::clearCache();
    $url = UrlHelper::calculator($sampleId);
    
    echo str_pad($structure, 25) . " => ";
    
    if (strpos($url, '.php') !== false) {
        echo "✅ PASSED: Contains .php\n";
    } else {
        echo "❌ FAILED: Missing .php (should have extension)\n";
    }
    echo "   URL: $url\n";
}

echo "\n";

// Test custom pattern with both settings
echo "TEST 3: Custom Pattern with PHP Extension Toggle\n";
echo str_repeat("-", 80) . "\n";

$stmt = $db->prepare("UPDATE settings SET setting_value = 'custom' WHERE setting_key = 'permalink_structure'");
$stmt->execute();

$stmt = $db->prepare("UPDATE settings SET setting_value = '{category}/{slug}' WHERE setting_key = 'permalink_custom_pattern'");
$stmt->execute();

// Test with extension disabled
$stmt = $db->prepare("UPDATE settings SET setting_value = '0' WHERE setting_key = 'permalink_php_extension'");
$stmt->execute();
UrlHelper::clearCache();
$urlClean = UrlHelper::calculator($sampleId);

// Test with extension enabled
$stmt = $db->prepare("UPDATE settings SET setting_value = '1' WHERE setting_key = 'permalink_php_extension'");
$stmt->execute();
UrlHelper::clearCache();
$urlWithPhp = UrlHelper::calculator($sampleId);

echo "Custom Pattern: {category}/{slug}\n";
echo "  Without .php: $urlClean ";
if (strpos($urlClean, '.php') === false) {
    echo "✅\n";
} else {
    echo "❌\n";
}

echo "  With .php:    $urlWithPhp ";
if (strpos($urlWithPhp, '.php') !== false) {
    echo "✅\n";
} else {
    echo "❌\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "✅ All tests completed!\n\n";

echo "Next Steps:\n";
echo "1. Visit: http://localhost/Bishwo_Calculator/admin/settings/permalinks\n";
echo "2. Toggle the 'Show .php Extension' checkbox\n";
echo "3. Watch the live preview update in real-time\n";
echo "4. Save settings and verify URLs throughout the site respect the setting\n";
