<?php
// MEP Cost Optimization Engine
// Advanced cost optimization algorithms and recommendations for MEP projects

require_once '../../../app/Config/config.php';
require_once '../../../../app/Core/DatabaseLegacy.php';
require_once '../../../../app/Helpers/functions.php';

// Initialize database connection
$db = new Database();

// Process form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'analyze_optimization':
                analyzeCostOptimization($db);
                break;
            case 'apply_optimization':
                applyOptimization($db);
                break;
            case 'generate_recommendations':
                generateRecommendations($db);
                break;
        }
    }
}

function analyzeCostOptimization($db) {
    global $message, $messageType;
    
    $project_id = $_POST['project_id'] ?? '';
    $optimization_type = $_POST['optimization_type'] ?? 'comprehensive';
    $budget_target = floatval($_POST['budget_target'] ?? 0);
    
    if (empty($project_id)) {
        $message = "Please select a project.";
        $messageType = "error";
        return;
    }
    
    // Get current project costs
    $current_costs = getCurrentProjectCosts($db, $project_id);
    
    if (!$current_costs) {
        $message = "Unable to retrieve project cost data.";
        $messageType = "error";
        return;
    }
    
    // Perform optimization analysis
    $optimization_data = performOptimizationAnalysis($current_costs, $optimization_type, $budget_target);
    
    // Store results in session
    $_SESSION['cost_optimization'] = [
        'project_id' => $project_id,
        'optimization_type' => $optimization_type,
        'budget_target' => $budget_target,
        'current_costs' => $current_costs,
        'optimization_data' => $optimization_data,
        'analysis_date' => date('Y-m-d H:i:s')
    ];
    
    $message = "Cost optimization analysis completed successfully.";
    $messageType = "success";
}

function getCurrentProjectCosts($db, $project_id) {
    // Get material costs by category
    $material_query = "SELECT 
        mt.category,
        mt.discipline,
        mt.system_type,
        SUM(mt.quantity) as total_quantity,
        SUM(mt.quantity * mp.unit_price) as total_cost,
        COUNT(*) as item_count,
        AVG(mp.unit_price) as avg_unit_price
        FROM material_takeoff mt
        JOIN material_prices mp ON mt.material_id = mp.id
        WHERE mt.project_id = ?
        GROUP BY mt.category, mt.discipline, mt.system_type";
    
    $material_result = $db->executeQuery($material_query, [$project_id]);
    $materials = [];
    while ($row = $material_result->fetch()) {
        $materials[] = $row;
    }
    
    // Get equipment costs
    $equipment_query = "SELECT 
        et.equipment_type,
        et.system_type,
        SUM(et.quantity) as total_quantity,
        SUM(et.quantity * ep.unit_price) as total_cost,
        COUNT(*) as item_count,
        AVG(ep.unit_price) as avg_unit_price
        FROM equipment_takeoff et
        JOIN equipment_prices ep ON et.equipment_id = ep.id
        WHERE et.project_id = ?
        GROUP BY et.equipment_type, et.system_type";
    
    $equipment_result = $db->executeQuery($equipment_query, [$project_id]);
    $equipment = [];
    while ($row = $equipment_result->fetch()) {
        $equipment[] = $row;
    }
    
    // Calculate totals
    $total_material_cost = array_sum(array_column($materials, 'total_cost'));
    $total_equipment_cost = array_sum(array_column($equipment, 'total_cost'));
    
    return [
        'materials' => $materials,
        'equipment' => $equipment,
        'total_material_cost' => $total_material_cost,
        'total_equipment_cost' => $total_equipment_cost,
        'total_cost' => $total_material_cost + $total_equipment_cost
    ];
}

function performOptimizationAnalysis($current_costs, $optimization_type, $budget_target) {
    $analysis = [
        'potential_savings' => [],
        'alternative_options' => [],
        'risk_assessment' => [],
        'implementation_plan' => []
    ];
    
    // Analyze material optimizations
    $material_analysis = analyzeMaterialOptimization($current_costs['materials']);
    $analysis['potential_savings'] = array_merge($analysis['potential_savings'], $material_analysis['savings']);
    
    // Analyze equipment optimizations
    $equipment_analysis = analyzeEquipmentOptimization($current_costs['equipment']);
    $analysis['potential_savings'] = array_merge($analysis['potential_savings'], $equipment_analysis['savings']);
    
    // Generate alternative options
    $analysis['alternative_options'] = generateAlternativeOptions($current_costs);
    
    // Perform risk assessment
    $analysis['risk_assessment'] = performRiskAssessment($analysis['potential_savings']);
    
    // Create implementation plan
    $analysis['implementation_plan'] = createImplementationPlan($analysis['potential_savings'], $budget_target);
    
    return $analysis;
}

function analyzeMaterialOptimization($materials) {
    $savings_opportunities = [];
    
    foreach ($materials as $material) {
        // Identify bulk purchasing opportunities
        if ($material['total_quantity'] > 1000) {
            $savings_opportunities[] = [
                'type' => 'bulk_purchase',
                'category' => $material['category'],
                'current_cost' => $material['total_cost'],
                'potential_savings' => $material['total_cost'] * 0.15, // 15% bulk discount
                'description' => 'Bulk purchasing discount for high-volume materials',
                'implementation_effort' => 'Low',
                'timeframe' => '1-2 weeks'
            ];
        }
        
        // Identify alternative materials
        if ($material['avg_unit_price'] > getMarketAverage($material['category'])) {
            $savings_opportunities[] = [
                'type' => 'material_alternative',
                'category' => $material['category'],
                'current_cost' => $material['total_cost'],
                'potential_savings' => $material['total_cost'] * 0.10, // 10% alternative material savings
                'description' => 'Alternative materials with similar specifications',
                'implementation_effort' => 'Medium',
                'timeframe' => '2-4 weeks'
            ];
        }
        
        // Identify supplier optimization
        $savings_opportunities[] = [
            'type' => 'supplier_negotiation',
            'category' => $material['category'],
            'current_cost' => $material['total_cost'],
            'potential_savings' => $material['total_cost'] * 0.08, // 8% supplier negotiation
            'description' => 'Negotiate better terms with suppliers',
            'implementation_effort' => 'Low',
            'timeframe' => '1-3 weeks'
        ];
    }
    
    return ['savings' => $savings_opportunities];
}

function analyzeEquipmentOptimization($equipment) {
    $savings_opportunities = [];
    
    foreach ($equipment as $item) {
        // Identify energy-efficient alternatives
        $savings_opportunities[] = [
            'type' => 'energy_efficiency',
            'category' => $item['equipment_type'],
            'current_cost' => $item['total_cost'],
            'potential_savings' => $item['total_cost'] * 0.12, // 12% energy savings
            'description' => 'Energy-efficient equipment with lower operating costs',
            'implementation_effort' => 'High',
            'timeframe' => '4-8 weeks'
        ];
        
        // Identify leasing options
        if ($item['total_cost'] > 50000) {
            $savings_opportunities[] = [
                'type' => 'equipment_leasing',
                'category' => $item['equipment_type'],
                'current_cost' => $item['total_cost'],
                'potential_savings' => $item['total_cost'] * 0.20, // 20% savings through leasing
                'description' => 'Equipment leasing instead of purchase',
                'implementation_effort' => 'Medium',
                'timeframe' => '2-6 weeks'
            ];
        }
    }
    
    return ['savings' => $savings_opportunities];
}

function getMarketAverage($category) {
    // Simulated market averages - in real implementation, this would query market data
    $market_averages = [
        'pipes_fittings' => 25.50,
        'valves_controls' => 150.00,
        'electrical_conduit' => 8.75,
        'equipment' => 2500.00,
        'insulation' => 12.25,
        'supports_hangers' => 35.00
    ];
    
    return $market_averages[$category] ?? 50.00;
}

function generateAlternativeOptions($current_costs) {
    $alternatives = [];
    
    // Material alternatives
    $material_alternatives = [
        'pipes_fittings' => [
            ['name' => 'PEX Pipes', 'cost_factor' => 0.85, 'pros' => ['Flexible', 'Corrosion resistant'], 'cons' => ['Temperature limitations']],
            ['name' => 'CPVC Pipes', 'cost_factor' => 0.90, 'pros' => ['Chemical resistant', 'Durable'], 'cons' => ['Higher cost']],
            ['name' => 'Stainless Steel', 'cost_factor' => 1.15, 'pros' => ['Long lasting', 'High pressure rating'], 'cons' => ['Expensive', 'Complex installation']]
        ],
        'electrical_conduit' => [
            ['name' => 'EMT Conduit', 'cost_factor' => 0.95, 'pros' => ['Easy to install', 'Lightweight'], 'cons' => ['Corrosion susceptible']],
            ['name' => 'Rigid Conduit', 'cost_factor' => 1.10, 'pros' => ['High protection', 'Durable'], 'cons' => ['Expensive', 'Difficult bends']],
            ['name' => 'Flexible Conduit', 'cost_factor' => 1.05, 'pros' => ['Flexible', 'Vibration resistant'], 'cons' => ['Higher cost', 'Limited applications']]
        ]
    ];
    
    foreach ($current_costs['materials'] as $material) {
        if (isset($material_alternatives[$material['category']])) {
            $alternatives = array_merge($alternatives, $material_alternatives[$material['category']]);
        }
    }
    
    return $alternatives;
}

function performRiskAssessment($savings_opportunities) {
    $risks = [];
    
    foreach ($savings_opportunities as $opportunity) {
        $risk_level = 'Low';
        $mitigation = '';
        
        switch ($opportunity['type']) {
            case 'bulk_purchase':
                $risk_level = 'Low';
                $mitigation = 'Ensure storage capacity and delivery schedule';
                break;
            case 'material_alternative':
                $risk_level = 'Medium';
                $mitigation = 'Verify specifications and obtain samples for testing';
                break;
            case 'supplier_negotiation':
                $risk_level = 'Low';
                $mitigation = 'Maintain good supplier relationships';
                break;
            case 'energy_efficiency':
                $risk_level = 'Medium';
                $mitigation = 'Ensure payback period analysis and utility rebates';
                break;
            case 'equipment_leasing':
                $risk_level = 'High';
                $mitigation = 'Review lease terms carefully and consider total cost of ownership';
                break;
        }
        
        $risks[] = [
            'opportunity' => $opportunity['type'],
            'risk_level' => $risk_level,
            'description' => $opportunity['description'],
            'mitigation' => $mitigation
        ];
    }
    
    return $risks;
}

function createImplementationPlan($savings_opportunities, $budget_target) {
    // Sort opportunities by savings potential
    usort($savings_opportunities, function($a, $b) {
        return $b['potential_savings'] - $a['potential_savings'];
    });
    
    $plan = [];
    $cumulative_savings = 0;
    $target_reached = false;
    
    foreach ($savings_opportunities as $index => $opportunity) {
        if ($budget_target > 0 && $cumulative_savings >= $budget_target && !$target_reached) {
            $target_reached = true;
            $plan[] = [
                'phase' => 'Target Achievement',
                'description' => 'Budget target achieved',
                'priority' => 'Critical',
                'timeline' => 'Completed'
            ];
        }
        
        $plan[] = [
            'phase' => 'Phase ' . ($index + 1),
            'opportunity' => $opportunity['type'],
            'description' => $opportunity['description'],
            'expected_savings' => $opportunity['potential_savings'],
            'priority' => $opportunity['potential_savings'] > 10000 ? 'High' : ($opportunity['potential_savings'] > 5000 ? 'Medium' : 'Low'),
            'effort' => $opportunity['implementation_effort'],
            'timeline' => $opportunity['timeframe']
        ];
        
        $cumulative_savings += $opportunity['potential_savings'];
    }
    
    return $plan;
}

function applyOptimization($db) {
    global $message, $messageType;
    
    $optimization_data = $_SESSION['cost_optimization'] ?? null;
    
    if (!$optimization_data) {
        $message = "No optimization data available. Please run optimization analysis first.";
        $messageType = "error";
        return;
    }
    
    try {
        // Save optimization plan
        $insert_query = "INSERT INTO cost_optimizations (
            project_id, optimization_type, analysis_data, potential_savings,
            target_budget, created_date, status
        ) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $analysis_json = json_encode($optimization_data['optimization_data']);
        $savings_total = array_sum(array_column($optimization_data['optimization_data']['potential_savings'], 'potential_savings'));
        
        $stmt = $db->executeQuery($insert_query, [
            $optimization_data['project_id'],
            $optimization_data['optimization_type'],
            $analysis_json,
            $savings_total,
            $optimization_data['budget_target'],
            $optimization_data['analysis_date']
        ]);
        
        $message = "Optimization plan applied successfully. Total potential savings: $" . number_format($savings_total, 2);
        $messageType = "success";
        
    } catch (Exception $e) {
        $message = "Error applying optimization: " . $e->getMessage();
        $messageType = "error";
    }
}

function generateRecommendations($db) {
    global $message, $messageType;
    
    $project_id = $_POST['project_id'] ?? '';
    
    if (empty($project_id)) {
        $message = "Please select a project.";
        $messageType = "error";
        return;
    }
    
    // Get optimization data
    $optimization_data = $_SESSION['cost_optimization'] ?? null;
    
    if (!$optimization_data || $optimization_data['project_id'] != $project_id) {
        analyzeCostOptimization($db);
        $optimization_data = $_SESSION['cost_optimization'];
    }
    
    if (!$optimization_data) {
        $message = "Unable to generate recommendations.";
        $messageType = "error";
        return;
    }
    
    // Generate detailed recommendations
    $recommendations = generateDetailedRecommendations($optimization_data);
    
    $_SESSION['cost_recommendations'] = [
        'project_id' => $project_id,
        'recommendations' => $recommendations,
        'generated_date' => date('Y-m-d H:i:s')
    ];
    
    $message = "Cost optimization recommendations generated successfully.";
    $messageType = "success";
}

function generateDetailedRecommendations($optimization_data) {
    $recommendations = [];
    
    // Top priority recommendations
    $top_savings = array_slice($optimization_data['optimization_data']['potential_savings'], 0, 3);
    
    foreach ($top_savings as $saving) {
        $recommendations[] = [
            'priority' => 'High',
            'category' => $saving['type'],
            'title' => 'Optimize ' . $saving['category'],
            'description' => $saving['description'],
            'potential_savings' => $saving['potential_savings'],
            'implementation_steps' => getImplementationSteps($saving['type']),
            'timeline' => $saving['timeframe'],
            'effort_required' => $saving['implementation_effort']
        ];
    }
    
    return $recommendations;
}

function getImplementationSteps($optimization_type) {
    $steps = [
        'bulk_purchase' => [
            'Analyze current inventory requirements',
            'Calculate optimal bulk quantities',
            'Negotiate bulk pricing with suppliers',
            'Plan delivery schedule and storage'
        ],
        'material_alternative' => [
            'Research alternative materials with similar specifications',
            'Obtain samples for testing and approval',
            'Update material specifications in drawings',
            'Coordinate with suppliers for new materials'
        ],
        'supplier_negotiation' => [
            'Research current market rates',
            'Prepare negotiation strategy',
            'Meet with suppliers to discuss terms',
            'Execute improved supplier agreements'
        ],
        'energy_efficiency' => [
            'Conduct energy efficiency assessment',
            'Identify high-efficiency equipment options',
            'Calculate payback periods',
            'Apply for utility rebates and incentives'
        ],
        'equipment_leasing' => [
            'Analyze total cost of ownership',
            'Research leasing options and terms',
            'Compare lease vs. purchase scenarios',
            'Execute lease agreements'
        ]
    ];
    
    return $steps[$optimization_type] ?? ['Review and implement optimization opportunity'];
}

// Get projects for dropdown
$projects_query = "SELECT id, project_name FROM projects ORDER BY project_name";
$projects_result = $db->executeQuery($projects_query);
$projects = [];
while ($row = $projects_result->fetch()) {
    $projects[] = $row;
}

$optimization_data = $_SESSION['cost_optimization'] ?? null;
$recommendations = $_SESSION['cost_recommendations'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP Cost Optimization Engine</title>
    <link rel="stylesheet" href="../../../../assets/css/estimation.css">
    <link rel="stylesheet" href="../../../../assets/css/project-management.css">
    <style>
        .optimization-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .optimization-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .optimization-header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        
        .optimization-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }
        
        .form-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group select, 
        .form-group input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group select:focus, 
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .analysis-results {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .analysis-title {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .savings-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .savings-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .savings-card h3 {
            margin: 0 0 10px 0;
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .savings-card .amount {
            font-size: 2em;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .opportunity-list {
            margin: 20px 0;
        }
        
        .opportunity-item {
            border: 1px solid #e1e5e9;
            border-radius: 6px;
            padding: 20px;
            margin: 15px 0;
            background: #f8f9fa;
        }
        
        .opportunity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .opportunity-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #333;
        }
        
        .opportunity-savings {
            font-size: 1.5em;
            font-weight: bold;
            color: #28a745;
        }
        
        .opportunity-details {
            color: #6c757d;
            margin: 10px 0;
        }
        
        .opportunity-tags {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .tag {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }
        
        .tag-effort-low {
            background: #d4edda;
            color: #155724;
        }
        
        .tag-effort-medium {
            background: #fff3cd;
            color: #856404;
        }
        
        .tag-effort-high {
            background: #f8d7da;
            color: #721c24;
        }
        
        .tag-priority-high {
            background: #dc3545;
            color: white;
        }
        
        .tag-priority-medium {
            background: #ffc107;
            color: #212529;
        }
        
        .tag-priority-low {
            background: #6c757d;
            color: white;
        }
        
        .recommendations-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .recommendation-item {
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            background: #f8f9fa;
            border-radius: 0 6px 6px 0;
        }
        
        .recommendation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .recommendation-title {
            font-size: 1.3em;
            font-weight: 600;
            color: #333;
        }
        
        .recommendation-priority {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }
        
        .priority-high {
            background: #dc3545;
            color: white;
        }
        
        .priority-medium {
            background: #ffc107;
            color: #212529;
        }
        
        .implementation-steps {
            margin-top: 15px;
        }
        
        .implementation-steps ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .implementation-steps li {
            margin: 5px 0;
            color: #495057;
        }
        
        .message {
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .risk-assessment {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .risk-level {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .risk-low {
            background: #d4edda;
            color: #155724;
        }
        
        .risk-medium {
            background: #fff3cd;
            color: #856404;
        }
        
        .risk-high {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="optimization-container">
        <div class="optimization-header">
            <h1>MEP Cost Optimization Engine</h1>
            <p>Advanced algorithms for maximizing cost efficiency in MEP projects</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Optimization Analysis Form -->
        <div class="form-section">
            <h2>Cost Optimization Analysis</h2>
            <form method="POST" id="optimizationForm">
                <input type="hidden" name="action" value="analyze_optimization">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="project_id">Select Project</label>
                        <select name="project_id" id="project_id" required>
                            <option value="">Choose a project...</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?php echo $project['id']; ?>">
                                    <?php echo htmlspecialchars($project['project_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="optimization_type">Optimization Type</label>
                        <select name="optimization_type" id="optimization_type" required>
                            <option value="comprehensive">Comprehensive Analysis</option>
                            <option value="material_focused">Material Cost Focus</option>
                            <option value="equipment_focused">Equipment Cost Focus</option>
                            <option value="supplier_focused">Supplier Optimization</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="budget_target">Target Savings ($)</label>
                        <input type="number" name="budget_target" id="budget_target" 
                               placeholder="Enter target savings amount" min="0" step="0.01">
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Analyze Optimization</button>
                    <button type="button" class="btn btn-warning" onclick="generateRecommendations()">Generate Recommendations</button>
                </div>
            </form>
        </div>
        
        <!-- Optimization Results -->
        <?php if ($optimization_data): ?>
            <div class="analysis-results">
                <h2 class="analysis-title">Optimization Analysis Results</h2>
                
                <div class="savings-summary">
                    <div class="savings-card">
                        <h3>Current Total Cost</h3>
                        <div class="amount">$<?php echo number_format($optimization_data['current_costs']['total_cost'], 2); ?></div>
                    </div>
                    
                    <div class="savings-card">
                        <h3>Potential Savings</h3>
                        <div class="amount">
                            $<?php 
                            $total_savings = array_sum(array_column($optimization_data['optimization_data']['potential_savings'], 'potential_savings'));
                            echo number_format($total_savings, 2); 
                            ?>
                        </div>
                    </div>
                    
                    <div class="savings-card">
                        <h3>Optimization Type</h3>
                        <div class="amount"><?php echo ucwords(str_replace('_', ' ', $optimization_data['optimization_type'])); ?></div>
                    </div>
                    
                    <div class="savings-card">
                        <h3>Analysis Date</h3>
                        <div class="amount"><?php echo date('M j, Y', strtotime($optimization_data['analysis_date'])); ?></div>
                    </div>
                </div>
                
                <h3>Cost Optimization Opportunities</h3>
                <div class="opportunity-list">
                    <?php foreach ($optimization_data['optimization_data']['potential_savings'] as $opportunity): ?>
                        <div class="opportunity-item">
                            <div class="opportunity-header">
                                <div class="opportunity-title">
                                    <?php echo ucwords(str_replace('_', ' ', $opportunity['type'])); ?> - 
                                    <?php echo htmlspecialchars($opportunity['category']); ?>
                                </div>
                                <div class="opportunity-savings">
                                    $<?php echo number_format($opportunity['potential_savings'], 2); ?>
                                </div>
                            </div>
                            <div class="opportunity-details">
                                <?php echo htmlspecialchars($opportunity['description']); ?>
                            </div>
                            <div class="opportunity-tags">
                                <span class="tag tag-priority-<?php echo $opportunity['potential_savings'] > 10000 ? 'high' : ($opportunity['potential_savings'] > 5000 ? 'medium' : 'low'); ?>">
                                    Priority: <?php echo $opportunity['potential_savings'] > 10000 ? 'High' : ($opportunity['potential_savings'] > 5000 ? 'Medium' : 'Low'); ?>
                                </span>
                                <span class="tag tag-effort-<?php echo strtolower(str_replace(' ', '-', $opportunity['implementation_effort'])); ?>">
                                    Effort: <?php echo $opportunity['implementation_effort']; ?>
                                </span>
                                <span class="tag">
                                    Timeline: <?php echo htmlspecialchars($opportunity['timeframe']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <h3>Risk Assessment</h3>
                <div class="risk-assessment">
                    <?php foreach ($optimization_data['optimization_data']['risk_assessment'] as $risk): ?>
                        <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ffeaa7;">
                            <strong><?php echo htmlspecialchars($risk['opportunity']); ?></strong>
                            <span class="risk-level risk-<?php echo strtolower($risk['risk_level']); ?>">
                                <?php echo $risk['risk_level']; ?> Risk
                            </span>
                            <div style="margin-top: 5px;">
                                <em>Mitigation:</em> <?php echo htmlspecialchars($risk['mitigation']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="btn-group">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="apply_optimization">
                        <button type="submit" class="btn btn-success">Apply Optimization Plan</button>
                    </form>
                    <button type="button" class="btn btn-secondary" onclick="exportAnalysis()">Export Analysis</button>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Detailed Recommendations -->
        <?php if ($recommendations): ?>
            <div class="recommendations-section">
                <h2 class="analysis-title">Detailed Recommendations</h2>
                
                <?php foreach ($recommendations['recommendations'] as $recommendation): ?>
                    <div class="recommendation-item">
                        <div class="recommendation-header">
                            <div class="recommendation-title"><?php echo htmlspecialchars($recommendation['title']); ?></div>
                            <div class="recommendation-priority priority-<?php echo strtolower($recommendation['priority']); ?>">
                                <?php echo $recommendation['priority']; ?> Priority
                            </div>
                        </div>
                        
                        <p><?php echo htmlspecialchars($recommendation['description']); ?></p>
                        
                        <div style="margin: 15px 0;">
                            <strong>Expected Savings:</strong> 
                            $<?php echo number_format($recommendation['potential_savings'], 2); ?>
                        </div>
                        
                        <div style="margin: 15px 0;">
                            <strong>Timeline:</strong> <?php echo htmlspecialchars($recommendation['timeline']); ?> | 
                            <strong>Effort Required:</strong> <?php echo htmlspecialchars($recommendation['effort_required']); ?>
                        </div>
                        
                        <div class="implementation-steps">
                            <strong>Implementation Steps:</strong>
                            <ol>
                                <?php foreach ($recommendation['implementation_steps'] as $step): ?>
                                    <li><?php echo htmlspecialchars($step); ?></li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function generateRecommendations() {
            const projectId = document.getElementById('project_id').value;
            if (!projectId) {
                alert('Please select a project first.');
                return;
            }
            
            // Submit form to generate recommendations
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="generate_recommendations">
                <input type="hidden" name="project_id" value="${projectId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        function exportAnalysis() {
            alert('Export functionality will be implemented with report generation');
        }
    </script>
</body>
</html>


