<?php
// scripts/seed_boq_data.php

$host = 'localhost';
$dbname = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $projectId = 1;
    $data = [
        'mb' => [
            ["1", "Wall Construction (North)", 1, 10, 0.23, 3, 6.9, "Ground Floor", "C-01"],
            ["2", "Wall Construction (South)", 1, 10, 0.23, 3, 6.9, "Ground Floor", "C-01"]
        ],
        'abstract' => [
            ["C-01", "Brickwork in 1:6 cement sand mortar", "m3", 13.8, 1234.56, 17036.928]
        ],
        'rate' => [
            ["Cement", 1.5, 800],
            ["Sand", 3, 3000]
        ]
    ];
    
    $json = json_encode($data);

    // Upsert
    $stmt = $db->prepare("SELECT id FROM est_boq_data WHERE project_id = ?");
    $stmt->execute([$projectId]);
    if ($stmt->fetch()) {
        $db->prepare("UPDATE est_boq_data SET grid_data = ? WHERE project_id = ?")->execute([$json, $projectId]);
    } else {
        $db->prepare("INSERT INTO est_boq_data (project_id, grid_data) VALUES (?, ?)")->execute([$projectId, $json]);
    }
    
    echo "BOQ Data Seeded for Project 1.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
