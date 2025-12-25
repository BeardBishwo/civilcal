<?php
/**
 * Plumbing Calculators Migration
 * Import plumbing configurations into database
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=bishwo_calculator', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to database\n\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// Load config
require_once __DIR__ . '/../../bootstrap.php';
$plumbingConfig = require __DIR__ . '/../../Config/Calculators/plumbing.php';

echo "=== Migrating " . count($plumbingConfig) . " Plumbing Calculators ===\n\n";

$migrated = 0;
$skipped = 0;
$errors = 0;

foreach ($plumbingConfig as $calculatorId => $config) {
    try {
        // Check existence
        $stmt = $pdo->prepare("SELECT id FROM calculators WHERE calculator_id = ?");
        $stmt->execute([$calculatorId]);
        
        if ($stmt->fetch()) {
            echo "⚠️  {$config['name']} - already exists\n";
            $skipped++;
            
            // Force update because we are actively developing this
            $update = $pdo->prepare("UPDATE calculators SET config_json = ?, name = ?, description = ?, category = ? WHERE calculator_id = ?");
            $update->execute([json_encode($config), $config['name'], $config['description'], $config['category'], $calculatorId]);
            echo "   Updated existing record.\n";
            
            continue;
        }
        
        // Insert calculator
        $stmt = $pdo->prepare("
            INSERT INTO calculators (
                calculator_id, name, description, category, subcategory,
                version, is_active, config_json, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, 1, ?, NOW())
        ");
        
        $stmt->execute([
            $calculatorId,
            $config['name'],
            $config['description'],
            $config['category'],
            $config['subcategory'] ?? null,
            $config['version'] ?? '1.0',
            json_encode($config)
        ]);
        
        // Populate searchable columns
        $updateJsonCols = $pdo->prepare("
            UPDATE calculators 
            SET inputs = ?, outputs = ?, formula = ? 
            WHERE calculator_id = ?
        ");
        
        $inputsJson = isset($config['inputs']) ? json_encode($config['inputs']) : null;
        $outputsJson = isset($config['outputs']) ? json_encode($config['outputs']) : null;
        $formulaText = isset($config['formulas']) ? 'See config_json for implementation' : null;
        
        $updateJsonCols->execute([$inputsJson, $outputsJson, $formulaText, $calculatorId]);
        
        echo "✅ {$config['name']}\n";
        $migrated++;
        
    } catch (PDOException $e) {
        echo "❌ {$calculatorId}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== Summary ===\n";
echo "✅ Migrated: $migrated\n";
echo "⚠️  Skipped: $skipped\n";
echo "❌ Errors: $errors\n";

// Verify total
$stmt = $pdo->query("SELECT category, COUNT(*) as count FROM calculators GROUP BY category");
echo "\n📊 Statistics:\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  {$row['category']}: {$row['count']}\n";
}
