<?php
// Application Configuration
return [
    'app' => [
        'name' => 'Bishwo Calculator',
        'env' => getenv('APP_ENV') ?? 'production',
        'debug' => filter_var(getenv('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOLEAN),
        'url' => 'http://localhost/Bishwo_Calculator',
        'timezone' => 'Asia/Kathmandu',
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
