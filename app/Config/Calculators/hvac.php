<?php

/**
 * HVAC Calculators Configuration
 * All HVAC engineering calculation tools
 */

return [
    // ============================================
    // LOAD CALCULATION (4 calculators)
    // ============================================
    
    'cooling-load' => [
        'name' => 'Cooling Load Calculator',
        'description' => 'Calculate cooling load estimation for different room types and occupancy conditions',
        'category' => 'hvac',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'area', 'type' => 'number', 'label' => 'Area', 'unit' => 'm²', 'required' => true, 'min' => 0],
            ['name' => 'room_type', 'type' => 'select', 'label' => 'Room Type', 'required' => true, 'options' => [
                'office' => 'Office',
                'residential' => 'Residential',
                'commercial' => 'Commercial',
                'hospital' => 'Hospital',
                'hotel' => 'Hotel Room',
                'classroom' => 'Classroom',
                'restaurant' => 'Restaurant'
            ]],
            ['name' => 'occupants', 'type' => 'number', 'label' => 'Occupants', 'unit' => 'people', 'required' => true, 'min' => 0, 'default' => 0],
            ['name' => 'equipment_load', 'type' => 'number', 'label' => 'Equipment Load', 'unit' => 'W', 'required' => true, 'min' => 0, 'default' => 0],
            ['name' => 'lighting_load', 'type' => 'number', 'label' => 'Lighting Load', 'unit' => 'W', 'required' => true, 'min' => 0, 'default' => 0],
        ],
        'formulas' => [
            'base_load' => function($inputs) {
                $factors = [
                    'office' => 100,
                    'residential' => 120,
                    'commercial' => 150,
                    'hospital' => 180,
                    'hotel' => 130,
                    'classroom' => 140,
                    'restaurant' => 200
                ];
                return $inputs['area'] * ($factors[$inputs['room_type']] ?? 120);
            },
            'occupant_load' => function($inputs) {
                return $inputs['occupants'] * 150; // 100W sensible + 50W latent
            },
            'total_load_w' => function($inputs, $results) {
                return $results['base_load'] + $results['occupant_load'] + $inputs['equipment_load'] + $inputs['lighting_load'];
            },
            'total_load_btuh' => function($inputs, $results) {
                return $results['total_load_w'] * 3.412;
            },
            'tons' => function($inputs, $results) {
                return $results['total_load_btuh'] / 12000;
            },
        ],
        'outputs' => [
            ['name' => 'base_load', 'label' => 'Base Load', 'unit' => 'W', 'precision' => 0],
            ['name' => 'occupant_load', 'label' => 'Occupant Load', 'unit' => 'W', 'precision' => 0],
            ['name' => 'total_load_w', 'label' => 'Total Cooling Load', 'unit' => 'W', 'precision' => 0],
            ['name' => 'total_load_btuh', 'label' => 'Total Cooling Load', 'unit' => 'BTU/hr', 'precision' => 0],
            ['name' => 'tons', 'label' => 'Required Capacity', 'unit' => 'Tons', 'precision' => 2],
        ],
    ],
    
    'heating-load' => [
        'name' => 'Heating Load Calculator',
        'description' => 'Calculate heating load for building spaces',
        'category' => 'hvac',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'area', 'type' => 'number', 'label' => 'Area', 'unit' => 'm²', 'required' => true, 'min' => 0],
            ['name' => 'height', 'type' => 'number', 'label' => 'Ceiling Height', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'temp_diff', 'type' => 'number', 'label' => 'Temperature Difference', 'unit' => '°C', 'required' => true, 'min' => 0],
            ['name' => 'u_value', 'type' => 'number', 'label' => 'U-Value', 'unit' => 'W/m²K', 'required' => true, 'min' => 0, 'default' => 0.3],
        ],
        'formulas' => [
            'volume' => function($inputs) {
                return $inputs['area'] * $inputs['height'];
            },
            'heat_loss' => function($inputs) {
                return $inputs['area'] * $inputs['u_value'] * $inputs['temp_diff'];
            },
            'heat_loss_btuh' => function($inputs, $results) {
                return $results['heat_loss'] * 3.412;
            },
        ],
        'outputs' => [
            ['name' => 'volume', 'label' => 'Room Volume', 'unit' => 'm³', 'precision' => 2],
            ['name' => 'heat_loss', 'label' => 'Heat Loss', 'unit' => 'W', 'precision' => 0],
            ['name' => 'heat_loss_btuh', 'label' => 'Heat Loss', 'unit' => 'BTU/hr', 'precision' => 0],
        ],
    ],
    
    'infiltration' => [
        'name' => 'Infiltration Calculator',
        'description' => 'Calculate air infiltration heat loss/gain',
        'category' => 'hvac',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'volume', 'type' => 'number', 'label' => 'Room Volume', 'unit' => 'm³', 'required' => true, 'min' => 0],
            ['name' => 'ach', 'type' => 'number', 'label' => 'Air Changes per Hour', 'unit' => 'ACH', 'required' => true, 'min' => 0, 'default' => 0.5],
            ['name' => 'temp_diff', 'type' => 'number', 'label' => 'Temperature Difference', 'unit' => '°C', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'airflow' => function($inputs) {
                return $inputs['volume'] * $inputs['ach'];
            },
            'heat_loss' => function($inputs, $results) {
                return $results['airflow'] * 0.33 * $inputs['temp_diff']; // 0.33 W/m³/°C
            },
        ],
        'outputs' => [
            ['name' => 'airflow', 'label' => 'Infiltration Airflow', 'unit' => 'm³/hr', 'precision' => 2],
            ['name' => 'heat_loss', 'label' => 'Heat Loss/Gain', 'unit' => 'W', 'precision' => 0],
        ],
    ],
    
    'ventilation' => [
        'name' => 'Ventilation Calculator',
        'description' => 'Calculate required ventilation rates',
        'category' => 'hvac',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'occupants', 'type' => 'number', 'label' => 'Number of Occupants', 'unit' => 'people', 'required' => true, 'min' => 0],
            ['name' => 'area', 'type' => 'number', 'label' => 'Floor Area', 'unit' => 'm²', 'required' => true, 'min' => 0],
            ['name' => 'space_type', 'type' => 'select', 'label' => 'Space Type', 'required' => true, 'options' => [
                'office' => 'Office',
                'classroom' => 'Classroom',
                'retail' => 'Retail',
                'restaurant' => 'Restaurant',
                'residential' => 'Residential'
            ]],
        ],
        'formulas' => [
            'cfm_per_person' => function($inputs) {
                $rates = [
                    'office' => 5,
                    'classroom' => 7.5,
                    'retail' => 7.5,
                    'restaurant' => 7.5,
                    'residential' => 7.5
                ];
                return $inputs['occupants'] * ($rates[$inputs['space_type']] ?? 7.5);
            },
            'cfm_per_area' => function($inputs) {
                return $inputs['area'] * 0.06; // CFM per ft²
            },
            'total_cfm' => function($inputs, $results) {
                return $results['cfm_per_person'] + $results['cfm_per_area'];
            },
            'total_lps' => function($inputs, $results) {
                return $results['total_cfm'] * 0.472; // Convert CFM to L/s
            },
        ],
        'outputs' => [
            ['name' => 'cfm_per_person', 'label' => 'Ventilation (People)', 'unit' => 'CFM', 'precision' => 1],
            ['name' => 'cfm_per_area', 'label' => 'Ventilation (Area)', 'unit' => 'CFM', 'precision' => 1],
            ['name' => 'total_cfm', 'label' => 'Total Ventilation', 'unit' => 'CFM', 'precision' => 1],
            ['name' => 'total_lps', 'label' => 'Total Ventilation', 'unit' => 'L/s', 'precision' => 1],
        ],
    ],
    
    // ============================================
    // EQUIPMENT SIZING (4 calculators)
    // ============================================
    
    'ac-sizing' => [
        'name' => 'AC Unit Sizing Calculator',
        'description' => 'Calculate proper air conditioning unit size',
        'category' => 'hvac',
        'subcategory' => 'equipment-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'cooling_load', 'type' => 'number', 'label' => 'Cooling Load', 'unit' => 'BTU/hr', 'required' => true, 'min' => 0],
            ['name' => 'safety_factor', 'type' => 'number', 'label' => 'Safety Factor', 'unit' => '%', 'required' => true, 'min' => 0, 'default' => 15],
        ],
        'formulas' => [
            'adjusted_load' => function($inputs) {
                return $inputs['cooling_load'] * (1 + $inputs['safety_factor'] / 100);
            },
            'tons' => function($inputs, $results) {
                return $results['adjusted_load'] / 12000;
            },
            'kw' => function($inputs, $results) {
                return $results['adjusted_load'] * 0.000293; // BTU/hr to kW
            },
        ],
        'outputs' => [
            ['name' => 'adjusted_load', 'label' => 'Adjusted Load', 'unit' => 'BTU/hr', 'precision' => 0],
            ['name' => 'tons', 'label' => 'Required Capacity', 'unit' => 'Tons', 'precision' => 2],
            ['name' => 'kw', 'label' => 'Required Capacity', 'unit' => 'kW', 'precision' => 2],
        ],
    ],
    
    'chiller-sizing' => [
        'name' => 'Chiller Sizing Calculator',
        'description' => 'Calculate chiller capacity requirements',
        'category' => 'hvac',
        'subcategory' => 'equipment-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'total_load', 'type' => 'number', 'label' => 'Total Cooling Load', 'unit' => 'Tons', 'required' => true, 'min' => 0],
            ['name' => 'diversity_factor', 'type' => 'number', 'label' => 'Diversity Factor', 'unit' => '%', 'required' => true, 'min' => 0, 'default' => 85],
            ['name' => 'safety_margin', 'type' => 'number', 'label' => 'Safety Margin', 'unit' => '%', 'required' => true, 'min' => 0, 'default' => 10],
        ],
        'formulas' => [
            'design_load' => function($inputs) {
                return $inputs['total_load'] * ($inputs['diversity_factor'] / 100);
            },
            'chiller_capacity' => function($inputs, $results) {
                return $results['design_load'] * (1 + $inputs['safety_margin'] / 100);
            },
            'kw' => function($inputs, $results) {
                return $results['chiller_capacity'] * 3.517; // Tons to kW
            },
        ],
        'outputs' => [
            ['name' => 'design_load', 'label' => 'Design Load', 'unit' => 'Tons', 'precision' => 2],
            ['name' => 'chiller_capacity', 'label' => 'Chiller Capacity', 'unit' => 'Tons', 'precision' => 2],
            ['name' => 'kw', 'label' => 'Chiller Capacity', 'unit' => 'kW', 'precision' => 2],
        ],
    ],
    
    'furnace-sizing' => [
        'name' => 'Furnace Sizing Calculator',
        'description' => 'Calculate furnace capacity requirements',
        'category' => 'hvac',
        'subcategory' => 'equipment-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'heat_loss', 'type' => 'number', 'label' => 'Heat Loss', 'unit' => 'BTU/hr', 'required' => true, 'min' => 0],
            ['name' => 'efficiency', 'type' => 'number', 'label' => 'Furnace Efficiency', 'unit' => '%', 'required' => true, 'min' => 0, 'default' => 95],
        ],
        'formulas' => [
            'input_capacity' => function($inputs) {
                return $inputs['heat_loss'] / ($inputs['efficiency'] / 100);
            },
            'output_capacity' => function($inputs) {
                return $inputs['heat_loss'];
            },
        ],
        'outputs' => [
            ['name' => 'output_capacity', 'label' => 'Output Capacity', 'unit' => 'BTU/hr', 'precision' => 0],
            ['name' => 'input_capacity', 'label' => 'Input Capacity', 'unit' => 'BTU/hr', 'precision' => 0],
        ],
    ],
    
    'hvac-pump-sizing' => [
        'name' => 'HVAC Pump Sizing Calculator',
        'description' => 'Calculate pump requirements for HVAC systems',
        'category' => 'hvac',
        'subcategory' => 'equipment-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'flow_rate', 'type' => 'number', 'label' => 'Flow Rate', 'unit' => 'GPM', 'required' => true, 'min' => 0],
            ['name' => 'head', 'type' => 'number', 'label' => 'Total Head', 'unit' => 'ft', 'required' => true, 'min' => 0],
            ['name' => 'efficiency', 'type' => 'number', 'label' => 'Pump Efficiency', 'unit' => '%', 'required' => true, 'min' => 0, 'default' => 70],
        ],
        'formulas' => [
            'hydraulic_power' => function($inputs) {
                return ($inputs['flow_rate'] * $inputs['head']) / 3960; // HP
            },
            'brake_power' => function($inputs, $results) {
                return $results['hydraulic_power'] / ($inputs['efficiency'] / 100);
            },
            'motor_power' => function($inputs, $results) {
                // Round up to next standard motor size
                $standard_sizes = [0.5, 0.75, 1, 1.5, 2, 3, 5, 7.5, 10, 15, 20, 25, 30];
                foreach ($standard_sizes as $size) {
                    if ($size >= $results['brake_power']) {
                        return $size;
                    }
                }
                return 30;
            },
        ],
        'outputs' => [
            ['name' => 'hydraulic_power', 'label' => 'Hydraulic Power', 'unit' => 'HP', 'precision' => 2],
            ['name' => 'brake_power', 'label' => 'Brake Power', 'unit' => 'HP', 'precision' => 2],
            ['name' => 'motor_power', 'label' => 'Motor Power (Standard)', 'unit' => 'HP', 'precision' => 2],
        ],
    ],
    
    // ============================================
    // DUCT SIZING (5 calculators)
    // ============================================
    
    'duct-by-velocity' => [
        'name' => 'Duct Velocity Sizing',
        'description' => 'Size ductwork based on velocity method',
        'category' => 'hvac',
        'subcategory' => 'duct-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'airflow', 'type' => 'number', 'label' => 'Airflow', 'unit' => 'CFM', 'required' => true, 'min' => 0],
            ['name' => 'velocity', 'type' => 'number', 'label' => 'Design Velocity', 'unit' => 'FPM', 'required' => true, 'min' => 0, 'default' => 1000],
        ],
        'formulas' => [
            'area' => function($inputs) {
                return $inputs['airflow'] / $inputs['velocity']; // ft²
            },
            'diameter' => function($inputs, $results) {
                return sqrt($results['area'] * 4 / pi()) * 12; // inches
            },
            'width' => function($inputs, $results) {
                return sqrt($results['area']) * 12; // inches (for square duct)
            },
        ],
        'outputs' => [
            ['name' => 'area', 'label' => 'Duct Area', 'unit' => 'ft²', 'precision' => 2],
            ['name' => 'diameter', 'label' => 'Round Duct Diameter', 'unit' => 'in', 'precision' => 1],
            ['name' => 'width', 'label' => 'Square Duct Size', 'unit' => 'in', 'precision' => 1],
        ],
    ],
    
    'pressure-drop' => [
        'name' => 'Duct Pressure Drop Calculator',
        'description' => 'Calculate pressure drop in ductwork',
        'category' => 'hvac',
        'subcategory' => 'duct-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'airflow', 'type' => 'number', 'label' => 'Airflow', 'unit' => 'CFM', 'required' => true, 'min' => 0],
            ['name' => 'diameter', 'type' => 'number', 'label' => 'Duct Diameter', 'unit' => 'in', 'required' => true, 'min' => 0],
            ['name' => 'length', 'type' => 'number', 'label' => 'Duct Length', 'unit' => 'ft', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'velocity' => function($inputs) {
                $area = pi() * pow($inputs['diameter'] / 12 / 2, 2); // ft²
                return $inputs['airflow'] / $area; // FPM
            },
            'pressure_drop_per_100ft' => function($inputs, $results) {
                // Simplified friction loss formula
                return 0.109 * pow($results['velocity'] / 1000, 1.9) / pow($inputs['diameter'], 1.22);
            },
            'total_pressure_drop' => function($inputs, $results) {
                return $results['pressure_drop_per_100ft'] * ($inputs['length'] / 100);
            },
        ],
        'outputs' => [
            ['name' => 'velocity', 'label' => 'Air Velocity', 'unit' => 'FPM', 'precision' => 0],
            ['name' => 'pressure_drop_per_100ft', 'label' => 'Pressure Drop per 100ft', 'unit' => 'in.w.g.', 'precision' => 3],
            ['name' => 'total_pressure_drop', 'label' => 'Total Pressure Drop', 'unit' => 'in.w.g.', 'precision' => 3],
        ],
    ],
    
    'equivalent-round' => [
        'name' => 'Equivalent Duct Calculator',
        'description' => 'Convert between round and rectangular ducts',
        'category' => 'hvac',
        'subcategory' => 'duct-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'width', 'type' => 'number', 'label' => 'Rectangular Width', 'unit' => 'in', 'required' => true, 'min' => 0],
            ['name' => 'height', 'type' => 'number', 'label' => 'Rectangular Height', 'unit' => 'in', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'equivalent_diameter' => function($inputs) {
                // Huebscher equation
                return 1.3 * pow(($inputs['width'] * $inputs['height']), 0.625) / pow(($inputs['width'] + $inputs['height']), 0.25);
            },
            'rect_area' => function($inputs) {
                return $inputs['width'] * $inputs['height'];
            },
            'round_area' => function($inputs, $results) {
                return pi() * pow($results['equivalent_diameter'] / 2, 2);
            },
        ],
        'outputs' => [
            ['name' => 'equivalent_diameter', 'label' => 'Equivalent Round Diameter', 'unit' => 'in', 'precision' => 1],
            ['name' => 'rect_area', 'label' => 'Rectangular Area', 'unit' => 'in²', 'precision' => 1],
            ['name' => 'round_area', 'label' => 'Round Area', 'unit' => 'in²', 'precision' => 1],
        ],
    ],
    
    'fitting-loss' => [
        'name' => 'Fitting Loss Calculator',
        'description' => 'Calculate pressure loss through duct fittings',
        'category' => 'hvac',
        'subcategory' => 'duct-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'velocity', 'type' => 'number', 'label' => 'Air Velocity', 'unit' => 'FPM', 'required' => true, 'min' => 0],
            ['name' => 'fitting_type', 'type' => 'select', 'label' => 'Fitting Type', 'required' => true, 'options' => [
                'elbow_90' => '90° Elbow',
                'elbow_45' => '45° Elbow',
                'tee_branch' => 'Tee Branch',
                'tee_straight' => 'Tee Straight',
                'damper' => 'Damper'
            ]],
        ],
        'formulas' => [
            'velocity_pressure' => function($inputs) {
                return pow($inputs['velocity'] / 4005, 2); // in.w.g.
            },
            'loss_coefficient' => function($inputs) {
                $coefficients = [
                    'elbow_90' => 0.25,
                    'elbow_45' => 0.15,
                    'tee_branch' => 0.5,
                    'tee_straight' => 0.1,
                    'damper' => 0.2
                ];
                return $coefficients[$inputs['fitting_type']] ?? 0.25;
            },
            'pressure_loss' => function($inputs, $results) {
                return $results['velocity_pressure'] * $results['loss_coefficient'];
            },
        ],
        'outputs' => [
            ['name' => 'velocity_pressure', 'label' => 'Velocity Pressure', 'unit' => 'in.w.g.', 'precision' => 3],
            ['name' => 'loss_coefficient', 'label' => 'Loss Coefficient', 'unit' => '', 'precision' => 2],
            ['name' => 'pressure_loss', 'label' => 'Pressure Loss', 'unit' => 'in.w.g.', 'precision' => 3],
        ],
    ],
    
    'grille-sizing' => [
        'name' => 'Grille Sizing Calculator',
        'description' => 'Size supply and return grilles',
        'category' => 'hvac',
        'subcategory' => 'duct-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'airflow', 'type' => 'number', 'label' => 'Airflow', 'unit' => 'CFM', 'required' => true, 'min' => 0],
            ['name' => 'velocity', 'type' => 'number', 'label' => 'Face Velocity', 'unit' => 'FPM', 'required' => true, 'min' => 0, 'default' => 500],
        ],
        'formulas' => [
            'free_area' => function($inputs) {
                return $inputs['airflow'] / $inputs['velocity']; // ft²
            },
            'gross_area' => function($inputs, $results) {
                return $results['free_area'] / 0.75; // Assuming 75% free area
            },
            'width' => function($inputs, $results) {
                return sqrt($results['gross_area']) * 12; // inches (square grille)
            },
        ],
        'outputs' => [
            ['name' => 'free_area', 'label' => 'Free Area', 'unit' => 'ft²', 'precision' => 2],
            ['name' => 'gross_area', 'label' => 'Gross Area', 'unit' => 'ft²', 'precision' => 2],
            ['name' => 'width', 'label' => 'Grille Size (Square)', 'unit' => 'in', 'precision' => 0],
        ],
    ],
    
    // ============================================
    // PSYCHROMETRICS (4 calculators)
    // ============================================
    
    'air-properties' => [
        'name' => 'Air Properties Calculator',
        'description' => 'Calculate psychrometric properties of air',
        'category' => 'hvac',
        'subcategory' => 'psychrometrics',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'dry_bulb', 'type' => 'number', 'label' => 'Dry Bulb Temperature', 'unit' => '°F', 'required' => true],
            ['name' => 'relative_humidity', 'type' => 'number', 'label' => 'Relative Humidity', 'unit' => '%', 'required' => true, 'min' => 0, 'max' => 100],
        ],
        'formulas' => [
            'humidity_ratio' => function($inputs) {
                // Simplified calculation
                $rh = $inputs['relative_humidity'] / 100;
                return 0.622 * $rh * exp(17.27 * ($inputs['dry_bulb'] - 32) / 1.8 / (237.3 + ($inputs['dry_bulb'] - 32) / 1.8)) / 14.7;
            },
            'enthalpy' => function($inputs, $results) {
                return 0.24 * $inputs['dry_bulb'] + $results['humidity_ratio'] * (1061 + 0.444 * $inputs['dry_bulb']);
            },
            'dew_point' => function($inputs) {
                $rh = $inputs['relative_humidity'] / 100;
                $tc = ($inputs['dry_bulb'] - 32) / 1.8;
                $a = 17.27;
                $b = 237.3;
                $alpha = (($a * $tc) / ($b + $tc)) + log($rh);
                $tdp = ($b * $alpha) / ($a - $alpha);
                return $tdp * 1.8 + 32; // Convert back to °F
            },
        ],
        'outputs' => [
            ['name' => 'humidity_ratio', 'label' => 'Humidity Ratio', 'unit' => 'lb/lb', 'precision' => 4],
            ['name' => 'enthalpy', 'label' => 'Enthalpy', 'unit' => 'BTU/lb', 'precision' => 2],
            ['name' => 'dew_point', 'label' => 'Dew Point', 'unit' => '°F', 'precision' => 1],
        ],
    ],
    
    'enthalpy' => [
        'name' => 'Enthalpy Calculator',
        'description' => 'Calculate air enthalpy from temperature and humidity',
        'category' => 'hvac',
        'subcategory' => 'psychrometrics',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'dry_bulb', 'type' => 'number', 'label' => 'Dry Bulb Temperature', 'unit' => '°F', 'required' => true],
            ['name' => 'humidity_ratio', 'type' => 'number', 'label' => 'Humidity Ratio', 'unit' => 'lb/lb', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'enthalpy' => function($inputs) {
                return 0.24 * $inputs['dry_bulb'] + $inputs['humidity_ratio'] * (1061 + 0.444 * $inputs['dry_bulb']);
            },
        ],
        'outputs' => [
            ['name' => 'enthalpy', 'label' => 'Enthalpy', 'unit' => 'BTU/lb', 'precision' => 2],
        ],
    ],
    
    'sensible-heat-ratio' => [
        'name' => 'Sensible Heat Ratio Calculator',
        'description' => 'Calculate SHR for cooling coil selection',
        'category' => 'hvac',
        'subcategory' => 'psychrometrics',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'sensible_load', 'type' => 'number', 'label' => 'Sensible Load', 'unit' => 'BTU/hr', 'required' => true, 'min' => 0],
            ['name' => 'latent_load', 'type' => 'number', 'label' => 'Latent Load', 'unit' => 'BTU/hr', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'total_load' => function($inputs) {
                return $inputs['sensible_load'] + $inputs['latent_load'];
            },
            'shr' => function($inputs, $results) {
                return $results['total_load'] > 0 ? $inputs['sensible_load'] / $results['total_load'] : 0;
            },
        ],
        'outputs' => [
            ['name' => 'total_load', 'label' => 'Total Load', 'unit' => 'BTU/hr', 'precision' => 0],
            ['name' => 'shr', 'label' => 'Sensible Heat Ratio', 'unit' => '', 'precision' => 3],
        ],
    ],
    
    'cooling-load-psych' => [
        'name' => 'Psychrometric Cooling Load',
        'description' => 'Calculate cooling load using psychrometric properties',
        'category' => 'hvac',
        'subcategory' => 'psychrometrics',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'airflow', 'type' => 'number', 'label' => 'Airflow', 'unit' => 'CFM', 'required' => true, 'min' => 0],
            ['name' => 'entering_db', 'type' => 'number', 'label' => 'Entering Dry Bulb', 'unit' => '°F', 'required' => true],
            ['name' => 'leaving_db', 'type' => 'number', 'label' => 'Leaving Dry Bulb', 'unit' => '°F', 'required' => true],
        ],
        'formulas' => [
            'sensible_load' => function($inputs) {
                return 1.08 * $inputs['airflow'] * ($inputs['entering_db'] - $inputs['leaving_db']);
            },
            'tons' => function($inputs, $results) {
                return $results['sensible_load'] / 12000;
            },
        ],
        'outputs' => [
            ['name' => 'sensible_load', 'label' => 'Sensible Cooling Load', 'unit' => 'BTU/hr', 'precision' => 0],
            ['name' => 'tons', 'label' => 'Cooling Capacity', 'unit' => 'Tons', 'precision' => 2],
        ],
    ],
    
    // ============================================
    // ENERGY ANALYSIS (4 calculators)
    // ============================================
    
    'energy-consumption' => [
        'name' => 'Energy Consumption Calculator',
        'description' => 'Calculate HVAC energy consumption and costs',
        'category' => 'hvac',
        'subcategory' => 'energy-analysis',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'capacity', 'type' => 'number', 'label' => 'System Capacity', 'unit' => 'Tons', 'required' => true, 'min' => 0],
            ['name' => 'seer', 'type' => 'number', 'label' => 'SEER Rating', 'unit' => '', 'required' => true, 'min' => 0, 'default' => 14],
            ['name' => 'hours', 'type' => 'number', 'label' => 'Annual Operating Hours', 'unit' => 'hrs', 'required' => true, 'min' => 0, 'default' => 2000],
            ['name' => 'cost_per_kwh', 'type' => 'number', 'label' => 'Electricity Cost', 'unit' => '$/kWh', 'required' => true, 'min' => 0, 'default' => 0.12],
        ],
        'formulas' => [
            'annual_kwh' => function($inputs) {
                return ($inputs['capacity'] * 12000) / $inputs['seer'] * $inputs['hours'] / 1000;
            },
            'annual_cost' => function($inputs, $results) {
                return $results['annual_kwh'] * $inputs['cost_per_kwh'];
            },
            'monthly_cost' => function($inputs, $results) {
                return $results['annual_cost'] / 12;
            },
        ],
        'outputs' => [
            ['name' => 'annual_kwh', 'label' => 'Annual Energy Use', 'unit' => 'kWh', 'precision' => 0],
            ['name' => 'annual_cost', 'label' => 'Annual Cost', 'unit' => '$', 'precision' => 2],
            ['name' => 'monthly_cost', 'label' => 'Monthly Cost', 'unit' => '$', 'precision' => 2],
        ],
    ],
    
    'co2-emissions' => [
        'name' => 'CO2 Emissions Calculator',
        'description' => 'Calculate carbon emissions from HVAC systems',
        'category' => 'hvac',
        'subcategory' => 'energy-analysis',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'annual_kwh', 'type' => 'number', 'label' => 'Annual Energy Use', 'unit' => 'kWh', 'required' => true, 'min' => 0],
            ['name' => 'emission_factor', 'type' => 'number', 'label' => 'CO2 Emission Factor', 'unit' => 'kg/kWh', 'required' => true, 'min' => 0, 'default' => 0.5],
        ],
        'formulas' => [
            'annual_co2' => function($inputs) {
                return $inputs['annual_kwh'] * $inputs['emission_factor'];
            },
            'annual_co2_tons' => function($inputs, $results) {
                return $results['annual_co2'] / 1000;
            },
        ],
        'outputs' => [
            ['name' => 'annual_co2', 'label' => 'Annual CO2 Emissions', 'unit' => 'kg', 'precision' => 0],
            ['name' => 'annual_co2_tons', 'label' => 'Annual CO2 Emissions', 'unit' => 'metric tons', 'precision' => 2],
        ],
    ],
    
    'insulation-savings' => [
        'name' => 'Insulation Savings Calculator',
        'description' => 'Calculate energy savings from improved insulation',
        'category' => 'hvac',
        'subcategory' => 'energy-analysis',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'area', 'type' => 'number', 'label' => 'Insulated Area', 'unit' => 'ft²', 'required' => true, 'min' => 0],
            ['name' => 'old_r_value', 'type' => 'number', 'label' => 'Old R-Value', 'unit' => '', 'required' => true, 'min' => 0],
            ['name' => 'new_r_value', 'type' => 'number', 'label' => 'New R-Value', 'unit' => '', 'required' => true, 'min' => 0],
            ['name' => 'temp_diff', 'type' => 'number', 'label' => 'Temperature Difference', 'unit' => '°F', 'required' => true, 'min' => 0],
            ['name' => 'hours', 'type' => 'number', 'label' => 'Annual Heating/Cooling Hours', 'unit' => 'hrs', 'required' => true, 'min' => 0, 'default' => 2000],
        ],
        'formulas' => [
            'old_heat_loss' => function($inputs) {
                return $inputs['area'] * $inputs['temp_diff'] / $inputs['old_r_value'];
            },
            'new_heat_loss' => function($inputs) {
                return $inputs['area'] * $inputs['temp_diff'] / $inputs['new_r_value'];
            },
            'annual_savings_btuh' => function($inputs, $results) {
                return ($results['old_heat_loss'] - $results['new_heat_loss']) * $inputs['hours'];
            },
            'annual_savings_kwh' => function($inputs, $results) {
                return $results['annual_savings_btuh'] * 0.000293; // BTU to kWh
            },
        ],
        'outputs' => [
            ['name' => 'old_heat_loss', 'label' => 'Old Heat Loss', 'unit' => 'BTU/hr', 'precision' => 0],
            ['name' => 'new_heat_loss', 'label' => 'New Heat Loss', 'unit' => 'BTU/hr', 'precision' => 0],
            ['name' => 'annual_savings_btuh', 'label' => 'Annual Energy Savings', 'unit' => 'BTU', 'precision' => 0],
            ['name' => 'annual_savings_kwh', 'label' => 'Annual Energy Savings', 'unit' => 'kWh', 'precision' => 0],
        ],
    ],
    
    'payback-period' => [
        'name' => 'Payback Period Calculator',
        'description' => 'Calculate payback period for HVAC upgrades',
        'category' => 'hvac',
        'subcategory' => 'energy-analysis',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'initial_cost', 'type' => 'number', 'label' => 'Initial Cost', 'unit' => '$', 'required' => true, 'min' => 0],
            ['name' => 'annual_savings', 'type' => 'number', 'label' => 'Annual Energy Savings', 'unit' => '$', 'required' => true, 'min' => 0],
            ['name' => 'maintenance_savings', 'type' => 'number', 'label' => 'Annual Maintenance Savings', 'unit' => '$', 'required' => true, 'min' => 0, 'default' => 0],
        ],
        'formulas' => [
            'total_annual_savings' => function($inputs) {
                return $inputs['annual_savings'] + $inputs['maintenance_savings'];
            },
            'simple_payback' => function($inputs, $results) {
                return $results['total_annual_savings'] > 0 ? $inputs['initial_cost'] / $results['total_annual_savings'] : 0;
            },
            'roi' => function($inputs, $results) {
                return $inputs['initial_cost'] > 0 ? ($results['total_annual_savings'] / $inputs['initial_cost']) * 100 : 0;
            },
        ],
        'outputs' => [
            ['name' => 'total_annual_savings', 'label' => 'Total Annual Savings', 'unit' => '$', 'precision' => 2],
            ['name' => 'simple_payback', 'label' => 'Simple Payback Period', 'unit' => 'years', 'precision' => 2],
            ['name' => 'roi', 'label' => 'Return on Investment', 'unit' => '%', 'precision' => 1],
        ],
    ],
];
