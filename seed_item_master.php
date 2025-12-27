<?php
require_once __DIR__ . '/app/bootstrap.php';
$norms = require_once __DIR__ . '/app/Config/norms.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

$items = [
    // Civil / Building
    ['code' => 'C-01', 'name' => 'PCC 1:2:4 (m³)', 'unit' => 'm3', 'category' => 'Building'],
    ['code' => 'C-02', 'name' => 'RCC 1:1.5:3 (m³)', 'unit' => 'm3', 'category' => 'Building'],
    ['code' => 'B-01', 'name' => 'Brickwork 1:4 (m³)', 'unit' => 'm3', 'category' => 'Building'],
    ['code' => 'B-02', 'name' => 'Brickwork 1:6 (m³)', 'unit' => 'm3', 'category' => 'Building'],
    ['code' => 'P-01', 'name' => 'Plaster 1:4 (12.5mm) (m²)', 'unit' => 'm2', 'category' => 'Building'],
    
    // Earthwork
    ['code' => 'E-01', 'name' => 'Earthwork in Normal Soil (m³)', 'unit' => 'm3', 'category' => 'Earthwork'],
    ['code' => 'E-02', 'name' => 'Earthwork in Hard Soil/Murrum (m³)', 'unit' => 'm3', 'category' => 'Earthwork'],
    
    // Road & Bridge
    ['code' => 'G-01', 'name' => 'Gabion Box (2x1x1m)', 'unit' => 'nos', 'category' => 'Road & Bridge'],
    ['code' => 'G-02', 'name' => 'Gabion Stone Filling (m³)', 'unit' => 'm3', 'category' => 'Road & Bridge'],
];

foreach ($items as $item) {
    try {
        $stmt = $pdo->prepare("INSERT INTO est_item_master (dudbc_code, item_name, unit, category) VALUES (?, ?, ?, ?)");
        $stmt->execute([$item['code'], $item['name'], $item['unit'], $item['category']]);
        echo "Inserted: " . $item['name'] . "\n";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Seeding complete.\n";
