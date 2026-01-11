<?php
// Application Configuration
return [
    'app' => [
        'name' => 'Bishwo Calculator',
        'env' => 'production',
        'debug' => false,
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
