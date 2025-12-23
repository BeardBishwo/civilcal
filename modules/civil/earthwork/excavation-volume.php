<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Excavation Volume Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Excavation Volume', 'url' => '#']
];

renderCalculator(
    'excavation-volume',
    'Excavation Volume Calculator',
    'Calculate volume of earth to be excavated',
    $breadcrumb
);
