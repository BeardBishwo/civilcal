<?php
/**
 * URL Migration Script
 * Populates calculator_urls table with all calculators from the config files
 */

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance()->getPdo();

echo "Starting URL Migration...\n";
echo str_repeat("=", 80) . "\n\n";

// Load all calculator configurations
$calculatorFiles = [
    'civil' => __DIR__ . '/../app/Config/Calculators/civil.php',
    'electrical' => __DIR__ . '/../app/Config/Calculators/electrical.php'
];

$totalCalculators = 0;
$inserted = 0;
$updated = 0;

foreach ($calculatorFiles as $category => $configFile) {
    if (!file_exists($configFile)) {
        echo "⚠ Config file not found: $configFile\n";
        continue;
    }
    
    $calculators = require $configFile;
    
    foreach ($calculators as $calculatorId => $config) {
        $totalCalculators++;
        
        $subcategory = $config['subcategory'] ?? '';
        $slug = $calculatorId; // Use calculator ID as slug
        
        // Determine full path based on category and subcategory
        $fullPath = "modules/{$category}/{$subcategory}/{$calculatorId}.php";
        
        // Check if URL already exists
        $stmt = $db->prepare("SELECT id FROM calculator_urls WHERE calculator_id = ?");
        $stmt->execute([$calculatorId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Update existing record
            $stmt = $db->prepare("
                UPDATE calculator_urls 
                SET category = ?, subcategory = ?, slug = ?, full_path = ?, updated_at = NOW()
                WHERE calculator_id = ?
            ");
            $stmt->execute([$category, $subcategory, $slug, $fullPath, $calculatorId]);
            $updated++;
            echo "✓ Updated: {$calculatorId} → /{$slug}\n";
        } else {
            // Insert new record
            $stmt = $db->prepare("
                INSERT INTO calculator_urls (calculator_id, category, subcategory, slug, full_path)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$calculatorId, $category, $subcategory, $slug, $fullPath]);
            $inserted++;
            echo "✓ Inserted: {$calculatorId} → /{$slug}\n";
        }
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "Migration Complete!\n";
echo "Total Calculators: $totalCalculators\n";
echo "Inserted: $inserted\n";
echo "Updated: $updated\n";
echo str_repeat("=", 80) . "\n";
