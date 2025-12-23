<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Plastering Estimator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Plastering Estimator', 'url' => '#']
];

renderCalculator(
    'plastering-estimator',
    'Plastering Estimator',
    'Calculate materials required for wall plast ering',
    $breadcrumb
);
