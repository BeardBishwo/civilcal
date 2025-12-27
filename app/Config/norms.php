<?php
/**
 * Construction Norms - Nepal Government (DUDBC) Based
 * Contains coefficients for material and labor consumption.
 */

return [
    'concrete' => [
        'pcc_124' => [
            'name' => 'PCC 1:2:4',
            'materials' => [
                'cement' => 0.228, // m3 per m3 of wet concrete
                'sand' => 0.457,   // m3 per m3
                'aggregate' => 0.914, // m3 per m3
                'cement_bags' => 3.23 // bags per m3
            ],
            'labor' => [
                'mason' => 0.25,
                'laborer' => 1.5,
                'mixer_operator' => 0.1
            ]
        ],
        'rcc_1153' => [
            'name' => 'RCC 1:1.5:3',
            'materials' => [
                'cement' => 0.285,
                'sand' => 0.428,
                'aggregate' => 0.856,
                'cement_bags' => 4.05
            ],
            'labor' => [
                'mason' => 0.35,
                'laborer' => 2.0,
                'mixer_operator' => 0.1
            ]
        ]
    ],
    'brickwork' => [
        'ratio_14' => [
            'name' => 'Brickwork 1:4',
            'materials' => [
                'bricks' => 530,    // numbers per m3
                'cement' => 0.064,  // m3 per m3
                'sand' => 0.25,     // m3 per m3
                'cement_bags' => 0.9 // bags per m3
            ],
            'labor' => [
                'mason' => 0.8,
                'laborer' => 1.2
            ]
        ],
        'ratio_16' => [
            'name' => 'Brickwork 1:6',
            'materials' => [
                'bricks' => 530,
                'cement' => 0.043,
                'sand' => 0.26,
                'cement_bags' => 0.6
            ],
            'labor' => [
                'mason' => 0.8,
                'laborer' => 1.2
            ]
        ]
    ],
    'plaster' => [
        'ratio_14_12mm' => [
            'name' => 'Plaster 1:4 (12.5mm)',
            'materials' => [
                'cement' => 0.005, // m3 per m2
                'sand' => 0.02,    // m3 per m2
                'cement_bags' => 0.125 // bags per m2
            ],
            'labor' => [
                'mason' => 0.15,
                'laborer' => 0.2
            ]
        ]
    ],
    'earthwork' => [
        'normal_soil' => [
            'name' => 'Earthwork in excavation (Normal Soil)',
            'unit' => 'm³',
            'labor' => [
                'laborer' => 0.70 // From Image 2, B1-1
            ]
        ],
        'hard_soil' => [
            'name' => 'Earthwork in excavation (Hard Soil/Murrum)',
            'unit' => 'm³',
            'labor' => [
                'laborer' => 0.80 // From Image 2, B1-2
            ]
        ]
    ],
    'road_bridge' => [
        'gabion_2x1x1' => [
            'name' => 'Gabion Box (2x1x1m)',
            'unit' => 'nos',
            'materials' => [
                'gi_wire' => 20.85, // From Image 3, GI Wire for 2x1x0.5 is 20, keeping 20.85 for 1m
                'selvage_wire' => 3.00
            ],
            'labor' => [
                'skilled_labor' => 0.32,
                'laborer' => 0.14
            ]
        ],
        'gabion_filling' => [
            'name' => 'Gabion Stone Filling',
            'unit' => 'm³',
            'materials' => [
                'stones' => 1.00
            ],
            'labor' => [
                'laborer' => 0.50 // From Image 3, P4
            ]
        ]
    ]
];
