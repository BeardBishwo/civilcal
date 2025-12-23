<?php

/**
 * Electrical Engineering Calculators Configuration
 * 
 * This file defines all electrical engineering calculators:
 * - Load calculations (Ohm's Law, power factor, etc.)
 * - Voltage drop calculations
 * - Wire sizing and ampacity
 * - Conduit sizing
 * - Short circuit calculations
 * 
 * @package App\Config\Calculators
 */

return [
    // ============================================
    // LOAD CALCULATION CALCULATORS
    // ============================================
    
    'ohms-law' => [
        'name' => "Ohm's Law Calculator",
        'description' => "Calculate voltage, current, resistance, and power using V=IR, P=VI",
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        
        'inputs' => [
            [
                'name' => 'voltage',
                'type' => 'number',
                'unit' => 'V',
                'required' => false,
                'label' => 'Voltage (V)',
                'min' => 0,
                'help' => 'Leave blank to calculate'
            ],
            [
                'name' => 'current',
                'type' => 'number',
                'unit' => 'A',
                'required' => false,
                'label' => 'Current (A)',
                'min' => 0,
                'help' => 'Leave blank to calculate'
            ],
            [
                'name' => 'resistance',
                'type' => 'number',
                'unit' => 'Ω',
                'required' => false,
                'label' => 'Resistance (Ω)',
                'min' => 0,
                'help' => 'Leave blank to calculate'
            ]
        ],
        
        'formulas' => [
            'voltage_calculated' => function($context) {
                $v = $context['voltage'] ?? null;
                $i = $context['current'] ?? null;
                $r = $context['resistance'] ?? null;
                
                if ($v !== null && $v !== '') return $v;
                if ($i !== null && $r !== null && $i !== '' && $r !== '') {
                    return $i * $r; // V = I × R
                }
                return null;
            },
            'current_calculated' => function($context) {
                $v = $context['voltage'] ?? null;
                $i = $context['current'] ?? null;
                $r = $context['resistance'] ?? null;
                
                if ($i !== null && $i !== '') return $i;
                if ($v !== null && $r !== null && $v !== '' && $r !== '' && $r > 0) {
                    return $v / $r; // I = V / R
                }
                return null;
            },
            'resistance_calculated' => function($context) {
                $v = $context['voltage'] ?? null;
                $i = $context['current'] ?? null;
                $r = $context['resistance'] ?? null;
                
                if ($r !== null && $r !== '') return $r;
                if ($v !== null && $i !== null && $v !== '' && $i !== '' && $i > 0) {
                    return $v / $i; // R = V / I
                }
                return null;
            },
            'power' => function($context) {
                $v = $context['voltage_calculated'] ?? null;
                $i = $context['current_calculated'] ?? null;
                
                if ($v !== null && $i !== null) {
                    return $v * $i; // P = V × I
                }
                return 0;
            }
        ],
        
        'outputs' => [
            ['name' => 'voltage_calculated', 'unit' => 'V', 'label' => 'Voltage', 'precision' => 2],
            ['name' => 'current_calculated', 'unit' => 'A', 'label' => 'Current', 'precision' => 4],
            ['name' => 'resistance_calculated', 'unit' => 'Ω', 'label' => 'Resistance', 'precision' => 4],
            ['name' => 'power', 'unit' => 'W', 'label' => 'Power', 'precision' => 2]
        ]
    ],
    
    'power-factor' => [
        'name' => 'Power Factor Calculator',
        'description' => 'Calculate power factor, real power, reactive power, and apparent power',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        
        'inputs' => [
            ['name' => 'real_power', 'type' => 'number', 'unit' => 'kW', 'required' => false, 'label' => 'Real Power (kW)', 'min' => 0],
            ['name' => 'reactive_power', 'type' => 'number', 'unit' => 'kVAR', 'required' => false, 'label' => 'Reactive Power (kVAR)', 'min' => 0],
            ['name' => 'apparent_power', 'type' => 'number', 'unit' => 'kVA', 'required' => false, 'label' => 'Apparent Power (kVA)', 'min' => 0],
            ['name' => 'power_factor_input', 'type' => 'number', 'unit' => '', 'required' => false, 'label' => 'Power Factor (0-1)', 'min' => 0, 'max' => 1]
        ],
        
        'formulas' => [
            'apparent_power_calc' => function($context) {
                $p = $context['real_power'] ?? null;
                $q = $context['reactive_power'] ?? null;
                $s = $context['apparent_power'] ?? null;
                
                if ($s) return $s;
                if ($p !== null && $q !== null) {
                    return sqrt(pow($p, 2) + pow($q, 2));
                }
                return null;
            },
            'power_factor' => function($context) {
                $pf = $context['power_factor_input'] ?? null;
                if ($pf) return $pf;
                
                $p = $context['real_power'] ?? null;
                $s = $context['apparent_power_calc'] ?? null;
                
                if ($p !== null && $s !== null && $s > 0) {
                    return $p / $s;
                }
                return null;
            },
            'power_factor_percent' => function($context) {
                $pf = $context['power_factor'] ?? null;
                return $pf ? $pf * 100 : null;
            }
        ],
        
        'outputs' => [
            ['name' => 'real_power', 'unit' => 'kW', 'label' => 'Real Power', 'precision' => 2],
            ['name' => 'reactive_power', 'unit' => 'kVAR', 'label' => 'Reactive Power', 'precision' => 2],
            ['name' => 'apparent_power_calc', 'unit' => 'kVA', 'label' => 'Apparent Power', 'precision' => 2],
            ['name' => 'power_factor', 'unit' => '', 'label' => 'Power Factor', 'precision' => 4],
            ['name' => 'power_factor_percent', 'unit' => '%', 'label' => 'Power Factor %', 'precision' => 1]
        ]
    ],
    
    // ============================================
    // VOLTAGE DROP CALCULATORS
    // ============================================
    
    'single-phase-voltage-drop' => [
        'name' => 'Single Phase Voltage Drop Calculator',
        'description' => 'Calculate voltage drop for single-phase circuits per NEC standards',
        'category' => 'electrical',
        'subcategory' => 'voltage-drop',
        'version' => '1.0',
        
        'inputs' => [
            ['name' => 'current', 'type' => 'number', 'unit' => 'A', 'required' => true, 'label' => 'Current (Amps)', 'min' => 0.1],
            ['name' => 'distance', 'type' => 'number', 'unit' => 'ft', 'required' => true, 'label' => 'Distance (feet)', 'min' => 1],
            [
                'name' => 'wire_size',
                'type' => 'string',
                'required' => true,
                'label' => 'Wire Size (AWG)',
                'options' => ['14', '12', '10', '8', '6', '4', '2', '1', '1/0', '2/0', '3/0', '4/0'],
                'default' => '12'
            ],
            [
                'name' => 'voltage',
                'type' => 'number',
                'unit' => 'V',
                'required' => true,
                'label' => 'System Voltage',
                'options' => [120, 208, 240, 277],
                'default' => 120
            ],
            [
                'name' => 'material',
                'type' => 'string',
                'required' => true,
                'label' => 'Wire Material',
                'options' => ['copper', 'aluminum'],
                'default' => 'copper'
            ],
            [
                'name' => 'power_factor',
                'type' => 'number',
                'unit' => '%',
                'required' => true,
                'label' => 'Power Factor (%)',
                'min' => 50,
                'max' => 100,
                'default' => 100
            ]
        ],
        
        'formulas' => [
            'resistance' => function($context) {
                // Wire resistance lookup table (ohms per 1000 feet) - NEC Chapter 9, Table 8
                $copperResistance = [
                    '14' => 2.525, '12' => 1.588, '10' => 0.9989, '8' => 0.6282,
                    '6' => 0.3951, '4' => 0.2485, '2' => 0.1563, '1' => 0.1239,
                    '1/0' => 0.0983, '2/0' => 0.0779, '3/0' => 0.0618, '4/0' => 0.0490
                ];
                $aluminumResistance = [
                    '14' => 4.106, '12' => 2.525, '10' => 1.588, '8' => 0.9989,
                    '6' => 0.6282, '4' => 0.3951, '2' => 0.2485, '1' => 0.1970,
                    '1/0' => 0.1563, '2/0' => 0.1239, '3/0' => 0.0983, '4/0' => 0.0779
                ];
                
                $material = $context['material'];
                $size = $context['wire_size'];
                
                return $material === 'aluminum' ? ($aluminumResistance[$size] ?? 2.525) : ($copperResistance[$size] ?? 1.588);
            },
            'voltage_drop' => function($context) {
                // Formula: VD = (2 × I × R × D) / 1000
                // Factor of 2 accounts for both conductors (hot and neutral)
                $vd = (2 * $context['current'] * $context['resistance'] * $context['distance']) / 1000;
                
                // Apply power factor correction if not 100%
                if ($context['power_factor'] < 100) {
                    $vd *= ($context['power_factor'] / 100);
                }
                
                return $vd;
            },
            'voltage_drop_percent' => '(voltage_drop / voltage) * 100',
            'voltage_at_load' => 'voltage - voltage_drop',
            'assessment' => function($context) {
                $percent = $context['voltage_drop_percent'] ?? 0;
                // NEC recommends max 3% for branch circuits, 5% total
                if ($percent > 5) return 'Poor (>5% - Exceeds NEC recommendation)';
                if ($percent > 3) return 'Fair (3-5% - Acceptable for feeders)';
                return 'Good (<3% - Within NEC recommendation)';
            }
        ],
        
        'outputs' => [
            ['name' => 'voltage_drop', 'unit' => 'V', 'label' => 'Voltage Drop', 'precision' => 2],
            ['name' => 'voltage_drop_percent', 'unit' => '%', 'label' => 'Voltage Drop %', 'precision' => 2],
            ['name' => 'voltage_at_load', 'unit' => 'V', 'label' => 'Voltage at Load', 'precision' => 1],
            ['name' => 'resistance', 'unit' => 'Ω/1000ft', 'label' => 'Wire Resistance', 'precision' => 4],
            ['name' => 'assessment', 'unit' => '', 'label' => 'NEC Assessment', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    // ============================================
    // WIRE SIZING CALCULATORS
    // ============================================
    
    'wire-ampacity' => [
        'name' => 'Wire Ampacity Calculator',
        'description' => 'Calculate wire ampacity with temperature and conduit corrections per NEC Table 310.16',
        'category' => 'electrical',
        'subcategory' => 'wire-sizing',
        'version' => '1.0',
        
        'inputs' => [
            [
                'name' => 'wire_size',
                'type' => 'string',
                'required' => true,
                'label' => 'Wire Size (AWG)',
                'options' => ['14', '12', '10', '8', '6', '4', '2', '1', '1/0', '2/0', '3/0', '4/0'],
                'default' => '12'
            ],
            [
                'name' => 'insulation_type',
                'type' => 'string',
                'required' => true,
                'label' => 'Insulation Type',
                'options' => ['THHN', 'THW', 'TW', 'XHHW'],
                'default' => 'THHN'
            ],
            [
                'name' => 'wire_material',
                'type' => 'string',
                'required' => true,
                'label' => 'Wire Material',
                'options' => ['copper', 'aluminum'],
                'default' => 'copper'
            ],
            [
                'name' => 'conductor_count',
                'type' => 'integer',
                'required' => true,
                'label' => 'Current-Carrying Conductors',
                'min' => 1,
                'max' => 50,
                'default' => 3
            ],
            [
                'name' => 'ambient_temp',
                'type' => 'number',
                'unit' => '°C',
                'required' => true,
                'label' => 'Ambient Temperature (°C)',
                'min' => 0,
                'max' => 100,
                'default' => 30
            ]
        ],
        
        'formulas' => [
            'base_ampacity' => function($context) {
                // NEC Table 310.16 - Ampacity ratings
                $copperAmpacity = [
                    'THHN' => [
                        '14' => 25, '12' => 30, '10' => 40, '8' => 55, '6' => 75,
                        '4' => 95, '2' => 130, '1' => 150, '1/0' => 170, '2/0' => 195,
                        '3/0' => 225, '4/0' => 260
                    ],
                    'THW' => [
                        '14' => 20, '12' => 25, '10' => 35, '8' => 50, '6' => 65,
                        '4' => 85, '2' => 115, '1' => 130, '1/0' => 150, '2/0' => 175,
                        '3/0' => 200, '4/0' => 230
                    ],
                    'TW' => [
                        '14' => 15, '12' => 20, '10' => 30, '8' => 40, '6' => 55,
                        '4' => 70, '2' => 95, '1' => 110, '1/0' => 125, '2/0' => 145,
                        '3/0' => 165, '4/0' => 190
                    ],
                    'XHHW' => [
                        '14' => 25, '12' => 30, '10' => 40, '8' => 55, '6' => 75,
                        '4' => 95, '2' => 130, '1' => 150, '1/0' => 170, '2/0' => 195,
                        '3/0' => 225, '4/0' => 260
                    ]
                ];
                
                $aluminumAmpacity = [
                    'THHN' => [
                        '12' => 25, '10' => 30, '8' => 40, '6' => 55, '4' => 75,
                        '2' => 95, '1' => 110, '1/0' => 125, '2/0' => 145, '3/0' => 165,
                        '4/0' => 190
                    ],
                    'THW' => [
                        '12' => 20, '10' => 25, '8' => 35, '6' => 50, '4' => 65,
                        '2' => 85, '1' => 100, '1/0' => 115, '2/0' => 135, '3/0' => 155,
                        '4/0' => 180
                    ],
                    'TW' => [
                        '12' => 15, '10' => 25, '8' => 30, '6' => 40, '4' => 55,
                        '2' => 75, '1' => 85, '1/0' => 100, '2/0' => 115, '3/0' => 130,
                        '4/0' => 150
                    ],
                    'XHHW' => [
                        '12' => 25, '10' => 30, '8' => 40, '6' => 55, '4' => 75,
                        '2' => 95, '1' => 110, '1/0' => 125, '2/0' => 145, '3/0' => 165,
                        '4/0' => 190
                    ]
                ];
                
                $material = $context['wire_material'];
                $insulation = $context['insulation_type'];
                $size = $context['wire_size'];
                
                if ($material === 'aluminum') {
                    return $aluminumAmpacity[$insulation][$size] ?? 0;
                } else {
                    return $copperAmpacity[$insulation][$size] ?? 0;
                }
            },
            'temp_correction' => function($context) {
                $temp = floatval($context['ambient_temp']);
                if ($temp <= 30) return 1.0;
                
                // NEC Table 310.15(B)(1) - Temperature correction factors
                $insulation = $context['insulation_type'];
                $tempRounded = round($temp / 5) * 5;
                
                $corrections = [
                    'THHN' => [35 => 0.96, 40 => 0.91, 45 => 0.87, 50 => 0.82, 55 => 0.76, 60 => 0.71],
                    'THW' => [35 => 0.94, 40 => 0.88, 45 => 0.82, 50 => 0.75, 55 => 0.67, 60 => 0.58],
                    'TW' => [35 => 0.91, 40 => 0.82, 45 => 0.71, 50 => 0.58, 55 => 0.41, 60 => 0.0],
                    'XHHW' => [35 => 0.96, 40 => 0.91, 45 => 0.87, 50 => 0.82, 55 => 0.76, 60 => 0.71]
                ];
                
                return $corrections[$insulation][$tempRounded] ?? 1.0;
            },
            'conduit_adjustment' => function($context) {
                // NEC 310.15(C)(1) - Adjustment for number of conductors
                $count = intval($context['conductor_count']);
                
                if ($count <= 3) return 1.0;
                if ($count <= 6) return 0.80;
                if ($count <= 9) return 0.70;
                if ($count <= 20) return 0.50;
                if ($count <= 30) return 0.45;
                if ($count <= 40) return 0.40;
                return 0.35;
            },
            'final_ampacity' => 'base_ampacity * temp_correction * conduit_adjustment',
            'max_ocpd' => function($context) {
                // Round up to nearest standard breaker size
                $ampacity = floatval($context['final_ampacity']);
                $standardSizes = [15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 110, 125, 150, 175, 200, 225, 250, 300, 350, 400];
                
                foreach ($standardSizes as $size) {
                    if ($size >= $ampacity) {
                        return $size;
                    }
                }
                return ceil($ampacity / 50) * 50;
            }
        ],
        
        'outputs' => [
            ['name' => 'base_ampacity', 'unit' => 'A', 'label' => 'Base Ampacity (NEC Table 310.16)', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'temp_correction', 'unit' => '', 'label' => 'Temperature Correction Factor', 'precision' => 2],
            ['name' => 'conduit_adjustment', 'unit' => '', 'label' => 'Conduit Fill Adjustment', 'precision' => 2],
            ['name' => 'final_ampacity', 'unit' => 'A', 'label' => 'Final Ampacity', 'precision' => 1],
            ['name' => 'max_ocpd', 'unit' => 'A', 'label' => 'Max Overcurrent Protection', 'precision' => 0, 'type' => 'integer']
        ]
    ],
    
    'wire-size-by-current' => [
        'name' => 'Wire Size by Current Calculator',
        'description' => 'Determine minimum wire size required for a given current load',
        'category' => 'electrical',
        'subcategory' => 'wire-sizing',
        'version' => '1.0',
        
        'inputs' => [
            ['name' => 'load_current', 'type' => 'number', 'unit' => 'A', 'required' => true, 'label' => 'Load Current (Amps)', 'min' => 0.1],
            [
                'name' => 'wire_material',
                'type' => 'string',
                'required' => true,
                'label' => 'Wire Material',
                'options' => ['copper', 'aluminum'],
                'default' => 'copper'
            ],
            [
                'name' => 'insulation_type',
                'type' => 'string',
                'required' => true,
                'label' => 'Insulation Type',
                'options' => ['THHN', 'THW', 'TW'],
                'default' => 'THHN'
            ],
            ['name' => 'ambient_temp', 'type' => 'number', 'unit' => '°C', 'required' => true, 'label' => 'Ambient Temperature (°C)', 'default' => 30, 'min' => 0, 'max' => 100],
            ['name' => 'conductor_count', 'type' => 'integer', 'required' => true, 'label' => 'Conductors in Conduit', 'default' => 3, 'min' => 1, 'max' => 50]
        ],
        
        'formulas' => [
            'required_ampacity' => function($context) {
                $temp = floatval($context['ambient_temp']);
                $count = intval($context['conductor_count']);
                $current = floatval($context['load_current']);
                
                // Calculate required base ampacity before derating
                $tempFactor = 1.0;
                if ($temp > 30) {
                    $insulation = $context['insulation_type'];
                    $tempRounded = round($temp / 5) * 5;
                    $corrections = [
                        'THHN' => [35 => 0.96, 40 => 0.91, 45 => 0.87, 50 => 0.82, 55 => 0.76, 60 => 0.71],
                        'THW' => [35 => 0.94, 40 => 0.88, 45 => 0.82, 50 => 0.75, 55 => 0.67, 60 => 0.58],
                        'TW' => [35 => 0.91, 40 => 0.82, 45 => 0.71, 50 => 0.58, 55 => 0.41, 60 => 0.0]
                    ];
                    $tempFactor = $corrections[$insulation][$tempRounded] ?? 1.0;
                }
                
                $conduitFactor = 1.0;
                if ($count > 3) {
                    if ($count <= 6) $conduitFactor = 0.80;
                    elseif ($count <= 9) $conduitFactor = 0.70;
                    elseif ($count <= 20) $conduitFactor = 0.50;
                    elseif ($count <= 30) $conduitFactor = 0.45;
                    elseif ($count <= 40) $conduitFactor = 0.40;
                    else $conduitFactor = 0.35;
                }
                
                return $current / ($tempFactor * $conduitFactor);
            },
            'recommended_wire_size' => function($context) {
                $requiredAmp = $context['required_ampacity'];
                $material = $context['wire_material'];
                $insulation = $context['insulation_type'];
                
                // Wire ampacity tables
                $copperAmpacity = [
                    'THHN' => ['14' => 25, '12' => 30, '10' => 40, '8' => 55, '6' => 75, '4' => 95, '2' => 130, '1' => 150, '1/0' => 170, '2/0' => 195, '3/0' => 225, '4/0' => 260],
                    'THW' => ['14' => 20, '12' => 25, '10' => 35, '8' => 50, '6' => 65, '4' => 85, '2' => 115, '1' => 130, '1/0' => 150, '2/0' => 175, '3/0' => 200, '4/0' => 230],
                    'TW' => ['14' => 15, '12' => 20, '10' => 30, '8' => 40, '6' => 55, '4' => 70, '2' => 95, '1' => 110, '1/0' => 125, '2/0' => 145, '3/0' => 165, '4/0' => 190]
                ];
                
                $aluminumAmpacity = [
                    'THHN' => ['12' => 25, '10' => 30, '8' => 40, '6' => 55, '4' => 75, '2' => 95, '1' => 110, '1/0' => 125, '2/0' => 145, '3/0' => 165, '4/0' => 190],
                    'THW' => ['12' => 20, '10' => 25, '8' => 35, '6' => 50, '4' => 65, '2' => 85, '1' => 100, '1/0' => 115, '2/0' => 135, '3/0' => 155, '4/0' => 180],
                    'TW' => ['12' => 15, '10' => 25, '8' => 30, '6' => 40, '4' => 55, '2' => 75, '1' => 85, '1/0' => 100, '2/0' => 115, '3/0' => 130, '4/0' => 150]
                ];
                
                $ampacityTable = $material === 'aluminum' ? $aluminumAmpacity[$insulation] : $copperAmpacity[$insulation];
                
                foreach ($ampacityTable as $size => $ampacity) {
                    if ($ampacity >= $requiredAmp) {
                        return $size . ' AWG';
                    }
                }
                
                return '4/0 AWG or larger';
            }
        ],
        
        'outputs' => [
            ['name' => 'load_current', 'unit' => 'A', 'label' => 'Load Current', 'precision' => 1],
            ['name' => 'required_ampacity', 'unit' => 'A', 'label' => 'Required Base Ampacity', 'precision' => 1],
            ['name' => 'recommended_wire_size', 'unit' => '', 'label' => 'Minimum Wire Size', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    // ============================================
    // ADDITIONAL VOLTAGE DROP CALCULATORS
    // ============================================
    
    'three-phase-voltage-drop' => [
        'name' => 'Three Phase Voltage Drop Calculator',
        'description' => 'Calculate voltage drop for three-phase circuits',
        'category' => 'electrical',
        'subcategory' => 'voltage-drop',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'current', 'type' => 'number', 'unit' => 'A', 'required' => true, 'label' => 'Current (Amps)', 'min' => 0.1],
            ['name' => 'distance', 'type' => 'number', 'unit' => 'ft', 'required' => true, 'label' => 'Distance (feet)', 'min' => 1],
            ['name' => 'wire_size', 'type' => 'string', 'required' => true, 'label' => 'Wire Size (AWG)', 'options' => ['14', '12', '10', '8', '6', '4', '2', '1', '1/0', '2/0', '3/0', '4/0'], 'default' => '12'],
            ['name' => 'voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'System Voltage', 'options' => [208, 240, 480, 600], 'default' => 480],
            ['name' => 'material', 'type' => 'string', 'required' => true, 'label' => 'Wire Material', 'options' => ['copper', 'aluminum'], 'default' => 'copper'],
            ['name' => 'power_factor', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Power Factor (%)', 'min' => 50, 'max' => 100, 'default' => 85]
        ],
        'formulas' => [
            'resistance' => function($context) {
                $copperResistance = ['14' => 2.525, '12' => 1.588, '10' => 0.9989, '8' => 0.6282, '6' => 0.3951, '4' => 0.2485, '2' => 0.1563, '1' => 0.1239, '1/0' => 0.0983, '2/0' => 0.0779, '3/0' => 0.0618, '4/0' => 0.0490];
                $aluminumResistance = ['14' => 4.106, '12' => 2.525, '10' => 1.588, '8' => 0.9989, '6' => 0.6282, '4' => 0.3951, '2' => 0.2485, '1' => 0.1970, '1/0' => 0.1563, '2/0' => 0.1239, '3/0' => 0.0983, '4/0' => 0.0779];
                return $context['material'] === 'aluminum' ? ($aluminumResistance[$context['wire_size']] ?? 2.525) : ($copperResistance[$context['wire_size']] ?? 1.588);
            },
            'voltage_drop' => function($context) {
                // Three-phase formula: VD = (√3 × I × R × D × PF) / 1000
                $vd = (sqrt(3) * $context['current'] * $context['resistance'] * $context['distance'] * ($context['power_factor'] / 100)) / 1000;
                return $vd;
            },
            'voltage_drop_percent' => '(voltage_drop / voltage) * 100',
            'voltage_at_load' => 'voltage - voltage_drop'
        ],
        'outputs' => [
            ['name' => 'voltage_drop', 'unit' => 'V', 'label' => 'Voltage Drop', 'precision' => 2],
            ['name' => 'voltage_drop_percent', 'unit' => '%', 'label' => 'Voltage Drop %', 'precision' => 2],
            ['name' => 'voltage_at_load', 'unit' => 'V', 'label' => 'Voltage at Load', 'precision' => 1]
        ]
    ],
    
    // ============================================
    // ADDITIONAL LOAD CALCULATION CALCULATORS
    // ============================================
    
    'voltage-divider' => [
        'name' => 'Voltage Divider Calculator',
        'description' => 'Calculate output voltage of a resistive voltage divider',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'input_voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'Input Voltage (V)', 'min' => 0],
            ['name' => 'r1', 'type' => 'number', 'unit' => 'Ω', 'required' => true, 'label' => 'Resistor R1 (Ω)', 'min' => 0],
            ['name' => 'r2', 'type' => 'number', 'unit' => 'Ω', 'required' => true, 'label' => 'Resistor R2 (Ω)', 'min' => 0]
        ],
        'formulas' => [
            'output_voltage' => '(r2 / (r1 + r2)) * input_voltage',
            'current' => 'input_voltage / (r1 + r2)',
            'power_r1' => 'current * current * r1',
            'power_r2' => 'current * current * r2'
        ],
        'outputs' => [
            ['name' => 'output_voltage', 'unit' => 'V', 'label' => 'Output Voltage', 'precision' => 2],
            ['name' => 'current', 'unit' => 'A', 'label' => 'Current', 'precision' => 4],
            ['name' => 'power_r1', 'unit' => 'W', 'label' => 'Power R1', 'precision' => 2],
            ['name' => 'power_r2', 'unit' => 'W', 'label' => 'Power R2', 'precision' => 2]
        ]
    ],
    
    'demand-load-calculation' => [
        'name' => 'Demand Load Calculator',
        'description' => 'Calculate demand load with diversity factors',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'connected_load', 'type' => 'number', 'unit' => 'kW', 'required' => true, 'label' => 'Connected Load (kW)', 'min' => 0],
            ['name' => 'demand_factor', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Demand Factor (%)', 'min' => 0, 'max' => 100, 'default' => 70]
        ],
        'formulas' => [
            'demand_load' => '(connected_load * demand_factor) / 100',
            'current_120v' => '(demand_load * 1000) / 120',
            'current_240v' => '(demand_load * 1000) / 240'
        ],
        'outputs' => [
            ['name' => 'demand_load', 'unit' => 'kW', 'label' => 'Demand Load', 'precision' => 2],
            ['name' => 'current_120v', 'unit' => 'A', 'label' => 'Current @ 120V', 'precision' => 1],
            ['name' => 'current_240v', 'unit' => 'A', 'label' => 'Current @ 240V', 'precision' => 1]
        ]
    ],
    
    // ============================================
    // ADDITIONAL VOLTAGE DROP CALCULATORS
    // ============================================
    
    'voltage-drop-sizing' => [
        'name' => 'Voltage Drop Wire Sizing Calculator',
        'description' => 'Calculate minimum wire size based on voltage drop limits using circular mils',
        'category' => 'electrical',
        'subcategory' => 'voltage-drop',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'current', 'type' => 'number', 'unit' => 'A', 'required' => true, 'label' => 'Load Current (A)', 'min' => 0.1],
            ['name' => 'distance', 'type' => 'number', 'unit' => 'ft', 'required' => true, 'label' => 'Distance (feet)', 'min' => 1],
            ['name' => 'voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'System Voltage (V)', 'default' => 120, 'min' => 1],
            ['name' => 'max_drop_percent', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Max Voltage Drop (%)', 'default' => 3.0, 'min' => 0.1, 'max' => 10],
            ['name' => 'phase', 'type' => 'string', 'required' => true, 'label' => 'Phase', 'options' => ['1', '3'], 'default' => '1'],
            ['name' => 'material', 'type' => 'string', 'required' => true, 'label' => 'Conductor Material', 'options' => ['copper', 'aluminum'], 'default' => 'copper']
        ],
        'formulas' => [
            'max_voltage_drop' => 'voltage * (max_drop_percent / 100)',
            'k_factor' => function($context) {
                return $context['material'] === 'copper' ? 12.9 : 21.2;
            },
            'required_cm' => function($context) {
                $kFactor = $context['k_factor'];
                $current = $context['current'];
                $distance = $context['distance'];
                $maxVoltageDrop = $context['max_voltage_drop'];
                
                if ($context['phase'] === '1') {
                    return (2 * $kFactor * $current * $distance) / $maxVoltageDrop;
                } else {
                    return (1.732 * $kFactor * $current * $distance) / $maxVoltageDrop;
                }
            },
            'recommended_size' => function($context) {
                $requiredCM = $context['required_cm'];
                $wireSizes = [
                    '14' => 4107, '12' => 6530, '10' => 10380, '8' => 16510, '6' => 26240,
                    '4' => 41740, '2' => 66360, '1' => 83690, '1/0' => 105600, '2/0' => 133100,
                    '3/0' => 167800, '4/0' => 211600, '250' => 250000, '300' => 300000,
                    '350' => 350000, '400' => 400000, '500' => 500000, '600' => 600000
                ];
                
                foreach ($wireSizes as $size => $cm) {
                    if ($cm >= $requiredCM) {
                        return $size . ' AWG';
                    }
                }
                return '600 kcmil or larger';
            },
            'actual_cm' => function($context) {
                $size = str_replace(' AWG', '', $context['recommended_size']);
                $wireSizes = [
                    '14' => 4107, '12' => 6530, '10' => 10380, '8' => 16510, '6' => 26240,
                    '4' => 41740, '2' => 66360, '1' => 83690, '1/0' => 105600, '2/0' => 133100,
                    '3/0' => 167800, '4/0' => 211600, '250' => 250000, '300' => 300000,
                    '350' => 350000, '400' => 400000, '500' => 500000, '600' => 600000
                ];
                return $wireSizes[$size] ?? 600000;
            },
            'actual_voltage_drop' => function($context) {
                $actualK = $context['k_factor'] * ($context['required_cm'] / $context['actual_cm']);
                $current = $context['current'];
                $distance = $context['distance'];
                
                if ($context['phase'] === '1') {
                    return (2 * $actualK * $current * $distance) / 1000;
                } else {
                    return (1.732 * $actualK * $current * $distance) / 1000;
                }
            },
            'actual_drop_percent' => '(actual_voltage_drop / voltage) * 100',
            'assessment' => function($context) {
                $actualPercent = $context['actual_drop_percent'];
                $maxPercent = $context['max_drop_percent'];
                
                if ($actualPercent > $maxPercent) return 'Exceeds Limit';
                if ($actualPercent > $maxPercent * 0.8) return 'Good';
                return 'Excellent';
            }
        ],
        'outputs' => [
            ['name' => 'required_cm', 'unit' => 'CM', 'label' => 'Required Circular Mils', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'recommended_size', 'unit' => '', 'label' => 'Recommended Wire Size', 'precision' => 0, 'type' => 'string'],
            ['name' => 'max_voltage_drop', 'unit' => 'V', 'label' => 'Max Voltage Drop', 'precision' => 2],
            ['name' => 'actual_voltage_drop', 'unit' => 'V', 'label' => 'Actual Voltage Drop', 'precision' => 2],
            ['name' => 'actual_drop_percent', 'unit' => '%', 'label' => 'Actual Drop %', 'precision' => 2],
            ['name' => 'assessment', 'unit' => '', 'label' => 'Assessment', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'voltage-regulation' => [
        'name' => 'Voltage Regulation Calculator',
        'description' => 'Calculate voltage regulation percentage between source and load',
        'category' => 'electrical',
        'subcategory' => 'voltage-drop',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'source_voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'Source Voltage (V)', 'min' => 0.1],
            ['name' => 'load_voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'Load Voltage (V)', 'min' => 0.1],
            ['name' => 'voltage_type', 'type' => 'string', 'required' => true, 'label' => 'Voltage Type', 'options' => ['line-to-line', 'line-to-neutral'], 'default' => 'line-to-line'],
            ['name' => 'load_type', 'type' => 'string', 'required' => true, 'label' => 'Load Type', 'options' => ['resistive', 'inductive', 'capacitive', 'mixed'], 'default' => 'resistive']
        ],
        'formulas' => [
            'regulation_percent' => '((source_voltage - load_voltage) / load_voltage) * 100',
            'regulation_absolute' => 'source_voltage - load_voltage',
            'load_regulation_percent' => '((source_voltage - load_voltage) / load_voltage) * 100',
            'assessment' => function($context) {
                $absRegulation = abs($context['regulation_percent']);
                if ($absRegulation <= 2) return 'Excellent';
                if ($absRegulation <= 5) return 'Good';
                if ($absRegulation <= 10) return 'Fair';
                return 'Poor';
            },
            'typical_range' => function($context) {
                $ranges = [
                    'resistive' => '1-3%',
                    'inductive' => '2-8%',
                    'capacitive' => '0.5-5%',
                    'mixed' => '2-6%'
                ];
                return $ranges[$context['load_type']] ?? '2-6%';
            }
        ],
        'outputs' => [
            ['name' => 'regulation_percent', 'unit' => '%', 'label' => 'Regulation (%)', 'precision' => 2],
            ['name' => 'regulation_absolute', 'unit' => 'V', 'label' => 'Voltage Drop', 'precision' => 2],
            ['name' => 'load_regulation_percent', 'unit' => '%', 'label' => 'Load Regulation', 'precision' => 2],
            ['name' => 'assessment', 'unit' => '', 'label' => 'Assessment', 'precision' => 0, 'type' => 'string'],
            ['name' => 'typical_range', 'unit' => '', 'label' => 'Typical Range', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'generic-voltage-drop' => [
        'name' => 'Generic Voltage Drop Calculator',
        'description' => 'Simple voltage drop calculation using resistance',
        'category' => 'electrical',
        'subcategory' => 'voltage-drop',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'current', 'type' => 'number', 'unit' => 'A', 'required' => true, 'label' => 'Current (A)', 'min' => 0],
            ['name' => 'length', 'type' => 'number', 'unit' => 'm', 'required' => true, 'label' => 'Length (meters)', 'min' => 0.1],
            ['name' => 'resistance', 'type' => 'number', 'unit' => 'Ω/m', 'required' => true, 'label' => 'Resistance (Ω/meter)', 'min' => 0]
        ],
        'formulas' => [
            'voltage_drop' => '(2 * length * current * resistance) / 1000',
            'formula_text' => function($context) {
                return 'VD = 2 × L × I × R / 1000';
            }
        ],
        'outputs' => [
            ['name' => 'voltage_drop', 'unit' => 'V', 'label' => 'Voltage Drop', 'precision' => 4],
            ['name' => 'formula_text', 'unit' => '', 'label' => 'Formula', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    // ============================================
    // ADDITIONAL WIRE SIZING CALCULATORS
    // ============================================
    
    'motor-circuit-wire-sizing' => [
        'name' => 'Motor Circuit Wire Sizing Calculator',
        'description' => 'Calculate wire size for motor circuits per NEC Article 430',
        'category' => 'electrical',
        'subcategory' => 'wire-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'horsepower', 'type' => 'number', 'unit' => 'HP', 'required' => true, 'label' => 'Motor Horsepower (HP)', 'min' => 0.25],
            ['name' => 'voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'Voltage (V)', 'options' => [208, 230, 460, 575], 'default' => 460],
            ['name' => 'phase', 'type' => 'string', 'required' => true, 'label' => 'Phase', 'options' => ['1', '3'], 'default' => '3']
        ],
        'formulas' => [
            'fla' => function($context) {
                // Simplified FLA lookup - NEC Table 430.250
                $hp = $context['horsepower'];
                $voltage = $context['voltage'];
                $phase = $context['phase'];
                
                if ($phase === '3' && $voltage == 460) {
                    $flaTable = [0.25 => 0.4, 0.33 => 0.5, 0.5 => 0.6, 0.75 => 0.9, 1 => 1.1, 1.5 => 1.6, 2 => 2.1, 3 => 3.0, 5 => 4.8, 7.5 => 6.8, 10 => 9.0, 15 => 13, 20 => 17, 25 => 21, 30 => 26, 40 => 34, 50 => 42, 60 => 52, 75 => 65, 100 => 84, 125 => 106, 150 => 125];
                    return $flaTable[$hp] ?? ($hp * 1.0);
                }
                return $hp * 1.25; // Simplified fallback
            },
            'conductor_ampacity' => 'fla * 1.25',
            'recommended_size' => function($context) {
                $ampacity = $context['conductor_ampacity'];
                if ($ampacity <= 20) return '12 AWG';
                if ($ampacity <= 25) return '10 AWG';
                if ($ampacity <= 35) return '8 AWG';
                if ($ampacity <= 50) return '6 AWG';
                if ($ampacity <= 65) return '4 AWG';
                if ($ampacity <= 85) return '2 AWG';
                if ($ampacity <= 115) return '1 AWG';
                if ($ampacity <= 130) return '1/0 AWG';
                if ($ampacity <= 150) return '2/0 AWG';
                if ($ampacity <= 175) return '3/0 AWG';
                return '4/0 AWG or larger';
            }
        ],
        'outputs' => [
            ['name' => 'fla', 'unit' => 'A', 'label' => 'Full Load Amps (FLA)', 'precision' => 1],
            ['name' => 'conductor_ampacity', 'unit' => 'A', 'label' => 'Required Conductor Ampacity (125% FLA)', 'precision' => 1],
            ['name' => 'recommended_size', 'unit' => '', 'label' => 'Minimum Wire Size', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'motor-circuit-wiring' => [
        'name' => 'Motor Circuit Wiring Calculator',
        'description' => 'Complete motor circuit design including OCPD and disconnect',
        'category' => 'electrical',
        'subcategory' => 'wire-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'horsepower', 'type' => 'number', 'unit' => 'HP', 'required' => true, 'label' => 'Motor Horsepower (HP)', 'min' => 0.25],
            ['name' => 'voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'Voltage (V)', 'default' => 460],
            ['name' => 'service_factor', 'type' => 'number', 'required' => true, 'label' => 'Service Factor', 'default' => 1.15, 'min' => 1.0, 'max' => 1.25]
        ],
        'formulas' => [
            'fla' => 'horsepower * 1.25',
            'ocpd_size' => 'fla * 2.5',
            'disconnect_size' => 'fla * 1.15',
            'wire_size' => function($context) {
                $ampacity = $context['fla'] * 1.25;
                if ($ampacity <= 20) return '12 AWG';
                if ($ampacity <= 30) return '10 AWG';
                return '8 AWG or larger';
            }
        ],
        'outputs' => [
            ['name' => 'fla', 'unit' => 'A', 'label' => 'Full Load Amps', 'precision' => 1],
            ['name' => 'ocpd_size', 'unit' => 'A', 'label' => 'OCPD Size (250% FLA)', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'disconnect_size', 'unit' => 'A', 'label' => 'Disconnect Size (115% FLA)', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'wire_size', 'unit' => '', 'label' => 'Wire Size', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'transformer-kva-sizing' => [
        'name' => 'Transformer KVA Sizing Calculator',
        'description' => 'Calculate transformer capacity based on load',
        'category' => 'electrical',
        'subcategory' => 'wire-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'load_kw', 'type' => 'number', 'unit' => 'kW', 'required' => true, 'label' => 'Load (kW)', 'min' => 0.1],
            ['name' => 'power_factor', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Power Factor (%)', 'default' => 85, 'min' => 50, 'max' => 100],
            ['name' => 'safety_factor', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Safety Factor (%)', 'default' => 125, 'min' => 100, 'max' => 200]
        ],
        'formulas' => [
            'load_kva' => '(load_kw / (power_factor / 100))',
            'required_kva' => '(load_kva * safety_factor) / 100',
            'recommended_size' => function($context) {
                $kva = $context['required_kva'];
                $standardSizes = [15, 30, 45, 75, 112.5, 150, 225, 300, 500, 750, 1000, 1500, 2000, 2500];
                foreach ($standardSizes as $size) {
                    if ($size >= $kva) return $size . ' kVA';
                }
                return round($kva, 0) . ' kVA';
            }
        ],
        'outputs' => [
            ['name' => 'load_kva', 'unit' => 'kVA', 'label' => 'Load (kVA)', 'precision' => 2],
            ['name' => 'required_kva', 'unit' => 'kVA', 'label' => 'Required Capacity', 'precision' => 2],
            ['name' => 'recommended_size', 'unit' => '', 'label' => 'Recommended Transformer', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    // ============================================
    // ADDITIONAL LOAD CALCULATION CALCULATORS
    // ============================================
    
    'arc-flash-boundary' => [
        'name' => 'Arc Flash Boundary Calculator',
        'description' => 'Calculate arc flash protection boundary per NFPA 70E',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'fault_current', 'type' => 'number', 'unit' => 'kA', 'required' => true, 'label' => 'Bolted Fault Current (kA)', 'min' => 0.1],
            ['name' => 'clearing_time', 'type' => 'number', 'unit' => 'sec', 'required' => true, 'label' => 'Clearing Time (seconds)', 'min' => 0.01],
            ['name' => 'working_distance', 'type' => 'number', 'unit' => 'in', 'required' => true, 'label' => 'Working Distance (inches)', 'default' => 18, 'min' => 1]
        ],
        'formulas' => [
            'incident_energy' => '(4.184 * fault_current * clearing_time * 1000) / (working_distance * working_distance)',
            'arc_flash_boundary' => 'sqrt((4.184 * fault_current * clearing_time * 1000) / 5) * 25.4',
            'ppe_category' => function($context) {
                $energy = $context['incident_energy'];
                if ($energy < 1.2) return 'Category 0';
                if ($energy < 4) return 'Category 1';
                if ($energy < 8) return 'Category 2';
                if ($energy < 25) return 'Category 3';
                return 'Category 4';
            }
        ],
        'outputs' => [
            ['name' => 'incident_energy', 'unit' => 'cal/cm²', 'label' => 'Incident Energy', 'precision' => 2],
            ['name' => 'arc_flash_boundary', 'unit' => 'mm', 'label' => 'Arc Flash Boundary', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'ppe_category', 'unit' => '', 'label' => 'PPE Category', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'battery-load-bank-sizing' => [
        'name' => 'Battery Load Bank Sizing Calculator',
        'description' => 'Size battery banks for backup power',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'load_watts', 'type' => 'number', 'unit' => 'W', 'required' => true, 'label' => 'Load (Watts)', 'min' => 1],
            ['name' => 'backup_hours', 'type' => 'number', 'unit' => 'hrs', 'required' => true, 'label' => 'Backup Time (hours)', 'min' => 0.5],
            ['name' => 'battery_voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'Battery Voltage (V)', 'options' => [12, 24, 48], 'default' => 12],
            ['name' => 'depth_of_discharge', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Depth of Discharge (%)', 'default' => 50, 'min' => 20, 'max' => 80]
        ],
        'formulas' => [
            'total_watt_hours' => 'load_watts * backup_hours',
            'amp_hours_required' => '(total_watt_hours / battery_voltage) / (depth_of_discharge / 100)',
            'recommended_capacity' => function($context) {
                $ah = $context['amp_hours_required'];
                $standardSizes = [35, 50, 75, 100, 150, 200, 250, 300];
                foreach ($standardSizes as $size) {
                    if ($size >= $ah) return $size . ' Ah';
                }
                return round($ah, 0) . ' Ah';
            }
        ],
        'outputs' => [
            ['name' => 'total_watt_hours', 'unit' => 'Wh', 'label' => 'Total Energy Required', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'amp_hours_required', 'unit' => 'Ah', 'label' => 'Battery Capacity Required', 'precision' => 1],
            ['name' => 'recommended_capacity', 'unit' => '', 'label' => 'Recommended Battery', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'feeder-sizing' => [
        'name' => 'Feeder Sizing Calculator',
        'description' => 'Calculate feeder ampacity and wire size per NEC Article 215',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'continuous_load', 'type' => 'number', 'unit' => 'A', 'required' => true, 'label' => 'Continuous Load (A)', 'min' => 0],
            ['name' => 'non_continuous_load', 'type' => 'number', 'unit' => 'A', 'required' => true, 'label' => 'Non-Continuous Load (A)', 'min' => 0]
        ],
        'formulas' => [
            'feeder_ampacity' => '(continuous_load * 1.25) + non_continuous_load',
            'recommended_ocpd' => function($context) {
                $ampacity = $context['feeder_ampacity'];
                $standardSizes = [15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 110, 125, 150, 175, 200, 225, 250, 300, 350, 400];
                foreach ($standardSizes as $size) {
                    if ($size >= $ampacity) return $size . ' A';
                }
                return round($ampacity, 0) . ' A';
            },
            'wire_size' => function($context) {
                $ampacity = $context['feeder_ampacity'];
                if ($ampacity <= 20) return '12 AWG';
                if ($ampacity <= 25) return '10 AWG';
                if ($ampacity <= 35) return '8 AWG';
                if ($ampacity <= 50) return '6 AWG';
                if ($ampacity <= 65) return '4 AWG';
                if ($ampacity <= 85) return '2 AWG';
                if ($ampacity <= 115) return '1 AWG';
                if ($ampacity <= 130) return '1/0 AWG';
                if ($ampacity <= 150) return '2/0 AWG';
                if ($ampacity <= 175) return '3/0 AWG';
                if ($ampacity <= 200) return '4/0 AWG';
                return '250 kcmil or larger';
            }
        ],
        'outputs' => [
            ['name' => 'feeder_ampacity', 'unit' => 'A', 'label' => 'Required Feeder Ampacity', 'precision' => 1],
            ['name' => 'recommended_ocpd', 'unit' => '', 'label' => 'OCPD Size', 'precision' => 0, 'type' => 'string'],
            ['name' => 'wire_size', 'unit' => '', 'label' => 'Minimum Wire Size', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'general-lighting-load' => [
        'name' => 'General Lighting Load Calculator',
        'description' => 'Calculate lighting loads per NEC Article 220',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'area', 'type' => 'number', 'unit' => 'sq ft', 'required' => true, 'label' => 'Floor Area (sq ft)', 'min' => 1],
            ['name' => 'occupancy_type', 'type' => 'string', 'required' => true, 'label' => 'Occupancy Type', 'options' => ['dwelling', 'office', 'warehouse', 'retail'], 'default' => 'office']
        ],
        'formulas' => [
            'unit_load' => function($context) {
                $loads = ['dwelling' => 3, 'office' => 3.5, 'warehouse' => 0.25, 'retail' => 3];
                return $loads[$context['occupancy_type']] ?? 3;
            },
            'total_load_va' => 'area * unit_load',
            'total_load_kw' => 'total_load_va / 1000',
            'current_120v' => 'total_load_va / 120',
            'current_277v' => 'total_load_va / 277'
        ],
        'outputs' => [
            ['name' => 'unit_load', 'unit' => 'VA/sq ft', 'label' => 'Unit Load', 'precision' => 2],
            ['name' => 'total_load_va', 'unit' => 'VA', 'label' => 'Total Load', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'total_load_kw', 'unit' => 'kW', 'label' => 'Total Load (kW)', 'precision' => 2],
            ['name' => 'current_120v', 'unit' => 'A', 'label' => 'Current @ 120V', 'precision' => 1],
            ['name' => 'current_277v', 'unit' => 'A', 'label' => 'Current @ 277V', 'precision' => 1]
        ]
    ],
    
    'motor-full-load-amps' => [
        'name' => 'Motor Full Load Amps Calculator',
        'description' => 'Lookup motor FLA from NEC tables',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'horsepower', 'type' => 'number', 'unit' => 'HP', 'required' => true, 'label' => 'Horsepower (HP)', 'min' => 0.25],
            ['name' => 'voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'Voltage (V)', 'options' => [115, 230, 460, 575], 'default' => 460],
            ['name' => 'phase', 'type' => 'string', 'required' => true, 'label' => 'Phase', 'options' => ['1', '3'], 'default' => '3']
        ],
        'formulas' => [
            'fla' => function($context) {
                $hp = $context['horsepower'];
                // Simplified FLA calculation
                if ($context['phase'] === '3' && $context['voltage'] == 460) {
                    return $hp * 1.0;
                }
                return $hp * 1.5;
            },
            'conductor_size' => 'fla * 1.25',
            'ocpd_size' => 'fla * 2.5'
        ],
        'outputs' => [
            ['name' => 'fla', 'unit' => 'A', 'label' => 'Full Load Amps', 'precision' => 1],
            ['name' => 'conductor_size', 'unit' => 'A', 'label' => 'Conductor Ampacity (125% FLA)', 'precision' => 1],
            ['name' => 'ocpd_size', 'unit' => 'A', 'label' => 'Max OCPD (250% FLA)', 'precision' => 0, 'type' => 'integer']
        ]
    ],
    
    'ocpd-sizing' => [
        'name' => 'OCPD Sizing Calculator',
        'description' => 'Size overcurrent protection devices per NEC',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'load_current', 'type' => 'number', 'unit' => 'A', 'required' => true, 'label' => 'Load Current (A)', 'min' => 0.1],
            ['name' => 'continuous', 'type' => 'string', 'required' => true, 'label' => 'Load Type', 'options' => ['continuous', 'non-continuous'], 'default' => 'continuous']
        ],
        'formulas' => [
            'required_ocpd' => function($context) {
                return $context['continuous'] === 'continuous' ? $context['load_current'] * 1.25 : $context['load_current'];
            },
            'recommended_size' => function($context) {
                $ampacity = $context['required_ocpd'];
                $standardSizes = [15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 110, 125, 150, 175, 200, 225, 250, 300, 350, 400];
                foreach ($standardSizes as $size) {
                    if ($size >= $ampacity) return $size . ' A';
                }
                return round($ampacity, 0) . ' A';
            }
        ],
        'outputs' => [
            ['name' => 'required_ocpd', 'unit' => 'A', 'label' => 'Required OCPD Rating', 'precision' => 1],
            ['name' => 'recommended_size', 'unit' => '', 'label' => 'Standard OCPD Size', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'panel-schedule' => [
        'name' => 'Panel Schedule Calculator',
        'description' => 'Generate panel load calculations',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'total_breakers', 'type' => 'integer', 'required' => true, 'label' => 'Number of Breakers', 'min' => 1, 'max' => 42],
            ['name' => 'avg_load_per_breaker', 'type' => 'number', 'unit' => 'A', 'required' => true, 'label' => 'Average Load per Breaker (A)', 'min' => 0.1],
            ['name' => 'demand_factor', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Demand Factor (%)', 'default' => 75, 'min' => 50, 'max' => 100]
        ],
        'formulas' => [
            'total_connected_load' => 'total_breakers * avg_load_per_breaker',
            'demand_load' => '(total_connected_load * demand_factor) / 100',
            'recommended_main' => function($context) {
                $load = $context['demand_load'];
                $standardSizes = [100, 125, 150, 175, 200, 225, 250, 300, 350, 400];
                foreach ($standardSizes as $size) {
                    if ($size >= $load) return $size . ' A';
                }
                return round($load, 0) . ' A';
            }
        ],
        'outputs' => [
            ['name' => 'total_connected_load', 'unit' => 'A', 'label' => 'Total Connected Load', 'precision' => 1],
            ['name' => 'demand_load', 'unit' => 'A', 'label' => 'Demand Load', 'precision' => 1],
            ['name' => 'recommended_main', 'unit' => '', 'label' => 'Recommended Main Breaker', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'receptacle-load' => [
        'name' => 'Receptacle Load Calculator',
        'description' => 'Calculate receptacle loads per NEC Article 220',
        'category' => 'electrical',
        'subcategory' => 'load-calculation',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'receptacle_count', 'type' => 'integer', 'required' => true, 'label' => 'Number of Receptacles', 'min' => 1],
            ['name' => 'receptacle_type', 'type' => 'string', 'required' => true, 'label' => 'Receptacle Type', 'options' => ['general', 'small_appliance', 'laundry'], 'default' => 'general']
        ],
        'formulas' => [
            'va_per_receptacle' => function($context) {
                $loads = ['general' => 180, 'small_appliance' => 1500, 'laundry' => 1500];
                return $loads[$context['receptacle_type']] ?? 180;
            },
            'total_va' => 'receptacle_count * va_per_receptacle',
            'current_120v' => 'total_va / 120',
            'circuits_required' => function($context) {
                return ceil($context['current_120v'] / 15);
            }
        ],
        'outputs' => [
            ['name' => 'va_per_receptacle', 'unit' => 'VA', 'label' => 'VA per Receptacle', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'total_va', 'unit' => 'VA', 'label' => 'Total Load', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'current_120v', 'unit' => 'A', 'label' => 'Current @ 120V', 'precision' => 1],
            ['name' => 'circuits_required', 'unit' => '', 'label' => '15A Circuits Required', 'precision' => 0, 'type' => 'integer']
        ]
    ],
    
    // ============================================
    // CONDUIT SIZING CALCULATORS
    // ============================================
    
    'cable-tray-sizing' => [
        'name' => 'Cable Tray Sizing Calculator',
        'description' => 'Calculate cable tray fill per NEC Article 392',
        'category' => 'electrical',
        'subcategory' => 'conduit-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'cable_diameter', 'type' => 'number', 'unit' => 'in', 'required' => true, 'label' => 'Cable Diameter (inches)', 'min' => 0.1],
            ['name' => 'cable_count', 'type' => 'integer', 'required' => true, 'label' => 'Number of Cables', 'min' => 1],
            ['name' => 'fill_percent', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Max Fill (%)', 'default' => 50, 'min' => 30, 'max' => 60]
        ],
        'formulas' => [
            'cable_area' => '3.14159 * (cable_diameter / 2) * (cable_diameter / 2)',
            'total_cable_area' => 'cable_area * cable_count',
            'required_tray_area' => '(total_cable_area * 100) / fill_percent',
            'recommended_width' => function($context) {
                $area = $context['required_tray_area'];
                $standardWidths = [6, 12, 18, 24, 30, 36];
                foreach ($standardWidths as $width) {
                    if ($width >= sqrt($area)) return $width . ' inches';
                }
                return round(sqrt($area), 0) . ' inches';
            }
        ],
        'outputs' => [
            ['name' => 'total_cable_area', 'unit' => 'sq in', 'label' => 'Total Cable Area', 'precision' => 2],
            ['name' => 'required_tray_area', 'unit' => 'sq in', 'label' => 'Required Tray Area', 'precision' => 2],
            ['name' => 'recommended_width', 'unit' => '', 'label' => 'Recommended Tray Width', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'conduit-fill-calculation' => [
        'name' => 'Conduit Fill Calculator',
        'description' => 'Calculate conduit fill percentage per NEC Chapter 9',
        'category' => 'electrical',
        'subcategory' => 'conduit-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'wire_size', 'type' => 'string', 'required' => true, 'label' => 'Wire Size (AWG)', 'options' => ['14', '12', '10', '8', '6', '4', '2', '1'], 'default' => '12'],
            ['name' => 'wire_count', 'type' => 'integer', 'required' => true, 'label' => 'Number of Wires', 'min' => 1],
            ['name' => 'conduit_size', 'type' => 'string', 'required' => true, 'label' => 'Conduit Size (inches)', 'options' => ['0.5', '0.75', '1', '1.25', '1.5', '2', '2.5', '3'], 'default' => '0.75']
        ],
        'formulas' => [
            'wire_area' => function($context) {
                $areas = ['14' => 0.0097, '12' => 0.0133, '10' => 0.0211, '8' => 0.0366, '6' => 0.0507, '4' => 0.0824, '2' => 0.1158, '1' => 0.1562];
                return $areas[$context['wire_size']] ?? 0.0133;
            },
            'total_wire_area' => 'wire_area * wire_count',
            'conduit_area' => function($context) {
                $areas = ['0.5' => 0.122, '0.75' => 0.213, '1' => 0.346, '1.25' => 0.581, '1.5' => 0.814, '2' => 1.363, '2.5' => 2.071, '3' => 3.538];
                return $areas[$context['conduit_size']] ?? 0.213;
            },
            'fill_percent' => '(total_wire_area / conduit_area) * 100',
            'max_allowed' => function($context) {
                $count = $context['wire_count'];
                if ($count == 1) return 53;
                if ($count == 2) return 31;
                return 40;
            },
            'assessment' => function($context) {
                $fill = $context['fill_percent'];
                $max = $context['max_allowed'];
                if ($fill <= $max) return 'Acceptable';
                return 'Exceeds NEC Limit';
            }
        ],
        'outputs' => [
            ['name' => 'total_wire_area', 'unit' => 'sq in', 'label' => 'Total Wire Area', 'precision' => 4],
            ['name' => 'conduit_area', 'unit' => 'sq in', 'label' => 'Conduit Area', 'precision' => 3],
            ['name' => 'fill_percent', 'unit' => '%', 'label' => 'Fill Percentage', 'precision' => 1],
            ['name' => 'max_allowed', 'unit' => '%', 'label' => 'Max Allowed Fill', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'assessment', 'unit' => '', 'label' => 'Assessment', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'entrance-service-sizing' => [
        'name' => 'Entrance Service Sizing Calculator',
        'description' => 'Size service entrance conductors per NEC Article 230',
        'category' => 'electrical',
        'subcategory' => 'conduit-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'total_load', 'type' => 'number', 'unit' => 'kW', 'required' => true, 'label' => 'Total Load (kW)', 'min' => 1],
            ['name' => 'voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'Service Voltage (V)', 'options' => [120, 240, 208, 480], 'default' => 240]
        ],
        'formulas' => [
            'service_current' => '(total_load * 1000) / voltage',
            'service_size' => function($context) {
                $current = $context['service_current'];
                $standardSizes = [100, 125, 150, 200, 225, 300, 400, 600, 800, 1000, 1200];
                foreach ($standardSizes as $size) {
                    if ($size >= $current) return $size . ' A';
                }
                return round($current, 0) . ' A';
            },
            'conductor_size' => function($context) {
                $current = $context['service_current'];
                if ($current <= 100) return '1 AWG';
                if ($current <= 125) return '1/0 AWG';
                if ($current <= 150) return '2/0 AWG';
                if ($current <= 175) return '3/0 AWG';
                if ($current <= 200) return '4/0 AWG';
                if ($current <= 250) return '250 kcmil';
                if ($current <= 300) return '350 kcmil';
                return '500 kcmil or larger';
            }
        ],
        'outputs' => [
            ['name' => 'service_current', 'unit' => 'A', 'label' => 'Service Current', 'precision' => 1],
            ['name' => 'service_size', 'unit' => '', 'label' => 'Service Size', 'precision' => 0, 'type' => 'string'],
            ['name' => 'conductor_size', 'unit' => '', 'label' => 'Minimum Conductor Size', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'junction-box-sizing' => [
        'name' => 'Junction Box Sizing Calculator',
        'description' => 'Calculate minimum junction box dimensions per NEC Article 314',
        'category' => 'electrical',
        'subcategory' => 'conduit-sizing',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'largest_wire', 'type' => 'string', 'required' => true, 'label' => 'Largest Wire Size (AWG)', 'options' => ['14', '12', '10', '8', '6', '4'], 'default' => '12'],
            ['name' => 'wire_count', 'type' => 'integer', 'required' => true, 'label' => 'Number of Wires', 'min' => 1]
        ],
        'formulas' => [
            'wire_volume' => function($context) {
                $volumes = ['14' => 2.0, '12' => 2.25, '10' => 2.5, '8' => 3.0, '6' => 5.0, '4' => 6.0];
                return $volumes[$context['largest_wire']] ?? 2.25;
            },
            'total_volume' => 'wire_volume * wire_count',
            'recommended_size' => function($context) {
                $volume = $context['total_volume'];
                if ($volume <= 18) return '4x4x1.5 inches';
                if ($volume <= 30) return '4x4x2.125 inches';
                if ($volume <= 42) return '4x4x2.875 inches';
                return 'Custom size required';
            }
        ],
        'outputs' => [
            ['name' => 'total_volume', 'unit' => 'cu in', 'label' => 'Required Volume', 'precision' => 1],
            ['name' => 'recommended_size', 'unit' => '', 'label' => 'Recommended Box Size', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    // ============================================
    // SHORT CIRCUIT CALCULATORS
    // ============================================
    
    'available-fault-current' => [
        'name' => 'Available Fault Current Calculator',
        'description' => 'Calculate short circuit current',
        'category' => 'electrical',
        'subcategory' => 'short-circuit',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'transformer_kva', 'type' => 'number', 'unit' => 'kVA', 'required' => true, 'label' => 'Transformer KVA', 'min' => 1],
            ['name' => 'impedance', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Transformer Impedance (%)', 'default' => 5.75, 'min' => 1, 'max' => 10],
            ['name' => 'voltage', 'type' => 'number', 'unit' => 'V', 'required' => true, 'label' => 'Secondary Voltage (V)', 'default' => 480]
        ],
        'formulas' => [
            'base_current' => '(transformer_kva * 1000) / (1.732 * voltage)',
            'fault_current' => '(base_current * 100) / impedance',
            'fault_current_ka' => 'fault_current / 1000'
        ],
        'outputs' => [
            ['name' => 'base_current', 'unit' => 'A', 'label' => 'Full Load Current', 'precision' => 1],
            ['name' => 'fault_current', 'unit' => 'A', 'label' => 'Available Fault Current', 'precision' => 0, 'type' => 'integer'],
            ['name' => 'fault_current_ka', 'unit' => 'kA', 'label' => 'Fault Current (kA)', 'precision' => 2]
        ]
    ],
    
    'ground-conductor-sizing' => [
        'name' => 'Ground Conductor Sizing Calculator',
        'description' => 'Size equipment grounding conductors per NEC Article 250',
        'category' => 'electrical',
        'subcategory' => 'short-circuit',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'ocpd_rating', 'type' => 'number', 'unit' => 'A', 'required' => true, 'label' => 'OCPD Rating (A)', 'min' => 15]
        ],
        'formulas' => [
            'ground_wire_size' => function($context) {
                $ocpd = $context['ocpd_rating'];
                if ($ocpd <= 15) return '14 AWG';
                if ($ocpd <= 20) return '12 AWG';
                if ($ocpd <= 60) return '10 AWG';
                if ($ocpd <= 100) return '8 AWG';
                if ($ocpd <= 200) return '6 AWG';
                if ($ocpd <= 300) return '4 AWG';
                if ($ocpd <= 400) return '3 AWG';
                if ($ocpd <= 500) return '2 AWG';
                if ($ocpd <= 600) return '1 AWG';
                return '1/0 AWG or larger';
            }
        ],
        'outputs' => [
            ['name' => 'ground_wire_size', 'unit' => '', 'label' => 'Minimum Ground Wire Size', 'precision' => 0, 'type' => 'string']
        ]
    ],
    
    'power-factor-correction' => [
        'name' => 'Power Factor Correction Calculator',
        'description' => 'Calculate capacitor size for power factor correction',
        'category' => 'electrical',
        'subcategory' => 'short-circuit',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'load_kw', 'type' => 'number', 'unit' => 'kW', 'required' => true, 'label' => 'Load (kW)', 'min' => 0.1],
            ['name' => 'existing_pf', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Existing Power Factor (%)', 'min' => 50, 'max' => 100],
            ['name' => 'target_pf', 'type' => 'number', 'unit' => '%', 'required' => true, 'label' => 'Target Power Factor (%)', 'default' => 95, 'min' => 80, 'max' => 100]
        ],
        'formulas' => [
            'existing_pf_decimal' => 'existing_pf / 100',
            'target_pf_decimal' => 'target_pf / 100',
            'existing_kvar' => 'load_kw * sqrt((1 / (existing_pf_decimal * existing_pf_decimal)) - 1)',
            'target_kvar' => 'load_kw * sqrt((1 / (target_pf_decimal * target_pf_decimal)) - 1)',
            'capacitor_kvar' => 'existing_kvar - target_kvar',
            'annual_savings' => function($context) {
                return $context['capacitor_kvar'] * 0.05 * 8760; // Simplified savings estimate
            }
        ],
        'outputs' => [
            ['name' => 'existing_kvar', 'unit' => 'kVAR', 'label' => 'Existing Reactive Power', 'precision' => 2],
            ['name' => 'target_kvar', 'unit' => 'kVAR', 'label' => 'Target Reactive Power', 'precision' => 2],
            ['name' => 'capacitor_kvar', 'unit' => 'kVAR', 'label' => 'Required Capacitor Size', 'precision' => 2],
            ['name' => 'annual_savings', 'unit' => 'kWh', 'label' => 'Estimated Annual Savings', 'precision' => 0, 'type' => 'integer']
        ]
    ]
];
