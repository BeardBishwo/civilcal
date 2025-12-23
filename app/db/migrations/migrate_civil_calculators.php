<?php
/**
 * Migrate Civil Calculators from Config to Database
 * 
 * This script reads calculator definitions from civil.php config file
 * and inserts them into the database tables for admin management.
 */

require_once __DIR__ . '/../bootstrap.php';

// Load database connection
global $pdo;

// Load civil calculator configurations
$civilConfig = require __DIR__ . '/../Config/Calculators/civil.php';

echo "=== Migrating Civil Calculators to Database ===\n\n";

$successCount = 0;
$errorCount = 0;

foreach ($civilConfig as $calculatorId => $config) {
    try {
        // Check if calculator already exists
        $stmt = $pdo->prepare("SELECT id FROM calculators WHERE calculator_id = ?");
        $stmt->execute([$calculatorId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            echo "⚠️  Skipping {$calculatorId} - already exists\n";
            continue;
        }
        
        // Insert main calculator record
        $stmt = $pdo->prepare("
            INSERT INTO calculators (
                calculator_id, name, description, category, subcategory,
                version, is_active, config_json, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $calculatorId,
            $config['name'],
            $config['description'],
            $config['category'],
            $config['subcategory'] ?? null,
            $config['version'] ?? '1.0',
            1, // is_active
            json_encode($config)
        ]);
        
        $calcDbId = $pdo->lastInsertId();
        
        // Insert inputs
        if (isset($config['inputs'])) {
            $inputStmt = $pdo->prepare("
                INSERT INTO calculator_inputs (
                    calculator_id, field_name, field_label, field_type, unit, unit_type,
                    is_required, min_value, max_value, default_value, placeholder,
                    help_text, validation_pattern, options_json, order_index
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($config['inputs'] as $index => $input) {
                $inputStmt->execute([
                    $calcDbId,
                    $input['name'],
                    $input['label'] ?? ucfirst($input['name']),
                    $input['type'] ?? 'number',
                    $input['unit'] ?? null,
                    $input['unit_type'] ?? null,
                    $input['required'] ?? 1,
                    $input['min'] ?? null,
                    $input['max'] ?? null,
                    $input['default'] ?? null,
                    $input['placeholder'] ?? null,
                    $input['help_text'] ?? null,
                    $input['pattern'] ?? null,
                    isset($input['options']) ? json_encode($input['options']) : null,
                    $index
                ]);
            }
        }
        
        // Insert outputs
        if (isset($config['outputs'])) {
            $outputStmt = $pdo->prepare("
                INSERT INTO calculator_outputs (
                    calculator_id, output_name, output_label, unit, output_type, 
                    precision, is_visible, order_index
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($config['outputs'] as $index => $output) {
                $outputStmt->execute([
                    $calcDbId,
                    $output['name'],
                    $output['label'] ?? ucfirst($output['name']),
                    $output['unit'] ?? null,
                    $output['type'] ?? 'number',
                    $output['precision'] ?? 2,
                    $output['visible'] ?? 1,
                    $index
                ]);
            }
        }
        
        // Insert formulas
        if (isset($config['formulas'])) {
            $formulaStmt = $pdo->prepare("
                INSERT INTO calculator_formulas (
                    calculator_id, result_name, formula, formula_type, order_index
                ) VALUES (?, ?, ?, ?, ?)
            ");
            
            $index = 0;
            foreach ($config['formulas'] as $resultName => $formula) {
                $formulaString = is_callable($formula) ? 'custom_function' : (string)$formula;
                $formulaType = is_callable($formula) ? 'function' : 'expression';
                
                $formulaStmt->execute([
                    $calcDbId,
                    $resultName,
                    $formulaString,
                    $formulaType,
                    $index++
                ]);
            }
        }
        
        echo "✅ Migrated: {$config['name']} ({$calculatorId})\n";
        $successCount++;
        
    } catch (\Exception $e) {
        echo "❌ Error migrating {$calculatorId}: " . $e->getMessage() . "\n";
        $errorCount++;
    }
}

echo "\n=== Migration Complete ===\n";
echo "Successful: {$successCount}\n";
echo "Errors: {$errorCount}\n";
echo "Total: " . count($civilConfig) . " calculators\n";
