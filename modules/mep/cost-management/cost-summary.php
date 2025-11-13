<?php
// MEP Cost Summary Generator
// Generates comprehensive cost summaries for MEP projects

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
            case 'generate_summary':
                generateCostSummary($db);
                break;
            case 'save_summary':
                saveCostSummary($db);
                break;
            case 'compare_budgets':
                compareBudgets($db);
                break;
        }
    }
}

function generateCostSummary($db) {
    global $message, $messageType;
    
    $project_id = $_POST['project_id'] ?? '';
    $cost_breakdown = $_POST['cost_breakdown'] ?? 'system';
    $include_contingency = isset($_POST['include_contingency']);
    $contingency_percentage = floatval($_POST['contingency_percentage'] ?? 10);
    
    if (empty($project_id)) {
        $message = "Please select a project.";
        $messageType = "error";
        return;
    }
    
    // Get project details
    $project_query = "SELECT * FROM projects WHERE id = ?";
    $project_result = $db->executeQuery($project_query, [$project_id]);
    $project = $project_result->fetch();
    
    if (!$project) {
        $message = "Project not found.";
        $messageType = "error";
        return;
    }
    
    // Generate cost summary based on breakdown type
    $summary_data = [];
    
    switch ($cost_breakdown) {
        case 'system':
            $summary_data = generateSystemBreakdown($db, $project_id);
            break;
        case 'phase':
            $summary_data = generatePhaseBreakdown($db, $project_id);
            break;
        case 'discipline':
            $summary_data = generateDisciplineBreakdown($db, $project_id);
            break;
        case 'material':
            $summary_data = generateMaterialBreakdown($db, $project_id);
            break;
    }
    
    // Store summary in session for display
    $_SESSION['cost_summary'] = [
        'project' => $project,
        'breakdown_type' => $cost_breakdown,
        'data' => $summary_data,
        'include_contingency' => $include_contingency,
        'contingency_percentage' => $contingency_percentage,
        'generated_date' => date('Y-m-d H:i:s')
    ];
    
    $message = "Cost summary generated successfully.";
    $messageType = "success";
}

function generateSystemBreakdown($db, $project_id) {
    $systems = [
        'mechanical' => 'Mechanical Systems',
        'electrical' => 'Electrical Systems', 
        'plumbing' => 'Plumbing Systems',
        'fire_protection' => 'Fire Protection'
    ];
    
    $breakdown = [];
    $total_cost = 0;
    
    foreach ($systems as $system_key => $system_name) {
        // Get material costs
        $material_query = "SELECT 
            SUM(mt.quantity * mp.unit_price) as material_cost
            FROM material_takeoff mt
            JOIN material_prices mp ON mt.material_id = mp.id
            WHERE mt.project_id = ? AND mt.system_type = ?";
        
        $material_result = $db->executeQuery($material_query, [$project_id, $system_key]);
        $material_data = $material_result->fetch();
        $material_cost = $material_data['material_cost'] ?? 0;
        
        // Get labor costs (assume 40% of material cost as labor)
        $labor_cost = $material_cost * 0.40;
        
        // Get equipment costs
        $equipment_query = "SELECT 
            SUM(eq.quantity * ep.unit_price) as equipment_cost
            FROM equipment_takeoff eq
            JOIN equipment_prices ep ON eq.equipment_id = ep.id
            WHERE eq.project_id = ? AND eq.system_type = ?";
        
        $equipment_result = $db->executeQuery($equipment_query, [$project_id, $system_key]);
        $equipment_data = $equipment_result->fetch();
        $equipment_cost = $equipment_data['equipment_cost'] ?? 0;
        
        // Calculate total system cost
        $system_total = $material_cost + $labor_cost + $equipment_cost;
        $total_cost += $system_total;
        
        $breakdown[] = [
            'system' => $system_name,
            'material_cost' => $material_cost,
            'labor_cost' => $labor_cost,
            'equipment_cost' => $equipment_cost,
            'total_cost' => $system_total,
            'percentage' => 0 // Will calculate after loop
        ];
    }
    
    // Calculate percentages
    foreach ($breakdown as &$item) {
        $item['percentage'] = $total_cost > 0 ? ($item['total_cost'] / $total_cost) * 100 : 0;
    }
    
    return [
        'breakdown' => $breakdown,
        'total_cost' => $total_cost,
        'summary_type' => 'System Breakdown'
    ];
}

function generatePhaseBreakdown($db, $project_id) {
    $phases = [
        'design' => 'Design Phase',
        'procurement' => 'Procurement Phase',
        'installation' => 'Installation Phase',
        'testing' => 'Testing & Commissioning',
        'warranty' => 'Warranty Period'
    ];
    
    $phase_percentages = [
        'design' => 0.05,
        'procurement' => 0.15,
        'installation' => 0.65,
        'testing' => 0.10,
        'warranty' => 0.05
    ];
    
    $breakdown = [];
    $total_cost = 0;
    
    foreach ($phases as $phase_key => $phase_name) {
        // Get costs for each phase (simplified calculation)
        $phase_percentage = $phase_percentages[$phase_key] ?? 0;
        
        // Get total project cost
        $total_query = "SELECT 
            SUM(mt.quantity * mp.unit_price) as material_cost
            FROM material_takeoff mt
            JOIN material_prices mp ON mt.material_id = mp.id
            WHERE mt.project_id = ?";
        
        $total_result = $db->executeQuery($total_query, [$project_id]);
        $total_data = $total_result->fetch();
        $total_material_cost = $total_data['material_cost'] ?? 0;
        $total_project_cost = $total_material_cost * 1.40; // Include labor
        
        $phase_cost = $total_project_cost * $phase_percentage;
        $total_cost += $phase_cost;
        
        $breakdown[] = [
            'phase' => $phase_name,
            'cost' => $phase_cost,
            'percentage' => 0, // Will calculate after loop
            'duration' => getPhaseDuration($phase_key),
            'start_date' => '', // Will be calculated based on project timeline
            'end_date' => ''
        ];
    }
    
    // Calculate percentages
    foreach ($breakdown as &$item) {
        $item['percentage'] = $total_cost > 0 ? ($item['cost'] / $total_cost) * 100 : 0;
    }
    
    return [
        'breakdown' => $breakdown,
        'total_cost' => $total_cost,
        'summary_type' => 'Phase Breakdown'
    ];
}

function generateDisciplineBreakdown($db, $project_id) {
    $disciplines = [
        'hvac' => 'HVAC',
        'electrical' => 'Electrical',
        'plumbing' => 'Plumbing',
        'fire_protection' => 'Fire Protection'
    ];
    
    $breakdown = [];
    $total_cost = 0;
    
    foreach ($disciplines as $discipline_key => $discipline_name) {
        // Get discipline-specific costs
        $discipline_query = "SELECT 
            SUM(mt.quantity * mp.unit_price) as base_cost
            FROM material_takeoff mt
            JOIN material_prices mp ON mt.material_id = mp.id
            WHERE mt.project_id = ? AND (mt.discipline = ? OR mt.system_type = ?)";
        
        $discipline_result = $db->executeQuery($discipline_query, [$project_id, $discipline_key, $discipline_key]);
        $discipline_data = $discipline_result->fetch();
        $base_cost = $discipline_data['base_cost'] ?? 0;
        
        // Add complexity factors
        $complexity_factor = getComplexityFactor($discipline_key);
        $discipline_cost = $base_cost * $complexity_factor;
        $total_cost += $discipline_cost;
        
        $breakdown[] = [
            'discipline' => $discipline_name,
            'base_cost' => $base_cost,
            'complexity_factor' => $complexity_factor,
            'total_cost' => $discipline_cost,
            'percentage' => 0, // Will calculate after loop
            'labor_hours' => estimateLaborHours($discipline_key, $base_cost)
        ];
    }
    
    // Calculate percentages
    foreach ($breakdown as &$item) {
        $item['percentage'] = $total_cost > 0 ? ($item['total_cost'] / $total_cost) * 100 : 0;
    }
    
    return [
        'breakdown' => $breakdown,
        'total_cost' => $total_cost,
        'summary_type' => 'Discipline Breakdown'
    ];
}

function generateMaterialBreakdown($db, $project_id) {
    $categories = [
        'pipes_fittings' => 'Pipes & Fittings',
        'valves_controls' => 'Valves & Controls',
        'electrical_conduit' => 'Electrical & Conduit',
        'equipment' => 'Equipment',
        'insulation' => 'Insulation',
        'supports_hangers' => 'Supports & Hangers'
    ];
    
    $breakdown = [];
    $total_cost = 0;
    
    foreach ($categories as $category_key => $category_name) {
        $category_query = "SELECT 
            SUM(mt.quantity * mp.unit_price) as category_cost,
            COUNT(*) as item_count
            FROM material_takeoff mt
            JOIN material_prices mp ON mt.material_id = mp.id
            WHERE mt.project_id = ? AND mt.category = ?";
        
        $category_result = $db->executeQuery($category_query, [$project_id, $category_key]);
        $category_data = $category_result->fetch();
        $category_cost = $category_data['category_cost'] ?? 0;
        $item_count = $category_data['item_count'] ?? 0;
        
        $total_cost += $category_cost;
        
        $breakdown[] = [
            'category' => $category_name,
            'cost' => $category_cost,
            'item_count' => $item_count,
            'percentage' => 0, // Will calculate after loop
            'avg_unit_cost' => $item_count > 0 ? $category_cost / $item_count : 0
        ];
    }
    
    // Calculate percentages
    foreach ($breakdown as &$item) {
        $item['percentage'] = $total_cost > 0 ? ($item['cost'] / $total_cost) * 100 : 0;
    }
    
    return [
        'breakdown' => $breakdown,
        'total_cost' => $total_cost,
        'summary_type' => 'Material Category Breakdown'
    ];
}

function getPhaseDuration($phase_key) {
    $durations = [
        'design' => '4-6 weeks',
        'procurement' => '8-12 weeks',
        'installation' => '16-24 weeks',
        'testing' => '2-4 weeks',
        'warranty' => '12 months'
    ];
    
    return $durations[$phase_key] ?? 'Unknown';
}

function getComplexityFactor($discipline_key) {
    $factors = [
        'hvac' => 1.2,
        'electrical' => 1.0,
        'plumbing' => 0.9,
        'fire_protection' => 1.1
    ];
    
    return $factors[$discipline_key] ?? 1.0;
}

function estimateLaborHours($discipline_key, $base_cost) {
    $labor_rates = [
        'hvac' => 0.15,
        'electrical' => 0.12,
        'plumbing' => 0.10,
        'fire_protection' => 0.13
    ];
    
    $labor_rate_per_dollar = $labor_rates[$discipline_key] ?? 0.12;
    return $base_cost * $labor_rate_per_dollar;
}

function saveCostSummary($db) {
    global $message, $messageType;
    
    $summary_data = $_SESSION['cost_summary'] ?? null;
    
    if (!$summary_data) {
        $message = "No summary data to save. Please generate a summary first.";
        $messageType = "error";
        return;
    }
    
    try {
        $insert_query = "INSERT INTO cost_summaries (
            project_id, summary_type, breakdown_data, total_cost, 
            include_contingency, contingency_percentage, created_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $breakdown_json = json_encode($summary_data['data']);
        $stmt = $db->executeQuery($insert_query, [
            $summary_data['project']['id'],
            $summary_data['summary_type'],
            $breakdown_json,
            $summary_data['data']['total_cost'],
            $summary_data['include_contingency'],
            $summary_data['contingency_percentage'],
            $summary_data['generated_date']
        ]);
        
        $message = "Cost summary saved successfully.";
        $messageType = "success";
        
    } catch (Exception $e) {
        $message = "Error saving summary: " . $e->getMessage();
        $messageType = "error";
    }
}

function compareBudgets($db) {
    global $message, $messageType;
    
    $project_id = $_POST['project_id'] ?? '';
    $budget_amount = floatval($_POST['budget_amount'] ?? 0);
    
    if (empty($project_id) || $budget_amount <= 0) {
        $message = "Please provide project ID and budget amount.";
        $messageType = "error";
        return;
    }
    
    // Get current cost summary
    $summary_data = $_SESSION['cost_summary'] ?? null;
    
    if (!$summary_data || $summary_data['project']['id'] != $project_id) {
        // Generate summary if not exists
        generateCostSummary($db);
        $summary_data = $_SESSION['cost_summary'];
    }
    
    if (!$summary_data) {
        $message = "Could not generate cost summary for comparison.";
        $messageType = "error";
        return;
    }
    
    $actual_cost = $summary_data['data']['total_cost'];
    $variance = $actual_cost - $budget_amount;
    $variance_percentage = $budget_amount > 0 ? ($variance / $budget_amount) * 100 : 0;
    
    $_SESSION['budget_comparison'] = [
        'budget_amount' => $budget_amount,
        'actual_cost' => $actual_cost,
        'variance' => $variance,
        'variance_percentage' => $variance_percentage,
        'status' => $variance <= 0 ? 'Under Budget' : 'Over Budget'
    ];
    
    $message = "Budget comparison completed.";
    $messageType = "success";
}

// Get projects for dropdown
$projects_query = "SELECT id, project_name FROM projects ORDER BY project_name";
$projects_result = $db->executeQuery($projects_query);
$projects = [];
while ($row = $projects_result->fetch()) {
    $projects[] = $row;
}

// Get saved summaries
$summaries_query = "SELECT cs.*, p.project_name 
                   FROM cost_summaries cs 
                   JOIN projects p ON cs.project_id = p.id 
                   ORDER BY cs.created_date DESC LIMIT 10";
$saved_summaries_result = $db->executeQuery($summaries_query);
$saved_summaries = [];
while ($row = $saved_summaries_result->fetch()) {
    $saved_summaries[] = $row;
}

$cost_summary = $_SESSION['cost_summary'] ?? null;
$budget_comparison = $_SESSION['budget_comparison'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP Cost Summary Generator</title>
    <link rel="stylesheet" href="../../../../assets/css/estimation.css">
    <link rel="stylesheet" href="../../../../assets/css/project-management.css">
    <style>
        .summary-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .summary-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .summary-header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        
        .summary-header p {
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
        .form-group input[type="number"], 
        .form-group input[type="text"] {
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
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 15px 0;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
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
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .summary-results {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .summary-title {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .cost-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .cost-table th,
        .cost-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .cost-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .cost-table tr:hover {
            background: #f8f9fa;
        }
        
        .cost-table .amount {
            text-align: right;
            font-weight: 600;
        }
        
        .cost-table .percentage {
            text-align: right;
            color: #6c757d;
        }
        
        .total-row {
            background: #667eea !important;
            color: white;
            font-weight: bold;
        }
        
        .total-row th,
        .total-row td {
            border-color: #667eea;
        }
        
        .cost-summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .cost-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .cost-card h3 {
            margin: 0 0 10px 0;
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .cost-card .amount {
            font-size: 2em;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .budget-comparison {
            background: #e8f5e8;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .budget-comparison.over-budget {
            background: #f8e8e8;
            border-color: #dc3545;
        }
        
        .comparison-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .comparison-item {
            text-align: center;
        }
        
        .comparison-item h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .comparison-item .value {
            font-size: 1.5em;
            font-weight: bold;
            color: #667eea;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .status-badge.under-budget {
            background: #28a745;
            color: white;
        }
        
        .status-badge.over-budget {
            background: #dc3545;
            color: white;
        }
        
        .saved-summaries {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .saved-summary-item {
            border: 1px solid #e1e5e9;
            border-radius: 6px;
            padding: 15px;
            margin: 10px 0;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .saved-summary-item:hover {
            background: #f8f9fa;
            border-color: #667eea;
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
        
        .chart-container {
            height: 400px;
            margin: 20px 0;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="summary-container">
        <div class="summary-header">
            <h1>MEP Cost Summary Generator</h1>
            <p>Comprehensive cost analysis and reporting for MEP projects</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Cost Summary Form -->
        <div class="form-section">
            <h2>Generate Cost Summary</h2>
            <form method="POST" id="costSummaryForm">
                <input type="hidden" name="action" value="generate_summary">
                
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
                        <label for="cost_breakdown">Cost Breakdown Type</label>
                        <select name="cost_breakdown" id="cost_breakdown" required>
                            <option value="system">System Breakdown</option>
                            <option value="phase">Phase Breakdown</option>
                            <option value="discipline">Discipline Breakdown</option>
                            <option value="material">Material Category Breakdown</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="contingency_percentage">Contingency Percentage (%)</label>
                        <input type="number" name="contingency_percentage" id="contingency_percentage" 
                               value="10" min="0" max="50" step="0.1">
                    </div>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" name="include_contingency" id="include_contingency" value="1">
                    <label for="include_contingency">Include contingency in calculations</label>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Generate Summary</button>
                    <button type="button" class="btn btn-secondary" onclick="clearForm()">Clear Form</button>
                </div>
            </form>
        </div>
        
        <!-- Budget Comparison Form -->
        <div class="form-section">
            <h2>Budget Comparison</h2>
            <form method="POST" id="budgetComparisonForm">
                <input type="hidden" name="action" value="compare_budgets">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="budget_project_id">Project</label>
                        <select name="project_id" id="budget_project_id" required>
                            <option value="">Choose a project...</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?php echo $project['id']; ?>">
                                    <?php echo htmlspecialchars($project['project_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="budget_amount">Budget Amount</label>
                        <input type="number" name="budget_amount" id="budget_amount" 
                               placeholder="Enter budget amount" min="0" step="0.01" required>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">Compare Budget</button>
                </div>
            </form>
        </div>
        
        <!-- Summary Results -->
        <?php if ($cost_summary): ?>
            <div class="summary-results">
                <h2 class="summary-title">
                    <?php echo htmlspecialchars($cost_summary['data']['summary_type']); ?> - 
                    <?php echo htmlspecialchars($cost_summary['project']['project_name']); ?>
                </h2>
                
                <div class="cost-summary-cards">
                    <div class="cost-card">
                        <h3>Total Cost</h3>
                        <div class="amount">$<?php echo number_format($cost_summary['data']['total_cost'], 2); ?></div>
                    </div>
                    
                    <?php if ($cost_summary['include_contingency']): ?>
                        <div class="cost-card">
                            <h3>With Contingency (<?php echo $cost_summary['contingency_percentage']; ?>%)</h3>
                            <div class="amount">
                                $<?php echo number_format(
                                    $cost_summary['data']['total_cost'] * (1 + $cost_summary['contingency_percentage'] / 100), 
                                    2
                                ); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="cost-card">
                        <h3>Generated</h3>
                        <div class="amount"><?php echo date('M j, Y', strtotime($cost_summary['generated_date'])); ?></div>
                    </div>
                </div>
                
                <table class="cost-table">
                    <thead>
                        <tr>
                            <th>
                                <?php
                                switch ($cost_summary['breakdown_type']) {
                                    case 'system': echo 'System'; break;
                                    case 'phase': echo 'Phase'; break;
                                    case 'discipline': echo 'Discipline'; break;
                                    case 'material': echo 'Category'; break;
                                }
                                ?>
                            </th>
                            <th class="amount">Cost</th>
                            <th class="percentage">Percentage</th>
                            <?php if ($cost_summary['breakdown_type'] === 'system'): ?>
                                <th class="amount">Materials</th>
                                <th class="amount">Labor</th>
                                <th class="amount">Equipment</th>
                            <?php elseif ($cost_summary['breakdown_type'] === 'phase'): ?>
                                <th>Duration</th>
                            <?php elseif ($cost_summary['breakdown_type'] === 'discipline'): ?>
                                <th>Labor Hours</th>
                            <?php elseif ($cost_summary['breakdown_type'] === 'material'): ?>
                                <th>Items</th>
                                <th class="amount">Avg Unit Cost</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cost_summary['data']['breakdown'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(
                                    $item['system'] ?? $item['phase'] ?? $item['discipline'] ?? $item['category'] ?? ''
                                ); ?></td>
                                <td class="amount">$<?php echo number_format(
                                    $item['total_cost'] ?? $item['cost'] ?? 0, 2
                                ); ?></td>
                                <td class="percentage"><?php echo number_format($item['percentage'], 1); ?>%</td>
                                
                                <?php if ($cost_summary['breakdown_type'] === 'system'): ?>
                                    <td class="amount">$<?php echo number_format($item['material_cost'], 2); ?></td>
                                    <td class="amount">$<?php echo number_format($item['labor_cost'], 2); ?></td>
                                    <td class="amount">$<?php echo number_format($item['equipment_cost'], 2); ?></td>
                                <?php elseif ($cost_summary['breakdown_type'] === 'phase'): ?>
                                    <td><?php echo htmlspecialchars($item['duration']); ?></td>
                                <?php elseif ($cost_summary['breakdown_type'] === 'discipline'): ?>
                                    <td><?php echo number_format($item['labor_hours'], 0); ?> hrs</td>
                                <?php elseif ($cost_summary['breakdown_type'] === 'material'): ?>
                                    <td><?php echo $item['item_count']; ?></td>
                                    <td class="amount">$<?php echo number_format($item['avg_unit_cost'], 2); ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        
                        <tr class="total-row">
                            <th>Total</th>
                            <th class="amount">$<?php echo number_format($cost_summary['data']['total_cost'], 2); ?></th>
                            <th class="percentage">100.0%</th>
                            <th colspan="<?php echo $cost_summary['breakdown_type'] === 'system' ? '3' : '2'; ?>"></th>
                        </tr>
                    </tbody>
                </table>
                
                <div class="btn-group">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="save_summary">
                        <button type="submit" class="btn btn-success">Save Summary</button>
                    </form>
                    <button type="button" class="btn btn-secondary" onclick="exportToPDF()">Export to PDF</button>
                    <button type="button" class="btn btn-secondary" onclick="exportToExcel()">Export to Excel</button>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Budget Comparison Results -->
        <?php if ($budget_comparison): ?>
            <div class="summary-results">
                <h2 class="summary-title">Budget Comparison Analysis</h2>
                
                <div class="budget-comparison <?php echo $budget_comparison['status'] === 'Over Budget' ? 'over-budget' : ''; ?>">
                    <div class="comparison-grid">
                        <div class="comparison-item">
                            <h4>Budget Amount</h4>
                            <div class="value">$<?php echo number_format($budget_comparison['budget_amount'], 2); ?></div>
                        </div>
                        
                        <div class="comparison-item">
                            <h4>Actual Cost</h4>
                            <div class="value">$<?php echo number_format($budget_comparison['actual_cost'], 2); ?></div>
                        </div>
                        
                        <div class="comparison-item">
                            <h4>Variance</h4>
                            <div class="value" style="color: <?php echo $budget_comparison['variance'] <= 0 ? '#28a745' : '#dc3545'; ?>">
                                $<?php echo number_format(abs($budget_comparison['variance']), 2); ?>
                            </div>
                        </div>
                        
                        <div class="comparison-item">
                            <h4>Variance %</h4>
                            <div class="value" style="color: <?php echo $budget_comparison['variance_percentage'] <= 0 ? '#28a745' : '#dc3545'; ?>">
                                <?php echo number_format(abs($budget_comparison['variance_percentage']), 1); ?>%
                            </div>
                        </div>
                    </div>
                    
                    <div class="status-badge <?php echo strtolower(str_replace(' ', '-', $budget_comparison['status'])); ?>">
                        <?php echo $budget_comparison['status']; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Saved Summaries -->
        <div class="saved-summaries">
            <h2>Recent Cost Summaries</h2>
            
            <?php if (empty($saved_summaries)): ?>
                <p>No saved cost summaries found.</p>
            <?php else: ?>
                <?php foreach ($saved_summaries as $summary): ?>
                    <div class="saved-summary-item" onclick="loadSummary(<?php echo $summary['id']; ?>)">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h4><?php echo htmlspecialchars($summary['project_name']); ?></h4>
                                <p><?php echo htmlspecialchars($summary['summary_type']); ?></p>
                                <small>Created: <?php echo date('M j, Y g:i A', strtotime($summary['created_date'])); ?></small>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.5em; font-weight: bold; color: #667eea;">
                                    $<?php echo number_format($summary['total_cost'], 2); ?>
                                </div>
                                <?php if ($summary['include_contingency']): ?>
                                    <small>+<?php echo $summary['contingency_percentage']; ?>% contingency</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function clearForm() {
            document.getElementById('costSummaryForm').reset();
            document.getElementById('budgetComparisonForm').reset();
        }
        
        function exportToPDF() {
            // PDF export functionality
            alert('PDF export functionality will be implemented with a PDF library');
        }
        
        function exportToExcel() {
            // Excel export functionality  
            alert('Excel export functionality will be implemented with Excel library');
        }
        
        function loadSummary(summaryId) {
            // Load saved summary
            alert('Load summary functionality for ID: ' + summaryId);
        }
        
        // Sync project selection between forms
        document.getElementById('project_id').addEventListener('change', function() {
            document.getElementById('budget_project_id').value = this.value;
        });
        
        document.getElementById('budget_project_id').addEventListener('change', function() {
            document.getElementById('project_id').value = this.value;
        });
    </script>
</body>
</html>


