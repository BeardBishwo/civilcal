<?php

return [
    'water-pipe-sizing' => [
        'name' => 'Water Pipe Sizing Calculator',
        'description' => 'Calculate theoretical and recommended pipe diameter based on flow rate and target velocity.',
        'category' => 'plumbing',
        'subcategory' => 'pipe_sizing',
        'inputs' => [
            [
                'name' => 'flowRate',
                'label' => 'Flow Rate',
                'type' => 'number',
                'unit' => 'L/s',
                'default' => 2,
                'step' => 0.1,
                'min' => 0,
                'required' => true,
                'help_text' => 'Enter the expected flow rate in Liters per second.'
            ],
            [
                'name' => 'velocity',
                'label' => 'Target Velocity',
                'type' => 'number',
                'unit' => 'm/s',
                'default' => 1.5,
                'step' => 0.1,
                'min' => 0,
                'required' => true,
                'help_text' => 'Enter the desired water velocity (typically 1.0 - 2.0 m/s).'
            ],
            [
                'name' => 'pipeMaterial',
                'label' => 'Pipe Material',
                'type' => 'select',
                'options' => ['Copper', 'PVC', 'Steel', 'PEX'],
                'default' => 'PVC',
                'required' => true
            ]
        ],
        'outputs' => [
            [
                'name' => 'theoretical_diameter',
                'label' => 'Theoretical Diameter',
                'unit' => 'mm',
                'type' => 'number'
            ],
            [
                'name' => 'recommended_size',
                'label' => 'Recommended Standard Size',
                'unit' => 'mm',
                'type' => 'number'
            ],
            [
                'name' => 'actual_velocity',
                'label' => 'Actual Velocity',
                'unit' => 'm/s',
                'type' => 'number'
            ],
            [
                'name' => 'velocity_check',
                'label' => 'Velocity Check',
                'type' => 'string'
            ]
        ],
        'formulas' => [
            'raw_calculation' => function($inputs) {
                $flowRate = (float)$inputs['flowRate']; // L/s
                $velocity = (float)$inputs['velocity']; // m/s
                
                if ($velocity <= 0) $velocity = 1.5;

                // 1. Convert Flow to m3/s
                $flowM3s = $flowRate / 1000;
                
                // 2. Calculate Area (A = Q / v)
                $area = $flowM3s / $velocity;
                
                // 3. Calculate Diameter (D = 2 * sqrt(A / pi)) -> Result in meters
                $diameterM = 2 * sqrt($area / pi());
                $diameterMm = $diameterM * 1000;
                
                // 4. Find Standard Size (Simple lookup)
                $standardSizes = [15, 20, 25, 32, 40, 50, 65, 80, 100, 125, 150];
                $recommended = 100; // Default fallback
                foreach ($standardSizes as $size) {
                    if ($size >= $diameterMm) {
                        $recommended = $size;
                        break;
                    }
                }
                if ($diameterMm > max($standardSizes)) $recommended = '> 150';

                // 5. Calculate Actual Velocity
                $actualArea = pi() * pow(($recommended / 2000), 2);
                $actualVel = $flowM3s / $actualArea;

                // 6. Limits Check
                $check = 'OK';
                if ($actualVel > 2.5) $check = 'High (> 2.5 m/s)';
                if ($actualVel < 0.5) $check = 'Low (< 0.5 m/s)';

                return [
                    'theoretical_diameter' => round($diameterMm, 2),
                    'recommended_size' => $recommended,
                    'actual_velocity' => round($actualVel, 2),
                    'velocity_check' => $check
                ];
            }
        ]
    ],
    'expansion-loop-sizing' => [
        'name' => 'Expansion Loop Sizing',
        'description' => 'Calculate thermal expansion and required loop dimensions for piping systems.',
        'category' => 'plumbing',
        'subcategory' => 'pipe_sizing',
        'inputs' => [
            ['name' => 'pipeLength', 'label' => 'Pipe Length', 'type' => 'number', 'unit' => 'm', 'default' => 30],
            ['name' => 'pipeDiameter', 'label' => 'Pipe Diameter', 'type' => 'number', 'unit' => 'mm', 'default' => 50],
            ['name' => 'tempChange', 'label' => 'Temperature Change', 'type' => 'number', 'unit' => '°C', 'default' => 50],
            ['name' => 'pipeMaterial', 'label' => 'Material', 'type' => 'select', 'options' => ['Copper', 'Steel', 'PVC', 'CPVC', 'PEX'], 'default' => 'Copper']
        ],
        'outputs' => [
            ['name' => 'expansion', 'label' => 'Total Expansion', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'loop_width', 'label' => 'Loop Width (W)', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'loop_length', 'label' => 'Loop Length (L)', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'check', 'label' => 'Status', 'type' => 'string']
        ],
        'formulas' => [
            'raw_calculation' => function($inputs) {
                $L = (float)$inputs['pipeLength'];
                $D = (float)$inputs['pipeDiameter'];
                $dT = (float)$inputs['tempChange'];
                $mat = strtolower($inputs['pipeMaterial']);

                $coeffs = [
                    'copper' => 0.0167, 'steel' => 0.0117, 
                    'pvc' => 0.07, 'cpvc' => 0.066, 'pex' => 0.18
                ];
                $alpha = $coeffs[$mat] ?? 0.0167;

                $expansion = $L * $alpha * $dT;
                $loopWidth = 2 * sqrt($expansion * $D);
                $loopLength = 4 * sqrt($expansion * $D);

                $check = 'OK';
                if ($expansion > 100) $check = 'High Expansion - Consider Joints';

                return [
                    'expansion' => round($expansion, 2),
                    'loop_width' => round($loopWidth, 0),
                    'loop_length' => round($loopLength, 0),
                    'check' => $check
                ];
            }
        ]
    ],
    'gas-pipe-sizing' => [
        'name' => 'Gas Pipe Sizing',
        'description' => 'Estimate gas pipe diameter using the Darcy-Weisbach equation.',
        'category' => 'plumbing',
        'subcategory' => 'pipe_sizing',
        'inputs' => [
            ['name' => 'gasType', 'label' => 'Gas Type', 'type' => 'select', 'options' => ['Natural Gas', 'Propane'], 'default' => 'Natural Gas'],
            ['name' => 'gasFlow', 'label' => 'Flow Rate', 'type' => 'number', 'unit' => 'm³/hr', 'default' => 5],
            ['name' => 'pipeLength', 'label' => 'Pipe Length', 'type' => 'number', 'unit' => 'm', 'default' => 20],
            ['name' => 'pressureDrop', 'label' => 'Allowable Drop', 'type' => 'number', 'unit' => 'Pa', 'default' => 100]
        ],
        'outputs' => [
            ['name' => 'theoretical_diameter', 'label' => 'Theoretical Diameter', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'recommended_size', 'label' => 'Recommended Size', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'string']
        ],
        'formulas' => [
            'raw_calculation' => function($inputs) {
                $gas = strtolower(str_replace(' ', '', $inputs['gasType'])); // naturalgas or propane
                $Q_m3h = (float)$inputs['gasFlow'];
                $L = (float)$inputs['pipeLength'];
                $dP = (float)$inputs['pressureDrop'];

                // Viscosity (Pa.s)
                $viscosity = ($gas === 'propane') ? 0.8e-5 : 1.1e-5;

                $Q_m3s = $Q_m3h / 3600;
                
                // Darcy-Weisbach derived for laminar/smooth approximation in this context
                // D = [ (128 * mu * L * Q) / (pi * dP) ] ^ 0.25
                if ($dP <= 0) $dP = 100;
                
                $term = (128 * $viscosity * $L * $Q_m3s) / (pi() * $dP);
                $D_m = pow($term, 0.25);
                $D_mm = $D_m * 1000;

                $standardSizes = [15, 20, 25, 32, 40, 50, 65, 80];
                $recommended = 80;
                foreach ($standardSizes as $s) {
                    if ($s >= $D_mm) {
                        $recommended = $s; 
                        break;
                    }
                }
                if ($D_mm > max($standardSizes)) $recommended = '> 80';

                return [
                    'theoretical_diameter' => round($D_mm, 2),
                    'recommended_size' => $recommended,
                    'status' => ($dP > 250) ? 'Check Drop Limit' : 'OK'
                ];
            }
        ]
    ],
    'pipe-flow-capacity' => [
        'name' => 'Pipe Flow Capacity',
        'description' => 'Calculate partial full flow or full bore flow capacity.',
        'category' => 'plumbing',
        'subcategory' => 'pipe_sizing',
        'inputs' => [
            ['name' => 'pipeDiameter', 'label' => 'Diameter', 'type' => 'number', 'unit' => 'mm', 'default' => 50],
            ['name' => 'velocity', 'label' => 'Flow Velocity', 'type' => 'number', 'unit' => 'm/s', 'default' => 1.5],
            ['name' => 'pipeMaterial', 'label' => 'Material', 'type' => 'select', 'options' => ['Copper', 'PVC', 'Steel', 'PEX'], 'default' => 'PVC']
        ],
        'outputs' => [
            ['name' => 'flow_lps', 'label' => 'Flow Rate', 'unit' => 'L/s', 'type' => 'number'],
            ['name' => 'flow_m3h', 'label' => 'Flow Rate', 'unit' => 'm³/hr', 'type' => 'number'],
            ['name' => 'velocity_check', 'label' => 'Velocity Check', 'type' => 'string']
        ],
        'formulas' => [
            'raw_calculation' => function($inputs) {
                $d_mm = (float)$inputs['pipeDiameter'];
                $v = (float)$inputs['velocity'];
                $mat = strtolower($inputs['pipeMaterial']);

                $area = pi() * pow(($d_mm / 2000), 2);
                $Q_m3s = $area * $v;
                
                $limits = ['copper' => 2.4, 'pvc' => 2.0, 'steel' => 2.4, 'pex' => 2.0];
                $maxV = $limits[$mat] ?? 2.0;
                
                $check = 'OK';
                if ($v > $maxV) $check = "Exceeds max ($maxV m/s)";
                
                return [
                    'flow_lps' => round($Q_m3s * 1000, 2),
                    'flow_m3h' => round($Q_m3s * 3600, 2),
                    'velocity_check' => $check
                ];
            }
        ]
    ],
    'fixture-unit-calculation' => [
        'name' => 'Fixture Unit Calculator',
        'description' => 'Calculate Total Fixture Units (FU) and required drain size based on IPC standards.',
        'category' => 'plumbing',
        'subcategory' => 'fixtures',
        'inputs' => [
            [
                'name' => 'fixture_counts',
                'label' => 'Fixture Counts',
                'type' => 'section_start'
            ],
            ['name' => 'wc_count', 'label' => 'Water Closets (4 FU)', 'type' => 'number', 'min' => 0, 'step' => 1, 'default' => 0],
            ['name' => 'urinal_count', 'label' => 'Urinals (2 FU)', 'type' => 'number', 'min' => 0, 'step' => 1, 'default' => 0],
            ['name' => 'lavatory_count', 'label' => 'Lavatories (1 FU)', 'type' => 'number', 'min' => 0, 'step' => 1, 'default' => 0],
            ['name' => 'shower_count', 'label' => 'Showers (2 FU)', 'type' => 'number', 'min' => 0, 'step' => 1, 'default' => 0],
            ['name' => 'bathtub_count', 'label' => 'Bathtubs (2 FU)', 'type' => 'number', 'min' => 0, 'step' => 1, 'default' => 0],
            ['name' => 'kitchen_sink_count', 'label' => 'Kitchen Sinks (2 FU)', 'type' => 'number', 'min' => 0, 'step' => 1, 'default' => 0],
            ['name' => 'laundry_tub_count', 'label' => 'Laundry Tubs (2 FU)', 'type' => 'number', 'min' => 0, 'step' => 1, 'default' => 0],
            ['name' => 'dishwasher_count', 'label' => 'Dishwashers (1.5 FU)', 'type' => 'number', 'min' => 0, 'step' => 1, 'default' => 0],
            ['name' => 'washing_machine_count', 'label' => 'Washing Machines (1.5 FU)', 'type' => 'number', 'min' => 0, 'step' => 1, 'default' => 0],
            ['name' => 'floor_drain_count', 'label' => 'Floor Drains (1 FU)', 'type' => 'number', 'min' => 0, 'step' => 1, 'default' => 0],
            [
                'name' => 'counts_end',
                'type' => 'section_end'
            ],
            [
                'name' => 'building_type',
                'label' => 'Building Type',
                'type' => 'select',
                'options' => [
                    'residential' => 'Residential',
                    'commercial' => 'Commercial',
                    'industrial' => 'Industrial'
                ],
                'default' => 'residential'
            ],
            [
                'name' => 'slope',
                'label' => 'Pipe Slope',
                'type' => 'select',
                'options' => [
                    '0.01' => '1% (1:100)',
                    '0.02' => '2% (1:50)',
                    '0.025' => '2.5% (1:40)',
                    '0.033' => '3.3% (1:30)'
                ],
                'default' => '0.02'
            ]
        ],
        'outputs' => [
            ['name' => 'total_fu', 'label' => 'Total Fixture Units', 'unit' => 'FU', 'type' => 'number'],
            ['name' => 'drain_size', 'label' => 'Required Drain Size', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'imperial_size', 'label' => 'Imperial Equivalent', 'unit' => 'in', 'type' => 'number'],
            ['name' => 'recommendation', 'label' => 'Recommendation', 'type' => 'text']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $values = [
                    'wc' => 4, 'urinal' => 2, 'lavatory' => 1, 'shower' => 2,
                    'bathtub' => 2, 'kitchen_sink' => 2, 'laundry_tub' => 2,
                    'dishwasher' => 1.5, 'washing_machine' => 1.5, 'floor_drain' => 1
                ];
                
                $total = 0;
                $total += (float)($inputs['wc_count'] ?? 0) * $values['wc'];
                $total += (float)($inputs['urinal_count'] ?? 0) * $values['urinal'];
                $total += (float)($inputs['lavatory_count'] ?? 0) * $values['lavatory'];
                $total += (float)($inputs['shower_count'] ?? 0) * $values['shower'];
                $total += (float)($inputs['bathtub_count'] ?? 0) * $values['bathtub'];
                $total += (float)($inputs['kitchen_sink_count'] ?? 0) * $values['kitchen_sink'];
                $total += (float)($inputs['laundry_tub_count'] ?? 0) * $values['laundry_tub'];
                $total += (float)($inputs['dishwasher_count'] ?? 0) * $values['dishwasher'];
                $total += (float)($inputs['washing_machine_count'] ?? 0) * $values['washing_machine'];
                $total += (float)($inputs['floor_drain_count'] ?? 0) * $values['floor_drain'];
                
                $drainSize = 40;
                if ($total <= 1) $drainSize = 40;
                else if ($total <= 3) $drainSize = 50;
                else if ($total <= 8) $drainSize = 50;
                else if ($total <= 20) $drainSize = 75;
                else if ($total <= 60) $drainSize = 100;
                else if ($total <= 160) $drainSize = 125;
                else if ($total <= 360) $drainSize = 150;
                else $drainSize = 200;
                
                $slope = (float)($inputs['slope'] ?? 0.02);
                if ($slope >= 0.033 && $drainSize > 50) {
                    $drainSize -= 25;
                    if ($drainSize < 50) $drainSize = 50;
                }
                
                $build = $inputs['building_type'] ?? 'residential';
                $rec = 'Standard sizing per IPC Table 710.1(1).';
                if ($build === 'commercial' && $total > 50) {
                    $rec = 'Large system - consider dividing into multiple branches or checking peak flow loads.';
                }
                
                return [
                    'total_fu' => $total,
                    'drain_size' => $drainSize,
                    'imperial_size' => round($drainSize / 25.4, 1),
                    'recommendation' => $rec
                ];
            }
        ]
    ],
    'shower-sizing' => [
        'name' => 'Shower Sizing Calculator',
        'description' => 'Calculate design flow rates and pipe sizes for shower installations.',
        'category' => 'plumbing',
        'subcategory' => 'fixtures',
        'inputs' => [
            [
                'name' => 'shower_type',
                'label' => 'Shower Type',
                'type' => 'select',
                'options' => [
                    'standard' => 'Standard (9 L/min)',
                    'eco' => 'Eco-Friendly (6 L/min)',
                    'deluxe' => 'Deluxe/Rain (12 L/min)',
                    'multi' => 'Multi-Head (15 L/min)',
                    'custom' => 'Custom'
                ],
                'default' => 'standard'
            ],
            ['name' => 'custom_flow', 'label' => 'Custom Flow Rate (L/min)', 'type' => 'number', 'min' => 0.1, 'default' => 9, 'condition' => "inputs.shower_type === 'custom'"],
            ['name' => 'count', 'label' => 'Number of Showers', 'type' => 'number', 'min' => 1, 'default' => 1],
            [
                'name' => 'usage_pattern',
                'label' => 'Usage Pattern',
                'type' => 'select',
                'options' => [
                    'residential' => 'Residential (Low Coincidence)',
                    'gym' => 'Gym/Sports (High Coincidence)',
                    'industrial' => 'Industrial (Shift Changes)',
                    'custom' => 'Custom'
                ],
                'default' => 'residential'
            ],
            [
                'name' => 'material',
                'label' => 'Pipe Material',
                'type' => 'select',
                'options' => [
                    'copper' => 'Copper',
                    'pex' => 'PEX',
                    'cpvc' => 'CPVC'
                ],
                'default' => 'copper'
            ],
            ['name' => 'hot_water', 'label' => 'Include Hot Water?', 'type' => 'boolean', 'default' => true]
        ],
        'outputs' => [
            ['name' => 'design_flow', 'label' => 'Design Flow Rate', 'unit' => 'L/min', 'type' => 'number'],
            ['name' => 'peak_flow', 'label' => 'Peak Flow Rate', 'unit' => 'L/min', 'type' => 'number'],
            ['name' => 'main_size', 'label' => 'Main Pipe Size', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'branch_size', 'label' => 'Branch Pipe Size', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'hot_water_flow', 'label' => 'Hot Water Demand', 'unit' => 'L/min', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $flows = ['standard' => 9, 'eco' => 6, 'deluxe' => 12, 'multi' => 15];
                $type = $inputs['shower_type'] ?? 'standard';
                $flow = ($type === 'custom') ? (float)($inputs['custom_flow'] ?? 9) : ($flows[$type] ?? 9);
                
                $patterns = [
                    'residential' => ['c' => 0.5, 'p' => 2.0],
                    'gym' => ['c' => 0.8, 'p' => 3.0],
                    'industrial' => ['c' => 0.9, 'p' => 4.0],
                    'custom' => ['c' => 0.7, 'p' => 2.5]
                ];
                $usage = $inputs['usage_pattern'] ?? 'residential';
                $pat = $patterns[$usage] ?? $patterns['residential'];
                
                $materials = [
                    'copper' => ['v' => 2.4], 'pex' => ['v' => 2.0], 'cpvc' => ['v' => 2.1]
                ];
                $matKey = $inputs['material'] ?? 'copper';
                $mat = $materials[$matKey] ?? $materials['copper'];
                
                $count = (float)($inputs['count'] ?? 1);
                $baseFlow = $flow * $count;
                $designFlow = $baseFlow * $pat['c'];
                $peakFlow = $designFlow * $pat['p'];
                $hotFlow = !empty($inputs['hot_water']) ? $designFlow * 0.6 : 0;
                
                $q_m3s = $peakFlow / 60000;
                $area = $q_m3s / $mat['v'];
                $dia = 2 * sqrt($area / pi()) * 1000;
                
                $sizes = [15, 20, 25, 32, 40, 50, 65];
                $main = ceil($dia);
                foreach ($sizes as $s) {
                    if ($s >= $dia) { $main = $s; break; }
                }

                $branchDia = $dia * 0.6;
                $branch = 15;
                foreach ($sizes as $s) {
                    if ($s >= $branchDia) { $branch = $s; break; }
                }
                
                return [
                    'design_flow' => round($designFlow, 1),
                    'peak_flow' => round($peakFlow, 1),
                    'main_size' => $main,
                    'branch_size' => $branch,
                    'hot_water_flow' => round($hotFlow, 1)
                ];
            }
        ]
    ],
    'sink-sizing' => [
        'name' => 'Sink Sizing Calculator',
        'description' => 'Calculate pipe size for sinks based on flow rate and velocity.',
        'category' => 'plumbing',
        'subcategory' => 'fixtures',
        'inputs' => [
            [
                'name' => 'sink_type',
                'label' => 'Sink Type',
                'type' => 'select',
                'options' => [
                    'lavatory' => 'Lavatory (6 L/min)',
                    'kitchen' => 'Kitchen (12 L/min)',
                    'service' => 'Service (15 L/min)',
                    'commercial' => 'Commercial (20 L/min)',
                    'custom' => 'Custom'
                ],
                'default' => 'lavatory'
            ],
            ['name' => 'custom_flow', 'label' => 'Custom Flow (L/min)', 'type' => 'number', 'default' => 6, 'condition' => "inputs.sink_type === 'custom'"],
            [
                'name' => 'velocity',
                'label' => 'Design Velocity',
                'type' => 'select',
                'options' => [
                    '0.6' => 'Low (0.6 m/s)',
                    '1.0' => 'Standard (1.0 m/s)',
                    '1.5' => 'High (1.5 m/s)',
                    'custom' => 'Custom'
                ],
                'default' => '1.0'
            ],
            ['name' => 'custom_velocity', 'label' => 'Custom Velocity (m/s)', 'type' => 'number', 'default' => 1.0, 'condition' => "inputs.velocity === 'custom'"],
            [
                'name' => 'material',
                'label' => 'Pipe Material',
                'type' => 'select',
                'options' => [
                    'copper' => 'Copper',
                    'pvc' => 'PVC',
                    'pex' => 'PEX',
                    'steel' => 'Steel'
                ],
                'default' => 'pvc'
            ]
        ],
        'outputs' => [
            ['name' => 'theo_dia', 'label' => 'Theoretical Diameter', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'rec_size', 'label' => 'Recommended Size', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'act_vel', 'label' => 'Actual Velocity', 'unit' => 'm/s', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $types = ['lavatory' => 6, 'kitchen' => 12, 'service' => 15, 'commercial' => 20];
                $type = $inputs['sink_type'] ?? 'lavatory';
                $flow = ($type === 'custom') ? (float)($inputs['custom_flow'] ?? 6) : ($types[$type] ?? 6);
                
                $velInput = $inputs['velocity'] ?? '1.0';
                $vel = ($velInput === 'custom') ? (float)($inputs['custom_velocity'] ?? 1.0) : (float)$velInput;
                
                $q = $flow / 60000;
                $area = $q / ($vel ?: 1);
                $dia = 2 * sqrt($area / pi()) * 1000;
                
                $sizes = [15, 20, 25, 32, 40, 50, 65, 80];
                $rec = ceil($dia);
                foreach ($sizes as $s) {
                    if ($s >= $dia) { $rec = $s; break; }
                }
                
                $actArea = pi() * pow($rec/2000, 2);
                $actVel = $q / ($actArea ?: 1);
                
                return [
                    'theo_dia' => round($dia, 1),
                    'rec_size' => $rec,
                    'act_vel' => round($actVel, 2)
                ];
            }
        ]
    ],
    'toilet-flow' => [
        'name' => 'Toilet Flow & Demand',
        'description' => 'Calculate daily water usage and design flows for toilet fixtures.',
        'category' => 'plumbing',
        'subcategory' => 'fixtures',
        'inputs' => [
            [
                'name' => 'toilet_type',
                'label' => 'Fixture Type',
                'type' => 'select',
                'options' => [
                    'standard' => 'Standard WC (6L)',
                    'dual' => 'Dual Flush (3/4.5L)',
                    'efficient' => 'Efficient (4.8L)',
                    'urinal' => 'Urinal (2.5L)',
                    'custom' => 'Custom'
                ],
                'default' => 'standard'
            ],
            ['name' => 'custom_vol', 'label' => 'Custom Volume (L)', 'type' => 'number', 'default' => 6, 'condition' => "inputs.toilet_type === 'custom'"],
            ['name' => 'flushes', 'label' => 'Flushes per Day', 'type' => 'number', 'min' => 1, 'default' => 5],
            ['name' => 'count', 'label' => 'Number of Fixtures', 'type' => 'number', 'min' => 1, 'default' => 1],
            [
                'name' => 'building_type',
                'label' => 'Building Type',
                'type' => 'select',
                'options' => [
                    'office' => 'Office',
                    'retail' => 'Retail',
                    'restaurant' => 'Restaurant',
                    'school' => 'School',
                    'residential' => 'Residential'
                ],
                'default' => 'residential'
            ]
        ],
        'outputs' => [
            ['name' => 'daily_vol', 'label' => 'Daily Water Usage', 'unit' => 'L/day', 'type' => 'number'],
            ['name' => 'peak_flow', 'label' => 'Peak Flow', 'unit' => 'L/s', 'type' => 'number'],
            ['name' => 'design_flow', 'label' => 'Design Flow (Hunter)', 'unit' => 'L/s', 'type' => 'number'],
            ['name' => 'total_fu', 'label' => 'Total Fixture Units', 'unit' => 'FU', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $types = [
                    'standard' => ['v' => 6.0, 'fu' => 4.0],
                    'dual' => ['v' => 4.5, 'fu' => 3.5],
                    'efficient' => ['v' => 4.8, 'fu' => 3.5],
                    'urinal' => ['v' => 2.5, 'fu' => 2.0]
                ];
                
                $tType = $inputs['toilet_type'] ?? 'standard';
                if ($tType === 'custom') {
                    $vol = (float)($inputs['custom_vol'] ?? 6);
                    $fu = 4.0;
                } else {
                    $vol = $types[$tType]['v'] ?? 6.0;
                    $fu = $types[$tType]['fu'] ?? 4.0;
                }
                
                $count = (float)($inputs['count'] ?? 1);
                $flushes = (float)($inputs['flushes'] ?? 5);
                $daily = $vol * $flushes * $count;
                $totalFU = $fu * $count;
                
                $patterns = [
                    'office' => 2.0, 'retail' => 2.5, 'restaurant' => 3.0,
                    'school' => 4.0, 'residential' => 1.5
                ];
                $bType = $inputs['building_type'] ?? 'residential';
                $peak = $patterns[$bType] ?? 1.5;
                
                $avg = $daily / 86400;
                $peakFlow = $avg * $peak;
                $design = 0.7 * sqrt($totalFU);
                
                return [
                    'daily_vol' => round($daily, 0),
                    'peak_flow' => round($peakFlow, 3),
                    'design_flow' => round($design, 2),
                    'total_fu' => $totalFU
                ];
            }
        ]
    ],
    // DRAINAGE CALCULATORS
    'drainage-pipe-sizing' => [
        'name' => 'Drainage Pipe Sizing',
        'description' => 'Calculate required drainage pipe size based on Fixture Units (DFU) or Flow Rate.',
        'category' => 'plumbing',
        'subcategory' => 'drainage',
        'inputs' => [
            [
                'name' => 'calc_type',
                'label' => 'Calculation Method',
                'type' => 'select',
                'options' => ['dfu' => 'Fixture Units (DFU)', 'flow' => 'Flow Rate'],
                'default' => 'dfu'
            ],
            ['name' => 'dfu', 'label' => 'Total Fixture Units', 'type' => 'number', 'min' => 0, 'step' => 1, 'condition' => "inputs.calc_type === 'dfu'"],
            ['name' => 'flow_rate', 'label' => 'Flow Rate', 'type' => 'number', 'min' => 0, 'step' => 0.1, 'condition' => "inputs.calc_type === 'flow'"],
            ['name' => 'flow_unit', 'label' => 'Flow Unit', 'type' => 'select', 'options' => ['lps' => 'L/s', 'gpm' => 'GPM'], 'default' => 'lps', 'condition' => "inputs.calc_type === 'flow'"],
            [
                'name' => 'slope',
                'label' => 'Pipe Slope (%)',
                'type' => 'select',
                'options' => ['0.5' => '0.5%', '1.0' => '1.0%', '2.0' => '2.0%', '4.0' => '4.0%'],
                'default' => '2.0'
            ],
            [
                'name' => 'material',
                'label' => 'Pipe Material',
                'type' => 'select',
                'options' => ['pvc' => 'PVC', 'cast' => 'Cast Iron'],
                'default' => 'pvc'
            ]
        ],
        'outputs' => [
            ['name' => 'flow_lps', 'label' => 'Design Flow', 'unit' => 'L/s', 'type' => 'number'],
            ['name' => 'min_dia', 'label' => 'Minimum Diameter', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'rec_size', 'label' => 'Recommended Size', 'unit' => 'mm', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $calcType = $inputs['calc_type'] ?? 'dfu';
                $flow = 0;
                
                if ($calcType === 'dfu') {
                    $fu = (float)($inputs['dfu'] ?? 0);
                    $flow = sqrt($fu) * 0.316; // Hunter's curve approx
                } else {
                    $rate = (float)($inputs['flow_rate'] ?? 0);
                    if (($inputs['flow_unit'] ?? 'lps') === 'gpm') $rate *= 0.0631;
                    $flow = $rate;
                }
                
                // Manning
                $mat = $inputs['material'] ?? 'pvc';
                $n = ($mat === 'pvc') ? 0.011 : 0.013;
                $slope = (float)($inputs['slope'] ?? 2.0) / 100;
                if ($slope <= 0) $slope = 0.02;
                
                // D = [(Q*n) / (0.312 * S^0.5)] ^ 0.375
                $term = ($flow * $n) / (0.312 * sqrt($slope));
                $d = pow($term, 0.375);
                $mm = ceil($d * 1000);
                
                $sizes = [32, 40, 50, 65, 75, 100, 125, 150, 200, 250, 300];
                $rec = ceil($mm/10)*10;
                foreach ($sizes as $s) {
                    if ($s >= $mm) { $rec = $s; break; }
                }
                
                return [
                    'flow_lps' => round($flow, 2),
                    'min_dia' => $mm,
                    'rec_size' => $rec
                ];
            }
        ]
    ],
    'grease-trap-sizing' => [
        'name' => 'Grease Trap Sizing',
        'description' => 'Calculate grease trap capacity based on meals, flow, and kitchen type.',
        'category' => 'plumbing',
        'subcategory' => 'drainage',
        'inputs' => [
            ['name' => 'meals', 'label' => 'Meals per Day', 'type' => 'number', 'min' => 0, 'step' => 1],
            [
                'name' => 'type',
                'label' => 'Kitchen Type',
                'type' => 'select',
                'options' => [
                    'restaurant' => 'Restaurant (13L/meal)',
                    'cafe' => 'Cafe (9L/meal)',
                    'hospital' => 'Hospital (11L/meal)',
                    'hotel' => 'Hotel (11L/meal)',
                    'takeaway' => 'Takeaway (7L/meal)'
                ],
                'default' => 'restaurant'
            ],
            ['name' => 'hours', 'label' => 'Operating Hours', 'type' => 'number', 'min' => 1, 'max' => 24, 'default' => 12],
            ['name' => 'sinks', 'label' => 'Kitchen Sinks', 'type' => 'number', 'default' => 1],
            ['name' => 'dishwashers', 'label' => 'Dishwashers', 'type' => 'number', 'default' => 0],
            ['name' => 'disposal', 'label' => 'Food Waste Disposal', 'type' => 'boolean', 'default' => false]
        ],
        'outputs' => [
            ['name' => 'peak_flow', 'label' => 'Peak Flow', 'unit' => 'L/min', 'type' => 'number'],
            ['name' => 'capacity', 'label' => 'Required Capacity', 'unit' => 'L', 'type' => 'number'],
            ['name' => 'rec_size', 'label' => 'Recommended Trap Size', 'unit' => 'L', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $factors = [
                    'restaurant' => 13, 'cafe' => 9, 'hospital' => 11, 
                    'hotel' => 11, 'takeaway' => 7
                ];
                $type = $inputs['type'] ?? 'restaurant';
                $factor = $factors[$type] ?? 13;
                
                $sinkFlow = 15;
                $dishFlow = 25;
                
                $sinks = (float)($inputs['sinks'] ?? 1);
                $dish = (float)($inputs['dishwashers'] ?? 0);
                
                $totalFlow = ($sinks * $sinkFlow) + ($dish * $dishFlow);
                $peakFlow = $totalFlow * 1.2;
                
                $meals = (float)($inputs['meals'] ?? 0);
                $cap = $meals * $factor;
                
                if (!empty($inputs['disposal'])) $cap *= 1.3;
                
                $flowCap = $peakFlow * 30;
                $reqCap = max($cap, $flowCap);
                
                $sizes = [500, 1000, 1500, 2000, 3000, 4000, 5000];
                $rec = ceil($reqCap/500)*500;
                foreach ($sizes as $s) {
                    if ($s >= $reqCap) { $rec = $s; break; }
                }
                
                return [
                    'peak_flow' => round($peakFlow, 1),
                    'capacity' => round($reqCap, 0),
                    'rec_size' => $rec
                ];
            }
        ]
    ],
    'soil-stack-sizing' => [
        'name' => 'Soil Stack Sizing',
        'description' => 'Determine soil stack size based on total fixture units and building height.',
        'category' => 'plumbing',
        'subcategory' => 'drainage',
        'inputs' => [
            ['name' => 'floors', 'label' => 'Number of Floors', 'type' => 'number', 'min' => 1, 'max' => 100, 'default' => 1],
            ['name' => 'total_fu', 'label' => 'Total Fixture Units (FU)', 'type' => 'number', 'min' => 1],
            ['name' => 'material', 'label' => 'Stack Material', 'type' => 'select', 'options' => ['pvc' => 'PVC', 'cast' => 'Cast Iron'], 'default' => 'pvc']
        ],
        'outputs' => [
            ['name' => 'stack_size', 'label' => 'Required Stack Size', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'max_fu', 'label' => 'Max FU Capacity', 'unit' => 'FU', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $fu = (float)($inputs['total_fu'] ?? 0);
                $floors = (float)($inputs['floors'] ?? 1);
                
                $size = 100;
                $limit = 0;
                
                if ($floors <= 3) {
                    if ($fu <= 24) { $size = 50; $limit = 24; }
                    else if ($fu <= 240) { $size = 100; $limit = 240; }
                    else if ($fu <= 960) { $size = 150; $limit = 960; }
                    else { $size = 200; $limit = 2200; }
                } else {
                    if ($fu <= 500) { $size = 100; $limit = 500; }
                    else if ($fu <= 1100) { $size = 125; $limit = 1100; }
                    else if ($fu <= 1900) { $size = 150; $limit = 1900; }
                    else { $size = 200; $limit = 3600; }
                }
                
                return [
                    'stack_size' => $size,
                    'max_fu' => $limit
                ];
            }
        ]
    ],
    'trap-sizing' => [
        'name' => 'Trap & Arm Sizing',
        'description' => 'Calculate trap size and maximum trap arm length.',
        'category' => 'plumbing',
        'subcategory' => 'drainage',
        'inputs' => [
            [
                'name' => 'fixture',
                'label' => 'Fixture Type',
                'type' => 'select',
                'options' => [
                    'lavatory' => 'Lavatory (1 DFU)',
                    'sink' => 'Kitchen Sink (2 DFU)',
                    'bathtub' => 'Bathtub (2 DFU)',
                    'shower' => 'Shower (2 DFU)',
                    'toilet' => 'Toilet (4 DFU)',
                    'custom' => 'Custom'
                ],
                'default' => 'lavatory'
            ],
            ['name' => 'custom_dfu', 'label' => 'Custom DFU', 'type' => 'number', 'condition' => "inputs.fixture === 'custom'", 'default' => 1],
            ['name' => 'arm_length', 'label' => 'Proposed Arm Length (m)', 'type' => 'number', 'step' => 0.1, 'default' => 1.0]
        ],
        'outputs' => [
            ['name' => 'trap_size', 'label' => 'Trap Size', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'max_len', 'label' => 'Max Arm Length', 'unit' => 'm', 'type' => 'number'],
            ['name' => 'status', 'label' => 'Compliance', 'type' => 'text']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $dfus = ['lavatory' => 1, 'sink' => 2, 'bathtub' => 2, 'shower' => 2, 'toilet' => 4];
                $fix = $inputs['fixture'] ?? 'lavatory';
                $dfu = ($fix === 'custom') ? (float)($inputs['custom_dfu'] ?? 1) : ($dfus[$fix] ?? 1);
                
                $size = 32; $maxLen = 1.0;
                
                if ($dfu <= 1) { $size = 32; $maxLen = 1.5; }
                else if ($dfu <= 2) { $size = 40; $maxLen = 1.8; }
                else if ($dfu <= 3) { $size = 50; $maxLen = 2.4; }
                else if ($dfu <= 6) { $size = 75; $maxLen = 3.6; }
                else { $size = 100; $maxLen = 4.8; }
                
                $actualLen = (float)($inputs['arm_length'] ?? 0);
                $status = ($actualLen <= $maxLen) ? 'OK' : 'Too Long';
                
                return [
                    'trap_size' => $size,
                    'max_len' => $maxLen,
                    'status' => $status
                ];
            }
        ]
    ],
    'vent-pipe-sizing' => [
        'name' => 'Vent Pipe Sizing',
        'description' => 'Calculate vent pipe size based on drainage fixture units and developed length.',
        'category' => 'plumbing',
        'subcategory' => 'drainage',
        'inputs' => [
            ['name' => 'drain_size', 'label' => 'Drain Size (mm)', 'type' => 'select', 'options' => ['40'=>'40', '50'=>'50', '80'=>'80', '100'=>'100'], 'default' => '50'],
            ['name' => 'dfu', 'label' => 'Fixture Units', 'type' => 'number', 'min' => 1],
            ['name' => 'length', 'label' => 'Developed Length (m)', 'type' => 'number', 'min' => 1],
            ['name' => 'type', 'label' => 'Vent Type', 'type' => 'select', 'options' => ['individual'=>'Individual', 'circuit'=>'Circuit', 'stack'=>'Stack'], 'default' => 'individual']
        ],
        'outputs' => [
            ['name' => 'vent_size', 'label' => 'Required Vent Size', 'unit' => 'mm', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $drain = (float)($inputs['drain_size'] ?? 50);
                $len = (float)($inputs['length'] ?? 0);
                $dfu = (float)($inputs['dfu'] ?? 0);
                
                $size = 32;
                if ($drain >= 100) $size = 50;
                else if ($drain >= 80) $size = 40;
                else $size = 32;
                
                if ($len > 15) $size = max($size, 40);
                if ($len > 30) $size = max($size, 50);
                
                if ($dfu > 40 && $size < 50) $size = 50;
                
                $type = $inputs['type'] ?? 'individual';
                if ($type === 'stack' && $size < 50) $size = 50;
                
                return [ 'vent_size' => $size ];
            }
        ]
    ],

    // STORMWATER CALCULATORS
    'storm-drainage' => [
        'name' => 'Storm Drainage',
        'description' => 'Calculate general storm drainage requirements.',
        'category' => 'plumbing',
        'subcategory' => 'stormwater',
        'inputs' => [
            ['name' => 'area', 'label' => 'Roof Area (m²)', 'type' => 'number'],
            ['name' => 'rainfall', 'label' => 'Rainfall (mm/hr)', 'type' => 'number', 'default' => 100],
            ['name' => 'slope', 'label' => 'Gutter Slope (%)', 'type' => 'number', 'default' => 1.0]
        ],
        'outputs' => [
            ['name' => 'flow', 'label' => 'Runoff Flow', 'unit' => 'L/s', 'type' => 'number'],
            ['name' => 'downpipe', 'label' => 'Downpipe Size', 'unit' => 'mm', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $area = (float)($inputs['area'] ?? 0);
                $rain = (float)($inputs['rainfall'] ?? 0);
                $flow = ($area * $rain * 0.95) / 3600;
                
                $dp = 50;
                if ($flow > 1) $dp = 65;
                if ($flow > 2.5) $dp = 75;
                if ($flow > 4.5) $dp = 90;
                if ($flow > 9) $dp = 100;
                if ($flow > 14) $dp = 150;
                
                return [
                    'flow' => round($flow, 2),
                    'downpipe' => $dp
                ];
            }
        ]
    ],
    'downpipe-sizing' => [
        'name' => 'Downpipe Sizing',
        'description' => 'Detailed downpipe sizing for round and rectangular pipes.',
        'category' => 'plumbing',
        'subcategory' => 'stormwater',
        'inputs' => [
            ['name' => 'area', 'label' => 'Catchment Area (m²)', 'type' => 'number'],
            ['name' => 'rainfall', 'label' => 'Rainfall (mm/hr)', 'type' => 'number', 'default' => 150],
            ['name' => 'count', 'label' => 'Number of Downpipes', 'type' => 'number', 'default' => 1],
            ['name' => 'shape', 'label' => 'Shape', 'type' => 'select', 'options' => ['round'=>'Round', 'rect'=>'Rectangular'], 'default' => 'round']
        ],
        'outputs' => [
            ['name' => 'flow_per_pipe', 'label' => 'Flow per Pipe', 'unit' => 'L/s', 'type' => 'number'],
            ['name' => 'size', 'label' => 'Recommended Size', 'type' => 'text']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $area = (float)($inputs['area'] ?? 0);
                $rain = (float)($inputs['rainfall'] ?? 0);
                $Q = ($area * $rain) / 3600;
                $count = (float)($inputs['count'] ?? 1);
                $qObs = $Q / max($count, 1);
                
                $size = '';
                $shape = $inputs['shape'] ?? 'round';
                
                if ($shape === 'round') {
                    if ($qObs < 1.0) $size = '50mm';
                    else if ($qObs < 2.5) $size = '65mm';
                    else if ($qObs < 4.5) $size = '75mm';
                    else if ($qObs < 8.0) $size = '90mm';
                    else if ($qObs < 13.0) $size = '100mm';
                    else $size = '150mm';
                } else {
                    if ($qObs < 1.5) $size = '65x50mm';
                    else if ($qObs < 3.5) $size = '75x50mm';
                    else if ($qObs < 6.0) $size = '100x50mm';
                    else $size = '100x75mm';
                }
                
                return [
                    'flow_per_pipe' => round($qObs, 2),
                    'size' => $size
                ];
            }
        ]
    ],
    'gutter-sizing' => [
        'name' => 'Gutter Sizing',
        'description' => 'Calculate required gutter cross-section dimensions.',
        'category' => 'plumbing',
        'subcategory' => 'stormwater',
        'inputs' => [
            ['name' => 'area', 'label' => 'Effective Roof Area (m²)', 'type' => 'number'],
            ['name' => 'rainfall', 'label' => 'Rainfall Intensity (mm/hr)', 'type' => 'number', 'default' => 150],
            ['name' => 'type', 'label' => 'Gutter Type', 'type' => 'select', 'options' => ['box'=>'Box', 'quad'=>'Quad', 'round'=>'Half Round'], 'default' => 'quad']
        ],
        'outputs' => [
            ['name' => 'flow', 'label' => 'Design Flow', 'unit' => 'L/s', 'type' => 'number'],
            ['name' => 'size', 'label' => 'Required Size', 'type' => 'text']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $area = (float)($inputs['area'] ?? 0);
                $rain = (float)($inputs['rainfall'] ?? 0);
                $flow = ($area * $rain) / 3600;
                
                $size = 'Standard';
                $type = $inputs['type'] ?? 'quad';
                
                if ($type === 'quad') {
                    if ($flow < 1.5) $size = '115mm';
                    else if ($flow < 2.5) $size = '125mm';
                    else $size = '150mm';
                } else {
                    $areaReq = $flow / 0.012;
                    $size = 'Area: ' . ceil($areaReq) . ' mm²';
                }
                
                return [
                    'flow' => round($flow, 2),
                    'size' => $size
                ];
            }
        ]
    ],
    'pervious-area' => [
        'name' => 'Pervious Area Calculator',
        'description' => 'Calculate site permeability ratios.',
        'category' => 'plumbing',
        'subcategory' => 'stormwater',
        'inputs' => [
            ['name' => 'site_area', 'label' => 'Total Site Area (m²)', 'type' => 'number'],
            ['name' => 'hardscape', 'label' => 'Impervious Area (Roof, Concrete)', 'type' => 'number'],
            ['name' => 'softscape', 'label' => 'Pervious Area (Grass, Garden)', 'type' => 'number']
        ],
        'outputs' => [
            ['name' => 'ratio', 'label' => 'Pervious Ratio', 'unit' => '%', 'type' => 'number'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'text']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $total = (float)($inputs['site_area'] ?? 1);
                $soft = (float)($inputs['softscape'] ?? 0);
                
                if ($total <= 0) $total = 1;
                $ratio = ($soft / $total) * 100;
                
                $status = 'Check Local Code';
                if ($ratio >= 20) $status = 'Good (>20%)';
                else if ($ratio < 15) $status = 'Low (<15%)';
                
                return [
                    'ratio' => round($ratio, 1),
                    'status' => $status
                ];
            }
        ]
    ],
    'stormwater-storage' => [
        'name' => 'Stormwater Storage',
        'description' => 'Calculate detention and retention tank volumes.',
        'category' => 'plumbing',
        'subcategory' => 'stormwater',
        'inputs' => [
            ['name' => 'catchment', 'label' => 'Catchment Area (m²)', 'type' => 'number'],
            ['name' => 'rainfall', 'label' => 'Design Rainfall (total mm)', 'type' => 'number', 'default' => 50],
            ['name' => 'detention', 'label' => 'Reuse/Retention %', 'type' => 'number', 'min' => 0, 'max' => 100, 'default' => 20]
        ],
        'outputs' => [
            ['name' => 'total_vol', 'label' => 'Total Volume Capture', 'unit' => 'm³', 'type' => 'number'],
            ['name' => 'reuse_vol', 'label' => 'Reuse Volume', 'unit' => 'm³', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $area = (float)($inputs['catchment'] ?? 0);
                $rain = (float)($inputs['rainfall'] ?? 0);
                $percent = (float)($inputs['detention'] ?? 0);
                
                $total = ($area * $rain) / 1000;
                $reuse = $total * ($percent / 100);
                
                return [
                    'total_vol' => round($total, 2),
                    'reuse_vol' => round($reuse, 2)
                ];
            }
        ]
    ],
    'water-demand-calculation' => [
        'name' => 'Water Demand Calculator',
        'description' => 'Calculate daily water demand, peak flows, and storage requirements.',
        'category' => 'plumbing',
        'subcategory' => 'water_supply',
        'inputs' => [
            [
                'name' => 'buildingType',
                'label' => 'Building Type',
                'type' => 'select',
                'options' => [
                    'residential' => 'Residential',
                    'apartment' => 'Apartment Building',
                    'commercial' => 'Commercial Office',
                    'retail' => 'Retail/Shopping',
                    'healthcare' => 'Healthcare Facility',
                    'educational' => 'Educational',
                    'hotel' => 'Hotel/Hospitality',
                    'industrial' => 'Industrial'
                ],
                'default' => 'residential'
            ],
            [
                'name' => 'calcMethod',
                'label' => 'Calculation Method',
                'type' => 'select',
                'options' => [
                    'occupants' => 'By Occupants',
                    'fixtures' => 'By Fixtures',
                    'floor-area' => 'By Floor Area'
                ],
                'default' => 'occupants'
            ],
            ['name' => 'occupants', 'label' => 'Number of Occupants', 'type' => 'number', 'min' => 1, 'condition' => "inputs.calcMethod === 'occupants'"],
            ['name' => 'floorArea', 'label' => 'Floor Area (m²)', 'type' => 'number', 'min' => 1, 'condition' => "inputs.calcMethod === 'floor-area'"],
            
            // Fixture Inputs
            ['name' => 'waterClosets', 'label' => 'Water Closets', 'type' => 'number', 'min' => 0, 'default' => 0, 'condition' => "inputs.calcMethod === 'fixtures'"],
            ['name' => 'lavatories', 'label' => 'Lavatories', 'type' => 'number', 'min' => 0, 'default' => 0, 'condition' => "inputs.calcMethod === 'fixtures'"],
            ['name' => 'showers', 'label' => 'Showers', 'type' => 'number', 'min' => 0, 'default' => 0, 'condition' => "inputs.calcMethod === 'fixtures'"],
            ['name' => 'bathtubs', 'label' => 'Bathtubs', 'type' => 'number', 'min' => 0, 'default' => 0, 'condition' => "inputs.calcMethod === 'fixtures'"],
            ['name' => 'kitchenSinks', 'label' => 'Kitchen Sinks', 'type' => 'number', 'min' => 0, 'default' => 0, 'condition' => "inputs.calcMethod === 'fixtures'"],
            ['name' => 'utilitySinks', 'label' => 'Utility Sinks', 'type' => 'number', 'min' => 0, 'default' => 0, 'condition' => "inputs.calcMethod === 'fixtures'"],

            // Usage Pattern
            ['name' => 'operatingHours', 'label' => 'Operating Hours per Day', 'type' => 'number', 'min' => 1, 'max' => 24, 'default' => 8],
            ['name' => 'operatingDays', 'label' => 'Operating Days per Week', 'type' => 'number', 'min' => 1, 'max' => 7, 'default' => 5],
            [
                'name' => 'peakFactor',
                'label' => 'Peak Factor',
                'type' => 'select',
                'options' => ['1.2'=>'Low (1.2)', '1.5'=>'Normal (1.5)', '2.0'=>'High (2.0)', '2.5'=>'Peak (2.5)'],
                'default' => '1.5'
            ],
            ['name' => 'diversityFactor', 'label' => 'Diversity Factor', 'type' => 'number', 'step' => 0.1, 'default' => 0.5]
        ],
        'outputs' => [
            ['name' => 'totalDemand', 'label' => 'Daily Water Demand', 'unit' => 'L/day', 'type' => 'number'],
            ['name' => 'avgFlowRate', 'label' => 'Average Flow Rate', 'unit' => 'L/s', 'type' => 'number'],
            ['name' => 'peakHourlyDemand', 'label' => 'Peak Hourly Demand', 'unit' => 'L/hr', 'type' => 'number'],
            ['name' => 'peakFlowRate', 'label' => 'Peak Flow Rate', 'unit' => 'L/s', 'type' => 'number'],
            ['name' => 'recommendedStorage', 'label' => 'Recommended Storage', 'unit' => 'm³', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $demandFactors = [
                    'residential' => 150, 'apartment' => 200, 'commercial' => 50, 'retail' => 75,
                    'healthcare' => 300, 'educational' => 75, 'hotel' => 200, 'industrial' => 100
                ];
                $floorAreaFactors = [
                    'residential' => 2.5, 'apartment' => 3.0, 'commercial' => 1.5, 'retail' => 2.0,
                    'healthcare' => 5.0, 'educational' => 2.0, 'hotel' => 3.5, 'industrial' => 2.5
                ];
                $fixtureUnits = [
                    'waterClosets' => 3.5, 'lavatories' => 1.0, 'showers' => 2.0,
                    'bathtubs' => 2.0, 'kitchenSinks' => 1.5, 'utilitySinks' => 1.5
                ];

                $type = $inputs['buildingType'] ?? 'residential';
                $method = $inputs['calcMethod'] ?? 'occupants';
                $totalDemand = 0;

                if ($method === 'occupants') {
                    $occ = (float)($inputs['occupants'] ?? 0);
                    $totalDemand = $occ * ($demandFactors[$type] ?? 150);
                } else if ($method === 'floor-area') {
                    $area = (float)($inputs['floorArea'] ?? 0);
                    $totalDemand = $area * ($floorAreaFactors[$type] ?? 2.0);
                } else if ($method === 'fixtures') {
                    $totalFU = 0;
                    $totalFU += ((float)($inputs['waterClosets'] ?? 0)) * $fixtureUnits['waterClosets'];
                    $totalFU += ((float)($inputs['lavatories'] ?? 0)) * $fixtureUnits['lavatories'];
                    $totalFU += ((float)($inputs['showers'] ?? 0)) * $fixtureUnits['showers'];
                    $totalFU += ((float)($inputs['bathtubs'] ?? 0)) * $fixtureUnits['bathtubs'];
                    $totalFU += ((float)($inputs['kitchenSinks'] ?? 0)) * $fixtureUnits['kitchenSinks'];
                    $totalFU += ((float)($inputs['utilitySinks'] ?? 0)) * $fixtureUnits['utilitySinks'];
                    $totalDemand = $totalFU * 100;
                }

                $peakFactor = (float)($inputs['peakFactor'] ?? 1.5);
                $hours = (float)($inputs['operatingHours'] ?? 8);
                
                $peakHourly = ($totalDemand * $peakFactor) / 24;
                $peakFlow = $peakHourly / 3600;
                $avgFlow = ($hours > 0) ? ($totalDemand / $hours) / 3600 : 0;
                
                $minStorage = max($avgFlow * 2 * 3600, $peakHourly * 0.5);

                return [
                    'totalDemand' => round($totalDemand, 0),
                    'avgFlowRate' => round($avgFlow, 3),
                    'peakHourlyDemand' => round($peakHourly, 0),
                    'peakFlowRate' => round($peakFlow, 3),
                    'recommendedStorage' => round($minStorage / 1000, 1)
                ];
            }
        ]
    ],
    'water-hammer-calculation' => [
        'name' => 'Water Hammer Calculator',
        'description' => 'Calculate pressure surge and water hammer effects.',
        'category' => 'plumbing',
        'subcategory' => 'water_supply',
        'inputs' => [
            ['name' => 'velocity', 'label' => 'Flow Velocity', 'type' => 'number', 'unit' => 'm/s', 'step' => 0.1, 'default' => 1.5, 'help_text' => 'Typical range: 0.5 - 3.0 m/s'],
            ['name' => 'length', 'label' => 'Pipe Length', 'type' => 'number', 'unit' => 'm', 'step' => 1.0, 'default' => 100],
            ['name' => 'closureTime', 'label' => 'Valve Closure Time', 'type' => 'number', 'unit' => 's', 'step' => 0.1, 'default' => 0.5],
            [
                'name' => 'pipeMaterial',
                'label' => 'Pipe Material',
                'type' => 'select',
                'options' => [
                    'steel' => 'Steel',
                    'ductile-iron' => 'Ductile Iron',
                    'pvc' => 'PVC',
                    'hdpe' => 'HDPE',
                    'copper' => 'Copper'
                ],
                'default' => 'ductile-iron'
            ],
            // Advanced Params
            ['name' => 'operatingPressure', 'label' => 'Operating Pressure', 'type' => 'number', 'unit' => 'bar', 'default' => 5],
            ['name' => 'allowableSurge', 'label' => 'Allowable Surge Pressure', 'type' => 'number', 'unit' => 'bar', 'default' => 10]
        ],
        'outputs' => [
            ['name' => 'pressureSurge', 'label' => 'Pressure Surge', 'unit' => 'bar', 'type' => 'number'],
            ['name' => 'criticalTime', 'label' => 'Critical Closure Time', 'unit' => 's', 'type' => 'number'],
            ['name' => 'totalPressure', 'label' => 'Total Pressure', 'unit' => 'bar', 'type' => 'number'],
            ['name' => 'surgeRatio', 'label' => 'Surge Ratio', 'unit' => 'x', 'type' => 'number'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'string'],
            ['name' => 'recommendations', 'label' => 'Recommendations', 'type' => 'string']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $waveSpeeds = [
                    'steel' => 1500, 'ductile-iron' => 1200, 'pvc' => 240, 'hdpe' => 200, 'copper' => 1200
                ];
                
                $vel = (float)($inputs['velocity'] ?? 1.5);
                $len = (float)($inputs['length'] ?? 100);
                $time = (float)($inputs['closureTime'] ?? 0.5);
                $mat = $inputs['pipeMaterial'] ?? 'ductile-iron';
                $opPress = (float)($inputs['operatingPressure'] ?? 5);
                $allowSurge = (float)($inputs['allowableSurge'] ?? 10);
                
                $waveSpeed = $waveSpeeds[$mat] ?? 1200;
                
                $criticalTime = (2 * $len) / $waveSpeed;
                
                $surge = 0;
                $status = 'OK';
                
                if ($time <= $criticalTime) {
                    $surge = (1000 * $vel * $waveSpeed) / 100000;
                    $status = 'Severe Water Hammer';
                } else {
                    $ratio = ($time > 0) ? $criticalTime / $time : 1;
                    $surge = (1000 * $vel * $waveSpeed * $ratio) / 100000;
                    $status = $ratio > 0.5 ? 'Moderate Water Hammer' : 'Low Water Hammer';
                }
                
                $total = $opPress + $surge;
                $surgeRatio = ($allowSurge > 0) ? $surge / $allowSurge : 0;
                
                $rec = '';
                if ($time < $criticalTime) $rec .= 'Increase valve closure time above ' . number_format($criticalTime, 2) . 's. <br>';
                if ($surge > $allowSurge) $rec .= 'Install surge arrestors. ';
                if ($vel > 2) $rec .= 'Reduce flow velocity. ';
                if (!$rec) $rec = 'System operating within parameters.';
                
                return [
                    'pressureSurge' => round($surge, 2),
                    'criticalTime' => round($criticalTime, 3),
                    'totalPressure' => round($total, 2),
                    'surgeRatio' => round($surgeRatio, 2),
                    'status' => $status,
                    'recommendations' => $rec
                ];
            }
        ]
    ],
    'heat-loss-calculation' => [
        'name' => 'Heat Loss Calculator',
        'description' => 'Calculate heat loss from pipes and hot water tanks.',
        'category' => 'plumbing',
        'subcategory' => 'hot_water',
        'inputs' => [
            ['name' => 'waterTemp', 'label' => 'Hot Water Temperature', 'type' => 'number', 'unit' => '°C', 'default' => 60],
            ['name' => 'ambientTemp', 'label' => 'Ambient Temperature', 'type' => 'number', 'unit' => '°C', 'default' => 20],
            
            // Pipe Section
            [
                'name' => 'pipe_section',
                'label' => 'Pipe Heat Loss',
                'type' => 'header'
            ],
            ['name' => 'pipeLength', 'label' => 'Pipe Length', 'type' => 'number', 'unit' => 'm', 'default' => 0],
            ['name' => 'pipeSize', 'label' => 'Pipe Size', 'type' => 'select', 'options' => ['15'=>'15mm', '20'=>'20mm', '25'=>'25mm', '32'=>'32mm', '40'=>'40mm', '50'=>'50mm'], 'default' => '25', 'condition' => 'inputs.pipeLength > 0'],
            [
                'name' => 'insulationType',
                'label' => 'Insulation Type',
                'type' => 'select',
                'options' => ['none'=>'No Insulation', 'foam'=>'Foam', 'mineral'=>'Mineral Wool', 'cellular'=>'Cellular Glass'],
                'default' => 'foam',
                'condition' => 'inputs.pipeLength > 0'
            ],
            ['name' => 'insulationThickness', 'label' => 'Insulation Thickness', 'type' => 'number', 'unit' => 'mm', 'default' => 25, 'condition' => "inputs.pipeLength > 0 && inputs.insulationType !== 'none'"],

            // Tank Section
            [
                'name' => 'tank_section',
                'label' => 'Tank Heat Loss',
                'type' => 'header'
            ],
            ['name' => 'tankVolume', 'label' => 'Tank Volume', 'type' => 'number', 'unit' => 'L', 'default' => 0],
            [
                'name' => 'tankInsulation',
                'label' => 'Tank Insulation',
                'type' => 'select',
                'options' => ['2'=>'Basic (R-2)', '4'=>'Good (R-4)', '6'=>'Better (R-6)', '8'=>'Best (R-8)'],
                'default' => '4',
                'condition' => 'inputs.tankVolume > 0'
            ]
        ],
        'outputs' => [
            ['name' => 'pipeLoss', 'label' => 'Pipe Heat Loss', 'unit' => 'W', 'type' => 'number'],
            ['name' => 'tankLoss', 'label' => 'Tank Heat Loss', 'unit' => 'W', 'type' => 'number'],
            ['name' => 'totalLoss', 'label' => 'Total Heat Loss', 'unit' => 'W', 'type' => 'number'],
            ['name' => 'kwhDay', 'label' => 'Total Energy', 'unit' => 'kWh/day', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $waterTemp = (float)($inputs['waterTemp'] ?? 60);
                $ambientTemp = (float)($inputs['ambientTemp'] ?? 20);
                $tempDiff = $waterTemp - $ambientTemp;
                
                $pipeLoss = 0;
                $pipeLen = (float)($inputs['pipeLength'] ?? 0);
                if ($pipeLen > 0) {
                    $size = (float)($inputs['pipeSize'] ?? 25);
                    $thick = (float)($inputs['insulationThickness'] ?? 0);
                    $ins = $inputs['insulationType'] ?? 'foam';
                    
                    $dia = $size / 1000;
                    $k = 15; // bare
                    if ($ins === 'foam') $k = 0.035;
                    else if ($ins === 'mineral') $k = 0.040;
                    else if ($ins === 'cellular') $k = 0.045;
                    
                    $U = 8; // bare
                    if ($ins !== 'none') {
                        $r2 = $dia/2 + $thick/1000;
                        $U = 2 * pi() * $k / log(2 * $r2 / $dia);
                    }
                    $pipeLoss = $U * $pipeLen * $tempDiff;
                }
                
                $tankLoss = 0;
                $vol = (float)($inputs['tankVolume'] ?? 0);
                if ($vol > 0) {
                    $rVal = (float)($inputs['tankInsulation'] ?? 2);
                    $h = pow($vol/1000, 1/3) * 2;
                    $r = sqrt($vol/(1000 * pi() * $h));
                    $area = 2 * pi() * $r * ($r + $h);
                    $tankLoss = $area * $tempDiff / $rVal;
                }
                
                $total = $pipeLoss + $tankLoss;
                $kwh = ($total * 0.0036 * 24);
                
                return [
                    'pipeLoss' => round($pipeLoss, 1),
                    'tankLoss' => round($tankLoss, 1),
                    'totalLoss' => round($total, 1),
                    'kwhDay' => round($kwh, 2)
                ];
            }
        ]
    ],
    'recirculation-loop' => [
        'name' => 'Recirculation Loop Calculator',
        'description' => 'Calculate recirculation pump flow, head, and pipe sizes.',
        'category' => 'plumbing',
        'subcategory' => 'hot_water',
        'inputs' => [
            ['name' => 'loopLength', 'label' => 'Total Loop Length', 'type' => 'number', 'unit' => 'm', 'min' => 0.1],
            [
                'name' => 'pipeSize',
                'label' => 'Supply Pipe Size',
                'type' => 'select',
                'options' => ['15'=>'15mm (1/2")', '20'=>'20mm (3/4")', '25'=>'25mm (1")', '32'=>'32mm (1-1/4")', '40'=>'40mm (1-1/2")'],
                'default' => '25'
            ],
            [
                'name' => 'returnSize',
                'label' => 'Return Pipe Size',
                'type' => 'select',
                'options' => ['15'=>'15mm (1/2")', '20'=>'20mm (3/4")', '25'=>'25mm (1")'],
                'default' => '20'
            ],
            ['name' => 'targetTemp', 'label' => 'Target Temperature', 'type' => 'number', 'unit' => '°C', 'default' => 60],
            ['name' => 'tempDrop', 'label' => 'Max Temperature Drop', 'type' => 'number', 'unit' => '°C', 'default' => 5],
            ['name' => 'fittings', 'label' => 'Major Fittings Count', 'type' => 'number', 'default' => 10]
        ],
        'outputs' => [
            ['name' => 'minFlow', 'label' => 'Minimum Flow Rate', 'unit' => 'L/min', 'type' => 'number'],
            ['name' => 'designFlow', 'label' => 'Design Flow Rate', 'unit' => 'L/min', 'type' => 'number'],
            ['name' => 'pumpHead', 'label' => 'Pump Head Loss', 'unit' => 'kPa', 'type' => 'number'],
            ['name' => 'pumpPower', 'label' => 'Pump Power', 'unit' => 'W', 'type' => 'number'],
            ['name' => 'warnings', 'label' => 'Warnings', 'type' => 'text']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $len = (float)($inputs['loopLength'] ?? 0);
                $supp = (float)($inputs['pipeSize'] ?? 25);
                $ret = (float)($inputs['returnSize'] ?? 20);
                $temp = (float)($inputs['targetTemp'] ?? 60);
                $drop = (float)($inputs['tempDrop'] ?? 5);
                $fits = (float)($inputs['fittings'] ?? 10);
                
                // Heat Loss (using simple U=2.0 logic from original)
                $U = 2.0;
                $amb = 20;
                $surface = pi() * ($supp/1000) * $len;
                $hLoss = $U * $surface * ($temp - $amb); // Watts
                
                // Flow Rate
                $Cp = 4186; // J/kgC
                $rho = 1000;
                // Avoid division by zero
                $drop = $drop > 0 ? $drop : 5;
                $minFlow = ($hLoss / ($Cp * $rho * $drop)) * 60000; // L/min
                $designFlow = $minFlow * 1.2;
                
                // Velocity Check
                $suppArea = pi() * pow($supp/2000, 2);
                $retArea = pi() * pow($ret/2000, 2);
                $vSupp = ($designFlow/60000) / ($suppArea ?: 1); // Avoid div by zero
                $vRet = ($designFlow/60000) / ($retArea ?: 1);
                
                $warn = '';
                if ($vSupp < 0.3) $warn .= 'Supply velocity too low (<0.3 m/s). ';
                if ($vSupp > 1.5) $warn .= 'Supply velocity too high (>1.5 m/s). ';
                if ($vRet > 1.5) $warn .= 'Return velocity too high (>1.5 m/s). ';
                
                // Head Loss
                $q_m3s = $designFlow / 60000;
                $d_m = $supp / 1000;
                $re = ($vSupp * $d_m) / 1.004e-6; // Reynolds
                // Blasius
                $f = ($re > 0) ? 0.316 * pow($re, -0.25) : 0.02;
                
                $equivLen = ($len * 2) + ($fits * 2); // Loop is double length approx + fittings
                
                $headMeters = ($f * $equivLen * pow($vSupp, 2)) / (2 * $d_m * 9.81);
                $headKpaResult = $headMeters * 9.81;
                
                // Power
                $power = ($headKpaResult * 1000 * $q_m3s) / 0.5; // 50% eff
                
                if (!$warn) $warn = 'OK';
                
                return [
                    'minFlow' => round($minFlow, 1),
                    'designFlow' => round($designFlow, 1),
                    'pumpHead' => round($headKpaResult, 1),
                    'pumpPower' => round($power, 1),
                    'warnings' => $warn
                ];
            }
        ]
    ],
    'safety-valve-calculation' => [
        'name' => 'Safety Valve & Expansion Vessel',
        'description' => 'Calculate pressure relief valve and expansion vessel sizing.',
        'category' => 'plumbing',
        'subcategory' => 'hot_water',
        'inputs' => [
            ['name' => 'heaterCapacity', 'label' => 'Heater Capacity', 'type' => 'number', 'unit' => 'L', 'min' => 1],
            ['name' => 'heaterRating', 'label' => 'Heater Power Rating', 'type' => 'number', 'unit' => 'kW', 'min' => 0.1],
            ['name' => 'maxTemp', 'label' => 'Max Temperature', 'type' => 'number', 'unit' => '°C', 'default' => 65],
            ['name' => 'coldTemp', 'label' => 'Cold Water Temp', 'type' => 'number', 'unit' => '°C', 'default' => 15],
            ['name' => 'systemPressure', 'label' => 'System Working Pressure', 'type' => 'number', 'unit' => 'kPa', 'default' => 350],
            ['name' => 'supplyPressure', 'label' => 'Supply Pressure', 'type' => 'number', 'unit' => 'kPa', 'default' => 500]
        ],
        'outputs' => [
            ['name' => 'valvePressure', 'label' => 'PRV Set Pressure', 'unit' => 'kPa', 'type' => 'number'],
            ['name' => 'valveSize', 'label' => 'PRV Size', 'unit' => 'mm', 'type' => 'number'],
            ['name' => 'vesselVolume', 'label' => 'Vessel Volume', 'unit' => 'L', 'type' => 'number'],
            ['name' => 'preCharge', 'label' => 'Pre-charge Pressure', 'unit' => 'kPa', 'type' => 'number'],
            ['name' => 'pipeDN', 'label' => 'Required Line Size', 'unit' => 'DN', 'type' => 'number'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'text']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $cap = (float)($inputs['heaterCapacity'] ?? 0);
                $kw = (float)($inputs['heaterRating'] ?? 0);
                $maxT = (float)($inputs['maxTemp'] ?? 65);
                $colT = (float)($inputs['coldTemp'] ?? 15);
                $sysP = (float)($inputs['systemPressure'] ?? 350);
                $supP = (float)($inputs['supplyPressure'] ?? 500);
                
                // Expansion
                $hotVol = 1.0434;
                $coldVol = 1.0001;
                $coeff = ($hotVol - $coldVol) * (($maxT - $colT) / 65);
                $expVol = $cap * $coeff;
                
                // Vessel
                $pre = min($sysP * 0.9, $supP * 0.7);
                $maxP = min($supP * 0.9, 1000);
                $factor = ($maxP > 0) ? ($maxP - $pre) / $maxP : 0;
                $vesVol = ($factor > 0) ? $expVol / $factor : 0;
                
                // Valve
                $relief = $kw * 1.5;
                $vSize = 15;
                if ($relief > 25) $vSize = 20;
                if ($relief > 50) $vSize = 25;
                if ($relief > 100) $vSize = 32;
                
                $vSet = min($sysP * 1.1, 1000);
                
                $line = 20;
                if ($relief > 20) $line = 25;
                if ($relief > 40) $line = 32;
                if ($relief > 80) $line = 40;
                
                $stat = 'OK';
                if ($vSet >= 1000) $stat = 'Pressure Limited to 1000kPa';
                if ($supP > 750) $stat = 'High Supply Pressure';
                
                return [
                    'valvePressure' => round($vSet, 0),
                    'valveSize' => $vSize,
                    'vesselVolume' => round($vesVol, 1),
                    'preCharge' => round($pre, 0),
                    'pipeDN' => $line,
                    'status' => $stat
                ];
            }
        ]
    ],
    'cold-water-storage' => [
        'name' => 'Storage Tank Sizing (Cold Water)',
        'description' => 'Calculate required cold water storage tank capacity.',
        'category' => 'plumbing',
        'subcategory' => 'water_supply',
        'inputs' => [
            ['name' => 'dailyDemand', 'label' => 'Daily Water Demand', 'type' => 'number', 'unit' => 'L/day', 'step' => 10, 'min' => 0],
            ['name' => 'storageHours', 'label' => 'Storage Duration', 'type' => 'number', 'unit' => 'hours', 'default' => 8],
            ['name' => 'safetyFactor', 'label' => 'Safety Factor', 'type' => 'number', 'unit' => '%', 'default' => 20],
            ['name' => 'peakFactor', 'label' => 'Peak Factor', 'type' => 'select', 'options' => ['1.0'=>'1.0','1.2'=>'1.2','1.5'=>'1.5','2.0'=>'2.0'], 'default' => '1.2'],
            [
                'name' => 'tankType',
                'label' => 'Tank Type',
                'type' => 'select',
                'options' => [
                    'plastic' => 'Plastic/Polyethylene ($2.0/L)',
                    'fiberglass' => 'Fiberglass ($3.5/L)',
                    'steel' => 'Steel ($4.0/L)',
                    'concrete' => 'Concrete ($3.0/L)',
                    'stainless' => 'Stainless Steel ($8.0/L)'
                ],
                'default' => 'plastic'
            ],
            ['name' => 'tankCount', 'label' => 'Number of Tanks', 'type' => 'number', 'min' => 1, 'default' => 1],
            ['name' => 'emergencyReserve', 'label' => 'Emergency Reserve', 'type' => 'number', 'unit' => 'L', 'default' => 0],
            ['name' => 'deadStorage', 'label' => 'Dead Storage', 'type' => 'number', 'unit' => '%', 'default' => 10]
        ],
        'outputs' => [
            ['name' => 'requiredVolume', 'label' => 'Required Usable Volume', 'unit' => 'L', 'type' => 'number'],
            ['name' => 'volumePerTank', 'label' => 'Volume Per Tank', 'unit' => 'L', 'type' => 'number'],
            ['name' => 'recommendedSize', 'label' => 'Recommended Tank Size', 'unit' => 'L', 'type' => 'number'],
            ['name' => 'dimensions', 'label' => 'Est. Dimensions (DxH)', 'type' => 'text'],
            ['name' => 'estimatedCost', 'label' => 'Estimated Cost', 'unit' => '$', 'type' => 'number']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $demand = (float)($inputs['dailyDemand'] ?? 0);
                $hours = (float)($inputs['storageHours'] ?? 8);
                $safety = (float)($inputs['safetyFactor'] ?? 0);
                $peak = (float)($inputs['peakFactor'] ?? 1.2);
                $count = (int)($inputs['tankCount'] ?? 1);
                if ($count < 1) $count = 1;
                $reserve = (float)($inputs['emergencyReserve'] ?? 0);
                $dead = (float)($inputs['deadStorage'] ?? 0);
                $type = $inputs['tankType'] ?? 'plastic';
                
                $hourly = $demand / 24;
                $base = $hourly * $hours;
                $peakSto = $base * $peak;
                $safeVol = $peakSto * ($safety / 100);
                $total = $peakSto + $safeVol + $reserve;
                
                // Existing logic divides by (1 + dead/100)
                $usable = $total / (1 + $dead / 100);
                
                $perTank = ceil($usable / $count);
                
                $standard = [500, 750, 1000, 1500, 2000, 2500, 3000, 5000, 7500, 10000, 15000, 20000, 25000, 30000, 50000];
                $rec = null;
                foreach ($standard as $s) {
                    if ($s >= $perTank) {
                        $rec = $s;
                        break;
                    }
                }
                if (!$rec) $rec = ceil($perTank/1000)*1000;
                
                $totalRec = $rec * $count; // Unused variable in output, but good for reference
                
                $dia = sqrt(($rec * 4) / (pi() * 3.5)); // H=3.5D assumption
                $h = $dia * 3.5;
                
                $costs = ['plastic' => 2.0, 'fiberglass' => 3.5, 'steel' => 4.0, 'concrete' => 3.0, 'stainless' => 8.0];
                $unitCost = $costs[$type] ?? 2.0;
                $cost = $rec * $unitCost; 
                $totalCost = $cost * $count;
                
                return [
                    'requiredVolume' => round($usable, 0),
                    'volumePerTank' => round($perTank, 0),
                    'recommendedSize' => $rec,
                    'dimensions' => number_format($dia, 2) . 'm x ' . number_format($h, 2) . 'm',
                    'estimatedCost' => round($totalCost, 0)
                ];
            }
        ]
    ],
    'water-heater-sizing' => [
        'name' => 'Water Heater Sizing',
        'description' => 'Calculate required water heater capacity.',
        'category' => 'plumbing',
        'subcategory' => 'hot_water',
        'inputs' => [
            [
                'name' => 'buildingType',
                'label' => 'Building Type',
                'type' => 'select',
                'options' => ['residential'=>'Residential', 'hotel'=>'Hotel/Motel', 'office'=>'Office Building', 'hospital'=>'Hospital', 'restaurant'=>'Restaurant'],
                'default' => 'residential'
            ],
            ['name' => 'occupants', 'label' => 'Number of Occupants', 'type' => 'number', 'min' => 1],
            ['name' => 'peakHours', 'label' => 'Peak Usage Hours', 'type' => 'number', 'default' => 2],
            ['name' => 'tempRise', 'label' => 'Temperature Rise', 'type' => 'number', 'unit' => '°C', 'default' => 45],
            [
                'name' => 'heaterType',
                'label' => 'Heater Type',
                'type' => 'select',
                'options' => ['electric'=>'Electric', 'gas'=>'Gas', 'heatPump'=>'Heat Pump'],
                'default' => 'electric'
            ]
        ],
        'outputs' => [
            ['name' => 'dailyUsage', 'label' => 'Daily Hot Water Demand', 'unit' => 'L/day', 'type' => 'number'],
            ['name' => 'peakHourly', 'label' => 'Peak Hourly Demand', 'unit' => 'L/hr', 'type' => 'number'],
            ['name' => 'requiredKW', 'label' => 'Required Power', 'unit' => 'kW', 'type' => 'number'],
            ['name' => 'storageSize', 'label' => 'Recommended Storage', 'unit' => 'L', 'type' => 'number'],
            ['name' => 'rating', 'label' => 'Additional Rating', 'type' => 'text']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $usages = ['residential'=>60, 'hotel'=>100, 'office'=>15, 'hospital'=>200, 'restaurant'=>40];
                $build = $inputs['buildingType'] ?? 'residential';
                $occ = (float)($inputs['occupants'] ?? 0);
                $daily = $occ * ($usages[$build] ?? 60);
                
                $peakH = (float)($inputs['peakHours'] ?? 2);
                $peakHourly = ($peakH > 0) ? ($daily * 0.3) / $peakH : 0;
                
                $rise = (float)($inputs['tempRise'] ?? 45);
                $baseKW = ($peakHourly * 4.186 * 1 * $rise) / 3600;
                
                $type = $inputs['heaterType'] ?? 'electric';
                $kw = $baseKW;
                $store = $peakHourly * 1.2;
                $extra = '';
                
                if ($type === 'gas') {
                    $btu = $baseKW * 3412.14;
                    $extra = number_format($btu, 0) . ' BTU/hr';
                } else if ($type === 'heatPump') {
                    $kw = ($baseKW / 3.5);
                    $extra = 'COP 3.5';
                }
                
                return [
                    'dailyUsage' => round($daily, 0),
                    'peakHourly' => round($peakHourly, 1),
                    'requiredKW' => round($kw, 2),
                    'storageSize' => round($store, 0),
                    'rating' => $extra
                ];
            }
        ]
    ],
    'hot-water-storage' => [
        'name' => 'Hot Water Storage Tank Sizing',
        'description' => 'Calculate hot water storage capacity based on fixtures and occupancy.',
        'category' => 'plumbing',
        'subcategory' => 'hot_water',
        'inputs' => [
            [
                'name' => 'buildingType',
                'label' => 'Building Type',
                'type' => 'select',
                'options' => ['residential'=>'Residential', 'hotel'=>'Hotel', 'office'=>'Office', 'hospital'=>'Hospital', 'school'=>'School', 'gym'=>'Gym'],
                'default' => 'residential'
            ],
            ['name' => 'occupants', 'label' => 'Number of Occupants', 'type' => 'number', 'min' => 1],
            ['name' => 'peakHours', 'label' => 'Peak Usage Hours', 'type' => 'number', 'default' => 2, 'min' => 0.5],
            ['name' => 'storageTemp', 'label' => 'Storage Temperature', 'type' => 'number', 'unit' => '°C', 'default' => 60],
            ['name' => 'deliveryTemp', 'label' => 'Delivery Temperature', 'type' => 'number', 'unit' => '°C', 'default' => 45],
            
            // Fixture Counts
            [
                'name' => 'fixture_section',
                'label' => 'Fixture Counts',
                'type' => 'header'
            ],
            ['name' => 'numShowers', 'label' => 'Showers (8 L/min)', 'type' => 'number', 'default' => 0],
            ['name' => 'numBaths', 'label' => 'Baths (60 L/use)', 'type' => 'number', 'default' => 0],
            ['name' => 'numBasins', 'label' => 'Basins (3 L/min)', 'type' => 'number', 'default' => 0],
            ['name' => 'numSinks', 'label' => 'Kitchen Sinks (6 L/min)', 'type' => 'number', 'default' => 0],
            ['name' => 'numDishwashers', 'label' => 'Dishwashers (15 L/cycle)', 'type' => 'number', 'default' => 0],
            ['name' => 'numWashers', 'label' => 'Washing Machines (40 L/cycle)', 'type' => 'number', 'default' => 0]
        ],
        'outputs' => [
            ['name' => 'minStorage', 'label' => 'Minimum Storage', 'unit' => 'L', 'type' => 'number'],
            ['name' => 'recommendedStorage', 'label' => 'Recommended Storage', 'unit' => 'L', 'type' => 'number'],
            ['name' => 'recoveryRate', 'label' => 'Recovery Rate', 'unit' => 'L/hr', 'type' => 'number'],
            ['name' => 'heaterPower', 'label' => 'Required Heater Power', 'unit' => 'kW', 'type' => 'number'],
            ['name' => 'dailyEnergy', 'label' => 'Daily Energy', 'unit' => 'kWh', 'type' => 'number'],
            ['name' => 'warnings', 'label' => 'Warnings', 'type' => 'text']
        ],
        'formulas' => [
            'calculate' => function($inputs) {
                $type = $inputs['buildingType'] ?? 'residential';
                $occ = (float)($inputs['occupants'] ?? 0);
                $peakH = (float)($inputs['peakHours'] ?? 2);
                $sTemp = (float)($inputs['storageTemp'] ?? 60);
                $dTemp = (float)($inputs['deliveryTemp'] ?? 45);
                
                $usages = ['residential'=>70, 'hotel'=>110, 'office'=>12, 'hospital'=>175, 'school'=>18, 'gym'=>45];
                $baseDaily = $occ * ($usages[$type] ?? 70);
                
                // Fixtures
                $show = (float)($inputs['numShowers'] ?? 0);
                $bath = (float)($inputs['numBaths'] ?? 0);
                $basin = (float)($inputs['numBasins'] ?? 0);
                $sink = (float)($inputs['numSinks'] ?? 0);
                $dish = (float)($inputs['numDishwashers'] ?? 0);
                $wash = (float)($inputs['numWashers'] ?? 0);
                
                // Flow based (L/min)
                $flowDemand = ($show * 8) + ($basin * 3) + ($sink * 6);
                
                // Vol based (L/use) -> convert to L/min over peak hours
                $volDemand = 0;
                if ($peakH > 0) {
                     $volDemand = (($bath * 60) + ($dish * 15) + ($wash * 40)) / ($peakH * 60);
                }
                
                $peakFixtureDemand = $flowDemand + $volDemand; // L/min
                
                $peakHourDemand = ($baseDaily / 24) * 3; // L/hr
                
                $minS = max(
                    $peakHourDemand * 0.7,
                    $peakFixtureDemand * 30 // 30 mins
                );
                
                $recS = $minS * 1.2;
                
                $recRate = $peakHourDemand * 0.6;
                $specHeat = 4.18;
                $power = ($recRate * $specHeat * ($sTemp - 10)) / 3600;
                $energy = ($baseDaily * $specHeat * ($dTemp - 10)) / 3600;
                
                $warn = '';
                $recTime = ($recRate > 0) ? $recS / $recRate : 0;
                if ($recTime > 2) $warn .= 'Recovery time > 2 hours. ';
                if ($recS > 5000) $warn .= 'Large volume. Consider multiple tanks. ';
                if ($sTemp < 60) $warn .= 'Storage temp < 60°C risk of Legionella. ';
                if (!$warn) $warn = 'OK';
                
                return [
                    'minStorage' => round($minS, 0),
                    'recommendedStorage' => round($recS, 0),
                    'recoveryRate' => round($recRate, 1),
                    'heaterPower' => round($power, 1),
                    'dailyEnergy' => round($energy, 0),
                    'warnings' => $warn
                ];
            }
        ]
    ]
];
