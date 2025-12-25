<?php

/**
 * Fire Protection Calculators Configuration
 * All fire safety and protection calculation tools
 */

return [
    // ============================================
    // SPRINKLERS (3 calculators)
    // ============================================
    
    'discharge-calculations' => [
        'name' => 'Sprinkler Discharge Calculator',
        'description' => 'Calculate sprinkler head discharge rates',
        'category' => 'fire',
        'subcategory' => 'sprinklers',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'pressure', 'type' => 'number', 'label' => 'Pressure at Head', 'unit' => 'psi', 'required' => true, 'min' => 0],
            ['name' => 'k_factor', 'type' => 'number', 'label' => 'K-Factor', 'unit' => '', 'required' => true, 'min' => 0, 'default' => 5.6],
        ],
        'formulas' => [
            'flow_rate' => function($inputs) {
                return $inputs['k_factor'] * sqrt($inputs['pressure']);
            },
            'flow_rate_lpm' => function($inputs, $results) {
                return $results['flow_rate'] * 3.785; // GPM to LPM
            },
        ],
        'outputs' => [
            ['name' => 'flow_rate', 'label' => 'Flow Rate', 'unit' => 'GPM', 'precision' => 2],
            ['name' => 'flow_rate_lpm', 'label' => 'Flow Rate', 'unit' => 'LPM', 'precision' => 2],
        ],
    ],
    
    'pipe-sizing' => [
        'name' => 'Sprinkler Pipe Sizing',
        'description' => 'Size sprinkler system piping',
        'category' => 'fire',
        'subcategory' => 'sprinklers',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'flow_rate', 'type' => 'number', 'label' => 'Flow Rate', 'unit' => 'GPM', 'required' => true, 'min' => 0],
            ['name' => 'velocity', 'type' => 'number', 'label' => 'Maximum Velocity', 'unit' => 'ft/s', 'required' => true, 'min' => 0, 'default' => 15],
        ],
        'formulas' => [
            'area' => function($inputs) {
                return ($inputs['flow_rate'] / 448.8) / $inputs['velocity']; // ft²
            },
            'diameter' => function($inputs, $results) {
                return sqrt($results['area'] * 4 / pi()) * 12; // inches
            },
            'nominal_size' => function($inputs, $results) {
                // Round up to standard pipe sizes
                $standard_sizes = [1, 1.25, 1.5, 2, 2.5, 3, 4, 5, 6, 8, 10, 12];
                foreach ($standard_sizes as $size) {
                    if ($size >= $results['diameter']) {
                        return $size;
                    }
                }
                return 12;
            },
        ],
        'outputs' => [
            ['name' => 'diameter', 'label' => 'Required Diameter', 'unit' => 'in', 'precision' => 2],
            ['name' => 'nominal_size', 'label' => 'Nominal Pipe Size', 'unit' => 'in', 'precision' => 2],
        ],
    ],
    
    'sprinkler-layout' => [
        'name' => 'Sprinkler Layout Calculator',
        'description' => 'Calculate sprinkler spacing and coverage',
        'category' => 'fire',
        'subcategory' => 'sprinklers',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'area', 'type' => 'number', 'label' => 'Protected Area', 'unit' => 'ft²', 'required' => true, 'min' => 0],
            ['name' => 'coverage_per_head', 'type' => 'number', 'label' => 'Coverage per Head', 'unit' => 'ft²', 'required' => true, 'min' => 0, 'default' => 130],
            ['name' => 'density', 'type' => 'number', 'label' => 'Design Density', 'unit' => 'GPM/ft²', 'required' => true, 'min' => 0, 'default' => 0.15],
        ],
        'formulas' => [
            'number_of_heads' => function($inputs) {
                return ceil($inputs['area'] / $inputs['coverage_per_head']);
            },
            'total_flow' => function($inputs) {
                return $inputs['area'] * $inputs['density'];
            },
            'flow_per_head' => function($inputs, $results) {
                return $results['total_flow'] / $results['number_of_heads'];
            },
        ],
        'outputs' => [
            ['name' => 'number_of_heads', 'label' => 'Number of Sprinklers', 'unit' => '', 'precision' => 0],
            ['name' => 'total_flow', 'label' => 'Total Flow Required', 'unit' => 'GPM', 'precision' => 1],
            ['name' => 'flow_per_head', 'label' => 'Flow per Head', 'unit' => 'GPM', 'precision' => 2],
        ],
    ],
    
    // ============================================
    // FIRE PUMPS (3 calculators)
    // ============================================
    
    'pump-sizing' => [
        'name' => 'Fire Pump Sizing',
        'description' => 'Calculate fire pump capacity requirements',
        'category' => 'fire',
        'subcategory' => 'fire-pumps',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'flow_rate', 'type' => 'number', 'label' => 'Required Flow', 'unit' => 'GPM', 'required' => true, 'min' => 0],
            ['name' => 'total_head', 'type' => 'number', 'label' => 'Total Head', 'unit' => 'ft', 'required' => true, 'min' => 0],
            ['name' => 'efficiency', 'type' => 'number', 'label' => 'Pump Efficiency', 'unit' => '%', 'required' => true, 'min' => 0, 'default' => 70],
        ],
        'formulas' => [
            'hydraulic_hp' => function($inputs) {
                return ($inputs['flow_rate'] * $inputs['total_head']) / 3960;
            },
            'brake_hp' => function($inputs, $results) {
                return $results['hydraulic_hp'] / ($inputs['efficiency'] / 100);
            },
            'rated_hp' => function($inputs, $results) {
                // Round up to standard motor sizes
                $standard_sizes = [5, 7.5, 10, 15, 20, 25, 30, 40, 50, 60, 75, 100, 125, 150, 200, 250];
                foreach ($standard_sizes as $size) {
                    if ($size >= $results['brake_hp']) {
                        return $size;
                    }
                }
                return 250;
            },
        ],
        'outputs' => [
            ['name' => 'hydraulic_hp', 'label' => 'Hydraulic Power', 'unit' => 'HP', 'precision' => 2],
            ['name' => 'brake_hp', 'label' => 'Brake Horsepower', 'unit' => 'HP', 'precision' => 2],
            ['name' => 'rated_hp', 'label' => 'Rated Motor Size', 'unit' => 'HP', 'precision' => 0],
        ],
    ],
    
    'driver-power' => [
        'name' => 'Fire Pump Driver Power',
        'description' => 'Calculate driver power requirements',
        'category' => 'fire',
        'subcategory' => 'fire-pumps',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'brake_hp', 'type' => 'number', 'label' => 'Brake Horsepower', 'unit' => 'HP', 'required' => true, 'min' => 0],
            ['name' => 'driver_type', 'type' => 'select', 'label' => 'Driver Type', 'required' => true, 'options' => [
                'electric' => 'Electric Motor',
                'diesel' => 'Diesel Engine'
            ]],
        ],
        'formulas' => [
            'rated_power' => function($inputs) {
                $multiplier = $inputs['driver_type'] === 'diesel' ? 1.5 : 1.15;
                return $inputs['brake_hp'] * $multiplier;
            },
            'kw' => function($inputs, $results) {
                return $results['rated_power'] * 0.746; // HP to kW
            },
        ],
        'outputs' => [
            ['name' => 'rated_power', 'label' => 'Rated Driver Power', 'unit' => 'HP', 'precision' => 1],
            ['name' => 'kw', 'label' => 'Rated Driver Power', 'unit' => 'kW', 'precision' => 1],
        ],
    ],
    
    'jockey-pump' => [
        'name' => 'Jockey Pump Sizing',
        'description' => 'Calculate jockey pump requirements',
        'category' => 'fire',
        'subcategory' => 'fire-pumps',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'main_pump_flow', 'type' => 'number', 'label' => 'Main Pump Flow', 'unit' => 'GPM', 'required' => true, 'min' => 0],
            ['name' => 'main_pump_pressure', 'type' => 'number', 'label' => 'Main Pump Pressure', 'unit' => 'psi', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'jockey_flow' => function($inputs) {
                return $inputs['main_pump_flow'] * 0.01; // 1% of main pump
            },
            'jockey_pressure' => function($inputs) {
                return $inputs['main_pump_pressure'] * 1.1; // 10% higher
            },
            'hp' => function($inputs, $results) {
                return ($results['jockey_flow'] * $results['jockey_pressure'] * 2.31) / (3960 * 0.6); // Assume 60% efficiency
            },
        ],
        'outputs' => [
            ['name' => 'jockey_flow', 'label' => 'Jockey Pump Flow', 'unit' => 'GPM', 'precision' => 1],
            ['name' => 'jockey_pressure', 'label' => 'Jockey Pump Pressure', 'unit' => 'psi', 'precision' => 1],
            ['name' => 'hp', 'label' => 'Motor Size', 'unit' => 'HP', 'precision' => 2],
        ],
    ],
    
    // ============================================
    // STANDPIPES (3 calculators)
    // ============================================
    
    'hose-demand' => [
        'name' => 'Standpipe Hose Demand',
        'description' => 'Calculate standpipe hose stream demand',
        'category' => 'fire',
        'subcategory' => 'standpipes',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'class', 'type' => 'select', 'label' => 'Standpipe Class', 'required' => true, 'options' => [
                'I' => 'Class I (2.5" hose)',
                'II' => 'Class II (1.5" hose)',
                'III' => 'Class III (Both)'
            ]],
            ['name' => 'outlets', 'type' => 'number', 'label' => 'Number of Outlets', 'unit' => '', 'required' => true, 'min' => 1, 'default' => 2],
        ],
        'formulas' => [
            'flow_per_outlet' => function($inputs) {
                $flows = [
                    'I' => 250,
                    'II' => 100,
                    'III' => 250
                ];
                return $flows[$inputs['class']] ?? 250;
            },
            'total_flow' => function($inputs, $results) {
                return $results['flow_per_outlet'] * $inputs['outlets'];
            },
            'residual_pressure' => function($inputs) {
                $pressures = [
                    'I' => 100,
                    'II' => 65,
                    'III' => 100
                ];
                return $pressures[$inputs['class']] ?? 100;
            },
        ],
        'outputs' => [
            ['name' => 'flow_per_outlet', 'label' => 'Flow per Outlet', 'unit' => 'GPM', 'precision' => 0],
            ['name' => 'total_flow', 'label' => 'Total Flow Demand', 'unit' => 'GPM', 'precision' => 0],
            ['name' => 'residual_pressure', 'label' => 'Residual Pressure', 'unit' => 'psi', 'precision' => 0],
        ],
    ],
    
    'pressure-calculations' => [
        'name' => 'Standpipe Pressure Calculator',
        'description' => 'Calculate standpipe system pressures',
        'category' => 'fire',
        'subcategory' => 'standpipes',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'height', 'type' => 'number', 'label' => 'Building Height', 'unit' => 'ft', 'required' => true, 'min' => 0],
            ['name' => 'residual_pressure', 'type' => 'number', 'label' => 'Required Residual Pressure', 'unit' => 'psi', 'required' => true, 'min' => 0, 'default' => 100],
            ['name' => 'friction_loss', 'type' => 'number', 'label' => 'Friction Loss', 'unit' => 'psi', 'required' => true, 'min' => 0, 'default' => 25],
        ],
        'formulas' => [
            'elevation_pressure' => function($inputs) {
                return $inputs['height'] * 0.433; // psi per foot
            },
            'total_pressure' => function($inputs, $results) {
                return $results['elevation_pressure'] + $inputs['residual_pressure'] + $inputs['friction_loss'];
            },
        ],
        'outputs' => [
            ['name' => 'elevation_pressure', 'label' => 'Elevation Pressure', 'unit' => 'psi', 'precision' => 1],
            ['name' => 'total_pressure', 'label' => 'Total System Pressure', 'unit' => 'psi', 'precision' => 1],
        ],
    ],
    
    'standpipe-classification' => [
        'name' => 'Standpipe Classification',
        'description' => 'Determine standpipe system classification',
        'category' => 'fire',
        'subcategory' => 'standpipes',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'building_height', 'type' => 'number', 'label' => 'Building Height', 'unit' => 'ft', 'required' => true, 'min' => 0],
            ['name' => 'occupancy_type', 'type' => 'select', 'label' => 'Occupancy Type', 'required' => true, 'options' => [
                'assembly' => 'Assembly',
                'business' => 'Business',
                'educational' => 'Educational',
                'residential' => 'Residential',
                'storage' => 'Storage'
            ]],
        ],
        'formulas' => [
            'recommended_class' => function($inputs) {
                // Simplified logic
                if ($inputs['building_height'] > 75) {
                    return 'Class I or III';
                } elseif ($inputs['occupancy_type'] === 'residential') {
                    return 'Class II or III';
                } else {
                    return 'Class I';
                }
            },
            'minimum_outlets' => function($inputs) {
                return ceil($inputs['building_height'] / 40); // One per 40 ft
            },
        ],
        'outputs' => [
            ['name' => 'recommended_class', 'label' => 'Recommended Class', 'unit' => '', 'precision' => 0],
            ['name' => 'minimum_outlets', 'label' => 'Minimum Outlets', 'unit' => '', 'precision' => 0],
        ],
    ],
    
    // ============================================
    // HAZARD CLASSIFICATION (3 calculators)
    // ============================================
    
    'occupancy-assessment' => [
        'name' => 'Occupancy Hazard Assessment',
        'description' => 'Assess fire hazard classification',
        'category' => 'fire',
        'subcategory' => 'hazard-classification',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'combustible_load', 'type' => 'number', 'label' => 'Combustible Load', 'unit' => 'lb/ft²', 'required' => true, 'min' => 0],
            ['name' => 'ceiling_height', 'type' => 'number', 'label' => 'Ceiling Height', 'unit' => 'ft', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'hazard_class' => function($inputs) {
                if ($inputs['combustible_load'] < 8) {
                    return 'Light Hazard';
                } elseif ($inputs['combustible_load'] < 12) {
                    return 'Ordinary Hazard Group 1';
                } elseif ($inputs['combustible_load'] < 20) {
                    return 'Ordinary Hazard Group 2';
                } else {
                    return 'Extra Hazard';
                }
            },
            'design_density' => function($inputs, $results) {
                $densities = [
                    'Light Hazard' => 0.10,
                    'Ordinary Hazard Group 1' => 0.15,
                    'Ordinary Hazard Group 2' => 0.20,
                    'Extra Hazard' => 0.30
                ];
                return $densities[$results['hazard_class']] ?? 0.15;
            },
        ],
        'outputs' => [
            ['name' => 'hazard_class', 'label' => 'Hazard Classification', 'unit' => '', 'precision' => 0],
            ['name' => 'design_density', 'label' => 'Design Density', 'unit' => 'GPM/ft²', 'precision' => 2],
        ],
    ],
    
    'design-density' => [
        'name' => 'Design Density Calculator',
        'description' => 'Calculate required sprinkler design density',
        'category' => 'fire',
        'subcategory' => 'hazard-classification',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'hazard_class', 'type' => 'select', 'label' => 'Hazard Classification', 'required' => true, 'options' => [
                'light' => 'Light Hazard',
                'oh1' => 'Ordinary Hazard Group 1',
                'oh2' => 'Ordinary Hazard Group 2',
                'eh1' => 'Extra Hazard Group 1',
                'eh2' => 'Extra Hazard Group 2'
            ]],
            ['name' => 'design_area', 'type' => 'number', 'label' => 'Design Area', 'unit' => 'ft²', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'density' => function($inputs) {
                $densities = [
                    'light' => 0.10,
                    'oh1' => 0.15,
                    'oh2' => 0.20,
                    'eh1' => 0.30,
                    'eh2' => 0.40
                ];
                return $densities[$inputs['hazard_class']] ?? 0.15;
            },
            'total_flow' => function($inputs, $results) {
                return $results['density'] * $inputs['design_area'];
            },
        ],
        'outputs' => [
            ['name' => 'density', 'label' => 'Design Density', 'unit' => 'GPM/ft²', 'precision' => 2],
            ['name' => 'total_flow', 'label' => 'Total Water Demand', 'unit' => 'GPM', 'precision' => 0],
        ],
    ],
    
    'commodity-classification' => [
        'name' => 'Commodity Classification',
        'description' => 'Classify stored commodities for fire protection',
        'category' => 'fire',
        'subcategory' => 'hazard-classification',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'storage_height', 'type' => 'number', 'label' => 'Storage Height', 'unit' => 'ft', 'required' => true, 'min' => 0],
            ['name' => 'commodity_type', 'type' => 'select', 'label' => 'Commodity Type', 'required' => true, 'options' => [
                'class1' => 'Class I (Non-combustible)',
                'class2' => 'Class II (Low combustible)',
                'class3' => 'Class III (Wood/paper)',
                'class4' => 'Class IV (Plastics)',
                'plastic' => 'Plastic commodities'
            ]],
        ],
        'formulas' => [
            'protection_level' => function($inputs) {
                if ($inputs['storage_height'] > 25) {
                    return 'ESFR or In-Rack';
                } elseif ($inputs['commodity_type'] === 'plastic') {
                    return 'High Challenge';
                } else {
                    return 'Standard Coverage';
                }
            },
            'k_factor' => function($inputs) {
                if ($inputs['storage_height'] > 25 || $inputs['commodity_type'] === 'plastic') {
                    return 25.2; // K-25 ESFR
                } else {
                    return 11.2; // K-11
                }
            },
        ],
        'outputs' => [
            ['name' => 'protection_level', 'label' => 'Protection Level', 'unit' => '', 'precision' => 0],
            ['name' => 'k_factor', 'label' => 'Recommended K-Factor', 'unit' => '', 'precision' => 1],
        ],
    ],
    
    // ============================================
    // HYDRAULICS (1 calculator)
    // ============================================
    
    'hazen-williams' => [
        'name' => 'Hazen-Williams Calculator',
        'description' => 'Calculate pipe friction loss using Hazen-Williams equation',
        'category' => 'fire',
        'subcategory' => 'hydraulics',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'flow_rate', 'type' => 'number', 'label' => 'Flow Rate', 'unit' => 'GPM', 'required' => true, 'min' => 0],
            ['name' => 'diameter', 'type' => 'number', 'label' => 'Pipe Diameter', 'unit' => 'in', 'required' => true, 'min' => 0],
            ['name' => 'length', 'type' => 'number', 'label' => 'Pipe Length', 'unit' => 'ft', 'required' => true, 'min' => 0],
            ['name' => 'c_factor', 'type' => 'number', 'label' => 'C-Factor', 'unit' => '', 'required' => true, 'min' => 0, 'default' => 120],
        ],
        'formulas' => [
            'velocity' => function($inputs) {
                $area = pi() * pow($inputs['diameter'] / 12 / 2, 2);
                return ($inputs['flow_rate'] / 448.8) / $area; // ft/s
            },
            'friction_loss_per_ft' => function($inputs) {
                return 4.52 * pow($inputs['flow_rate'], 1.85) / (pow($inputs['c_factor'], 1.85) * pow($inputs['diameter'], 4.87));
            },
            'total_friction_loss' => function($inputs, $results) {
                return $results['friction_loss_per_ft'] * $inputs['length'];
            },
        ],
        'outputs' => [
            ['name' => 'velocity', 'label' => 'Velocity', 'unit' => 'ft/s', 'precision' => 2],
            ['name' => 'friction_loss_per_ft', 'label' => 'Friction Loss per Foot', 'unit' => 'psi/ft', 'precision' => 4],
            ['name' => 'total_friction_loss', 'label' => 'Total Friction Loss', 'unit' => 'psi', 'precision' => 2],
        ],
    ],
];
