<?php
/**
 * Energy Consumption Analysis and Optimization
 * Comprehensive energy consumption analysis and optimization for MEP systems
 * Building energy modeling, system efficiency analysis, energy cost calculations, and sustainability metrics
 */

require_once '../../../app/Config/config.php';
require_once '../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Helpers/functions.php';

// Initialize database connection
$db = new Database();

// Get project data
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

// Handle form submissions
$message = '';
$message_type = '';

if ($_POST) {
    try {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'calculate_energy':
                $result = calculateEnergyConsumption($_POST);
                $message = 'Energy consumption calculated successfully!';
                $message_type = 'success';
                $calculation_data = $result;
                break;
                
            case 'optimize_systems':
                $result = optimizeMepSystems($_POST);
                $message = 'System optimization completed!';
                $message_type = 'success';
                $optimization_data = $result;
                break;
                
            case 'save_project':
                $result = saveEnergyProject($_POST, $project_id);
                if ($result) {
                    $message = 'Project saved successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error saving project.';
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get saved projects
$saved_projects = [];
if ($project_id > 0) {
    $query = "SELECT * FROM mep_energy_projects WHERE id = ?";
    $saved_projects = $db->query($query, [$project_id]);
}

/**
 * Calculate comprehensive energy consumption
 */
function calculateEnergyConsumption($data) {
    // Building parameters
    $building_area = floatval($data['building_area'] ?? 0);
    $building_height = floatval($data['building_height'] ?? 0);
    $building_type = $data['building_type'] ?? 'office';
    $occupancy_count = intval($data['occupancy_count'] ?? 0);
    $operating_hours = intval($data['operating_hours'] ?? 8);
    $operating_days = intval($data['operating_days'] ?? 5);
    
    // Climate parameters
    $location = $data['location'] ?? 'temperate';
    $climate_zone = $data['climate_zone'] ?? 'zone_4';
    
    // System specifications
    $hvac_type = $data['hvac_type'] ?? 'split';
    $lighting_type = $data['lighting_type'] ?? 'led';
    $equipment_efficiency = floatval($data['equipment_efficiency'] ?? 0.8);
    $ventilation_rate = floatval($data['ventilation_rate'] ?? 5);
    
    // Energy rates
    $electricity_rate = floatval($data['electricity_rate'] ?? 0.12); // $/kWh
    $gas_rate = floatval($data['gas_rate'] ?? 0.85); // $/therm
    
    // Base energy consumption calculations (kWh/m²/year)
    $base_consumption = getBaseEnergyConsumption($building_type, $climate_zone);
    
    // HVAC energy consumption
    $hvac_consumption = calculateHvacEnergy($building_area, $hvac_type, $building_type, $climate_zone);
    
    // Lighting energy consumption
    $lighting_consumption = calculateLightingEnergy($building_area, $lighting_type, $building_type);
    
    // Equipment energy consumption
    $equipment_consumption = calculateEquipmentEnergy($building_area, $equipment_efficiency, $building_type);
    
    // Ventilation energy consumption
    $ventilation_consumption = calculateVentilationEnergy($building_area, $ventilation_rate, $hvac_type);
    
    // Total annual energy consumption
    $total_energy = $hvac_consumption + $lighting_consumption + $equipment_consumption + $ventilation_consumption;
    
    // Energy costs
    $electricity_consumption = $total_energy * 0.7; // 70% electricity
    $gas_consumption = $total_energy * 0.3; // 30% gas
    $annual_cost = ($electricity_consumption * $electricity_rate) + ($gas_consumption * $gas_rate);
    
    // Efficiency metrics
    $eui = $total_energy / $building_area; // Energy Use Intensity
    $carbon_footprint = $total_energy * 0.5; // kg CO2/year (approximate)
    
    return [
        'hvac_consumption' => $hvac_consumption,
        'lighting_consumption' => $lighting_consumption,
        'equipment_consumption' => $equipment_consumption,
        'ventilation_consumption' => $ventilation_consumption,
        'total_energy' => $total_energy,
        'electricity_consumption' => $electricity_consumption,
        'gas_consumption' => $gas_consumption,
        'annual_cost' => $annual_cost,
        'eui' => $eui,
        'carbon_footprint' => $carbon_footprint,
        'breakdown' => [
            'HVAC' => ($hvac_consumption / $total_energy) * 100,
            'Lighting' => ($lighting_consumption / $total_energy) * 100,
            'Equipment' => ($equipment_consumption / $total_energy) * 100,
            'Ventilation' => ($ventilation_consumption / $total_energy) * 100
        ]
    ];
}

/**
 * Get base energy consumption by building type and climate zone
 */
function getBaseEnergyConsumption($building_type, $climate_zone) {
    $base_values = [
        'office' => ['zone_1' => 150, 'zone_2' => 180, 'zone_3' => 220, 'zone_4' => 280, 'zone_5' => 320],
        'residential' => ['zone_1' => 80, 'zone_2' => 100, 'zone_3' => 130, 'zone_4' => 170, 'zone_5' => 210],
        'retail' => ['zone_1' => 200, 'zone_2' => 240, 'zone_3' => 290, 'zone_4' => 350, 'zone_5' => 400],
        'industrial' => ['zone_1' => 300, 'zone_2' => 350, 'zone_3' => 420, 'zone_4' => 500, 'zone_5' => 580],
        'healthcare' => ['zone_1' => 350, 'zone_2' => 420, 'zone_3' => 500, 'zone_4' => 600, 'zone_5' => 700]
    ];
    
    return $base_values[$building_type][$climate_zone] ?? 250;
}

/**
 * Calculate HVAC energy consumption
 */
function calculateHvacEnergy($area, $hvac_type, $building_type, $climate_zone) {
    $hvac_multipliers = [
        'split' => 1.0,
        'vrf' => 0.85,
        'chiller' => 0.9,
        'geothermal' => 0.6,
        'package' => 1.1
    ];
    
    $building_loads = [
        'office' => 40,
        'residential' => 25,
        'retail' => 50,
        'industrial' => 60,
        'healthcare' => 55
    ];
    
    $climate_factors = [
        'zone_1' => 0.8, 'zone_2' => 0.9, 'zone_3' => 1.0, 'zone_4' => 1.2, 'zone_5' => 1.4
    ];
    
    $base_load = $building_loads[$building_type] ?? 40;
    $hvac_multiplier = $hvac_multipliers[$hvac_type] ?? 1.0;
    $climate_factor = $climate_factors[$climate_zone] ?? 1.0;
    
    return $area * $base_load * $hvac_multiplier * $climate_factor / 1000;
}

/**
 * Calculate lighting energy consumption
 */
function calculateLightingEnergy($area, $lighting_type, $building_type) {
    $lighting_densities = [
        'incandescent' => 20,
        'fluorescent' => 12,
        'led' => 6,
        'smart_led' => 4
    ];
    
    $building_factors = [
        'office' => 1.2,
        'residential' => 0.8,
        'retail' => 1.5,
        'industrial' => 1.0,
        'healthcare' => 1.3
    ];
    
    $power_density = $lighting_densities[$lighting_type] ?? 6;
    $building_factor = $building_factors[$building_type] ?? 1.0;
    
    return $area * $power_density * $building_factor / 1000;
}

/**
 * Calculate equipment energy consumption
 */
function calculateEquipmentEnergy($area, $efficiency, $building_type) {
    $equipment_densities = [
        'office' => 8,
        'residential' => 3,
        'retail' => 5,
        'industrial' => 25,
        'healthcare' => 15
    ];
    
    $base_density = $equipment_densities[$building_type] ?? 8;
    $efficiency_factor = 1 / $efficiency; // Lower efficiency = higher consumption
    
    return $area * $base_density * $efficiency_factor / 1000;
}

/**
 * Calculate ventilation energy consumption
 */
function calculateVentilationEnergy($area, $ventilation_rate, $hvac_type) {
    $ventilation_efficiency = [
        'split' => 1.0,
        'vrf' => 0.9,
        'chiller' => 0.85,
        'geothermal' => 0.7,
        'package' => 1.1
    ];
    
    $efficiency = $ventilation_efficiency[$hvac_type] ?? 1.0;
    
    return $area * $ventilation_rate * 0.05 * $efficiency; // Simplified calculation
}

/**
 * Optimize MEP systems for energy efficiency
 */
function optimizeMepSystems($data) {
    $current_consumption = calculateEnergyConsumption($data);
    
    $optimizations = [];
    
    // HVAC optimizations
    $hvac_optimizations = optimizeHvacSystem($data);
    $optimizations['hvac'] = $hvac_optimizations;
    
    // Lighting optimizations
    $lighting_optimizations = optimizeLightingSystem($data);
    $optimizations['lighting'] = $lighting_optimizations;
    
    // Equipment optimizations
    $equipment_optimizations = optimizeEquipment($data);
    $optimizations['equipment'] = $equipment_optimizations;
    
    // Calculate potential savings
    $total_current_cost = $current_consumption['annual_cost'];
    $total_optimized_cost = 0;
    
    foreach ($optimizations as $system => $opt) {
        $total_optimized_cost += $opt['annual_cost'];
    }
    
    $total_savings = $total_current_cost - $total_optimized_cost;
    $savings_percentage = ($total_savings / $total_current_cost) * 100;
    
    return [
        'current_consumption' => $current_consumption,
        'optimizations' => $optimizations,
        'total_savings' => $total_savings,
        'savings_percentage' => $savings_percentage,
        'payback_period' => calculatePaybackPeriod($optimizations, $total_savings)
    ];
}

/**
 * Optimize HVAC system
 */
function optimizeHvacSystem($data) {
    $current_type = $data['hvac_type'] ?? 'split';
    $area = floatval($data['building_area'] ?? 0);
    
    $optimal_type = determineOptimalHvac($area, $data['building_type'] ?? 'office');
    
    // Calculate savings
    $current_hvac_consumption = calculateHvacEnergy($area, $current_type, $data['building_type'], $data['climate_zone']);
    $optimized_hvac_consumption = calculateHvacEnergy($area, $optimal_type, $data['building_type'], $data['climate_zone']);
    
    $energy_savings = $current_hvac_consumption - $optimized_hvac_consumption;
    $cost_savings = $energy_savings * ($data['electricity_rate'] ?? 0.12);
    
    return [
        'current_type' => $current_type,
        'optimal_type' => $optimal_type,
        'energy_savings' => $energy_savings,
        'cost_savings' => $cost_savings,
        'annual_cost' => $optimized_hvac_consumption * ($data['electricity_rate'] ?? 0.12),
        'recommendations' => generateHvacRecommendations($optimal_type)
    ];
}

/**
 * Determine optimal HVAC type
 */
function determineOptimalHvac($area, $building_type) {
    if ($area > 50000) {
        return 'chiller';
    } elseif ($area > 20000) {
        return 'vrf';
    } elseif ($area > 5000) {
        return 'package';
    } else {
        return 'split';
    }
}

/**
 * Generate HVAC recommendations
 */
function generateHvacRecommendations($hvac_type) {
    $recommendations = [];
    
    switch ($hvac_type) {
        case 'geothermal':
            $recommendations[] = 'Install geothermal heat pump system';
            $recommendations[] = 'Consider ground loop installation';
            $recommendations[] = 'Implement variable speed controls';
            break;
        case 'vrf':
            $recommendations[] = 'Implement VRF system with heat recovery';
            $recommendations[] = 'Install individual zone controls';
            $recommendations[] = 'Consider demand-controlled ventilation';
            break;
        case 'chiller':
            $recommendations[] = 'Install high-efficiency chiller';
            $recommendations[] = 'Implement variable speed drives';
            $recommendations[] = 'Consider chilled water system optimization';
            break;
        default:
            $recommendations[] = 'Upgrade to high-efficiency equipment';
            $recommendations[] = 'Implement programmable thermostats';
            $recommendations[] = 'Add zone control capabilities';
    }
    
    return $recommendations;
}

/**
 * Optimize lighting system
 */
function optimizeLightingSystem($data) {
    $current_type = $data['lighting_type'] ?? 'led';
    $area = floatval($data['building_area'] ?? 0);
    
    $optimal_type = 'smart_led'; // Always optimal
    
    $current_lighting_consumption = calculateLightingEnergy($area, $current_type, $data['building_type']);
    $optimized_lighting_consumption = calculateLightingEnergy($area, $optimal_type, $data['building_type']);
    
    $energy_savings = $current_lighting_consumption - $optimized_lighting_consumption;
    $cost_savings = $energy_savings * ($data['electricity_rate'] ?? 0.12);
    
    return [
        'current_type' => $current_type,
        'optimal_type' => $optimal_type,
        'energy_savings' => $energy_savings,
        'cost_savings' => $cost_savings,
        'annual_cost' => $optimized_lighting_consumption * ($data['electricity_rate'] ?? 0.12),
        'recommendations' => [
            'Install LED lighting throughout',
            'Implement smart lighting controls',
            'Add occupancy sensors',
            'Consider daylight harvesting',
            'Install dimming controls'
        ]
    ];
}

/**
 * Optimize equipment
 */
function optimizeEquipment($data) {
    $current_efficiency = floatval($data['equipment_efficiency'] ?? 0.8);
    $optimal_efficiency = 0.95; // Target efficiency
    
    $area = floatval($data['building_area'] ?? 0);
    
    $current_equipment_consumption = calculateEquipmentEnergy($area, $current_efficiency, $data['building_type']);
    $optimized_equipment_consumption = calculateEquipmentEnergy($area, $optimal_efficiency, $data['building_type']);
    
    $energy_savings = $current_equipment_consumption - $optimized_equipment_consumption;
    $cost_savings = $energy_savings * ($data['electricity_rate'] ?? 0.12);
    
    return [
        'current_efficiency' => $current_efficiency,
        'optimal_efficiency' => $optimal_efficiency,
        'energy_savings' => $energy_savings,
        'cost_savings' => $cost_savings,
        'annual_cost' => $optimized_equipment_consumption * ($data['electricity_rate'] ?? 0.12),
        'recommendations' => [
            'Upgrade to ENERGY STAR equipment',
            'Implement power management settings',
            'Install smart power strips',
            'Consider equipment scheduling',
            'Implement monitoring systems'
        ]
    ];
}

/**
 * Calculate payback period
 */
function calculatePaybackPeriod($optimizations, $annual_savings) {
    $total_investment = 0;
    
    foreach ($optimizations as $system => $opt) {
        // Estimate implementation costs
        $total_investment += estimateImplementationCost($system, $opt);
    }
    
    return $total_investment / $annual_savings;
}

/**
 * Estimate implementation cost
 */
function estimateImplementationCost($system, $opt) {
    $costs = [
        'hvac' => 50000,
        'lighting' => 15000,
        'equipment' => 25000
    ];
    
    return $costs[$system] ?? 30000;
}

/**
 * Save energy project data
 */
function saveEnergyProject($data, $project_id) {
    global $db;
    
    $project_data = [
        'building_area' => floatval($data['building_area']),
        'building_height' => floatval($data['building_height']),
        'building_type' => $data['building_type'],
        'occupancy_count' => intval($data['occupancy_count']),
        'operating_hours' => intval($data['operating_hours']),
        'climate_zone' => $data['climate_zone'],
        'hvac_type' => $data['hvac_type'],
        'lighting_type' => $data['lighting_type'],
        'equipment_efficiency' => floatval($data['equipment_efficiency']),
        'ventilation_rate' => floatval($data['ventilation_rate']),
        'electricity_rate' => floatval($data['electricity_rate']),
        'gas_rate' => floatval($data['gas_rate']),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if ($project_id > 0) {
        $query = "UPDATE mep_energy_projects SET " . 
                 implode(', ', array_map(fn($k) => "$k = ?", array_keys($project_data))) . 
                 " WHERE id = ?";
        return $db->execute($query, array_merge(array_values($project_data), [$project_id]));
    } else {
        $query = "INSERT INTO mep_energy_projects (" . 
                 implode(', ', array_keys($project_data)) . 
                 ", created_at) VALUES (" . 
                 implode(', ', array_fill(0, count($project_data), '?')) . 
                 ", NOW())";
        return $db->execute($query, array_values($project_data));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Consumption Analysis - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .energy-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .energy-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn {
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 10px 10px 0;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #45a049;
        }
        
        .btn-secondary {
            background: #666;
        }
        
        .btn-secondary:hover {
            background: #555;
        }
        
        .results-section {
            display: none;
            margin-top: 30px;
        }
        
        .results-section.active {
            display: block;
        }
        
        .energy-breakdown {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .energy-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .energy-value {
            font-size: 24px;
            font-weight: 600;
            color: #4CAF50;
        }
        
        .energy-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .optimization-item {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #4CAF50;
        }
        
        .savings-highlight {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .savings-amount {
            font-size: 28px;
            font-weight: 600;
            color: #28a745;
        }
        
        .chart-container {
            height: 300px;
            margin: 20px 0;
            position: relative;
        }
        
        .recommendations {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .recommendations h4 {
            color: #856404;
            margin-bottom: 15px;
        }
        
        .recommendations ul {
            list-style-type: disc;
            padding-left: 20px;
        }
        
        .recommendations li {
            margin: 8px 0;
            color: #856404;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .energy-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <?php include '../../../themes/default/views/partials/header.php'; ?>
    
    <div class="energy-container">
        <div class="page-header">
            <h1>Energy Consumption Analysis & Optimization</h1>
            <p>Comprehensive energy consumption analysis and MEP system optimization</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="energy-grid">
            <!-- Input Form -->
            <div class="card">
                <div class="card-header">Building & System Parameters</div>
                
                <form method="POST" id="energy-form">
                    <input type="hidden" name="action" value="calculate_energy">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="building_area">Building Area (m²)</label>
                            <input type="number" id="building_area" name="building_area" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['building_area'] ?? ''); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="building_height">Building Height (m)</label>
                            <input type="number" id="building_height" name="building_height" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['building_height'] ?? ''); ?>" 
                                   step="0.1" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="building_type">Building Type</label>
                            <select id="building_type" name="building_type" required>
                                <option value="office" <?php echo (($saved_projects[0]['building_type'] ?? '') === 'office') ? 'selected' : ''; ?>>Office</option>
                                <option value="residential" <?php echo (($saved_projects[0]['building_type'] ?? '') === 'residential') ? 'selected' : ''; ?>>Residential</option>
                                <option value="retail" <?php echo (($saved_projects[0]['building_type'] ?? '') === 'retail') ? 'selected' : ''; ?>>Retail</option>
                                <option value="industrial" <?php echo (($saved_projects[0]['building_type'] ?? '') === 'industrial') ? 'selected' : ''; ?>>Industrial</option>
                                <option value="healthcare" <?php echo (($saved_projects[0]['building_type'] ?? '') === 'healthcare') ? 'selected' : ''; ?>>Healthcare</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="climate_zone">Climate Zone</label>
                            <select id="climate_zone" name="climate_zone" required>
                                <option value="zone_1" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'zone_1') ? 'selected' : ''; ?>>Zone 1 (Hot-Humid)</option>
                                <option value="zone_2" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'zone_2') ? 'selected' : ''; ?>>Zone 2 (Hot-Dry)</option>
                                <option value="zone_3" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'zone_3') ? 'selected' : ''; ?>>Zone 3 (Warm-Humid)</option>
                                <option value="zone_4" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'zone_4') ? 'selected' : ''; ?>>Zone 4 (Mixed-Humid)</option>
                                <option value="zone_5" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'zone_5') ? 'selected' : ''; ?>>Zone 5 (Cool-Humid)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="occupancy_count">Occupancy Count</label>
                            <input type="number" id="occupancy_count" name="occupancy_count" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['occupancy_count'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="operating_hours">Daily Operating Hours</label>
                            <input type="number" id="operating_hours" name="operating_hours" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['operating_hours'] ?? '8'); ?>" min="1" max="24" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hvac_type">HVAC System Type</label>
                            <select id="hvac_type" name="hvac_type" required>
                                <option value="split" <?php echo (($saved_projects[0]['hvac_type'] ?? '') === 'split') ? 'selected' : ''; ?>>Split System</option>
                                <option value="vrf" <?php echo (($saved_projects[0]['hvac_type'] ?? '') === 'vrf') ? 'selected' : ''; ?>>VRF System</option>
                                <option value="chiller" <?php echo (($saved_projects[0]['hvac_type'] ?? '') === 'chiller') ? 'selected' : ''; ?>>Chiller System</option>
                                <option value="geothermal" <?php echo (($saved_projects[0]['hvac_type'] ?? '') === 'geothermal') ? 'selected' : ''; ?>>Geothermal</option>
                                <option value="package" <?php echo (($saved_projects[0]['hvac_type'] ?? '') === 'package') ? 'selected' : ''; ?>>Package Unit</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="lighting_type">Lighting Type</label>
                            <select id="lighting_type" name="lighting_type" required>
                                <option value="incandescent" <?php echo (($saved_projects[0]['lighting_type'] ?? '') === 'incandescent') ? 'selected' : ''; ?>>Incandescent</option>
                                <option value="fluorescent" <?php echo (($saved_projects[0]['lighting_type'] ?? '') === 'fluorescent') ? 'selected' : ''; ?>>Fluorescent</option>
                                <option value="led" <?php echo (($saved_projects[0]['lighting_type'] ?? '') === 'led') ? 'selected' : ''; ?>>LED</option>
                                <option value="smart_led" <?php echo (($saved_projects[0]['lighting_type'] ?? '') === 'smart_led') ? 'selected' : ''; ?>>Smart LED</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="equipment_efficiency">Equipment Efficiency</label>
                            <input type="number" id="equipment_efficiency" name="equipment_efficiency" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['equipment_efficiency'] ?? '0.8'); ?>" 
                                   min="0.1" max="1.0" step="0.05" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="ventilation_rate">Ventilation Rate (ACH)</label>
                            <input type="number" id="ventilation_rate" name="ventilation_rate" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['ventilation_rate'] ?? '5'); ?>" 
                                   min="1" max="20" step="0.5" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="electricity_rate">Electricity Rate ($/kWh)</label>
                            <input type="number" id="electricity_rate" name="electricity_rate" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['electricity_rate'] ?? '0.12'); ?>" 
                                   step="0.01" min="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="gas_rate">Natural Gas Rate ($/therm)</label>
                            <input type="number" id="gas_rate" name="gas_rate" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['gas_rate'] ?? '0.85'); ?>" 
                                   step="0.05" min="0.1" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Calculate Energy Consumption</button>
                    <button type="button" class="btn btn-secondary" onclick="optimizeSystems()">Optimize Systems</button>
                </form>
            </div>
            
            <!-- Energy Analysis Chart -->
            <div class="card">
                <div class="card-header">Energy Consumption Overview</div>
                <div class="chart-container">
                    <canvas id="energyChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Results Section -->
        <div id="results-section" class="results-section">
            <div class="card">
                <div class="card-header">Energy Analysis Results</div>
                
                <div id="energy-results"></div>
            </div>
        </div>
        
        <!-- Optimization Section -->
        <div id="optimization-section" class="results-section">
            <div class="card">
                <div class="card-header">System Optimization Results</div>
                
                <div id="optimization-results"></div>
            </div>
        </div>
        
        <!-- Recommendations -->
        <div class="recommendations">
            <h4>Energy Efficiency Best Practices</h4>
            <ul>
                <li>Implement building automation systems for optimal control</li>
                <li>Regular maintenance of HVAC systems for peak efficiency</li>
                <li>Use natural lighting where possible to reduce artificial lighting needs</li>
                <li>Install occupancy sensors to minimize energy waste</li>
                <li>Consider renewable energy sources like solar panels</li>
                <li>Implement heat recovery ventilation systems</li>
                <li>Use high-efficiency equipment and appliances</li>
                <li>Regular energy audits to identify optimization opportunities</li>
            </ul>
        </div>
    </div>
    
    <?php include '../../../themes/default/views/partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let energyChart = null;
        
        function calculateEnergy() {
            const formData = new FormData(document.getElementById('energy-form'));
            formData.append('action', 'calculate_energy');
            
            fetch('energy-consumption.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayResults(data.results);
                } else {
                    showNotification('Error calculating energy consumption: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error calculating energy consumption', 'danger');
            });
        }
        
        function optimizeSystems() {
            const formData = new FormData(document.getElementById('energy-form'));
            formData.append('action', 'optimize_systems');
            
            fetch('energy-consumption.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayOptimizationResults(data.results);
                } else {
                    showNotification('Error optimizing systems: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error optimizing systems', 'danger');
            });
        }
        
        function displayResults(results) {
            document.getElementById('results-section').classList.add('active');
            
            const breakdownHtml = Object.entries(results.breakdown).map(([system, percentage]) => 
                `<div class="energy-item">
                    <div class="energy-value">${percentage.toFixed(1)}%</div>
                    <div class="energy-label">${system}</div>
                </div>`
            ).join('');
            
            const resultsHtml = `
                <div class="energy-breakdown">
                    <div class="energy-item">
                        <div class="energy-value">${(results.hvac_consumption / 1000).toFixed(1)} MWh</div>
                        <div class="energy-label">HVAC Energy</div>
                    </div>
                    <div class="energy-item">
                        <div class="energy-value">${(results.lighting_consumption / 1000).toFixed(1)} MWh</div>
                        <div class="energy-label">Lighting Energy</div>
                    </div>
                    <div class="energy-item">
                        <div class="energy-value">${(results.equipment_consumption / 1000).toFixed(1)} MWh</div>
                        <div class="energy-label">Equipment Energy</div>
                    </div>
                    <div class="energy-item">
                        <div class="energy-value">${(results.ventilation_consumption / 1000).toFixed(1)} MWh</div>
                        <div class="energy-label">Ventilation Energy</div>
                    </div>
                </div>
                
                <div class="savings-highlight">
                    <h3>Annual Energy Summary</h3>
                    <div class="energy-value">${(results.total_energy / 1000).toFixed(1)} MWh</div>
                    <div class="energy-label">Total Annual Consumption</div>
                    <div class="savings-amount">$${results.annual_cost.toLocaleString()}</div>
                    <div class="energy-label">Annual Energy Cost</div>
                    <div style="margin-top: 10px;">
                        <strong>EUI:</strong> ${results.eui.toFixed(1)} kWh/m²/year | 
                        <strong>Carbon Footprint:</strong> ${results.carbon_footprint.toFixed(0)} kg CO₂/year
                    </div>
                </div>
                
                <div class="chart-container">
                    <canvas id="breakdownChart"></canvas>
                </div>
            `;
            
            document.getElementById('energy-results').innerHTML = resultsHtml;
            createBreakdownChart(results.breakdown);
        }
        
        function displayOptimizationResults(optimization) {
            document.getElementById('optimization-section').classList.add('active');
            
            const optimizationsHtml = Object.entries(optimization.optimizations).map(([system, opt]) => 
                `<div class="optimization-item">
                    <h4>${system.charAt(0).toUpperCase() + system.slice(1)} Optimization</h4>
                    <p><strong>Current:</strong> ${opt.current_type || opt.current_efficiency}</p>
                    <p><strong>Optimal:</strong> ${opt.optimal_type || opt.optimal_efficiency}</p>
                    <p><strong>Energy Savings:</strong> ${(opt.energy_savings / 1000).toFixed(1)} MWh/year</p>
                    <p><strong>Cost Savings:</strong> $${opt.cost_savings.toLocaleString()}/year</p>
                    <div class="recommendations" style="margin-top: 10px;">
                        <h5>Recommendations:</h5>
                        <ul>
                            ${opt.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                        </ul>
                    </div>
                </div>`
            ).join('');
            
            const optimizationHtml = `
                <div class="savings-highlight">
                    <h3>Optimization Summary</h3>
                    <div class="savings-amount">$${optimization.total_savings.toLocaleString()}/year</div>
                    <div class="energy-label">Total Annual Savings (${optimization.savings_percentage.toFixed(1)}% reduction)</div>
                    <div style="margin-top: 10px;">
                        <strong>Payback Period:</strong> ${optimization.payback_period.toFixed(1)} years
                    </div>
                </div>
                
                ${optimizationsHtml}
            `;
            
            document.getElementById('optimization-results').innerHTML = optimizationHtml;
        }
        
        function createBreakdownChart(breakdown) {
            const ctx = document.getElementById('breakdownChart').getContext('2d');
            
            if (energyChart) {
                energyChart.destroy();
            }
            
            energyChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(breakdown),
                    datasets: [{
                        data: Object.values(breakdown),
                        backgroundColor: [
                            '#4CAF50',
                            '#2196F3',
                            '#FF9800',
                            '#9C27B0'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Energy Consumption Breakdown'
                        }
                    }
                }
            });
        }
        
        // Initialize chart on page load
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('energyChart').getContext('2d');
            
            energyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['HVAC', 'Lighting', 'Equipment', 'Ventilation'],
                    datasets: [{
                        label: 'Energy Consumption (MWh/year)',
                        data: [0, 0, 0, 0],
                        backgroundColor: '#4CAF50'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Energy Consumption by System'
                        }
                    }
                }
            });
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>



