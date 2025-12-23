#!/usr/bin/env php
<?php
/**
 * Test all 29 migrated electrical calculators
 * This script sends test requests to each calculator and verifies they respond correctly
 */

$baseUrl = 'http://localhost/Bishwo_Calculator/modules/electrical';
$results = [];
$passed = 0;
$failed = 0;

// Test data for each calculator
$tests = [
    // Voltage Drop
    ['url' => "$baseUrl/voltage-drop/single-phase-voltage-drop.php", 'data' => 'current=20&distance=100&wire_size=12&voltage=120&material=copper&power_factor=100', 'name' => 'Single Phase Voltage Drop'],
    ['url' => "$baseUrl/voltage-drop/three-phase-voltage-drop.php", 'data' => 'current=20&distance=100&wire_size=12&voltage=480&material=copper&power_factor=85', 'name' => 'Three Phase Voltage Drop'],
    ['url' => "$baseUrl/voltage-drop/voltage-drop-sizing.php", 'data' => 'current=20&distance=100&voltage=120&max_drop_percent=3&phase=1&material=copper', 'name' => 'Voltage Drop Sizing'],
    ['url' => "$baseUrl/voltage-drop/voltage-regulation.php", 'data' => 'source_voltage=120&load_voltage=115&voltage_type=line-to-line&load_type=resistive', 'name' => 'Voltage Regulation'],
    ['url' => "$baseUrl/voltage-drop/voltage_drop.php", 'data' => 'current=10&length=50&resistance=0.001', 'name' => 'Generic Voltage Drop'],
    
    // Wire Sizing
    ['url' => "$baseUrl/wire-sizing/wire-ampacity.php", 'data' => 'wire_size=12&insulation_type=THHN&wire_material=copper&conductor_count=3&ambient_temp=30', 'name' => 'Wire Ampacity'],
    ['url' => "$baseUrl/wire-sizing/wire-size-by-current.php", 'data' => 'load_current=30&wire_material=copper&insulation_type=THHN&ambient_temp=30&conductor_count=3', 'name' => 'Wire Size by Current'],
    ['url' => "$baseUrl/wire-sizing/motor-circuit-wire-sizing.php", 'data' => 'horsepower=10&voltage=460&phase=3', 'name' => 'Motor Circuit Wire Sizing'],
    ['url' => "$baseUrl/wire-sizing/motor-circuit-wiring.php", 'data' => 'horsepower=10&voltage=460&service_factor=1.15', 'name' => 'Motor Circuit Wiring'],
    ['url' => "$baseUrl/wire-sizing/transformer-kva-sizing.php", 'data' => 'load_kw=50&power_factor=85&safety_factor=125', 'name' => 'Transformer KVA Sizing'],
    
    // Load Calculation
    ['url' => "$baseUrl/load-calculation/ohms-law.php", 'data' => 'voltage=120&current=10&resistance=', 'name' => "Ohm's Law"],
    ['url' => "$baseUrl/load-calculation/power_factor.php", 'data' => 'real_power=50&reactive_power=30&apparent_power=&power_factor_input=', 'name' => 'Power Factor'],
    ['url' => "$baseUrl/load-calculation/voltage_divider.php", 'data' => 'input_voltage=12&r1=1000&r2=2000', 'name' => 'Voltage Divider'],
    ['url' => "$baseUrl/load-calculation/demand-load-calculation.php", 'data' => 'connected_load=100&demand_factor=70', 'name' => 'Demand Load'],
    ['url' => "$baseUrl/load-calculation/arc-flash-boundary.php", 'data' => 'fault_current=10&clearing_time=0.1&working_distance=18', 'name' => 'Arc Flash Boundary'],
    ['url' => "$baseUrl/load-calculation/battery-load-bank-sizing.php", 'data' => 'load_watts=1000&backup_hours=4&battery_voltage=12&depth_of_discharge=50', 'name' => 'Battery Load Bank Sizing'],
    ['url' => "$baseUrl/load-calculation/feeder-sizing.php", 'data' => 'continuous_load=50&non_continuous_load=20', 'name' => 'Feeder Sizing'],
    ['url' => "$baseUrl/load-calculation/general-lighting-load.php", 'data' => 'area=5000&occupancy_type=office', 'name' => 'General Lighting Load'],
    ['url' => "$baseUrl/load-calculation/motor-full-load-amps.php", 'data' => 'horsepower=10&voltage=460&phase=3', 'name' => 'Motor Full Load Amps'],
    ['url' => "$baseUrl/load-calculation/ocpd-sizing.php", 'data' => 'load_current=50&continuous=continuous', 'name' => 'OCPD Sizing'],
    ['url' => "$baseUrl/load-calculation/panel-schedule.php", 'data' => 'total_breakers=20&avg_load_per_breaker=10&demand_factor=75', 'name' => 'Panel Schedule'],
    ['url' => "$baseUrl/load-calculation/receptacle-load.php", 'data' => 'receptacle_count=20&receptacle_type=general', 'name' => 'Receptacle Load'],
    
    // Conduit Sizing
    ['url' => "$baseUrl/conduit-sizing/cable-tray-sizing.php", 'data' => 'cable_diameter=0.5&cable_count=10&fill_percent=50', 'name' => 'Cable Tray Sizing'],
    ['url' => "$baseUrl/conduit-sizing/conduit-fill-calculation.php", 'data' => 'wire_size=12&wire_count=4&conduit_size=0.75', 'name' => 'Conduit Fill Calculation'],
    ['url' => "$baseUrl/conduit-sizing/entrance-service-sizing.php", 'data' => 'total_load=50&voltage=240', 'name' => 'Entrance Service Sizing'],
    ['url' => "$baseUrl/conduit-sizing/junction-box-sizing.php", 'data' => 'largest_wire=12&wire_count=6', 'name' => 'Junction Box Sizing'],
    
    // Short Circuit
    ['url' => "$baseUrl/short-circuit/available-fault-current.php", 'data' => 'transformer_kva=500&impedance=5.75&voltage=480', 'name' => 'Available Fault Current'],
    ['url' => "$baseUrl/short-circuit/ground-conductor-sizing.php", 'data' => 'ocpd_rating=100', 'name' => 'Ground Conductor Sizing'],
    ['url' => "$baseUrl/short-circuit/power-factor-correction.php", 'data' => 'load_kw=100&existing_pf=75&target_pf=95', 'name' => 'Power Factor Correction'],
];

echo "Testing " . count($tests) . " Electrical Calculators...\n";
echo str_repeat("=", 80) . "\n\n";

foreach ($tests as $test) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test['url']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $test['data']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $status = '✗ FAIL';
    $message = '';
    
    if ($error) {
        $message = "cURL Error: $error";
        $failed++;
    } elseif ($httpCode !== 200) {
        $message = "HTTP $httpCode";
        $failed++;
    } elseif (stripos($response, 'Exception') !== false || stripos($response, 'Fatal error') !== false) {
        $message = "PHP Error detected";
        $failed++;
    } elseif (stripos($response, 'Results') !== false || stripos($response, 'result-area') !== false) {
        $status = '✓ PASS';
        $message = "Calculator loaded and executed";
        $passed++;
    } else {
        $message = "Response received but no results found";
        $failed++;
    }
    
    $results[] = [
        'name' => $test['name'],
        'status' => $status,
        'message' => $message
    ];
    
    printf("%-40s %s %s\n", $test['name'], $status, $message);
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "SUMMARY: $passed passed, $failed failed out of " . count($tests) . " tests\n";
echo str_repeat("=", 80) . "\n";

exit($failed > 0 ? 1 : 0);
