<?php

/**
 * Estimation & Costing Calculators Configuration
 * Focused on Enterprise BOQ, Rate Analysis, and Billing.
 */

return [
    // ============================================
    // RATE ANALYSIS (Enterprise - Phase 14)
    // ============================================
    'item-rate-analysis' => [
        'name' => 'Item Rate Analysis (Nepal Norms)',
        'description' => 'Detailed unit rate analysis based on Nepal Government (DUDBC) Norms',
        'category' => 'estimation',
        'subcategory' => 'rates',
        'version' => '1.0',
        'inputs' => [
            ['name' => 'item_type', 'type' => 'select', 'label' => 'Item Type', 'required' => true, 'options' => [
                'pcc_124' => 'PCC 1:2:4 (m³)',
                'rcc_1153' => 'RCC 1:1.5:3 (m³)',
                'brick_14' => 'Brickwork 1:4 (m³)',
                'brick_16' => 'Brickwork 1:6 (m³)',
                'plaster_12mm' => 'Plaster 1:4 (12.5mm) (m²)',
                'soil_normal' => 'Earthwork (Normal Soil) (m³)',
                'soil_hard' => 'Earthwork (Hard Soil) (m³)',
                'gabion_box' => 'Gabion Box (2x1x1m) (nos)',
                'gabion_fill' => 'Gabion Filling (m³)'
            ]],
            ['name' => 'quantity', 'type' => 'number', 'label' => 'Quantity', 'required' => true, 'default' => 1],
            ['name' => 'cement_price', 'type' => 'number', 'label' => 'Cement Price (per bag)', 'default' => 750],
            ['name' => 'sand_price', 'type' => 'number', 'label' => 'Sand Price (per m³)', 'default' => 2500],
            ['name' => 'agg_price', 'type' => 'number', 'label' => 'Aggregate Price (per m³)', 'default' => 3000],
            ['name' => 'brick_price', 'type' => 'number', 'label' => 'Brick Price (per nos)', 'default' => 18],
            ['name' => 'stone_price', 'type' => 'number', 'label' => 'Stone Price (per m³)', 'default' => 1800],
            ['name' => 'gi_wire_price', 'type' => 'number', 'label' => 'GI Wire Price (per kg)', 'default' => 120],
            ['name' => 'mason_rate', 'type' => 'number', 'label' => 'Mason Rate (per day)', 'default' => 1200],
            ['name' => 'labor_rate', 'type' => 'number', 'label' => 'Labor Rate (per day)', 'default' => 850],
            ['name' => 'overhead', 'type' => 'number', 'label' => 'Overhead & Profit (%)', 'default' => 15],
        ],
        'formulas' => [
            'analysis' => function($i) {
                $norms = require __DIR__ . '/../norms.php';
                $type = $i['item_type'];
                
                // Map input type to norms keys
                $map = [
                    'pcc_124' => ['cat' => 'concrete', 'key' => 'pcc_124'],
                    'rcc_1153' => ['cat' => 'concrete', 'key' => 'rcc_1153'],
                    'brick_14' => ['cat' => 'brickwork', 'key' => 'ratio_14'],
                    'brick_16' => ['cat' => 'brickwork', 'key' => 'ratio_16'],
                    'plaster_12mm' => ['cat' => 'plaster', 'key' => 'ratio_14_12mm'],
                    'soil_normal' => ['cat' => 'earthwork', 'key' => 'normal_soil'],
                    'soil_hard' => ['cat' => 'earthwork', 'key' => 'hard_soil'],
                    'gabion_box' => ['cat' => 'road_bridge', 'key' => 'gabion_2x1x1'],
                    'gabion_fill' => ['cat' => 'road_bridge', 'key' => 'gabion_filling']
                ];
                
                $conf = $norms[$map[$type]['cat']][$map[$type]['key']];
                $mat = $conf['materials'] ?? [];
                $lab = $conf['labor'] ?? [];
                $qty = $i['quantity'];
                
                $mat_cost = 0;
                if (isset($mat['cement_bags'])) $mat_cost += ($mat['cement_bags'] * $qty) * $i['cement_price'];
                if (isset($mat['sand'])) $mat_cost += ($mat['sand'] * $qty) * $i['sand_price'];
                if (isset($mat['aggregate'])) $mat_cost += ($mat['aggregate'] * $qty) * $i['agg_price'];
                if (isset($mat['bricks'])) $mat_cost += ($mat['bricks'] * $qty) * $i['brick_price'];
                if (isset($mat['stones'])) $mat_cost += ($mat['stones'] * $qty) * $i['stone_price'];
                if (isset($mat['gi_wire'])) $mat_cost += ($mat['gi_wire'] * $qty) * $i['gi_wire_price'];
                if (isset($mat['selvage_wire'])) $mat_cost += ($mat['selvage_wire'] * $qty) * $i['gi_wire_price'];
                
                $lab_cost = 0;
                if (isset($lab['mason']) || isset($lab['skilled_labor'])) {
                    $m_qty = ($lab['mason'] ?? 0) + ($lab['skilled_labor'] ?? 0);
                    $lab_cost += ($m_qty * $qty) * $i['mason_rate'];
                }
                if (isset($lab['laborer'])) $lab_cost += ($lab['laborer'] * $qty) * $i['labor_rate'];
                
                $direct_cost = $mat_cost + $lab_cost;
                $vat_profit = $direct_cost * ($i['overhead'] / 100);
                $total_cost = $direct_cost + $vat_profit;
                
                return [
                    'mat_total' => $mat_cost,
                    'lab_total' => $lab_cost,
                    'direct' => $direct_cost,
                    'final' => $total_cost,
                    'per_unit' => $total_cost / $qty
                ];
            }
        ],
        'outputs' => [
            ['name' => 'analysis', 'label' => 'Rate Breakdown', 'type' => 'table']
        ]
    ],
    'boq-preparation' => [
        'name' => 'BOQ Preparation Tool',
        'description' => 'Prepare Bill of Quantities',
        'category' => 'estimation',
        'subcategory' => 'rates',
        'version' => '1.0',
        'inputs' => [['name' => 'total', 'type' => 'number', 'label' => 'Total Items', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['total']; }],
        'outputs' => [['name' => 'total', 'label' => 'Count', 'unit' => 'nos']]
    ],
    'project-cost-summary' => [
        'name' => 'Project Cost Summary',
        'description' => 'Overall project budget estimation',
        'category' => 'estimation',
        'subcategory' => 'rates',
        'version' => '1.0',
        'inputs' => [['name' => 'budget', 'type' => 'number', 'label' => 'Estimated Budget', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['budget']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total', 'unit' => 'Amt']]
    ],
    'contingency-overheads' => [
        'name' => 'Contingency & Overheads',
        'description' => 'Calculate extra costs and profit margins',
        'category' => 'estimation',
        'subcategory' => 'rates',
        'version' => '1.0',
        'inputs' => [['name' => 'cost', 'type' => 'number', 'label' => 'Direct Cost', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['cost'] * 0.15; }],
        'outputs' => [['name' => 'total', 'label' => 'Extra', 'unit' => 'Amt']]
    ],
    'labor-rate-analysis' => [
        'name' => 'Labor Rate Analysis',
        'description' => 'Calculate labor cost per unit of work',
        'category' => 'estimation',
        'subcategory' => 'rates',
        'version' => '1.0',
        'inputs' => [['name' => 'rate', 'type' => 'number', 'label' => 'Daily Rate', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['rate']; }],
        'outputs' => [['name' => 'total', 'label' => 'Rate', 'unit' => 'Amt/day']]
    ],

    // ============================================
    // LABOR & MANPOWER
    // ============================================
    'manpower-requirement' => [
        'name' => 'Manpower Requirement',
        'description' => 'Estimate total workers needed for project',
        'category' => 'estimation',
        'subcategory' => 'labor',
        'version' => '1.0',
        'inputs' => [['name' => 'days', 'type' => 'number', 'label' => 'Project Duration', 'unit' => 'days', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['days']; }],
        'outputs' => [['name' => 'total', 'label' => 'Workers', 'unit' => 'nos']]
    ],
    'labor-hour-calculation' => [
        'name' => 'Labor Hour Calculation',
        'description' => 'Calculate total man-hours required',
        'category' => 'estimation',
        'subcategory' => 'labor',
        'version' => '1.0',
        'inputs' => [['name' => 'effort', 'type' => 'number', 'label' => 'Total Effort', 'unit' => 'man-days', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['effort'] * 8; }],
        'outputs' => [['name' => 'total', 'label' => 'Man-Hours', 'unit' => 'hrs']]
    ],

    // ============================================
    // MACHINERY & FUEL
    // ============================================
    'equipment-hourly-rate' => [
        'name' => 'Equipment Hourly Rate',
        'description' => 'Calculate hourly cost of machinery',
        'category' => 'estimation',
        'subcategory' => 'machinery',
        'version' => '1.0',
        'inputs' => [['name' => 'cost', 'type' => 'number', 'label' => 'Purchase Price', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['cost'] / 2000; }],
        'outputs' => [['name' => 'total', 'label' => 'Rate', 'unit' => 'Amt/hr']]
    ],
    'machinery-usage' => [
        'name' => 'Machinery Usage Estimator',
        'description' => 'Estimate total machinery hours for task',
        'category' => 'estimation',
        'subcategory' => 'machinery',
        'version' => '1.0',
        'inputs' => [['name' => 'hours', 'type' => 'number', 'label' => 'Usage Hours', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['hours']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total', 'unit' => 'hrs']]
    ],
    'fuel-consumption' => [
        'name' => 'Fuel Consumption Calculator',
        'description' => 'Calculate fuel requirement for machinery',
        'category' => 'estimation',
        'subcategory' => 'machinery',
        'version' => '1.0',
        'inputs' => [['name' => 'hours', 'type' => 'number', 'label' => 'Operation Hours', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['hours'] * 2.5; }],
        'outputs' => [['name' => 'total', 'label' => 'Fuel', 'unit' => 'Liters']]
    ],

    // ============================================
    // FINANCIAL ANALYSIS
    // ============================================
    'cash-flow-analysis' => [
        'name' => 'Cash Flow Analysis',
        'description' => 'Project monthly cash inflows and outflows',
        'category' => 'estimation',
        'subcategory' => 'financial',
        'version' => '1.0',
        'inputs' => [['name' => 'income', 'type' => 'number', 'label' => 'Projected Income', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['income']; }],
        'outputs' => [['name' => 'total', 'label' => 'Balance', 'unit' => 'Amt']]
    ],
    'profit-loss-analysis' => [
        'name' => 'Profit & Loss Analysis',
        'description' => 'Calculate projected profit margins',
        'category' => 'estimation',
        'subcategory' => 'financial',
        'version' => '1.0',
        'inputs' => [['name' => 'revenue', 'type' => 'number', 'label' => 'Total Revenue', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['revenue'] * 0.1; }],
        'outputs' => [['name' => 'total', 'label' => 'Profit', 'unit' => 'Amt']]
    ],
    'npv-irr-analysis' => [
        'name' => 'NPV / IRR Analysis',
        'description' => 'Net Present Value and Internal Rate of Return',
        'category' => 'estimation',
        'subcategory' => 'financial',
        'version' => '1.0',
        'inputs' => [['name' => 'investment', 'type' => 'number', 'label' => 'Initial Investment', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['investment']; }],
        'outputs' => [['name' => 'total', 'label' => 'NPV', 'unit' => 'Amt']]
    ],
    'bid-price-comparison' => [
        'name' => 'Bid Price Comparison',
        'description' => 'Compare multiple vendor bids',
        'category' => 'estimation',
        'subcategory' => 'financial',
        'version' => '1.0',
        'inputs' => [['name' => 'bid', 'type' => 'number', 'label' => 'Bid Amount', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['bid']; }],
        'outputs' => [['name' => 'total', 'label' => 'Difference', 'unit' => 'Amt']]
    ],
    'bid-sheet-generator' => [
        'name' => 'Bid Sheet Generator',
        'description' => 'Generate standard bidding documents',
        'category' => 'estimation',
        'subcategory' => 'financial',
        'version' => '1.0',
        'inputs' => [['name' => 'count', 'type' => 'number', 'label' => 'Item Count', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['count']; }],
        'outputs' => [['name' => 'total', 'label' => 'Pages', 'unit' => 'nos']]
    ],

    // ============================================
    // REPORTS
    // ============================================
    'summary-report' => [
        'name' => 'Project Summary Report',
        'description' => 'Executive summary of project estimation',
        'category' => 'estimation',
        'subcategory' => 'reports',
        'version' => '1.0',
        'inputs' => [['name' => 'cost', 'type' => 'number', 'label' => 'Total Cost', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['cost']; }],
        'outputs' => [['name' => 'total', 'label' => 'Summary', 'unit' => 'Amt']]
    ],
    'detailed-boq-report' => [
        'name' => 'Detailed BOQ Report',
        'description' => 'Item-wise detailed breakdown report',
        'category' => 'estimation',
        'subcategory' => 'reports',
        'version' => '1.0',
        'inputs' => [['name' => 'pages', 'type' => 'number', 'label' => 'Estimated Pages', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['pages']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total', 'unit' => 'pages']]
    ],
    'financial-dashboard' => [
        'name' => 'Financial Dashboard',
        'description' => 'Visual summary of project financials',
        'category' => 'estimation',
        'subcategory' => 'reports',
        'version' => '1.0',
        'inputs' => [['name' => 'data', 'type' => 'number', 'label' => 'Data Points', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['data']; }],
        'outputs' => [['name' => 'total', 'label' => 'Widgets', 'unit' => 'nos']]
    ],
];
