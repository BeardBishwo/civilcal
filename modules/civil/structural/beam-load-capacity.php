<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Beam Load Capacity Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Beam Load Capacity', 'url' => '#']
];

renderCalculator(
    'beam-load-capacity',
    'Beam Load Capacity Calculator',
    'Calculate maximum load capacity of a beam',
    $breadcrumb
);
