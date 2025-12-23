<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Cut and Fill Volume Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Cut & Fill', 'url' => '#']
];

renderCalculator(
    'cut-and-fill-volume',
    'Cut and Fill Volume Calculator',
    'Calculate cut and fill volumes for site grading',
    $breadcrumb
);
