<?php

/**
 * Site Calculators Configuration
 * Site Engineering and Construction Management Tools
 */

return [
    // ============================================
    // EARTHWORK (5 calculators)
    // ============================================
    
    'excavation-cost' => [
        'name' => 'Excavation Cost',
        'description' => 'Calculate excavation volume and cost',
        'category' => 'site',
        'subcategory' => 'earthwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'length', 'type' => 'number', 'label' => 'Length', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'width', 'type' => 'number', 'label' => 'Width', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'depth', 'type' => 'number', 'label' => 'Depth', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'rate', 'type' => 'number', 'label' => 'Rate per m³', 'unit' => '$', 'required' => true, 'min' => 0, 'default' => 15],
        ],
        'formulas' => [
            'volume' => function($i) { return $i['length'] * $i['width'] * $i['depth']; },
            'cost' => function($i, $r) { return $r['volume'] * $i['rate']; },
        ],
        'outputs' => [
            ['name' => 'volume', 'label' => 'Excavation Volume', 'unit' => 'm³', 'precision' => 2],
            ['name' => 'cost', 'label' => 'Total Cost', 'unit' => '$', 'precision' => 2],
        ],
    ],

    'trench-volume' => [
        'name' => 'Trench Volume',
        'description' => 'Calculate trench excavation volume with slope',
        'category' => 'site',
        'subcategory' => 'earthwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'length', 'type' => 'number', 'label' => 'Length', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'top_width', 'type' => 'number', 'label' => 'Top Width', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'bottom_width', 'type' => 'number', 'label' => 'Bottom Width', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'depth', 'type' => 'number', 'label' => 'Depth', 'unit' => 'm', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'area' => function($i) { return ($i['top_width'] + $i['bottom_width']) / 2 * $i['depth']; },
            'volume' => function($i, $r) { return $r['area'] * $i['length']; },
        ],
        'outputs' => [
            ['name' => 'area', 'label' => 'Cross-Section Area', 'unit' => 'm²', 'precision' => 2],
            ['name' => 'volume', 'label' => 'Total Volume', 'unit' => 'm³', 'precision' => 2],
        ],
    ],

    'cut-fill' => [
        'name' => 'Cut and Fill Estimation',
        'description' => 'Estimate cut and fill volumes (SIMPLIFIED: Grid Method)',
        'category' => 'site',
        'subcategory' => 'earthwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'area', 'type' => 'number', 'label' => 'Grid Area', 'unit' => 'm²', 'required' => true, 'min' => 0],
            ['name' => 'avg_cut_depth', 'type' => 'number', 'label' => 'Avg Cut Depth', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'avg_fill_depth', 'type' => 'number', 'label' => 'Avg Fill Depth', 'unit' => 'm', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'cut_volume' => function($i) { return $i['area'] * $i['avg_cut_depth']; },
            'fill_volume' => function($i) { return $i['area'] * $i['avg_fill_depth']; },
            'net_volume' => function($i, $r) { return $r['cut_volume'] - $r['fill_volume']; },
        ],
        'outputs' => [
            ['name' => 'cut_volume', 'label' => 'Cut Volume', 'unit' => 'm³', 'precision' => 2],
            ['name' => 'fill_volume', 'label' => 'Fill Volume', 'unit' => 'm³', 'precision' => 2],
            ['name' => 'net_volume', 'label' => 'Net Volume (Pos=Cut, Neg=Fill)', 'unit' => 'm³', 'precision' => 2],
        ],
    ],

    'soil-compaction' => [
        'name' => 'Soil Compaction',
        'description' => 'Calculate shrinkage/swell factor adjustments',
        'category' => 'site',
        'subcategory' => 'earthwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'loose_volume', 'type' => 'number', 'label' => 'Loose Volume', 'unit' => 'm³', 'required' => true, 'min' => 0],
            ['name' => 'swell_factor', 'type' => 'number', 'label' => 'Swell Factor (%)', 'unit' => '%', 'required' => true, 'min' => 0, 'default' => 20],
            ['name' => 'shrinkage_factor', 'type' => 'number', 'label' => 'Shrinkage Factor (%)', 'unit' => '%', 'required' => true, 'min' => 0, 'default' => 10],
        ],
        'formulas' => [
            'bank_volume' => function($i) { return $i['loose_volume'] / (1 + ($i['swell_factor'] / 100)); },
            'compacted_volume' => function($i, $r) { return $r['bank_volume'] * (1 - ($i['shrinkage_factor'] / 100)); },
        ],
        'outputs' => [
            ['name' => 'bank_volume', 'label' => 'Bank Volume (Insitu)', 'unit' => 'm³', 'precision' => 2],
            ['name' => 'compacted_volume', 'label' => 'Compacted Volume', 'unit' => 'm³', 'precision' => 2],
        ],
    ],

    'topsoil-removal' => [
        'name' => 'Topsoil Removal',
        'description' => 'Calculate volume of topsoil to strip',
        'category' => 'site',
        'subcategory' => 'earthwork',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'length', 'type' => 'number', 'label' => 'Site Length', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'width', 'type' => 'number', 'label' => 'Site Width', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'depth', 'type' => 'number', 'label' => 'Strip Depth', 'unit' => 'mm', 'required' => true, 'min' => 0, 'default' => 150],
        ],
        'formulas' => [
            'area' => function($i) { return $i['length'] * $i['width']; },
            'volume' => function($i, $r) { return $r['area'] * ($i['depth'] / 1000); },
        ],
        'outputs' => [
            ['name' => 'area', 'label' => 'Area', 'unit' => 'm²', 'precision' => 2],
            ['name' => 'volume', 'label' => 'Topsoil Volume', 'unit' => 'm³', 'precision' => 2],
        ],
    ],
    'swelling-shrinkage' => ['name' => 'Swelling & Shrinkage', 'description' => 'Calculate soil volume change', 'category' => 'site', 'subcategory' => 'earthwork', 'version' => '1.0', 'inputs' => [['name' => 'vol', 'type' => 'number', 'label' => 'Initial Volume', 'unit' => 'm³'], ['name' => 'factor', 'type' => 'number', 'label' => 'Factor (%)', 'unit' => '%']], 'formulas' => ['final_vol' => function($i) { return $i['vol'] * (1 + $i['factor']/100); }], 'outputs' => [['name' => 'final_vol', 'label' => 'Final Volume', 'unit' => 'm³', 'precision' => 2]]],
    'cut-fill-balancing' => ['name' => 'Cut/Fill Balancing', 'description' => 'Balance excavation and embankment', 'category' => 'site', 'subcategory' => 'earthwork', 'version' => '1.0', 'inputs' => [['name' => 'cut', 'type' => 'number', 'label' => 'Cut Volume', 'unit' => 'm³'], ['name' => 'fill', 'type' => 'number', 'label' => 'Fill Volume', 'unit' => 'm³']], 'formulas' => ['balance' => function($i) { return $i['cut'] - $i['fill']; }], 'outputs' => [['name' => 'balance', 'label' => 'Net Balance', 'unit' => 'm³', 'precision' => 2]]],
    'slope-paving' => ['name' => 'Slope Paving Calculator', 'description' => 'Calculate paving requirements on slopes', 'category' => 'site', 'subcategory' => 'earthwork', 'version' => '1.0', 'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Slope Area', 'unit' => 'm²']], 'formulas' => ['qty' => function($i) { return $i['area'] * 1.1; }], 'outputs' => [['name' => 'qty', 'label' => 'Total Materials', 'unit' => 'ton', 'precision' => 2]]],


    // ============================================
    // SURVEYING (5 calculators)
    // ============================================

    'slope-gradient' => [
        'name' => 'Slope Gradient',
        'description' => 'Calculate slope percentage and angle',
        'category' => 'site',
        'subcategory' => 'surveying',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'rise', 'type' => 'number', 'label' => 'Rise (Vertical)', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'run', 'type' => 'number', 'label' => 'Run (Horizontal)', 'unit' => 'm', 'required' => true, 'min' => 0.001],
        ],
        'formulas' => [
            'percentage' => function($i) { return ($i['rise'] / $i['run']) * 100; },
            'angle' => function($i) { return rad2deg(atan($i['rise'] / $i['run'])); },
            'ratio' => function($i) { return "1:" . round($i['run'] / $i['rise'], 1); },
        ],
        'outputs' => [
            ['name' => 'percentage', 'label' => 'Slope', 'unit' => '%', 'precision' => 2],
            ['name' => 'angle', 'label' => 'Angle', 'unit' => '°', 'precision' => 2],
            ['name' => 'ratio', 'label' => 'Ratio (1:X)', 'unit' => '', 'precision' => 0, 'type' => 'string'],
        ],
    ],

    'coordinates-distance' => [
        'name' => 'Coordinates Distance',
        'description' => 'Calculate 2D distance between two coordinate points',
        'category' => 'site',
        'subcategory' => 'surveying',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'x1', 'type' => 'number', 'label' => 'X1 (Easting)', 'unit' => 'm', 'required' => true],
            ['name' => 'y1', 'type' => 'number', 'label' => 'Y1 (Northing)', 'unit' => 'm', 'required' => true],
            ['name' => 'x2', 'type' => 'number', 'label' => 'X2 (Easting)', 'unit' => 'm', 'required' => true],
            ['name' => 'y2', 'type' => 'number', 'label' => 'Y2 (Northing)', 'unit' => 'm', 'required' => true],
        ],
        'formulas' => [
            'dx' => function($i) { return $i['x2'] - $i['x1']; },
            'dy' => function($i) { return $i['y2'] - $i['y1']; },
            'distance' => function($i, $r) { return sqrt(pow($r['dx'], 2) + pow($r['dy'], 2)); },
        ],
        'outputs' => [
            ['name' => 'distance', 'label' => 'Horizontal Distance', 'unit' => 'm', 'precision' => 3],
        ],
    ],

    'leveling-reduction' => [
        'name' => 'Leveling Reduction',
        'description' => 'Calculate Reduced Level (HI Method)',
        'category' => 'site',
        'subcategory' => 'surveying',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'bm_rl', 'type' => 'number', 'label' => 'Benchmark RL', 'unit' => 'm', 'required' => true],
            ['name' => 'bs', 'type' => 'number', 'label' => 'Backsight (BS)', 'unit' => 'm', 'required' => true],
            ['name' => 'fs', 'type' => 'number', 'label' => 'Foresight (FS)', 'unit' => 'm', 'required' => true],
        ],
        'formulas' => [
            'hi' => function($i) { return $i['bm_rl'] + $i['bs']; },
            'rl' => function($i, $r) { return $r['hi'] - $i['fs']; },
        ],
        'outputs' => [
            ['name' => 'hi', 'label' => 'Height of Instrument', 'unit' => 'm', 'precision' => 3],
            ['name' => 'rl', 'label' => 'Reduced Level (Point)', 'unit' => 'm', 'precision' => 3],
        ],
    ],

    'curve-setting' => [
        'name' => 'Horizontal Curve Setting',
        'description' => 'Basic parameters for a circular curve',
        'category' => 'site',
        'subcategory' => 'surveying',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'radius', 'type' => 'number', 'label' => 'Radius (R)', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'angle', 'type' => 'number', 'label' => 'Intersection Angle (I)', 'unit' => '°', 'required' => true, 'min' => 0, 'max' => 180],
        ],
        'formulas' => [
            'tangent_dist' => function($i) { return $i['radius'] * tan(deg2rad($i['angle'] / 2)); },
            'curve_length' => function($i) { return (pi() * $i['radius'] * $i['angle']) / 180; },
            'long_chord' => function($i) { return 2 * $i['radius'] * sin(deg2rad($i['angle'] / 2)); },
        ],
        'outputs' => [
            ['name' => 'tangent_dist', 'label' => 'Tangent Distance (T)', 'unit' => 'm', 'precision' => 3],
            ['name' => 'curve_length', 'label' => 'Curve Length (L)', 'unit' => 'm', 'precision' => 3],
            ['name' => 'long_chord', 'label' => 'Long Chord', 'unit' => 'm', 'precision' => 3],
        ],
    ],
    'slope-staking' => ['name' => 'Slope Staking Calculator', 'description' => 'Calculate staking offsets', 'category' => 'site', 'subcategory' => 'surveying', 'version' => '1.0', 'inputs' => [['name' => 'dist', 'type' => 'number', 'label' => 'CL Distance', 'unit' => 'm']], 'formulas' => ['offset' => function($i) { return $i['dist'] * 1.5; }], 'outputs' => [['name' => 'offset', 'label' => 'Required Offset', 'unit' => 'm', 'precision' => 2]]],
    'batter-boards' => ['name' => 'Batter Board Setup', 'description' => 'Calculate batter board parameters', 'category' => 'site', 'subcategory' => 'surveying', 'version' => '1.0', 'inputs' => [['name' => 'width', 'type' => 'number', 'label' => 'Building Width', 'unit' => 'm']], 'formulas' => ['board_len' => function($i) { return $i['width'] + 2; }], 'outputs' => [['name' => 'board_len', 'label' => 'Board Length', 'unit' => 'm', 'precision' => 2]]],
    'horizontal-curve-staking' => ['name' => 'Horizontal Curve Staking', 'description' => 'Staking offsets for curves', 'category' => 'site', 'subcategory' => 'surveying', 'version' => '1.0', 'inputs' => [['name' => 'radius', 'type' => 'number', 'label' => 'Radius', 'unit' => 'm']], 'formulas' => ['chord' => function($i) { return $i['radius'] * 0.1; }], 'outputs' => [['name' => 'chord', 'label' => 'Chord Length', 'unit' => 'm', 'precision' => 2]]],
    'grade-rod' => ['name' => 'Grade Rod Calculator', 'description' => 'Calculate rod readings for grade', 'category' => 'site', 'subcategory' => 'surveying', 'version' => '1.0', 'inputs' => [['name' => 'hi', 'type' => 'number', 'label' => 'HI', 'unit' => 'm'], ['name' => 'grade', 'type' => 'number', 'label' => 'Design Grade', 'unit' => 'm']], 'formulas' => ['reading' => function($i) { return $i['hi'] - $i['grade']; }], 'outputs' => [['name' => 'reading', 'label' => 'Rod Reading', 'unit' => 'm', 'precision' => 3]]],


    'area-coordinates' => [
        'name' => 'Area by Coordinates',
        'description' => 'Calculate triangle area from 3 points',
        'category' => 'site',
        'subcategory' => 'surveying',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'x1', 'type' => 'number', 'label' => 'X1', 'unit' => 'm', 'required' => true], ['name' => 'y1', 'type' => 'number', 'label' => 'Y1', 'unit' => 'm', 'required' => true],
            ['name' => 'x2', 'type' => 'number', 'label' => 'X2', 'unit' => 'm', 'required' => true], ['name' => 'y2', 'type' => 'number', 'label' => 'Y2', 'unit' => 'm', 'required' => true],
            ['name' => 'x3', 'type' => 'number', 'label' => 'X3', 'unit' => 'm', 'required' => true], ['name' => 'y3', 'type' => 'number', 'label' => 'Y3', 'unit' => 'm', 'required' => true],
        ],
        'formulas' => [
            'area' => function($i) { 
                return abs(($i['x1'] * ($i['y2'] - $i['y3']) + $i['x2'] * ($i['y3'] - $i['y1']) + $i['x3'] * ($i['y1'] - $i['y2'])) / 2); 
            },
        ],
        'outputs' => [
            ['name' => 'area', 'label' => 'Area', 'unit' => 'm²', 'precision' => 3],
        ],
    ],

    // ============================================
    // CONSTRUCTION SAFETY (3 calculators)
    // ============================================

    'scaffold-load' => [
        'name' => 'Scaffold Loading',
        'description' => 'Check scaffold leg loads (Simplified)',
        'category' => 'site',
        'subcategory' => 'safety',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'dead_load', 'type' => 'number', 'label' => 'Dead Load', 'unit' => 'kN', 'required' => true, 'min' => 0],
            ['name' => 'live_load', 'type' => 'number', 'label' => 'Live Load', 'unit' => 'kN', 'required' => true, 'min' => 0],
            ['name' => 'legs', 'type' => 'number', 'label' => 'Number of Legs', 'unit' => '', 'required' => true, 'min' => 4],
        ],
        'formulas' => [
            'total_load' => function($i) { return 1.5 * ($i['dead_load'] + $i['live_load']); }, // Safety factor included
            'leg_load' => function($i, $r) { return $r['total_load'] / $i['legs']; },
        ],
        'outputs' => [
            ['name' => 'total_load', 'label' => 'Total Factored Load', 'unit' => 'kN', 'precision' => 2],
            ['name' => 'leg_load', 'label' => 'Load Per Leg', 'unit' => 'kN', 'precision' => 2],
        ],
    ],

    'crane-stability' => [
        'name' => 'Crane Stability',
        'description' => 'Basic moment check for overturning',
        'category' => 'site',
        'subcategory' => 'safety',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'crane_weight', 'type' => 'number', 'label' => 'Crane Weight', 'unit' => 'kN', 'required' => true, 'min' => 0],
            ['name' => 'cg_dist', 'type' => 'number', 'label' => 'CG Distance from Pivot', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'load_weight', 'type' => 'number', 'label' => 'Load Weight', 'unit' => 'kN', 'required' => true, 'min' => 0],
            ['name' => 'load_radius', 'type' => 'number', 'label' => 'Load Radius from Pivot', 'unit' => 'm', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'restoring_moment' => function($i) { return $i['crane_weight'] * $i['cg_dist']; },
            'overturning_moment' => function($i) { return $i['load_weight'] * $i['load_radius']; },
            'safety_factor' => function($i, $r) { return $r['restoring_moment'] / $r['overturning_moment']; },
            'status' => function($i, $r) { return $r['safety_factor'] >= 1.5 ? 'SAFE' : 'UNSAFE'; },
        ],
        'outputs' => [
            ['name' => 'safety_factor', 'label' => 'Safety Factor', 'unit' => '', 'precision' => 2],
            ['name' => 'status', 'label' => 'Stability Status', 'unit' => '', 'precision' => 0, 'type' => 'string'],
        ],
    ],

    'excavation-safety' => [
        'name' => 'Excavation Sloping',
        'description' => 'Determine horizontal distance for given slope',
        'category' => 'site',
        'subcategory' => 'safety',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'depth', 'type' => 'number', 'label' => 'Excavation Depth', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'slope_ratio', 'type' => 'select', 'label' => 'Soil Type Slope (H:V)', 'required' => true, 'options' => ['0.75' => 'Type A (0.75:1)', '1' => 'Type B (1:1)', '1.5' => 'Type C (1.5:1)']],
        ],
        'formulas' => [
            'cut_back' => function($i) { return $i['depth'] * (float)$i['slope_ratio']; },
        ],
        'outputs' => [
            ['name' => 'cut_back', 'label' => 'Required Cut Back Distance', 'unit' => 'm', 'precision' => 2],
        ],
    ],
    'temperature-control' => ['name' => 'Temperature Control', 'description' => 'Concrete temperature monitoring', 'category' => 'site', 'subcategory' => 'concrete', 'version' => '1.0', 'inputs' => [['name' => 'temp', 'type' => 'number', 'label' => 'Current Temp', 'unit' => '°C']], 'formulas' => ['status' => function($i) { return $i['temp'] > 30 ? 'High' : 'Normal'; }], 'outputs' => [['name' => 'status', 'label' => 'Status', 'type' => 'string']]],
    'yardage-adjustments' => ['name' => 'Yardage Adjustments', 'description' => 'Adjust concrete volume for field conditions', 'category' => 'site', 'subcategory' => 'concrete', 'version' => '1.0', 'inputs' => [['name' => 'vol', 'type' => 'number', 'label' => 'Theoretical Vol', 'unit' => 'm³']], 'formulas' => ['actual' => function($i) { return $i['vol'] * 1.05; }], 'outputs' => [['name' => 'actual', 'label' => 'Actual Volume', 'unit' => 'm³', 'precision' => 2]]],
    'placement-rate' => ['name' => 'Placement Rate Calculator', 'description' => 'Calculate concrete placement speed', 'category' => 'site', 'subcategory' => 'concrete', 'version' => '1.0', 'inputs' => [['name' => 'vol', 'type' => 'number', 'label' => 'Total Volume', 'unit' => 'm³'], ['name' => 'time', 'type' => 'number', 'label' => 'Time', 'unit' => 'hrs']], 'formulas' => ['rate' => function($i) { return $i['time'] > 0 ? $i['vol'] / $i['time'] : 0; }], 'outputs' => [['name' => 'rate', 'label' => 'Rate', 'unit' => 'm³/hr', 'precision' => 1]]],
    'testing-requirements' => ['name' => 'Testing Requirements', 'description' => 'Concrete testing frequency', 'category' => 'site', 'subcategory' => 'concrete', 'version' => '1.0', 'inputs' => [['name' => 'vol', 'type' => 'number', 'label' => 'Total Volume', 'unit' => 'm³']], 'formulas' => ['tests' => function($i) { return ceil($i['vol'] / 50); }], 'outputs' => [['name' => 'tests', 'label' => 'Number of Tests']]],
    'fall-protection' => ['name' => 'Fall Protection Planning', 'description' => 'Fall clearance calculations', 'category' => 'site', 'subcategory' => 'safety', 'version' => '1.0', 'inputs' => [['name' => 'height', 'type' => 'number', 'label' => 'Work Height', 'unit' => 'm']], 'formulas' => ['clearance' => function($i) { return $i['height'] + 2; }], 'outputs' => [['name' => 'clearance', 'label' => 'Required Clearance', 'unit' => 'm', 'precision' => 1]]],
    'trench-safety' => ['name' => 'Trench Safety Calculator', 'description' => 'Trench shoring and sloping requirement', 'category' => 'site', 'subcategory' => 'safety', 'version' => '1.0', 'inputs' => [['name' => 'depth', 'type' => 'number', 'label' => 'Depth', 'unit' => 'm']], 'formulas' => ['status' => function($i) { return $i['depth'] > 1.2 ? 'Shoring Required' : 'Safe'; }], 'outputs' => [['name' => 'status', 'label' => 'Status', 'type' => 'string']]],
    'crane-setup' => ['name' => 'Crane Setup Calculator', 'description' => 'Crane outrigger loading', 'category' => 'site', 'subcategory' => 'safety', 'version' => '1.0', 'inputs' => [['name' => 'load', 'type' => 'number', 'label' => 'Gross Load', 'unit' => 'kN']], 'formulas' => ['outrigger' => function($i) { return $i['load'] * 0.75; }], 'outputs' => [['name' => 'outrigger', 'label' => 'Outrigger Load', 'unit' => 'kN', 'precision' => 1]]],
    'evacuation-planning' => ['name' => 'Evacuation Planning', 'description' => 'Site evacuation time estimate', 'category' => 'site', 'subcategory' => 'safety', 'version' => '1.0', 'inputs' => [['name' => 'people', 'type' => 'number', 'label' => 'Total People']], 'formulas' => ['time' => function($i) { return $i['people'] * 0.1; }], 'outputs' => [['name' => 'time', 'label' => 'Time', 'unit' => 'min', 'precision' => 1]]],


    // ============================================
    // EQUIPMENT (3 calculators)
    // ============================================

    'equipment-production' => [
        'name' => 'Equipment Production',
        'description' => 'Calculate hourly production rate',
        'category' => 'site',
        'subcategory' => 'equipment',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'capacity', 'type' => 'number', 'label' => 'Bucket Capacity', 'unit' => 'm³', 'required' => true, 'min' => 0],
            ['name' => 'fill_factor', 'type' => 'number', 'label' => 'Fill Factor (%)', 'unit' => '%', 'required' => true, 'min' => 0, 'max' => 100],
            ['name' => 'efficiency', 'type' => 'number', 'label' => 'Efficiency (min/hr)', 'unit' => 'min', 'required' => true, 'min' => 0, 'max' => 60, 'default' => 50],
            ['name' => 'cycle_time', 'type' => 'number', 'label' => 'Cycle Time', 'unit' => 'sec', 'required' => true, 'min' => 1],
        ],
        'formulas' => [
            'cycles_per_hr' => function($i) { return ($i['efficiency'] * 60) / $i['cycle_time']; },
            'vol_per_cycle' => function($i) { return $i['capacity'] * ($i['fill_factor'] / 100); },
            'production' => function($i, $r) { return $r['cycles_per_hr'] * $r['vol_per_cycle']; },
        ],
        'outputs' => [
            ['name' => 'production', 'label' => 'Hourly Production', 'unit' => 'm³/hr', 'precision' => 1],
        ],
    ],

    'owning-operating-cost' => [
        'name' => 'Owning and Operating Cost',
        'description' => 'Estimate hourly equipment cost',
        'category' => 'site',
        'subcategory' => 'equipment',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'purchase_price', 'type' => 'number', 'label' => 'Purchase Price', 'unit' => '$', 'required' => true, 'min' => 0],
            ['name' => 'life_hours', 'type' => 'number', 'label' => 'Useful Life', 'unit' => 'hrs', 'required' => true, 'min' => 1],
            ['name' => 'fuel_cost', 'type' => 'number', 'label' => 'Fuel Cost/Hr', 'unit' => '$', 'required' => true, 'min' => 0],
            ['name' => 'maint_cost', 'type' => 'number', 'label' => 'Maintenance Cost/Hr', 'unit' => '$', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'depreciation' => function($i) { return $i['purchase_price'] / $i['life_hours']; },
            'ownership_cost' => function($i, $r) { return $r['depreciation'] * 1.1; }, // +10% interest/ins
            'operating_cost' => function($i) { return $i['fuel_cost'] + $i['maint_cost']; },
            'total_hourly_cost' => function($i, $r) { return $r['ownership_cost'] + $r['operating_cost']; },
        ],
        'outputs' => [
            ['name' => 'ownership_cost', 'label' => 'Ownership Cost', 'unit' => '$/hr', 'precision' => 2],
            ['name' => 'operating_cost', 'label' => 'Operating Cost', 'unit' => '$/hr', 'precision' => 2],
            ['name' => 'total_hourly_cost', 'label' => 'Total Hourly Cost', 'unit' => '$/hr', 'precision' => 2],
        ],
    ],

    'fleet-sizing' => [
        'name' => 'Fleet Sizing',
        'description' => 'Calculate number of trucks needed',
        'category' => 'site',
        'subcategory' => 'equipment',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'excavator_output', 'type' => 'number', 'label' => 'Excavator Output', 'unit' => 'm³/hr', 'required' => true, 'min' => 0],
            ['name' => 'truck_capacity', 'type' => 'number', 'label' => 'Truck Capacity', 'unit' => 'm³', 'required' => true, 'min' => 0],
            ['name' => 'truck_cycle', 'type' => 'number', 'label' => 'Truck Cycle Time', 'unit' => 'min', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'trucks_needed' => function($i) { 
                if ($i['truck_cycle'] <= 0) return 0;
                $cycles_per_hr = 60 / $i['truck_cycle'];
                $truck_output = $i['truck_capacity'] * $cycles_per_hr;
                if ($truck_output <= 0) return 0;
                return ceil($i['excavator_output'] / $truck_output);
            },
        ],
        'outputs' => [
            ['name' => 'trucks_needed', 'label' => 'Trucks Required', 'unit' => '', 'precision' => 0],
        ],
    ],

    // ============================================
    // MATERIALS (5 calculators)
    // ============================================

    'bricks-calculation' => [
        'name' => 'Bricks Calculation',
        'description' => 'Calculate number of bricks for a wall',
        'category' => 'site',
        'subcategory' => 'materials',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'length', 'type' => 'number', 'label' => 'Wall Length', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'height', 'type' => 'number', 'label' => 'Wall Height', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'thickness', 'type' => 'number', 'label' => 'Wall Thickness', 'unit' => 'mm', 'required' => true, 'min' => 0, 'default' => 230],
        ],
        'formulas' => [
            'wall_vol' => function($i) { return $i['length'] * $i['height'] * ($i['thickness'] / 1000); },
            'bricks' => function($i, $r) { return ceil($r['wall_vol'] * 500); }, // Approx 500 bricks per m3 with mortar
        ],
        'outputs' => [
            ['name' => 'wall_vol', 'label' => 'Volume', 'unit' => 'm³', 'precision' => 2],
            ['name' => 'bricks', 'label' => 'Total Bricks', 'unit' => '', 'precision' => 0],
        ],
    ],

    'cement-mortar' => [
        'name' => 'Cement and Sand for Mortar',
        'description' => 'Quantities for mortar mix',
        'category' => 'site',
        'subcategory' => 'materials',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'volume', 'type' => 'number', 'label' => 'Mortar Volume', 'unit' => 'm³', 'required' => true, 'min' => 0],
            ['name' => 'ratio', 'type' => 'select', 'label' => 'Mix Ratio', 'required' => true, 'options' => ['4' => '1:4', '5' => '1:5', '6' => '1:6']],
        ],
        'formulas' => [
            'dry_vol' => function($i) { return $i['volume'] * 1.33; },
            'cement' => function($i, $r) { 
                $ratio = (int)$i['ratio'];
                $cement_vol = $r['dry_vol'] / (1 + $ratio);
                return $cement_vol * 1440; // kg
            },
            'sand' => function($i, $r) { 
                $ratio = (int)$i['ratio'];
                $cement_vol = $r['dry_vol'] / (1 + $ratio);
                return $cement_vol * $ratio; // m3
            },
        ],
        'outputs' => [
            ['name' => 'cement', 'label' => 'Cement', 'unit' => 'kg', 'precision' => 1],
            ['name' => 'sand', 'label' => 'Sand', 'unit' => 'm³', 'precision' => 2],
        ],
    ],

    'concrete-mix' => [
        'name' => 'Concrete Mix Design',
        'description' => 'Calculate concrete ingredients (Nominal)',
        'category' => 'site',
        'subcategory' => 'materials',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'volume', 'type' => 'number', 'label' => 'Wet Volume', 'unit' => 'm³', 'required' => true, 'min' => 0],
            ['name' => 'grade', 'type' => 'select', 'label' => 'Grade', 'required' => true, 'options' => ['M15' => 'M15 (1:2:4)', 'M20' => 'M20 (1:1.5:3)', 'M25' => 'M25 (1:1:2)']],
        ],
        'formulas' => [
            'dry_vol' => function($i) { return $i['volume'] * 1.54; },
            'ratios' => function($i) {
                switch ($i['grade']) {
                    case 'M15': return [1, 2, 4];
                    case 'M20': return [1, 1.5, 3];
                    case 'M25': return [1, 1, 2];
                    default: return [1, 2, 4];
                }
            },
            'cement' => function($i, $r) {
                $sum = array_sum($r['ratios']);
                return ($r['dry_vol'] / $sum) * 1440;
            },
            'sand' => function($i, $r) {
                $sum = array_sum($r['ratios']);
                return ($r['dry_vol'] / $sum) * $r['ratios'][1];
            },
            'aggregate' => function($i, $r) {
                $sum = array_sum($r['ratios']);
                return ($r['dry_vol'] / $sum) * $r['ratios'][2];
            },
        ],
        'outputs' => [
            ['name' => 'cement', 'label' => 'Cement', 'unit' => 'kg', 'precision' => 0],
            ['name' => 'sand', 'label' => 'Sand', 'unit' => 'm³', 'precision' => 2],
            ['name' => 'aggregate', 'label' => 'Aggregate', 'unit' => 'm³', 'precision' => 2],
        ],
    ],

    'asphalt-calculator' => [
        'name' => 'Asphalt Calculator',
        'description' => 'Calculate asphalt tonnage',
        'category' => 'site',
        'subcategory' => 'materials',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'area', 'type' => 'number', 'label' => 'Paving Area', 'unit' => 'm²', 'required' => true, 'min' => 0],
            ['name' => 'thickness', 'type' => 'number', 'label' => 'Thickness', 'unit' => 'mm', 'required' => true, 'min' => 0],
            ['name' => 'density', 'type' => 'number', 'label' => 'Density', 'unit' => 'kg/m³', 'required' => true, 'min' => 0, 'default' => 2400],
        ],
        'formulas' => [
            'volume' => function($i) { return $i['area'] * ($i['thickness'] / 1000); },
            'weight' => function($i, $r) { return ($r['volume'] * $i['density']) / 1000; }, // tons
        ],
        'outputs' => [
            ['name' => 'weight', 'label' => 'Required Asphalt', 'unit' => 'tons', 'precision' => 2],
        ],
    ],

    'tile-calculator' => [
        'name' => 'Tile Calculator',
        'description' => 'Calculate floor tiles needed',
        'category' => 'site',
        'subcategory' => 'materials',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'floor_area', 'type' => 'number', 'label' => 'Floor Area', 'unit' => 'm²', 'required' => true, 'min' => 0],
            ['name' => 'tile_length', 'type' => 'number', 'label' => 'Tile Length', 'unit' => 'mm', 'required' => true, 'min' => 0],
            ['name' => 'tile_width', 'type' => 'number', 'label' => 'Tile Width', 'unit' => 'mm', 'required' => true, 'min' => 0],
            ['name' => 'wastage', 'type' => 'number', 'label' => 'Wastage (%)', 'unit' => '%', 'required' => true, 'min' => 0, 'default' => 5],
        ],
        'formulas' => [
            'tile_area' => function($i) { return ($i['tile_length'] / 1000) * ($i['tile_width'] / 1000); },
            'num_tiles' => function($i, $r) { 
                if ($r['tile_area'] <= 0) return 0;
                $base = $i['floor_area'] / $r['tile_area'];
                return ceil($base * (1 + $i['wastage'] / 100));
            },
        ],
        'outputs' => [
            ['name' => 'num_tiles', 'label' => 'Total Tiles Needed', 'unit' => '', 'precision' => 0],
        ],
    ],
    'labor-productivity' => ['name' => 'Labor Productivity', 'description' => 'Track labor output', 'category' => 'site', 'subcategory' => 'productivity', 'version' => '1.0', 'inputs' => [['name' => 'work', 'type' => 'number', 'label' => 'Work Done', 'unit' => 'm²'], ['name' => 'hours', 'type' => 'number', 'label' => 'Total Hours']], 'formulas' => ['output' => function($i) { return $i['hours'] > 0 ? $i['work'] / $i['hours'] : 0; }], 'outputs' => [['name' => 'output', 'label' => 'Production Rate', 'unit' => 'm²/hr', 'precision' => 1]]],
    'equipment-utilization' => ['name' => 'Equipment Utilization', 'description' => 'Analyze equipment efficiency', 'category' => 'site', 'subcategory' => 'productivity', 'version' => '1.0', 'inputs' => [['name' => 'runtime', 'type' => 'number', 'label' => 'Run Time', 'unit' => 'hrs'], ['name' => 'shift', 'type' => 'number', 'label' => 'Shift Length', 'unit' => 'hrs', 'default' => 8]], 'formulas' => ['util' => function($i) { return $i['shift'] > 0 ? ($i['runtime'] / $i['shift']) * 100 : 0; }], 'outputs' => [['name' => 'util', 'label' => 'Utilization', 'unit' => '%', 'precision' => 1]]],
    'schedule-compression' => ['name' => 'Schedule Compression', 'description' => 'Analyze crashing costs', 'category' => 'site', 'subcategory' => 'productivity', 'version' => '1.0', 'inputs' => [['name' => 'days', 'type' => 'number', 'label' => 'Days Reduced']], 'formulas' => ['cost' => function($i) { return $i['days'] * 1000; }], 'outputs' => [['name' => 'cost', 'label' => 'Crashing Cost', 'unit' => '$', 'precision' => 0]]],
    'cost-productivity' => ['name' => 'Cost Productivity Analysis', 'description' => 'Analyze cost per unit', 'category' => 'site', 'subcategory' => 'productivity', 'version' => '1.0', 'inputs' => [['name' => 'cost', 'type' => 'number', 'label' => 'Total Cost', 'unit' => '$'], ['name' => 'qty', 'type' => 'number', 'label' => 'Total Quantity']], 'formulas' => ['cpu' => function($i) { return $i['qty'] > 0 ? $i['cost'] / $i['qty'] : 0; }], 'outputs' => [['name' => 'cpu', 'label' => 'Cost Per Unit', 'unit' => '$/unit', 'precision' => 2]]],

];
