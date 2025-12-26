<?php
$site = [
    'earthwork/excavation-cost', 'earthwork/trench-volume', 'earthwork/cut-fill', 'earthwork/soil-compaction', 'earthwork/topsoil-removal',
    'surveying/slope-gradient', 'surveying/coordinates-distance', 'surveying/leveling-reduction', 'surveying/curve-setting', 'surveying/area-coordinates',
    'safety/scaffold-load', 'safety/crane-stability', 'safety/excavation-safety',
    'equipment/equipment-production', 'equipment/owning-operating-cost', 'equipment/fleet-sizing',
    'materials/bricks-calculation', 'materials/cement-mortar', 'materials/concrete-mix', 'materials/asphalt-calculator', 'materials/tile-calculator'
];

$count = 0;
foreach ($site as $calc) {
    $parts = explode('/', $calc);
    $slug = $parts[1];
    $dir = __DIR__ . "/modules/site/{$parts[0]}";
    
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $file = "{$dir}/{$slug}.php";
    file_put_contents($file, "<?php\nrequire_once dirname(__DIR__, 3) . '/app/bootstrap.php';\nrequire_once dirname(__DIR__, 3) . '/themes/default/views/shared/calculator-template.php';\nrenderCalculator('{$slug}');\n");
    $count++;
}
echo "Created/Updated {$count} Site frontend files.\n";
