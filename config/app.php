<?php
// Application Configuration
return [
    'app' => [
        'name' => 'Bishwo Calculator',
        'env' => 'development',
        'debug' => true,
        'url' => defined('APP_URL') ? APP_URL : (isset($_SERVER['HTTP_HOST']) ? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . (defined('APP_BASE') ? APP_BASE : '') : 'http://localhost'),
        'timezone' => 'Asia/Katmandu',
    ],
    'auth' => [
        'login_url' => '/login',
        'logout_url' => '/logout',
        'dashboard_url' => '/dashboard',
    ],
    'calculators' => [
        'categories' => [
            'civil' => 'Civil Engineering',
            'electrical' => 'Electrical Engineering',
            'plumbing' => 'Plumbing',
            'hvac' => 'HVAC',
            'fire' => 'Fire Protection',
            'structural' => 'Structural Engineering',
            'estimation' => 'Estimation',
            'mep' => 'MEP',
            'project-management' => 'Project Management',
            'site' => 'Site Engineering'
        ]
    ]
];
