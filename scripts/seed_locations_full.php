<?php
// scripts/seed_locations_full.php

$host = 'localhost';
$dbname = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Clearing existing location data (optional, creating fresh structure)...\n";
    // $db->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE est_locations; SET FOREIGN_KEY_CHECKS = 1;");

    // 1. Defined Provinces & Districts Mapping
    $nepalData = [
        'Koshi' => [
            'Bhojpur', 'Dhankuta', 'Ilam', 'Jhapa', 'Khotang', 'Morang', 'Okhaldhunga', 'Panchthar', 'Sankhuwasabha', 'Solukhumbu', 'Sunsari', 'Taplejung', 'Terhathum', 'Udayapur'
        ],
        'Madhesh' => [
            'Bara', 'Dhanusha', 'Mahottari', 'Parsa', 'Rautahat', 'Saptari', 'Sarlahi', 'Siraha'
        ],
        'Bagmati' => [
            'Bhaktapur', 'Chitwan', 'Dhading', 'Dolakha', 'Kathmandu', 'Kavrepalanchok', 'Lalitpur', 'Makwanpur', 'Nuwakot', 'Ramechhap', 'Rasuwa', 'Sindhuli', 'Sindhupalchok'
        ],
        'Gandaki' => [
            'Baglung', 'Gorkha', 'Kaski', 'Lamjung', 'Manang', 'Mustang', 'Myagdi', 'Nawalpur', 'Parbat', 'Syangja', 'Tanahun'
        ],
        'Lumbini' => [
            'Arghakhanchi', 'Banke', 'Bardiya', 'Dang', 'Gulmi', 'Kapilvastu', 'Parasi', 'Palpa', 'Pyuthan', 'Rolpa', 'Rukum East', 'Rupandehi'
        ],
        'Karnali' => [
            'Dailekh', 'Dolpa', 'Humla', 'Jajarkot', 'Jumla', 'Kalikot', 'Mugu', 'Rukum West', 'Salyan', 'Surkhet'
        ],
        'Sudurpaschim' => [
            'Achham', 'Baitadi', 'Bajhang', 'Bajura', 'Dadeldhura', 'Darchula', 'Doti', 'Kailali', 'Kanchanpur'
        ]
    ];

    $stmtProv = $db->prepare("INSERT INTO est_locations (name, type) VALUES (?, 'PROVINCE')");
    $stmtDist = $db->prepare("INSERT INTO est_locations (name, type, parent_id) VALUES (?, 'DISTRICT', ?)");

    foreach ($nepalData as $province => $districts) {
        // Check if Province exists
        $stmtCheck = $db->prepare("SELECT id FROM est_locations WHERE name = ? AND type = 'PROVINCE'");
        $stmtCheck->execute([$province]);
        $provId = $stmtCheck->fetchColumn();

        if (!$provId) {
            $stmtProv->execute([$province]);
            $provId = $db->lastInsertId();
            echo "Created Province: $province\n";
        } else {
            echo "Province Exists: $province\n";
        }

        foreach ($districts as $district) {
            // Check if District exists
            $stmtCheckDist = $db->prepare("SELECT id FROM est_locations WHERE name = ? AND type = 'DISTRICT'");
            $stmtCheckDist->execute([$district]);
            if (!$stmtCheckDist->fetchColumn()) {
                $stmtDist->execute([$district, $provId]);
                echo "  -> Created District: $district\n";
            }
        }
    }
    
    echo "Seeding Complete.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
