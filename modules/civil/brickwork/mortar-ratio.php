<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Mortar Ratio Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Mortar Ratio', 'url' => '#']
];

renderCalculator(
    'mortar-ratio',
    'Mortar Ratio Calculator',
    'Calculate cement and sand quantities for mortar',
    $breadcrumb
);
