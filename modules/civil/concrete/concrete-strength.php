<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap.php';

require_once __DIR__ . '/../../../themes/default/views/shared/calculator-template.php';

$page_title = 'Concrete Strength Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil', 'url' => app_base_url('civil')],
    ['name' => 'Concrete Strength', 'url' => '#']
];

renderCalculator(
    'concrete-strength',
    'Concrete Strength Calculator',
    'Calculate compressive strength of concrete',
    $breadcrumb
);
