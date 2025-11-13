<?php
/**
 * Green Building Rating System
 * Comprehensive green building rating and sustainability certification system
 * LEED, BREEAM, Green Star, and other certification standards analysis
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
            case 'calculate_leed':
                $result = calculateLeedRating($_POST);
                $message = 'LEED rating calculated successfully!';
                $message_type = 'success';
                $leed_data = $result;
                break;
                
            case 'calculate_breeam':
                $result = calculateBreeamRating($_POST);
                $message = 'BREEAM rating calculated successfully!';
                $message_type = 'success';
                $breeam_data = $result;
                break;
                
            case 'calculate_green_star':
                $result = calculateGreenStarRating($_POST);
                $message = 'Green Star rating calculated successfully!';
                $message_type = 'success';
                $green_star_data = $result;
                break;
                
            case 'generate_action_plan':
                $result = generateSustainabilityActionPlan($_POST);
                $message = 'Sustainability action plan generated successfully!';
                $message_type = 'success';
                $action_plan = $result;
                break;
                
            case 'save_project':
                $result = saveGreenRatingProject($_POST, $project_id);
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
    $query = "SELECT * FROM mep_green_rating_projects WHERE id = ?";
    $saved_projects = $db->fetch($query, [$project_id]);
}

/**
 * Calculate LEED (Leadership in Energy and Environmental Design) rating
 */
function calculateLeedRating($data) {
    // Project basics
    $project_type = $data['project_type'] ?? 'new_construction';
    $building_area = floatval($data['building_area'] ?? 0);
    $occupancy_count = intval($data['occupancy_count'] ?? 0);
    
    // Credit categories
    $energy_performance = floatval($data['energy_performance'] ?? 0);
    $water_efficiency = floatval($data['water_efficiency'] ?? 0);
    $materials_resources = floatval($data['materials_resources'] ?? 0);
    $indoor_environmental_quality = floatval($data['indoor_environmental_quality'] ?? 0);
    $innovation = floatval($data['innovation'] ?? 0);
    $regional_priority = floatval($data['regional_priority'] ?? 0);
    
    // Sustainable sites
    $sustainable_sites = floatval($data['sustainable_sites'] ?? 0);
    
    // Calculate total points
    $total_points = $energy_performance + $water_efficiency + $materials_resources + 
                   $indoor_environmental_quality + $innovation + $regional_priority + $sustainable_sites;
    
    // LEED Rating levels
    $certification_levels = [
        'certified' => 40,
        'silver' => 50,
        'gold' => 60,
        'platinum' => 80
    ];
    
    $rating_level = 'not_certified';
    if ($total_points >= 80) {
        $rating_level = 'platinum';
    } elseif ($total_points >= 60) {
        $rating_level = 'gold';
    } elseif ($total_points >= 50) {
        $rating_level = 'silver';
    } elseif ($total_points >= 40) {
        $rating_level = 'certified';
    }
    
    // LEED points breakdown
    $points_breakdown = [
        'Energy & Atmosphere' => $energy_performance,
        'Water Efficiency' => $water_efficiency,
        'Materials & Resources' => $materials_resources,
        'Indoor Environmental Quality' => $indoor_environmental_quality,
        'Innovation' => $innovation,
        'Regional Priority' => $regional_priority,
        'Sustainable Sites' => $sustainable_sites
    ];
    
    // Recommendations for improvement
    $recommendations = generateLeedRecommendations($points_breakdown, $total_points);
    
    // Estimated certification costs
    $certification_cost = estimateLeedCertificationCost($rating_level, $building_area);
    
    return [
        'total_points' => $total_points,
        'rating_level' => $rating_level,
        'points_breakdown' => $points_breakdown,
        'certification_levels' => $certification_levels,
        'recommendations' => $recommendations,
        'certification_cost' => $certification_cost,
        'potential_savings' => calculateLeedEnergySavings($data)
    ];
}

/**
 * Calculate BREEAM (Building Research Establishment Environmental Assessment Method) rating
 */
function calculateBreeamRating($data) {
    // BREEAM categories
    $management = floatval($data['management'] ?? 0);
    $health_wellbeing = floatval($data['health_wellbeing'] ?? 0);
    $energy = floatval($data['energy'] ?? 0);
    $transport = floatval($data['transport'] ?? 0);
    $water = floatval($data['water'] ?? 0);
    $materials = floatval($data['materials'] ?? 0);
    $waste = floatval($data['waste'] ?? 0);
    $land_use = floatval($data['land_use'] ?? 0);
    $pollution = floatval($data['pollution'] ?? 0);
    
    // Calculate total percentage
    $total_percentage = $management + $health_wellbeing + $energy + $transport + 
                       $water + $materials + $waste + $land_use + $pollution;
    
    // BREEAM Rating levels
    $breeam_levels = [
        'pass' => 30,
        'good' => 45,
        'very_good' => 55,
        'excellent' => 70,
        'outstanding' => 85
    ];
    
    $rating_level = 'unclassified';
    if ($total_percentage >= 85) {
        $rating_level = 'outstanding';
    } elseif ($total_percentage >= 70) {
        $rating_level = 'excellent';
    } elseif ($total_percentage >= 55) {
        $rating_level = 'very_good';
    } elseif ($total_percentage >= 45) {
        $rating_level = 'good';
    } elseif ($total_percentage >= 30) {
        $rating_level = 'pass';
    }
    
    // BREEAM percentage breakdown
    $percentage_breakdown = [
        'Management' => $management,
        'Health & Wellbeing' => $health_wellbeing,
        'Energy' => $energy,
        'Transport' => $transport,
        'Water' => $water,
        'Materials' => $materials,
        'Waste' => $waste,
        'Land Use & Ecology' => $land_use,
        'Pollution' => $pollution
    ];
    
    // Recommendations for improvement
    $recommendations = generateBreeamRecommendations($percentage_breakdown, $total_percentage);
    
    return [
        'total_percentage' => $total_percentage,
        'rating_level' => $rating_level,
        'percentage_breakdown' => $percentage_breakdown,
        'breeam_levels' => $breeam_levels,
        'recommendations' => $recommendations
    ];
}

/**
 * Calculate Green Star rating
 */
function calculateGreenStarRating($data) {
    // Green Star categories
    $management = floatval($data['management'] ?? 0);
    $energy = floatval($data['energy'] ?? 0);
    $indoor_environment = floatval($data['indoor_environment'] ?? 0);
    $water = floatval($data['water'] ?? 0);
    $materials = floatval($data['materials'] ?? 0);
    $emissions = floatval($data['emissions'] ?? 0);
    $transport = floatval($data['transport'] ?? 0);
    $land_use = floatval($data['land_use'] ?? 0);
    
    // Calculate total points
    $total_points = $management + $energy + $indoor_environment + $water + 
                   $materials + $emissions + $transport + $land_use;
    
    // Green Star rating levels
    $green_star_levels = [
        'four_star' => 45,
        'five_star' => 60,
        'six_star' => 75
    ];
    
    $rating_level = 'unrated';
    if ($total_points >= 75) {
        $rating_level = 'six_star';
    } elseif ($total_points >= 60) {
        $rating_level = 'five_star';
    } elseif ($total_points >= 45) {
        $rating_level = 'four_star';
    }
    
    // Green Star points breakdown
    $points_breakdown = [
        'Management' => $management,
        'Energy' => $energy,
        'Indoor Environment' => $indoor_environment,
        'Water' => $water,
        'Materials' => $materials,
        'Emissions' => $emissions,
        'Transport' => $transport,
        'Land Use & Ecology' => $land_use
    ];
    
    // Recommendations for improvement
    $recommendations = generateGreenStarRecommendations($points_breakdown, $total_points);
    
    return [
        'total_points' => $total_points,
        'rating_level' => $rating_level,
        'points_breakdown' => $points_breakdown,
        'green_star_levels' => $green_star_levels,
        'recommendations' => $recommendations
    ];
}

/**
 * Generate LEED recommendations
 */
function generateLeedRecommendations($points_breakdown, $total_points) {
    $recommendations = [];
    
    // Energy & Atmosphere recommendations
    if ($points_breakdown['Energy & Atmosphere'] < 15) {
        $recommendations[] = 'Implement energy-efficient HVAC systems';
        $recommendations[] = 'Install LED lighting with daylight controls';
        $recommendations[] = 'Consider renewable energy systems';
    }
    
    // Water Efficiency recommendations
    if ($points_breakdown['Water Efficiency'] < 8) {
        $recommendations[] = 'Install low-flow fixtures and sensors';
        $recommendations[] = 'Implement rainwater harvesting system';
        $recommendations[] = 'Use drought-resistant landscaping';
    }
    
    // Materials & Resources recommendations
    if ($points_breakdown['Materials & Resources'] < 8) {
        $recommendations[] = 'Use recycled and locally-sourced materials';
        $recommendations[] = 'Implement construction waste management';
        $recommendations[] = 'Select products with low VOC emissions';
    }
    
    // Indoor Environmental Quality recommendations
    if ($points_breakdown['Indoor Environmental Quality'] < 10) {
        $recommendations[] = 'Improve indoor air quality with proper ventilation';
        $recommendations[] = 'Use low-emitting materials';
        $recommendations[] = 'Provide thermal comfort controls';
    }
    
    // Innovation recommendations
    if ($points_breakdown['Innovation'] < 5) {
        $recommendations[] = 'Implement innovative green building technologies';
        $recommendations[] = 'Use green building education programs';
        $recommendations[] = 'Exceed minimum performance standards';
    }
    
    // Regional Priority recommendations
    if ($points_breakdown['Regional Priority'] < 4) {
        $recommendations[] = 'Address regional environmental priorities';
        $recommendations[] = 'Support local environmental initiatives';
    }
    
    return $recommendations;
}

/**
 * Generate BREEAM recommendations
 */
function generateBreeamRecommendations($percentage_breakdown, $total_percentage) {
    $recommendations = [];
    
    // Energy recommendations
    if ($percentage_breakdown['Energy'] < 15) {
        $recommendations[] = 'Improve building energy efficiency';
        $recommendations[] = 'Install renewable energy systems';
        $recommendations[] = 'Implement energy monitoring systems';
    }
    
    // Water recommendations
    if ($percentage_breakdown['Water'] < 5) {
        $recommendations[] = 'Install water-efficient fixtures';
        $recommendations[] = 'Implement water recycling systems';
        $recommendations[] = 'Use rainwater harvesting';
    }
    
    // Materials recommendations
    if ($percentage_breakdown['Materials'] < 10) {
        $recommendations[] = 'Use sustainable building materials';
        $recommendations[] = 'Implement responsible sourcing policies';
        $recommendations[] = 'Consider life cycle assessments';
    }
    
    // Waste recommendations
    if ($percentage_breakdown['Waste'] < 5) {
        $recommendations[] = 'Implement waste reduction strategies';
        $recommendations[] = 'Establish recycling and composting programs';
        $recommendations[] = 'Use construction waste management';
    }
    
    return $recommendations;
}

/**
 * Generate Green Star recommendations
 */
function generateGreenStarRecommendations($points_breakdown, $total_points) {
    $recommendations = [];
    
    // Energy recommendations
    if ($points_breakdown['Energy'] < 15) {
        $recommendations[] = 'Install high-efficiency HVAC systems';
        $recommendations[] = 'Implement solar photovoltaic systems';
        $recommendations[] = 'Use energy-efficient lighting';
    }
    
    // Indoor Environment recommendations
    if ($points_breakdown['Indoor Environment'] < 10) {
        $recommendations[] = 'Improve natural ventilation systems';
        $recommendations[] = 'Use non-toxic building materials';
        $recommendations[] = 'Provide thermal and acoustic comfort';
    }
    
    // Materials recommendations
    if ($points_breakdown['Materials'] < 8) {
        $recommendations[] = 'Select certified sustainable materials';
        $recommendations[] = 'Reduce embodied carbon in materials';
        $recommendations[] = 'Implement materials recycling';
    }
    
    return $recommendations;
}

/**
 * Generate sustainability action plan
 */
function generateSustainabilityActionPlan($data) {
    $action_plan = [
        'short_term' => [],
        'medium_term' => [],
        'long_term' => []
    ];
    
    // Short-term actions (0-6 months)
    $action_plan['short_term'] = [
        'Conduct energy audit and identify quick wins',
        'Install occupancy sensors and smart lighting controls',
        'Implement water conservation measures',
        'Establish green procurement policies',
        'Train staff on sustainable practices'
    ];
    
    // Medium-term actions (6-18 months)
    $action_plan['medium_term'] = [
        'Upgrade to energy-efficient HVAC systems',
        'Install rainwater harvesting and greywater systems',
        'Implement comprehensive waste management program',
        'Transition to renewable energy sources',
        'Install green roofs or walls for improved insulation'
    ];
    
    // Long-term actions (18+ months)
    $action_plan['long_term'] = [
        'Pursue green building certification (LEED/BREEAM/Green Star)',
        'Implement building automation and IoT systems',
        'Establish carbon neutrality goals',
        'Create green transportation programs',
        'Develop sustainable urban design features'
    ];
    
    // Priority actions based on current scores
    $priority_actions = [];
    
    if (isset($data['energy_score']) && $data['energy_score'] < 50) {
        $priority_actions[] = 'Immediate energy efficiency upgrades required';
    }
    
    if (isset($data['water_score']) && $data['water_score'] < 40) {
        $priority_actions[] = 'Water conservation measures are urgent';
    }
    
    if (isset($data['waste_score']) && $data['waste_score'] < 30) {
        $priority_actions[] = 'Waste reduction strategy needed';
    }
    
    return [
        'action_plan' => $action_plan,
        'priority_actions' => $priority_actions,
        'estimated_costs' => [
            'short_term' => 25000,
            'medium_term' => 150000,
            'long_term' => 500000
        ],
        'potential_savings' => [
            'annual_energy' => 75000,
            'annual_water' => 15000,
            'annual_maintenance' => 20000
        ]
    ];
}

/**
 * Estimate LEED certification costs
 */
function estimateLeedCertificationCost($rating_level, $building_area) {
    $base_cost_per_sqm = 2.50;
    $certification_fees = [
        'certified' => 5000,
        'silver' => 7500,
        'gold' => 10000,
        'platinum' => 15000
    ];
    
    $area_cost = $building_area * $base_cost_per_sqm;
    $certification_fee = $certification_fees[$rating_level] ?? 0;
    
    return [
        'area_cost' => $area_cost,
        'certification_fee' => $certification_fee,
        'total_cost' => $area_cost + $certification_fee,
        'consulting_fees' => $area_cost * 0.3
    ];
}

/**
 * Calculate LEED energy savings
 */
function calculateLeedEnergySavings($data) {
    $building_area = floatval($data['building_area'] ?? 0);
    $electricity_rate = floatval($data['electricity_rate'] ?? 0.12);
    
    // Estimated savings percentages by LEED level
    $savings_by_level = [
        'certified' => 0.15,
        'silver' => 0.25,
        'gold' => 0.35,
        'platinum' => 0.50
    ];
    
    // Calculate baseline energy consumption
    $baseline_consumption = $building_area * 200; // kWh/m²/year
    
    // Estimate LEED level from energy performance
    $energy_performance = floatval($data['energy_performance'] ?? 0);
    $estimated_level = 'certified';
    if ($energy_performance >= 15) {
        $estimated_level = 'gold';
    } elseif ($energy_performance >= 12) {
        $estimated_level = 'silver';
    } elseif ($energy_performance >= 8) {
        $estimated_level = 'certified';
    }
    
    $savings_percentage = $savings_by_level[$estimated_level];
    $energy_savings = $baseline_consumption * $savings_percentage;
    $cost_savings = $energy_savings * $electricity_rate;
    
    return [
        'baseline_consumption' => $baseline_consumption,
        'energy_savings' => $energy_savings,
        'cost_savings' => $cost_savings,
        'estimated_level' => $estimated_level,
        'payback_period' => estimateLeedCertificationCost($estimated_level, $building_area)['total_cost'] / $cost_savings
    ];
}

/**
 * Save green rating project data
 */
function saveGreenRatingProject($data, $project_id) {
    global $db;
    
    $project_data = [
        'project_type' => $data['project_type'],
        'building_area' => floatval($data['building_area']),
        'occupancy_count' => intval($data['occupancy_count']),
        'energy_performance' => floatval($data['energy_performance']),
        'water_efficiency' => floatval($data['water_efficiency']),
        'materials_resources' => floatval($data['materials_resources']),
        'indoor_environmental_quality' => floatval($data['indoor_environmental_quality']),
        'innovation' => floatval($data['innovation']),
        'regional_priority' => floatval($data['regional_priority']),
        'sustainable_sites' => floatval($data['sustainable_sites']),
        'electricity_rate' => floatval($data['electricity_rate']),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if ($project_id > 0) {
        $query = "UPDATE mep_green_rating_projects SET " . 
                 implode(', ', array_map(fn($k) => "$k = ?", array_keys($project_data))) . 
                 " WHERE id = ?";
        return $db->execute($query, array_merge(array_values($project_data), [$project_id]));
    } else {
        $query = "INSERT INTO mep_green_rating_projects (" . 
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
    <title>Green Building Rating System - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .green-rating-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #2E7D32, #388E3C);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .rating-tabs {
            display: flex;
            background: #f5f5f5;
            border-radius: 10px 10px 0 0;
            overflow: hidden;
            margin-bottom: 0;
        }
        
        .rating-tab {
            flex: 1;
            padding: 15px;
            background: #e0e0e0;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .rating-tab.active {
            background: #4CAF50;
            color: white;
        }
        
        .rating-tab:hover {
            background: #66BB6A;
            color: white;
        }
        
        .tab-content {
            display: none;
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .form-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #4CAF50;
        }
        
        .form-section h3 {
            margin: 0 0 15px 0;
            color: #2E7D32;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group input[type="range"] {
            padding: 0;
        }
        
        .range-value {
            float: right;
            font-weight: 600;
            color: #4CAF50;
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
        
        .rating-result {
            background: #e8f5e8;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .rating-level {
            font-size: 28px;
            font-weight: 600;
            color: #2E7D32;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .rating-score {
            font-size: 24px;
            font-weight: 600;
            color: #4CAF50;
            text-align: center;
            margin: 10px 0;
        }
        
        .score-breakdown {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .score-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e0e0e0;
        }
        
        .score-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .score-value {
            font-size: 18px;
            font-weight: 600;
            color: #4CAF50;
        }
        
        .recommendations-list {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .recommendations-list h4 {
            color: #856404;
            margin-bottom: 15px;
        }
        
        .recommendations-list ul {
            list-style-type: disc;
            padding-left: 20px;
        }
        
        .recommendations-list li {
            margin: 8px 0;
            color: #856404;
        }
        
        .action-plan {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .action-phase {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            border-left: 4px solid #4CAF50;
        }
        
        .action-phase h4 {
            color: #2E7D32;
            margin: 0 0 15px 0;
        }
        
        .cost-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .cost-item {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .cost-amount {
            font-size: 20px;
            font-weight: 600;
            color: #1976D2;
        }
        
        .cost-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
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
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .rating-tabs {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include '../../../themes/default/views/partials/header.php'; ?>
    
    <div class="green-rating-container">
        <div class="page-header">
            <h1>Green Building Rating System</h1>
            <p>LEED, BREEAM, and Green Star certification analysis and planning</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Rating System Tabs -->
        <div class="rating-tabs">
            <button class="rating-tab active" onclick="showTab('leed')">LEED Rating</button>
            <button class="rating-tab" onclick="showTab('breeam')">BREEAM Rating</button>
            <button class="rating-tab" onclick="showTab('green-star')">Green Star</button>
            <button class="rating-tab" onclick="showTab('action-plan')">Action Plan</button>
        </div>
        
        <!-- LEED Rating Tab -->
        <div id="leed" class="tab-content active">
            <h2>LEED (Leadership in Energy and Environmental Design) Rating</h2>
            
            <form method="POST" id="leed-form">
                <input type="hidden" name="action" value="calculate_leed">
                
                <div class="form-grid">
                    <div class="form-section">
                        <h3>Project Information</h3>
                        
                        <div class="form-group">
                            <label for="project_type">Project Type</label>
                            <select id="project_type" name="project_type" required>
                                <option value="new_construction">New Construction</option>
                                <option value="major_renovation">Major Renovation</option>
                                <option value="existing_building">Existing Building</option>
                                <option value="core_shell">Core & Shell</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="building_area">Building Area (m²)</label>
                            <input type="number" id="building_area" name="building_area" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['building_area'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="occupancy_count">Expected Occupancy</label>
                            <input type="number" id="occupancy_count" name="occupancy_count" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['occupancy_count'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="electricity_rate">Electricity Rate ($/kWh)</label>
                            <input type="number" id="electricity_rate" name="electricity_rate" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['electricity_rate'] ?? '0.12'); ?>" 
                                   step="0.01" min="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Credit Categories</h3>
                        
                        <div class="form-group">
                            <label for="energy_performance">
                                Energy & Atmosphere (Max: 33)
                                <span class="range-value" id="energy_performance_value">0</span>
                            </label>
                            <input type="range" id="energy_performance" name="energy_performance" 
                                   min="0" max="33" value="0" 
                                   oninput="document.getElementById('energy_performance_value').textContent = this.value">
                        </div>
                        
                        <div class="form-group">
                            <label for="water_efficiency">
                                Water Efficiency (Max: 11)
                                <span class="range-value" id="water_efficiency_value">0</span>
                            </label>
                            <input type="range" id="water_efficiency" name="water_efficiency" 
                                   min="0" max="11" value="0"
                                   oninput="document.getElementById('water_efficiency_value').textContent = this.value">
                        </div>
                        
                        <div class="form-group">
                            <label for="materials_resources">
                                Materials & Resources (Max: 13)
                                <span class="range-value" id="materials_resources_value">0</span>
                            </label>
                            <input type="range" id="materials_resources" name="materials_resources" 
                                   min="0" max="13" value="0"
                                   oninput="document.getElementById('materials_resources_value').textContent = this.value">
                        </div>
                        
                        <div class="form-group">
                            <label for="indoor_environmental_quality">
                                Indoor Environmental Quality (Max: 16)
                                <span class="range-value" id="indoor_environmental_quality_value">0</span>
                            </label>
                            <input type="range" id="indoor_environmental_quality" name="indoor_environmental_quality" 
                                   min="0" max="16" value="0"
                                   oninput="document.getElementById('indoor_environmental_quality_value').textContent = this.value">
                        </div>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-section">
                        <h3>Additional Credits</h3>
                        
                        <div class="form-group">
                            <label for="innovation">
                                Innovation (Max: 6)
                                <span class="range-value" id="innovation_value">0</span>
                            </label>
                            <input type="range" id="innovation" name="innovation" 
                                   min="0" max="6" value="0"
                                   oninput="document.getElementById('innovation_value').textContent = this.value">
                        </div>
                        
                        <div class="form-group">
                            <label for="regional_priority">
                                Regional Priority (Max: 6)
                                <span class="range-value" id="regional_priority_value">0</span>
                            </label>
                            <input type="range" id="regional_priority" name="regional_priority" 
                                   min="0" max="6" value="0"
                                   oninput="document.getElementById('regional_priority_value').textContent = this.value">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Sustainable Sites</h3>
                        
                        <div class="form-group">
                            <label for="sustainable_sites">
                                Sustainable Sites (Max: 10)
                                <span class="range-value" id="sustainable_sites_value">0</span>
                            </label>
                            <input type="range" id="sustainable_sites" name="sustainable_sites" 
                                   min="0" max="10" value="0"
                                   oninput="document.getElementById('sustainable_sites_value').textContent = this.value">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn">Calculate LEED Rating</button>
            </form>
            
            <?php if (isset($leed_data)): ?>
                <div class="rating-result">
                    <div class="rating-level"><?php echo ucfirst(str_replace('_', ' ', $leed_data['rating_level'])); ?></div>
                    <div class="rating-score"><?php echo $leed_data['total_points']; ?> Points</div>
                    
                    <div class="score-breakdown">
                        <?php foreach ($leed_data['points_breakdown'] as $category => $points): ?>
                            <div class="score-item">
                                <div class="score-label"><?php echo $category; ?></div>
                                <div class="score-value"><?php echo $points; ?> pts</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (!empty($leed_data['potential_savings'])): ?>
                        <div class="cost-summary">
                            <div class="cost-item">
                                <div class="cost-amount">$<?php echo number_format($leed_data['potential_savings']['cost_savings']); ?></div>
                                <div class="cost-label">Annual Energy Savings</div>
                            </div>
                            <div class="cost-item">
                                <div class="cost-amount"><?php echo $leed_data['potential_savings']['payback_period']; ?> yrs</div>
                                <div class="cost-label">Payback Period</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($leed_data['recommendations'])): ?>
                    <div class="recommendations-list">
                        <h4>Improvement Recommendations</h4>
                        <ul>
                            <?php foreach ($leed_data['recommendations'] as $recommendation): ?>
                                <li><?php echo htmlspecialchars($recommendation); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- BREEAM Rating Tab -->
        <div id="breeam" class="tab-content">
            <h2>BREEAM (Building Research Establishment Environmental Assessment Method)</h2>
            
            <form method="POST" id="breeam-form">
                <input type="hidden" name="action" value="calculate_breeam">
                
                <div class="form-grid">
                    <div class="form-section">
                        <h3>Management & Health</h3>
                        
                        <div class="form-group">
                            <label for="management">
                                Management (Max: 25)
                                <span class="range-value" id="management_value">0</span>
                            </label>
                            <input type="range" id="management" name="management" 
                                   min="0" max="25" value="0"
                                   oninput="document.getElementById('management_value').textContent = this.value">
                        </div>
                        
                        <div class="form-group">
                            <label for="health_wellbeing">
                                Health & Wellbeing (Max: 20)
                                <span class="range-value" id="health_wellbeing_value">0</span>
                            </label>
                            <input type="range" id="health_wellbeing" name="health_wellbeing" 
                                   min="0" max="20" value="0"
                                   oninput="document.getElementById('health_wellbeing_value').textContent = this.value">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Energy & Transport</h3>
                        
                        <div class="form-group">
                            <label for="energy">
                                Energy (Max: 25)
                                <span class="range-value" id="energy_value">0</span>
                            </label>
                            <input type="range" id="energy" name="energy" 
                                   min="0" max="25" value="0"
                                   oninput="document.getElementById('energy_value').textContent = this.value">
                        </div>
                        
                        <div class="form-group">
                            <label for="transport">
                                Transport (Max: 10)
                                <span class="range-value" id="transport_value">0</span>
                            </label>
                            <input type="range" id="transport" name="transport" 
                                   min="0" max="10" value="0"
                                   oninput="document.getElementById('transport_value').textContent = this.value">
                        </div>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-section">
                        <h3>Resources</h3>
                        
                        <div class="form-group">
                            <label for="water">
                                Water (Max: 10)
                                <span class="range-value" id="water_value">0</span>
                            </label>
                            <input type="range" id="water" name="water" 
                                   min="0" max="10" value="0"
                                   oninput="document.getElementById('water_value').textContent = this.value">
                        </div>
                        
                        <div class="form-group">
                            <label for="materials">
                                Materials (Max: 20)
                                <span class="range-value" id="materials_value">0</span>
                            </label>
                            <input type="range" id="materials" name="materials" 
                                   min="0" max="20" value="0"
                                   oninput="document.getElementById('materials_value').textContent = this.value">
                        </div>
                        
                        <div class="form-group">
                            <label for="waste">
                                Waste (Max: 10)
                                <span class="range-value" id="waste_value">0</span>
                            </label>
                            <input type="range" id="waste" name="waste" 
                                   min="0" max="10" value="0"
                                   oninput="document.getElementById('waste_value').textContent = this.value">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Site & Environment</h3>
                        
                        <div class="form-group">
                            <label for="land_use">
                                Land Use & Ecology (Max: 10)
                                <span class="range-value" id="land_use_value">0</span>
                            </label>
                            <input type="range" id="land_use" name="land_use" 
                                   min="0" max="10" value="0"
                                   oninput="document.getElementById('land_use_value').textContent = this.value">
                        </div>
                        
                        <div class="form-group">
                            <label for="pollution">
                                Pollution (Max: 10)
                                <span class="range-value" id="pollution_value">0</span>
                            </label>
                            <input type="range" id="pollution" name="pollution" 
                                   min="0" max="10" value="0"
                                   oninput="document.getElementById('pollution_value').textContent = this.value">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn">Calculate BREEAM Rating</button>
            </form>
            
            <?php if (isset($breeam_data)): ?>
                <div class="rating-result">
                    <div class="rating-level"><?php echo ucfirst(str_replace('_', ' ', $breeam_data['rating_level'])); ?></div>
                    <div class="rating-score"><?php echo $breeam_data['total_percentage']; ?>%</div>
                    
                    <div class="score-breakdown">
                        <?php foreach ($breeam_data['percentage_breakdown'] as $category => $percentage): ?>
                            <div class="score-item">
                                <div class="score-label"><?php echo $category; ?></div>
                                <div class="score-value"><?php echo $percentage; ?>%</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if (!empty($breeam_data['recommendations'])): ?>
                    <div class="recommendations-list">
                        <h4>Improvement Recommendations</h4>
                        <ul>
                            <?php foreach ($breeam_data['recommendations'] as $recommendation): ?>
                                <li><?php echo htmlspecialchars($recommendation); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Action Plan Tab -->
        <div id="action-plan" class="tab-content">
            <h2>Sustainability Action Plan</h2>
            
            <form method="POST" id="action-plan-form">
                <input type="hidden" name="action" value="generate_action_plan">
                
                <div class="form-grid">
                    <div class="form-section">
                        <h3>Current Performance Scores</h3>
                        
                        <div class="form-group">
                            <label for="energy_score">Energy Performance Score (%)</label>
                            <input type="number" id="energy_score" name="energy_score" min="0" max="100" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="water_score">Water Efficiency Score (%)</label>
                            <input type="number" id="water_score" name="water_score" min="0" max="100" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="waste_score">Waste Management Score (%)</label>
                            <input type="number" id="waste_score" name="waste_score" min="0" max="100" value="0">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Action Plan Preferences</h3>
                        
                        <div class="form-group">
                            <label for="budget_range">Budget Range</label>
                            <select id="budget_range" name="budget_range">
                                <option value="low">Low ($0 - $50,000)</option>
                                <option value="medium">Medium ($50,000 - $200,000)</option>
                                <option value="high">High ($200,000+)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="timeline">Preferred Timeline</label>
                            <select id="timeline" name="timeline">
                                <option value="aggressive">Aggressive (6-12 months)</option>
                                <option value="moderate">Moderate (1-2 years)</option>
                                <option value="gradual">Gradual (2-5 years)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn">Generate Action Plan</button>
            </form>
            
            <?php if (isset($action_plan)): ?>
                <div class="action-plan">
                    <h3>Comprehensive Sustainability Action Plan</h3>
                    
                    <?php foreach ($action_plan['action_plan'] as $phase => $actions): ?>
                        <div class="action-phase">
                            <h4><?php echo ucfirst(str_replace('_', ' ', $phase)); ?> Term Actions</h4>
                            <ul>
                                <?php foreach ($actions as $action): ?>
                                    <li><?php echo htmlspecialchars($action); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (!empty($action_plan['priority_actions'])): ?>
                        <div class="action-phase">
                            <h4>Priority Actions</h4>
                            <ul>
                                <?php foreach ($action_plan['priority_actions'] as $priority): ?>
                                    <li><?php echo htmlspecialchars($priority); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="cost-summary">
                        <div class="cost-item">
                            <div class="cost-amount">$<?php echo number_format($action_plan['estimated_costs']['short_term']); ?></div>
                            <div class="cost-label">Short-term Investment</div>
                        </div>
                        <div class="cost-item">
                            <div class="cost-amount">$<?php echo number_format($action_plan['estimated_costs']['medium_term']); ?></div>
                            <div class="cost-label">Medium-term Investment</div>
                        </div>
                        <div class="cost-item">
                            <div class="cost-amount">$<?php echo number_format($action_plan['estimated_costs']['long_term']); ?></div>
                            <div class="cost-label">Long-term Investment</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../../../themes/default/views/partials/footer.php'; ?>
    
    <script>
        function showTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Remove active class from all tab buttons
            const tabButtons = document.querySelectorAll('.rating-tab');
            tabButtons.forEach(button => button.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }
    </script>
</body>
</html>



