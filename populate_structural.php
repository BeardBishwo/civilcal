<?php
require 'app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();
$structural = ['simply-supported-beam'=>'beam-analysis','cantilever-beam'=>'beam-analysis','continuous-beam'=>'beam-analysis','beam-design'=>'beam-analysis','beam-load-combination'=>'beam-analysis','short-column'=>'column-design','long-column'=>'column-design','biaxial-column'=>'column-design','steel-column-design'=>'column-design','column-footing-link'=>'column-design','isolated-footing'=>'foundation-design','combined-footing'=>'foundation-design','strap-footing'=>'foundation-design','pile-foundation'=>'foundation-design','mat-foundation'=>'foundation-design','one-way-slab'=>'slab-design','two-way-slab'=>'slab-design','flat-slab'=>'slab-design','waffle-slab'=>'slab-design','cantilever-slab'=>'slab-design','dead-load'=>'load-analysis','live-load'=>'load-analysis','wind-load'=>'load-analysis','seismic-load'=>'load-analysis','load-combination'=>'load-analysis','rebar-spacing'=>'reinforcement','development-length'=>'reinforcement','lap-length'=>'reinforcement','stirrup-spacing'=>'reinforcement','anchorage-length'=>'reinforcement','steel-beam'=>'steel-structure','steel-truss'=>'steel-structure','connection-design'=>'steel-structure','plate-girder'=>'steel-structure','composite-beam'=>'steel-structure','quantity-takeoff'=>'reports','cost-estimate'=>'reports','material-summary'=>'reports','bar-bending-schedule'=>'reports','structural-report'=>'reports'];
$count = 0;
foreach ($structural as $id => $sub) {
    $stmt = $db->prepare("INSERT INTO calculator_urls (calculator_id, category, subcategory, slug, full_path, created_at, updated_at) VALUES (?, 'structural', ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE subcategory=VALUES(subcategory), full_path=VALUES(full_path), updated_at=NOW()");
    $stmt->execute([$id, $sub, $id, "structural/{$sub}/{$id}.php"]);
    $count++;
}
echo "✅ Populated {$count}/40 structural calculators to database.\n";
