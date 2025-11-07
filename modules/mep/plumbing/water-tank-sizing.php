<?php
require_once rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/aec-calculator/modules/mep/bootstrap.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$pageTitle = "Water Tank Sizing Calculator - MEP Suite";
include AEC_ROOT . '/includes/header.php';

// Handle form submission
$results = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buildingType = $_POST['building_type'] ?? '';
    $totalOccupancy = intval($_POST['total_occupancy'] ?? 0);
    $dailyDemandPerPerson = floatval($_POST['daily_demand_per_person'] ?? 0);
    $operatingHours = intval($_POST['operating_hours'] ?? 0);
    $fireFlowRequired = floatval($_POST['fire_flow_required'] ?? 0);
    $fireDuration = intval($_POST['fire_duration'] ?? 0);
    $emergencyStorage = isset($_POST['emergency_storage']) ? true : false;
    $emergencyDays = intval($_POST['emergency_days'] ?? 0);
    $storageEfficiency = floatval($_POST['storage_efficiency'] ?? 0);
    $tankType = $_POST['tank_type'] ?? '';
    $includeStandby = isset($_POST['include_standby']) ? true : false;
    
    // Validate inputs
    if (empty($buildingType)) $errors[] = "Building type is required";
    if ($totalOccupancy <= 0) $errors[] = "Total occupancy must be greater than 0";
    if ($dailyDemandPerPerson <= 0) $errors[] = "Daily demand per person must be greater than 0";
    if ($operatingHours <= 0) $errors[] = "Operating hours must be greater than 0";
    if ($fireFlowRequired < 0) $errors[] = "Fire flow cannot be negative";
    if ($fireDuration <= 0 && $fireFlowRequired > 0) $errors[] = "Fire duration is required when fire flow is specified";
    if ($emergencyStorage && $emergencyDays <= 0) $errors[] = "Emergency days must be greater than 0";
    if ($storageEfficiency <= 0 || $storageEfficiency > 1) $errors[] = "Storage efficiency must be between 0 and 1";
    if (empty($tankType)) $errors[] = "Tank type is required";
    
    if (empty($errors)) {
        // Calculate water tank sizing requirements
        $results = calculateWaterTankSizing($buildingType, $totalOccupancy, $dailyDemandPerPerson, 
                                          $operatingHours, $fireFlowRequired, $fireDuration, 
                                          $emergencyStorage, $emergencyDays, $storageEfficiency, 
                                          $tankType, $includeStandby);
    }
}

function calculateWaterTankSizing($buildingType, $totalOccupancy, $dailyDemandPerPerson, $operatingHours, $fireFlowRequired, $fireDuration, $emergencyStorage, $emergencyDays, $storageEfficiency, $tankType, $includeStandby) {
    
    // Building type factors for water consumption
    $buildingFactors = [
        'residential' => 1.0,
        'commercial' => 1.2,
        'office' => 1.1,
        'industrial' => 1.5,
        'hospital' => 1.8,
        'school' => 1.3,
        'hotel' => 1.4,
        'retail' => 1.1,
        'apartment' => 1.2,
        'warehouse' => 0.8
    ];
    
    // Peak demand factors
    $peakFactors = [
        'residential' => 2.5,
        'commercial' => 2.0,
        'office' => 1.8,
        'industrial' => 1.5,
        'hospital' => 3.0,
        'school' => 2.2,
        'hotel' => 2.3,
        'retail' => 2.1,
        'apartment' => 2.4,
        'warehouse' => 1.2
    ];
    
    $buildingFactor = $buildingFactors[$buildingType] ?? 1.0;
    $peakFactor = $peakFactors[$buildingType] ?? 2.0;
    
    // Domestic water storage calculations
    $dailyDemand = $totalOccupancy * $dailyDemandPerPerson * $buildingFactor; // gallons per day
    $peakHourDemand = ($dailyDemand / $operatingHours) * $peakFactor; // gallons per hour
    $peakMinuteDemand = $peakHourDemand / 60; // gallons per minute
    
    // Fire protection storage
    $fireStorage = 0;
    if ($fireFlowRequired > 0 && $fireDuration > 0) {
        $fireStorage = $fireFlowRequired * $fireDuration; // gallons
    }
    
    // Emergency storage
    $emergencyDemand = 0;
    if ($emergencyStorage) {
        $emergencyDemand = $dailyDemand * $emergencyDays; // gallons
    }
    
    // Standby storage (additional 10-25% for operational reliability)
    $standbyDemand = 0;
    if ($includeStandby) {
        $standbyDemand = $dailyDemand * 0.15; // 15% standby storage
    }
    
    // Total storage requirement
    $totalStorage = ($dailyDemand + $fireStorage + $emergencyDemand + $standbyDemand) / $storageEfficiency;
    
    // Tank sizing calculations
    $tankDimensions = calculateTankDimensions($totalStorage, $tankType);
    $tankVolume = calculateTankVolume($tankDimensions, $tankType);
    
    // Pressure requirements
    $pressureRequirements = calculatePressureRequirements($buildingType, $totalOccupancy);
    
    // Booster pump requirements
    $boosterPump = calculateBoosterPump($peakMinuteDemand, $pressureRequirements['required_pressure'], $tankType);
    
    // Cost estimation
    $tankCost = calculateTankCost($tankVolume, $tankType, $buildingType);
    $pumpCost = calculatePumpCost($boosterPump['capacity'], $boosterPump['head']);
    $pipingCost = calculatePipingCost($totalStorage, $buildingType);
    $installationCost = ($tankCost + $pumpCost + $pipingCost) * 0.3; // 30% installation cost
    $totalCost = $tankCost + $pumpCost + $pipingCost + $installationCost;
    
    // Water quality considerations
    $waterQuality = assessWaterQuality($buildingType, $totalStorage);
    
    // Regulatory compliance
    $complianceCheck = checkTankCompliance($buildingType, $tankType, $totalStorage, $fireStorage);
    
    // Maintenance requirements
    $maintenanceSchedule = generateMaintenanceSchedule($tankType, $buildingType);
    
    $results = [
        'daily_demand' => round($dailyDemand, 2),
        'peak_hour_demand' => round($peakHourDemand, 2),
        'peak_minute_demand' => round($peakMinuteDemand, 2),
        'fire_storage' => round($fireStorage, 2),
        'emergency_demand' => round($emergencyDemand, 2),
        'standby_demand' => round($standbyDemand, 2),
        'total_storage' => round($totalStorage, 2),
        'tank_dimensions' => $tankDimensions,
        'tank_volume' => round($tankVolume, 2),
        'pressure_requirements' => $pressureRequirements,
        'booster_pump' => $boosterPump,
        'tank_cost' => round($tankCost, 2),
        'pump_cost' => round($pumpCost, 2),
        'piping_cost' => round($pipingCost, 2),
        'installation_cost' => round($installationCost, 2),
        'total_cost' => round($totalCost, 2),
        'water_quality' => $waterQuality,
        'compliance_check' => $complianceCheck,
        'maintenance_schedule' => $maintenanceSchedule,
        'building_factor' => $buildingFactor,
        'peak_factor' => $peakFactor,
        'storage_efficiency' => $storageEfficiency
    ];
    
    return $results;
}

function calculateTankDimensions($totalStorage, $tankType) {
    $dimensions = [];
    
    // Tank shape factors
    $shapeFactors = [
        'cylindrical_vertical' => ['diameter_factor' => 0.707, 'height_factor' => 2.0],
        'cylindrical_horizontal' => ['diameter_factor' => 1.0, 'height_factor' => 1.5],
        'rectangular' => ['length_factor' => 2.0, 'width_factor' => 1.0, 'height_factor' => 1.5],
        'spherical' => ['diameter_factor' => 0.806]
    ];
    
    $shapeFactor = $shapeFactors[$tankType] ?? $shapeFactors['cylindrical_vertical'];
    
    if (strpos($tankType, 'cylindrical') !== false) {
        // Cylindrical tank calculations
        $diameter = pow(($totalStorage * 4) / (pi() * $shapeFactor['height_factor'] * 7.48052), 1/3); // feet
        $height = $diameter * ($shapeFactor['height_factor'] / $shapeFactor['diameter_factor']);
        
        $dimensions = [
            'diameter' => round($diameter, 2),
            'height' => round($height, 2),
            'length' => null,
            'width' => null,
            'surface_area' => round(pi() * $diameter * $height + 2 * pi() * pow($diameter/2, 2), 2)
        ];
    } elseif ($tankType === 'rectangular') {
        // Rectangular tank calculations
        $length = pow(($totalStorage * 6) / (7.48052 * $shapeFactor['height_factor']), 1/3);
        $width = $length / $shapeFactor['width_factor'];
        $height = $length / $shapeFactor['height_factor'];
        
        $dimensions = [
            'diameter' => null,
            'height' => round($height, 2),
            'length' => round($length, 2),
            'width' => round($width, 2),
            'surface_area' => round((2 * $length * $width) + (2 * $length * $height) + (2 * $width * $height), 2)
        ];
    } elseif ($tankType === 'spherical') {
        // Spherical tank calculations
        $diameter = pow(($totalStorage * 6) / (pi() * 7.48052 * $shapeFactor['diameter_factor']), 1/3);
        
        $dimensions = [
            'diameter' => round($diameter, 2),
            'height' => round($diameter, 2),
            'length' => round($diameter, 2),
            'width' => round($diameter, 2),
            'surface_area' => round(pi() * pow($diameter, 2), 2)
        ];
    }
    
    return $dimensions;
}

function calculateTankVolume($dimensions, $tankType) {
    if (strpos($tankType, 'cylindrical') !== false && $dimensions['diameter']) {
        // Cylindrical volume
        $radius = $dimensions['diameter'] / 2;
        return round(pi() * pow($radius, 2) * $dimensions['height'] * 7.48052, 2); // gallons
    } elseif ($tankType === 'rectangular') {
        // Rectangular volume
        return round($dimensions['length'] * $dimensions['width'] * $dimensions['height'] * 7.48052, 2);
    } elseif ($tankType === 'spherical') {
        // Spherical volume
        $radius = $dimensions['diameter'] / 2;
        return round((4/3) * pi() * pow($radius, 3) * 7.48052, 2);
    }
    
    return 0;
}

function calculatePressureRequirements($buildingType, $totalOccupancy) {
    // Minimum pressure requirements by building type
    $basePressure = [
        'residential' => 30,
        'commercial' => 40,
        'office' => 35,
        'industrial' => 45,
        'hospital' => 50,
        'school' => 35,
        'hotel' => 40,
        'retail' => 35,
        'apartment' => 30,
        'warehouse' => 25
    ];
    
    // Additional pressure for high-rise buildings
    $stories = max(1, $totalOccupancy / 100); // Rough estimate
    $pressurePerStory = 4; // psi per story
    $additionalPressure = ($stories - 1) * $pressurePerStory;
    
    $requiredPressure = ($basePressure[$buildingType] ?? 35) + $additionalPressure;
    $recommendedPressure = $requiredPressure + 10; // 10 psi safety margin
    $maximumPressure = min(100, $recommendedPressure + 20); // Max 100 psi
    
    return [
        'minimum_pressure' => $requiredPressure,
        'recommended_pressure' => $recommendedPressure,
        'maximum_pressure' => $maximumPressure,
        'stories' => $stories
    ];
}

function calculateBoosterPump($peakFlowRate, $requiredPressure, $tankType) {
    // Pump sizing based on flow and pressure requirements
    $pumpEfficiency = 0.75; // 75% efficiency
    $safetyFactor = 1.2; // 20% safety factor
    
    $requiredHp = ($peakFlowRate * $requiredPressure * 0.000583) / $pumpEfficiency; // Simplified pump power calculation
    $motorHp = ceil($requiredHp * $safetyFactor); // Round up to standard motor sizes
    
    return [
        'capacity' => round($peakFlowRate * 1.1, 2), // 10% flow safety factor
        'head' => round($requiredPressure * 2.31, 2), // Convert psi to feet of head
        'power' => ceil($motorHp / 0.746), // Convert HP to kW
        'efficiency' => $pumpEfficiency * 100
    ];
}

function calculateTankCost($tankVolume, $tankType, $buildingType) {
    // Base costs per gallon by tank type
    $baseCosts = [
        'cylindrical_vertical' => 2.50,
        'cylindrical_horizontal' => 3.00,
        'rectangular' => 2.20,
        'spherical' => 4.00
    ];
    
    // Material multipliers
    $materialMultipliers = [
        'residential' => 1.0,
        'commercial' => 1.2,
        'office' => 1.1,
        'industrial' => 1.5,
        'hospital' => 1.8,
        'school' => 1.2,
        'hotel' => 1.3,
        'retail' => 1.1,
        'apartment' => 1.1,
        'warehouse' => 1.0
    ];
    
    $baseCost = $baseCosts[$tankType] ?? 2.50;
    $multiplier = $materialMultipliers[$buildingType] ?? 1.0;
    
    // Volume-based scaling (larger tanks have lower cost per gallon)
    $volumeFactor = min(1.0, 100000 / $tankVolume); // Scale down for very large tanks
    
    return $tankVolume * $baseCost * $multiplier * $volumeFactor;
}

function calculatePumpCost($capacity, $head) {
    // Base pump cost estimation
    $baseCost = 5000; // Base cost for small pump
    $capacityFactor = sqrt($capacity / 1000); // Scale with flow rate
    $headFactor = sqrt($head / 100); // Scale with head
    
    return $baseCost * $capacityFactor * $headFactor;
}

function calculatePipingCost($totalStorage, $buildingType) {
    // Piping cost estimation
    $pipeLength = sqrt($totalStorage) * 5; // Approximate pipe length
    $costPerFoot = 50; // Cost per linear foot including installation
    $buildingFactor = [
        'industrial' => 1.3,
        'hospital' => 1.4,
        'commercial' => 1.2,
        'office' => 1.1,
        'residential' => 1.0,
        'warehouse' => 0.9
    ][$buildingType] ?? 1.0;
    
    return $pipeLength * $costPerFoot * $buildingFactor;
}

function assessWaterQuality($buildingType, $totalStorage) {
    $qualityFactors = [];
    
    // Building type specific requirements
    $requirements = [
        'hospital' => ['Disinfection required', 'Water quality monitoring essential', 'Backup systems critical'],
        'food_service' => ['Sanitary quality critical', 'Regular testing required', 'Chlorination recommended'],
        'laboratory' => ['High purity water systems', 'Particulate filtration', 'UV sterilization'],
        'hotel' => ['Aesthetic quality important', 'Temperature control', 'Consistent pressure']
    ];
    
    // Storage tank considerations
    if ($totalStorage > 50000) {
        $qualityFactors[] = "Large storage capacity requires active circulation to prevent stagnation";
        $qualityFactors[] = "Consider tank mixing systems for water quality maintenance";
    }
    
    if (in_array($buildingType, ['hospital', 'laboratory'])) {
        $qualityFactors[] = "Water quality monitoring systems recommended";
        $qualityFactors[] = "Backup power for treatment systems required";
    }
    
    $qualityFactors[] = "Annual tank cleaning and inspection required";
    $qualityFactors[] = "Water temperature monitoring to prevent Legionella growth";
    
    return $qualityFactors;
}

function checkTankCompliance($buildingType, $tankType, $totalStorage, $fireStorage) {
    $compliance = [];
    
    // Fire protection requirements
    if ($fireStorage > 0) {
        if ($fireStorage < 50000) {
            $compliance[] = "⚠ Fire storage capacity may be below NFPA recommendations for large buildings";
        } else {
            $compliance[] = "✓ Fire storage capacity meets typical NFPA requirements";
        }
    }
    
    // Health department requirements
    if ($buildingType === 'hospital' || $buildingType === 'food_service') {
        if ($totalStorage > 10000) {
            $compliance[] = "⚠ Large storage requires enhanced monitoring per health department regulations";
        } else {
            $compliance[] = "✓ Storage capacity within standard health department guidelines";
        }
    }
    
    // Structural requirements
    $compliance[] = "Verify tank structural design for seismic and wind loads per local codes";
    $compliance[] = "Confirm accessibility for maintenance per OSHA requirements";
    
    // Environmental compliance
    $compliance[] = "Check secondary containment requirements for industrial applications";
    $compliance[] = "Verify stormwater management requirements for outdoor tanks";
    
    return $compliance;
}

function generateMaintenanceSchedule($tankType, $buildingType) {
    $schedule = [];
    
    // Daily inspections
    $schedule[] = [
        'frequency' => 'Daily',
        'task' => 'Visual inspection of tank exterior and access points',
        'priority' => 'High'
    ];
    
    // Weekly inspections
    $schedule[] = [
        'frequency' => 'Weekly',
        'task' => 'Check water level and pressure systems',
        'priority' => 'High'
    ];
    
    // Monthly inspections
    $schedule[] = [
        'frequency' => 'Monthly',
        'task' => 'Test alarm and control systems',
        'priority' => 'Medium'
    ];
    
    // Quarterly maintenance
    $schedule[] = [
        'frequency' => 'Quarterly',
        'task' => 'Inspect and clean strainers and filters',
        'priority' => 'Medium'
    ];
    
    // Annual maintenance
    $schedule[] = [
        'frequency' => 'Annual',
        'task' => 'Complete tank cleaning and inspection',
        'priority' => 'High'
    ];
    
    if (in_array($buildingType, ['hospital', 'food_service'])) {
        $schedule[] = [
            'frequency' => 'Quarterly',
            'task' => 'Water quality testing and monitoring',
            'priority' => 'High'
        ];
    }
    
    return $schedule;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i class="fas fa-tint"></i> Water Tank Sizing Calculator</h1>
                        <p class="lead">Comprehensive water storage tank design with capacity, pressure, and cost analysis</p>
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
                    <h5><i class="fas fa-calculator"></i> Tank Parameters</h5>
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
                                <option value="apartment" <?= ($_POST['building_type'] ?? '') === 'apartment' ? 'selected' : '' ?>>Apartment</option>
                                <option value="warehouse" <?= ($_POST['building_type'] ?? '') === 'warehouse' ? 'selected' : '' ?>>Warehouse</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="total_occupancy" class="form-label">Total Occupancy</label>
                            <input type="number" class="form-control" id="total_occupancy" name="total_occupancy" 
                                   value="<?= htmlspecialchars($_POST['total_occupancy'] ?? '') ?>" required min="1">
                        </div>

                        <div class="mb-3">
                            <label for="daily_demand_per_person" class="form-label">Daily Demand per Person (gallons)</label>
                            <input type="number" step="0.1" class="form-control" id="daily_demand_per_person" name="daily_demand_per_person" 
                                   value="<?= htmlspecialchars($_POST['daily_demand_per_person'] ?? '50') ?>" required min="0.1">
                        </div>

                        <div class="mb-3">
                            <label for="operating_hours" class="form-label">Operating Hours per Day</label>
                            <input type="number" class="form-control" id="operating_hours" name="operating_hours" 
                                   value="<?= htmlspecialchars($_POST['operating_hours'] ?? '16') ?>" required min="1" max="24">
                        </div>

                        <div class="mb-3">
                            <label for="tank_type" class="form-label">Tank Type</label>
                            <select class="form-select" id="tank_type" name="tank_type" required>
                                <option value="">Select Tank Type</option>
                                <option value="cylindrical_vertical" <?= ($_POST['tank_type'] ?? '') === 'cylindrical_vertical' ? 'selected' : '' ?>>Cylindrical Vertical</option>
                                <option value="cylindrical_horizontal" <?= ($_POST['tank_type'] ?? '') === 'cylindrical_horizontal' ? 'selected' : '' ?>>Cylindrical Horizontal</option>
                                <option value="rectangular" <?= ($_POST['tank_type'] ?? '') === 'rectangular' ? 'selected' : '' ?>>Rectangular</option>
                                <option value="spherical" <?= ($_POST['tank_type'] ?? '') === 'spherical' ? 'selected' : '' ?>>Spherical</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="storage_efficiency" class="form-label">Storage Efficiency</label>
                            <input type="number" step="0.1" class="form-control" id="storage_efficiency" name="storage_efficiency" 
                                   value="<?= htmlspecialchars($_POST['storage_efficiency'] ?? '0.85') ?>" required min="0.1" max="1.0">
                            <div class="form-text">0.1 = 10% efficiency, 1.0 = 100% efficiency</div>
                        </div>

                        <div class="mb-4 border-top pt-3">
                            <h6><i class="fas fa-fire-extinguisher"></i> Fire Protection</h6>
                            <div class="mb-3">
                                <label for="fire_flow_required" class="form-label">Fire Flow Required (GPM)</label>
                                <input type="number" class="form-control" id="fire_flow_required" name="fire_flow_required" 
                                       value="<?= htmlspecialchars($_POST['fire_flow_required'] ?? '') ?>" min="0">
                            </div>
                            <div class="mb-3">
                                <label for="fire_duration" class="form-label">Fire Duration (minutes)</label>
                                <input type="number" class="form-control" id="fire_duration" name="fire_duration" 
                                       value="<?= htmlspecialchars($_POST['fire_duration'] ?? '') ?>" min="0">
                            </div>
                        </div>

                        <div class="mb-4 border-top pt-3">
                            <h6><i class="fas fa-exclamation-triangle"></i> Emergency Storage</h6>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="emergency_storage" name="emergency_storage" 
                                           <?= isset($_POST['emergency_storage']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="emergency_storage">
                                        Include Emergency Storage
                                    </label>
                                </div>
                            </div>
                            <?php if (isset($_POST['emergency_storage']) || !empty($_POST)): ?>
                                <div class="mb-3">
                                    <label for="emergency_days" class="form-label">Emergency Days</label>
                                    <input type="number" class="form-control" id="emergency_days" name="emergency_days" 
                                           value="<?= htmlspecialchars($_POST['emergency_days'] ?? '3') ?>" min="1">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_standby" name="include_standby" 
                                       <?= isset($_POST['include_standby']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="include_standby">
                                    Include Standby Storage (15%)
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-calculator"></i> Calculate Tank Sizing
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <?php if (!empty($results)): ?>
                <div class="row">
                    <!-- Demand Analysis -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-tint"></i> Water Demand</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= number_format($results['daily_demand']) ?></div>
                                            <div class="metric-label">Gallons</div>
                                            <small class="text-muted">Daily Demand</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= round($results['peak_minute_demand'], 1) ?></div>
                                            <div class="metric-label">GPM</div>
                                            <small class="text-muted">Peak Flow</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Storage Requirements -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-archive"></i> Storage Requirements</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= number_format($results['total_storage']) ?></div>
                                            <div class="metric-label">Gallons</div>
                                            <small class="text-muted">Total Storage</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= number_format($results['fire_storage']) ?></div>
                                            <div class="metric-label">Gallons</div>
                                            <small class="text-muted">Fire Storage</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tank Dimensions -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-ruler-combined"></i> Tank Dimensions</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['tank_dimensions']['diameter'] ?: $results['tank_dimensions']['length'] ?></div>
                                            <div class="metric-label">Feet</div>
                                            <small class="text-muted"><?= $results['tank_dimensions']['diameter'] ? 'Diameter' : 'Length' ?></small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['tank_dimensions']['height'] ?></div>
                                            <div class="metric-label">Feet</div>
                                            <small class="text-muted">Height/Depth</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pressure Requirements -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-tachometer-alt"></i> Pressure Requirements</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['pressure_requirements']['recommended_pressure'] ?></div>
                                            <div class="metric-label">PSI</div>
                                            <small class="text-muted">Recommended</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['pressure_requirements']['stories'] ?></div>
                                            <div class="metric-label">Stories</div>
                                            <small class="text-muted">Building Height</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booster Pump -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-cog"></i> Booster Pump</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['booster_pump']['capacity'] ?></div>
                                            <div class="metric-label">GPM</div>
                                            <small class="text-muted">Capacity</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['booster_pump']['power'] ?></div>
                                            <div class="metric-label">kW</div>
                                            <small class="text-muted">Power</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Estimation -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-dollar-sign"></i> Cost Estimation</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value">$<?= number_format($results['tank_cost']) ?></div>
                                            <div class="metric-label">Tank</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value">$<?= number_format($results['total_cost']) ?></div>
                                            <div class="metric-label">Total</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tank Details Table -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-info-circle"></i> Tank Specifications</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Parameter</th>
                                                <th>Value</th>
                                                <th>Units</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($results['tank_dimensions']['diameter']): ?>
                                                <tr>
                                                    <td>Diameter</td>
                                                    <td><?= $results['tank_dimensions']['diameter'] ?></td>
                                                    <td>Feet</td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if ($results['tank_dimensions']['length']): ?>
                                                <tr>
                                                    <td>Length</td>
                                                    <td><?= $results['tank_dimensions']['length'] ?></td>
                                                    <td>Feet</td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if ($results['tank_dimensions']['width']): ?>
                                                <tr>
                                                    <td>Width</td>
                                                    <td><?= $results['tank_dimensions']['width'] ?></td>
                                                    <td>Feet</td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td>Height/Depth</td>
                                                <td><?= $results['tank_dimensions']['height'] ?></td>
                                                <td>Feet</td>
                                            </tr>
                                            <tr>
                                                <td>Surface Area</td>
                                                <td><?= $results['tank_dimensions']['surface_area'] ?></td>
                                                <td>Square Feet</td>
                                            </tr>
                                            <tr>
                                                <td>Volume Capacity</td>
                                                <td><?= number_format($results['tank_volume']) ?></td>
                                                <td>Gallons</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Water Quality -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-flask"></i> Water Quality Considerations</h6>
                            </div>
                            <div class="card-body">
                                <div class="quality-status">
                                    <?php foreach ($results['water_quality'] as $quality): ?>
                                        <div class="quality-item">
                                            <i class="fas fa-check-circle"></i>
                                            <?= htmlspecialchars($quality) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compliance -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-clipboard-check"></i> Regulatory Compliance</h6>
                            </div>
                            <div class="card-body">
                                <div class="compliance-status">
                                    <?php foreach ($results['compliance_check'] as $compliance): ?>
                                        <div class="compliance-item <?= strpos($compliance, '⚠') !== false ? 'warning' : 'success' ?>">
                                            <i class="fas <?= strpos($compliance, '⚠') !== false ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
                                            <?= htmlspecialchars($compliance) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Schedule -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-calendar-alt"></i> Maintenance Schedule</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Frequency</th>
                                                <th>Task</th>
                                                <th>Priority</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($results['maintenance_schedule'] as $task): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($task['frequency']) ?></td>
                                                    <td><?= htmlspecialchars($task['task']) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $task['priority'] === 'High' ? 'danger' : 'warning' ?>">
                                                            <?= htmlspecialchars($task['priority']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Design Recommendations -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-lightbulb"></i> Design Recommendations</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Installation Considerations</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Install level monitoring systems</li>
                                            <li><i class="fas fa-check text-success"></i> Include overflow and drain connections</li>
                                            <li><i class="fas fa-check text-success"></i> Provide access for maintenance vehicles</li>
                                            <li><i class="fas fa-check text-success"></i> Consider thermal insulation requirements</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Safety Features</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Install emergency shutoff valves</li>
                                            <li><i class="fas fa-check text-success"></i> Include level alarms and controls</li>
                                            <li><i class="fas fa-check text-success"></i> Provide fall protection for elevated tanks</li>
                                            <li><i class="fas fa-check text-success"></i> Install lightning protection if required</li>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                                <div class="alert alert-info">
                                    <strong><i class="fas fa-info-circle"></i> Professional Review Required:</strong>
                                    This calculation provides preliminary sizing parameters. All water tank designs must be reviewed and approved by licensed mechanical engineers and must comply with local building codes, health department regulations, and fire protection requirements.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-tint fa-3x text-muted mb-3"></i>
                        <h4>Water Tank Sizing Calculator</h4>
                        <p class="text-muted">Design optimal water storage systems with comprehensive capacity calculations, pressure analysis, and cost estimation for domestic, fire protection, and emergency storage requirements.</p>
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-calculator text-primary"></i>
                                    <h6>Capacity Analysis</h6>
                                    <small>Calculate domestic, fire, and emergency storage needs</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-ruler-combined text-success"></i>
                                    <h6>Optimal Sizing</h6>
                                    <small>Determine tank dimensions and specifications</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-tachometer-alt text-warning"></i>
                                    <h6>Pressure Systems</h6>
                                    <small>Booster pump and pressure requirements</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-dollar-sign text-info"></i>
                                    <h6>Cost Planning</h6>
                                    <small>Comprehensive cost estimation and budgeting</small>
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

.quality-status,
.compliance-status {
    max-height: 300px;
    overflow-y: auto;
}

.quality-item,
.compliance-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: flex-start;
}

.quality-item:last-child,
.compliance-item:last-child {
    border-bottom: none;
}

.compliance-item.success {
    color: #28a745;
}

.compliance-item.warning {
    color: #ffc107;
}

.compliance-item i {
    margin-right: 0.75rem;
    width: 1.2rem;
    margin-top: 0.2rem;
}

.quality-item i {
    margin-right: 0.75rem;
    width: 1.2rem;
    margin-top: 0.2rem;
    color: var(--primary-color);
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
