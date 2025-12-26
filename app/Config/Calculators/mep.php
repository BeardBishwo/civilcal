<?php

/**
 * MEP (Mechanical, Electrical, Plumbing) Coordination Configuration
 */

return [
    // ============================================
    // ELECTRICAL (7 calculators)
    // ============================================
    'conduit-sizing' => [
        'name' => 'MEP Conduit Sizing',
        'description' => 'Size electrical conduits based on wire fill',
        'category' => 'mep',
        'subcategory' => 'electrical',
        'version' => '1.0',
        'inputs' => [['name' => 'wires', 'type' => 'number', 'label' => 'Total Wires', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['wires']; }],
        'outputs' => [['name' => 'total', 'label' => 'Diameter', 'unit' => 'in']]
    ],
    'earthing-system' => [
        'name' => 'Earthing System Design',
        'description' => 'Calculate earthing rod and strip requirements',
        'category' => 'mep',
        'subcategory' => 'electrical',
        'version' => '1.0',
        'inputs' => [['name' => 'current', 'type' => 'number', 'label' => 'Fault Current', 'unit' => 'kA', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['current']; }],
        'outputs' => [['name' => 'total', 'label' => 'Electrodes', 'unit' => 'nos']]
    ],
    'emergency-power' => [
        'name' => 'Emergency Power Calculation',
        'description' => 'Sizing for generator and UPS systems',
        'category' => 'mep',
        'subcategory' => 'electrical',
        'version' => '1.0',
        'inputs' => [['name' => 'load', 'type' => 'number', 'label' => 'Critical Load', 'unit' => 'kW', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['load']; }],
        'outputs' => [['name' => 'total', 'label' => 'Capacity', 'unit' => 'kVA']]
    ],
    'lighting-layout' => [
        'name' => 'Lighting Layout Planner',
        'description' => 'Calculate fixture spacing and illumination',
        'category' => 'mep',
        'subcategory' => 'electrical',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Illumination', 'unit' => 'Lux']]
    ],
    'mep-electrical-summary' => [
        'name' => 'MEP Electrical Summary',
        'description' => 'Total electrical demand for MEP system',
        'category' => 'mep',
        'subcategory' => 'electrical',
        'version' => '1.0',
        'inputs' => [['name' => 'demand', 'type' => 'number', 'label' => 'Total Demand', 'unit' => 'kW', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['demand']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total', 'unit' => 'kW']]
    ],
    'panel-schedule' => [
        'name' => 'Panel Schedule Generator',
        'description' => 'Generate electrical panel distributions',
        'category' => 'mep',
        'subcategory' => 'electrical',
        'version' => '1.0',
        'inputs' => [['name' => 'circuits', 'type' => 'number', 'label' => 'Circuits', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['circuits']; }],
        'outputs' => [['name' => 'total', 'label' => 'Slots', 'unit' => 'nos']]
    ],
    'transformer-sizing' => [
        'name' => 'Transformer Sizing',
        'description' => 'Calculate required transformer capacity',
        'category' => 'mep',
        'subcategory' => 'electrical',
        'version' => '1.0',
        'inputs' => [['name' => 'load', 'type' => 'number', 'label' => 'Total Load', 'unit' => 'kVA', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['load']; }],
        'outputs' => [['name' => 'total', 'label' => 'Size', 'unit' => 'kVA']]
    ],

    // ============================================
    // MECHANICAL (3 calculators)
    // ============================================
    'chilled-water-piping' => [
        'name' => 'Chilled Water Piping',
        'description' => 'Size pipes for chilled water systems',
        'category' => 'mep',
        'subcategory' => 'mechanical',
        'version' => '1.0',
        'inputs' => [['name' => 'flow', 'type' => 'number', 'label' => 'Flow Rate', 'unit' => 'GPM', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['flow']; }],
        'outputs' => [['name' => 'total', 'label' => 'Diameter', 'unit' => 'in']]
    ],
    'equipment-database' => [
        'name' => 'Equipment Database',
        'description' => 'MEP equipment technical specifications',
        'category' => 'mep',
        'subcategory' => 'mechanical',
        'version' => '1.0',
        'inputs' => [['name' => 'id', 'type' => 'number', 'label' => 'Equipment ID', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['id']; }],
        'outputs' => [['name' => 'total', 'label' => 'Found', 'unit' => '']]
    ],
    'hvac-duct-sizing' => [
        'name' => 'MEP HVAC Duct Sizing',
        'description' => 'Size ducts for coordinated MEP systems',
        'category' => 'mep',
        'subcategory' => 'mechanical',
        'version' => '1.0',
        'inputs' => [['name' => 'airflow', 'type' => 'number', 'label' => 'Airflow', 'unit' => 'CFM', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['airflow']; }],
        'outputs' => [['name' => 'total', 'label' => 'Size', 'unit' => 'in']]
    ],

    // ============================================
    // PLUMBING (6 calculators)
    // ============================================
    'drainage-system' => [
        'name' => 'Drainage System Design',
        'description' => 'Calculate drainage pipe sizes and slopes',
        'category' => 'mep',
        'subcategory' => 'plumbing',
        'version' => '1.0',
        'inputs' => [['name' => 'dfu', 'type' => 'number', 'label' => 'Total DFU', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['dfu']; }],
        'outputs' => [['name' => 'total', 'label' => 'Pipe Size', 'unit' => 'in']]
    ],
    'plumbing-fixture-count' => [
        'name' => 'Fixture Count Calculator',
        'description' => 'Determine required fixtures per code',
        'category' => 'mep',
        'subcategory' => 'plumbing',
        'version' => '1.0',
        'inputs' => [['name' => 'occupants', 'type' => 'number', 'label' => 'Occupants', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['occupants']; }],
        'outputs' => [['name' => 'total', 'label' => 'Fixtures', 'unit' => 'nos']]
    ],
    'pump-selection' => [
        'name' => 'Pump Selection Tool',
        'description' => 'Select appropriate pump for MEP system',
        'category' => 'mep',
        'subcategory' => 'plumbing',
        'version' => '1.0',
        'inputs' => [['name' => 'head', 'type' => 'number', 'label' => 'Total Head', 'unit' => 'm', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['head']; }],
        'outputs' => [['name' => 'total', 'label' => 'Power', 'unit' => 'kW']]
    ],
    'storm-water' => [
        'name' => 'Storm Water Management',
        'description' => 'Calculate runoff and storage for site',
        'category' => 'mep',
        'subcategory' => 'plumbing',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Site Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Runoff', 'unit' => 'l/s']]
    ],
    'water-supply' => [
        'name' => 'MEP Water Supply',
        'description' => 'Coordinated water supply distribution',
        'category' => 'mep',
        'subcategory' => 'plumbing',
        'version' => '1.0',
        'inputs' => [['name' => 'demand', 'type' => 'number', 'label' => 'Peak Demand', 'unit' => 'l/s', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['demand']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total', 'unit' => 'l/s']]
    ],
    'water-tank-sizing' => [
        'name' => 'MEP Water Tank Sizing',
        'description' => 'Size storage tanks for MEP plumbing',
        'category' => 'mep',
        'subcategory' => 'plumbing',
        'version' => '1.0',
        'inputs' => [['name' => 'demand', 'type' => 'number', 'label' => 'Daily Demand', 'unit' => 'L', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['demand']; }],
        'outputs' => [['name' => 'total', 'label' => 'Storage', 'unit' => 'L']]
    ],

    // ============================================
    // FIRE PROTECTION (4 calculators)
    // ============================================
    'fire-hydrant-system' => [
        'name' => 'Hydrant System Design',
        'description' => 'Calculate external hydrant flow and pressure',
        'category' => 'mep',
        'subcategory' => 'fire',
        'version' => '1.0',
        'inputs' => [['name' => 'hydrants', 'type' => 'number', 'label' => 'Number of Hydrants', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['hydrants']; }],
        'outputs' => [['name' => 'total', 'label' => 'Flow', 'unit' => 'GPM']]
    ],
    'fire-pump-sizing' => [
        'name' => 'MEP Fire Pump Sizing',
        'description' => 'Coordinated fire pump requirements',
        'category' => 'mep',
        'subcategory' => 'fire',
        'version' => '1.0',
        'inputs' => [['name' => 'flow', 'type' => 'number', 'label' => 'Required Flow', 'unit' => 'GPM', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['flow']; }],
        'outputs' => [['name' => 'total', 'label' => 'Power', 'unit' => 'HP']]
    ],
    'fire-safety-zoning' => [
        'name' => 'Fire Safety Zoning',
        'description' => 'Define fire zones and compartmentation',
        'category' => 'mep',
        'subcategory' => 'fire',
        'version' => '1.0',
        'inputs' => [['name' => 'area', 'type' => 'number', 'label' => 'Total Area', 'unit' => 'm²', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['area']; }],
        'outputs' => [['name' => 'total', 'label' => 'Zones', 'unit' => 'nos']]
    ],
    'fire-tank-sizing' => [
        'name' => 'Fire Water Tank',
        'description' => 'Size dedicated fire water storage tanks',
        'category' => 'mep',
        'subcategory' => 'fire',
        'version' => '1.0',
        'inputs' => [['name' => 'flow', 'type' => 'number', 'label' => 'Required Flow', 'unit' => 'GPM', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['flow'] * 60; }],
        'outputs' => [['name' => 'total', 'label' => 'Storage', 'unit' => 'Gallons']]
    ],

    // ============================================
    // SUSTAINABILITY (5 calculators)
    // ============================================
    'energy-consumption' => [
        'name' => 'Total MEP Energy',
        'description' => 'Aggregate energy consumption for all MEP systems',
        'category' => 'mep',
        'subcategory' => 'sustainability',
        'version' => '1.0',
        'inputs' => [['name' => 'kwh', 'type' => 'number', 'label' => 'Annual Use', 'unit' => 'kWh', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['kwh']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total', 'unit' => 'kWh']]
    ],
    'green-rating' => [
        'name' => 'Green Building Rating',
        'description' => 'Estimate LEED/IGBC points for MEP',
        'category' => 'mep',
        'subcategory' => 'sustainability',
        'version' => '1.0',
        'inputs' => [['name' => 'credits', 'type' => 'number', 'label' => 'Achieved Credits', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['credits']; }],
        'outputs' => [['name' => 'total', 'label' => 'Level', 'unit' => '']]
    ],
    'hvac-efficiency' => [
        'name' => 'HVAC System Efficiency',
        'description' => 'Analyze overall HVAC system performance',
        'category' => 'mep',
        'subcategory' => 'sustainability',
        'version' => '1.0',
        'inputs' => [['name' => 'input', 'type' => 'number', 'label' => 'Energy Input', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['input']; }],
        'outputs' => [['name' => 'total', 'label' => 'COP', 'unit' => '']]
    ],
    'solar-system' => [
        'name' => 'Solar Integration Tool',
        'description' => 'Calculate solar PV required for MEP load',
        'category' => 'mep',
        'subcategory' => 'sustainability',
        'version' => '1.0',
        'inputs' => [['name' => 'load', 'type' => 'number', 'label' => 'Daily Load', 'unit' => 'kWh', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['load'] / 4; }],
        'outputs' => [['name' => 'total', 'label' => 'Panels', 'unit' => 'kWp']]
    ],
    'water-efficiency' => [
        'name' => 'Water Stewardship',
        'description' => 'Project water savings through MEP fixtures',
        'category' => 'mep',
        'subcategory' => 'sustainability',
        'version' => '1.0',
        'inputs' => [['name' => 'use', 'type' => 'number', 'label' => 'Daily Use', 'unit' => 'L', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['use'] * 0.2; }],
        'outputs' => [['name' => 'total', 'label' => 'Savings', 'unit' => 'L']]
    ],

    // ============================================
    // BIM & COORDINATION (5 calculators)
    // ============================================
    'bim-export' => [
        'name' => 'BIM Metadata Exporter',
        'description' => 'Export MEP parameters to BIM models',
        'category' => 'mep',
        'subcategory' => 'coordination',
        'version' => '1.0',
        'inputs' => [['name' => 'elements', 'type' => 'number', 'label' => 'Element Count', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['elements']; }],
        'outputs' => [['name' => 'total', 'label' => 'Exported', 'unit' => 'nos']]
    ],
    'clash-detection' => [
        'name' => 'Clash Detection Helper',
        'description' => 'Analyze inter-disciplinary service clashes',
        'category' => 'mep',
        'subcategory' => 'coordination',
        'version' => '1.0',
        'inputs' => [['name' => 'clashes', 'type' => 'number', 'label' => 'Detected Clashes', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['clashes']; }],
        'outputs' => [['name' => 'total', 'label' => 'Remaining', 'unit' => 'nos']]
    ],
    'coordination-map' => [
        'name' => 'Service Coordination Map',
        'description' => 'Plan service routing and clearances',
        'category' => 'mep',
        'subcategory' => 'coordination',
        'version' => '1.0',
        'inputs' => [['name' => 'services', 'type' => 'number', 'label' => 'Service Count', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['services']; }],
        'outputs' => [['name' => 'total', 'label' => 'Layers', 'unit' => 'nos']]
    ],
    'space-allocation' => [
        'name' => 'MEP Space Allocation',
        'description' => 'Calculate space required for plant rooms',
        'category' => 'mep',
        'subcategory' => 'coordination',
        'version' => '1.0',
        'inputs' => [['name' => 'load', 'type' => 'number', 'label' => 'Total Equipment Load', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['load'] * 0.1; }],
        'outputs' => [['name' => 'total', 'label' => 'Area', 'unit' => 'm²']]
    ],
    'system-priority' => [
        'name' => 'System Priority Matrix',
        'description' => 'Define priority for service routing',
        'category' => 'mep',
        'subcategory' => 'coordination',
        'version' => '1.0',
        'inputs' => [['name' => 'id', 'type' => 'number', 'label' => 'System ID', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['id']; }],
        'outputs' => [['name' => 'total', 'label' => 'Rank', 'unit' => '']]
    ],

    // ============================================
    // COST & MANAGEMENT (5 calculators)
    // ============================================
    'boq-generator' => [
        'name' => 'MEP BOQ Generator',
        'description' => 'Generate bill of quantities for services',
        'category' => 'mep',
        'subcategory' => 'management',
        'version' => '1.0',
        'inputs' => [['name' => 'cost', 'type' => 'number', 'label' => 'Total Cost', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['cost']; }],
        'outputs' => [['name' => 'total', 'label' => 'Summary', 'unit' => 'Amt']]
    ],
    'cost-optimization' => [
        'name' => 'MEP Cost Optimization',
        'description' => 'Identify cost-saving service alternatives',
        'category' => 'mep',
        'subcategory' => 'management',
        'version' => '1.0',
        'inputs' => [['name' => 'savings', 'type' => 'number', 'label' => 'Projected Savings', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['savings']; }],
        'outputs' => [['name' => 'total', 'label' => 'Impact', 'unit' => '%']]
    ],
    'cost-summary' => [
        'name' => 'MEP Service Cost Summary',
        'description' => 'Aggregate service-wise expenditure',
        'category' => 'mep',
        'subcategory' => 'management',
        'version' => '1.0',
        'inputs' => [['name' => 'budget', 'type' => 'number', 'label' => 'Total Budget', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['budget']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total', 'unit' => 'Amt']]
    ],
    'material-takeoff' => [
        'name' => 'Service Material Takeoff',
        'description' => 'Detailed list of pipes/ducts/wires',
        'category' => 'mep',
        'subcategory' => 'management',
        'version' => '1.0',
        'inputs' => [['name' => 'length', 'type' => 'number', 'label' => 'Total Length', 'unit' => 'm', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['length']; }],
        'outputs' => [['name' => 'total', 'label' => 'Quantity', 'unit' => 'units']]
    ],
    'vendor-pricing' => [
        'name' => 'MEP Vendor Analysis',
        'description' => 'Compare service pricing from vendors',
        'category' => 'mep',
        'subcategory' => 'management',
        'version' => '1.0',
        'inputs' => [['name' => 'bid', 'type' => 'number', 'label' => 'Bid Price', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['bid']; }],
        'outputs' => [['name' => 'total', 'label' => 'Deviation', 'unit' => 'Amt']]
    ],

    // ============================================
    // REPORTS (5 calculators)
    // ============================================
    'clash-detection-report' => [
        'name' => 'Clash Analysis Report',
        'description' => 'Detailed report on inter-service clashes',
        'category' => 'mep',
        'subcategory' => 'reports',
        'version' => '1.0',
        'inputs' => [['name' => 'clashes', 'type' => 'number', 'label' => 'Clash Count', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['clashes']; }],
        'outputs' => [['name' => 'total', 'label' => 'Pages', 'unit' => 'nos']]
    ],
    'equipment-schedule' => [
        'name' => 'MEP Equipment Schedule',
        'description' => 'Consolidated equipment list and data',
        'category' => 'mep',
        'subcategory' => 'reports',
        'version' => '1.0',
        'inputs' => [['name' => 'units', 'type' => 'number', 'label' => 'Unit Count', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['units']; }],
        'outputs' => [['name' => 'total', 'label' => 'Entries', 'unit' => 'nos']]
    ],
    'load-summary' => [
        'name' => 'MEP Total Load Summary',
        'description' => 'Total MEP load for all services',
        'category' => 'mep',
        'subcategory' => 'reports',
        'version' => '1.0',
        'inputs' => [['name' => 'load', 'type' => 'number', 'label' => 'Total Load', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['load']; }],
        'outputs' => [['name' => 'total', 'label' => 'Total', 'unit' => 'kVA']]
    ],
    'mep-summary' => [
        'name' => 'Executive MEP Summary',
        'description' => 'Comprehensive MEP project overview',
        'category' => 'mep',
        'subcategory' => 'reports',
        'version' => '1.0',
        'inputs' => [['name' => 'progress', 'type' => 'number', 'label' => 'Project Progress', 'unit' => '%', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['progress']; }],
        'outputs' => [['name' => 'total', 'label' => 'Summary', 'unit' => '']]
    ],
    'pdf-export' => [
        'name' => 'MEP PDF Export Tool',
        'description' => 'Generate consolidated PDF reports',
        'category' => 'mep',
        'subcategory' => 'reports',
        'version' => '1.0',
        'inputs' => [['name' => 'pages', 'type' => 'number', 'label' => 'Page Count', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['pages']; }],
        'outputs' => [['name' => 'total', 'label' => 'Status', 'unit' => '']]
    ],

    // ============================================
    // SYSTEM & CONFIG (7 calculators)
    // ============================================
    'api-endpoints' => [
        'name' => 'MEP API Explorer',
        'description' => 'View and test MEP engine endpoints',
        'category' => 'mep',
        'subcategory' => 'system',
        'version' => '1.0',
        'inputs' => [['name' => 'id', 'type' => 'number', 'label' => 'API Key', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['id']; }],
        'outputs' => [['name' => 'total', 'label' => 'Active', 'unit' => '']]
    ],
    'input-validator' => [
        'name' => 'MEP Data Validator',
        'description' => 'Validate MEP system parameters',
        'category' => 'mep',
        'subcategory' => 'system',
        'version' => '1.0',
        'inputs' => [['name' => 'value', 'type' => 'number', 'label' => 'Check Value', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['value']; }],
        'outputs' => [['name' => 'total', 'label' => 'Valid', 'unit' => '']]
    ],
    'material-database' => [
        'name' => 'MEP Material DB',
        'description' => 'Browse coordinated material properties',
        'category' => 'mep',
        'subcategory' => 'system',
        'version' => '1.0',
        'inputs' => [['name' => 'code', 'type' => 'number', 'label' => 'Material Code', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['code']; }],
        'outputs' => [['name' => 'total', 'label' => 'Found', 'unit' => '']]
    ],
    'mep-config' => [
        'name' => 'MEP Global Settings',
        'description' => 'Configure global project parameters',
        'category' => 'mep',
        'subcategory' => 'system',
        'version' => '1.0',
        'inputs' => [['name' => 'id', 'type' => 'number', 'label' => 'Setting ID', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['id']; }],
        'outputs' => [['name' => 'total', 'label' => 'Applied', 'unit' => '']]
    ],
    'permissions' => [
        'name' => 'MEP Access Control',
        'description' => 'Manage service-wise access permissions',
        'category' => 'mep',
        'subcategory' => 'system',
        'version' => '1.0',
        'inputs' => [['name' => 'user', 'type' => 'number', 'label' => 'User ID', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['user']; }],
        'outputs' => [['name' => 'total', 'label' => 'Access', 'unit' => '']]
    ],
    'unit-converter' => [
        'name' => 'MEP Multi-Unit Converter',
        'description' => 'Convert units across all MEP disciplines',
        'category' => 'mep',
        'subcategory' => 'system',
        'version' => '1.0',
        'inputs' => [['name' => 'value', 'type' => 'number', 'label' => 'Input Value', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['value']; }],
        'outputs' => [['name' => 'total', 'label' => 'Result', 'unit' => '']]
    ],
    'autocad-layer-mapper' => [
        'name' => 'CAD Layer Coordinator',
        'description' => 'Map MEP services to standard CAD layers',
        'category' => 'mep',
        'subcategory' => 'system',
        'version' => '1.0',
        'inputs' => [['name' => 'count', 'type' => 'number', 'label' => 'Layer Count', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['count']; }],
        'outputs' => [['name' => 'total', 'label' => 'Mapped', 'unit' => 'nos']]
    ],

    // ============================================
    // COLLABORATION (4 calculators)
    // ============================================
    'bim-integration' => [
        'name' => 'Cloud BIM Integration',
        'description' => 'Sync MEP data with cloud BIM platforms',
        'category' => 'mep',
        'subcategory' => 'collaboration',
        'version' => '1.0',
        'inputs' => [['name' => 'id', 'type' => 'number', 'label' => 'Project ID', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['id']; }],
        'outputs' => [['name' => 'total', 'label' => 'Synced', 'unit' => '']]
    ],
    'cloud-sync' => [
        'name' => 'MEP Cloud Sync Tool',
        'description' => 'Synchronize multi-user MEP sessions',
        'category' => 'mep',
        'subcategory' => 'collaboration',
        'version' => '1.0',
        'inputs' => [['name' => 'data', 'type' => 'number', 'label' => 'Data Blocks', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['data']; }],
        'outputs' => [['name' => 'total', 'label' => 'Status', 'unit' => '']]
    ],
    'project-sharing' => [
        'name' => 'MEP Project Sharing',
        'description' => 'Share MEP calculations and results',
        'category' => 'mep',
        'subcategory' => 'collaboration',
        'version' => '1.0',
        'inputs' => [['name' => 'user', 'type' => 'number', 'label' => 'Target User', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['user']; }],
        'outputs' => [['name' => 'total', 'label' => 'Sent', 'unit' => '']]
    ],
    'revit-plugin' => [
        'name' => 'MEP Revit Connector',
        'description' => 'Direct link to Revit MEP models',
        'category' => 'mep',
        'subcategory' => 'collaboration',
        'version' => '1.0',
        'inputs' => [['name' => 'id', 'type' => 'number', 'label' => 'Revit Model ID', 'required' => true]],
        'formulas' => ['total' => function($inputs) { return $inputs['id']; }],
        'outputs' => [['name' => 'total', 'label' => 'Connected', 'unit' => '']]
    ],
];
