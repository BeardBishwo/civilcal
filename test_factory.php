<?php
require 'app/bootstrap.php';

use App\Calculators\CalculatorFactory;

echo "Testing CalculatorFactory...\n\n";

// Test if it can find cooling-load
try {
    $calculator = CalculatorFactory::create('cooling-load');
    echo "✅ Successfully created cooling-load calculator!\n";
    echo "Name: " . $calculator->getName() . "\n";
    echo "Category: " . $calculator->getCategory() . "\n";
    echo "Inputs: " . count($calculator->getInputs()) . "\n";
    echo "Outputs: " . count($calculator->getOutputs()) . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// List all available calculators
echo "\n\nChecking available calculators...\n";
$all = CalculatorFactory::getAvailableCalculators();
$hvac = array_filter($all, fn($c) => $c['category'] === 'hvac');
echo "Found " . count($hvac) . " HVAC calculators in CalculatorFactory\n";

foreach ($hvac as $calc) {
    echo "  - {$calc['slug']}\n";
}
