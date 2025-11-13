<?php
// Fire Safety Zoning Calculator
// Fire Compartment Design, Egress Analysis and Life Safety Compliance

require_once '../../../app/Config/config.php';
require_once '../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Helpers/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$page_title = "Fire Safety Zoning Calculator";
include '../../../themes/default/views/partials/header.php';

// Handle form submission
$results = null;
if ($_POST) {
    $results = calculateFireZoning($_POST);
}

function calculateFireZoning($data) {
    $results = [];
    
    // Input validation and sanitization
    $building_type = sanitizeInput($data['building_type'] ?? '');
    $area = floatval($data['area'] ?? 0);
    $occupancy = sanitizeInput($data['occupancy'] ?? '');
    $stories = intval($data['stories'] ?? 1);
    $sprinklered = sanitizeInput($data['sprinklered'] ?? '');
    $occupant_load = intval($data['occupant_load'] ?? 0);
    $height = floatval($data['height'] ?? 0);
    $construction_type = sanitizeInput($data['construction_type'] ?? '');
    $use_group = sanitizeInput($data['use_group'] ?? '');
    $special_hazard = sanitizeInput($data['special_hazard'] ?? '');
    $exits_available = intval($data['exits_available'] ?? 0);
    $travel_distance = floatval($data['travel_distance'] ?? 0);
    
    // Fire compartment calculations
    $max_compartment_area = calculateMaxCompartmentArea($building_type, $construction_type, $sprinklered);
    $compartment_count = ceil($area / $max_compartment_area);
    $required_separation = calculateSeparationRequirements($occupancy, $special_hazard);
    
    // Egress calculations
    $min_exits = calculateMinExits($occupant_load, $stories);
    $exit_capacity = calculateExitCapacity($exits_available);
    $travel_distance_limit = calculateTravelDistanceLimit($occupancy, $sprinklered);
    $exit_accessibility = checkExitAccessibility($travel_distance, $travel_distance_limit);
    
    // Life safety analysis
    $fire_resistance = calculateFireResistance($construction_type, $height);
    $smoke_control = calculateSmokeControl($stories, $height, $occupancy);
    $emergency_lighting = calculateEmergencyLighting($area, $occupant_load);
    
    // Code compliance
    $compliance_issues = [];
    $compliance_pass = [];
    
    // Check compartment area compliance
    if ($area > $max_compartment_area) {
        $compliance_issues[] = "Compartment area exceeds maximum allowed ({$max_compartment_area} sq ft)";
    } else {
        $compliance_pass[] = "Compartment area within limits";
    }
    
    // Check egress compliance
    if ($exits_available < $min_exits) {
        $compliance_issues[] = "Insufficient exits: {$min_exits} required, {$exits_available} provided";
    } else {
        $compliance_pass[] = "Minimum exit requirements met";
    }
    
    // Check travel distance compliance
    if ($travel_distance > $travel_distance_limit) {
        $compliance_issues[] = "Travel distance exceeds limit ({$travel_distance_limit} ft)";
    } else {
        $compliance_pass[] = "Travel distance within limits";
    }
    
    // Check exit capacity compliance
    if ($exit_capacity < $occupant_load) {
        $compliance_issues[] = "Exit capacity insufficient for occupant load";
    } else {
        $compliance_pass[] = "Exit capacity adequate";
    }
    
    // Fire safety recommendations
    $safety_recommendations = getSafetyRecommendations($building_type, $special_hazard, $compliance_issues);
    
    // Cost estimation for fire safety measures
    $compartment_cost = $compartment_count * 15000; // $15K per compartment
    $exit_enhancement_cost = ($min_exits > $exits_available) ? ($min_exits - $exits_available) * 25000 : 0;
    $sprinkler_upgrade_cost = ($sprinklered === 'no') ? $area * 2.5 : 0; // $2.50 per sq ft
    $total_safety_cost = $compartment_cost + $exit_enhancement_cost + $sprinkler_upgrade_cost;
    
    $results = [
        'input_data' => [
            'building_type' => $building_type,
            'area' => $area,
            'occupancy' => $occupancy,
            'stories' => $stories,
            'sprinklered' => $sprinklered,
            'occupant_load' => $occupant_load,
            'height' => $height,
            'construction_type' => $construction_type,
            'use_group' => $use_group,
            'special_hazard' => $special_hazard,
            'exits_available' => $exits_available,
            'travel_distance' => $travel_distance
        ],
        'compartment_analysis' => [
            'max_area' => $max_compartment_area,
            'compartment_count' => $compartment_count,
            'separation_hours' => $required_separation,
            'cost' => $compartment_cost
        ],
        'egress_analysis' => [
            'min_exits' => $min_exits,
            'exit_capacity' => $exit_capacity,
            'travel_distance_limit' => $travel_distance_limit,
            'travel_distance_ok' => $exit_accessibility,
            'exit_enhancement_cost' => $exit_enhancement_cost
        ],
        'life_safety' => [
            'fire_resistance' => $fire_resistance,
            'smoke_control' => $smoke_control,
            'emergency_lighting' => $emergency_lighting
        ],
        'compliance' => [
            'issues' => $compliance_issues,
            'passed' => $compliance_pass,
            'overall_status' => empty($compliance_issues) ? 'PASS' : 'NEEDS_IMPROVEMENT'
        ],
        'cost_estimation' => [
            'compartment_cost' => $compartment_cost,
            'exit_enhancement_cost' => $exit_enhancement_cost,
            'sprinkler_upgrade_cost' => $sprinkler_upgrade_cost,
            'total_cost' => $total_safety_cost
        ],
        'safety' => $safety_recommendations
    ];
    
    return $results;
}

function calculateMaxCompartmentArea($building_type, $construction_type, $sprinklered) {
    // Base areas in square feet
    $base_areas = [
        'assembly' => 12000,
        'business' => 15000,
        'educational' => 20000,
        'factory' => 15000,
        'hazardous' => 8000,
        'institutional' => 12000,
        'mercantile' => 15000,
        'residential' => 25000,
        'storage' => 12000,
        'utility' => 20000
    ];
    
    $base_area = $base_areas[$building_type] ?? 12000;
    
    // Adjust for construction type
    $construction_multipliers = [
        'type_ia' => 1.5,
        'type_ib' => 1.3,
        'type_ii' => 1.0,
        'type_iii' => 0.8,
        'type_iv' => 1.2,
        'type_v' => 0.6
    ];
    
    $multiplier = $construction_multipliers[$construction_type] ?? 1.0;
    $adjusted_area = $base_area * $multiplier;
    
    // Sprinkler multiplier
    $sprinkler_multiplier = ($sprinklered === 'yes') ? 1.5 : 1.0;
    
    return round($adjusted_area * $sprinkler_multiplier);
}

function calculateSeparationRequirements($occupancy, $special_hazard) {
    // Fire resistance hours required
    $base_hours = [
        'assembly' => 1,
        'business' => 1,
        'educational' => 1,
        'factory' => 2,
        'hazardous' => 3,
        'institutional' => 2,
        'mercantile' => 1,
        'residential' => 1,
        'storage' => 2,
        'utility' => 1
    ];
    
    $hours = $base_hours[$occupancy] ?? 1;
    
    // Increase for special hazards
    if ($special_hazard === 'high') {
        $hours += 1;
    } elseif ($special_hazard === 'moderate') {
        $hours += 0.5;
    }
    
    return $hours;
}

function calculateMinExits($occupant_load, $stories) {
    if ($occupant_load <= 500) {
        return 1;
    } elseif ($occupant_load <= 1000) {
        return 2;
    } else {
        return 3;
    }
}

function calculateExitCapacity($num_exits) {
    // Each exit provides 1000 person capacity
    return $num_exits * 1000;
}

function calculateTravelDistanceLimit($occupancy, $sprinklered) {
    $base_distances = [
        'assembly' => 200,
        'business' => 300,
        'educational' => 200,
        'factory' => 400,
        'hazardous' => 150,
        'institutional' => 200,
        'mercantile' => 300,
        'residential' => 400,
        'storage' => 400,
        'utility' => 400
    ];
    
    $base_distance = $base_distances[$occupancy] ?? 300;
    
    // Sprinklered buildings can have increased distances
    if ($sprinklered === 'yes') {
        $base_distance *= 1.5;
    }
    
    return round($base_distance);
}

function calculateFireResistance($construction_type, $height) {
    $fire_ratings = [
        'type_ia' => 3,
        'type_ib' => 2,
        'type_ii' => 1,
        'type_iii' => 1,
        'type_iv' => 2,
        'type_v' => 0.5
    ];
    
    $base_rating = $fire_ratings[$construction_type] ?? 1;
    
    // Increase for high-rise buildings
    if ($height > 75) {
        $base_rating += 1;
    }
    
    return $base_rating;
}

function calculateSmokeControl($stories, $height, $occupancy) {
    $smoke_control_required = false;
    
    // Required for high-rise buildings
    if ($height > 75) {
        $smoke_control_required = true;
    }
    
    // Required for certain occupancies
    $high_risk_occupancies = ['hazardous', 'institutional', 'assembly'];
    if (in_array($occupancy, $high_risk_occupancies) && $stories > 2) {
        $smoke_control_required = true;
    }
    
    return $smoke_control_required ? 'Required' : 'Not Required';
}

function calculateEmergencyLighting($area, $occupant_load) {
    // Required for areas with 50+ occupants or 10,000+ sq ft
    return ($occupant_load >= 50 || $area >= 10000) ? 'Required' : 'Not Required';
}

function checkExitAccessibility($actual_distance, $limit) {
    return $actual_distance <= $limit;
}

function getSafetyRecommendations($building_type, $special_hazard, $compliance_issues) {
    $recommendations = [];
    
    // General recommendations based on building type
    if ($building_type === 'hazardous') {
        $recommendations[] = "Install explosion-proof electrical systems";
        $recommendations[] = "Provide specialized ventilation systems";
        $recommendations[] = "Install gas detection systems";
    }
    
    if ($building_type === 'assembly') {
        $recommendations[] = "Provide public address system for emergency notifications";
        $recommendations[] = "Install crowd management systems";
        $recommendations[] = "Ensure wheelchair accessibility compliance";
    }
    
    if ($special_hazard === 'high') {
        $recommendations[] = "Consider smoke evacuation systems";
        $recommendations[] = "Install deluge sprinkler systems";
        $recommendations[] = "Provide emergency shutdown systems";
    }
    
    // Recommendations based on compliance issues
    if (!empty($compliance_issues)) {
        $recommendations[] = "Review and update fire safety plan";
        $recommendations[] = "Conduct fire drill and evacuation training";
        $recommendations[] = "Install additional fire detection systems";
    }
    
    // Standard recommendations
    $recommendations[] = "Conduct regular fire safety inspections";
    $recommendations[] = "Maintain fire protection systems per NFPA standards";
    $recommendations[] = "Provide fire safety training for all occupants";
    $recommendations[] = "Establish clear evacuation routes and assembly points";
    
    return $recommendations;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-shield-alt"></i> Fire Safety Zoning Calculator</h4>
                    <p class="mb-0">Fire Compartment Design, Egress Analysis and Life Safety Compliance</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Building Information</h6>
                                <div class="form-group">
                                    <label for="building_type">Building Type</label>
                                    <select class="form-control" id="building_type" name="building_type" required>
                                        <option value="">Select Building Type</option>
                                        <option value="assembly" <?= ($_POST['building_type'] ?? '') === 'assembly' ? 'selected' : '' ?>>Assembly</option>
                                        <option value="business" <?= ($_POST['building_type'] ?? '') === 'business' ? 'selected' : '' ?>>Business</option>
                                        <option value="educational" <?= ($_POST['building_type'] ?? '') === 'educational' ? 'selected' : '' ?>>Educational</option>
                                        <option value="factory" <?= ($_POST['building_type'] ?? '') === 'factory' ? 'selected' : '' ?>>Factory</option>
                                        <option value="hazardous" <?= ($_POST['building_type'] ?? '') === 'hazardous' ? 'selected' : '' ?>>Hazardous</option>
                                        <option value="institutional" <?= ($_POST['building_type'] ?? '') === 'institutional' ? 'selected' : '' ?>>Institutional</option>
                                        <option value="mercantile" <?= ($_POST['building_type'] ?? '') === 'mercantile' ? 'selected' : '' ?>>Mercantile</option>
                                        <option value="residential" <?= ($_POST['building_type'] ?? '') === 'residential' ? 'selected' : '' ?>>Residential</option>
                                        <option value="storage" <?= ($_POST['building_type'] ?? '') === 'storage' ? 'selected' : '' ?>>Storage</option>
                                        <option value="utility" <?= ($_POST['building_type'] ?? '') === 'utility' ? 'selected' : '' ?>>Utility</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="area">Total Building Area (sq ft)</label>
                                    <input type="number" class="form-control" id="area" name="area" 
                                           value="<?= htmlspecialchars($_POST['area'] ?? '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="occupancy">Specific Occupancy</label>
                                    <input type="text" class="form-control" id="occupancy" name="occupancy" 
                                           value="<?= htmlspecialchars($_POST['occupancy'] ?? '') ?>" 
                                           placeholder="e.g., Office, Classroom, Warehouse">
                                </div>
                                
                                <div class="form-group">
                                    <label for="stories">Number of Stories</label>
                                    <input type="number" class="form-control" id="stories" name="stories" 
                                           value="<?= htmlspecialchars($_POST['stories'] ?? '1') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="height">Building Height (ft)</label>
                                    <input type="number" class="form-control" id="height" name="height" 
                                           value="<?= htmlspecialchars($_POST['height'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Construction & Protection</h6>
                                <div class="form-group">
                                    <label for="construction_type">Construction Type</label>
                                    <select class="form-control" id="construction_type" name="construction_type" required>
                                        <option value="">Select Construction Type</option>
                                        <option value="type_ia" <?= ($_POST['construction_type'] ?? '') === 'type_ia' ? 'selected' : '' ?>>Type IA (Fire Resistive)</option>
                                        <option value="type_ib" <?= ($_POST['construction_type'] ?? '') === 'type_ib' ? 'selected' : '' ?>>Type IB (Fire Resistive)</option>
                                        <option value="type_ii" <?= ($_POST['construction_type'] ?? '') === 'type_ii' ? 'selected' : '' ?>>Type II (Noncombustible)</option>
                                        <option value="type_iii" <?= ($_POST['construction_type'] ?? '') === 'type_iii' ? 'selected' : '' ?>>Type III (Ordinary)</option>
                                        <option value="type_iv" <?= ($_POST['construction_type'] ?? '') === 'type_iv' ? 'selected' : '' ?>>Type IV (Heavy Timber)</option>
                                        <option value="type_v" <?= ($_POST['construction_type'] ?? '') === 'type_v' ? 'selected' : '' ?>>Type V (Wood Frame)</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="sprinklered">Fully Sprinklered?</label>
                                    <select class="form-control" id="sprinklered" name="sprinklered" required>
                                        <option value="">Select Option</option>
                                        <option value="yes" <?= ($_POST['sprinklered'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        <option value="no" <?= ($_POST['sprinklered'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="occupant_load">Maximum Occupant Load</label>
                                    <input type="number" class="form-control" id="occupant_load" name="occupant_load" 
                                           value="<?= htmlspecialchars($_POST['occupant_load'] ?? '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="use_group">Use Group</label>
                                    <select class="form-control" id="use_group" name="use_group" required>
                                        <option value="">Select Use Group</option>
                                        <option value="a" <?= ($_POST['use_group'] ?? '') === 'a' ? 'selected' : '' ?>>A (Assembly)</option>
                                        <option value="b" <?= ($_POST['use_group'] ?? '') === 'b' ? 'selected' : '' ?>>B (Business)</option>
                                        <option value="e" <?= ($_POST['use_group'] ?? '') === 'e' ? 'selected' : '' ?>>E (Educational)</option>
                                        <option value="f" <?= ($_POST['use_group'] ?? '') === 'f' ? 'selected' : '' ?>>F (Factory/Industrial)</option>
                                        <option value="h" <?= ($_POST['use_group'] ?? '') === 'h' ? 'selected' : '' ?>>H (High Hazard)</option>
                                        <option value="i" <?= ($_POST['use_group'] ?? '') === 'i' ? 'selected' : '' ?>>I (Institutional)</option>
                                        <option value="m" <?= ($_POST['use_group'] ?? '') === 'm' ? 'selected' : '' ?>>M (Mercantile)</option>
                                        <option value="r" <?= ($_POST['use_group'] ?? '') === 'r' ? 'selected' : '' ?>>R (Residential)</option>
                                        <option value="s" <?= ($_POST['use_group'] ?? '') === 's' ? 'selected' : '' ?>>S (Storage)</option>
                                        <option value="u" <?= ($_POST['use_group'] ?? '') === 'u' ? 'selected' : '' ?>>U (Utility)</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="special_hazard">Special Hazard Level</label>
                                    <select class="form-control" id="special_hazard" name="special_hazard" required>
                                        <option value="">Select Hazard Level</option>
                                        <option value="none" <?= ($_POST['special_hazard'] ?? '') === 'none' ? 'selected' : '' ?>>None</option>
                                        <option value="low" <?= ($_POST['special_hazard'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                                        <option value="moderate" <?= ($_POST['special_hazard'] ?? '') === 'moderate' ? 'selected' : '' ?>>Moderate</option>
                                        <option value="high" <?= ($_POST['special_hazard'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Egress Information</h6>
                                <div class="form-group">
                                    <label for="exits_available">Number of Exits Available</label>
                                    <input type="number" class="form-control" id="exits_available" name="exits_available" 
                                           value="<?= htmlspecialchars($_POST['exits_available'] ?? '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="travel_distance">Maximum Travel Distance to Exit (ft)</label>
                                    <input type="number" class="form-control" id="travel_distance" name="travel_distance" 
                                           value="<?= htmlspecialchars($_POST['travel_distance'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-calculator"></i> Analyze Fire Safety
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($results): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-clipboard-check"></i> Fire Safety Zoning Analysis Results</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Fire Compartment Analysis</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Max Compartment Area:</td>
                                    <td><strong><?= number_format($results['compartment_analysis']['max_area']) ?> sq ft</strong></td>
                                </tr>
                                <tr>
                                    <td>Required Compartments:</td>
                                    <td><strong><?= number_format($results['compartment_analysis']['compartment_count']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Separation Rating:</td>
                                    <td><strong><?= number_format($results['compartment_analysis']['separation_hours'], 1) ?> hours</strong></td>
                                </tr>
                                <tr>
                                    <td>Compartmentation Cost:</td>
                                    <td>$<?= number_format($results['compartment_analysis']['cost'], 0) ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Egress Analysis</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Minimum Exits Required:</td>
                                    <td><strong><?= number_format($results['egress_analysis']['min_exits']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Exits Available:</td>
                                    <td><strong><?= number_format($results['input_data']['exits_available']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Exit Capacity:</td>
                                    <td><strong><?= number_format($results['egress_analysis']['exit_capacity']) ?> persons</strong></td>
                                </tr>
                                <tr>
                                    <td>Travel Distance Limit:</td>
                                    <td><strong><?= number_format($results['egress_analysis']['travel_distance_limit']) ?> ft</strong></td>
                                </tr>
                                <tr class="<?= $results['egress_analysis']['travel_distance_ok'] ? 'table-success' : 'table-warning' ?>">
                                    <td>Travel Distance Status:</td>
                                    <td><strong><?= $results['egress_analysis']['travel_distance_ok'] ? 'ACCEPTABLE' : 'EXCESSIVE' ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Life Safety Systems</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Fire Resistance Rating:</td>
                                    <td><strong><?= number_format($results['life_safety']['fire_resistance']) ?> hours</strong></td>
                                </tr>
                                <tr>
                                    <td>Smoke Control:</td>
                                    <td><strong><?= htmlspecialchars($results['life_safety']['smoke_control']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Emergency Lighting:</td>
                                    <td><strong><?= htmlspecialchars($results['life_safety']['emergency_lighting']) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Code Compliance Status</h6>
                            <div class="alert <?= $results['compliance']['overall_status'] === 'PASS' ? 'alert-success' : 'alert-warning' ?>">
                                <h6><i class="fas fa-<?= $results['compliance']['overall_status'] === 'PASS' ? 'check-circle' : 'exclamation-triangle' ?>"></i> 
                                    Overall Status: <?= htmlspecialchars($results['compliance']['overall_status']) ?></h6>
                                
                                <?php if (!empty($results['compliance']['passed'])): ?>
                                <h6>Compliant Areas:</h6>
                                <ul class="mb-2">
                                    <?php foreach ($results['compliance']['passed'] as $pass): ?>
                                    <li><i class="fas fa-check text-success"></i> <?= htmlspecialchars($pass) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                                
                                <?php if (!empty($results['compliance']['issues'])): ?>
                                <h6>Issues to Address:</h6>
                                <ul class="mb-0">
                                    <?php foreach ($results['compliance']['issues'] as $issue): ?>
                                    <li><i class="fas fa-exclamation text-warning"></i> <?= htmlspecialchars($issue) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Safety Improvements Cost</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Compartmentation:</td>
                                    <td>$<?= number_format($results['cost_estimation']['compartment_cost'], 0) ?></td>
                                </tr>
                                <tr>
                                    <td>Exit Enhancements:</td>
                                    <td>$<?= number_format($results['cost_estimation']['exit_enhancement_cost'], 0) ?></td>
                                </tr>
                                <tr>
                                    <td>Sprinkler Upgrades:</td>
                                    <td>$<?= number_format($results['cost_estimation']['sprinkler_upgrade_cost'], 0) ?></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total Safety Investment:</strong></td>
                                    <td><strong>$<?= number_format($results['cost_estimation']['total_cost'], 0) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Design Summary</h6>
                            <div class="alert alert-info">
                                <small>
                                    <strong>Building:</strong> <?= htmlspecialchars(ucwords($results['input_data']['building_type'])) ?><br>
                                    <strong>Area:</strong> <?= number_format($results['input_data']['area']) ?> sq ft<br>
                                    <strong>Compartments:</strong> <?= number_format($results['compartment_analysis']['compartment_count']) ?><br>
                                    <strong>Exits:</strong> <?= number_format($results['input_data']['exits_available']) ?> (<?= number_format($results['egress_analysis']['min_exits']) ?> min)<br>
                                    <strong>Status:</strong> <?= htmlspecialchars($results['compliance']['overall_status']) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($results['safety'])): ?>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Fire Safety Recommendations</h6>
                            <div class="alert alert-info">
                                <ul class="mb-0">
                                    <?php foreach ($results['safety'] as $recommendation): ?>
                                    <li><i class="fas fa-lightbulb"></i> <?= htmlspecialchars($recommendation) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table-primary {
    background-color: rgba(0, 123, 255, 0.1);
}

.table-success {
    background-color: rgba(40, 167, 69, 0.1);
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.text-success {
    color: #28a745 !important;
}

.text-warning {
    color: #ffc107 !important;
}

h6 {
    color: #007bff;
    font-weight: 600;
    margin-bottom: 1rem;
}

.form-group label {
    font-weight: 500;
    color: #495057;
}

.btn {
    font-weight: 500;
}

.card-header h4 {
    color: #007bff;
    margin-bottom: 0.5rem;
}

.card-header p {
    color: #6c757d;
    font-size: 0.9rem;
}

small {
    font-size: 0.85rem;
    line-height: 1.4;
}

.alert h6 {
    margin-bottom: 0.5rem;
    color: inherit;
}
</style>

<?php include '../../../themes/default/views/partials/footer.php'; ?>



