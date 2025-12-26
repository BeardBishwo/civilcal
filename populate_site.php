<?php
require 'app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();

$site = [
    'excavation-cost' => 'earthwork', 'trench-volume' => 'earthwork', 'cut-fill' => 'earthwork', 'soil-compaction' => 'earthwork', 'topsoil-removal' => 'earthwork',
    'slope-gradient' => 'surveying', 'coordinates-distance' => 'surveying', 'leveling-reduction' => 'surveying', 'curve-setting' => 'surveying', 'area-coordinates' => 'surveying',
    'scaffold-load' => 'safety', 'crane-stability' => 'safety', 'excavation-safety' => 'safety',
    'equipment-production' => 'equipment', 'owning-operating-cost' => 'equipment', 'fleet-sizing' => 'equipment',
    'bricks-calculation' => 'materials', 'cement-mortar' => 'materials', 'concrete-mix' => 'materials', 'asphalt-calculator' => 'materials', 'tile-calculator' => 'materials'
];

$count = 0;
foreach ($site as $slug => $sub) {
    $path = "site/{$sub}/{$slug}.php";
    $stmt = $db->prepare("INSERT INTO calculator_urls (calculator_id, category, subcategory, slug, full_path, created_at, updated_at) VALUES (?, 'site', ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE subcategory=VALUES(subcategory), full_path=VALUES(full_path), updated_at=NOW()");
    $stmt->execute([$slug, $sub, $slug, $path]);
    $count++;
}
echo "✅ Populated {$count} Site calculators to database.\n";
