<?php
require_once '../../../includes/config.php';
require_once '../../../includes/Database.php';
require_once '../../../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$pageTitle = "Storm Water Management Calculator - MEP Suite";
include '../../../includes/header.php';

// Handle form submission
$results = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteArea = floatval($_POST['site_area'] ?? 0);
    $imperviousArea = floatval($_POST['impervious_area'] ?? 0);
    $perviousArea = floatval($_POST['pervious_area'] ?? 0);
    $designStorm = $_POST['design_storm'] ?? '';
    $rainfallIntensity = floatval($_POST['rainfall_intensity'] ?? 0);
    $timeOfConcentration = floatval($_POST['time_of_concentration'] ?? 0);
    $detentionRequired = isset($_POST['detention_required']) ? true : false;
    $detentionVolume = floatval($_POST['detention_volume'] ?? 0);
    $outletType = $_POST['outlet_type'] ?? '';
    $drainageCoefficient = floatval($_POST['drainage_coefficient'] ?? 0);
    
    // Validate inputs
    if ($siteArea <= 0) $errors[] = "Site area must be greater than 0";
    if ($imperviousArea < 0) $errors[] = "Impervious area cannot be negative";
    if ($perviousArea < 0) $errors[] = "Pervious area cannot be negative";
    if ($imperviousArea + $perviousArea > $siteArea) $errors[] = "Impervious and pervious areas cannot exceed total site area";
    if (empty($designStorm)) $errors[] = "Design storm return period is required";
    if ($rainfallIntensity <= 0) $errors[] = "Rainfall intensity must be greater than 0";
    if ($timeOfConcentration <= 0) $errors[] = "Time of concentration must be greater than 0";
    if (empty($outletType)) $errors[] = "Outlet type is required";
    if ($drainageCoefficient <= 0) $errors[] = "Drainage coefficient must be greater than 0";
    
    if (empty($errors)) {
        // Calculate storm water management requirements
        $results = calculateStormWater($siteArea, $imperviousArea, $perviousArea, $designStorm, 
                                     $rainfallIntensity, $timeOfConcentration, $detentionRequired, 
                                     $detentionVolume, $outletType, $drainageCoefficient);
    }
}

function calculateStormWater($siteArea, $imperviousArea, $perviousArea, $designStorm, $rainfallIntensity, $timeOfConcentration, $detentionRequired, $detentionVolume, $outletType, $drainageCoefficient) {
    $results = [];
    
    // Design storm return periods and factors
    $stormFactors = [
        '1-year' => 1.0,
        '2-year' => 1.2,
        '5-year' => 1.5,
        '10-year' => 1.8,
        '25-year' => 2.2,
        '50-year' => 2.6,
        '100-year' => 3.0
    ];
    
    $stormFactor = $stormFactors[$designStorm] ?? 1.5;
    
    // Runoff calculations using Rational Method
    $imperviousRunoff = $imperviousArea * 0.95 * $rainfallIntensity; // C = 0.95 for impervious
    $perviousRunoff = $perviousArea * 0.30 * $rainfallIntensity; // C = 0.30 for pervious
    $totalRunoffRate = ($imperviousRunoff + $perviousRunoff) / 12; // Convert to CFS
    
    // Peak discharge calculations
    $peakDischarge = $totalRunoffRate * $stormFactor;
    $adjustedIntensity = $rainfallIntensity * ($timeOfConcentration / 10); // Simplified adjustment
    
    // Storage requirements
    $totalRainfall = $rainfallIntensity * $stormFactor; // inches over storm duration
    $directRunoff = ($imperviousArea * 0.95 + $perviousArea * 0.30) / 12; // cubic feet per inch of rainfall
    $storageVolume = $totalRainfall * $directRunoff * 0.5; // 50% retention factor
    
    // Detention pond sizing (if required)
    $detentionArea = 0;
    $detentionDepth = 0;
    $sideSlopes = 3; // 3:1 slopes
    if ($detentionRequired && $storageVolume > 0) {
        $detentionArea = sqrt($storageVolume / 4); // Simplified pond area calculation
        $detentionDepth = $storageVolume / $detentionArea; // Assuming rectangular pond
        $detentionArea = sqrt($storageVolume / ($detentionDepth + ($sideSlopes * $detentionDepth))); // Adjusted for slopes
    }
    
    // Outlet design
    $outletCapacity = 0;
    $outletSize = 0;
    switch ($outletType) {
        case 'culvert':
            $outletCapacity = calculateCulvertCapacity($peakDischarge);
            $outletSize = calculateCulvertSize($peakDischarge);
            break;
        case 'weir':
            $outletCapacity = calculateWeirCapacity($peakDischarge);
            $outletSize = calculateWeirLength($peakDischarge);
            break;
        case 'orifice':
            $outletCapacity = calculateOrificeCapacity($peakDischarge);
            $outletSize = calculateOrificeDiameter($peakDischarge);
            break;
    }
    
    // Channel design
    $channelCapacity = calculateChannelCapacity($peakDischarge);
    $channelDimensions = calculateChannelDimensions($peakDischarge);
    
    // Inlet design
    $inletSpacing = calculateInletSpacing($peakDischarge, $siteArea);
    $inletCount = ceil($siteArea / ($inletSpacing * 1000)); // Approximate spacing
    
    // Cost estimation
    $earthworkCost = calculateEarthworkCost($storageVolume);
    $outletCost = calculateOutletCost($outletType, $outletSize);
    $inletCost = calculateInletCost($inletCount);
    $channelCost = calculateChannelCost($channelDimensions, $siteArea);
    $totalCost = $earthworkCost + $outletCost + $inletCost + $channelCost;
    
    // Environmental considerations
    $environmentalImpact = assessEnvironmentalImpact($siteArea, $imperviousArea, $perviousArea);
    
    // Regulatory compliance
    $complianceCheck = checkRegulatoryCompliance($designStorm, $detentionRequired, $storageVolume, $peakDischarge);
    
    $results = [
        'peak_discharge' => round($peakDischarge, 2),
        'total_runoff_rate' => round($totalRunoffRate, 2),
        'storage_volume' => round($storageVolume, 2),
        'detention_area' => round($detentionArea, 2),
        'detention_depth' => round($detentionDepth, 2),
        'outlet_capacity' => round($outletCapacity, 2),
        'outlet_size' => round($outletSize, 2),
        'channel_capacity' => round($channelCapacity, 2),
        'channel_dimensions' => $channelDimensions,
        'inlet_spacing' => round($inletSpacing, 2),
        'inlet_count' => $inletCount,
        'earthwork_cost' => round($earthworkCost, 2),
        'outlet_cost' => round($outletCost, 2),
        'inlet_cost' => round($inletCost, 2),
        'channel_cost' => round($channelCost, 2),
        'total_cost' => round($totalCost, 2),
        'environmental_impact' => $environmentalImpact,
        'compliance_check' => $complianceCheck,
        'storm_factor' => $stormFactor,
        'total_rainfall' => round($totalRainfall, 2),
        'adjusted_intensity' => round($adjustedIntensity, 2),
        'drainage_coefficient' => $drainageCoefficient
    ];
    
    return $results;
}

function calculateCulvertCapacity($peakDischarge) {
    // Simplified culvert capacity calculation
    return $peakDischarge * 1.2; // 20% safety factor
}

function calculateCulvertSize($peakDischarge) {
    // Simplified culvert sizing (diameter in feet)
    return sqrt($peakDischarge / (pi() * 3)) * 2; // Rough approximation
}

function calculateWeirCapacity($peakDischarge) {
    // Weir capacity calculation
    return $peakDischarge * 1.1; // 10% safety factor
}

function calculateWeirLength($peakDischarge) {
    // Weir length calculation (feet)
    return sqrt($peakDischarge * 2);
}

function calculateOrificeCapacity($peakDischarge) {
    // Orifice capacity calculation
    return $peakDischarge * 1.3; // 30% safety factor
}

function calculateOrificeDiameter($peakDischarge) {
    // Orifice diameter calculation (inches)
    return sqrt($peakDischarge * 4) * 2;
}

function calculateChannelCapacity($peakDischarge) {
    // Channel capacity (Manning's equation simplified)
    return $peakDischarge * 0.8; // Conservative estimate
}

function calculateChannelDimensions($peakDischarge) {
    // Simplified channel design
    $bottomWidth = sqrt($peakDischarge) * 0.5;
    $depth = sqrt($peakDischarge) * 0.3;
    $sideSlope = 2; // 2:1 slopes
    
    return [
        'bottom_width' => round($bottomWidth, 2),
        'depth' => round($depth, 2),
        'side_slope' => $sideSlope,
        'top_width' => round($bottomWidth + (2 * $depth * $sideSlope), 2)
    ];
}

function calculateInletSpacing($peakDischarge, $siteArea) {
    // Inlet spacing based on peak discharge (feet)
    $baseSpacing = 200; // Base spacing
    $spacingAdjustment = $peakDischarge * 10;
    return max(50, $baseSpacing - $spacingAdjustment);
}

function calculateEarthworkCost($storageVolume) {
    // Earthwork cost calculation
    $costPerCuYd = 8.50; // Cost per cubic yard
    return ($storageVolume / 27) * $costPerCuYd; // Convert cubic feet to cubic yards
}

function calculateOutletCost($outletType, $outletSize) {
    // Outlet cost calculation
    $baseCosts = [
        'culvert' => 150,
        'weir' => 200,
        'orifice' => 100
    ];
    
    $baseCost = $baseCosts[$outletType] ?? 150;
    $sizeMultiplier = max(1, $outletSize / 2);
    
    return $baseCost * $sizeMultiplier;
}

function calculateInletCost($inletCount) {
    // Inlet cost calculation
    $costPerInlet = 500; // Cost per inlet structure
    return $inletCount * $costPerInlet;
}

function calculateChannelCost($channelDimensions, $siteArea) {
    // Channel cost calculation
    $channelLength = sqrt($siteArea) * 0.5; // Approximate channel length
    $costPerFoot = 25; // Cost per linear foot
    $sizeMultiplier = ($channelDimensions['depth'] + $channelDimensions['bottom_width']) / 6;
    
    return $channelLength * $costPerFoot * $sizeMultiplier;
}

function assessEnvironmentalImpact($siteArea, $imperviousArea, $perviousArea) {
    $impact = [];
    
    $imperviousPercentage = ($imperviousArea / $siteArea) * 100;
    $impactScore = $imperviousPercentage / 10; // Simple scoring system
    
    if ($impactScore < 1) {
        $impact[] = "✓ Low environmental impact - adequate green space provided";
    } elseif ($impactScore < 2) {
        $impact[] = "⚠ Moderate environmental impact - consider green infrastructure";
    } else {
        $impact[] = "⚠ High environmental impact - extensive green infrastructure required";
    }
    
    $impact[] = "Water quality treatment recommended for runoff from impervious surfaces";
    $impact[] = "Consider bioretention or bioswales for enhanced treatment";
    $impact[] = "Native vegetation recommended for stability and habitat";
    
    return $impact;
}

function checkRegulatoryCompliance($designStorm, $detentionRequired, $storageVolume, $peakDischarge) {
    $compliance = [];
    
    // Typical regulatory requirements
    if (in_array($designStorm, ['10-year', '25-year', '50-year'])) {
        $compliance[] = "✓ Design storm selection meets common regulatory requirements";
    } else {
        $compliance[] = "⚠ Consider higher return period for critical facilities";
    }
    
    if ($detentionRequired && $storageVolume > 10000) {
        $compliance[] = "✓ Detention storage meets regulatory volume requirements";
    } elseif ($detentionRequired) {
        $compliance[] = "⚠ Verify detention storage volume with local regulations";
    } else {
        $compliance[] = "ℹ Verify if detention is required based on local stormwater regulations";
    }
    
    if ($peakDischarge < 50) {
        $compliance[] = "✓ Peak discharge within typical municipal limits";
    } else {
        $compliance[] = "⚠ High peak discharge - coordinate with downstream conveyance systems";
    }
    
    $compliance[] = "Verify compliance with local NPDES permit requirements";
    $compliance[] = "Check for post-construction stormwater management requirements";
    
    return $compliance;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i class="fas fa-cloud-rain"></i> Storm Water Management Calculator</h1>
                        <p class="lead">Comprehensive storm water management design with detention, conveyance, and environmental analysis</p>
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
                    <h5><i class="fas fa-calculator"></i> Site Parameters</h5>
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
                            <label for="site_area" class="form-label">Total Site Area (acres)</label>
                            <input type="number" step="0.01" class="form-control" id="site_area" name="site_area" 
                                   value="<?= htmlspecialchars($_POST['site_area'] ?? '') ?>" required min="0.01">
                        </div>

                        <div class="mb-3">
                            <label for="impervious_area" class="form-label">Impervious Area (acres)</label>
                            <input type="number" step="0.01" class="form-control" id="impervious_area" name="impervious_area" 
                                   value="<?= htmlspecialchars($_POST['impervious_area'] ?? '') ?>" min="0">
                        </div>

                        <div class="mb-3">
                            <label for="pervious_area" class="form-label">Pervious Area (acres)</label>
                            <input type="number" step="0.01" class="form-control" id="pervious_area" name="pervious_area" 
                                   value="<?= htmlspecialchars($_POST['pervious_area'] ?? '') ?>" min="0">
                        </div>

                        <div class="mb-3">
                            <label for="design_storm" class="form-label">Design Storm Return Period</label>
                            <select class="form-select" id="design_storm" name="design_storm" required>
                                <option value="">Select Design Storm</option>
                                <option value="1-year" <?= ($_POST['design_storm'] ?? '') === '1-year' ? 'selected' : '' ?>>1-Year Storm</option>
                                <option value="2-year" <?= ($_POST['design_storm'] ?? '') === '2-year' ? 'selected' : '' ?>>2-Year Storm</option>
                                <option value="5-year" <?= ($_POST['design_storm'] ?? '') === '5-year' ? 'selected' : '' ?>>5-Year Storm</option>
                                <option value="10-year" <?= ($_POST['design_storm'] ?? '') === '10-year' ? 'selected' : '' ?>>10-Year Storm</option>
                                <option value="25-year" <?= ($_POST['design_storm'] ?? '') === '25-year' ? 'selected' : '' ?>>25-Year Storm</option>
                                <option value="50-year" <?= ($_POST['design_storm'] ?? '') === '50-year' ? 'selected' : '' ?>>50-Year Storm</option>
                                <option value="100-year" <?= ($_POST['design_storm'] ?? '') === '100-year' ? 'selected' : '' ?>>100-Year Storm</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="rainfall_intensity" class="form-label">Rainfall Intensity (inches/hour)</label>
                            <input type="number" step="0.1" class="form-control" id="rainfall_intensity" name="rainfall_intensity" 
                                   value="<?= htmlspecialchars($_POST['rainfall_intensity'] ?? '') ?>" required min="0.1">
                        </div>

                        <div class="mb-3">
                            <label for="time_of_concentration" class="form-label">Time of Concentration (minutes)</label>
                            <input type="number" step="0.1" class="form-control" id="time_of_concentration" name="time_of_concentration" 
                                   value="<?= htmlspecialchars($_POST['time_of_concentration'] ?? '') ?>" required min="0.1">
                        </div>

                        <div class="mb-3">
                            <label for="drainage_coefficient" class="form-label">Drainage Coefficient (C)</label>
                            <input type="number" step="0.01" class="form-control" id="drainage_coefficient" name="drainage_coefficient" 
                                   value="<?= htmlspecialchars($_POST['drainage_coefficient'] ?? '0.5') ?>" required min="0.01" max="1.0">
                        </div>

                        <div class="mb-3">
                            <label for="outlet_type" class="form-label">Outlet Type</label>
                            <select class="form-select" id="outlet_type" name="outlet_type" required>
                                <option value="">Select Outlet Type</option>
                                <option value="culvert" <?= ($_POST['outlet_type'] ?? '') === 'culvert' ? 'selected' : '' ?>>Culvert</option>
                                <option value="weir" <?= ($_POST['outlet_type'] ?? '') === 'weir' ? 'selected' : '' ?>>Weir</option>
                                <option value="orifice" <?= ($_POST['outlet_type'] ?? '') === 'orifice' ? 'selected' : '' ?>>Orifice</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="detention_required" name="detention_required" 
                                       <?= isset($_POST['detention_required']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="detention_required">
                                    Detention Required
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-calculator"></i> Calculate Storm Water System
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <?php if (!empty($results)): ?>
                <div class="row">
                    <!-- Peak Discharge -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-tachometer-alt"></i> Peak Discharge</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['peak_discharge'] ?></div>
                                            <div class="metric-label">CFS</div>
                                            <small class="text-muted">Peak Discharge</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['total_runoff_rate'] ?></div>
                                            <div class="metric-label">CFS</div>
                                            <small class="text-muted">Total Runoff</small>
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
                                            <div class="metric-value"><?= $results['storage_volume'] ?></div>
                                            <div class="metric-label">Cubic Feet</div>
                                            <small class="text-muted">Storage Volume</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['detention_area'] ?></div>
                                            <div class="metric-label">Acres</div>
                                            <small class="text-muted">Detention Area</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detention Details -->
                    <?php if ($results['detention_area'] > 0): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6><i class="fas fa-swimming-pool"></i> Detention Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="metric">
                                                <div class="metric-value"><?= $results['detention_depth'] ?></div>
                                                <div class="metric-label">Feet</div>
                                                <small class="text-muted">Depth</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="metric">
                                                <div class="metric-value"><?= number_format($results['storage_volume'] / 43560, 2) ?></div>
                                                <div class="metric-label">Acre-Feet</div>
                                                <small class="text-muted">Volume</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Outlet Design -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-pipe"></i> Outlet Design</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['outlet_capacity'] ?></div>
                                            <div class="metric-label">CFS</div>
                                            <small class="text-muted">Capacity</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['outlet_size'] ?></div>
                                            <div class="metric-label"><?= ($_POST['outlet_type'] ?? '') === 'weir' ? 'Feet' : 'Inches' ?></div>
                                            <small class="text-muted">Size</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Channel Design -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-route"></i> Channel Design</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['channel_capacity'] ?></div>
                                            <div class="metric-label">CFS</div>
                                            <small class="text-muted">Capacity</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['channel_dimensions']['bottom_width'] ?></div>
                                            <div class="metric-label">Feet</div>
                                            <small class="text-muted">Bottom Width</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inlet System -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-arrow-alt-circle-down"></i> Inlet System</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['inlet_count'] ?></div>
                                            <div class="metric-label">Count</div>
                                            <small class="text-muted">Inlets</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric">
                                            <div class="metric-value"><?= $results['inlet_spacing'] ?></div>
                                            <div class="metric-label">Feet</div>
                                            <small class="text-muted">Spacing</small>
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
                                            <div class="metric-value">$<?= number_format($results['earthwork_cost']) ?></div>
                                            <div class="metric-label">Earthwork</div>
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

                    <!-- Channel Dimensions -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-ruler-combined"></i> Channel Dimensions</h6>
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
                                            <tr>
                                                <td>Bottom Width</td>
                                                <td><?= $results['channel_dimensions']['bottom_width'] ?></td>
                                                <td>Feet</td>
                                            </tr>
                                            <tr>
                                                <td>Depth</td>
                                                <td><?= $results['channel_dimensions']['depth'] ?></td>
                                                <td>Feet</td>
                                            </tr>
                                            <tr>
                                                <td>Side Slope</td>
                                                <td><?= $results['channel_dimensions']['side_slope'] ?>:1</td>
                                                <td>Ratio</td>
                                            </tr>
                                            <tr>
                                                <td>Top Width</td>
                                                <td><?= $results['channel_dimensions']['top_width'] ?></td>
                                                <td>Feet</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Environmental Impact -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-leaf"></i> Environmental Impact Assessment</h6>
                            </div>
                            <div class="card-body">
                                <div class="environmental-status">
                                    <?php foreach ($results['environmental_impact'] as $impact): ?>
                                        <div class="impact-item">
                                            <i class="fas fa-info-circle"></i>
                                            <?= htmlspecialchars($impact) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Regulatory Compliance -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-clipboard-check"></i> Regulatory Compliance</h6>
                            </div>
                            <div class="card-body">
                                <div class="compliance-status">
                                    <?php foreach ($results['compliance_check'] as $compliance): ?>
                                        <div class="compliance-item <?= strpos($compliance, '⚠') !== false ? 'warning' : (strpos($compliance, 'ℹ') !== false ? 'info' : 'success') ?>">
                                            <i class="fas <?= strpos($compliance, '⚠') !== false ? 'fa-exclamation-triangle' : (strpos($compliance, 'ℹ') !== false ? 'fa-info-circle' : 'fa-check-circle') ?>"></i>
                                            <?= htmlspecialchars($compliance) ?>
                                        </div>
                                    <?php endforeach; ?>
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
                                        <h6>Best Management Practices</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Install forebays for sediment capture</li>
                                            <li><i class="fas fa-check text-success"></i> Use native vegetation for stability</li>
                                            <li><i class="fas fa-check text-success"></i> Include overflow structures for extreme events</li>
                                            <li><i class="fas fa-check text-success"></i> Consider bioretention cells for quality treatment</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Construction Considerations</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Stabilize channels before first major storm</li>
                                            <li><i class="fas fa-check text-success"></i> Install temporary erosion controls during construction</li>
                                            <li><i class="fas fa-check text-success"></i> Provide access for maintenance equipment</li>
                                            <li><i class="fas fa-check text-success"></i> Include inspection and cleaning ports</li>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                                <div class="alert alert-info">
                                    <strong><i class="fas fa-info-circle"></i> Professional Review Required:</strong>
                                    This calculation provides preliminary design parameters. All storm water management designs must be reviewed and approved by licensed civil engineers and must comply with local NPDES permits and stormwater regulations.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-cloud-rain fa-3x text-muted mb-3"></i>
                        <h4>Storm Water Management Calculator</h4>
                        <p class="text-muted">Design comprehensive storm water management systems with detention storage, conveyance design, and environmental protection measures.</p>
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-chart-line text-primary"></i>
                                    <h6>Runoff Analysis</h6>
                                    <small>Calculate peak discharge using Rational Method</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-archive text-success"></i>
                                    <h6>Detention Design</h6>
                                    <small>Size storage facilities for flood control</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-pipe text-warning"></i>
                                    <h6>Conveyance</h6>
                                    <small>Design channels, culverts, and outlets</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="feature">
                                    <i class="fas fa-leaf text-info"></i>
                                    <h6>Environmental</h6>
                                    <small>BMP selection and water quality treatment</small>
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

.environmental-status,
.compliance-status {
    max-height: 400px;
    overflow-y: auto;
}

.impact-item,
.compliance-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: flex-start;
}

.impact-item:last-child,
.compliance-item:last-child {
    border-bottom: none;
}

.compliance-item.success {
    color: #28a745;
}

.compliance-item.warning {
    color: #ffc107;
}

.compliance-item.info {
    color: #17a2b8;
}

.compliance-item i {
    margin-right: 0.75rem;
    width: 1.2rem;
    margin-top: 0.2rem;
}

.impact-item i {
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

<?php include '../../../includes/footer.php'; ?>
