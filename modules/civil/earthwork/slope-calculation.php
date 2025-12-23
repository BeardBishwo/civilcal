<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Slope Calculation';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Slope Calculation', 'url' => '#']
];

renderCalculator(
    'slope-calculation',
    'Slope Calculation',
    'Calculate slope angle and gradient',
    $breadcrumb
);
