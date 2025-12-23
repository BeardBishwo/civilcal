<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Brick Quantity Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Brick Quantity', 'url' => '#']
];

renderCalculator(
    'brick-quantity',
    'Brick Quantity Calculator',
    'Calculate number of bricks required for wall construction',
    $breadcrumb
);
