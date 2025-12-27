<?php
// scripts/test_location_api.php

// Mock Input
$input = [
    'project_id' => 1, // Demo Project
    'location' => 'Kathmandu Metropolitan City, Kathmandu',
    'district' => 'Kathmandu',
    'muni' => 'Kathmandu Metropolitan City'
];

$host = 'localhost';
$dbname = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Testing Location Resolution...\n";
    
    // Logic from Controller
    $locationId = null;
    $dist = $db->query("SELECT id FROM est_locations WHERE name = '{$input['district']}' AND type = 'DISTRICT'")->fetch(PDO::FETCH_ASSOC);
    
    if ($dist) {
        echo "Found District ID: " . $dist['id'] . "\n";
        $muni = $db->query("SELECT id FROM est_locations WHERE name = '{$input['muni']}' AND type = 'LOCAL_BODY' AND parent_id = " . $dist['id'])->fetch(PDO::FETCH_ASSOC);
        if ($muni) {
            echo "Found Muni ID: " . $muni['id'] . "\n";
            $locationId = $muni['id'];
        } else {
            echo "Muni NOT FOUND.\n";
        }
    } else {
        echo "District NOT FOUND.\n";
    }

    if ($locationId) {
        $db->exec("UPDATE est_projects SET location_id = $locationId, location = '{$input['location']}' WHERE id = {$input['project_id']}");
        echo "Project updated successfully.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
