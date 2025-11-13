<?php
/**
 * MEP Plumbing Water Supply System Calculator
 * Calculates water supply requirements, pipe sizing, and pressure analysis
 */

require_once '../../../app/Config/config.php';

// Get input parameters
$projectName = filter_input(INPUT_POST, 'project_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'MEP Project';
$buildingType = filter_input(INPUT_POST, 'building_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'commercial';
$totalArea = filter_input(INPUT_POST, 'total_area', FILTER_VALIDATE_FLOAT) ?: 1000;
$floors = filter_input(INPUT_POST, 'number_floors', FILTER_VALIDATE_INT) ?: 3;
$occupancy = filter_input(INPUT_POST, 'occupancy_load', FILTER_VALIDATE_FLOAT) ?: 500;
$peakDemand = filter_input(INPUT_POST, 'peak_demand_factor', FILTER_VALIDATE_FLOAT) ?: 1.8;

$calculations = [];
$results = null;

if ($projectName && $buildingType) {
    // Water demand calculations based on building type
    $waterDemandRates = [
        'residential' => [
            'domestic_l_per_day_per_person' => 150,
            'fixture_units' => 1.0,
            'peak_factor' => 2.5
        ],
        'commercial' => [
            'domestic_l_per_day_per_person' => 75,
            'fixture_units' => 1.5,
            'peak_factor' => 2.0
        ],
        'industrial' => [
            'domestic_l_per_day_per_person' => 50,
            'fixture_units' => 2.0,
            'peak_factor' => 1.8
        ],
        'hospital' => [
            'domestic_l_per_day_per_person' => 300,
            'fixture_units' => 2.5,
            'peak_factor' => 3.0
        ],
        'hotel' => [
            'domestic_l_per_day_per_person' => 200,
            'fixture_units' => 1.8,
            'peak_factor' => 2.2
        ]
    ];
    
    $demandData = $waterDemandRates[$buildingType] ?: $waterDemandRates['commercial'];
    
    // Calculate water demands
    $averageDailyDemand = $occupancy * $demandData['domestic_l_per_day_per_person'];
    $peakDailyDemand = $averageDailyDemand * $demandData['peak_factor'];
    $peakHourlyDemand = $peakDailyDemand / 10; // Peak hour is 10% of daily demand
    $peakMinuteDemand = $peakHourlyDemand / 60; // Peak minute is 1/60 of hourly demand
    
    // Fixture unit calculations
    $totalFixtureUnits = $occupancy * $demandData['fixture_units'];
    $demandFlowRate = calculateDemandFlowRate($totalFixtureUnits); // L/min
    
    // Pipe sizing calculations
    $pipeSizes = [
        'main_service' => calculatePipeSize($demandFlowRate, 2.5), // 2.5 m/s velocity
        'distribution' => calculatePipeSize($demandFlowRate * 0.6, 2.0), // 60% of peak flow
        'branch_lines' => calculatePipeSize($demandFlowRate * 0.3, 1.8) // 30% of peak flow
    ];
    
    // Pressure requirements
    $pressureRequirements = [
        'minimum_pressure' => 150, // kPa (22 psi)
        'maximum_pressure' => 500, // kPa (72 psi)
        'recommended_pressure' => 250, // kPa (36 psi)
        'pressure_loss_per_floor' => 35, // kPa per floor
        'total_pressure_loss' => $floors * 35 + 100 // Static + friction losses
    ];
    
    // Pump sizing
    $pumpRequirements = [
        'flow_rate' => $peakMinuteDemand / 60, // L/s
        'head_pressure' => $pressureRequirements['total_pressure_loss'] + 50, // kPa + safety margin
        'power_required' => ($peakMinuteDemand / 60) * $pressureRequirements['total_pressure_loss'] / (1000 * 0.7), // kW (70% efficiency)
        'pump_type' => 'Centrifugal multi-stage'
    ];
    
    // Water storage requirements
    $storageRequirements = [
        'fire_storage' => max(50000, $totalArea * 50), // Minimum 50,000L or 50L/m²
        'domestic_storage' => $averageDailyDemand * 0.25, // 25% of daily demand
        'emergency_storage' => $averageDailyDemand * 0.5, // 50% of daily demand
        'total_storage' => 0
    ];
    $storageRequirements['total_storage'] = $storageRequirements['fire_storage'] + 
                                          $storageRequirements['domestic_storage'] + 
                                          $storageRequirements['emergency_storage'];
    
    // Pipe material specifications
    $pipeMaterials = [
        'main_service' => [
            'material' => 'Ductile Iron or HDPE',
            'class' => 'PN16 (16 bar)',
            'lining' => 'Cement mortar lined',
            'joint_type' => 'Push-on or mechanical joint'
        ],
        'internal_distribution' => [
            'material' => 'Copper or PEX',
            'class' => 'PN10 (10 bar)',
            'insulation' => 'Required for hot water lines',
            'joint_type' => 'Soldered or compression'
        ],
        'branch_lines' => [
            'material' => 'Copper or PEX',
            'class' => 'PN6 (6 bar)',
            'insulation' => 'Condensation prevention',
            'joint_type' => 'Soldered or compression'
        ]
    ];
    
    // Valves and fittings
    $valvesAndFittings = [
        'isolation_valves' => [
            'main_service' => 'Gate valve',
            'distribution' => 'Ball valve',
            'branch_lines' => 'Ball valve'
        ],
        'control_valves' => [
            'pressure_reducing' => 'Required for floors > 4',
            'check_valves' => 'Prevent backflow',
            'float_valves' => 'Tank level control'
        ],
        'backflow_prevention' => [
            'type' => 'Double check valve assembly',
            'location' => 'Main service entrance',
            'testing' => 'Annual testing required'
        ]
    ];
    
    // Water quality requirements
    $waterQuality = [
        'potable_water' => [
            'turbidity' => '< 1 NTU',
            'chlorine_residual' => '0.2-0.5 mg/L',
            'ph_range' => '6.5-8.5',
            'total_dissolved_solids' => '< 500 mg/L'
        ],
        'hot_water' => [
            'temperature' => '60°C ± 5°C',
            'circulation' => 'Required for buildings > 3 floors',
            'scald_protection' => 'Mixing valves required'
        ],
        'testing_requirements' => [
            'frequency' => 'Monthly bacteriological',
            'parameters' => 'E.coli, Total coliform, Turbidity',
            'certification' => 'Third-party laboratory required'
        ]
    ];
    
    // Cost estimation
    $costEstimation = [
        'piping_system' => [
            'main_service' => 15000,
            'distribution_pipes' => $totalArea * 45,
            'branch_lines' => $totalArea * 35,
            'valves_and_fittings' => $totalArea * 25
        ],
        'pumping_equipment' => [
            'main_pumps' => 25000,
            'booster_pumps' => 15000,
            'control_systems' => 8000,
            'installation' => 12000
        ],
        'storage_tanks' => [
            'water_tanks' => $storageRequirements['total_storage'] * 2.5, // $2.5 per liter
            'tank_foundations' => 8000,
            'piping_to_tanks' => 5000,
            'level_control' => 3000
        ],
        'water_treatment' => [
            'filtration' => 12000,
            'disinfection' => 8000,
            'water_softening' => 15000,
            'testing_equipment' => 5000
        ]
    ];
    
    // Calculate total cost
    $totalWaterSupplyCost = 0;
    foreach ($costEstimation as $category => $items) {
        foreach ($items as $item => $cost) {
            $totalWaterSupplyCost += $cost;
        }
    }
    
    // Integration with other systems
    $systemIntegration = [
        'fire_protection' => [
            'shared_storage' => 'Combined fire/domestic storage tank',
            'pump_ coordination' => 'Fire pump priority over domestic',
            'pressure_monitoring' => 'Common pressure monitoring system'
        ],
        'hvac_systems' => [
            'cooling_towers' => 'Make-up water supply required',
            'boilers' => 'Water treatment for boiler feedwater',
            'humidification' => 'Potable water for humidification systems'
        ],
        'electrical_systems' => [
            'pump_controls' => 'Variable speed drives for energy efficiency',
            'monitoring' => 'SCADA integration for system monitoring',
            'emergency_power' => 'Generator backup for critical pumps'
        ]
    ];
    
    // Compliance and standards
    $complianceStandards = [
        'international_codes' => [
            'iso_9001' => 'Quality management systems',
            'iso_14001' => 'Environmental management',
            'who_guidelines' => 'Drinking water quality guidelines'
        ],
        'national_standards' => [
            'astm' => 'Pipe and fitting standards',
            'awwa' => 'Water works standards',
            'nsf_std_61' => 'Drinking water system components'
        ],
        'local_requirements' => [
            'building_code' => 'Local plumbing code compliance',
            'health_department' => 'Public health requirements',
            'utility_company' => 'Water service connection standards'
        ]
    ];
    
    $calculations = [
        'project_information' => [
            'name' => $projectName,
            'building_type' => $buildingType,
            'total_area' => $totalArea,
            'number_of_floors' => $floors,
            'occupancy_load' => $occupancy
        ],
        'demand_analysis' => [
            'average_daily_demand' => round($averageDailyDemand, 0),
            'peak_daily_demand' => round($peakDailyDemand, 0),
            'peak_hourly_demand' => round($peakHourlyDemand, 0),
            'peak_minute_demand' => round($peakMinuteDemand, 0),
            'demand_flow_rate' => round($demandFlowRate, 1),
            'total_fixture_units' => round($totalFixtureUnits, 1)
        ],
        'pipe_sizing' => [
            'main_service' => $pipeSizes['main_service'],
            'distribution' => $pipeSizes['distribution'],
            'branch_lines' => $pipeSizes['branch_lines']
        ],
        'pressure_analysis' => $pressureRequirements,
        'pump_requirements' => $pumpRequirements,
        'storage_requirements' => $storageRequirements,
        'pipe_materials' => $pipeMaterials,
        'valves_and_fittings' => $valvesAndFittings,
        'water_quality' => $waterQuality,
        'cost_estimation' => [
            'breakdown' => $costEstimation,
            'total_cost' => round($totalWaterSupplyCost, 0),
            'cost_per_m2' => round($totalWaterSupplyCost / $totalArea, 2)
        ],
        'system_integration' => $systemIntegration,
        'compliance_standards' => $complianceStandards,
        'recommendations' => generateWaterSupplyRecommendations($buildingType, $totalArea, $floors, $pumpRequirements)
    ];
    
    $results = true;
}

function calculateDemandFlowRate($fixtureUnits) {
    // Using Hunter's curve approximation
    if ($fixtureUnits <= 10) {
        return $fixtureUnits * 0.6; // L/min per fixture unit
    } elseif ($fixtureUnits <= 50) {
        return 6 + ($fixtureUnits - 10) * 0.4;
    } else {
        return 22 + ($fixtureUnits - 50) * 0.2;
    }
}

function calculatePipeSize($flowRate, $velocity) {
    // Q = A × V, where Q = flow rate, A = area, V = velocity
    // A = π × (D/2)², so D = √(4Q/πV)
    $area = $flowRate / ($velocity * 1000); // Convert L/min to m³/s
    $diameter = sqrt((4 * $area) / (pi() * 1000)); // Convert back to mm
    return round($diameter, 0);
}

function generateWaterSupplyRecommendations($buildingType, $totalArea, $floors, $pumpRequirements) {
    $recommendations = [];
    
    if ($floors > 4) {
        $recommendations[] = 'Install pressure reducing valves on lower floors to prevent excessive pressure';
        $recommendations[] = 'Consider zone control for high-rise buildings';
    }
    
    if ($buildingType === 'hospital' || $buildingType === 'hotel') {
        $recommendations[] = 'Install redundant pumping systems for critical water supply';
        $recommendations[] = 'Implement water quality monitoring and treatment systems';
    }
    
    if ($pumpRequirements['power_required'] > 10) {
        $recommendations[] = 'Install variable frequency drives for energy-efficient pump operation';
        $recommendations[] = 'Consider high-efficiency pump selection to reduce operating costs';
    }
    
    $recommendations[] = 'Implement water metering for consumption monitoring';
    $recommendations[] = 'Install backflow prevention devices to protect public water supply';
    $recommendations[] = 'Consider rainwater harvesting for non-potable water uses';
    $recommendations[] = 'Implement leak detection systems to minimize water loss';
    $recommendations[] = 'Ensure compliance with local water conservation requirements';
    
    return $recommendations;
}

include '../../../themes/default/views/partials/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><i class="icon-tint"></i> MEP Water Supply System Calculator</h1>
        <p>Water supply requirements, pipe sizing, and pressure analysis</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Project Information</h3>
                </div>
                <div class="card-body">
                    <form method="POST" class="form-horizontal">
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Project Name</label>
                            <div class="col-sm-6">
                                <input type="text" name="project_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($projectName); ?>" required>
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
                                    <option value="hotel" <?php echo $buildingType === 'hotel' ? 'selected' : ''; ?>>Hotel</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Total Area (m²)</label>
                            <div class="col-sm-6">
                                <input type="number" step="10" name="total_area" class="form-control" 
                                       value="<?php echo $totalArea; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Number of Floors</label>
                            <div class="col-sm-6">
                                <input type="number" step="1" name="number_floors" class="form-control" 
                                       value="<?php echo $floors; ?>" min="1">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Occupancy Load (persons)</label>
                            <div class="col-sm-6">
                                <input type="number" step="10" name="occupancy_load" class="form-control" 
                                       value="<?php echo $occupancy; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Peak Demand Factor</label>
                            <div class="col-sm-6">
                                <input type="number" step="0.1" name="peak_demand_factor" class="form-control" 
                                       value="<?php echo $peakDemand; ?>" min="1.0" max="3.0">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="icon-calculator"></i> Calculate Water Supply
                        </button>
                        
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <?php if ($results): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="icon-bar-chart"></i> Water Supply Analysis</h3>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Project Details</h4>
                            <table class="table table-condensed">
                                <tr><td>Project</td><td><?php echo htmlspecialchars($calculations['project_information']['name']); ?></td></tr>
                                <tr><td>Building Type</td><td><?php echo ucfirst($calculations['project_information']['building_type']); ?></td></tr>
                                <tr><td>Total Area</td><td><?php echo $calculations['project_information']['total_area']; ?> m²</td></tr>
                                <tr><td>Floors</td><td><?php echo $calculations['project_information']['number_of_floors']; ?></td></tr>
                                <tr><td>Occupancy</td><td><?php echo $calculations['project_information']['occupancy_load']; ?> persons</td></tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Water Demand</h4>
                            <table class="table table-condensed">
                                <tr><td>Average Daily</td><td><?php echo number_format($calculations['demand_analysis']['average_daily_demand']); ?> L/day</td></tr>
                                <tr><td>Peak Daily</td><td><?php echo number_format($calculations['demand_analysis']['peak_daily_demand']); ?> L/day</td></tr>
                                <tr><td>Peak Hourly</td><td><?php echo number_format($calculations['demand_analysis']['peak_hourly_demand']); ?> L/hr</td></tr>
                                <tr><td>Peak Minute</td><td><?php echo number_format($calculations['demand_analysis']['peak_minute_demand']); ?> L/min</td></tr>
                                <tr><td><strong>Demand Flow</strong></td><td><strong><?php echo $calculations['demand_analysis']['demand_flow_rate']; ?> L/min</strong></td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <h4>Pipe Sizing</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Main Service</h5>
                            <p><strong>Diameter:</strong> <?php echo $calculations['pipe_sizing']['main_service']; ?> mm</p>
                            <p><strong>Material:</strong> <?php echo $calculations['pipe_materials']['main_service']['material']; ?></p>
                            <p><strong>Class:</strong> <?php echo $calculations['pipe_materials']['main_service']['class']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Distribution</h5>
                            <p><strong>Diameter:</strong> <?php echo $calculations['pipe_sizing']['distribution']; ?> mm</p>
                            <p><strong>Material:</strong> <?php echo $calculations['pipe_materials']['internal_distribution']['material']; ?></p>
                            <p><strong>Class:</strong> <?php echo $calculations['pipe_materials']['internal_distribution']['class']; ?></p>
                        </div>
                    </div>
                    
                    <h4>Pressure Analysis</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Minimum Pressure:</strong> <?php echo $calculations['pressure_analysis']['minimum_pressure']; ?> kPa</p>
                            <p><strong>Maximum Pressure:</strong> <?php echo $calculations['pressure_analysis']['maximum_pressure']; ?> kPa</p>
                            <p><strong>Recommended Pressure:</strong> <?php echo $calculations['pressure_analysis']['recommended_pressure']; ?> kPa</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Pressure Loss/Floor:</strong> <?php echo $calculations['pressure_analysis']['pressure_loss_per_floor']; ?> kPa</p>
                            <p><strong>Total Pressure Loss:</strong> <?php echo $calculations['pressure_analysis']['total_pressure_loss']; ?> kPa</p>
                        </div>
                    </div>
                    
                    <h4>Pump Requirements</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Flow Rate:</strong> <?php echo $calculations['pump_requirements']['flow_rate']; ?> L/s</p>
                            <p><strong>Head Pressure:</strong> <?php echo $calculations['pump_requirements']['head_pressure']; ?> kPa</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Power Required:</strong> <?php echo $calculations['pump_requirements']['power_required']; ?> kW</p>
                            <p><strong>Pump Type:</strong> <?php echo $calculations['pump_requirements']['pump_type']; ?></p>
                        </div>
                    </div>
                    
                    <h4>Storage Requirements</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Fire Storage:</strong> <?php echo number_format($calculations['storage_requirements']['fire_storage']); ?> L</p>
                            <p><strong>Domestic Storage:</strong> <?php echo number_format($calculations['storage_requirements']['domestic_storage']); ?> L</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Emergency Storage:</strong> <?php echo number_format($calculations['storage_requirements']['emergency_storage']); ?> L</p>
                            <p><strong>Total Storage:</strong> <span class="text-primary"><strong><?php echo number_format($calculations['storage_requirements']['total_storage']); ?> L</strong></span></p>
                        </div>
                    </div>
                    
                    <h4>Cost Analysis</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Piping System:</strong> $<?php echo number_format(array_sum($calculations['cost_estimation']['breakdown']['piping_system'])); ?></p>
                            <p><strong>Pumping Equipment:</strong> $<?php echo number_format(array_sum($calculations['cost_estimation']['breakdown']['pumping_equipment'])); ?></p>
                            <p><strong>Storage Tanks:</strong> $<?php echo number_format(array_sum($calculations['cost_estimation']['breakdown']['storage_tanks'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Water Treatment:</strong> $<?php echo number_format(array_sum($calculations['cost_estimation']['breakdown']['water_treatment'])); ?></p>
                            <p><strong>Total Cost:</strong> <span class="text-primary"><strong>$<?php echo number_format($calculations['cost_estimation']['total_cost']); ?></strong></span></p>
                            <p><strong>Cost per m²:</strong> $<?php echo $calculations['cost_estimation']['cost_per_m2']; ?></p>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><i class="icon-shield"></i> Water Quality Requirements</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Potable Water</h5>
                                    <p><strong>Turbidity:</strong> <?php echo $calculations['water_quality']['potable_water']['turbidity']; ?></p>
                                    <p><strong>Chlorine Residual:</strong> <?php echo $calculations['water_quality']['potable_water']['chlorine_residual']; ?></p>
                                    <p><strong>pH Range:</strong> <?php echo $calculations['water_quality']['potable_water']['ph_range']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Hot Water</h5>
                                    <p><strong>Temperature:</strong> <?php echo $calculations['water_quality']['hot_water']['temperature']; ?></p>
                                    <p><strong>Circulation:</strong> <?php echo $calculations['water_quality']['hot_water']['circulation']; ?></p>
                                    <p><strong>Protection:</strong> <?php echo $calculations['water_quality']['hot_water']['scald_protection']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h4><i class="icon-check"></i> Recommendations</h4>
                        </div>
                        <div class="panel-body">
                            <ul class="list-group">
                                <?php foreach ($calculations['recommendations'] as $recommendation): ?>
                                <li class="list-group-item">
                                    <i class="icon-arrow-right text-primary"></i> <?php echo htmlspecialchars($recommendation); ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-success btn-lg" onclick="printReport()">
                            <i class="icon-print"></i> Print Report
                        </button>
                        <button type="button" class="btn btn-info btn-lg" onclick="exportToPDF()">
                            <i class="icon-file-pdf"></i> Export to PDF
                        </button>
                        <button type="button" class="btn btn-warning btn-lg" onclick="saveConfiguration()">
                            <i class="icon-save"></i> Save Configuration
                        </button>
                    </div>
                    
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function printReport() {
    window.print();
}

function exportToPDF() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'export-water-supply.php';
    
    const data = {
        project_name: '<?php echo htmlspecialchars($projectName); ?>',
        building_type: '<?php echo htmlspecialchars($buildingType); ?>',
        total_area: <?php echo $totalArea; ?>,
        calculations: <?php echo json_encode($calculations); ?>
    };
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'pdf_data';
    input.value = JSON.stringify(data);
    form.appendChild(input);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function saveConfiguration() {
    const configData = {
        project_name: '<?php echo htmlspecialchars($projectName); ?>',
        building_type: '<?php echo htmlspecialchars($buildingType); ?>',
        total_area: <?php echo $totalArea; ?>,
        calculations: <?php echo json_encode($calculations); ?>,
        timestamp: new Date().toISOString()
    };
    
    localStorage.setItem('mep_water_supply', JSON.stringify(configData));
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="icon-check"></i> Saved!';
    button.classList.remove('btn-warning');
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-warning');
    }, 2000);
}

function loadSavedConfiguration() {
    const saved = localStorage.getItem('mep_water_supply');
    if (saved) {
        const config = JSON.parse(saved);
        if (confirm('Load saved configuration from ' + new Date(config.timestamp).toLocaleString() + '?')) {
            document.querySelector('input[name="project_name"]').value = config.project_name;
            document.querySelector('select[name="building_type"]').value = config.building_type;
            document.querySelector('input[name="total_area"]').value = config.total_area;
            document.querySelector('form').submit();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadSavedConfiguration();
});
</script>

<style>
@media print {
    .btn, .form-group, .card-header {
        display: none !important;
    }
    .card-body {
        padding: 10px !important;
    }
    .panel {
        border: 1px solid #ddd !important;
        margin-bottom: 10px !important;
    }
}

.panel {
    margin-bottom: 15px;
}

.list-group-item {
    padding: 8px 15px;
    border: 1px solid #ddd;
    margin-bottom: 2px;
}

.table-condensed th,
.table-condensed td {
    padding: 4px 8px;
    font-size: 12px;
}

h4 {
    margin-top: 15px;
    margin-bottom: 10px;
    color: #333;
}

h5 {
    margin-top: 10px;
    margin-bottom: 8px;
    color: #555;
}
</style>

<?php
include '../../../themes/default/views/partials/footer.php';
?>

