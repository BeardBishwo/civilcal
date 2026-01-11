<?php

/**
 * Mechanical Engineering Calculators Configuration
 * 
 * Restored definitions for:
 * - Force Calculator (F = m * a)
 * - Torque Calculator (T = F * r * sin(theta))
 * - Power Calculator (P = W / t)
 */

return [
    'force_calculator' => [
        'name' => 'Force Calculator',
        'description' => 'Calculate Force using Newton\'s Second Law (F = m × a)',
        'category' => 'mechanical',
        'subcategory' => 'mechanics',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'mass', 'type' => 'number', 'unit' => 'kg', 'required' => true, 'label' => 'Mass (m)', 'min' => 0],
            ['name' => 'acceleration', 'type' => 'number', 'unit' => 'm/s²', 'required' => true, 'label' => 'Acceleration (a)']
        ],
        'formulas' => [
            'force' => 'mass * acceleration'
        ],
        'outputs' => [
            ['name' => 'force', 'unit' => 'N', 'label' => 'Force', 'precision' => 2]
        ]
    ],
    
    'torque_calculator' => [
        'name' => 'Torque Calculator',
        'description' => 'Calculate Torque (T = F × r × sin(θ))',
        'category' => 'mechanical',
        'subcategory' => 'mechanics',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'force', 'type' => 'number', 'unit' => 'N', 'required' => true, 'label' => 'Force (F)'],
            ['name' => 'radius', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Radius (r)', 'min' => 0],
            ['name' => 'angle', 'type' => 'number', 'unit' => 'deg', 'required' => true, 'label' => 'Angle (θ)', 'default' => 90]
        ],
        'formulas' => [
            'torque' => 'force * radius * sin(deg2rad(angle))'
        ],
        'outputs' => [
            ['name' => 'torque', 'unit' => 'N·m', 'label' => 'Torque', 'precision' => 2]
        ]
    ],
    
    'power_calculator' => [
        'name' => 'Mechanical Power Calculator',
        'description' => 'Calculate Power (P = Work / Time)',
        'category' => 'mechanical',
        'subcategory' => 'thermodynamics',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'work', 'type' => 'number', 'unit' => 'J', 'required' => true, 'label' => 'Work (W)'],
            ['name' => 'time', 'type' => 'number', 'unit' => 's', 'required' => true, 'label' => 'Time (t)', 'min' => 0.0001]
        ],
        'formulas' => [
            'power' => 'work / time',
            'horsepower' => '(work / time) / 745.7'
        ],
        'outputs' => [
            ['name' => 'power', 'unit' => 'W', 'label' => 'Power (Watts)', 'precision' => 2],
            ['name' => 'horsepower', 'unit' => 'HP', 'label' => 'Horsepower', 'precision' => 2]
        ]
    ]
];
