<?php
/**
 * Earthing System Calculator
 * Professional electrical earthing system design and analysis
 * Includes soil resistivity, electrode sizing, and safety calculations
 */

require_once '../../../app/Config/config.php';

// Get system parameters
$systemVoltage = filter_input(INPUT_POST, 'system_voltage', FILTER_VALIDATE_FLOAT);
$faultCurrent = filter_input(INPUT_POST, 'fault_current', FILTER_VALIDATE_FLOAT);
$soilResistivity = filter_input(INPUT_POST, 'soil_resistivity', FILTER_VALIDATE_FLOAT) ?: 100;
$soilType = filter_input(INPUT_POST, 'soil_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'clay';
$electrodeType = filter_input(INPUT_POST, 'electrode_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'rods';
$areaSize = filter_input(INPUT_POST, 'area_size', FILTER_VALIDATE_FLOAT) ?: 1000;
$buildingType = filter_input(INPUT_POST, 'building_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'commercial';
$climate = filter_input(INPUT_POST, 'climate', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'temperate';

$calculations = [];
$results = null;

if ($systemVoltage && $faultCurrent) {
    // Define soil types and their characteristics
    $soilTypes = [
        'clay' => [
            'resistivity_range' => [20, 100],
            'typical_resistivity' => 60,
            'moisture_content' => 0.25,
            'corrosiveness' => 'medium',
            'stability' => 'good'
        ],
        'sand' => [
            'resistivity_range' => [100, 1000],
            'typical_resistivity' => 500,
            'moisture_content' => 0.15,
            'corrosiveness' => 'low',
            'stability' => 'fair'
        ],
        'rock' => [
            'resistivity_range' => [1000, 10000],
            'typical_resistivity' => 5000,
            'moisture_content' => 0.05,
            'corrosiveness' => 'low',
            'stability' => 'excellent'
        ],
        'loam' => [
            'resistivity_range' => [50, 300],
            'typical_resistivity' => 150,
            'moisture_content' => 0.20,
            'corrosiveness' => 'medium',
            'stability' => 'good'
        ],
        'gravel' => [
            'resistivity_range' => [300, 3000],
            'typical_resistivity' => 1000,
            'moisture_content' => 0.10,
            'corrosiveness' => 'low',
            'stability' => 'fair'
        ]
    ];
    
    $soilData = $soilTypes[$soilType] ?: $soilTypes['clay'];
    $actualSoilResistivity = $soilResistivity ?: $soilData['typical_resistivity'];
    
    // Building type requirements
    $buildingRequirements = [
        'residential' => [
            'max_touch_voltage' => 50,
            'max_step_voltage' => 50,
            'safety_factor' => 1.25,
            'electrode_spacing' => 3.0
        ],
        'commercial' => [
            'max_touch_voltage' => 50,
            'max_step_voltage' => 50,
            'safety_factor' => 1.5,
            'electrode_spacing' => 2.0
        ],
        'industrial' => [
            'max_touch_voltage' => 50,
            'max_step_voltage' => 50,
            'safety_factor' => 2.0,
            'electrode_spacing' => 1.5
        ],
        'hospital' => [
            'max_touch_voltage' => 25,
            'max_step_voltage' => 25,
            'safety_factor' => 2.5,
            'electrode_spacing' => 1.0
        ],
        'data_center' => [
            'max_touch_voltage' => 25,
            'max_step_voltage' => 25,
            'safety_factor' => 3.0,
            'electrode_spacing' => 1.0
        ]
    ];
    
    $buildingReq = $buildingRequirements[$buildingType] ?: $buildingRequirements['commercial'];
    
    // Climate factors affecting soil resistivity
    $climateFactors = [
        'tropical' => 0.8,    // High moisture, lower resistivity
        'temperate' => 1.0,   // Moderate conditions
        'arid' => 1.5,        // Dry conditions, higher resistivity
        'continental' => 1.2, // Variable conditions
        'polar' => 2.0        // Frozen ground, very high resistivity
    ];
    
    $climateFactor = $climateFactors[$climate] ?: 1.0;
    $effectiveSoilResistivity = $actualSoilResistivity * $climateFactor;
    
    // Electrode specifications
    $electrodeSpecs = [
        'rods' => [
            'diameter_mm' => 16,
            'length_m' => 2.4,
            'material' => 'copper-clad steel',
            'resistance_per_meter' => 0.1,
            'cost_per_meter' => 25
        ],
        'plates' => [
            'size_mm' => [600, 600],
            'thickness_mm' => 3,
            'material' => 'copper',
            'resistance_per_m2' => 0.05,
            'cost_per_m2' => 150
        ],
        'strips' => [
            'width_mm' => 25,
            'thickness_mm' => 3,
            'material' => 'copper',
            'resistance_per_meter' => 0.08,
            'cost_per_meter' => 15
        ],
        'mesh' => [
            'spacing_mm' => 300,
            'wire_diameter_mm' => 4,
            'material' => 'copper-clad steel',
            'resistance_per_m2' => 0.03,
            'cost_per_m2' => 50
        ]
    ];
    
    $electrodeSpec = $electrodeSpecs[$electrodeType] ?: $electrodeSpecs['rods'];
    
    // Calculate basic earthing resistance
    $basicResistance = calculateBasicResistance($effectiveSoilResistivity, $areaSize, $electrodeType, $electrodeSpec);
    
    // Calculate required electrode configuration
    $requiredResistance = $buildingReq['max_touch_voltage'] / $faultCurrent;
    $resistanceRatio = $basicResistance / max($requiredResistance, 0.1);
    
    // Determine number of electrodes needed
    if ($electrodeType === 'rods') {
        $numberOfRods = ceil($resistanceRatio);
        $rodSpacing = $buildingReq['electrode_spacing'];
        $totalRodLength = $numberOfRods * $electrodeSpec['length_m'];
        $systemResistance = $basicResistance / sqrt($numberOfRods);
    } elseif ($electrodeType === 'plates') {
        $plateArea = $electrodeSpec['size_mm'][0] * $electrodeSpec['size_mm'][1] / 1000000; // Convert to m²
        $numberOfPlates = ceil($resistanceRatio / 2);
        $totalPlateArea = $numberOfPlates * $plateArea;
        $systemResistance = $basicResistance / sqrt($numberOfPlates);
    } else {
        // Strips or mesh
        $stripLength = sqrt($areaSize) * 4; // Perimeter of area
        $numberOfStrips = ceil($resistanceRatio);
        $totalStripLength = $numberOfStrips * $stripLength;
        $systemResistance = $basicResistance / sqrt($numberOfStrips);
    }
    
    // Safety voltage calculations
    $touchVoltage = $systemResistance * $faultCurrent;
    $stepVoltage = $touchVoltage * 0.6; // Step voltage is typically 60% of touch voltage
    $meshVoltage = $touchVoltage * 0.8; // Mesh voltage is typically 80% of touch voltage
    
    // Safety compliance check
    $touchVoltageCompliant = $touchVoltage <= $buildingReq['max_touch_voltage'];
    $stepVoltageCompliant = $stepVoltage <= $buildingReq['max_step_voltage'];
    
    // Conductor sizing for earthing system
    $minConductorSize = calculateConductorSize($faultCurrent, $systemVoltage);
    
    // Chemical earthing enhancement
    $chemicalEnhancement = $effectiveSoilResistivity > 500;
    if ($chemicalEnhancement) {
        $chemicalCost = $areaSize * 50; // $50 per m² for chemical enhancement
        $enhancedResistance = $systemResistance * 0.6; // 40% improvement
    } else {
        $chemicalCost = 0;
        $enhancedResistance = $systemResistance;
    }
    
    // Cost calculations
    if ($electrodeType === 'rods') {
        $electrodeCost = $totalRodLength * $electrodeSpec['cost_per_meter'];
    } elseif ($electrodeType === 'plates') {
        $electrodeCost = $totalPlateArea * $electrodeSpec['cost_per_m2'];
    } else {
        $electrodeCost = $totalStripLength * $electrodeSpec['cost_per_meter'];
    }
    
    $conductorCost = $minConductorSize['cost_per_meter'] * sqrt($areaSize) * 4; // Perimeter wiring
    $installationCost = ($electrodeCost + $conductorCost) * 0.5; // 50% installation cost
    $totalCost = $electrodeCost + $conductorCost + $installationCost + $chemicalCost;
    
    // Maintenance requirements
    $maintenanceInterval = $soilData['corrosiveness'] === 'high' ? 2 : 5; // Years
    $annualMaintenanceCost = $totalCost * 0.02; // 2% of capital cost annually
    
    // Environmental impact
    $copperContent_kg = ($electrodeCost + $conductorCost) / 20; // Approximate copper content
    $landDisturbance_m2 = $areaSize * 0.1; // 10% of area disturbed during installation
    
    // Lightning protection integration
    $lightningProtection = $systemVoltage >= 10000; // High voltage systems need lightning protection
    if ($lightningProtection) {
        $lightningRods = ceil(sqrt($areaSize) / 30); // One rod per 30m
        $lightningCost = $lightningRods * 500; // $500 per lightning rod
        $enhancedProtection = true;
    } else {
        $lightningCost = 0;
        $enhancedProtection = false;
    }
    
    // System monitoring
    $monitoringSystem = $buildingType === 'hospital' || $buildingType === 'data_center';
    if ($monitoringSystem) {
        $monitoringCost = 2000; // $2000 for monitoring system
        $monitoringFeatures = ['Real-time resistance monitoring', 'Automated alerts', 'Data logging'];
    } else {
        $monitoringCost = 0;
        $monitoringFeatures = [];
    }
    
    $calculations = [
        'system_parameters' => [
            'voltage' => $systemVoltage,
            'fault_current' => $faultCurrent,
            'soil_resistivity' => $actualSoilResistivity,
            'effective_resistivity' => round($effectiveSoilResistivity, 1),
            'area_size' => $areaSize,
            'climate_factor' => $climateFactor
        ],
        'soil_analysis' => [
            'type' => $soilType,
            'characteristics' => $soilData,
            'moisture_content' => $soilData['moisture_content'],
            'corrosiveness' => $soilData['corrosiveness'],
            'stability' => $soilData['stability']
        ],
        'earthing_design' => [
            'electrode_type' => $electrodeType,
            'basic_resistance' => round($basicResistance, 2),
            'required_resistance' => round($requiredResistance, 2),
            'system_resistance' => round($systemResistance, 2),
            'enhanced_resistance' => round($enhancedResistance, 2)
        ],
        'safety_analysis' => [
            'touch_voltage' => round($touchVoltage, 1),
            'step_voltage' => round($stepVoltage, 1),
            'mesh_voltage' => round($meshVoltage, 1),
            'max_touch_allowed' => $buildingReq['max_touch_voltage'],
            'max_step_allowed' => $buildingReq['max_step_voltage'],
            'touch_compliant' => $touchVoltageCompliant,
            'step_compliant' => $stepVoltageCompliant
        ],
        'electrode_configuration' => [
            'number_required' => $electrodeType === 'rods' ? $numberOfRods : ($electrodeType === 'plates' ? $numberOfPlates : $numberOfStrips),
            'specifications' => $electrodeSpec,
            'total_length_area' => isset($totalRodLength) ? $totalRodLength : (isset($totalPlateArea) ? $totalPlateArea : $totalStripLength),
            'spacing' => $buildingReq['electrode_spacing']
        ],
        'conductor_sizing' => [
            'minimum_size' => $minConductorSize['size'],
            'ampacity' => $minConductorSize['ampacity'],
            'material' => $minConductorSize['material'],
            'cost_per_meter' => $minConductorSize['cost_per_meter']
        ],
        'cost_analysis' => [
            'electrode_cost' => round($electrodeCost, 0),
            'conductor_cost' => round($conductorCost, 0),
            'installation_cost' => round($installationCost, 0),
            'chemical_enhancement' => round($chemicalCost, 0),
            'lightning_protection' => round($lightningCost, 0),
            'monitoring_system' => round($monitoringCost, 0),
            'total_installed' => round($totalCost + $lightningCost + $monitoringCost, 0),
            'annual_maintenance' => round($annualMaintenanceCost, 0)
        ],
        'environmental' => [
            'copper_content_kg' => round($copperContent_kg, 1),
            'land_disturbance_m2' => round($landDisturbance_m2, 1),
            'chemical_enhancement' => $chemicalEnhancement
        ],
        'additional_systems' => [
            'lightning_protection' => $lightningProtection,
            'enhanced_protection' => $enhancedProtection,
            'monitoring_system' => $monitoringSystem,
            'monitoring_features' => $monitoringFeatures
        ],
        'maintenance' => [
            'interval_years' => $maintenanceInterval,
            'annual_cost' => round($annualMaintenanceCost, 0),
            'requirements' => generateMaintenanceRequirements($soilData, $electrodeType)
        ],
        'recommendations' => generateEarthingRecommendations($systemResistance, $requiredResistance, $touchVoltageCompliant, $stepVoltageCompliant, $chemicalEnhancement, $lightningProtection)
    ];
    
    $results = true;
}

function calculateBasicResistance($soilResistivity, $areaSize, $electrodeType, $spec) {
    if ($electrodeType === 'rods') {
        // Single rod resistance formula
        $length = $spec['length_m'];
        $diameter = $spec['diameter_mm'] / 1000; // Convert to meters
        return ($soilResistivity / (2 * pi() * $length)) * log(4 * $length / $diameter);
    } elseif ($electrodeType === 'plates') {
        // Plate electrode resistance
        $area = ($spec['size_mm'][0] * $spec['size_mm'][1]) / 1000000; // Convert to m²
        return $soilResistivity / (2 * sqrt($area));
    } else {
        // Strip or mesh electrode
        $perimeter = 4 * sqrt($areaSize);
        return $soilResistivity / (2 * $perimeter);
    }
}

function calculateConductorSize($faultCurrent, $systemVoltage) {
    // Simplified conductor sizing based on fault current
    if ($faultCurrent <= 1000) {
        return ['size' => '16 mm²', 'ampacity' => 85, 'material' => 'Copper', 'cost_per_meter' => 8];
    } elseif ($faultCurrent <= 5000) {
        return ['size' => '25 mm²', 'ampacity' => 115, 'material' => 'Copper', 'cost_per_meter' => 12];
    } elseif ($faultCurrent <= 10000) {
        return ['size' => '35 mm²', 'ampacity' => 140, 'material' => 'Copper', 'cost_per_meter' => 16];
    } elseif ($faultCurrent <= 25000) {
        return ['size' => '50 mm²', 'ampacity' => 175, 'material' => 'Copper', 'cost_per_meter' => 22];
    } else {
        return ['size' => '70 mm²', 'ampacity' => 225, 'material' => 'Copper', 'cost_per_meter' => 30];
    }
}

function generateMaintenanceRequirements($soilData, $electrodeType) {
    $requirements = [];
    
    $requirements[] = 'Visual inspection of all connections and conductors';
    $requirements[] = 'Measure earth resistance using earth tester';
    
    if ($soilData['corrosiveness'] === 'high') {
        $requirements[] = 'Check for corrosion on electrodes and connections';
        $requirements[] = 'Apply anti-corrosion compound if necessary';
    }
    
    if ($electrodeType === 'rods') {
        $requirements[] = 'Verify rod depth and condition';
        $requirements[] = 'Check clamp connections for tightness';
    }
    
    $requirements[] = 'Test continuity of earthing conductor';
    $requirements[] = 'Document resistance measurements';
    
    return $requirements;
}

function generateEarthingRecommendations($actual, $required, $touchCompliant, $stepCompliant, $chemical, $lightning) {
    $recommendations = [];
    
    if ($actual > $required * 1.2) {
        $recommendations[] = 'Consider adding more electrodes or chemical enhancement to reduce resistance';
    }
    
    if (!$touchCompliant) {
        $recommendations[] = 'Touch voltage exceeds safe limits - increase electrode quantity or size';
    }
    
    if (!$stepCompliant) {
        $recommendations[] = 'Step voltage exceeds safe limits - improve electrode spacing or add mesh';
    }
    
    if ($chemical) {
        $recommendations[] = 'Chemical earthing enhancement recommended for high resistivity soil';
        $recommendations[] = 'Consider periodic chemical treatment maintenance';
    }
    
    if ($lightning) {
        $recommendations[] = 'Install lightning protection system for high voltage applications';
        $recommendations[] = 'Integrate lightning protection with earthing system';
    }
    
    $recommendations[] = 'Install earth resistance monitoring system for critical facilities';
    $recommendations[] = 'Ensure all metal structures are properly bonded to earthing system';
    $recommendations[] = 'Regular testing and maintenance according to local standards';
    $recommendations[] = 'Consider soil treatment in areas with high resistivity';
    
    return $recommendations;
}

include '../../../themes/default/views/partials/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><i class="icon-shield"></i> Earthing System Calculator</h1>
        <p>Professional electrical earthing system design and analysis</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>System Parameters</h3>
                </div>
                <div class="card-body">
                    <form method="POST" class="form-horizontal">
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">System Voltage (V)</label>
                            <div class="col-sm-6">
                                <input type="number" step="1" name="system_voltage" class="form-control" 
                                       value="<?php echo $systemVoltage ?: ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Fault Current (A)</label>
                            <div class="col-sm-6">
                                <input type="number" step="1" name="fault_current" class="form-control" 
                                       value="<?php echo $faultCurrent ?: ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Soil Resistivity (Ω·m)</label>
                            <div class="col-sm-6">
                                <input type="number" step="1" name="soil_resistivity" class="form-control" 
                                       value="<?php echo $soilResistivity; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Soil Type</label>
                            <div class="col-sm-6">
                                <select name="soil_type" class="form-control">
                                    <option value="clay" <?php echo $soilType === 'clay' ? 'selected' : ''; ?>>Clay</option>
                                    <option value="sand" <?php echo $soilType === 'sand' ? 'selected' : ''; ?>>Sand</option>
                                    <option value="rock" <?php echo $soilType === 'rock' ? 'selected' : ''; ?>>Rock</option>
                                    <option value="loam" <?php echo $soilType === 'loam' ? 'selected' : ''; ?>>Loam</option>
                                    <option value="gravel" <?php echo $soilType === 'gravel' ? 'selected' : ''; ?>>Gravel</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Electrode Type</label>
                            <div class="col-sm-6">
                                <select name="electrode_type" class="form-control">
                                    <option value="rods" <?php echo $electrodeType === 'rods' ? 'selected' : ''; ?>>Ground Rods</option>
                                    <option value="plates" <?php echo $electrodeType === 'plates' ? 'selected' : ''; ?>>Ground Plates</option>
                                    <option value="strips" <?php echo $electrodeType === 'strips' ? 'selected' : ''; ?>>Ground Strips</option>
                                    <option value="mesh" <?php echo $electrodeType === 'mesh' ? 'selected' : ''; ?>>Ground Mesh</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Area Size (m²)</label>
                            <div class="col-sm-6">
                                <input type="number" step="10" name="area_size" class="form-control" 
                                       value="<?php echo $areaSize; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Building Type</label>
                            <div class="col-sm-6">
                                <select name="building_type" class="form-control">
                                    <option value="residential" <?php echo $buildingType === 'residential' ? 'selected' : ''; ?>>Residential</option>
                                    <option value="commercial" <?php echo $buildingType === 'commercial' ? 'selected' : ''; ?>>Commercial</option>
                                    <option value="industrial" <?php echo $buildingType === 'industrial' ? 'selected' : ''; ?>>Industrial</option>
                                    <option value="hospital" <?php echo $buildingType === 'hospital' ? 'selected' : ''; ?>>Hospital</option>
                                    <option value="data_center" <?php echo $buildingType === 'data_center' ? 'selected' : ''; ?>>Data Center</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Climate</label>
                            <div class="col-sm-6">
                                <select name="climate" class="form-control">
                                    <option value="tropical" <?php echo $climate === 'tropical' ? 'selected' : ''; ?>>Tropical</option>
                                    <option value="temperate" <?php echo $climate === 'temperate' ? 'selected' : ''; ?>>Temperate</option>
                                    <option value="arid" <?php echo $climate === 'arid' ? 'selected' : ''; ?>>Arid</option>
                                    <option value="continental" <?php echo $climate === 'continental' ? 'selected' : ''; ?>>Continental</option>
                                    <option value="polar" <?php echo $climate === 'polar' ? 'selected' : ''; ?>>Polar</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="icon-calculator"></i> Calculate Earthing System
                        </button>
                        
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <?php if ($results): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="icon-chart-line"></i> Earthing System Analysis</h3>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4>System Parameters</h4>
                            <table class="table table-condensed">
                                <tr><td>Voltage</td><td><?php echo $calculations['system_parameters']['voltage']; ?> V</td></tr>
                                <tr><td>Fault Current</td><td><?php echo $calculations['system_parameters']['fault_current']; ?> A</td></tr>
                                <tr><td>Soil Resistivity</td><td><?php echo $calculations['system_parameters']['soil_resistivity']; ?> Ω·m</td></tr>
                                <tr><td>Effective Resistivity</td><td><?php echo $calculations['system_parameters']['effective_resistivity']; ?> Ω·m</td></tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Resistance Analysis</h4>
                            <table class="table table-condensed">
                                <tr><td>Basic Resistance</td><td><?php echo $calculations['earthing_design']['basic_resistance']; ?> Ω</td></tr>
                                <tr><td>Required Resistance</td><td><?php echo $calculations['earthing_design']['required_resistance']; ?> Ω</td></tr>
                                <tr><td>System Resistance</td><td><strong><?php echo $calculations['earthing_design']['system_resistance']; ?> Ω</strong></td></tr>
                                <tr><td>Enhanced Resistance</td><td><?php echo $calculations['earthing_design']['enhanced_resistance']; ?> Ω</td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <h4>Safety Analysis</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Touch Voltage:</strong> 
                                <span class="<?php echo $calculations['safety_analysis']['touch_compliant'] ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $calculations['safety_analysis']['touch_voltage']; ?> V
                                </span>
                            </p>
                            <p><strong>Step Voltage:</strong> 
                                <span class="<?php echo $calculations['safety_analysis']['step_compliant'] ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $calculations['safety_analysis']['step_voltage']; ?> V
                                </span>
                            </p>
                            <p><strong>Mesh Voltage:</strong> <?php echo $calculations['safety_analysis']['mesh_voltage']; ?> V</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Max Touch Allowed:</strong> <?php echo $calculations['safety_analysis']['max_touch_allowed']; ?> V</p>
                            <p><strong>Max Step Allowed:</strong> <?php echo $calculations['safety_analysis']['max_step_allowed']; ?> V</p>
                            <p><strong>Compliance Status:</strong> 
                                <span class="<?php echo ($calculations['safety_analysis']['touch_compliant'] && $calculations['safety_analysis']['step_compliant']) ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo ($calculations['safety_analysis']['touch_compliant'] && $calculations['safety_analysis']['step_compliant']) ? 'Compliant' : 'Non-compliant'; ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <h4>Electrode Configuration</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Type:</strong> <?php echo ucfirst($calculations['earthing_design']['electrode_type']); ?></p>
                            <p><strong>Quantity Required:</strong> <?php echo $calculations['electrode_configuration']['number_required']; ?></p>
                            <p><strong>Spacing:</strong> <?php echo $calculations['electrode_configuration']['spacing']; ?> m</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Material:</strong> <?php echo $calculations['electrode_configuration']['specifications']['material']; ?></p>
                            <p><strong>Total Length/Area:</strong> <?php echo round($calculations['electrode_configuration']['total_length_area'], 2); ?></p>
                            <p><strong>Cost per Unit:</strong> $<?php echo $calculations['electrode_configuration']['specifications']['cost_per_meter'] ?: $calculations['electrode_configuration']['specifications']['cost_per_m2']; ?></p>
                        </div>
                    </div>
                    
                    <h4>Conductor Sizing</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Minimum Size:</strong> <?php echo $calculations['conductor_sizing']['minimum_size']; ?></p>
                            <p><strong>Material:</strong> <?php echo $calculations['conductor_sizing']['material']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ampacity:</strong> <?php echo $calculations['conductor_sizing']['ampacity']; ?> A</p>
                            <p><strong>Cost per Meter:</strong> $<?php echo $calculations['conductor_sizing']['cost_per_meter']; ?></p>
                        </div>
                    </div>
                    
                    <h4>Cost Analysis</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Electrodes:</strong> $<?php echo number_format($calculations['cost_analysis']['electrode_cost']); ?></p>
                            <p><strong>Conductors:</strong> $<?php echo number_format($calculations['cost_analysis']['conductor_cost']); ?></p>
                            <p><strong>Installation:</strong> $<?php echo number_format($calculations['cost_analysis']['installation_cost']); ?></p>
                            <p><strong>Chemical Enhancement:</strong> $<?php echo number_format($calculations['cost_analysis']['chemical_enhancement']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Lightning Protection:</strong> $<?php echo number_format($calculations['cost_analysis']['lightning_protection']); ?></p>
                            <p><strong>Monitoring System:</strong> $<?php echo number_format($calculations['cost_analysis']['monitoring_system']); ?></p>
                            <p><strong>Total Installed:</strong> <span class="text-primary"><strong>$<?php echo number_format($calculations['cost_analysis']['total_installed']); ?></strong></span></p>
                            <p><strong>Annual Maintenance:</strong> $<?php echo number_format($calculations['cost_analysis']['annual_maintenance']); ?></p>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><i class="icon-leaf"></i> Environmental Impact</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Copper Content:</strong> <?php echo $calculations['environmental']['copper_content_kg']; ?> kg</p>
                                    <p><strong>Land Disturbance:</strong> <?php echo $calculations['environmental']['land_disturbance_m2']; ?> m²</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Chemical Enhancement:</strong> <?php echo $calculations['environmental']['chemical_enhancement'] ? 'Required' : 'Not Required'; ?></p>
                                    <p><strong>Soil Type:</strong> <?php echo ucfirst($calculations['soil_analysis']['type']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h4><i class="icon-wrench"></i> Maintenance Schedule</h4>
                        </div>
                        <div class="panel-body">
                            <p><strong>Inspection Interval:</strong> <?php echo $calculations['maintenance']['interval_years']; ?> years</p>
                            <p><strong>Annual Cost:</strong> $<?php echo number_format($calculations['maintenance']['annual_cost']); ?></p>
                            <h5>Maintenance Requirements:</h5>
                            <ul>
                                <?php foreach ($calculations['maintenance']['requirements'] as $requirement): ?>
                                <li><?php echo $requirement; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><i class="icon-lightbulb"></i> Recommendations</h4>
                        </div>
                        <div class="panel-body">
                            <ul>
                                <?php foreach ($calculations['recommendations'] as $recommendation): ?>
                                <li><?php echo htmlspecialchars($recommendation); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    
                </div>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="icon-info-circle"></i> Instructions</h3>
                </div>
                <div class="card-body">
                    <h4>Earthing System Calculator</h4>
                    <p>This calculator provides comprehensive earthing system design including:</p>
                    <ul>
                        <li><strong>Soil Analysis:</strong> Resistivity measurements and soil characterization</li>
                        <li><strong>Electrode Sizing:</strong> Calculate optimal electrode configuration</li>
                        <li><strong>Safety Analysis:</strong> Touch and step voltage calculations</li>
                        <li><strong>Conductor Sizing:</strong> Minimum conductor requirements</li>
                        <li><strong>Cost Estimation:</strong> Complete installation and maintenance costs</li>
                        <li><strong>Compliance Check:</strong> Safety standard verification</li>
                        <li><strong>Environmental Impact:</strong> Material usage and land disturbance</li>
                    </ul>
                    
                    <h4>Electrode Types</h4>
                    <ul>
                        <li><strong>Ground Rods:</strong> Vertical electrodes, cost-effective, easy installation</li>
                        <li><strong>Ground Plates:</strong> Horizontal electrodes, good for limited space</li>
                        <li><strong>Ground Strips:</strong> Linear electrodes, suitable for perimeter earthing</li>
                        <li><strong>Ground Mesh:</strong> Grid pattern, excellent for large areas</li>
                    </ul>
                    
                    <h4>Building Types & Requirements</h4>
                    <ul>
                        <li><strong>Residential:</strong> Basic protection, 50V touch/step limits</li>
                        <li><strong>Commercial:</strong> Enhanced protection, 50V limits with monitoring</li>
                        <li><strong>Industrial:</strong> High reliability, 50V limits with redundancy</li>
                        <li><strong>Hospital:</strong> Critical care, 25V limits with monitoring</li>
                        <li><strong>Data Center:</strong> IT protection, 25V limits with monitoring</li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <strong>Important:</strong> Earthing system design requires professional engineering. This calculator provides preliminary estimates only. Always verify with local electrical codes and standards.
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.page-header {
    background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);
    color: white;
    padding: 30px;
    margin-bottom: 30px;
    border-radius: 8px;
}

.icon-shield:before {
    content: "🛡️";
    margin-right: 10px;
}

.table-condensed td {
    padding: 4px 8px;
}

.text-primary {
    color: #2980b9;
    font-weight: bold;
}

.text-success {
    color: #27ae60;
    font-weight: bold;
}

.text-danger {
    color: #e74c3c;
    font-weight: bold;
}

.panel-info .panel-heading {
    background-color: #3498db;
    color: white;
}

.panel-warning .panel-heading {
    background-color: #f39c12;
    color: white;
}

.form-group {
    margin-bottom: 15px;
}

.control-label {
    font-weight: bold;
}
</style>

<?php include '../../../themes/default/views/partials/footer.php'; ?>

