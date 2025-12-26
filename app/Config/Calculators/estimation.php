<?php

/**
 * Estimation & Costing Calculators Configuration
 */

return [
    // ============================================
    // QUANTITY ESTIMATION (10 calculators)
    // ============================================
    'concrete-quantity' => [
        'name' => 'Concrete Quantity Estimation',
        'description' => 'Calculate concrete volume and material requirements',
        'category' => 'estimation',
        'subcategory' => 'quantity',
        'version' => '1.0',
        'inputs' => [['name' => 'length', 'type' => 'number', 'label' => 'Length', 'unit' => 'm', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['length']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total', 'unit' => 'm³']]
    ],
    'brickwork-quantity' => [
        'name' => 'Brickwork Quantity Estimation',
        'description' => 'Estimate bricks and mortar for walls',
        'category' => 'estimation',
        'subcategory' => 'quantity',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Wall Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total Bricks', 'unit' => 'nos']]
    ],
    'plaster-quantity' => [
        'name' => 'Plaster Quantity Estimation',
        'description' => 'Calculate plaster area and material volume',
        'category' => 'estimation',
        'subcategory' => 'quantity',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Surface Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total Plaster', 'unit' => 'm²']]
    ],
    'flooring-quantity' => [
        'name' => 'Flooring Quantity Estimation',
        'description' => 'Estimate tiles or marble for flooring',
        'category' => 'estimation',
        'subcategory' => 'quantity',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Floor Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total Area', 'unit' => 'm²']]
    ],
    'paint-quantity' => [
        'name' => 'Paint Quantity Estimation',
        'description' => 'Calculate paint requirements for surfaces',
        'category' => 'estimation',
        'subcategory' => 'quantity',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Surface Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total Paint', 'unit' => 'Liters']]
    ],
    'formwork-quantity' => [
        'name' => 'Formwork Quantity Estimation',
        'description' => 'Estimate shuttering and formwork area',
        'category' => 'estimation',
        'subcategory' => 'quantity',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Contact Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total Area', 'unit' => 'm²']]
    ],
    'rebar-quantity' => [
        'name' => 'Rebar Quantity Estimation',
        'description' => 'Calculate reinforcement steel requirements',
        'category' => 'estimation',
        'subcategory' => 'quantity',
        'version' => '1.0',
        'inputs' => [['name' => 'weight', 'type' => 'number', 'label' => 'Steel Weight', 'unit' => 'kg', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['weight']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total Steel', 'unit' => 'kg']]
    ],

    // ============================================
    // MATERIAL CALCULATORS (5 calculators)
    // ============================================
    'concrete-materials' => [
        'name' => 'Concrete Materials (Cement/Sand/Agg)',
        'description' => 'Break down concrete into constituent materials',
        'category' => 'estimation',
        'subcategory' => 'materials',
        'version' => '1.0',
        'inputs' => [['name' => 'volume', 'type' => 'number', 'label' => 'Concrete Volume', 'unit' => 'm³', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['volume']; }],
        'outputs' => [['name' => 'total', 'label' => 'Volume', 'unit' => 'm³']]
    ],
    'masonry-materials' => [
        'name' => 'Masonry Materials Calculator',
        'description' => 'Calculate cement and sand for brick/block work',
        'category' => 'estimation',
        'subcategory' => 'materials',
        'version' => '1.0',
        'inputs' => [['name' => 'volume', 'type' => 'number', 'label' => 'Mortar Volume', 'unit' => 'm³', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['volume']; }],
        'outputs' => [['name' => 'total', 'label' => 'Volume', 'unit' => 'm³']]
    ],
    'plaster-materials' => [
        'name' => 'Plaster Materials Calculator',
        'description' => 'Cement and sand requirement for plaster',
        'category' => 'estimation',
        'subcategory' => 'materials',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Plaster Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Area', 'unit' => 'm²']]
    ],
    'tile-materials' => [
        'name' => 'Tile & Grout Calculator',
        'description' => 'Calculate tiles, adhesive, and grout',
        'category' => 'estimation',
        'subcategory' => 'materials',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Floor Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Area', 'unit' => 'm²']]
    ],
    'paint-materials' => [
        'name' => 'Paint & Primer Calculator',
        'description' => 'Calculate paint, primer, and putty requirement',
        'category' => 'estimation',
        'subcategory' => 'materials',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Surface Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Area', 'unit' => 'm²']]
    ],

    // ============================================
    // RATE ANALYSIS (5 calculators)
    // ============================================
    'item-rate-analysis' => [
        'name' => 'Item Rate Analysis',
        'description' => 'Analyze unit rate for construction items',
        'category' => 'estimation',
        'subcategory' => 'rates',
        'version' => '1.0',
        'inputs' => [['name' => 'cost', 'type' => 'number', 'label' => 'Base Cost', 'unit' => 'Amt', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['cost']; }],
        'outputs' => [['name' => 'total', 'label' => 'Unit Rate', 'unit' => 'Amt/Unit']]
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
    // LABOR & MANPOWER (3 calculators)
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

    // ============================================
    // MACHINERY & FUEL (2 calculators)
    // ============================================
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
    // FINANCIAL ANALYSIS (5 calculators)
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
    // REPORTS (5 calculators)
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
