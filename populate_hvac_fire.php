<?php
/**
 * Populate calculator_urls for HVAC and Fire modules
 */
require_once 'app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance()->getPdo();

// HVAC Calculators
$hvacCalculators = [
    // Load Calculation
    ['id' => 'cooling-load', 'category' => 'hvac', 'subcategory' => 'load-calculation', 'path' => 'hvac/load-calculation/cooling-load.php'],
    ['id' => 'heating-load', 'category' => 'hvac', 'subcategory' => 'load-calculation', 'path' => 'hvac/load-calculation/heating-load.php'],
    ['id' => 'infiltration', 'category' => 'hvac', 'subcategory' => 'load-calculation', 'path' => 'hvac/load-calculation/infiltration.php'],
    ['id' => 'ventilation', 'category' => 'hvac', 'subcategory' => 'load-calculation', 'path' => 'hvac/load-calculation/ventilation.php'],
    
    // Equipment Sizing
    ['id' => 'ac-sizing', 'category' => 'hvac', 'subcategory' => 'equipment-sizing', 'path' => 'hvac/equipment-sizing/ac-sizing.php'],
    ['id' => 'chiller-sizing', 'category' => 'hvac', 'subcategory' => 'equipment-sizing', 'path' => 'hvac/equipment-sizing/chiller-sizing.php'],
    ['id' => 'furnace-sizing', 'category' => 'hvac', 'subcategory' => 'equipment-sizing', 'path' => 'hvac/equipment-sizing/furnace-sizing.php'],
    ['id' => 'pump-sizing', 'category' => 'hvac', 'subcategory' => 'equipment-sizing', 'path' => 'hvac/equipment-sizing/pump-sizing.php'],
    
    // Duct Sizing
    ['id' => 'velocity-sizing', 'category' => 'hvac', 'subcategory' => 'duct-sizing', 'path' => 'hvac/duct-sizing/velocity-sizing.php'],
    ['id' => 'pressure-drop', 'category' => 'hvac', 'subcategory' => 'duct-sizing', 'path' => 'hvac/duct-sizing/pressure-drop.php'],
    ['id' => 'equivalent-duct', 'category' => 'hvac', 'subcategory' => 'duct-sizing', 'path' => 'hvac/duct-sizing/equivalent-duct.php'],
    ['id' => 'fitting-loss', 'category' => 'hvac', 'subcategory' => 'duct-sizing', 'path' => 'hvac/duct-sizing/fitting-loss.php'],
    ['id' => 'grille-sizing', 'category' => 'hvac', 'subcategory' => 'duct-sizing', 'path' => 'hvac/duct-sizing/grille-sizing.php'],
    
    // Psychrometrics
    ['id' => 'air-properties', 'category' => 'hvac', 'subcategory' => 'psychrometrics', 'path' => 'hvac/psychrometrics/air-properties.php'],
    ['id' => 'enthalpy', 'category' => 'hvac', 'subcategory' => 'psychrometrics', 'path' => 'hvac/psychrometrics/enthalpy.php'],
    ['id' => 'sensible-heat-ratio', 'category' => 'hvac', 'subcategory' => 'psychrometrics', 'path' => 'hvac/psychrometrics/sensible-heat-ratio.php'],
    ['id' => 'cooling-load-psych', 'category' => 'hvac', 'subcategory' => 'psychrometrics', 'path' => 'hvac/psychrometrics/cooling-load-psych.php'],
    
    // Energy Analysis
    ['id' => 'energy-consumption', 'category' => 'hvac', 'subcategory' => 'energy-analysis', 'path' => 'hvac/energy-analysis/energy-consumption.php'],
    ['id' => 'co2-emissions', 'category' => 'hvac', 'subcategory' => 'energy-analysis', 'path' => 'hvac/energy-analysis/co2-emissions.php'],
    ['id' => 'insulation-savings', 'category' => 'hvac', 'subcategory' => 'energy-analysis', 'path' => 'hvac/energy-analysis/insulation-savings.php'],
    ['id' => 'payback-period', 'category' => 'hvac', 'subcategory' => 'energy-analysis', 'path' => 'hvac/energy-analysis/payback-period.php'],
];

// Fire Calculators
$fireCalculators = [
    // Sprinklers
    ['id' => 'discharge-calculations', 'category' => 'fire', 'subcategory' => 'sprinklers', 'path' => 'fire/sprinklers/discharge-calculations.php'],
    ['id' => 'pipe-sizing', 'category' => 'fire', 'subcategory' => 'sprinklers', 'path' => 'fire/sprinklers/pipe-sizing.php'],
    ['id' => 'sprinkler-layout', 'category' => 'fire', 'subcategory' => 'sprinklers', 'path' => 'fire/sprinklers/sprinkler-layout.php'],
    
    // Fire Pumps
    ['id' => 'pump-sizing', 'category' => 'fire', 'subcategory' => 'fire-pumps', 'path' => 'fire/fire-pumps/pump-sizing.php'],
    ['id' => 'driver-power', 'category' => 'fire', 'subcategory' => 'fire-pumps', 'path' => 'fire/fire-pumps/driver-power.php'],
    ['id' => 'jockey-pump', 'category' => 'fire', 'subcategory' => 'fire-pumps', 'path' => 'fire/fire-pumps/jockey-pump.php'],
    
    // Standpipes
    ['id' => 'hose-demand', 'category' => 'fire', 'subcategory' => 'standpipes', 'path' => 'fire/standpipes/hose-demand.php'],
    ['id' => 'pressure-calculations', 'category' => 'fire', 'subcategory' => 'standpipes', 'path' => 'fire/standpipes/pressure-calculations.php'],
    ['id' => 'standpipe-classification', 'category' => 'fire', 'subcategory' => 'standpipes', 'path' => 'fire/standpipes/standpipe-classification.php'],
    
    // Hazard Classification
    ['id' => 'occupancy-assessment', 'category' => 'fire', 'subcategory' => 'hazard-classification', 'path' => 'fire/hazard-classification/occupancy-assessment.php'],
    ['id' => 'design-density', 'category' => 'fire', 'subcategory' => 'hazard-classification', 'path' => 'fire/hazard-classification/design-density.php'],
    ['id' => 'commodity-classification', 'category' => 'fire', 'subcategory' => 'hazard-classification', 'path' => 'fire/hazard-classification/commodity-classification.php'],
    
    // Hydraulics
    ['id' => 'hazen-williams', 'category' => 'fire', 'subcategory' => 'hydraulics', 'path' => 'fire/hydraulics/hazen-williams.php'],
];

$allCalculators = array_merge($hvacCalculators, $fireCalculators);
$count = 0;

foreach ($allCalculators as $calc) {
    $stmt = $db->prepare("
        INSERT INTO calculator_urls (calculator_id, category, subcategory, slug, full_path, created_at, updated_at)
        VALUES (:id, :cat, :sub, :slug, :path, NOW(), NOW())
        ON DUPLICATE KEY UPDATE
        category = VALUES(category),
        subcategory = VALUES(subcategory),
        full_path = VALUES(full_path),
        updated_at = NOW()
    ");
    
    try {
        $stmt->execute([
            ':id' => $calc['id'],
            ':cat' => $calc['category'],
            ':sub' => $calc['subcategory'],
            ':slug' => $calc['id'],
            ':path' => $calc['path']
        ]);
        $count++;
        echo ".";
        if ($count % 50 == 0) echo "\n";
    } catch (PDOException $e) {
        echo "\nError inserting {$calc['id']}: " . $e->getMessage() . "\n";
    }
}

echo "\n\n✅ Done! Inserted/Updated {$count} calculators.\n";
echo "HVAC: 21 calculators\n";
echo "Fire: 13 calculators\n";
echo "Total: 34 calculators\n";
