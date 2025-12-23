<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Foundation Design Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Foundation Design', 'url' => '#']
];

renderCalculator(
    'foundation-design',
    'Foundation Design Calculator',
    'Calculate foundation size based on soil bearing capacity',
    $breadcrumb
);
