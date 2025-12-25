<?php
/**
 * Electrical Calculators Migration
 * Import electrical configurations into database
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
$electricalConfig = require __DIR__ . '/../../Config/Calculators/electrical.php';

echo "=== Migrating " . count($electricalConfig) . " Electrical Calculators ===\n\n";

$migrated = 0;
$skipped = 0;
$errors = 0;

foreach ($electricalConfig as $calculatorId => $config) {
    try {
        // Check existence
        $stmt = $pdo->prepare("SELECT id FROM calculators WHERE calculator_id = ?");
        $stmt->execute([$calculatorId]);
        
        if ($stmt->fetch()) {
            echo "⚠️  {$config['name']} - already exists\n";
            $skipped++;
            
            // Optional: Update content if needed (uncomment to force update)
            /*
            $update = $pdo->prepare("UPDATE calculators SET config_json = ?, name = ?, description = ? WHERE calculator_id = ?");
            $update->execute([json_encode($config), $config['name'], $config['description'], $calculatorId]);
            echo "   Updated existing record.\n";
            */
            
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
        
        // Also populate inputs/outputs/formulas tables if we wanted full DB editing capabilities
        // For now, we store the full JSON in `config_json` which serves as the source of truth
        // and we can parse it for the Inputs/Outputs forms in the admin panel edit view.
        // But to populate the `inputs` and `outputs` columns (TEXT/JSON) for searching:
        
        $updateJsonCols = $pdo->prepare("
            UPDATE calculators 
            SET inputs = ?, outputs = ?, formula = ? 
            WHERE calculator_id = ?
        ");
        
        $inputsJson = isset($config['inputs']) ? json_encode($config['inputs']) : null;
        $outputsJson = isset($config['outputs']) ? json_encode($config['outputs']) : null;
        
        // Formula is tricky as it can be closures. We only store text formulas here.
        // Closures are serialized in config_json but cannot be stored as plain text easily.
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
