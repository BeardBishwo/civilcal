<?php
// scripts/seed_locations_from_local_json.php

$host = 'localhost';
$dbname = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $jsonFile = __DIR__ . '/temp_locations.json';
    if (!file_exists($jsonFile)) {
        die("Error: temp_locations.json not found.\n");
    }

    $json = file_get_contents($jsonFile);
    $data = json_decode($json, true);

    if (!$data) {
        die("Error: Failed to decode JSON.\n");
    }

    echo "Truncating existing location data for clean import...\n";
    $db->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE est_locations; SET FOREIGN_KEY_CHECKS = 1;");

    $stmtInsert = $db->prepare("INSERT INTO est_locations (name, type, parent_id) VALUES (?, ?, ?)");

    // JSON Format: { "Province Name": { "District Name": { "Muni Name": [ ...wards ] } } }
    
    foreach ($data as $provinceName => $districts) {
        // 1. Insert Province
        $stmtInsert->execute([$provinceName, 'PROVINCE', null]);
        $provId = $db->lastInsertId();
        echo "Province: $provinceName\n";

        foreach ($districts as $districtName => $munis) {
            // 2. Insert District
            $stmtInsert->execute([$districtName, 'DISTRICT', $provId]);
            $distId = $db->lastInsertId();
            // echo "  - District: $districtName\n";

            // 3. Insert Local Bodies (Municipalities/VDCs)
            if (is_array($munis)) {
                foreach ($munis as $muniName => $wards) {
                    // Muni name is key
                    $stmtInsert->execute([$muniName, 'LOCAL_BODY', $distId]);
                    // echo "    -- Loc: $muniName\n";
                }
            }
        }
    }
    
    echo "\nSuccess! Full Nepal location hierarchy imported.\n";

} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage();
}
