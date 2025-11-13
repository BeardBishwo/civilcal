<?php
// Fire Pump Sizing Calculator
// NFPA 20 Compliant Fire Pump Selection and Sizing

require_once '../../../app/Config/config.php';
require_once '../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Helpers/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$page_title = "Fire Pump Sizing Calculator";
include '../../../themes/default/views/partials/header.php';

// Handle form submission
$results = null;
if ($_POST) {
    $results = calculateFirePump($_POST);
}

function calculateFirePump($data) {
    $results = [];
    
    // Input validation and sanitization
    $building_type = sanitizeInput($data['building_type'] ?? '');
    $area = floatval($data['area'] ?? 0);
    $occupancy = sanitizeInput($data['occupancy'] ?? '');
    $hazard_class = sanitizeInput($data['hazard_class'] ?? '');
    $sprinkler_density = floatval($data['sprinkler_density'] ?? 0);
    $number_sprinklers = intval($data['number_sprinklers'] ?? 0);
    $standpipe_demand = floatval($data['standpipe_demand'] ?? 0);
    $hose_stream = floatval($data['hose_stream'] ?? 0);
    $suction_source = sanitizeInput($data['suction_source'] ?? '');
    $elevation = floatval($data['elevation'] ?? 0);
    $friction_loss = floatval($data['friction_loss'] ?? 0);
    
    // Basic flow requirements based on building type and area
    $base_flow = getBaseFlowRate($building_type, $area);
    
    // Sprinkler system demand
    $sprinkler_flow = $sprinkler_density * $area * 1.0; // gpm per sq ft
    
    // Standpipe demand
    $standpipe_flow = $standpipe_demand;
    
    // Hose stream demand
    $hose_stream_flow = $hose_stream;
    
    // Total required flow (gpm)
    $total_flow = max($base_flow, $sprinkler_flow + $standpipe_flow + $hose_stream_flow);
    
    // Pressure calculations
    $residual_pressure = 100; // psi minimum residual pressure
    $suction_pressure = calculateSuctionPressure($suction_source, $elevation);
    $net_positive_suction_head = $suction_pressure + ($elevation * 0.433); // Convert feet to psi
    
    // Total dynamic head
    $total_dynamic_head = $residual_pressure + $friction_loss + ($elevation * 0.433);
    
    // Pump selection
    $pump_capacity = roundUpToStandardSize($total_flow);
    $pump_head = roundUpToStandardHead($total_dynamic_head);
    
    // Pump type selection
    $pump_type = selectPumpType($hazard_class, $total_flow);
    
    // Driver power calculation
    $driver_power = calculateDriverPower($pump_capacity, $pump_head);
    
    // Performance analysis
    $efficiency = calculatePumpEfficiency($pump_capacity, $pump_head);
    $npsh_available = $net_positive_suction_head;
    $npsh_required = getNPSHRequired($pump_capacity);
    
    // Cost estimation
    $pump_cost = estimatePumpCost($pump_capacity, $pump_head, $pump_type);
    $installation_cost = $pump_cost * 0.3; // 30% of equipment cost
    $total_cost = $pump_cost + $installation_cost;
    
    // Code compliance
    $compliance_notes = getComplianceNotes($building_type, $hazard_class, $pump_capacity);
    
    // Safety recommendations
    $safety_notes = getSafetyRecommendations($pump_type, $npsh_available, $npsh_required);
    
    $results = [
        'input_data' => [
            'building_type' => $building_type,
            'area' => $area,
            'occupancy' => $occupancy,
            'hazard_class' => $hazard_class,
            'sprinkler_density' => $sprinkler_density,
            'number_sprinklers' => $number_sprinklers,
            'standpipe_demand' => $standpipe_demand,
            'hose_stream' => $hose_stream,
            'suction_source' => $suction_source,
            'elevation' => $elevation,
            'friction_loss' => $friction_loss
        ],
        'calculations' => [
            'base_flow' => $base_flow,
            'sprinkler_flow' => $sprinkler_flow,
            'standpipe_flow' => $standpipe_flow,
            'hose_stream_flow' => $hose_stream_flow,
            'total_flow' => $total_flow,
            'residual_pressure' => $residual_pressure,
            'suction_pressure' => $suction_pressure,
            'net_positive_suction_head' => $net_positive_suction_head,
            'total_dynamic_head' => $total_dynamic_head
        ],
        'pump_selection' => [
            'capacity' => $pump_capacity,
            'head' => $pump_head,
            'type' => $pump_type,
            'driver_power' => $driver_power,
            'efficiency' => $efficiency
        ],
        'performance' => [
            'npsh_available' => $npsh_available,
            'npsh_required' => $npsh_required,
            'npsh_margin' => $npsh_available - $npsh_required,
            'efficiency' => $efficiency
        ],
        'cost_estimation' => [
            'pump_cost' => $pump_cost,
            'installation_cost' => $installation_cost,
            'total_cost' => $total_cost
        ],
        'compliance' => $compliance_notes,
        'safety' => $safety_notes
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
    if ($area > 50000) {
        $base_rate *= 1.5;
    } elseif ($area > 20000) {
        $base_rate *= 1.25;
    }
    
    return $base_rate;
}

function calculateSuctionPressure($source, $elevation) {
    $pressures = [
        'municipal' => 60, // psi
        'tank_elevated' => 40 + ($elevation * 0.433),
        'tank_ground' => 20,
        'well' => 30,
        'pond' => 15
    ];
    
    return $pressures[$source] ?? 40;
}

function roundUpToStandardSize($flow) {
    $standard_sizes = [500, 750, 1000, 1250, 1500, 2000, 2500, 3000, 4000, 5000];
    
    foreach ($standard_sizes as $size) {
        if ($flow <= $size) {
            return $size;
        }
    }
    
    return 5000; // Maximum standard size
}

function roundUpToStandardHead($head) {
    $standard_heads = [40, 60, 80, 100, 120, 140, 160, 180, 200];
    
    foreach ($standard_heads as $std_head) {
        if ($head <= $std_head) {
            return $std_head;
        }
    }
    
    return 200; // Maximum standard head
}

function selectPumpType($hazard_class, $flow) {
    if ($hazard_class === 'extra_hazard' || $flow > 2000) {
        return 'Horizontal Split-Case';
    } elseif ($hazard_class === 'ordinary_hazard' || $flow > 1000) {
        return 'Vertical Turbine';
    } else {
        return 'End-Suction';
    }
}

function calculateDriverPower($flow, $head) {
    // Power (HP) = (Flow GPM × Head PSI) / (1714 × Efficiency)
    $efficiency = 0.75; // 75% efficiency assumption
    $power = ($flow * $head) / (1714 * $efficiency);
    
    // Add 20% safety factor
    $power *= 1.2;
    
    // Round up to standard motor sizes
    $standard_powers = [15, 20, 25, 30, 40, 50, 60, 75, 100, 125, 150, 200, 250, 300];
    
    foreach ($standard_powers as $std_power) {
        if ($power <= $std_power) {
            return $std_power;
        }
    }
    
    return 300; // Maximum standard power
}

function calculatePumpEfficiency($flow, $head) {
    // Simplified efficiency calculation based on pump size
    if ($flow < 1000) {
        return 65;
    } elseif ($flow < 2000) {
        return 72;
    } elseif ($flow < 3000) {
        return 78;
    } else {
        return 82;
    }
}

function getNPSHRequired($flow) {
    if ($flow < 1000) {
        return 5;
    } elseif ($flow < 2000) {
        return 8;
    } elseif ($flow < 3000) {
        return 12;
    } else {
        return 15;
    }
}

function estimatePumpCost($capacity, $head, $type) {
    $base_cost = $capacity * $head * 0.5; // Base cost calculation
    
    $type_multipliers = [
        'End-Suction' => 1.0,
        'Vertical Turbine' => 1.3,
        'Horizontal Split-Case' => 1.5
    ];
    
    $multiplier = $type_multipliers[$type] ?? 1.0;
    
    return $base_cost * $multiplier;
}

function getComplianceNotes($building_type, $hazard_class, $capacity) {
    $notes = [];
    
    $notes[] = "NFPA 20: Fire pump capacity must be adequate for the largest fire protection demand";
    
    if ($capacity >= 1500) {
        $notes[] = "NFPA 20: Multiple pumps may be required for redundancy";
    }
    
    if ($hazard_class === 'extra_hazard') {
        $notes[] = "NFPA 13: Extra hazard occupancies require higher density sprinkler systems";
    }
    
    $notes[] = "NFPA 20: Pump must be tested annually per manufacturer specifications";
    $notes[] = "Local authority having jurisdiction approval required";
    
    return $notes;
}

function getSafetyRecommendations($pump_type, $npsh_available, $npsh_required) {
    $recommendations = [];
    
    if ($npsh_available < $npsh_required) {
        $recommendations[] = "WARNING: NPSH available is less than required - cavitation risk";
        $recommendations[] = "Consider increasing suction pipe size or reducing pump elevation";
    }
    
    if ($pump_type === 'Horizontal Split-Case') {
        $recommendations[] = "Ensure adequate space for maintenance access around pump";
    }
    
    $recommendations[] = "Install pressure gauges on suction and discharge lines";
    $recommendations[] = "Provide adequate ventilation for pump room";
    $recommendations[] = "Install fire detection in pump room";
    $recommendations[] = "Ensure emergency power supply for pump operation";
    
    return $recommendations;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-fire-extinguisher"></i> Fire Pump Sizing Calculator</h4>
                    <p class="mb-0">NFPA 20 Compliant Fire Pump Selection and Sizing</p>
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
                                    <label for="number_sprinklers">Number of Sprinklers</label>
                                    <input type="number" class="form-control" id="number_sprinklers" name="number_sprinklers" 
                                           value="<?= htmlspecialchars($_POST['number_sprinklers'] ?? '') ?>">
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
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Suction Conditions</h6>
                                <div class="form-group">
                                    <label for="suction_source">Suction Source</label>
                                    <select class="form-control" id="suction_source" name="suction_source" required>
                                        <option value="">Select Suction Source</option>
                                        <option value="municipal" <?= ($_POST['suction_source'] ?? '') === 'municipal' ? 'selected' : '' ?>>Municipal Water</option>
                                        <option value="tank_elevated" <?= ($_POST['suction_source'] ?? '') === 'tank_elevated' ? 'selected' : '' ?>>Elevated Tank</option>
                                        <option value="tank_ground" <?= ($_POST['suction_source'] ?? '') === 'tank_ground' ? 'selected' : '' ?>>Ground Tank</option>
                                        <option value="well" <?= ($_POST['suction_source'] ?? '') === 'well' ? 'selected' : '' ?>>Well</option>
                                        <option value="pond" <?= ($_POST['suction_source'] ?? '') === 'pond' ? 'selected' : '' ?>>Pond/Lake</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="elevation">Pump Elevation Above Source (ft)</label>
                                    <input type="number" step="0.1" class="form-control" id="elevation" name="elevation" 
                                           value="<?= htmlspecialchars($_POST['elevation'] ?? '') ?>" 
                                           placeholder="0 - 50">
                                </div>
                                
                                <div class="form-group">
                                    <label for="friction_loss">Friction Loss (psi)</label>
                                    <input type="number" step="0.1" class="form-control" id="friction_loss" name="friction_loss" 
                                           value="<?= htmlspecialchars($_POST['friction_loss'] ?? '') ?>" 
                                           placeholder="20 - 100">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-calculator"></i> Calculate Fire Pump
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
                    <h4><i class="fas fa-chart-line"></i> Fire Pump Sizing Results</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Flow Requirements</h6>
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
                                    <td>Standpipe Flow:</td>
                                    <td><?= number_format($results['calculations']['standpipe_flow']) ?> gpm</td>
                                </tr>
                                <tr>
                                    <td>Hose Stream Flow:</td>
                                    <td><?= number_format($results['calculations']['hose_stream_flow']) ?> gpm</td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total Required Flow:</strong></td>
                                    <td><strong><?= number_format($results['calculations']['total_flow']) ?> gpm</strong></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Pressure Analysis</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Residual Pressure:</td>
                                    <td><?= number_format($results['calculations']['residual_pressure']) ?> psi</td>
                                </tr>
                                <tr>
                                    <td>Suction Pressure:</td>
                                    <td><?= number_format($results['calculations']['suction_pressure']) ?> psi</td>
                                </tr>
                                <tr>
                                    <td>NPSH Available:</td>
                                    <td><?= number_format($results['performance']['npsh_available'], 1) ?> ft</td>
                                </tr>
                                <tr>
                                    <td>Total Dynamic Head:</td>
                                    <td><?= number_format($results['calculations']['total_dynamic_head']) ?> psi</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Pump Selection</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Pump Capacity:</td>
                                    <td><strong><?= number_format($results['pump_selection']['capacity']) ?> gpm</strong></td>
                                </tr>
                                <tr>
                                    <td>Pump Head:</td>
                                    <td><strong><?= number_format($results['pump_selection']['head']) ?> psi</strong></td>
                                </tr>
                                <tr>
                                    <td>Pump Type:</td>
                                    <td><strong><?= htmlspecialchars($results['pump_selection']['type']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Driver Power:</td>
                                    <td><strong><?= number_format($results['pump_selection']['driver_power']) ?> HP</strong></td>
                                </tr>
                                <tr>
                                    <td>Pump Efficiency:</td>
                                    <td><?= number_format($results['pump_selection']['efficiency']) ?>%</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Performance Analysis</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>NPSH Required:</td>
                                    <td><?= number_format($results['performance']['npsh_required']) ?> ft</td>
                                </tr>
                                <tr>
                                    <td>NPSH Margin:</td>
                                    <td class="<?= $results['performance']['npsh_margin'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <strong><?= number_format($results['performance']['npsh_margin'], 1) ?> ft</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>System Efficiency:</td>
                                    <td><?= number_format($results['performance']['efficiency']) ?>%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Cost Estimation</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Pump Equipment Cost:</td>
                                    <td>$<?= number_format($results['cost_estimation']['pump_cost'], 0) ?></td>
                                </tr>
                                <tr>
                                    <td>Installation Cost:</td>
                                    <td>$<?= number_format($results['cost_estimation']['installation_cost'], 0) ?></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total Project Cost:</strong></td>
                                    <td><strong>$<?= number_format($results['cost_estimation']['total_cost'], 0) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Code Compliance</h6>
                            <ul class="list-unstyled">
                                <?php foreach ($results['compliance'] as $note): ?>
                                <li><i class="fas fa-check text-success"></i> <?= htmlspecialchars($note) ?></li>
                                <?php endforeach; ?>
                            </ul>
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

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
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
</style>

<?php include '../../../themes/default/views/partials/footer.php'; ?>



