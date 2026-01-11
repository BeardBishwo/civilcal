<?php

/**
 * General Calculators Configuration
 * 
 * Restored definitions for:
 * - Area Calculator
 * - Volume Calculator
 * - Unit Converter (Placeholder)
 */

return [
    'area_calculator' => [
        'name' => 'Area Calculator',
        'description' => 'Calculate area of various shapes',
        'category' => 'general',
        'subcategory' => 'geometry',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'shape', 'type' => 'string', 'label' => 'Shape', 'options' => ['rectangle', 'circle', 'triangle'], 'default' => 'rectangle'],
            ['name' => 'dim1', 'type' => 'number', 'label' => 'Dimension 1 (L/r/b)', 'min' => 0],
            ['name' => 'dim2', 'type' => 'number', 'label' => 'Dimension 2 (W/-/h)', 'min' => 0, 'required' => false]
        ],
        'formulas' => [
            'area' => function($context) {
                $shape = $context['shape'];
                $d1 = $context['dim1'];
                $d2 = $context['dim2'] ?? 0;
                
                if ($shape === 'circle') {
                    return pi() * pow($d1, 2);
                } elseif ($shape === 'triangle') {
                    return 0.5 * $d1 * $d2;
                } else {
                    return $d1 * $d2; // Rectangle
                }
            }
        ],
        'outputs' => [
            ['name' => 'area', 'unit' => 'sq unit', 'label' => 'Area', 'precision' => 2]
        ]
    ],
    
    'volume_calculator' => [
        'name' => 'Volume Calculator',
        'description' => 'Calculate volume of various 3D shapes',
        'category' => 'general',
        'subcategory' => 'geometry',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'shape', 'type' => 'string', 'label' => 'Shape', 'options' => ['cube', 'sphere', 'cylinder'], 'default' => 'cube'],
            ['name' => 'dim1', 'type' => 'number', 'label' => 'Dimension 1 (Side/r/r)', 'min' => 0],
            ['name' => 'dim2', 'type' => 'number', 'label' => 'Dimension 2 (-/-/h)', 'min' => 0, 'required' => false]
        ],
        'formulas' => [
            'volume' => function($context) {
                $shape = $context['shape'];
                $d1 = $context['dim1'];
                $d2 = $context['dim2'] ?? 0;
                
                if ($shape === 'sphere') {
                    return (4/3) * pi() * pow($d1, 3);
                } elseif ($shape === 'cylinder') {
                    return pi() * pow($d1, 2) * $d2;
                } else {
                    return pow($d1, 3); // Cube
                }
            }
        ],
        'outputs' => [
            ['name' => 'volume', 'unit' => 'cu unit', 'label' => 'Volume', 'precision' => 2]
        ]
    ]
];
