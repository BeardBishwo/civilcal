<?php
require 'app/bootstrap.php';

use App\Engine\CalculatorEngine;

$engine = new CalculatorEngine();

echo "Testing CalculatorEngine with cooling-load...\n\n";

try {
    $metadata = $engine->getMetadata('cooling-load');
    echo "✅ Success!\n";
    print_r($metadata);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

// Test calculation
echo "\n\nTesting calculation...\n";
try {
    $result = $engine->calculate('cooling-load', [
        'area' => 100,
        'room_type' => 'office',
        'occupants' => 10,
        'equipment_load' => 500,
        'lighting_load' => 300
    ]);
    echo "✅ Calculation Success!\n";
    print_r($result);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
