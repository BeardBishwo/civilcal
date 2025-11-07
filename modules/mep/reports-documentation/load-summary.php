<?php
/**
 * MEP Load Summary Report Generator
 * Comprehensive load analysis and summary for MEP systems
 * Includes heating, cooling, electrical, and plumbing load calculations
 */

require_once '../../../includes/config.php';
require_once '../../../includes/Database.php';
require_once '../../../includes/functions.php';

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
            case 'calculate_loads':
                $result = performLoadCalculation($_POST);
                $message = 'Load calculations completed successfully!';
                $message_type = 'success';
                $load_data = $result;
                break;
                
            case 'generate_summary':
                $result = generateLoadSummary($_POST);
                $message = 'Load summary report generated!';
                $message_type = 'success';
                $summary_data = $result;
                break;
                
            case 'export_loads':
                $result = exportLoadData($_POST);
                if ($result) {
                    $message = 'Load data exported successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error exporting load data.';
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get saved load analyses
$saved_analyses = array();
if ($project_id > 0) {
    $query = "SELECT * FROM mep_load_analysis WHERE project_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $db->executeQuery($query, array($project_id));
    $saved_analyses = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : array();
}

/**
 * Perform comprehensive load calculations
 */
function performLoadCalculation($data) {
    $building_area = floatval($data['building_area'] ?? 0);
    $floors = intval($data['floors'] ?? 1);
    $occupancy_type = $data['occupancy_type'] ?? 'office';
    $climate_zone = $data['climate_zone'] ?? 'temperate';
    $building_type = $data['building_type'] ?? 'commercial';
    
    // Calculate heating load
    $heating_load = calculateHeatingLoad($building_area, $floors, $occupancy_type, $climate_zone);
    
    // Calculate cooling load
    $cooling_load = calculateCoolingLoad($building_area, $floors, $occupancy_type, $climate_zone);
    
    // Calculate electrical load
    $electrical_load = calculateElectricalLoad($building_area, $floors, $occupancy_type, $building_type);
    
    // Calculate plumbing load
    $plumbing_load = calculatePlumbingLoad($building_area, $floors, $occupancy_type);
    
    // Calculate fire protection load
    $fire_protection_load = calculateFireProtectionLoad($building_area, $floors, $occupancy_type);
    
    // Calculate total loads
    $total_loads = array(
        'heating' => $heating_load,
        'cooling' => $cooling_load,
        'electrical' => $electrical_load,
        'plumbing' => $plumbing_load,
        'fire_protection' => $fire_protection_load
    );
    
    // Calculate load distribution by floor
    $load_distribution = calculateLoadDistribution($total_loads, $floors);
    
    // Calculate peak demand factors
    $demand_factors = calculateDemandFactors($total_loads, $occupancy_type);
    
    // Calculate load diversity factors
    $diversity_factors = calculateDiversityFactors($total_loads, $occupancy_type);
    
    return array(
        'building_info' => array(
            'area' => $building_area,
            'floors' => $floors,
            'occupancy_type' => $occupancy_type,
            'climate_zone' => $climate_zone,
            'building_type' => $building_type,
            'calculation_date' => date('Y-m-d H:i:s')
        ),
        'load_results' => $total_loads,
        'load_distribution' => $load_distribution,
        'demand_factors' => $demand_factors,
        'diversity_factors' => $diversity_factors,
        'peak_demands' => calculatePeakDemands($total_loads, $demand_factors),
        'efficiency_ratings' => calculateEfficiencyRatings($total_loads, $building_type),
        'equipment_sizing' => calculateEquipmentSizing($total_loads, $building_type)
    );
}

/**
 * Calculate heating load
 */
function calculateHeatingLoad($area, $floors, $occupancy_type, $climate_zone) {
    $base_heating_load = $area * 60; // W/m² base heating load
    
    // Occupancy type adjustment
    $occupancy_factors = array(
        'office' => 1.0,
        'retail' => 1.2,
        'hospital' => 1.3,
        'school' => 1.1,
        'residential' => 0.9
    );
    
    $occupancy_factor = isset($occupancy_factors[$occupancy_type]) ? $occupancy_factors[$occupancy_type] : 1.0;
    
    // Climate zone adjustment
    $climate_factors = array(
        'cold' => 1.4,
        'temperate' => 1.0,
        'warm' => 0.7,
        'hot' => 0.5
    );
    
    $climate_factor = isset($climate_factors[$climate_zone]) ? $climate_factors[$climate_zone] : 1.0;
    
    $total_heating_load = $base_heating_load * $occupancy_factor * $climate_factor;
    
    return array(
        'sensible_load' => $total_heating_load * 0.8,
        'latent_load' => $total_heating_load * 0.2,
        'total_load' => $total_heating_load,
        'load_per_m2' => $total_heating_load / $area,
        'peak_demand' => $total_heating_load * 1.1
    );
}

/**
 * Calculate cooling load
 */
function calculateCoolingLoad($area, $floors, $occupancy_type, $climate_zone) {
    $base_cooling_load = $area * 80; // W/m² base cooling load
    
    // Occupancy type adjustment
    $occupancy_factors = array(
        'office' => 1.0,
        'retail' => 1.3,
        'hospital' => 1.4,
        'school' => 1.1,
        'residential' => 0.8
    );
    
    $occupancy_factor = isset($occupancy_factors[$occupancy_type]) ? $occupancy_factors[$occupancy_type] : 1.0;
    
    // Climate zone adjustment
    $climate_factors = array(
        'cold' => 0.6,
        'temperate' => 1.0,
        'warm' => 1.3,
        'hot' => 1.6
    );
    
    $climate_factor = isset($climate_factors[$climate_zone]) ? $climate_factors[$climate_zone] : 1.0;
    
    $total_cooling_load = $base_cooling_load * $occupancy_factor * $climate_factor;
    
    return array(
        'sensible_load' => $total_cooling_load * 0.7,
        'latent_load' => $total_cooling_load * 0.3,
        'total_load' => $total_cooling_load,
        'load_per_m2' => $total_cooling_load / $area,
        'peak_demand' => $total_cooling_load * 1.15
    );
}

/**
 * Calculate electrical load
 */
function calculateElectricalLoad($area, $floors, $occupancy_type, $building_type) {
    $base_electrical_load = $area * 25; // W/m² base electrical load
    
    // Building type adjustment
    $building_factors = array(
        'commercial' => 1.0,
        'industrial' => 1.4,
        'residential' => 0.7,
        'healthcare' => 1.3,
        'education' => 1.1
    );
    
    $building_factor = isset($building_factors[$building_type]) ? $building_factors[$building_type] : 1.0;
    
    // Occupancy density adjustment
    $occupancy_factors = array(
        'office' => 1.0,
        'retail' => 1.5,
        'hospital' => 1.8,
        'school' => 0.9,
        'residential' => 0.6
    );
    
    $occupancy_factor = isset($occupancy_factors[$occupancy_type]) ? $occupancy_factors[$occupancy_type] : 1.0;
    
    $total_electrical_load = $base_electrical_load * $building_factor * $occupancy_factor;
    
    return array(
        'lighting_load' => $total_electrical_load * 0.3,
        'power_load' => $total_electrical_load * 0.5,
        'hvac_load' => $total_electrical_load * 0.2,
        'total_load' => $total_electrical_load,
        'load_per_m2' => $total_electrical_load / $area,
        'demand_factor' => 0.8,
        'diversity_factor' => 0.75
    );
}

/**
 * Calculate plumbing load
 */
function calculatePlumbingLoad($area, $floors, $occupancy_type) {
    $base_water_demand = $area * 0.05; // L/m² base water demand
    
    // Occupancy type adjustment
    $occupancy_factors = array(
        'office' => 1.0,
        'retail' => 1.3,
        'hospital' => 2.0,
        'school' => 1.1,
        'residential' => 0.8
    );
    
    $occupancy_factor = isset($occupancy_factors[$occupancy_type]) ? $occupancy_factors[$occupancy_type] : 1.0;
    
    $total_water_demand = $base_water_demand * $occupancy_factor;
    
    return array(
        'cold_water_demand' => $total_water_demand,
        'hot_water_demand' => $total_water_demand * 0.4,
        'total_water_demand' => $total_water_demand * 1.4,
        'demand_per_m2' => $total_water_demand * 1.4 / $area,
        'peak_flow_rate' => ($total_water_demand * 1.4) * 2.5, // Peak factor
        'daily_consumption' => $total_water_demand * 1.4 * 8 // 8 hours operation
    );
}

/**
 * Calculate fire protection load
 */
function calculateFireProtectionLoad($area, $floors, $occupancy_type) {
    $base_fire_demand = $area * 0.1; // L/min/m² base fire protection demand
    
    // Occupancy hazard adjustment
    $hazard_factors = array(
        'office' => 1.0,
        'retail' => 1.2,
        'hospital' => 1.5,
        'school' => 1.1,
        'residential' => 0.9
    );
    
    $hazard_factor = isset($hazard_factors[$occupancy_type]) ? $hazard_factors[$occupancy_type] : 1.0;
    
    $total_fire_demand = $base_fire_demand * $hazard_factor;
    
    return array(
        'sprinkler_demand' => $total_fire_demand,
        'standpipe_demand' => $total_fire_demand * 0.5,
        'hose_demand' => $total_fire_demand * 0.3,
        'total_demand' => $total_fire_demand * 1.8,
        'demand_per_m2' => $total_fire_demand * 1.8 / $area,
        'storage_requirement' => $total_fire_demand * 1.8 * 60 // 60 minutes storage
    );
}

/**
 * Calculate load distribution by floor
 */
function calculateLoadDistribution($total_loads, $floors) {
    $distribution = array();
    
    for ($i = 1; $i <= $floors; $i++) {
        // Assume load distribution is roughly equal with slight variation
        $floor_factor = 0.8 + (rand(0, 40) / 100); // 0.8 to 1.2 variation
        
        $distribution['floor_' . $i] = array(
            'heating_load' => $total_loads['heating']['total_load'] * $floor_factor / $floors,
            'cooling_load' => $total_loads['cooling']['total_load'] * $floor_factor / $floors,
            'electrical_load' => $total_loads['electrical']['total_load'] * $floor_factor / $floors,
            'plumbing_load' => $total_loads['plumbing']['total_water_demand'] * $floor_factor / $floors,
            'fire_protection_load' => $total_loads['fire_protection']['total_demand'] * $floor_factor / $floors
        );
    }
    
    return $distribution;
}

/**
 * Calculate demand factors
 */
function calculateDemandFactors($total_loads, $occupancy_type) {
    $base_factors = array(
        'office' => array('heating' => 0.8, 'cooling' => 0.9, 'electrical' => 0.7, 'plumbing' => 0.6, 'fire_protection' => 1.0),
        'retail' => array('heating' => 0.9, 'cooling' => 1.0, 'electrical' => 0.8, 'plumbing' => 0.8, 'fire_protection' => 1.0),
        'hospital' => array('heating' => 0.9, 'cooling' => 1.0, 'electrical' => 0.9, 'plumbing' => 0.9, 'fire_protection' => 1.0),
        'school' => array('heating' => 0.8, 'cooling' => 0.8, 'electrical' => 0.6, 'plumbing' => 0.5, 'fire_protection' => 1.0),
        'residential' => array('heating' => 0.7, 'cooling' => 0.8, 'electrical' => 0.6, 'plumbing' => 0.7, 'fire_protection' => 1.0)
    );
    
    $factors = isset($base_factors[$occupancy_type]) ? $base_factors[$occupancy_type] : $base_factors['office'];
    
    return array(
        'heating' => $factors['heating'],
        'cooling' => $factors['cooling'],
        'electrical' => $factors['electrical'],
        'plumbing' => $factors['plumbing'],
        'fire_protection' => $factors['fire_protection']
    );
}

/**
 * Calculate diversity factors
 */
function calculateDiversityFactors($total_loads, $occupancy_type) {
    $base_factors = array(
        'office' => array('heating' => 0.8, 'cooling' => 0.9, 'electrical' => 0.7, 'plumbing' => 0.5, 'fire_protection' => 1.0),
        'retail' => array('heating' => 0.9, 'cooling' => 0.95, 'electrical' => 0.8, 'plumbing' => 0.7, 'fire_protection' => 1.0),
        'hospital' => array('heating' => 0.9, 'cooling' => 0.95, 'electrical' => 0.85, 'plumbing' => 0.8, 'fire_protection' => 1.0),
        'school' => array('heating' => 0.7, 'cooling' => 0.8, 'electrical' => 0.6, 'plumbing' => 0.4, 'fire_protection' => 1.0),
        'residential' => array('heating' => 0.6, 'cooling' => 0.7, 'electrical' => 0.5, 'plumbing' => 0.6, 'fire_protection' => 1.0)
    );
    
    $factors = isset($base_factors[$occupancy_type]) ? $base_factors[$occupancy_type] : $base_factors['office'];
    
    return array(
        'heating' => $factors['heating'],
        'cooling' => $factors['cooling'],
        'electrical' => $factors['electrical'],
        'plumbing' => $factors['plumbing'],
        'fire_protection' => $factors['fire_protection']
    );
}

/**
 * Calculate peak demands
 */
function calculatePeakDemands($total_loads, $demand_factors) {
    return array(
        'heating_peak' => $total_loads['heating']['peak_demand'] * $demand_factors['heating'],
        'cooling_peak' => $total_loads['cooling']['peak_demand'] * $demand_factors['cooling'],
        'electrical_peak' => $total_loads['electrical']['total_load'] * $demand_factors['electrical'],
        'plumbing_peak' => $total_loads['plumbing']['peak_flow_rate'] * $demand_factors['plumbing'],
        'fire_protection_peak' => $total_loads['fire_protection']['total_demand'] * $demand_factors['fire_protection']
    );
}

/**
 * Calculate efficiency ratings
 */
function calculateEfficiencyRatings($total_loads, $building_type) {
    $base_efficiencies = array(
        'commercial' => array('heating' => 0.85, 'cooling' => 3.2, 'electrical' => 0.92, 'plumbing' => 0.88),
        'industrial' => array('heating' => 0.8, 'cooling' => 3.0, 'electrical' => 0.88, 'plumbing' => 0.85),
        'residential' => array('heating' => 0.9, 'cooling' => 3.5, 'electrical' => 0.95, 'plumbing' => 0.9),
        'healthcare' => array('heating' => 0.88, 'cooling' => 3.4, 'electrical' => 0.94, 'plumbing' => 0.92),
        'education' => array('heating' => 0.87, 'cooling' => 3.3, 'electrical' => 0.93, 'plumbing' => 0.89)
    );
    
    $efficiencies = isset($base_efficiencies[$building_type]) ? $base_efficiencies[$building_type] : $base_efficiencies['commercial'];
    
    return array(
        'heating_efficiency' => $efficiencies['heating'],
        'cooling_efficiency' => $efficiencies['cooling'], // COP
        'electrical_efficiency' => $efficiencies['electrical'],
        'plumbing_efficiency' => $efficiencies['plumbing']
    );
}

/**
 * Calculate equipment sizing
 */
function calculateEquipmentSizing($total_loads, $building_type) {
    // Safety factors for equipment sizing
    $safety_factors = array(
        'heating' => 1.2,
        'cooling' => 1.15,
        'electrical' => 1.25,
        'plumbing' => 1.1,
        'fire_protection' => 1.0
    );
    
    return array(
        'boiler_capacity' => ($total_loads['heating']['total_load'] / 1000) * $safety_factors['heating'], // kW
        'chiller_capacity' => ($total_loads['cooling']['total_load'] / 1000) * $safety_factors['cooling'], // kW
        'transformer_capacity' => ($total_loads['electrical']['total_load'] / 1000) * $safety_factors['electrical'], // kVA
        'pump_capacity' => ($total_loads['plumbing']['peak_flow_rate'] / 1000) * $safety_factors['plumbing'], // m³/h
        'fire_pump_capacity' => ($total_loads['fire_protection']['total_demand'] / 1000) * $safety_factors['fire_protection'] // m³/h
    );
}

/**
 * Generate load summary report
 */
function generateLoadSummary($data) {
    $load_analysis = performLoadCalculation($data);
    
    return array(
        'report_header' => array(
            'title' => 'MEP Load Summary Report',
            'project_name' => $data['project_name'] ?? 'MEP Project',
            'report_date' => date('Y-m-d H:i:s'),
            'analyst' => 'MEP Coordination Suite'
        ),
        'executive_summary' => array(
            'total_building_area' => $load_analysis['building_info']['area'],
            'total_floors' => $load_analysis['building_info']['floors'],
            'occupancy_type' => $load_analysis['building_info']['occupancy_type'],
            'peak_heating_demand' => $load_analysis['peak_demands']['heating_peak'],
            'peak_cooling_demand' => $load_analysis['peak_demands']['cooling_peak'],
            'peak_electrical_demand' => $load_analysis['peak_demands']['electrical_peak'],
            'peak_plumbing_demand' => $load_analysis['peak_demands']['plumbing_peak'],
            'fire_protection_capacity' => $load_analysis['peak_demands']['fire_protection_peak']
        ),
        'detailed_analysis' => $load_analysis,
        'recommendations' => array(
            'Select high-efficiency equipment to minimize operating costs',
            'Consider renewable energy integration for sustainability',
            'Implement demand control ventilation for energy savings',
            'Use variable speed drives for pumps and fans',
            'Install building automation system for optimal control'
        )
    );
}

/**
 * Export load data
 */
function exportLoadData($data) {
    $load_analysis = performLoadCalculation($data);
    
    // Create export directory if it doesn't exist
    if (!is_dir('../../exports')) {
        mkdir('../../exports', 0755, true);
    }
    
    $filename = 'load_summary_' . date('Y-m-d_H-i-s') . '.json';
    $filepath = '../../exports/' . $filename;
    
    file_put_contents($filepath, json_encode($load_analysis, JSON_PRETTY_PRINT));
    
    return true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP Load Summary - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .load-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #FF6B35, #F7931E);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .analysis-grid {
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
            border-bottom: 2px solid #FF6B35;
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
            background: #FF6B35;
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
            background: #E55A2B;
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
        
        .load-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .overview-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #FF6B35;
        }
        
        .overview-value {
            font-size: 24px;
            font-weight: 600;
            color: #FF6B35;
        }
        
        .overview-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .load-breakdown {
            margin: 20px 0;
        }
        
        .load-category {
            background: #fff3e0;
            border: 1px solid #ffcc02;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #ff9800;
        }
        
        .load-category h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .load-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        
        .detail-item {
            text-align: center;
            padding: 8px;
            background: white;
            border-radius: 4px;
        }
        
        .detail-value {
            font-weight: 600;
            color: #FF6B35;
        }
        
        .efficiency-section {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .efficiency-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .efficiency-item {
            text-align: center;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        
        .efficiency-value {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .equipment-section {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        
        .equipment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .equipment-item {
            padding: 10px;
            background: white;
            border-radius: 6px;
            text-align: center;
        }
        
        .equipment-name {
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 5px;
        }
        
        .equipment-capacity {
            font-size: 16px;
            color: #FF6B35;
            font-weight: 600;
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
            .analysis-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .load-overview {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>
    <?php include '../../../includes/header.php'; ?>
    
    <div class="load-container">
        <div class="page-header">
            <h1>MEP Load Summary Analysis</h1>
            <p>Comprehensive load calculations and analysis for MEP systems</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="analysis-grid">
            <!-- Input Form -->
            <div class="card">
                <div class="card-header">Building Parameters</div>
                
                <form method="POST" id="load-form">
                    <input type="hidden" name="action" value="calculate_loads">
                    
                    <div class="form-group">
                        <label for="project_name">Project Name</label>
                        <input type="text" id="project_name" name="project_name" 
                               value="<?php echo htmlspecialchars($saved_analyses[0]['project_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="building_area">Building Area (m²)</label>
                            <input type="number" id="building_area" name="building_area" 
                                   value="<?php echo htmlspecialchars($saved_analyses[0]['building_area'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="floors">Number of Floors</label>
                            <input type="number" id="floors" name="floors" 
                                   value="<?php echo htmlspecialchars($saved_analyses[0]['floors'] ?? '1'); ?>" min="1" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="occupancy_type">Occupancy Type</label>
                            <select id="occupancy_type" name="occupancy_type" required>
                                <option value="office">Office</option>
                                <option value="retail">Retail</option>
                                <option value="hospital">Hospital</option>
                                <option value="school">School</option>
                                <option value="residential">Residential</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="building_type">Building Type</label>
                            <select id="building_type" name="building_type" required>
                                <option value="commercial">Commercial</option>
                                <option value="industrial">Industrial</option>
                                <option value="residential">Residential</option>
                                <option value="healthcare">Healthcare</option>
                                <option value="education">Education</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="climate_zone">Climate Zone</label>
                        <select id="climate_zone" name="climate_zone" required>
                            <option value="cold">Cold</option>
                            <option value="temperate" selected>Temperate</option>
                            <option value="warm">Warm</option>
                            <option value="hot">Hot</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Calculate Loads</button>
                    <button type="button" class="btn btn-secondary" onclick="exportLoads()">Export Data</button>
                </form>
            </div>
            
            <!-- Load Overview -->
            <div class="card">
                <div class="card-header">Load Analysis Overview</div>
                <div id="load-overview">
                    <p style="color: #666; text-align: center; padding: 50px 20px;">
                        Enter building parameters and click "Calculate Loads" to start the analysis.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Results Section -->
        <div id="results-section" class="results-section">
            <div class="card">
                <div class="card-header">Load Analysis Results</div>
                
                <div id="load-results"></div>
            </div>
        </div>
    </div>
    
    <?php include '../../../includes/footer.php'; ?>
    
    <script>
        function calculateLoads() {
            const formData = new FormData(document.getElementById('load-form'));
            formData.append('action', 'calculate_loads');
            
            fetch('load-summary.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayLoadResults(data.results);
                } else {
                    alert('Error calculating loads: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error calculating loads');
            });
        }
        
        function exportLoads() {
            const formData = new FormData(document.getElementById('load-form'));
            formData.append('action', 'export_loads');
            
            fetch('load-summary.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Load data exported successfully!');
                } else {
                    alert('Error exporting data: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error exporting data');
            });
        }
        
        function displayLoadResults(analysis) {
            document.getElementById('results-section').classList.add('active');
            
            const overviewHtml = `
                <div class="load-overview">
                    <div class="overview-item">
                        <div class="overview-value">${(analysis.load_results.heating.total_load / 1000).toFixed(1)}kW</div>
                        <div class="overview-label">Heating Load</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${(analysis.load_results.cooling.total_load / 1000).toFixed(1)}kW</div>
                        <div class="overview-label">Cooling Load</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${(analysis.load_results.electrical.total_load / 1000).toFixed(1)}kW</div>
                        <div class="overview-label">Electrical Load</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-value">${analysis.load_results.plumbing.total_water_demand.toFixed(0)}L/h</div>
                        <div class="overview-label">Water Demand</div>
                    </div>
                </div>
                
                <div class="load-breakdown">
                    <h3>Detailed Load Breakdown</h3>
                    
                    <div class="load-category">
                        <h4>Heating System</h4>
                        <div class="load-details">
                            <div class="detail-item">
                                <div class="detail-value">${(analysis.load_results.heating.sensible_load / 1000).toFixed(1)}kW</div>
                                <div class="overview-label">Sensible Load</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${(analysis.load_results.heating.latent_load / 1000).toFixed(1)}kW</div>
                                <div class="overview-label">Latent Load</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${analysis.load_results.heating.load_per_m2.toFixed(1)}W/m²</div>
                                <div class="overview-label">Load/m²</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${(analysis.peak_demands.heating_peak / 1000).toFixed(1)}kW</div>
                                <div class="overview-label">Peak Demand</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="load-category">
                        <h4>Cooling System</h4>
                        <div class="load-details">
                            <div class="detail-item">
                                <div class="detail-value">${(analysis.load_results.cooling.sensible_load / 1000).toFixed(1)}kW</div>
                                <div class="overview-label">Sensible Load</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${(analysis.load_results.cooling.latent_load / 1000).toFixed(1)}kW</div>
                                <div class="overview-label">Latent Load</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${analysis.load_results.cooling.load_per_m2.toFixed(1)}W/m²</div>
                                <div class="overview-label">Load/m²</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${(analysis.peak_demands.cooling_peak / 1000).toFixed(1)}kW</div>
                                <div class="overview-label">Peak Demand</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="load-category">
                        <h4>Electrical System</h4>
                        <div class="load-details">
                            <div class="detail-item">
                                <div class="detail-value">${(analysis.load_results.electrical.lighting_load / 1000).toFixed(1)}kW</div>
                                <div class="overview-label">Lighting</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${(analysis.load_results.electrical.power_load / 1000).toFixed(1)}kW</div>
                                <div class="overview-label">Power</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${(analysis.load_results.electrical.hvac_load / 1000).toFixed(1)}kW</div>
                                <div class="overview-label">HVAC</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${(analysis.peak_demands.electrical_peak / 1000).toFixed(1)}kW</div>
                                <div class="overview-label">Peak Demand</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="load-category">
                        <h4>Plumbing System</h4>
                        <div class="load-details">
                            <div class="detail-item">
                                <div class="detail-value">${analysis.load_results.plumbing.cold_water_demand.toFixed(0)}L/h</div>
                                <div class="overview-label">Cold Water</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${analysis.load_results.plumbing.hot_water_demand.toFixed(0)}L/h</div>
                                <div class="overview-label">Hot Water</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${analysis.load_results.plumbing.demand_per_m2.toFixed(2)}L/h/m²</div>
                                <div class="overview-label">Demand/m²</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-value">${analysis.peak_demands.plumbing_peak.toFixed(0)}L/h</div>
                                <div class="overview-label">Peak Flow</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="efficiency-section">
                    <h3>System Efficiency Ratings</h3>
                    <div class="efficiency-grid">
                        <div class="efficiency-item">
                            <div class="efficiency-value">${(analysis.efficiency_ratings.heating_efficiency * 100).toFixed(1)}%</div>
                            <div class="overview-label">Heating Efficiency</div>
                        </div>
                        <div class="efficiency-item">
                            <div class="efficiency-value">${analysis.efficiency_ratings.cooling_efficiency.toFixed(1)}</div>
                            <div class="overview-label">Cooling COP</div>
                        </div>
                        <div class="efficiency-item">
                            <div class="efficiency-value">${(analysis.efficiency_ratings.electrical_efficiency * 100).toFixed(1)}%</div>
                            <div class="overview-label">Electrical Efficiency</div>
                        </div>
                        <div class="efficiency-item">
                            <div class="efficiency-value">${(analysis.efficiency_ratings.plumbing_efficiency * 100).toFixed(1)}%</div>
                            <div class="overview-label">Plumbing Efficiency</div>
                        </div>
                    </div>
                </div>
                
                <div class="equipment-section">
                    <h3>Recommended Equipment Sizing</h3>
                    <div class="equipment-grid">
                        <div class="equipment-item">
                            <div class="equipment-name">Boiler Capacity</div>
                            <div class="equipment-capacity">${analysis.equipment_sizing.boiler_capacity.toFixed(1)} kW</div>
                        </div>
                        <div class="equipment-item">
                            <div class="equipment-name">Chiller Capacity</div>
                            <div class="equipment-capacity">${analysis.equipment_sizing.chiller_capacity.toFixed(1)} kW</div>
                        </div>
                        <div class="equipment-item">
                            <div class="equipment-name">Transformer</div>
                            <div class="equipment-capacity">${analysis.equipment_sizing.transformer_capacity.toFixed(1)} kVA</div>
                        </div>
                        <div class="equipment-item">
                            <div class="equipment-name">Water Pump</div>
                            <div class="equipment-capacity">${analysis.equipment_sizing.pump_capacity.toFixed(1)} m³/h</div>
                        </div>
                        <div class="equipment-item">
                            <div class="equipment-name">Fire Pump</div>
                            <div class="equipment-capacity">${analysis.equipment_sizing.fire_pump_capacity.toFixed(1)} m³/h</div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('load-results').innerHTML = overviewHtml;
        }
        
        // Form submission handler
        document.getElementById('load-form').addEventListener('submit', function(e) {
            e.preventDefault();
            calculateLoads();
        });
    </script>
</body>
</html>
