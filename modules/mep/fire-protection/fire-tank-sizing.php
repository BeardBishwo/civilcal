<?php
// Fire Water Storage Tank Sizing Calculator
// NFPA 22 Compliant Fire Water Tank Design and Sizing

require_once '../../../app/Config/config.php';
require_once '../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Helpers/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$page_title = "Fire Water Tank Sizing Calculator";
include '../../../themes/default/views/partials/header.php';

// Handle form submission
$results = null;
if ($_POST) {
    $results = calculateFireTank($_POST);
}

function calculateFireTank($data) {
    $results = [];
    
    // Input validation and sanitization
    $building_type = sanitizeInput($data['building_type'] ?? '');
    $area = floatval($data['area'] ?? 0);
    $occupancy = sanitizeInput($data['occupancy'] ?? '');
    $hazard_class = sanitizeInput($data['hazard_class'] ?? '');
    $sprinkler_density = floatval($data['sprinkler_density'] ?? 0);
    $standpipe_demand = floatval($data['standpipe_demand'] ?? 0);
    $hose_stream = floatval($data['hose_stream'] ?? 0);
    $duration = floatval($data['duration'] ?? 60); // minutes
    $tank_type = sanitizeInput($data['tank_type'] ?? '');
    $elevation = floatval($data['elevation'] ?? 0);
    $pressure_req = floatval($data['pressure_req'] ?? 0);
    $soil_type = sanitizeInput($data['soil_type'] ?? '');
    $climate_zone = sanitizeInput($data['climate_zone'] ?? '');
    
    // Basic water supply requirements
    $base_flow = getBaseFlowRate($building_type, $area);
    $sprinkler_flow = $sprinkler_density * $area; // gpm
    $total_flow = max($base_flow, $sprinkler_flow + $standpipe_demand + $hose_stream);
    
    // Total water volume requirement
    $water_volume = ($total_flow * $duration) / 7.48; // Convert gallons to cubic feet
    
    // Tank capacity calculation with factors
    $storage_factor = getStorageFactor($hazard_class, $building_type);
    $tank_capacity = $water_volume * $storage_factor;
    
    // Tank dimension calculations
    $tank_dimensions = calculateTankDimensions($tank_capacity, $tank_type);
    
    // Pressure calculations
    $elevation_head = $elevation * 0.433; // Convert feet to psi
    $total_pressure_req = $pressure_req + $elevation_head;
    
    // Foundation requirements
    $foundation_type = selectFoundationType($tank_type, $soil_type, $tank_capacity);
    $foundation_cost = calculateFoundationCost($foundation_type, $tank_capacity, $elevation);
    
    // Tank material selection
    $material_type = selectTankMaterial($tank_type, $climate_zone, $tank_capacity);
    $material_cost = calculateMaterialCost($material_type, $tank_capacity);
    
    // Cost estimation
    $tank_cost = $material_cost;
    $installation_cost = $tank_cost * 0.4; // 40% of material cost
    $piping_cost = $tank_capacity * 25; // $25 per cubic foot capacity
    $total_cost = $tank_cost + $installation_cost + $piping_cost + $foundation_cost;
    
    // Performance analysis
    $filling_time = calculateFillingTime($tank_capacity, $total_flow);
    $operating_pressure = calculateOperatingPressure($elevation, $pressure_req);
    
    // Safety and compliance
    $compliance_notes = getTankComplianceNotes($tank_type, $tank_capacity, $hazard_class);
    $safety_recommendations = getSafetyRecommendations($tank_type, $elevation, $total_pressure_req);
    
    $results = [
        'input_data' => [
            'building_type' => $building_type,
            'area' => $area,
            'occupancy' => $occupancy,
            'hazard_class' => $hazard_class,
            'sprinkler_density' => $sprinkler_density,
            'standpipe_demand' => $standpipe_demand,
            'hose_stream' => $hose_stream,
            'duration' => $duration,
            'tank_type' => $tank_type,
            'elevation' => $elevation,
            'pressure_req' => $pressure_req,
            'soil_type' => $soil_type,
            'climate_zone' => $climate_zone
        ],
        'calculations' => [
            'base_flow' => $base_flow,
            'sprinkler_flow' => $sprinkler_flow,
            'total_flow' => $total_flow,
            'water_volume' => $water_volume,
            'storage_factor' => $storage_factor,
            'tank_capacity' => $tank_capacity,
            'total_pressure_req' => $total_pressure_req
        ],
        'tank_design' => [
            'dimensions' => $tank_dimensions,
            'material_type' => $material_type,
            'foundation_type' => $foundation_type
        ],
        'performance' => [
            'filling_time' => $filling_time,
            'operating_pressure' => $operating_pressure,
            'elevation_head' => $elevation_head
        ],
        'cost_estimation' => [
            'material_cost' => $material_cost,
            'installation_cost' => $installation_cost,
            'piping_cost' => $piping_cost,
            'foundation_cost' => $foundation_cost,
            'total_cost' => $total_cost
        ],
        'compliance' => $compliance_notes,
        'safety' => $safety_recommendations
    ];
    
    return $results;
}

function getBaseFlowRate($building_type, $area) {
    $flow_rates = [
        'assembly' => 1500,
        'business' => 1000,
        'educational' => 1500,
        'factory' => 2000,
        'hazardous' => 2500,
        'institutional' => 1000,
        'mercantile' => 1500,
        'residential' => 500,
        'storage' => 2000,
        'utility' => 1000
    ];
    
    $base_rate = $flow_rates[$building_type] ?? 1000;
    
    // Adjust based on area
    if ($area > 100000) {
        $base_rate *= 1.5;
    } elseif ($area > 50000) {
        $base_rate *= 1.25;
    }
    
    return $base_rate;
}

function getStorageFactor($hazard_class, $building_type) {
    // Factors account for emergency supply and redundancy
    $factors = [
        'light_hazard' => 1.0,
        'ordinary_hazard' => 1.2,
        'extra_hazard' => 1.5
    ];
    
    $base_factor = $factors[$hazard_class] ?? 1.0;
    
    // Additional factor for high-risk buildings
    if (in_array($building_type, ['factory', 'hazardous', 'storage'])) {
        $base_factor *= 1.1;
    }
    
    return $base_factor;
}

function calculateTankDimensions($capacity, $tank_type) {
    // Capacity is in cubic feet, convert to gallons for dimension calculations
    $capacity_gallons = $capacity * 7.48;
    
    if ($tank_type === 'ground_cylindrical') {
        // Standard cylindrical tank proportions
        $height_to_diameter_ratio = 1.0; // 1:1 ratio for stability
        $diameter = pow(($capacity_gallons * 4) / (pi() * $height_to_diameter_ratio), 1/3);
        $height = $diameter * $height_to_diameter_ratio;
        
        return [
            'shape' => 'Cylindrical',
            'diameter' => round($diameter, 1),
            'height' => round($height, 1),
            'surface_area' => round(pi() * $diameter * $height, 1),
            'volume' => round($capacity, 0)
        ];
    } elseif ($tank_type === 'ground_rectangular') {
        // Rectangular tank with economical proportions
        $length_width_ratio = 2.0;
        $height = 12; // Typical 12 ft height for ground tanks
        
        $width = sqrt(($capacity * 7.48) / ($length_width_ratio * $height));
        $length = $width * $length_width_ratio;
        
        return [
            'shape' => 'Rectangular',
            'length' => round($length, 1),
            'width' => round($width, 1),
            'height' => round($height, 1),
            'surface_area' => round((2 * $length * $height) + (2 * $width * $height) + ($length * $width), 1),
            'volume' => round($capacity, 0)
        ];
    } else {
        // Elevated tank
        $diameter = sqrt(($capacity * 4 * 7.48) / (pi() * 20)); // 20 ft height assumed
        $height = 20;
        
        return [
            'shape' => 'Elevated Cylindrical',
            'diameter' => round($diameter, 1),
            'height' => round($height, 1),
            'support_height' => 30, // Typical elevated support
            'surface_area' => round(pi() * $diameter * $height, 1),
            'volume' => round($capacity, 0)
        ];
    }
}

function selectFoundationType($tank_type, $soil_type, $capacity) {
    $foundation_types = [
        'concrete_slab' => 'Concrete Slab Foundation',
        'pile_foundation' => 'Pile Foundation',
        'grade_beam' => 'Grade Beam Foundation'
    ];
    
    if ($tank_type === 'ground_cylindrical' || $tank_type === 'ground_rectangular') {
        if ($soil_type === 'rock' || $soil_type === 'dense_sand') {
            return 'concrete_slab';
        } elseif ($capacity > 50000) { // Large capacity tanks
            return 'pile_foundation';
        } else {
            return 'grade_beam';
        }
    } else {
        return 'pile_foundation'; // Elevated tanks always need pile foundations
    }
}

function selectTankMaterial($tank_type, $climate_zone, $capacity) {
    $materials = [
        'steel' => 'Bolted Steel Tank',
        'welded_steel' => 'Welded Steel Tank',
        'concrete' => 'Prestressed Concrete Tank',
        'fiberglass' => 'Fiberglass Reinforced Plastic Tank'
    ];
    
    if ($tank_type === 'elevated') {
        return 'welded_steel';
    } elseif ($capacity > 100000) { // Large capacity
        return 'concrete';
    } elseif ($climate_zone === 'cold') {
        return 'welded_steel'; // Better for freeze protection
    } else {
        return 'steel'; // Most economical option
    }
}

function calculateFoundationCost($foundation_type, $tank_capacity, $elevation) {
    $base_costs = [
        'concrete_slab' => 15, // $15 per cubic ft
        'pile_foundation' => 25, // $25 per cubic ft
        'grade_beam' => 20 // $20 per cubic ft
    ];
    
    $base_cost_per_cf = $base_costs[$foundation_type] ?? 15;
    
    // Elevated tanks require additional foundation work
    $elevation_multiplier = $elevation > 0 ? 1.5 : 1.0;
    
    return $tank_capacity * $base_cost_per_cf * $elevation_multiplier;
}

function calculateMaterialCost($material_type, $tank_capacity) {
    $costs_per_cf = [
        'steel' => 45,
        'welded_steel' => 65,
        'concrete' => 35,
        'fiberglass' => 85
    ];
    
    return $tank_capacity * ($costs_per_cf[$material_type] ?? 45);
}

function calculateFillingTime($tank_capacity, $flow_rate) {
    $filling_flow = $flow_rate * 0.7; // Assume 70% of peak flow for filling
    $filling_time_hours = ($tank_capacity * 7.48) / $filling_flow / 60;
    
    return round($filling_time_hours, 1);
}

function calculateOperatingPressure($elevation, $pressure_req) {
    $elevation_head = $elevation * 0.433;
    return $elevation_head + $pressure_req;
}

function getTankComplianceNotes($tank_type, $capacity, $hazard_class) {
    $notes = [];
    
    $notes[] = "NFPA 22: Water storage tanks for fire protection systems";
    
    if ($capacity >= 50000) {
        $notes[] = "NFPA 22: Multiple tanks may be required for systems over 50,000 gallons";
    }
    
    if ($tank_type === 'elevated') {
        $notes[] = "NFPA 22: Elevated tanks must be designed for wind and seismic loads";
    }
    
    if ($hazard_class === 'extra_hazard') {
        $notes[] = "NFPA 13: Extra hazard occupancies require extended water supply duration";
    }
    
    $notes[] = "NFPA 25: Annual tank inspection and testing required";
    $notes[] = "Local fire marshal approval required for tank location and access";
    
    return $notes;
}

function getSafetyRecommendations($tank_type, $elevation, $total_pressure) {
    $recommendations = [];
    
    if ($elevation > 50) {
        $recommendations[] = "WARNING: High elevation may require pressure reducing valves";
    }
    
    if ($total_pressure > 150) {
        $recommendations[] = "WARNING: High operating pressure - verify system components rating";
    }
    
    if ($tank_type === 'elevated') {
        $recommendations[] = "Ensure adequate access road for maintenance vehicles";
        $recommendations[] = "Install lightning protection system";
        $recommendations[] = "Provide catwalk and safety railings for maintenance";
    }
    
    $recommendations[] = "Install level monitoring and alarm systems";
    $recommendations[] = "Provide adequate drainage around tank foundation";
    $recommendations[] = "Install security fencing and access control";
    $recommendations[] = "Ensure tamper-proof fill line connections";
    
    return $recommendations;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-tint"></i> Fire Water Tank Sizing Calculator</h4>
                    <p class="mb-0">NFPA 22 Compliant Fire Water Storage Tank Design</p>
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
                                    <label for="area">Building Area (sq ft)</label>
                                    <input type="number" class="form-control" id="area" name="area" 
                                           value="<?= htmlspecialchars($_POST['area'] ?? '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="occupancy">Occupancy Classification</label>
                                    <input type="text" class="form-control" id="occupancy" name="occupancy" 
                                           value="<?= htmlspecialchars($_POST['occupancy'] ?? '') ?>" 
                                           placeholder="e.g., Business, Assembly, Storage">
                                </div>
                                
                                <div class="form-group">
                                    <label for="hazard_class">Hazard Classification</label>
                                    <select class="form-control" id="hazard_class" name="hazard_class" required>
                                        <option value="">Select Hazard Class</option>
                                        <option value="light_hazard" <?= ($_POST['hazard_class'] ?? '') === 'light_hazard' ? 'selected' : '' ?>>Light Hazard</option>
                                        <option value="ordinary_hazard" <?= ($_POST['hazard_class'] ?? '') === 'ordinary_hazard' ? 'selected' : '' ?>>Ordinary Hazard</option>
                                        <option value="extra_hazard" <?= ($_POST['hazard_class'] ?? '') === 'extra_hazard' ? 'selected' : '' ?>>Extra Hazard</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Fire Protection Demands</h6>
                                <div class="form-group">
                                    <label for="sprinkler_density">Sprinkler Density (gpm/sq ft)</label>
                                    <input type="number" step="0.01" class="form-control" id="sprinkler_density" name="sprinkler_density" 
                                           value="<?= htmlspecialchars($_POST['sprinkler_density'] ?? '') ?>" 
                                           placeholder="0.10 - 0.60">
                                </div>
                                
                                <div class="form-group">
                                    <label for="standpipe_demand">Standpipe Demand (gpm)</label>
                                    <input type="number" class="form-control" id="standpipe_demand" name="standpipe_demand" 
                                           value="<?= htmlspecialchars($_POST['standpipe_demand'] ?? '') ?>" 
                                           placeholder="500 - 1000">
                                </div>
                                
                                <div class="form-group">
                                    <label for="hose_stream">Hose Stream Demand (gpm)</label>
                                    <input type="number" class="form-control" id="hose_stream" name="hose_stream" 
                                           value="<?= htmlspecialchars($_POST['hose_stream'] ?? '') ?>" 
                                           placeholder="250 - 500">
                                </div>
                                
                                <div class="form-group">
                                    <label for="duration">Water Supply Duration (minutes)</label>
                                    <input type="number" class="form-control" id="duration" name="duration" 
                                           value="<?= htmlspecialchars($_POST['duration'] ?? '60') ?>" 
                                           placeholder="30 - 120">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Tank Design Parameters</h6>
                                <div class="form-group">
                                    <label for="tank_type">Tank Type</label>
                                    <select class="form-control" id="tank_type" name="tank_type" required>
                                        <option value="">Select Tank Type</option>
                                        <option value="ground_cylindrical" <?= ($_POST['tank_type'] ?? '') === 'ground_cylindrical' ? 'selected' : '' ?>>Ground Cylindrical</option>
                                        <option value="ground_rectangular" <?= ($_POST['tank_type'] ?? '') === 'ground_rectangular' ? 'selected' : '' ?>>Ground Rectangular</option>
                                        <option value="elevated" <?= ($_POST['tank_type'] ?? '') === 'elevated' ? 'selected' : '' ?>>Elevated</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="elevation">Tank Elevation (ft)</label>
                                    <input type="number" step="0.1" class="form-control" id="elevation" name="elevation" 
                                           value="<?= htmlspecialchars($_POST['elevation'] ?? '') ?>" 
                                           placeholder="0 - 100">
                                </div>
                                
                                <div class="form-group">
                                    <label for="pressure_req">Required Pressure (psi)</label>
                                    <input type="number" step="0.1" class="form-control" id="pressure_req" name="pressure_req" 
                                           value="<?= htmlspecialchars($_POST['pressure_req'] ?? '') ?>" 
                                           placeholder="50 - 150">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Site Conditions</h6>
                                <div class="form-group">
                                    <label for="soil_type">Soil Type</label>
                                    <select class="form-control" id="soil_type" name="soil_type" required>
                                        <option value="">Select Soil Type</option>
                                        <option value="rock" <?= ($_POST['soil_type'] ?? '') === 'rock' ? 'selected' : '' ?>>Rock</option>
                                        <option value="dense_sand" <?= ($_POST['soil_type'] ?? '') === 'dense_sand' ? 'selected' : '' ?>>Dense Sand</option>
                                        <option value="medium_sand" <?= ($_POST['soil_type'] ?? '') === 'medium_sand' ? 'selected' : '' ?>>Medium Sand</option>
                                        <option value="loose_sand" <?= ($_POST['soil_type'] ?? '') === 'loose_sand' ? 'selected' : '' ?>>Loose Sand</option>
                                        <option value="clay" <?= ($_POST['soil_type'] ?? '') === 'clay' ? 'selected' : '' ?>>Clay</option>
                                        <option value="soft_clay" <?= ($_POST['soil_type'] ?? '') === 'soft_clay' ? 'selected' : '' ?>>Soft Clay</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="climate_zone">Climate Zone</label>
                                    <select class="form-control" id="climate_zone" name="climate_zone" required>
                                        <option value="">Select Climate Zone</option>
                                        <option value="temperate" <?= ($_POST['climate_zone'] ?? '') === 'temperate' ? 'selected' : '' ?>>Temperate</option>
                                        <option value="cold" <?= ($_POST['climate_zone'] ?? '') === 'cold' ? 'selected' : '' ?>>Cold Climate</option>
                                        <option value="hot" <?= ($_POST['climate_zone'] ?? '') === 'hot' ? 'selected' : '' ?>>Hot Climate</option>
                                        <option value="coastal" <?= ($_POST['climate_zone'] ?? '') === 'coastal' ? 'selected' : '' ?>>Coastal</option>
                                        <option value="desert" <?= ($_POST['climate_zone'] ?? '') === 'desert' ? 'selected' : '' ?>>Desert</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-calculator"></i> Calculate Fire Tank
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
                    <h4><i class="fas fa-chart-bar"></i> Fire Water Tank Sizing Results</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Water Supply Requirements</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Base Flow Rate:</td>
                                    <td><strong><?= number_format($results['calculations']['base_flow']) ?> gpm</strong></td>
                                </tr>
                                <tr>
                                    <td>Sprinkler Flow:</td>
                                    <td><?= number_format($results['calculations']['sprinkler_flow']) ?> gpm</td>
                                </tr>
                                <tr>
                                    <td>Total Required Flow:</td>
                                    <td><strong><?= number_format($results['calculations']['total_flow']) ?> gpm</strong></td>
                                </tr>
                                <tr>
                                    <td>Duration:</td>
                                    <td><?= number_format($results['input_data']['duration']) ?> minutes</td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total Tank Capacity:</strong></td>
                                    <td><strong><?= number_format($results['calculations']['tank_capacity']) ?> cu ft (<?= number_format($results['calculations']['tank_capacity'] * 7.48, 0) ?> gallons)</strong></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Tank Design Specifications</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Tank Type:</td>
                                    <td><strong><?= htmlspecialchars($results['tank_design']['dimensions']['shape']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Material:</td>
                                    <td><strong><?= htmlspecialchars($results['tank_design']['material_type']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Foundation:</td>
                                    <td><strong><?= htmlspecialchars($results['tank_design']['foundation_type']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Surface Area:</td>
                                    <td><?= number_format($results['tank_design']['dimensions']['surface_area']) ?> sq ft</td>
                                </tr>
                            </table>
                            
                            <?php if ($results['tank_design']['dimensions']['shape'] === 'Cylindrical'): ?>
                            <table class="table table-sm">
                                <tr>
                                    <td>Diameter:</td>
                                    <td><?= number_format($results['tank_design']['dimensions']['diameter']) ?> ft</td>
                                </tr>
                                <tr>
                                    <td>Height:</td>
                                    <td><?= number_format($results['tank_design']['dimensions']['height']) ?> ft</td>
                                </tr>
                            </table>
                            <?php elseif ($results['tank_design']['dimensions']['shape'] === 'Rectangular'): ?>
                            <table class="table table-sm">
                                <tr>
                                    <td>Length:</td>
                                    <td><?= number_format($results['tank_design']['dimensions']['length']) ?> ft</td>
                                </tr>
                                <tr>
                                    <td>Width:</td>
                                    <td><?= number_format($results['tank_design']['dimensions']['width']) ?> ft</td>
                                </tr>
                                <tr>
                                    <td>Height:</td>
                                    <td><?= number_format($results['tank_design']['dimensions']['height']) ?> ft</td>
                                </tr>
                            </table>
                            <?php elseif ($results['tank_design']['dimensions']['shape'] === 'Elevated Cylindrical'): ?>
                            <table class="table table-sm">
                                <tr>
                                    <td>Diameter:</td>
                                    <td><?= number_format($results['tank_design']['dimensions']['diameter']) ?> ft</td>
                                </tr>
                                <tr>
                                    <td>Tank Height:</td>
                                    <td><?= number_format($results['tank_design']['dimensions']['height']) ?> ft</td>
                                </tr>
                                <tr>
                                    <td>Support Height:</td>
                                    <td><?= number_format($results['tank_design']['dimensions']['support_height']) ?> ft</td>
                                </tr>
                            </table>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Performance Analysis</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Filling Time:</td>
                                    <td><?= number_format($results['performance']['filling_time']) ?> hours</td>
                                </tr>
                                <tr>
                                    <td>Elevation Head:</td>
                                    <td><?= number_format($results['performance']['elevation_head'], 1) ?> psi</td>
                                </tr>
                                <tr>
                                    <td>Total Operating Pressure:</td>
                                    <td><?= number_format($results['performance']['operating_pressure'], 1) ?> psi</td>
                                </tr>
                                <tr>
                                    <td>Storage Factor:</td>
                                    <td><?= number_format($results['calculations']['storage_factor'], 1) ?>x</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Cost Estimation</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Material Cost:</td>
                                    <td>$<?= number_format($results['cost_estimation']['material_cost'], 0) ?></td>
                                </tr>
                                <tr>
                                    <td>Installation Cost:</td>
                                    <td>$<?= number_format($results['cost_estimation']['installation_cost'], 0) ?></td>
                                </tr>
                                <tr>
                                    <td>Piping Cost:</td>
                                    <td>$<?= number_format($results['cost_estimation']['piping_cost'], 0) ?></td>
                                </tr>
                                <tr>
                                    <td>Foundation Cost:</td>
                                    <td>$<?= number_format($results['cost_estimation']['foundation_cost'], 0) ?></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total Project Cost:</strong></td>
                                    <td><strong>$<?= number_format($results['cost_estimation']['total_cost'], 0) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Code Compliance</h6>
                            <ul class="list-unstyled">
                                <?php foreach ($results['compliance'] as $note): ?>
                                <li><i class="fas fa-check text-success"></i> <?= htmlspecialchars($note) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Design Summary</h6>
                            <div class="alert alert-info">
                                <small>
                                    <strong>Capacity:</strong> <?= number_format($results['calculations']['tank_capacity'] * 7.48, 0) ?> gallons<br>
                                    <strong>Dimensions:</strong> <?= htmlspecialchars($results['tank_design']['dimensions']['shape']) ?><br>
                                    <strong>Material:</strong> <?= htmlspecialchars($results['tank_design']['material_type']) ?><br>
                                    <strong>Pressure:</strong> <?= number_format($results['performance']['operating_pressure'], 1) ?> psi
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($results['safety'])): ?>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Safety Recommendations</h6>
                            <div class="alert alert-warning">
                                <ul class="mb-0">
                                    <?php foreach ($results['safety'] as $recommendation): ?>
                                    <li><?= htmlspecialchars($recommendation) ?></li>
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
</style>

<?php include '../../../themes/default/views/partials/footer.php'; ?>



