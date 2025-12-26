<?php

/**
 * Structural Calculators Configuration
 * All structural engineering calculation tools
 * 
 * Note: These are simplified calculations for preliminary design.
 * Always verify with detailed analysis and local building codes.
 */

return [
    // ============================================
    // BEAM ANALYSIS (5 calculators)
    // ============================================
    
    'simply-supported-beam' => [
        'name' => 'Simply Supported Beam Calculator',
        'description' => 'Calculate reactions, moments, and deflections for simply supported beams',
        'category' => 'structural',
        'subcategory' => 'beam-analysis',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'span', 'type' => 'number', 'label' => 'Span Length', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'load', 'type' => 'number', 'label' => 'Uniform Load', 'unit' => 'kN/m', 'required' => true, 'min' => 0],
            ['name' => 'point_load', 'type' => 'number', 'label' => 'Point Load (center)', 'unit' => 'kN', 'required' => true, 'min' => 0, 'default' => 0],
        ],
        'formulas' => [
            'reaction' => function($inputs) {
                return ($inputs['load'] * $inputs['span'] / 2) + ($inputs['point_load'] / 2);
            },
            'max_moment' => function($inputs) {
                $udl_moment = $inputs['load'] * pow($inputs['span'], 2) / 8;
                $point_moment = $inputs['point_load'] * $inputs['span'] / 4;
                return $udl_moment + $point_moment;
            },
            'max_shear' => function($inputs, $results) {
                return $results['reaction'];
            },
        ],
        'outputs' => [
            ['name' => 'reaction', 'label' => 'Support Reaction', 'unit' => 'kN', 'precision' => 2],
            ['name' => 'max_moment', 'label' => 'Maximum Moment', 'unit' => 'kN·m', 'precision' => 2],
            ['name' => 'max_shear', 'label' => 'Maximum Shear', 'unit' => 'kN', 'precision' => 2],
        ],
    ],
    
    'cantilever-beam' => [
        'name' => 'Cantilever Beam Calculator',
        'description' => 'Calculate reactions and moments for cantilever beams',
        'category' => 'structural',
        'subcategory' => 'beam-analysis',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'length', 'type' => 'number', 'label' => 'Cantilever Length', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'load', 'type' => 'number', 'label' => 'Uniform Load', 'unit' => 'kN/m', 'required' => true, 'min' => 0],
            ['name' => 'end_load', 'type' => 'number', 'label' => 'End Point Load', 'unit' => 'kN', 'required' => true, 'min' => 0, 'default' => 0],
        ],
        'formulas' => [
            'reaction' => function($inputs) {
                return ($inputs['load'] * $inputs['length']) + $inputs['end_load'];
            },
            'max_moment' => function($inputs) {
                $udl_moment = $inputs['load'] * pow($inputs['length'], 2) / 2;
                $point_moment = $inputs['end_load'] * $inputs['length'];
                return $udl_moment + $point_moment;
            },
        ],
        'outputs' => [
            ['name' => 'reaction', 'label' => 'Fixed End Reaction', 'unit' => 'kN', 'precision' => 2],
            ['name' => 'max_moment', 'label' => 'Maximum Moment', 'unit' => 'kN·m', 'precision' => 2],
        ],
    ],
    
    'continuous-beam' => [
        'name' => 'Continuous Beam Calculator',
        'description' => 'Calculate reactions for continuous beams (2 spans)',
        'category' => 'structural',
        'subcategory' => 'beam-analysis',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'span1', 'type' => 'number', 'label' => 'Span 1 Length', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'span2', 'type' => 'number', 'label' => 'Span 2 Length', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'load', 'type' => 'number', 'label' => 'Uniform Load', 'unit' => 'kN/m', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'total_load' => function($inputs) {
                return $inputs['load'] * ($inputs['span1'] + $inputs['span2']);
            },
            'center_reaction' => function($inputs, $results) {
                // Simplified - actual requires moment distribution
                return $results['total_load'] * 0.625; // Approximate for equal spans
            },
            'end_reaction' => function($inputs, $results) {
                return ($results['total_load'] - $results['center_reaction']) / 2;
            },
        ],
        'outputs' => [
            ['name' => 'end_reaction', 'label' => 'End Support Reaction', 'unit' => 'kN', 'precision' => 2],
            ['name' => 'center_reaction', 'label' => 'Center Support Reaction', 'unit' => 'kN', 'precision' => 2],
            ['name' => 'total_load', 'label' => 'Total Load', 'unit' => 'kN', 'precision' => 2],
        ],
    ],
    
    'beam-design' => [
        'name' => 'Beam Design Calculator',
        'description' => 'Design reinforced concrete beam section',
        'category' => 'structural',
        'subcategory' => 'beam-analysis',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'moment', 'type' => 'number', 'label' => 'Design Moment', 'unit' => 'kN·m', 'required' => true, 'min' => 0],
            ['name' => 'width', 'type' => 'number', 'label' => 'Beam Width', 'unit' => 'mm', 'required' => true, 'min' => 0, 'default' => 300],
            ['name' => 'depth', 'type' => 'number', 'label' => 'Beam Depth', 'unit' => 'mm', 'required' => true, 'min' => 0, 'default' => 600],
            ['name' => 'fck', 'type' => 'number', 'label' => 'Concrete Strength (fck)', 'unit' => 'MPa', 'required' => true, 'min' => 0, 'default' => 25],
            ['name' => 'fy', 'type' => 'number', 'label' => 'Steel Yield Strength (fy)', 'unit' => 'MPa', 'required' => true, 'min' => 0, 'default' => 415],
        ],
        'formulas' => [
            'effective_depth' => function($inputs) {
                return $inputs['depth'] - 50; // Assuming 50mm cover
            },
            'required_ast' => function($inputs, $results) {
                // Simplified formula: Ast = M / (0.87 * fy * 0.9 * d)
                $M = $inputs['moment'] * 1000000; // Convert to N·mm
                $d = $results['effective_depth'];
                return $M / (0.87 * $inputs['fy'] * 0.9 * $d);
            },
            'min_ast' => function($inputs) {
                return 0.85 * $inputs['width'] * $inputs['depth'] / $inputs['fy'];
            },
            'ast_provided' => function($inputs, $results) {
                return max($results['required_ast'], $results['min_ast']);
            },
        ],
        'outputs' => [
            ['name' => 'effective_depth', 'label' => 'Effective Depth', 'unit' => 'mm', 'precision' => 0],
            ['name' => 'required_ast', 'label' => 'Required Steel Area', 'unit' => 'mm²', 'precision' => 0],
            ['name' => 'min_ast', 'label' => 'Minimum Steel Area', 'unit' => 'mm²', 'precision' => 0],
            ['name' => 'ast_provided', 'label' => 'Steel Area to Provide', 'unit' => 'mm²', 'precision' => 0],
        ],
    ],
    
    'beam-load-combination' => [
        'name' => 'Beam Load Combination',
        'description' => 'Calculate load combinations for beam design',
        'category' => 'structural',
        'subcategory' => 'beam-analysis',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'dead_load', 'type' => 'number', 'label' => 'Dead Load', 'unit' => 'kN/m', 'required' => true, 'min' => 0],
            ['name' => 'live_load', 'type' => 'number', 'label' => 'Live Load', 'unit' => 'kN/m', 'required' => true, 'min' => 0],
            ['name' => 'wind_load', 'type' => 'number', 'label' => 'Wind Load', 'unit' => 'kN/m', 'required' => true, 'min' => 0, 'default' => 0],
        ],
        'formulas' => [
            'lc1' => function($inputs) {
                return 1.5 * ($inputs['dead_load'] + $inputs['live_load']); // 1.5(DL + LL)
            },
            'lc2' => function($inputs) {
                return 1.2 * $inputs['dead_load'] + 1.6 * $inputs['live_load']; // 1.2DL + 1.6LL
            },
            'lc3' => function($inputs) {
                return 1.2 * $inputs['dead_load'] + 1.0 * $inputs['live_load'] + 1.0 * $inputs['wind_load']; // 1.2DL + LL + WL
            },
            'governing_load' => function($inputs, $results) {
                return max($results['lc1'], $results['lc2'], $results['lc3']);
            },
        ],
        'outputs' => [
            ['name' => 'lc1', 'label' => 'LC1: 1.5(DL+LL)', 'unit' => 'kN/m', 'precision' => 2],
            ['name' => 'lc2', 'label' => 'LC2: 1.2DL+1.6LL', 'unit' => 'kN/m', 'precision' => 2],
            ['name' => 'lc3', 'label' => 'LC3: 1.2DL+LL+WL', 'unit' => 'kN/m', 'precision' => 2],
            ['name' => 'governing_load', 'label' => 'Governing Load', 'unit' => 'kN/m', 'precision' => 2],
        ],
    ],
    
    // ============================================
    // COLUMN DESIGN (5 calculators)
    // ============================================
    
    'short-column' => [
        'name' => 'Short Column Design',
        'description' => 'Design short axially loaded column',
        'category' => 'structural',
        'subcategory' => 'column-design',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'load', 'type' => 'number', 'label' => 'Axial Load', 'unit' => 'kN', 'required' => true, 'min' => 0],
            ['name' => 'fck', 'type' => 'number', 'label' => 'Concrete Strength', 'unit' => 'MPa', 'required' => true, 'min' => 0, 'default' => 25],
            ['name' => 'fy', 'type' => 'number', 'label' => 'Steel Strength', 'unit' => 'MPa', 'required' => true, 'min' => 0, 'default' => 415],
            ['name' => 'steel_percent', 'type' => 'number', 'label' => 'Steel Percentage', 'unit' => '%', 'required' => true, 'min' => 0.8, 'max' => 6, 'default' => 2],
        ],
        'formulas' => [
            'required_area' => function($inputs) {
                $Pu = $inputs['load'] * 1000; // Convert to N
                $p = $inputs['steel_percent'] / 100;
                return $Pu / (0.4 * $inputs['fck'] + 0.67 * $inputs['fy'] * $p);
            },
            'column_size' => function($inputs, $results) {
                return ceil(sqrt($results['required_area']) / 25) * 25; // Round to 25mm
            },
            'ast' => function($inputs, $results) {
                return ($inputs['steel_percent'] / 100) * pow($results['column_size'], 2);
            },
        ],
        'outputs' => [
            ['name' => 'required_area', 'label' => 'Required Gross Area', 'unit' => 'mm²', 'precision' => 0],
            ['name' => 'column_size', 'label' => 'Column Size (Square)', 'unit' => 'mm', 'precision' => 0],
            ['name' => 'ast', 'label' => 'Steel Area Required', 'unit' => 'mm²', 'precision' => 0],
        ],
    ],
    
    'long-column' => [
        'name' => 'Long Column Design',
        'description' => 'Design slender column with buckling effects',
        'category' => 'structural',
        'subcategory' => 'column-design',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'load', 'type' => 'number', 'label' => 'Axial Load', 'unit' => 'kN', 'required' => true, 'min' => 0],
            ['name' => 'height', 'type' => 'number', 'label' => 'Unsupported Height', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'size', 'type' => 'number', 'label' => 'Column Size', 'unit' => 'mm', 'required' => true, 'min' => 0, 'default' => 300],
        ],
        'formulas' => [
            'slenderness_ratio' => function($inputs) {
                $lef = $inputs['height'] * 1000; // Effective length
                $d = $inputs['size'];
                return $lef / $d;
            },
            'is_slender' => function($inputs, $results) {
                return $results['slenderness_ratio'] > 12 ? 'Yes - Slender Column' : 'No - Short Column';
            },
            'reduction_factor' => function($inputs, $results) {
                if ($results['slenderness_ratio'] <= 12) return 1.0;
                return max(0.7, 1 - 0.03 * ($results['slenderness_ratio'] - 12));
            },
            'reduced_capacity' => function($inputs, $results) {
                return $inputs['load'] / $results['reduction_factor'];
            },
        ],
        'outputs' => [
            ['name' => 'slenderness_ratio', 'label' => 'Slenderness Ratio', 'unit' => '', 'precision' => 1],
            ['name' => 'is_slender', 'label' => 'Column Type', 'unit' => '', 'precision' => 0],
            ['name' => 'reduction_factor', 'label' => 'Capacity Reduction Factor', 'unit' => '', 'precision' => 3],
            ['name' => 'reduced_capacity', 'label' => 'Design Load (Factored)', 'unit' => 'kN', 'precision' => 2],
        ],
    ],
    
    'biaxial-column' => [
        'name' => 'Biaxial Column Design',
        'description' => 'Column with bending in both axes',
        'category' => 'structural',
        'subcategory' => 'column-design',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'axial_load', 'type' => 'number', 'label' => 'Axial Load', 'unit' => 'kN', 'required' => true, 'min' => 0],
            ['name' => 'moment_x', 'type' => 'number', 'label' => 'Moment about X-axis', 'unit' => 'kN·m', 'required' => true, 'min' => 0],
            ['name' => 'moment_y', 'type' => 'number', 'label' => 'Moment about Y-axis', 'unit' => 'kN·m', 'required' => true, 'min' => 0],
        ],
        'formulas' => [
            'equivalent_moment' => function($inputs) {
                // Simplified interaction formula
                return sqrt(pow($inputs['moment_x'], 2) + pow($inputs['moment_y'], 2));
            },
            'eccentricity_x' => function($inputs) {
                return $inputs['axial_load'] > 0 ? ($inputs['moment_x'] / $inputs['axial_load']) * 1000 : 0; // mm
            },
            'eccentricity_y' => function($inputs) {
                return $inputs['axial_load'] > 0 ? ($inputs['moment_y'] / $inputs['axial_load']) * 1000 : 0; // mm
            },
        ],
        'outputs' => [
            ['name' => 'equivalent_moment', 'label' => 'Equivalent Uniaxial Moment', 'unit' => 'kN·m', 'precision' => 2],
            ['name' => 'eccentricity_x', 'label' => 'Eccentricity (X-axis)', 'unit' => 'mm', 'precision' => 1],
            ['name' => 'eccentricity_y', 'label' => 'Eccentricity (Y-axis)', 'unit' => 'mm', 'precision' => 1],
        ],
    ],
    
    'steel-column-design' => [
        'name' => 'Steel Column Design',
        'description' => 'Design steel column section',
        'category' => 'structural',
        'subcategory' => 'column-design',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'load', 'type' => 'number', 'label' => 'Axial Load', 'unit' => 'kN', 'required' => true, 'min' => 0],
            ['name' => 'length', 'type' => 'number', 'label' => 'Unsupported Length', 'unit' => 'm', 'required' => true, 'min' => 0],
            ['name' => 'fy', 'type' => 'number', 'label' => 'Steel Yield Strength', 'unit' => 'MPa', 'required' => true, 'min' => 0, 'default' => 250],
        ],
        'formulas' => [
            'required_area' => function($inputs) {
                // Simplified - assumes short column
                return ($inputs['load'] * 1000) / (0.6 * $inputs['fy']); // mm²
            },
            'radius_of_gyration' => function($inputs, $results) {
                // Assume r = 0.3 * sqrt(A) for preliminary design
                return 0.3 * sqrt($results['required_area']);
            },
            'slenderness' => function($inputs, $results) {
                return ($inputs['length'] * 1000) / $results['radius_of_gyration'];
            },
        ],
        'outputs' => [
            ['name' => 'required_area', 'label' => 'Required Section Area', 'unit' => 'mm²', 'precision' => 0],
            ['name' => 'radius_of_gyration', 'label' => 'Radius of Gyration', 'unit' => 'mm', 'precision' => 1],
            ['name' => 'slenderness', 'label' => 'Slenderness Ratio', 'unit' => '', 'precision' => 1],
        ],
    ],
    
    'column-footing-link' => [
        'name' => 'Column-Footing Connection',
        'description' => 'Design column to footing connection',
        'category' => 'structural',
        'subcategory' => 'column-design',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'column_load', 'type' => 'number', 'label' => 'Column Load', 'unit' => 'kN', 'required' => true, 'min' => 0],
            ['name' => 'column_size', 'type' => 'number', 'label' => 'Column Size', 'unit' => 'mm', 'required' => true, 'min' => 0],
            ['name' => 'fck', 'type' => 'number', 'label' => 'Concrete Strength', 'unit' => 'MPa', 'required' => true, 'min' => 0, 'default' => 25],
        ],
        'formulas' => [
            'bearing_stress' => function($inputs) {
                $area = pow($inputs['column_size'], 2);
                return ($inputs['column_load'] * 1000) / $area; // MPa
            },
            'dowel_area' => function($inputs) {
                // 0.5% of column area minimum
                return 0.005 * pow($inputs['column_size'], 2);
            },
            'development_length' => function($inputs) {
                // Simplified: 40 times bar diameter (assume 16mm bars)
                return 40 * 16; // mm
            },
        ],
        'outputs' => [
            ['name' => 'bearing_stress', 'label' => 'Bearing Stress', 'unit' => 'MPa', 'precision' => 2],
            ['name' => 'dowel_area', 'label' => 'Minimum Dowel Area', 'unit' => 'mm²', 'precision' => 0],
            ['name' => 'development_length', 'label' => 'Development Length', 'unit' => 'mm', 'precision' => 0],
        ],
    ],
    
    // Foundation Design (5), Slab Design (5), Load Analysis (5), 
    // Reinforcement (5), Steel Structure (5), Reports (5) = 30 more
    // Simplified versions for MVP - user can expand later
    
    'isolated-footing' => ['name' => 'Isolated Footing', 'description' => 'Design isolated column footing', 'category' => 'structural', 'subcategory' => 'foundation-design', 'version' => '1.0', 'inputs' => [['name' => 'load', 'type' => 'number', 'label' => 'Column Load', 'unit' => 'kN', 'required' => true, 'min' => 0], ['name' => 'sbc', 'type' => 'number', 'label' => 'Safe Bearing Capacity', 'unit' => 'kN/m²', 'required' => true, 'min' => 0, 'default' => 150]], 'formulas' => ['area' => function($i) { return $i['load'] / $i['sbc']; }, 'size' => function($i, $r) { return ceil(sqrt($r['area']) * 10) / 10; }], 'outputs' => [['name' => 'area', 'label' => 'Required Area', 'unit' => 'm²', 'precision' => 2], ['name' => 'size', 'label' => 'Footing Size (Square)', 'unit' => 'm', 'precision' => 1]]],
    'combined-footing' => ['name' => 'Combined Footing', 'description' => 'Design combined footing for 2 columns', 'category' => 'structural', 'subcategory' => 'foundation-design', 'version' => '1.0', 'inputs' => [['name' => 'load1', 'type' => 'number', 'label' => 'Load 1', 'unit' => 'kN', 'required' => true, 'min' => 0], ['name' => 'load2', 'type' => 'number', 'label' => 'Load 2', 'unit' => 'kN', 'required' => true, 'min' => 0], ['name' => 'sbc', 'type' => 'number', 'label' => 'SBC', 'unit' => 'kN/m²', 'required' => true, 'min' => 0, 'default' => 150]], 'formulas' => ['total_load' => function($i) { return $i['load1'] + $i['load2']; }, 'area' => function($i, $r) { return $r['total_load'] / $i['sbc']; }], 'outputs' => [['name' => 'total_load', 'label' => 'Total Load', 'unit' => 'kN', 'precision' => 2], ['name' => 'area', 'label' => 'Required Area', 'unit' => 'm²', 'precision' => 2]]],
    'strap-footing' => ['name' => 'Strap Footing', 'description' => 'Design strap footing', 'category' => 'structural', 'subcategory' => 'foundation-design', 'version' => '1.0', 'inputs' => [['name' => 'load', 'type' => 'number', 'label' => 'Load', 'unit' => 'kN', 'required' => true, 'min' => 0], ['name' => 'sbc', 'type' => 'number', 'label' => 'SBC', 'unit' => 'kN/m²', 'required' => true, 'min' => 0, 'default' => 150]], 'formulas' => ['area' => function($i) { return $i['load'] / $i['sbc']; }], 'outputs' => [['name' => 'area', 'label' => 'Area', 'unit' => 'm²', 'precision' => 2]]],
    'pile-foundation' => ['name' => 'Pile Foundation', 'description' => 'Design pile foundation', 'category' => 'structural', 'subcategory' => 'foundation-design', 'version' => '1.0', 'inputs' => [['name' => 'load', 'type' => 'number', 'label' => 'Total Load', 'unit' => 'kN', 'required' => true, 'min' => 0], ['name' => 'pile_capacity', 'type' => 'number', 'label' => 'Pile Capacity', 'unit' => 'kN', 'required' => true, 'min' => 0, 'default' => 200]], 'formulas' => ['num_piles' => function($i) { return ceil($i['load'] / $i['pile_capacity']); }], 'outputs' => [['name' => 'num_piles', 'label' => 'Number of Piles', 'unit' => '', 'precision' => 0]]],
    'mat-foundation' => ['name' => 'Mat Foundation', 'description' => 'Design mat foundation', 'category' => 'structural', 'subcategory' => 'foundation-design', 'version' => '1.0', 'inputs' => [['name' => 'load', 'type' => 'number', 'label' => 'Total Load', 'unit' => 'kN', 'required' => true, 'min' => 0], ['name' => 'sbc', 'type' => 'number', 'label' => 'SBC', 'unit' => 'kN/m²', 'required' => true, 'min' => 0, 'default' => 150]], 'formulas' => ['area' => function($i) { return $i['load'] / $i['sbc']; }], 'outputs' => [['name' => 'area', 'label' => 'Mat Area', 'unit' => 'm²', 'precision' => 2]]],
    
    'one-way-slab' => ['name' => 'One Way Slab', 'description' => 'Design one-way slab', 'category' => 'structural', 'subcategory' => 'slab-design', 'version' => '1.0', 'inputs' => [['name' => 'span', 'type' => 'number', 'label' => 'Span', 'unit' => 'm', 'required' => true, 'min' => 0], ['name' => 'load', 'type' => 'number', 'label' => 'Load', 'unit' => 'kN/m²', 'required' => true, 'min' => 0]], 'formulas' => ['thickness' => function($i) { return ceil($i['span'] * 1000 / 20); }, 'moment' => function($i, $r) { return $i['load'] * pow($i['span'], 2) / 8; }], 'outputs' => [['name' => 'thickness', 'label' => 'Slab Thickness', 'unit' => 'mm', 'precision' => 0], ['name' => 'moment', 'label' => 'Max Moment', 'unit' => 'kN·m/m', 'precision' => 2]]],
    'two-way-slab' => ['name' => 'Two Way Slab', 'description' => 'Design two-way slab', 'category' => 'structural', 'subcategory' => 'slab-design', 'version' => '1.0', 'inputs' => [['name' => 'lx', 'type' => 'number', 'label' => 'Short Span', 'unit' => 'm', 'required' => true, 'min' => 0], ['name' => 'ly', 'type' => 'number', 'label' => 'Long Span', 'unit' => 'm', 'required' => true, 'min' => 0]], 'formulas' => ['ratio' => function($i) { return $i['ly'] / $i['lx']; }, 'thickness' => function($i) { return ceil($i['lx'] * 1000 / 28); }], 'outputs' => [['name' => 'ratio', 'label' => 'Span Ratio', 'unit' => '', 'precision' => 2], ['name' => 'thickness', 'label' => 'Thickness', 'unit' => 'mm', 'precision' => 0]]],
    'flat-slab' => ['name' => 'Flat Slab', 'description' => 'Design flat slab', 'category' => 'structural', 'subcategory' => 'slab-design', 'version' => '1.0', 'inputs' => [['name' => 'span', 'type' => 'number', 'label' => 'Span', 'unit' => 'm', 'required' => true, 'min' => 0]], 'formulas' => ['thickness' => function($i) { return ceil($i['span'] * 1000 / 32); }], 'outputs' => [['name' => 'thickness', 'label' => 'Thickness', 'unit' => 'mm', 'precision' => 0]]],
    'waffle-slab' => ['name' => 'Waffle Slab', 'description' => 'Design waffle slab', 'category' => 'structural', 'subcategory' => 'slab-design', 'version' => '1.0', 'inputs' => [['name' => 'span', 'type' => 'number', 'label' => 'Span', 'unit' => 'm', 'required' => true, 'min' => 0]], 'formulas' => ['depth' => function($i) { return ceil($i['span'] * 1000 / 20); }], 'outputs' => [['name' => 'depth', 'label' => 'Total Depth', 'unit' => 'mm', 'precision' => 0]]],
    'cantilever-slab' => ['name' => 'Cantilever Slab', 'description' => 'Design cantilever slab', 'category' => 'structural', 'subcategory' => 'slab-design', 'version' => '1.0', 'inputs' => [['name' => 'length', 'type' => 'number', 'label' => 'Length', 'unit' => 'm', 'required' => true, 'min' => 0]], 'formulas' => ['thickness' => function($i) { return ceil($i['length'] * 1000 / 10); }], 'outputs' => [['name' => 'thickness', 'label' => 'Thickness', 'unit' => 'mm', 'precision' => 0]]],
    
    'dead-load' => ['name' => 'Dead Load Calculator', 'description' => 'Calculate dead loads', 'category' => 'structural', 'subcategory' => 'load-analysis', 'version' => '1.0', 'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Area', 'unit' => 'm²', 'required' => true, 'min' => 0], ['name' => 'unit_weight', 'type' => 'number', 'label' => 'Unit Weight', 'unit' => 'kN/m²', 'required' => true, 'min' => 0, 'default' => 5]], 'formulas' => ['total_load' => function($i) { return $i['area'] * $i['unit_weight']; }], 'outputs' => [['name' => 'total_load', 'label' => 'Total Dead Load', 'unit' => 'kN', 'precision' => 2]]],
    'live-load' => ['name' => 'Live Load Calculator', 'description' => 'Calculate live loads', 'category' => 'structural', 'subcategory' => 'load-analysis', 'version' => '1.0', 'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Area', 'unit' => 'm²', 'required' => true, 'min' => 0], ['name' => 'occupancy', 'type' => 'select', 'label' => 'Occupancy', 'required' => true, 'options' => ['residential' => 'Residential (2 kN/m²)', 'office' => 'Office (3 kN/m²)', 'assembly' => 'Assembly (5 kN/m²)']]], 'formulas' => ['unit_ll' => function($i) { $ll = ['residential' => 2, 'office' => 3, 'assembly' => 5]; return $ll[$i['occupancy']] ?? 2; }, 'total_load' => function($i, $r) { return $i['area'] * $r['unit_ll']; }], 'outputs' => [['name' => 'unit_ll', 'label' => 'Unit Live Load', 'unit' => 'kN/m²', 'precision' => 1], ['name' => 'total_load', 'label' => 'Total Live Load', 'unit' => 'kN', 'precision' => 2]]],
    'wind-load' => ['name' => 'Wind Load Calculator', 'description' => 'Calculate wind loads', 'category' => 'structural', 'subcategory' => 'load-analysis', 'version' => '1.0', 'inputs' => [['name' => 'height', 'type' => 'number', 'label' => 'Height', 'unit' => 'm', 'required' => true, 'min' => 0], ['name' => 'wind_speed', 'type' => 'number', 'label' => 'Wind Speed', 'unit' => 'm/s', 'required' => true, 'min' => 0, 'default' => 40]], 'formulas' => ['pressure' => function($i) { return 0.6 * pow($i['wind_speed'], 2) / 1000; }], 'outputs' => [['name' => 'pressure', 'label' => 'Wind Pressure', 'unit' => 'kN/m²', 'precision' => 2]]],
    'seismic-load' => ['name' => 'Seismic Load Calculator', 'description' => 'Calculate seismic loads', 'category' => 'structural', 'subcategory' => 'load-analysis', 'version' => '1.0', 'inputs' => [['name' => 'weight', 'type' => 'number', 'label' => 'Seismic Weight', 'unit' => 'kN', 'required' => true, 'min' => 0], ['name' => 'zone_factor', 'type' => 'number', 'label' => 'Zone Factor', 'unit' => '', 'required' => true, 'min' => 0, 'default' => 0.36]], 'formulas' => ['base_shear' => function($i) { return $i['weight'] * $i['zone_factor'] * 0.5; }], 'outputs' => [['name' => 'base_shear', 'label' => 'Base Shear', 'unit' => 'kN', 'precision' => 2]]],
    'load-combination' => ['name' => 'Load Combination', 'description' => 'Combine loads per code', 'category' => 'structural', 'subcategory' => 'load-analysis', 'version' => '1.0', 'inputs' => [['name' => 'dl', 'type' => 'number', 'label' => 'Dead Load', 'unit' => 'kN', 'required' => true, 'min' => 0], ['name' => 'll', 'type' => 'number', 'label' => 'Live Load', 'unit' => 'kN', 'required' => true, 'min' => 0]], 'formulas' => ['lc1' => function($i) { return 1.5 * ($i['dl'] + $i['ll']); }, 'lc2' => function($i) { return 1.2 * $i['dl'] + 1.6 * $i['ll']; }], 'outputs' => [['name' => 'lc1', 'label' => 'LC1', 'unit' => 'kN', 'precision' => 2], ['name' => 'lc2', 'label' => 'LC2', 'unit' => 'kN', 'precision' => 2]]],
    
    'rebar-spacing' => ['name' => 'Rebar Spacing', 'description' => 'Calculate rebar spacing', 'category' => 'structural', 'subcategory' => 'reinforcement', 'version' => '1.0', 'inputs' => [['name' => 'ast', 'type' => 'number', 'label' => 'Steel Area Required', 'unit' => 'mm²/m', 'required' => true, 'min' => 0], ['name' => 'bar_dia', 'type' => 'number', 'label' => 'Bar Diameter', 'unit' => 'mm', 'required' => true, 'min' => 0, 'default' => 12]], 'formulas' => ['bar_area' => function($i) { return 3.14159 * pow($i['bar_dia'], 2) / 4; }, 'spacing' => function($i, $r) { return 1000 * $r['bar_area'] / $i['ast']; }], 'outputs' => [['name' => 'bar_area', 'label' => 'Bar Area', 'unit' => 'mm²', 'precision' => 1], ['name' => 'spacing', 'label' => 'Spacing', 'unit' => 'mm', 'precision' => 0]]],
    'development-length' => ['name' => 'Development Length', 'description' => 'Calculate development length', 'category' => 'structural', 'subcategory' => 'reinforcement', 'version' => '1.0', 'inputs' => [['name' => 'dia', 'type' => 'number', 'label' => 'Bar Diameter', 'unit' => 'mm', 'required' => true, 'min' => 0], ['name' => 'fy', 'type' => 'number', 'label' => 'Steel Strength', 'unit' => 'MPa', 'required' => true, 'min' => 0, 'default' => 415]], 'formulas' => ['ld' => function($i) { return 0.87 * $i['fy'] * $i['dia'] / 4; }], 'outputs' => [['name' => 'ld', 'label' => 'Development Length', 'unit' => 'mm', 'precision' => 0]]],
    'lap-length' => ['name' => 'Lap Length', 'description' => 'Calculate lap length', 'category' => 'structural', 'subcategory' => 'reinforcement', 'version' => '1.0', 'inputs' => [['name' => 'ld', 'type' => 'number', 'label' => 'Development Length', 'unit' => 'mm', 'required' => true, 'min' => 0]], 'formulas' => ['lap' => function($i) { return $i['ld'] * 1.3; }], 'outputs' => [['name' => 'lap', 'label' => 'Lap Length', 'unit' => 'mm', 'precision' => 0]]],
    'stirrup-spacing' => ['name' => 'Stirrup Spacing', 'description' => 'Calculate stirrup spacing', 'category' => 'structural', 'subcategory' => 'reinforcement', 'version' => '1.0', 'inputs' => [['name' => 'shear', 'type' => 'number', 'label' => 'Shear Force', 'unit' => 'kN', 'required' => true, 'min' => 0], ['name' => 'depth', 'type' => 'number', 'label' => 'Effective Depth', 'unit' => 'mm', 'required' => true, 'min' => 0]], 'formulas' => ['spacing' => function($i) { return min(0.75 * $i['depth'], 300); }], 'outputs' => [['name' => 'spacing', 'label' => 'Max Spacing', 'unit' => 'mm', 'precision' => 0]]],
    'anchorage-length' => ['name' => 'Anchorage Length', 'description' => 'Calculate anchorage length', 'category' => 'structural', 'subcategory' => 'reinforcement', 'version' => '1.0', 'inputs' => [['name' => 'dia', 'type' => 'number', 'label' => 'Bar Diameter', 'unit' => 'mm', 'required' => true, 'min' => 0]], 'formulas' => ['length' => function($i) { return 16 * $i['dia']; }], 'outputs' => [['name' => 'length', 'label' => 'Anchorage Length', 'unit' => 'mm', 'precision' => 0]]],
    
    'steel-beam' => ['name' => 'Steel Beam Design', 'description' => 'Design steel beam', 'category' => 'structural', 'subcategory' => 'steel-structure', 'version' => '1.0', 'inputs' => [['name' => 'moment', 'type' => 'number', 'label' => 'Moment', 'unit' => 'kN·m', 'required' => true, 'min' => 0]], 'formulas' => ['section_modulus' => function($i) { return $i['moment'] * 1000000 / 165; }], 'outputs' => [['name' => 'section_modulus', 'label' => 'Required Z', 'unit' => 'mm³', 'precision' => 0]]],
    'steel-truss' => ['name' => 'Steel Truss', 'description' => 'Analyze steel truss', 'category' => 'structural', 'subcategory' => 'steel-structure', 'version' => '1.0', 'inputs' => [['name' => 'span', 'type' => 'number', 'label' => 'Span', 'unit' => 'm', 'required' => true, 'min' => 0]], 'formulas' => ['depth' => function($i) { return $i['span'] / 5; }], 'outputs' => [['name' => 'depth', 'label' => 'Truss Depth', 'unit' => 'm', 'precision' => 2]]],
    'connection-design' => ['name' => 'Connection Design', 'description' => 'Design steel connection', 'category' => 'structural', 'subcategory' => 'steel-structure', 'version' => '1.0', 'inputs' => [['name' => 'force', 'type' => 'number', 'label' => 'Force', 'unit' => 'kN', 'required' => true, 'min' => 0]], 'formulas' => ['bolt_capacity' => function($i) { return 50; }, 'num_bolts' => function($i, $r) { return ceil($i['force'] / $r['bolt_capacity']); }], 'outputs' => [['name' => 'num_bolts', 'label' => 'Number of Bolts', 'unit' => '', 'precision' => 0]]],
    'plate-girder' => ['name' => 'Plate Girder', 'description' => 'Design plate girder', 'category' => 'structural', 'subcategory' => 'steel-structure', 'version' => '1.0', 'inputs' => [['name' => 'span', 'type' => 'number', 'label' => 'Span', 'unit' => 'm', 'required' => true, 'min' => 0]], 'formulas' => ['depth' => function($i) { return $i['span'] / 10; }], 'outputs' => [['name' => 'depth', 'label' => 'Girder Depth', 'unit' => 'm', 'precision' => 2]]],
    'composite-beam' => ['name' => 'Composite Beam', 'description' => 'Design composite beam', 'category' => 'structural', 'subcategory' => 'steel-structure', 'version' => '1.0', 'inputs' => [['name' => 'moment', 'type' => 'number', 'label' => 'Moment', 'unit' => 'kN·m', 'required' => true, 'min' => 0]], 'formulas' => ['section_modulus' => function($i) { return $i['moment'] * 1000000 / 200; }], 'outputs' => [['name' => 'section_modulus', 'label' => 'Required Z', 'unit' => 'mm³', 'precision' => 0]]],
    
    'quantity-takeoff' => ['name' => 'Quantity Takeoff', 'description' => 'Calculate quantities', 'category' => 'structural', 'subcategory' => 'reports', 'version' => '1.0', 'inputs' => [['name' => 'volume', 'type' => 'number', 'label' => 'Concrete Volume', 'unit' => 'm³', 'required' => true, 'min' => 0]], 'formulas' => ['cement' => function($i) { return $i['volume'] * 350; }, 'sand' => function($i) { return $i['volume'] * 0.45; }, 'aggregate' => function($i) { return $i['volume'] * 0.9; }], 'outputs' => [['name' => 'cement', 'label' => 'Cement', 'unit' => 'kg', 'precision' => 0], ['name' => 'sand', 'label' => 'Sand', 'unit' => 'm³', 'precision' => 2], ['name' => 'aggregate', 'label' => 'Aggregate', 'unit' => 'm³', 'precision' => 2]]],
    'cost-estimate' => ['name' => 'Cost Estimate', 'description' => 'Estimate structural cost', 'category' => 'structural', 'subcategory' => 'reports', 'version' => '1.0', 'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Built-up Area', 'unit' => 'm²', 'required' => true, 'min' => 0], ['name' => 'rate', 'type' => 'number', 'label' => 'Rate per m²', 'unit' => '$/m²', 'required' => true, 'min' => 0, 'default' => 500]], 'formulas' => ['total_cost' => function($i) { return $i['area'] * $i['rate']; }], 'outputs' => [['name' => 'total_cost', 'label' => 'Total Cost', 'unit' => '$', 'precision' => 0]]],
    'material-summary' => ['name' => 'Material Summary', 'description' => 'Summarize materials', 'category' => 'structural', 'subcategory' => 'reports', 'version' => '1.0', 'inputs' => [['name' => 'concrete', 'type' => 'number', 'label' => 'Concrete', 'unit' => 'm³', 'required' => true, 'min' => 0], ['name' => 'steel', 'type' => 'number', 'label' => 'Steel', 'unit' => 'kg', 'required' => true, 'min' => 0]], 'formulas' => ['total_weight' => function($i) { return $i['concrete'] * 2400 + $i['steel']; }], 'outputs' => [['name' => 'total_weight', 'label' => 'Total Weight', 'unit' => 'kg', 'precision' => 0]]],
    'bar-bending-schedule' => ['name' => 'Bar Bending Schedule', 'description' => 'Generate BBS', 'category' => 'structural', 'subcategory' => 'reports', 'version' => '1.0', 'inputs' => [['name' => 'length', 'type' => 'number', 'label' => 'Bar Length', 'unit' => 'm', 'required' => true, 'min' => 0], ['name' => 'num', 'type' => 'number', 'label' => 'Number of Bars', 'unit' => '', 'required' => true, 'min' => 0]], 'formulas' => ['total_length' => function($i) { return $i['length'] * $i['num']; }], 'outputs' => [['name' => 'total_length', 'label' => 'Total Length', 'unit' => 'm', 'precision' => 2]]],
    'structural-report' => ['name' => 'Structural Report', 'description' => 'Generate structural report', 'category' => 'structural', 'subcategory' => 'reports', 'version' => '1.0', 'inputs' => [['name' => 'floors', 'type' => 'number', 'label' => 'Number of Floors', 'unit' => '', 'required' => true, 'min' => 0]], 'formulas' => ['total_height' => function($i) { return $i['floors'] * 3; }], 'outputs' => [['name' => 'total_height', 'label' => 'Building Height', 'unit' => 'm', 'precision' => 1]]],
];
