<?php
require 'app/bootstrap.php';

$db = App\Core\Database::getInstance()->getPdo();

// Check if cooling-load exists in database
$stmt = $db->prepare('SELECT * FROM calculator_urls WHERE calculator_id = ? LIMIT 1');
$stmt->execute(['cooling-load']);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo "✅ Found in database:\n";
    print_r($result);
} else {
    echo "❌ NOT found in database!\n";
    echo "Checking all HVAC entries...\n\n";
    
    $stmt = $db->query("SELECT calculator_id, category, full_path FROM calculator_urls WHERE category = 'hvac' OR category = 'Hvac'");
    $hvac = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($hvac) . " HVAC entries:\n";
    foreach ($hvac as $calc) {
        echo "  - {$calc['calculator_id']} ({$calc['category']}) -> {$calc['full_path']}\n";
    }
}

// Test URL helper
echo "\n\nTesting UrlHelper:\n";
$url = App\Helpers\UrlHelper::getCalculatorUrl('cooling-load');
echo "URL for cooling-load: {$url}\n";
