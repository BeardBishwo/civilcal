<?php

/**
 * Project Management Calculators Configuration
 */

return [
    // Dashboard tools
    'project-overview' => [
        'name' => 'Project Overview',
        'description' => 'Summary of project status and key metrics',
        'category' => 'management',
        'subcategory' => 'dashboard',
        'version' => '1.0',
        'inputs' => [['name' => 'proj_id', 'type' => 'string', 'label' => 'Project ID']],
        'formulas' => ['status' => function($i) { return 'Active'; }],
        'outputs' => [['name' => 'status', 'label' => 'Project Status', 'type' => 'string']]
    ],
    'gantt-chart' => [
        'name' => 'Gantt Chart',
        'description' => 'Visual project timeline and task scheduling',
        'category' => 'management',
        'subcategory' => 'dashboard',
        'version' => '1.0',
        'inputs' => [['name' => 'tasks', 'type' => 'integer', 'label' => 'Number of Tasks']],
        'formulas' => ['completion' => function($i) { return 0; }],
        'outputs' => [['name' => 'completion', 'label' => 'Percent Complete', 'unit' => '%']]
    ],
    'milestone-tracker' => [
        'name' => 'Milestone Tracker',
        'description' => 'Track key project milestones and deadlines',
        'category' => 'management',
        'subcategory' => 'dashboard',
        'version' => '1.0',
        'inputs' => [['name' => 'target', 'type' => 'integer', 'label' => 'Total Milestones']],
        'formulas' => ['pending' => function($i) { return $i['target']; }],
        'outputs' => [['name' => 'pending', 'label' => 'Pending Milestones']]
    ],

    // Scheduling tools
    'create-task' => [
        'name' => 'Create Task',
        'description' => 'Define new project tasks and durations',
        'category' => 'management',
        'subcategory' => 'scheduling',
        'version' => '1.0',
        'inputs' => [['name' => 'duration', 'type' => 'number', 'label' => 'Duration (Days)']],
        'formulas' => ['hours' => function($i) { return $i['duration'] * 8; }],
        'outputs' => [['name' => 'hours', 'label' => 'Total Labor Hours']]
    ],
    'assign-task' => [
        'name' => 'Assign Task',
        'description' => 'Assign team members to project tasks',
        'category' => 'management',
        'subcategory' => 'scheduling',
        'version' => '1.0',
        'inputs' => [['name' => 'team_size', 'type' => 'integer', 'label' => 'Team Size']],
        'formulas' => ['capacity' => function($i) { return $i['team_size'] * 40; }],
        'outputs' => [['name' => 'capacity', 'label' => 'Weekly Capacity', 'unit' => 'hrs']]
    ],
    'task-dependency' => [
        'name' => 'Task Dependencies',
        'description' => 'Define relationships between project tasks',
        'category' => 'management',
        'subcategory' => 'scheduling',
        'version' => '1.0',
        'inputs' => [['name' => 'deps', 'type' => 'integer', 'label' => 'Number of Dependencies']],
        'formulas' => ['risk' => function($i) { return $i['deps'] > 5 ? 'High' : 'Low'; }],
        'outputs' => [['name' => 'risk', 'label' => 'Schedule Risk', 'type' => 'string']]
    ],

    // Resource tools
    'manpower-planning' => [
        'name' => 'Manpower Planning',
        'description' => 'Calculate required workforce for project tasks',
        'category' => 'management',
        'subcategory' => 'resources',
        'version' => '1.0',
        'inputs' => [['name' => 'total_work', 'type' => 'number', 'label' => 'Total Work Units']],
        'formulas' => ['men' => function($i) { return ceil($i['total_work'] / 100); }],
        'outputs' => [['name' => 'men', 'label' => 'Required Workers']]
    ],
    'equipment-allocation' => [
        'name' => 'Equipment Allocation',
        'description' => 'Allocate machinery to different work sites',
        'category' => 'management',
        'subcategory' => 'resources',
        'version' => '1.0',
        'inputs' => [['name' => 'sites', 'type' => 'integer', 'label' => 'Number of Sites']],
        'formulas' => ['equip' => function($i) { return $i['sites'] * 2; }],
        'outputs' => [['name' => 'equip', 'label' => 'Total Equipment Units']]
    ],
    'material-tracking' => [
        'name' => 'Material Tracking',
        'description' => 'Monitor material inventory and consumption',
        'category' => 'management',
        'subcategory' => 'resources',
        'version' => '1.0',
        'inputs' => [['name' => 'stock', 'type' => 'number', 'label' => 'Current Stock']],
        'formulas' => ['status' => function($i) { return $i['stock'] < 10 ? 'Low' : 'OK'; }],
        'outputs' => [['name' => 'status', 'label' => 'Stock Status', 'type' => 'string']]
    ],

    // Financial tools
    'budget-tracking' => [
        'name' => 'Budget Tracking',
        'description' => 'Track project spending against budget',
        'category' => 'management',
        'subcategory' => 'financial',
        'version' => '1.0',
        'inputs' => [['name' => 'budget', 'type' => 'number', 'label' => 'Total Budget'], ['name' => 'spent', 'type' => 'number', 'label' => 'Total Spent']],
        'formulas' => ['variance' => function($i) { return $i['budget'] - $i['spent']; }],
        'outputs' => [['name' => 'variance', 'label' => 'Budget Variance', 'unit' => '$']]
    ],
    'cost-control' => [
        'name' => 'Cost Control',
        'description' => 'Monitor and control project costs',
        'category' => 'management',
        'subcategory' => 'financial',
        'version' => '1.0',
        'inputs' => [['name' => 'cost', 'type' => 'number', 'label' => 'Actual Cost']],
        'formulas' => ['limit' => function($i) { return $i['cost'] * 0.95; }],
        'outputs' => [['name' => 'limit', 'label' => 'Target Limit', 'unit' => '$']]
    ],
    'forecast-analysis' => [
        'name' => 'Forecast Analysis',
        'description' => 'Predict future project costs and outcomes',
        'category' => 'management',
        'subcategory' => 'financial',
        'version' => '1.0',
        'inputs' => [['name' => 'rate', 'type' => 'number', 'label' => 'Burn Rate ($/day)']],
        'formulas' => ['etc' => function($i) { return $i['rate'] * 30; }],
        'outputs' => [['name' => 'etc', 'label' => 'Estimated Month End', 'unit' => '$']]
    ],

    // Quality tools
    'quality-checklist' => [
        'name' => 'Quality Checklist',
        'description' => 'Maintain quality control standards and checklists',
        'category' => 'management',
        'subcategory' => 'quality',
        'version' => '1.0',
        'inputs' => [['name' => 'items', 'type' => 'integer', 'label' => 'Total Items']],
        'formulas' => ['passed' => function($i) { return 0; }],
        'outputs' => [['name' => 'passed', 'label' => 'Passed Items']]
    ],
    'safety-incidents' => [
        'name' => 'Safety Incidents',
        'description' => 'Log and track safety incidents on site',
        'category' => 'management',
        'subcategory' => 'quality',
        'version' => '1.0',
        'inputs' => [['name' => 'days', 'type' => 'integer', 'label' => 'Days without Incidents']],
        'formulas' => ['record' => function($i) { return $i['days']; }],
        'outputs' => [['name' => 'record', 'label' => 'Current Record']]
    ],
    'audit-reports' => [
        'name' => 'Audit Reports',
        'description' => 'Generate and review project audit reports',
        'category' => 'management',
        'subcategory' => 'quality',
        'version' => '1.0',
        'inputs' => [['name' => 'audits', 'type' => 'integer', 'label' => 'Number of Audits']],
        'formulas' => ['score' => function($i) { return 100; }],
        'outputs' => [['name' => 'score', 'label' => 'Average Score', 'unit' => '%']]
    ],

    // Document tools
    'document-repository' => [
        'name' => 'Document Repository',
        'description' => 'Central storage for all project documents',
        'category' => 'management',
        'subcategory' => 'documents',
        'version' => '1.0',
        'inputs' => [['name' => 'files', 'type' => 'integer', 'label' => 'Total Files']],
        'formulas' => ['space' => function($i) { return $i['files'] * 2; }],
        'outputs' => [['name' => 'space', 'label' => 'Estimated Space', 'unit' => 'MB']]
    ],
    'drawing-register' => [
        'name' => 'Drawing Register',
        'description' => 'Track project drawings and revisions',
        'category' => 'management',
        'subcategory' => 'documents',
        'version' => '1.0',
        'inputs' => [['name' => 'drawings', 'type' => 'integer', 'label' => 'Total Drawings']],
        'formulas' => ['revs' => function($i) { return $i['drawings'] * 3; }],
        'outputs' => [['name' => 'revs', 'label' => 'Total Revisions']]
    ],
    'submittal-tracking' => [
        'name' => 'Submittal Tracking',
        'description' => 'Manage project submittals and approvals',
        'category' => 'management',
        'subcategory' => 'documents',
        'version' => '1.0',
        'inputs' => [['name' => 'subs', 'type' => 'integer', 'label' => 'Total Submittals']],
        'formulas' => ['open' => function($i) { return $i['subs']; }],
        'outputs' => [['name' => 'open', 'label' => 'Open Submittals']]
    ],
];
