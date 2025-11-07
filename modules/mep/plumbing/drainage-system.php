<?php
require_once rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/aec-calculator/modules/mep/bootstrap.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$pageTitle = "Drainage System Calculator - MEP Suite";
include AEC_ROOT . '/includes/header.php';

// Handle form submission
$results = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buildingType = $_POST['building_type'] ?? '';
    $totalArea = floatval($_POST['total_area'] ?? 0);
    $occupancyLoad = intval($_POST['occupancy_load'] ?? 0);
    $fixtureCount = intval($_POST['fixture_count'] ?? 0);
    $systemType = $_POST['system_type'] ?? '';
    $pipeMaterial = $_POST['pipe_material'] ?? '';
    $includeStorm = isset($_POST['include_storm']) ? true : false;
    
    // Validate inputs
    if (empty($buildingType)) $errors[] = "Building type is required";
    if ($totalArea <= 0) $errors[] = "Total area must be greater than 0";
    if ($occupancyLoad <= 0) $errors[] = "Occupancy load must be greater than 0";
    if ($fixtureCount <= 0) $errors[] = "Fixture count must be greater than 0";
    if (empty($systemType)) $errors[] = "System type is required";
    if (empty($pipeMaterial)) $errors[] = "Pipe material is required";
    
    if (empty($errors)) {
        // Calculate drainage system requirements
        $results = calculateDrainageSystem($buildingType, $totalArea, $occupancyLoad, $fixtureCount, $systemType, $pipeMaterial, $includeStorm);
    }
}

function calculateDrainageSystem($buildingType, $totalArea, $occupancyLoad, $fixtureCount, $systemType, $pipeMaterial, $includeStorm) {
    $results = [];
    
    // Building type factors
    $buildingFactors = [
        'residential' => 0.6,
        'commercial' => 0.8,
        'office' => 0.7,
        'industrial' => 1.0,
        'hospital' => 1.2,
        'school' => 0.9,
        'hotel' => 0.8,
        'retail' => 0.75
    ];
    
    // System type factors
    $systemFactors = [
        'gravity' => 1.0,
        'pressure' => 0.8,
        'vacuum' => 0.6
    ];
    
    // Pipe material factors
    $materialFactors = [
        'pvc' => 1.0,
        'cast_iron' => 0.95,
        'hdpe' => 1.05,
        'copper' => 0.9,
        'steel' => 0.85
    ];
    
    $buildingFactor = $buildingFactors[$buildingType] ?? 1.0;
    $systemFactor = $systemFactors[$systemType] ?? 1.0;
    $materialFactor = $materialFactors[$pipeMaterial] ?? 1.0;
    
    // Base drainage flow calculations
    $baseFlowRate = $occupancyLoad * 0.8 * $buildingFactor; // GPM
    $peakFlowRate = $baseFlowRate * 2.5; // Peak flow factor
    $fixtureLoad = $fixtureCount * 1.0; // DFU (Drainage Fixture Units)
    
    // Storm drainage if requested
    $stormFlowRate = 0;
    $stormDiameter = 0;
    if ($includeStorm) {
        $rainfallIntensity = 3.0; // inches per hour (typical design rainfall)
        $runoffCoefficient = 0.7;
        $stormFlowRate = ($totalArea * $rainfallIntensity * $runoffCoefficient) / 96.2; // CFS
        $stormDiameter = calculatePipeDiameter($stormFlowRate, $systemType, $materialFactor);
    }
    
    // Sanitary drainage calculations
    $sanitaryFlowRate = $peakFlowRate * 0.8; // 80% of peak flow for sanitary
    $sanitaryDiameter = calculatePipeDiameter($sanitaryFlowRate, $systemType, $materialFactor);
    
    // Pipe sizing for different segments
    $segments = calculateDrainageSegments($fixtureLoad, $sanitaryFlowRate, $systemType, $materialFactor);
    
    // Slope calculations
    $minSlope = calculateMinimumSlope($sanitaryDiameter);
    $recommendedSlope = $minSlope * 1.5; // 50% more than minimum
    
    // Ventilation requirements
    $ventDiameter = max($sanitaryDiameter * 0.5, 3); // At least 3 inches
    $ventHeight = calculateVentilationHeight($buildingType, $totalArea);
    
    // Pump requirements (if pressure system)
    $pumpHead = 0;
    $pumpCapacity = 0;
    if ($systemType === 'pressure') {
        $pumpHead = calculatePumpHead($totalArea, $buildingType);
        $pumpCapacity = $peakFlowRate * 1.2; // 20% safety factor
    }
    
    // Cost estimation
    $pipeCost = calculatePipeCost($sanitaryDiameter, $totalArea, $pipeMaterial);
    $fittingCost = $pipeCost * 0.3; // Fittings typically 30% of pipe cost
    $laborCost = ($pipeCost + $fittingCost) * 0.6; // Labor typically 60% of material cost
    $totalCost = $pipeCost + $fittingCost + $laborCost;
    
    // Code compliance
    $codeCompliance = checkCodeCompliance($buildingType, $systemType, $sanitaryDiameter, $ventDiameter, $fixtureLoad);
    
    $results = [
        'sanitary_flow_rate' => round($sanitaryFlowRate, 2),
        'peak_flow_rate' => round($peakFlowRate, 2),
        'sanitary_diameter' => round($sanitaryDiameter, 2),
        'storm_flow_rate' => round($stormFlowRate, 2),
        'storm_diameter' => round($stormDiameter, 2),
        'segments' => $segments,
        'minimum_slope' => round($minSlope, 4),
        'recommended_slope' => round($recommendedSlope, 4),
        'vent_diameter' => round($ventDiameter, 2),
        'vent_height' => round($ventHeight, 2),
        'pump_head' => round($pumpHead, 2),
        'pump_capacity' => round($pumpCapacity, 2),
        'pipe_cost' => round($pipeCost, 2),
        'fitting_cost' => round($fittingCost, 2),
        'labor_cost' => round($laborCost, 2),
        'total_cost' => round($totalCost, 2),
        'code_compliance' => $codeCompliance,
        'fixture_load' => $fixtureLoad,
        'building_factor' => $buildingFactor,
        'system_factor' => $systemFactor,
        'material_factor' => $materialFactor
    ];
    
    return $results;
}

function calculatePipeDiameter($flowRate, $systemType, $materialFactor) {
    // Simplified pipe sizing calculation
    // Using Hazen-Williams formula simplified for drainage
    $velocity = 2.0; // ft/s (typical range 1.5-3.0)
    $area = $flowRate / ($velocity * 448.8); // Convert GPM to ft³/s
    $diameter = sqrt(($area * 4) / pi()) * 12; // Convert to inches
    return max($diameter * $materialFactor, 4); // Minimum 4 inches
}

function calculateDrainageSegments($fixtureLoad, $flowRate, $systemType, $materialFactor) {
    $segments = [];
    
    // Main building drain (largest diameter)
    $mainDiameter = calculatePipeDiameter($flowRate * 0.8, $systemType, $materialFactor);
    $segments[] = [
        'name' => 'Main Building Drain',
        'diameter' => round($mainDiameter, 2),
        'flow_rate' => round($flowRate * 0.8, 2),
        'slope' => 0.02,
        'length' => round(sqrt(2) * 50, 0) // Approximate diagonal length
    ];
    
    // Horizontal branches
    $branchFlowRate = $flowRate * 0.6;
    $branchDiameter = calculatePipeDiameter($branchFlowRate, $systemType, $materialFactor);
    $segments[] = [
        'name' => 'Horizontal Branch',
        'diameter' => round($branchDiameter, 2),
        'flow_rate' => round($branchFlowRate, 2),
        'slope' => 0.02,
        'length' => 30
    ];
    
    // Vertical stacks
    $stackFlowRate = $flowRate * 0.4;
    $stackDiameter = calculatePipeDiameter($stackFlowRate, $systemType, $materialFactor);
    $segments[] = [
        'name' => 'Vertical Stack',
        'diameter' => round($stackDiameter, 2),
        'flow_rate' => round($stackFlowRate, 2),
        'slope' => 0,
        'length' => 25
    ];
    
    // Fixture connections
    $fixtureFlowRate = $flowRate * 0.2;
    $fixtureDiameter = calculatePipeDiameter($fixtureFlowRate, $systemType, $materialFactor);
    $segments[] = [
        'name' => 'Fixture Connections',
        'diameter' => round($fixtureDiameter, 2),
        'flow_rate' => round($fixtureFlowRate, 2),
        'slope' => 0.04,
        'length' => 10
    ];
    
    return $segments;
}

function calculateMinimumSlope($diameter) {
    // IPC minimum slope requirements
    $slopes = [
        4 => 0.008,  // 4" pipe
        6 => 0.006,  // 6" pipe
        8 => 0.004,  // 8" pipe
        10 => 0.003, // 10" pipe
        12 => 0.002  // 12" pipe
    ];
    
    foreach ($slopes as $size => $slope) {
        if ($diameter <= $size) {
            return $slope;
        }
    }
    
    return 0.001; // Minimum slope for larger pipes
}

function calculateVentilationHeight($buildingType, $totalArea) {
    // Ventilation height calculation based on building type and area
    $baseHeight = 20; // feet
    $areaMultiplier = sqrt($totalArea) * 0.5;
    $buildingMultiplier = [
        'hospital' => 1.5,
        'industrial' => 1.3,
        'commercial' => 1.2,
        'office' => 1.1,
        'residential' => 1.0,
        'school' => 1.1,
        'hotel' => 1.2,
        'retail' => 1.1
    ][$buildingType] ?? 1.0;
    
    return ($baseHeight + $areaMultiplier) * $buildingMultiplier;
}

function calculatePumpHead($totalArea, $buildingType) {
    // Static head calculation
    $stories = max(1, $totalArea / 10000); // Rough estimate of stories
    $staticHead = $stories * 12; // 12 feet per story
    
    // Friction head (simplified)
    $frictionHead = sqrt($totalArea) * 0.5;
    
    // Building type adjustment
    $multiplier = [
        'hospital' => 1.3,
        'industrial' => 1.2,
        'commercial' => 1.1,
        'office' => 1.0,
        'residential' => 0.9,
        'school' => 1.0,
        'hotel' => 1.1,
        'retail' => 1.0
    ][$buildingType] ?? 1.0;
    
    return ($staticHead + $frictionHead) * $multiplier;
}

function calculatePipeCost($diameter, $totalArea, $material) {
    $baseCostPerFt = [
        'pvc' => 5.50,
        'cast_iron' => 12.00,
        'hdpe' => 6.75,
        'copper' => 18.50,
        'steel' => 15.25
    ][$material] ?? 8.00;
    
    $pipeLength = sqrt($totalArea) * 3; // Estimate pipe length
    $diameterMultiplier = ($diameter / 6); // Scale cost with diameter
    
    return $pipeLength * $baseCostPerFt * $diameterMultiplier;
}

function checkCodeCompliance($buildingType, $systemType, $sanitaryDiameter, $ventDiameter, $fixtureLoad) {
    $compliance = [];
    
    // Pipe diameter requirements
    if ($sanitaryDiameter < 6) {
        $compliance[] = "Warning: Sanitary pipe diameter may be insufficient for peak flow conditions";
    } else {
        $compliance[] = "✓ Sanitary pipe diameter meets minimum requirements";
    }
    
    // Ventilation requirements
    if ($ventDiameter < 3) {
        $compliance[] = "Error: Vent diameter too small (minimum 3 inches required)";
    } else {
        $compliance[] = "✓ Ventilation system meets minimum diameter requirements";
    }
    
    // Fixture load limits
    if ($fixtureLoad > 1000) {
        $compliance[] = "Warning: High fixture load may require multiple drainage systems";
    } else {
        $compliance[] = "✓ Fixture load within acceptable limits";
    }
    
    // System type suitability
    $suitableSystems = [
        'hospital' => ['gravity', 'pressure'],
        'industrial' => ['gravity', 'pressure'],
        'commercial' => ['gravity', 'pressure'],
        'office' => ['gravity'],
        'residential' => ['gravity'],
        'school' => ['gravity'],
        'hotel' => ['gravity', 'pressure'],
        'retail' => ['gravity', 'pressure']
    ];
    
    if (in_array($systemType, $suitableSystems[$buildingType] ?? ['gravity'])) {
        $compliance[] = "✓ Selected system type is appropriate for building use";
    } else {
        $compliance[] = "Warning: Consider gravity system for better reliability";
    }
    
    return $compliance;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i class="fas fa-water"></i> Drainage System Calculator</h1>
                        <p class="lead">Design and analyze building drainage systems with comprehensive calculations</p>
                    </div>
                    <div>
                        <a href="../mep.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to MEP Suite
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calculator"></i> System Parameters</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <strong>Please correct the following errors:</strong>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="building_type" class="form-label">Building Type</label>
                            <select class="form-select" id="building_type" name="building_type" required>
                                <option value="">Select Building Type</option>
                                <option value="residential" <?= ($_POST['building_type'] ?? '') === 'residential' ? 'selected' : '' ?>>Residential</option>
                                <option value="commercial" <?= ($_POST['building_type'] ?? '') === 'commercial' ? 'selected' : '' ?>>Commercial</option>
                                <option value="office" <?= ($_POST['building_type'] ?? '') === 'office' ? 'selected' : '' ?>>Office Building</option>
                                <option value="industrial" <?= ($_POST['building_type'] ?? '') === 'industrial' ? 'selected' : '' ?>>Industrial</option>
                                <option value="hospital" <?= ($_POST['building_type'] ?? '') === 'hospital' ? 'selected' : '' ?>>Hospital</option>
                                <option value="school" <?= ($_POST['building_type'] ?? '') === 'school' ? 'selected' : '' ?>>School</option>
                                <option value="hotel" <?= ($_POST['building_type'] ?? '') === 'hotel' ? 'selected' : '' ?>>Hotel</option>
                                <option value="retail" <?= ($_POST['building_type'] ?? '') === 'retail' ? 'selected' : '' ?>>Retail</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="total_area" class="form-label">Total Building Area (sq ft)</label>
                            <input type="number" class="form-control" id="total_area" name="total_area" 
                                   value="<?= htmlspecialchars($_POST['total_area'] ?? '') ?>" required min="1">
                        </div>

                        <div class="mb-3">
                            <label for="occupancy_load" class="form-label">Occupancy Load</label>
                            <input type="number" class="form-control" id="occupancy_load" name="occupancy_load" 
                                   value="<?= htmlspecialchars($_POST['occupancy_load'] ?? '') ?>" required min="1">
                        </div>

                        <div class="mb-3">
                            <label for="fixture_count" class="form-label">Total Fixture Count</label>
                            <input type="number" class="form-control" id="fixture_count" name="fixture_count" 
                                   value="<?= htmlspecialchars($_POST['fixture_count'] ?? '') ?>" required min="1">
                        </div>

                        <div class="mb-3">
                            <label for="system_type" class="form-label">System Type</label>
                            <select class="form-select" id="system_type" name="system_type" required>
                                <option value="">Select System Type</option>
                                <option value="gravity" <?= ($_POST['system_type'] ?? '') === 'gravity' ? 'selected' : '' ?>>Gravity System</option>
                                <option value="pressure" <?= ($_POST['system_type'] ?? '') === 'pressure' ? 'selected' : '' ?>>Pressure System</option>
                                <option value="vacuum" <?= ($_POST['system_type'] ?? '') === 'vacuum' ? 'selected' : '' ?>>Vacuum System</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="pipe_material" class="form-label">Pipe Material</label>
                            <select class="form-select" id="pipe_material" name="pipe_material" required>
                                <option value="">Select Pipe Material</option>
                                <option value="pvc" <?= ($_POST['pipe_material'] ?? '') === 'pvc' ? 'selected' : '' ?>>PVC</option>
                                <option value="cast_iron" <?= ($_POST['pipe_material'] ?? '') === 'cast_iron' ? 'selected' : '' ?>>Cast Iron</option>
                                <option value="hdpe" <?= ($_POST['pipe_material'] ?? '') === 'hdpe' ? 'selected' : '' ?>>HDPE</option>
                                <option value="copper" <?= ($_POST['pipe_material'] ?? '') === 'copper' ? 'selected' : '' ?>>Copper</option>
                                <option value="steel" <?= ($_POST['pipe_material'] ?? '') === 'steel' ? 'selected' : '' ?>>Steel</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_storm" name="include_storm" 
                                       <?= isset($_POST['include_storm']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="include_storm">
                                    Include Storm Water Drainage
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-calculator"></i> Calculate Drainage System
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <?php if (!empty($results)): ?>
                <div class="row">
                    <!-- Flow Rates -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-tint"></i> Flow Rates</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['sanitary_flow_rate'] ?></div>
                                            <div class="metric-label">GPM</div>
                                            <small class="text-muted">Sanitary Flow</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['peak_flow_rate'] ?></div>
                                            <div class="metric-label">GPM</div>
                                            <small class="text-muted">Peak Flow</small>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($results['storm_flow_rate'] > 0): ?>
                                    <hr>
                                    <div class="text-center">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['storm_flow_rate'] ?></div>
                                            <div class="metric-label">CFS</div>
                                            <small class="text-muted">Storm Flow</small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Pipe Sizing -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-circle"></i> Pipe Sizing</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['sanitary_diameter'] ?></div>
                                            <div class="metric-label">inches</div>
                                            <small class="text-muted">Sanitary Pipe</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['vent_diameter'] ?></div>
                                            <div class="metric-label">inches</div>
                                            <small class="text-muted">Vent Pipe</small>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($results['storm_diameter'] > 0): ?>
                                    <hr>
                                    <div class="text-center">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['storm_diameter'] ?></div>
                                            <div class="metric-label">inches</div>
                                            <small class="text-muted">Storm Pipe</small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Slopes -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-angle-down"></i> Pipe Slopes</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['minimum_slope'] ?></div>
                                            <div class="metric-label">ft/ft</div>
                                            <small class="text-muted">Minimum Slope</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['recommended_slope'] ?></div>
                                            <div class="metric-label">ft/ft</div>
                                            <small class="text-muted">Recommended</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Details -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-info-circle"></i> System Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['vent_height'] ?></div>
                                            <div class="metric-label">feet</div>
                                            <small class="text-muted">Vent Height</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['fixture_load'] ?></div>
                                            <div class="metric-label">DFU</div>
                                            <small class="text-muted">Fixture Units</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pump Requirements (if applicable) -->
                    <?php if ($results['pump_capacity'] > 0): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6><i class="fas fa-cog"></i> Pump Requirements</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="metric">
                                                <div class="metric-value"><?= $results['pump_capacity'] ?></div>
                                                <div class="metric-label">GPM</div>
                                                <small class="text-muted">Capacity</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="metric">
                                                <div class="metric-value"><?= $results['pump_head'] ?></div>
                                                <div class="metric-label">feet</div>
                                                <small class="text-muted">Total Head</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Cost Estimation -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-dollar-sign"></i> Cost Estimation</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric">
                                            <div class="metric-value">$<?= number_format($results['pipe_cost']) ?></div>
                                            <div class="metric-label">Pipe</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric">
                                            <div class="metric-value">$<?= number_format($results['fitting_cost']) ?></div>
                                            <div class="metric-label">Fittings</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric">
                                            <div class="metric-value">$<?= number_format($results['total_cost']) ?></div>
                                            <div class="metric-label">Total</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pipe Segments -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-list"></i> Drainage System Segments</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Segment</th>
                                                <th>Diameter (in)</th>
                                                <th>Flow Rate (GPM)</th>
                                                <th>Slope (ft/ft)</th>
                                                <th>Length (ft)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($results['segments'] as $segment): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($segment['name']) ?></td>
                                                    <td><?= $segment['diameter'] ?></td>
                                                    <td><?= $segment['flow_rate'] ?></td>
                                                    <td><?= $segment['slope'] ?></td>
                                                    <td><?= $segment['length'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Code Compliance -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-clipboard-check"></i> Code Compliance</h6>
                            </div>
                            <div class="card-body">
                                <div class="compliance-status">
                                    <?php foreach ($results['code_compliance'] as $compliance): ?>
                                        <div class="compliance-item <?= strpos($compliance, 'Error') !== false ? 'error' : (strpos($compliance, 'Warning') !== false ? 'warning' : 'success') ?>">
                                            <i class="fas <?= strpos($compliance, 'Error') !== false ? 'fa-times-circle' : (strpos($compliance, 'Warning') !== false ? 'fa-exclamation-triangle' : 'fa-check-circle') ?>"></i>
                                            <?= htmlspecialchars($compliance) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Safety Recommendations -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-shield-alt"></i> Safety & Design Recommendations</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Design Considerations</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Install cleanouts at appropriate intervals</li>
                                            <li><i class="fas fa-check text-success"></i> Provide adequate ventilation</li>
                                            <li><i class="fas fa-check text-success"></i> Use proper pipe supports and hangers</li>
                                            <li><i class="fas fa-check text-success"></i> Install backwater valves where required</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Maintenance Access</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Locate cleanouts in accessible areas</li>
                                            <li><i class="fas fa-check text-success"></i> Provide adequate clearance for maintenance</li>
                                            <li><i class="fas fa-check text-success"></i> Include access panels for hidden sections</li>
                                            <li><i class="fas fa-check text-success"></i> Install flow meters for monitoring</li>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                                <div class="alert alert-info">
                                    <strong><i class="fas fa-info-circle"></i> Professional Review Required:</strong>
                                    This calculation provides preliminary design parameters. All drainage system designs must be reviewed and approved by licensed plumbing professionals and must comply with local codes and regulations.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-water fa-3x text-muted mb-3"></i>
                        <h4>Drainage System Calculator</h4>
                        <p class="text-muted">Enter the building parameters and system requirements to calculate optimal drainage system design including pipe sizing, flow rates, ventilation requirements, and cost estimation.</p>
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-circle text-primary"></i>
                                    <h6>Flow Analysis</h6>
                                    <small>Calculate sanitary, peak, and storm flow rates</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-circle text-success"></i>
                                    <h6>Pipe Sizing</h6>
                                    <small>Optimal diameter calculations for all segments</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-circle text-warning"></i>
                                    <h6>Slope Requirements</h6>
                                    <small>Minimum and recommended slopes for proper drainage</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-circle text-info"></i>
                                    <h6>Code Compliance</h6>
                                    <small>Built-in compliance checking for safety standards</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.metric {
    margin: 0.5rem 0;
}

.metric-value {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--primary-color);
    line-height: 1;
}

.metric-label {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

.compliance-status {
    max-height: 300px;
    overflow-y: auto;
}

.compliance-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
}

.compliance-item:last-child {
    border-bottom: none;
}

.compliance-item.success {
    color: #28a745;
}

.compliance-item.warning {
    color: #ffc107;
}

.compliance-item.error {
    color: #dc3545;
}

.compliance-item i {
    margin-right: 0.5rem;
    width: 1.2rem;
}

.feature {
    text-align: center;
    padding: 1rem;
}

.feature i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.feature h6 {
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--primary-color);
}

.card-header h5,
.card-header h6 {
    margin: 0;
    color: var(--primary-color);
}

.alert-info {
    border-left: 4px solid var(--primary-color);
}

@media (max-width: 768px) {
    .metric-value {
        font-size: 1.4rem;
    }
    
    .feature {
        margin-bottom: 1rem;
    }
}
</style>

<?php include AEC_ROOT . '/includes/footer.php'; ?>
