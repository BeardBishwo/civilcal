<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Column Design Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Column Design', 'url' => '#']
];

renderCalculator(
    'column-design',
    'Column Design Calculator',
    'Calculate column load-bearing capacity',
    $breadcrumb
);
