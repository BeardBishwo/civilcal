<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Slab Design Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Slab Design', 'url' => '#']
];

renderCalculator(
    'slab-design',
    'Slab Design Calculator',
    'Calculate slab thickness and reinforcement',
    $breadcrumb
);
