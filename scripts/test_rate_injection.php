<?php
// scripts/test_rate_injection.php

// Mock Data
$locationId = 363; // Kathmandu Metro (from previous test)
$items = [
    ['dudbc_code' => 'C-01', 'rate' => 1234.56],
    ['dudbc_code' => 'C-02', 'rate' => 999.99]
];

$host = 'localhost';
$dbname = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "1. Simulating Bulk Save for Location $locationId...\n";
    
    foreach ($items as $r) {
        // Check/Insert
        $existing = $db->query("SELECT id FROM est_local_rates WHERE item_code = '{$r['dudbc_code']}' AND location_id = $locationId")->fetch();
        if ($existing) {
            $db->exec("UPDATE est_local_rates SET rate = {$r['rate']} WHERE id = {$existing['id']}");
        } else {
            $db->exec("INSERT INTO est_local_rates (item_code, location_id, rate) VALUES ('{$r['dudbc_code']}', $locationId, {$r['rate']})");
        }
    }
    echo "Rates Saved.\n\n";

    echo "2. Simulating Sheet Load (Get Project Rates)...\n";
    // Project 1 is linked to Location 363
    $projectId = 1;
    $project = $db->query("SELECT location_id FROM est_projects WHERE id = $projectId")->fetch();
    
    if ($project && $project['location_id'] == $locationId) {
        $rates = $db->query("SELECT item_code, rate FROM est_local_rates WHERE location_id = $locationId")->fetchAll(PDO::FETCH_ASSOC);
        echo "Rates for Project $projectId (Location $locationId):\n";
        foreach ($rates as $row) {
            echo " - {$row['item_code']}: {$row['rate']}\n";
        }
        
        // Verify
        $c01 = array_filter($rates, fn($i) => $i['item_code'] == 'C-01');
        if (reset($c01)['rate'] == 1234.56) {
            echo "\nSUCCESS: Dynamic Pricing Verification Passed!\n";
        } else {
             echo "\nFAILURE: Rate mismatch.\n";
        }
    } else {
        echo "Project not linked correctly.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
