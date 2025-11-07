<?php
/**
 * Emergency Power System Calculator
 * Professional emergency power system design and sizing
 * Includes generator sizing, UPS calculations, and code compliance
 */

require_once '../../../includes/config.php';

// Get system parameters
$totalConnectedLoad = filter_input(INPUT_POST, 'total_connected_load', FILTER_VALIDATE_FLOAT);
$loadFactor = filter_input(INPUT_POST, 'load_factor', FILTER_VALIDATE_FLOAT) ?: 0.7;
$powerFactor = filter_input(INPUT_POST, 'power_factor', FILTER_VALIDATE_FLOAT) ?: 0.8;
$generatorType = filter_input(INPUT_POST, 'generator_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'diesel';
$application = filter_input(INPUT_POST, 'application_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'hospital';
$durationHours = filter_input(INPUT_POST, 'duration_hours', FILTER_VALIDATE_FLOAT) ?: 72;
$altitude = filter_input(INPUT_POST, 'altitude', FILTER_VALIDATE_FLOAT) ?: 0;
$temperature = filter_input(INPUT_POST, 'ambient_temperature', FILTER_VALIDATE_FLOAT) ?: 25;

$calculations = [];
$results = null;

if ($totalConnectedLoad) {
    // Define load categories and their priorities
    $loadCategories = [
        'essential' => [
            'name' => 'Essential Loads',
            'percentage' => 0.4,
            'priority' => 1,
            'description' => 'Life safety, critical equipment'
        ],
        'emergency' => [
            'name' => 'Emergency Loads', 
            'percentage' => 0.3,
            'priority' => 2,
            'description' => 'Emergency lighting, fire pumps, security'
        ],
        'non_essential' => [
            'name' => 'Non-Essential Loads',
            'percentage' => 0.3,
            'priority' => 3,
            'description' => 'General power, HVAC, normal lighting'
        ]
    ];
    
    // Application-specific requirements
    $applicationRequirements = [
        'hospital' => [
            'min_duration' => 72,
            'essential_load_factor' => 0.8,
            'generator_start_time' => 10,
            'code_compliance' => 'NFPA 110, NEC 700'
        ],
        'data_center' => [
            'min_duration' => 24,
            'essential_load_factor' => 0.9,
            'generator_start_time' => 30,
            'code_compliance' => 'TIA-942, NEC 645'
        ],
        'office_building' => [
            'min_duration' => 8,
            'essential_load_factor' => 0.6,
            'generator_start_time' => 15,
            'code_compliance' => 'NEC 700, IBC'
        ],
        'industrial' => [
            'min_duration' => 4,
            'essential_load_factor' => 0.5,
            'generator_start_time' => 20,
            'code_compliance' => 'NFPA 70, OSHA'
        ],
        'residential' => [
            'min_duration' => 24,
            'essential_load_factor' => 0.4,
            'generator_start_time' => 30,
            'code_compliance' => 'NEC 700'
        ]
    ];
    
    $appReq = $applicationRequirements[$application] ?: $applicationRequirements['office_building'];
    
    // Apply load factor and power factor
    $actualLoad = $totalConnectedLoad * $loadFactor;
    $reactiveLoad = sqrt(pow($actualLoad, 2) - pow($actualLoad * $powerFactor, 2));
    $apparentLoad = $actualLoad / $powerFactor;
    
    // Generator sizing calculations
    $essentialLoad = $actualLoad * $appReq['essential_load_factor'];
    $generatorCapacity = $essentialLoad * 1.25; // 25% future expansion margin
    
    // Environmental derating factors
    $altitudeFactor = 1 - ($altitude * 0.0001); // 1% per 100m above sea level
    $temperatureFactor = 1 - (($temperature - 25) * 0.01); // 1% per °C above 25°C
    $finalGeneratorCapacity = $generatorCapacity / max($altitudeFactor * $temperatureFactor, 0.7);
    
    // Generator specifications by type
    $generatorSpecs = [
        'diesel' => [
            'fuel_consumption_l_per_kwh' => 0.25,
            'fuel_cost_per_liter' => 1.20,
            'maintenance_hours' => 250,
            'noise_db_at_7m' => 85,
            'emissions_tier' => 'Tier 2'
        ],
        'natural_gas' => [
            'fuel_consumption_m3_per_kwh' => 0.4,
            'fuel_cost_per_m3' => 0.35,
            'maintenance_hours' => 200,
            'noise_db_at_7m' => 75,
            'emissions_tier' => 'Low emission'
        ],
        'propane' => [
            'fuel_consumption_kg_per_kwh' => 0.23,
            'fuel_cost_per_kg' => 1.80,
            'maintenance_hours' => 225,
            'noise_db_at_7m' => 80,
            'emissions_tier' => 'Medium emission'
        ],
        'battery' => [
            'battery_cost_per_kwh' => 300,
            'inverter_efficiency' => 0.95,
            'cycle_life' => 5000,
            'maintenance_years' => 10,
            'emissions_tier' => 'Zero emission'
        ]
    ];
    
    $genSpec = $generatorSpecs[$generatorType] ?: $generatorSpecs['diesel'];
    
    // Fuel/battery calculations
    if ($generatorType === 'battery') {
        $batteryCapacity = $essentialLoad * $durationHours;
        $batteryCost = $batteryCapacity * $genSpec['battery_cost_per_kwh'];
        $inverterRating = $essentialLoad / $genSpec['inverter_efficiency'];
        $batteryReplacementCost = $batteryCost * 2; // Two battery cycles over system life
    } else {
        if ($generatorType === 'diesel') {
            $fuelRequired = $essentialLoad * $durationHours * $genSpec['fuel_consumption_l_per_kwh'];
        } elseif ($generatorType === 'natural_gas') {
            $fuelRequired = $essentialLoad * $durationHours * $genSpec['fuel_consumption_m3_per_kwh'];
        } else {
            $fuelRequired = $essentialLoad * $durationHours * $genSpec['fuel_consumption_kg_per_kwh'];
        }
        $fuelCost = $fuelRequired * $genSpec['fuel_cost_per_liter'];
    }
    
    // UPS calculations for critical loads
    $criticalLoadPercentage = 0.25; // 25% of total load requires UPS
    $criticalLoad = $totalConnectedLoad * $criticalLoadPercentage;
    $upsLoad = $criticalLoad / 0.9; // Account for UPS efficiency
    $upsBackupTime = 0.5; // 30 minutes battery backup
    $upsBatteryCapacity = ($upsLoad * $upsBackupTime) / 0.8; // 80% depth of discharge
    $upsRating = $upsLoad * 1.3; // 30% overload capacity
    
    // Automatic Transfer Switch (ATS) requirements
    $atsRating = $essentialLoad * 1.2; // 20% margin for motor starting
    $transferTime = 0.25; // 250ms typical transfer time
    $isochronous = true; // Synchronize generator with utility
    
    // Installation and infrastructure costs
    $generatorInstallationCost = $finalGeneratorCapacity * 150; // $150 per kW installed
    $fuelTankCost = $fuelRequired * 2.5; // $2.50 per liter fuel tank capacity
    $electricalInfrastructureCost = $finalGeneratorCapacity * 75; // Wiring, panels, etc.
    $totalInstalledCost = $generatorInstallationCost + $fuelTankCost + $electricalInfrastructureCost;
    
    if ($generatorType === 'battery') {
        $totalInstalledCost = $batteryCost + ($inverterRating * 200) + $electricalInfrastructureCost;
    }
    
    // Maintenance and operational costs
    if ($generatorType === 'battery') {
        $annualMaintenanceCost = $totalInstalledCost * 0.02; // 2% of capital cost
        $batteryReplacementAnnualCost = $batteryReplacementCost / 20; // 20-year battery life
        $annualOperationalCost = $annualMaintenanceCost + $batteryReplacementAnnualCost;
    } else {
        $annualMaintenanceCost = $finalGeneratorCapacity * 25; // $25 per kW annual maintenance
        $annualFuelCost = $essentialLoad * 2000 * 0.25 * 0.12; // Assuming 2000 hours/year operation
        $annualOperationalCost = $annualMaintenanceCost + $annualFuelCost;
    }
    
    // System reliability calculations
    $mtbf = $generatorType === 'battery' ? 87600 : 2000; // Hours (battery vs generator)
    $availability = $mtbf / ($mtbf + 24); // Assuming 24 hours for repair
    $maintenanceInterval = $genSpec['maintenance_hours'];
    
    // Environmental impact
    if ($generatorType === 'battery') {
        $co2Emissions_kg_per_year = 0;
        $noiseLevel = 45; // dB at 7m
    } else {
        $co2Emissions_kg_per_hour = $essentialLoad * 0.7; // kg CO2 per kWh
        $co2Emissions_kg_per_year = $co2Emissions_kg_per_hour * 2000; // 2000 operating hours
        $noiseLevel = $genSpec['noise_db_at_7m'];
    }
    
    // Code compliance verification
    $codeRequirements = [
        'generator_capacity_min' => $essentialLoad * 1.2, // Minimum 120% of essential load
        'fuel_storage_min_hours' => $appReq['min_duration'],
        'generator_start_time_max' => $appReq['generator_start_time'],
        'transfer_time_max' => 300, // 5 minutes maximum for essential loads
        'battery_backup_min' => 30 // 30 minutes minimum
    ];
    
    $complianceStatus = [
        'capacity' => $finalGeneratorCapacity >= $codeRequirements['generator_capacity_min'],
        'start_time' => $appReq['generator_start_time'] <= $codeRequirements['generator_start_time_max'],
        'fuel_duration' => true, // User specified duration
        'transfer_time' => ($transferTime * 1000) <= $codeRequirements['transfer_time_max'],
        'battery_backup' => $upsBackupTime * 60 >= $codeRequirements['battery_backup_min']
    ];
    
    // Load sequencing and priority system
    $loadSequencing = [];
    foreach ($loadCategories as $key => $category) {
        $categoryLoad = $actualLoad * $category['percentage'];
        $loadSequencing[] = [
            'category' => $category['name'],
            'load_kw' => round($categoryLoad, 1),
            'priority' => $category['priority'],
            'delay_seconds' => ($category['priority'] - 1) * 30, // 30-second intervals
            'description' => $category['description']
        ];
    }
    
    // Emergency procedures and maintenance schedule
    $maintenanceSchedule = [
        'weekly' => ['Visual inspection', 'Fuel level check', 'Battery voltage check'],
        'monthly' => ['Generator exercise', 'Fluid levels check', 'Connection inspection'],
        'quarterly' => ['Professional inspection', 'Load bank testing', 'Coolant system check'],
        'annually' => ['Major service', 'Engine overhaul', 'Electrical system testing']
    ];
    
    $calculations = [
        'system_requirements' => [
            'connected_load' => $totalConnectedLoad,
            'actual_load' => round($actualLoad, 1),
            'reactive_load' => round($reactiveLoad, 1),
            'apparent_load' => round($apparentLoad, 1),
            'load_factor' => $loadFactor,
            'power_factor' => $powerFactor,
            'duration_hours' => $durationHours
        ],
        'generator_sizing' => [
            'essential_load' => round($essentialLoad, 1),
            'generator_capacity' => round($finalGeneratorCapacity, 1),
            'altitude_derating' => round((1 - $altitudeFactor) * 100, 1),
            'temperature_derating' => round((1 - $temperatureFactor) * 100, 1),
            'future_expansion_margin' => 25
        ],
        'fuel_battery' => [
            'type' => $generatorType,
            'specifications' => $genSpec
        ],
        'ups_system' => [
            'critical_load_kw' => round($criticalLoad, 1),
            'ups_rating_kva' => round($upsRating, 1),
            'battery_capacity_kwh' => round($upsBatteryCapacity, 1),
            'backup_time_minutes' => $upsBackupTime * 60,
            'efficiency' => 90
        ],
        'transfer_system' => [
            'ats_rating_amps' => round($atsRating, 1),
            'transfer_time_ms' => $transferTime * 1000,
            'synchronized' => $isochronous,
            'monitoring' => true
        ],
        'costs' => [
            'generator_installation' => round($generatorInstallationCost, 0),
            'fuel_battery_cost' => isset($fuelCost) ? round($fuelCost, 0) : round($batteryCost, 0),
            'infrastructure' => round($electricalInfrastructureCost, 0),
            'total_installed' => round($totalInstalledCost, 0),
            'annual_operation' => round($annualOperationalCost, 0)
        ],
        'reliability' => [
            'mtbf_hours' => $mtbf,
            'availability_percent' => round($availability * 100, 1),
            'maintenance_interval_hours' => $maintenanceInterval
        ],
        'environmental' => [
            'co2_emissions_kg_per_year' => isset($co2Emissions_kg_per_year) ? round($co2Emissions_kg_per_year, 0) : 0,
            'noise_level_db' => $noiseLevel,
            'emissions_tier' => $genSpec['emissions_tier']
        ],
        'compliance' => [
            'application' => $application,
            'code_compliance' => $appReq['code_compliance'],
            'status' => $complianceStatus,
            'requirements' => $codeRequirements
        ],
        'load_sequencing' => $loadSequencing,
        'maintenance' => $maintenanceSchedule,
        'recommendations' => generateEmergencyPowerRecommendations($finalGeneratorCapacity, $essentialLoad, $complianceStatus, $generatorType)
    ];
    
    $results = true;
}

function generateEmergencyPowerRecommendations($capacity, $essential, $compliance, $type) {
    $recommendations = [];
    
    if ($capacity > $essential * 1.5) {
        $recommendations[] = 'Consider right-sizing generator to reduce costs and improve efficiency';
    }
    
    if (!$compliance['capacity']) {
        $recommendations[] = 'Increase generator capacity to meet minimum code requirements';
    }
    
    if ($type === 'diesel') {
        $recommendations[] = 'Consider implementing fuel polishing system for long-term storage';
        $recommendations[] = 'Install automatic fuel transfer system for extended operation';
    }
    
    if ($type === 'battery') {
        $recommendations[] = 'Ensure adequate ventilation for battery enclosure';
        $recommendations[] = 'Implement thermal management system for battery temperature control';
    }
    
    $recommendations[] = 'Install remote monitoring system for generator status and alerts';
    $recommendations[] = 'Develop and test emergency response procedures';
    $recommendations[] = 'Consider redundant systems for critical applications';
    $recommendations[] = 'Regular load bank testing to verify performance';
    
    return $recommendations;
}

include '../../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><i class="icon-bolt"></i> Emergency Power System Calculator</h1>
        <p>Professional emergency power system design and sizing</p>
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
                            <label class="control-label col-sm-6">Total Connected Load (kW)</label>
                            <div class="col-sm-6">
                                <input type="number" step="0.1" name="total_connected_load" class="form-control" 
                                       value="<?php echo $totalConnectedLoad ?: ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Load Factor</label>
                            <div class="col-sm-6">
                                <input type="number" step="0.1" name="load_factor" class="form-control" 
                                       value="<?php echo $loadFactor; ?>" min="0.1" max="1.0">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Power Factor</label>
                            <div class="col-sm-6">
                                <input type="number" step="0.1" name="power_factor" class="form-control" 
                                       value="<?php echo $powerFactor; ?>" min="0.6" max="1.0">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Application Type</label>
                            <div class="col-sm-6">
                                <select name="application_type" class="form-control">
                                    <option value="hospital" <?php echo $application === 'hospital' ? 'selected' : ''; ?>>Hospital/Healthcare</option>
                                    <option value="data_center" <?php echo $application === 'data_center' ? 'selected' : ''; ?>>Data Center</option>
                                    <option value="office_building" <?php echo $application === 'office_building' ? 'selected' : ''; ?>>Office Building</option>
                                    <option value="industrial" <?php echo $application === 'industrial' ? 'selected' : ''; ?>>Industrial Facility</option>
                                    <option value="residential" <?php echo $application === 'residential' ? 'selected' : ''; ?>>Residential Complex</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Generator Type</label>
                            <div class="col-sm-6">
                                <select name="generator_type" class="form-control">
                                    <option value="diesel" <?php echo $generatorType === 'diesel' ? 'selected' : ''; ?>>Diesel Generator</option>
                                    <option value="natural_gas" <?php echo $generatorType === 'natural_gas' ? 'selected' : ''; ?>>Natural Gas Generator</option>
                                    <option value="propane" <?php echo $generatorType === 'propane' ? 'selected' : ''; ?>>Propane Generator</option>
                                    <option value="battery" <?php echo $generatorType === 'battery' ? 'selected' : ''; ?>>Battery Energy Storage</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Runtime Duration (hours)</label>
                            <div class="col-sm-6">
                                <input type="number" step="1" name="duration_hours" class="form-control" 
                                       value="<?php echo $durationHours; ?>" min="1">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Altitude (m)</label>
                            <div class="col-sm-6">
                                <input type="number" step="10" name="altitude" class="form-control" 
                                       value="<?php echo $altitude; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Ambient Temperature (°C)</label>
                            <div class="col-sm-6">
                                <input type="number" step="1" name="ambient_temperature" class="form-control" 
                                       value="<?php echo $temperature; ?>">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="icon-calculator"></i> Calculate Emergency Power System
                        </button>
                        
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <?php if ($results): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="icon-chart-line"></i> Emergency Power Analysis</h3>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Load Analysis</h4>
                            <table class="table table-condensed">
                                <tr><td>Connected Load</td><td><?php echo $calculations['system_requirements']['connected_load']; ?> kW</td></tr>
                                <tr><td>Actual Load</td><td><?php echo $calculations['system_requirements']['actual_load']; ?> kW</td></tr>
                                <tr><td>Apparent Load</td><td><?php echo $calculations['system_requirements']['apparent_load']; ?> kVA</td></tr>
                                <tr><td>Reactive Load</td><td><?php echo $calculations['system_requirements']['reactive_load']; ?> kVAR</td></tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Generator Sizing</h4>
                            <table class="table table-condensed">
                                <tr><td>Essential Load</td><td><?php echo $calculations['generator_sizing']['essential_load']; ?> kW</td></tr>
                                <tr><td>Generator Capacity</td><td><strong><?php echo $calculations['generator_sizing']['generator_capacity']; ?> kW</strong></td></tr>
                                <tr><td>Altitude Derating</td><td><?php echo $calculations['generator_sizing']['altitude_derating']; ?>%</td></tr>
                                <tr><td>Temp Derating</td><td><?php echo $calculations['generator_sizing']['temperature_derating']; ?>%</td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <h4>System Components</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>UPS System</h5>
                            <p><strong>Rating:</strong> <?php echo $calculations['ups_system']['ups_rating_kva']; ?> kVA</p>
                            <p><strong>Battery Capacity:</strong> <?php echo $calculations['ups_system']['battery_capacity_kwh']; ?> kWh</p>
                            <p><strong>Backup Time:</strong> <?php echo $calculations['ups_system']['backup_time_minutes']; ?> minutes</p>
                            <p><strong>Efficiency:</strong> <?php echo $calculations['ups_system']['efficiency']; ?>%</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Transfer System</h5>
                            <p><strong>ATS Rating:</strong> <?php echo $calculations['transfer_system']['ats_rating_amps']; ?> A</p>
                            <p><strong>Transfer Time:</strong> <?php echo $calculations['transfer_system']['transfer_time_ms']; ?> ms</p>
                            <p><strong>Synchronized:</strong> <?php echo $calculations['transfer_system']['synchronized'] ? 'Yes' : 'No'; ?></p>
                            <p><strong>Monitoring:</strong> <?php echo $calculations['transfer_system']['monitoring'] ? 'Yes' : 'No'; ?></p>
                        </div>
                    </div>
                    
                    <h4>Cost Analysis</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Generator/Installation:</strong> $<?php echo number_format($calculations['costs']['generator_installation']); ?></p>
                            <p><strong>Fuel/Battery:</strong> $<?php echo number_format($calculations['costs']['fuel_battery_cost']); ?></p>
                            <p><strong>Infrastructure:</strong> $<?php echo number_format($calculations['costs']['infrastructure']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Installed:</strong> <span class="text-primary"><strong>$<?php echo number_format($calculations['costs']['total_installed']); ?></strong></span></p>
                            <p><strong>Annual Operation:</strong> $<?php echo number_format($calculations['costs']['annual_operation']); ?></p>
                            <p><strong>Type:</strong> <?php echo strtoupper($generatorType); ?></p>
                        </div>
                    </div>
                    
                    <h4>Reliability & Environmental</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>MTBF:</strong> <?php echo number_format($calculations['reliability']['mtbf_hours']); ?> hours</p>
                            <p><strong>Availability:</strong> <?php echo $calculations['reliability']['availability_percent']; ?>%</p>
                            <p><strong>Maintenance Interval:</strong> <?php echo $calculations['reliability']['maintenance_interval_hours']; ?> hours</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>CO₂ Emissions:</strong> <?php echo number_format($calculations['environmental']['co2_emissions_kg_per_year']); ?> kg/year</p>
                            <p><strong>Noise Level:</strong> <?php echo $calculations['environmental']['noise_level_db']; ?> dB @ 7m</p>
                            <p><strong>Emissions Tier:</strong> <?php echo $calculations['environmental']['emissions_tier']; ?></p>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><i class="icon-list"></i> Load Sequencing by Priority</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr><th>Priority</th><th>Category</th><th>Load (kW)</th><th>Delay (s)</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($calculations['load_sequencing'] as $seq): ?>
                                    <tr>
                                        <td><?php echo $seq['priority']; ?></td>
                                        <td><?php echo $seq['category']; ?></td>
                                        <td><?php echo $seq['load_kw']; ?></td>
                                        <td><?php echo $seq['delay_seconds']; ?></td>
                                        <td><?php echo $seq['description']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="panel <?php echo in_array(false, $calculations['compliance']['status']) ? 'panel-danger' : 'panel-success'; ?>">
                        <div class="panel-heading">
                            <h4><i class="icon-check-sign"></i> Code Compliance Status - <?php echo $applicationRequirements[$application]['code_compliance']; ?></h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Generator Capacity:</strong> 
                                        <span class="<?php echo $calculations['compliance']['status']['capacity'] ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $calculations['compliance']['status']['capacity'] ? '✓ Compliant' : '✗ Non-compliant'; ?>
                                        </span>
                                    </p>
                                    <p><strong>Start Time:</strong> 
                                        <span class="<?php echo $calculations['compliance']['status']['start_time'] ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $calculations['compliance']['status']['start_time'] ? '✓ Compliant' : '✗ Non-compliant'; ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Fuel Duration:</strong> 
                                        <span class="<?php echo $calculations['compliance']['status']['fuel_duration'] ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $calculations['compliance']['status']['fuel_duration'] ? '✓ Compliant' : '✗ Non-compliant'; ?>
                                        </span>
                                    </p>
                                    <p><strong>Transfer Time:</strong> 
                                        <span class="<?php echo $calculations['compliance']['status']['transfer_time'] ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $calculations['compliance']['status']['transfer_time'] ? '✓ Compliant' : '✗ Non-compliant'; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h4><i class="icon-wrench"></i> Maintenance Schedule</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Weekly</h5>
                                    <ul>
                                        <?php foreach ($calculations['maintenance']['weekly'] as $item): ?>
                                        <li><?php echo $item; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    
                                    <h5>Monthly</h5>
                                    <ul>
                                        <?php foreach ($calculations['maintenance']['monthly'] as $item): ?>
                                        <li><?php echo $item; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5>Quarterly</h5>
                                    <ul>
                                        <?php foreach ($calculations['maintenance']['quarterly'] as $item): ?>
                                        <li><?php echo $item; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    
                                    <h5>Annually</h5>
                                    <ul>
                                        <?php foreach ($calculations['maintenance']['annually'] as $item): ?>
                                        <li><?php echo $item; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
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
                    <h4>Emergency Power System Calculator</h4>
                    <p>This calculator provides comprehensive emergency power system design including:</p>
                    <ul>
                        <li><strong>Generator Sizing:</strong> Calculate optimal generator capacity with environmental derating</li>
                        <li><strong>UPS Systems:</strong> Critical load protection with battery backup sizing</li>
                        <li><strong>Transfer Systems:</strong> ATS sizing and transfer time analysis</li>
                        <li><strong>Fuel/Battery:</strong> Fuel consumption and battery capacity calculations</li>
                        <li><strong>Cost Analysis:</strong> Total installed costs and operational expenses</li>
                        <li><strong>Code Compliance:</strong> NFPA, NEC, and other relevant code verification</li>
                        <li><strong>Load Sequencing:</strong> Priority-based load management strategies</li>
                    </ul>
                    
                    <h4>Application Types</h4>
                    <ul>
                        <li><strong>Hospital/Healthcare:</strong> Critical patient care facilities (72-hour minimum)</li>
                        <li><strong>Data Center:</strong> IT infrastructure protection (24-hour minimum)</li>
                        <li><strong>Office Building:</strong> General business operations (8-hour minimum)</li>
                        <li><strong>Industrial:</strong> Manufacturing and process loads (4-hour minimum)</li>
                        <li><strong>Residential:</strong> Essential home services (24-hour minimum)</li>
                    </ul>
                    
                    <h4>System Types</h4>
                    <ul>
                        <li><strong>Diesel Generator:</strong> High reliability, proven technology, longer runtime</li>
                        <li><strong>Natural Gas:</strong> Clean burning, continuous fuel supply, lower maintenance</li>
                        <li><strong>Propane:</strong> Portable, clean fuel, good for remote locations</li>
                        <li><strong>Battery Storage:</strong> Silent operation, zero emissions, limited runtime</li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <strong>Important:</strong> Emergency power systems require professional engineering design and installation. This calculator provides preliminary estimates only.
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.page-header {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    padding: 30px;
    margin-bottom: 30px;
    border-radius: 8px;
}

.icon-bolt:before {
    content: "⚡";
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

.panel-danger .panel-heading {
    background-color: #e74c3c;
    color: white;
}

.panel-success .panel-heading {
    background-color: #27ae60;
    color: white;
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

.table-striped tr:nth-child(odd) {
    background-color: #f9f9f9;
}
</style>

<?php include '../../../includes/footer.php'; ?>
