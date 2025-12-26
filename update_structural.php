<?php
$structural = [
    'beam-analysis/simply-supported-beam', 'beam-analysis/cantilever-beam', 'beam-analysis/continuous-beam', 'beam-analysis/beam-design', 'beam-analysis/beam-load-combination',
    'column-design/short-column', 'column-design/long-column', 'column-design/biaxial-column', 'column-design/steel-column-design', 'column-design/column-footing-link',
    'foundation-design/isolated-footing', 'foundation-design/combined-footing', 'foundation-design/strap-footing', 'foundation-design/pile-foundation', 'foundation-design/mat-foundation',
    'slab-design/one-way-slab', 'slab-design/two-way-slab', 'slab-design/flat-slab', 'slab-design/waffle-slab', 'slab-design/cantilever-slab',
    'load-analysis/dead-load', 'load-analysis/live-load', 'load-analysis/wind-load', 'load-analysis/seismic-load', 'load-analysis/load-combination',
    'reinforcement/rebar-spacing', 'reinforcement/development-length', 'reinforcement/lap-length', 'reinforcement/stirrup-spacing', 'reinforcement/anchorage-length',
    'steel-structure/steel-beam', 'steel-structure/steel-truss', 'steel-structure/connection-design', 'steel-structure/plate-girder', 'steel-structure/composite-beam',
    'reports/quantity-takeoff', 'reports/cost-estimate', 'reports/material-summary', 'reports/bar-bending-schedule', 'reports/structural-report'
];
$count = 0;
foreach ($structural as $calc) {
    $slug = basename($calc);
    $file = __DIR__ . "/modules/structural/{$calc}.php";
    if (file_exists($file)) {
        file_put_contents($file, "<?php\nrequire_once dirname(__DIR__, 3) . '/app/bootstrap.php';\nrequire_once dirname(__DIR__, 3) . '/themes/default/views/shared/calculator-template.php';\nrenderCalculator('{$slug}');\n");
        $count++;
    }
}
echo "Updated {$count}/40 structural frontend files.\n";
