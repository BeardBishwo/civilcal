<?php

/**
 * Civil Engineering Calculators Configuration
 * 
 * This file defines all civil engineering calculators:
 * - Concrete calculations (volume, mix, strength, rebar)
 * - Earthwork calculations
 * - Brickwork calculations
 * - Structural calculations
 * 
 * @package App\Config\Calculators
 */

return [
    'concrete-volume' => [
        'name' => 'Concrete Volume Calculator',
        'description' => 'Calculate volume of concrete required for slabs, beams, and columns',
        'category' => 'civil',
        'subcategory' => 'concrete',
        'version' => '1.0',
        
        'inputs' => [
            [
                'name' => 'length',
                'type' => 'number',
                'unit' => 'm',
                'unit_type' => 'length',
                'required' => true,
                'label' => 'Length',
                'min' => 0.01,
                'max' => 1000
            ],
            [
                'name' => 'width',
                'type' => 'number',
                'unit' => 'm',
                'unit_type' => 'length',
                'required' => true,
                'label' => 'Width',
                'min' => 0.01,
                'max' => 1000
            ],
            [
                'name' => 'depth',
                'type' => 'number',
                'unit' => 'm',
                'unit_type' => '

length',
                'required' => true,
                'label' => 'Depth/Height',
                'min' => 0.01,
                'max' => 100
            ]
        ],
        
        'formulas' => [
            'volume' => 'length * width * depth',
            'concrete_bags_50kg' => 'volume * 405 / 50', // Approx 405 kg cement per m³ for M20
            'concrete_weight' => 'volume * 2400', // Density of concrete: 2400 kg/m³
        ],
        
        'outputs' => [
            [
                'name' => 'volume',
                'unit' => 'm³',
                'label' => 'Concrete Volume',
                'precision' => 2,
                'type' => 'number'
            ],
            [
                'name' => 'concrete_bags_50kg',
                'unit' => 'bags',
                'label' => 'Cement Bags (50kg)',
                'precision' => 0,
                'type' => 'integer'
            ],
            [
                'name' => 'concrete_weight',
                'unit' => 'kg',
                'label' => 'Total Weight',
                'precision' => 0,
                'type' => 'integer'
            ]
        ]
    ],
    
    'concrete-mix' => [
        'name' => 'Concrete Mix Design Calculator',
        'description' => 'Calculate material quantities for concrete mix (cement, sand, aggregate, water)',
        'category' => 'civil',
        'subcategory' => 'concrete',
        'version' => '1.0',
        
        'inputs' => [
            [
                'name' => 'volume',
                'type' => 'number',
                'unit' => 'm³',
                'required' => true,
                'label' => 'Concrete Volume',
                'min' => 0.01,
                'max' => 10000
            ],
            [
                'name' => 'mix_ratio',
                'type' => 'string',
                'required' => true,
                'label' => 'Mix Ratio (e.g., 1:2:4)',
                'default' => '1:2:4',
                'options' => ['1:1.5:3', '1:2:4', '1:3:6']
            ]
        ],
        
        'formulas' => [
            // Custom formula function for mix design
            'cement' => function($context) {
                $volume = $context['volume'];
                $mix = $context['mix_ratio'];
                
                // Parse mix ratio
                list($cement_ratio, $sand_ratio, $aggregate_ratio) = explode(':', $mix);
                $total_ratio = $cement_ratio + $sand_ratio + $aggregate_ratio;
                
                // Calculate cement (with 54% increase for wastage and water)
                $dry_volume = $volume * 1.54;
                return ($cement_ratio / $total_ratio) * $dry_volume * 1440; // 1440 kg/m³ cement density
            },
            'sand' => function($context) {
                $volume = $context['volume'];
                $mix = $context['mix_ratio'];
                
                list($cement_ratio, $sand_ratio, $aggregate_ratio) = explode(':', $mix);
                $total_ratio = $cement_ratio + $sand_ratio + $aggregate_ratio;
                
                $dry_volume = $volume * 1.54;
                return ($sand_ratio / $total_ratio) * $dry_volume * 1600; // 1600 kg/m³ sand density
            },
            'aggregate' => function($context) {
                $volume = $context['volume'];
                $mix = $context['mix_ratio'];
                
                list($cement_ratio, $sand_ratio, $aggregate_ratio) = explode(':', $mix);
                $total_ratio = $cement_ratio + $sand_ratio + $aggregate_ratio;
                
                $dry_volume = $volume * 1.54;
                return ($aggregate_ratio / $total_ratio) * $dry_volume * 1520; // 1520 kg/m³ aggregate density
            },
            'water' => 'cement * 0.45', // Water-cement ratio 0.45
            'cement_bags' => 'cement / 50' // 50kg bags
        ],
        
        'outputs' => [
            [
                'name' => 'cement',
                'unit' => 'kg',
                'label' => 'Cement',
                'precision' => 1,
                'type' => 'number'
            ],
            [
                'name' => 'cement_bags',
                'unit' => 'bags',
                'label' => 'Cement Bags (50kg)',
                'precision' => 0,
                'type' => 'integer'
            ],
            [
                'name' => 'sand',
                'unit' => 'kg',
                'label' => 'Sand',
                'precision' => 1,
                'type' => 'number'
            ],
            [
                'name' => 'aggregate',
                'unit' => 'kg',
                'label' => 'Coarse Aggregate',
                'precision' => 1,
                'type' => 'number'
            ],
            [
                'name' => 'water',
                'unit' => 'liters',
                'label' => 'Water',
                'precision' => 1,
                'type' => 'number'
            ]
        ]
    ],
    
    'rebar-calculation' => [
        'name' => 'Rebar/Reinforcement Calculator',
        'description' => 'Calculate steel reinforcement quantity and weight',
        'category' => 'civil',
        'subcategory' => 'concrete',
        'version' => '1.0',
        
        'inputs' => [
            [
                'name' => 'diameter',
                'type' => 'number',
                'unit' => 'mm',
                'required' => true,
                'label' => 'Bar Diameter',
                'min' => 6,
                'max' => 50
            ],
            [
                'name' => 'length',
                'type' => 'number',
                'unit' => 'm',
                'required' => true,
                'label' => 'Total Length',
                'min' => 0.1,
                'max' => 100000
            ],
            [
                'name' => 'quantity',
                'type' => 'integer',
                'required' => true,
                'label' => 'Number of Bars',
                'min' => 1,
                'max' => 100000,
                'default' => 1
            ]
        ],
        
        'formulas' => [
            'unit_weight' => '(diameter * diameter) / 162', // Weight per meter for steel
            'total_weight' => 'unit_weight * length * quantity',
            'total_length' => 'length * quantity'
        ],
        
        'outputs' => [
            [
                'name' => 'unit_weight',
                'unit' => 'kg/m',
                'label' => 'Unit Weight',
                'precision' => 3,
                'type' => 'number'
            ],
            [
                'name' => 'total_weight',
                'unit' => 'kg',
                'label' => 'Total Weight',
                'precision' => 2,
                'type' => 'number'
            ],
            [
                'name' => 'total_length',
                'unit' => 'm',
                'label' => 'Total Length',
                'precision' => 2,
                'type' => 'number'
            ]
        ]
    ],
    
    'concrete-strength' => [
        'name' => 'Concrete Strength Calculator',
        'description' => 'Calculate compressive strength of concrete',
        'category' => 'civil',
        'subcategory' => 'concrete',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'load', 'type' => 'number', 'unit' => 'kN', 'required' => true, 'label' => 'Load', 'min' => 0],
            ['name' => 'area', 'type' => 'number', 'unit' => 'mm²', 'required' => true, 'label' => 'Area', 'min' => 1]
        ],
        'formulas' => [
            'strength' => '(load * 1000) / area'
        ],
        'outputs' => [
            ['name' => 'strength', 'unit' => 'MPa', 'label' => 'Compressive Strength', 'precision' => 2]
        ]
    ],
    
    // BRICKWORK CALCULATORS
    'brick-quantity' => [
        'name' => 'Brick Quantity Calculator',
        'description' => 'Calculate number of bricks required for wall construction',
        'category' => 'civil',
        'subcategory' => 'brickwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'wall_length', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Wall Length', 'min' => 0.1],
            ['name' => 'wall_height', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Wall Height', 'min' => 0.1],
           ['name' => 'brick_length', 'type' => 'number', 'unit' => 'mm', 'required' => true, 'label' => 'Brick Length', 'min' => 1, 'default' => 230],
            ['name' => 'brick_height', 'type' => 'number', 'unit' => 'mm', 'required' => true, 'label' => 'Brick Height', 'min' => 1, 'default' => 75],
            ['name' => 'mortar_thickness', 'type' => 'number', 'unit' => 'mm', 'required' => true, 'label' => 'Mortar Thickness', 'min' => 0, 'default' => 10]
        ],
        'formulas' => [
            'wall_area' => 'wall_length * wall_height',
            'brick_area' => '((brick_length / 1000) + (mortar_thickness / 1000)) * ((brick_height / 1000) + (mortar_thickness / 1000))',
            'total_bricks' => 'ceil(wall_area / brick_area)'
        ],
        'outputs' => [
            ['name' => 'wall_area', 'unit' => 'm²', 'label' => 'Wall Area', 'precision' => 2],
            ['name' => 'total_bricks', 'unit' => 'bricks', 'label' => 'Total Bricks', 'precision' => 0, 'type' => 'integer']
        ]
    ],
    
    'mortar-ratio' => [
        'name' => 'Mortar Ratio Calculator',
        'description' => 'Calculate cement and sand quantities for mortar',
        'category' => 'civil',
        'subcategory' => 'brickwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'mortar_volume', 'type' => 'number', 'unit' => 'm³', 'required' => true, 'label' => 'Mortar Volume', 'min' => 0.001],
            ['name' => 'mortar_ratio', 'type' => 'string', 'required' => true, 'label' => 'Mortar Ratio', 'default' => '1:4', 'options' => ['1:3', '1:4', '1:5', '1:6']]
        ],
        'formulas' => [
            'cement_volume' => function($context) {
                $volume = $context['mortar_volume'];
                $ratio = explode(':', $context['mortar_ratio']);
                $total = $ratio[0] + $ratio[1];
                return ($volume / $total) * $ratio[0];
            },
            'sand_volume' => function($context) {
                $volume = $context['mortar_volume'];
                $ratio = explode(':', $context['mortar_ratio']);
                $total = $ratio[0] + $ratio[1];
                return ($volume / $total) * $ratio[1];
            }
        ],
        'outputs' => [
            ['name' => 'cement_volume', 'unit' => 'm³', 'label' => 'Cement Volume', 'precision' => 3],
            ['name' => 'sand_volume', 'unit' => 'm³', 'label' => 'Sand Volume', 'precision' => 3]
        ]
    ],
    
    'plastering-estimator' => [
        'name' => 'Plastering Estimator',
        'description' => 'Calculate materials required for wall plastering',
        'category' => 'civil',
        'subcategory' => 'brickwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'area', 'type' => 'number', 'unit' => 'm²', 'required' => true, 'label' => 'Plaster Area', 'min' => 0.1],
            ['name' => 'thickness', 'type' => 'number', 'unit' => 'mm', 'required' => true, 'label' => 'Plaster Thickness', 'min' => 1, 'default' => 12]
        ],
        'formulas' => [
            'volume' => 'area * (thickness / 1000)',
            'dry_volume' => 'volume * 1.33',
            'cement' => 'dry_volume * (1/6) * 1440',
            'sand' => 'dry_volume * (5/6) * 1600'
        ],
        'outputs' => [
            ['name' => 'volume', 'unit' => 'm³', 'label' => 'Mortar Volume', 'precision' => 3],
            ['name' => 'cement', 'unit' => 'kg', 'label' => 'Cement Required', 'precision' => 2],
            ['name' => 'sand', 'unit' => 'kg', 'label' => 'Sand Required', 'precision' => 2]
        ]
    ],
    
    // EARTHWORK CALCULATORS
    'excavation-volume' => [
        'name' => 'Excavation Volume Calculator',
        'description' => 'Calculate volume of earth to be excavated',
        'category' => 'civil',
        'subcategory' => 'earthwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'length', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Length', 'min' => 0.1],
            ['name' => 'width', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Width', 'min' => 0.1],
            ['name' => 'depth', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Depth', 'min' => 0.1]
        ],
        'formulas' => [
            'volume' => 'length * width * depth',
            'weight' => 'volume * 1800'
        ],
        'outputs' => [
            ['name' => 'volume', 'unit' => 'm³', 'label' => 'Excavation Volume', 'precision' => 2],
            ['name' => 'weight', 'unit' => 'kg', 'label' => 'Approximate Weight', 'precision' => 0, 'type' => 'integer']
        ]
    ],
    
    'cut-and-fill-volume' => [
        'name' => 'Cut and Fill Volume Calculator',
        'description' => 'Calculate cut and fill volumes for site grading',
        'category' => 'civil',
        'subcategory' => 'earthwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'area', 'type' => 'number', 'unit' => 'm²', 'required' => true, 'label' => 'Area', 'min' => 1],
            ['name' => 'avg_cut_depth', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Average Cut Depth', 'min' => 0],
            ['name' => 'avg_fill_depth', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Average Fill Depth', 'min' => 0]
        ],
        'formulas' => [
            'cut_volume' => 'area * avg_cut_depth',
            'fill_volume' => 'area * avg_fill_depth',
            'net_volume' => 'cut_volume - fill_volume'
        ],
        'outputs' => [
            ['name' => 'cut_volume', 'unit' => 'm³', 'label' => 'Cut Volume', 'precision' => 2],
            ['name' => 'fill_volume', 'unit' => 'm³', 'label' => 'Fill Volume', 'precision' => 2],
            ['name' => 'net_volume', 'unit' => 'm³', 'label' => 'Net Volume', 'precision' => 2]
        ]
    ],
    
    'slope-calculation' => [
        'name' => 'Slope Calculation',
        'description' => 'Calculate slope angle and gradient',
        'category' => 'civil',
        'subcategory' => 'earthwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'horizontal_distance', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Horizontal Distance', 'min' => 0.1],
            ['name' => 'vertical_distance', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Vertical Distance', 'min' => 0]
        ],
        'formulas' => [
            'slope_percentage' => '(vertical_distance / horizontal_distance) * 100',
            'slope_angle' => 'atan(vertical_distance / horizontal_distance) * (180 / pi())'
        ],
        'outputs' => [
            ['name' => 'slope_percentage', 'unit' => '%', 'label' => 'Slope Percentage', 'precision' => 2],
            ['name' => 'slope_angle', 'unit' => '°', 'label' => 'Slope Angle', 'precision' => 2]
        ]
    ],
    
    // STRUCTURAL CALCULATORS
    'beam-load-capacity' => [
        'name' => 'Beam Load Capacity Calculator',
        'description' => 'Calculate maximum load capacity of a beam',
        'category' => 'civil',
        'subcategory' => 'structural',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'length', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Beam Length', 'min' => 0.1],
            ['name' => 'width', 'type' => 'number', 'unit' => 'mm', 'required' => true, 'label' => 'Beam Width', 'min' => 1],
            ['name' => 'depth', 'type' => 'number', 'unit' => 'mm', 'required' => true, 'label' => 'Beam Depth', 'min' => 1],
            ['name' => 'concrete_grade', 'type' => 'string', 'required' => true, 'label' => 'Concrete Grade', 'default' => 'M20', 'options' => ['M15', 'M20', 'M25', 'M30']]
        ],
        'formulas' => [
            'fck' => function($context) {
                return (int)str_replace('M', '', $context['concrete_grade']);
            },
            'moment_capacity' => '0.138 * fck * (width / 1000) * pow(depth / 1000, 2)',
            'uniformly_distributed_load' => '(8 * moment_capacity) / pow(length, 2)'
        ],
        'outputs' => [
            ['name' => 'moment_capacity', 'unit' => 'kN·m', 'label' => 'Moment Capacity', 'precision' => 2],
            ['name' => 'uniformly_distributed_load', 'unit' => 'kN/m', 'label' => 'Max UDL', 'precision' => 2]
        ]
    ],
    
    'column-design' => [
        'name' => 'Column Design Calculator',
        'description' => 'Calculate column load-bearing capacity',
        'category' => 'civil',
        'subcategory' => 'structural',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'width', 'type' => 'number', 'unit' => 'mm', 'required' => true, 'label' => 'Column Width', 'min' => 100],
            ['name' => 'depth', 'type' => 'number', 'unit' => 'mm', 'required' => true, 'label' => 'Column Depth', 'min' => 100],
            ['name' => 'height', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Column Height', 'min' => 1],
            ['name' => 'concrete_grade', 'type' => 'string', 'required' => true, 'label' => 'Concrete Grade', 'default' => 'M20', 'options' => ['M15', 'M20', 'M25', 'M30']]
        ],
        'formulas' => [
            'area' => '(width / 1000) * (depth / 1000)',
            'fck' => function($context) {
                return (int)str_replace('M', '', $context['concrete_grade']);
            },
            'slenderness_ratio' => '(height * 1000) / min(width, depth)',
            'load_capacity' => '0.4 * fck * area * 1000'
        ],
        'outputs' => [
            ['name' => 'area', 'unit' => 'm²', 'label' => 'Cross-sectional Area', 'precision' => 4],
            ['name' => 'slenderness_ratio', 'unit' => '', 'label' => 'Slenderness Ratio', 'precision' => 1],
            ['name' => 'load_capacity', 'unit' => 'kN', 'label' => 'Load Capacity', 'precision' => 2]
        ]
    ],
    
    'slab-design' => [
        'name' => 'Slab Design Calculator',
        'description' => 'Calculate slab thickness and reinforcement',
        'category' => 'civil',
        'subcategory' => 'structural',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'span', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Slab Span', 'min' => 1],
            ['name' => 'live_load', 'type' => 'number', 'unit' => 'kN/m²', 'required' => true, 'label' => 'Live Load', 'min' => 0, 'default' => 2]
        ],
        'formulas' => [
            'min_thickness' => '(span * 1000) / 28',
            'self_weight' => '(min_thickness / 1000) * 25',
            'total_load' => 'live_load + self_weight + 1',
            'moment' => 'total_load * pow(span, 2) / 8'
        ],
        'outputs' => [
            ['name' => 'min_thickness', 'unit' => 'mm', 'label' => 'Minimum Thickness', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'self_weight', 'unit' => 'kN/m²', 'label' => 'Self Weight', 'precision' => 2],
            ['name' => 'total_load', 'unit' => 'kN/m²', 'label' => 'Total Load', 'precision' => 2],
            ['name' => 'moment', 'unit' => 'kN·m/m', 'label' => 'Design Moment', 'precision' => 2]
        ]
    ],
    
    'foundation-design' => [
        'name' => 'Foundation Design Calculator',
        'description' => 'Calculate foundation size based on soil bearing capacity',
        'category' => 'civil',
        'subcategory' => 'structural',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'column_load', 'type' => 'number', 'unit' => 'kN', 'required' => true, 'label' => 'Column Load', 'min' => 1],
            ['name' => 'soil_bearing_capacity', 'type' => 'number', 'unit' => 'kN/m²', 'required' => true, 'label' => 'Safe Bearing Capacity', 'min' => 1, 'default' => 150]
        ],
        'formulas' => [
            'required_area' => 'column_load / soil_bearing_capacity',
            'foundation_width' => 'sqrt(required_area)',
            'foundation_length' => 'sqrt(required_area)'
        ],
        'outputs' => [
            ['name' => 'required_area', 'unit' => 'm²', 'label' => 'Required Area', 'precision' => 2],
            ['name' => 'foundation_width', 'unit' => 'm', 'label' => 'Foundation Width', 'precision' => 2],
            ['name' => 'foundation_length', 'unit' => 'm', 'label' => 'Foundation Length', 'precision' => 2]
        ]
    ]
];
