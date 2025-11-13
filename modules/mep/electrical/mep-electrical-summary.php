<?php
/**
 * MEP Electrical Systems Summary
 * Comprehensive electrical systems analysis and coordination
 * Integrates all electrical calculations and provides system overview
 */

require_once '../../../app/Config/config.php';

// Get summary parameters
$projectName = filter_input(INPUT_POST, 'project_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'MEP Project';
$buildingType = filter_input(INPUT_POST, 'building_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'commercial';
$totalArea = filter_input(INPUT_POST, 'total_area', FILTER_VALIDATE_FLOAT) ?: 1000;
$floors = filter_input(INPUT_POST, 'number_floors', FILTER_VALIDATE_INT) ?: 3;
$occupancy = filter_input(INPUT_POST, 'occupancy_load', FILTER_VALIDATE_FLOAT) ?: 500;

$calculations = [];
$results = null;

// Check if we have electrical calculation data from other modules
$powerDistributionData = $_SESSION['power_distribution'] ?? null;
$panelCoordinationData = $_SESSION['panel_coordination'] ?? null;
$lightingLayoutData = $_SESSION['lighting_layout'] ?? null;
$emergencyPowerData = $_SESSION['emergency_power'] ?? null;
$earthingSystemData = $_SESSION['earthing_system'] ?? null;

if ($projectName && $buildingType) {
    // Building type electrical load densities
    $loadDensities = [
        'residential' => [
            'lighting_w_per_m2' => 15,
            'power_w_per_m2' => 25,
            'hvac_w_per_m2' => 30,
            'misc_w_per_m2' => 10
        ],
        'commercial' => [
            'lighting_w_per_m2' => 20,
            'power_w_per_m2' => 35,
            'hvac_w_per_m2' => 40,
            'misc_w_per_m2' => 15
        ],
        'industrial' => [
            'lighting_w_per_m2' => 25,
            'power_w_per_m2' => 60,
            'hvac_w_per_m2' => 50,
            'misc_w_per_m2' => 20
        ],
        'hospital' => [
            'lighting_w_per_m2' => 25,
            'power_w_per_m2' => 45,
            'hvac_w_per_m2' => 55,
            'misc_w_per_m2' => 25
        ],
        'data_center' => [
            'lighting_w_per_m2' => 15,
            'power_w_per_m2' => 150,
            'hvac_w_per_m2' => 80,
            'misc_w_per_m2' => 30
        ]
    ];
    
    $loadDensity = $loadDensities[$buildingType] ?: $loadDensities['commercial'];
    
    // Calculate total electrical loads
    $lightingLoad = $totalArea * $loadDensity['lighting_w_per_m2'];
    $powerLoad = $totalArea * $loadDensity['power_w_per_m2'];
    $hvacLoad = $totalArea * $loadDensity['hvac_w_per_m2'];
    $miscLoad = $totalArea * $loadDensity['misc_w_per_m2'];
    
    $totalConnectedLoad = $lightingLoad + $powerLoad + $hvacLoad + $miscLoad;
    $demandFactor = calculateDemandFactor($buildingType, $totalConnectedLoad);
    $totalDemandLoad = $totalConnectedLoad * $demandFactor;
    
    // Electrical system configuration
    $voltageLevels = [
        'service_voltage' => 400,    // 3-phase service voltage
        'distribution_voltage' => 230, // 3-phase distribution
        'branch_circuit_voltage' => 230, // Single-phase branch circuits
        'lighting_voltage' => 230,   // Lighting circuits
        'emergency_voltage' => 230   // Emergency circuits
    ];
    
    // Power quality requirements
    $powerQuality = [
        'voltage_regulation' => 5,    // ±5% voltage regulation
        'frequency_regulation' => 0.5, // ±0.5% frequency regulation
        'harmonics_thd' => 5,         // 5% total harmonic distortion
        'power_factor' => 0.9,        // 0.9 minimum power factor
        'unbalance' => 2              // 2% voltage unbalance
    ];
    
    // Electrical room requirements
    $electricalRooms = [
        'main_electrical_room' => [
            'area_m2' => max(20, $totalArea * 0.02),
            'clear_height_m' => 3.5,
            'access_requirements' => 'Vehicle access for equipment',
            'ventilation' => 'Mechanical ventilation required',
            'fire_rating' => '2-hour fire rating'
        ],
        'branch_electrical_rooms' => [
            'quantity' => ceil($floors / 2),
            'area_m2' => 8,
            'clear_height_m' => 3.0,
            'access_requirements' => 'Corridor access',
            'ventilation' => 'Natural or mechanical ventilation',
            'fire_rating' => '1-hour fire rating'
        ]
    ];
    
    // Cable and conductor requirements
    $cableRequirements = [
        'service_entrance' => [
            'type' => 'XLPE insulated, copper conductor',
            'installation' => 'Underground conduit system',
            'protection' => 'Mechanical protection required',
            'grounding' => 'Separate grounding conductor'
        ],
        'distribution' => [
            'type' => 'PVC insulated, copper conductor',
            'installation' => 'Cable tray or conduit',
            'protection' => 'Circuit breaker protection',
            'grounding' => 'Equipment grounding conductor'
        ],
        'branch_circuits' => [
            'type' => 'THWN/THHN insulated, copper conductor',
            'installation' => 'Conduit or cable assembly',
            'protection' => 'Circuit breaker or fuse',
            'grounding' => 'Equipment grounding conductor'
        ]
    ];
    
    // Protection and coordination
    $protectionCoordination = [
        'main_protection' => [
            'device_type' => 'Circuit breaker',
            'rating_amps' => ceil($totalDemandLoad / ($voltageLevels['service_voltage'] * sqrt(3) * 0.9)),
            'interrupting_capacity' => 25000, // 25kA
            'coordination' => 'Selectivity with downstream devices'
        ],
        'distribution_protection' => [
            'device_type' => 'Circuit breaker',
            'rating_amps' => 100,
            'interrupting_capacity' => 10000, // 10kA
            'coordination' => 'Time-current coordination'
        ],
        'branch_protection' => [
            'device_type' => 'Circuit breaker',
            'rating_amps' => 20,
            'interrupting_capacity' => 5000, // 5kA
            'coordination' => 'Instantaneous trip coordination'
        ]
    ];
    
    // Energy efficiency measures
    $energyEfficiency = [
        'led_lighting' => [
            'energy_savings' => 60, // 60% savings vs conventional
            'lifespan_hours' => 50000,
            'maintenance_savings' => 80 // 80% reduction in maintenance
        ],
        'variable_frequency_drives' => [
            'energy_savings' => 30, // 30% savings for motor loads
            'soft_start' => true,
            'power_factor_correction' => true
        ],
        'power_factor_correction' => [
            'target_power_factor' => 0.95,
            'capacitor_rating_kvar' => ceil($totalDemandLoad * 0.3 / 100),
            'harmonic_filtering' => true
        ],
        'smart_metering' => [
            'real_time_monitoring' => true,
            'demand_response' => true,
            'energy_management' => true
        ]
    ];
    
    // Electrical safety requirements
    $safetyRequirements = [
        'ground_fault_protection' => [
            'required' => true,
            'sensitivity_ma' => 30, // 30mA for personnel protection
            'coordination' => 'Selective coordination required'
        ],
        'arc_fault_protection' => [
            'required' => $buildingType === 'residential',
            'device_type' => 'AFCI circuit breaker',
            'coordination' => 'With downstream protection'
        ],
        'surge_protection' => [
            'service_entrance' => 'Type 1 SPD required',
            'distribution' => 'Type 2 SPD recommended',
            'branch_circuits' => 'Type 3 SPD for sensitive equipment'
        ],
        'emergency_systems' => [
            'emergency_power' => 'Generator or UPS system',
            'emergency_lighting' => 'Battery backup required',
            'fire_alarm' => 'Dedicated power supply',
            'security_systems' => 'Uninterruptible power supply'
        ]
    ];
    
    // Cost estimation
    $costEstimation = [
        'electrical_infrastructure' => [
            'service_entrance' => 15000,
            'distribution_system' => $totalArea * 25,
            'branch_circuit_wiring' => $totalArea * 35,
            'electrical_rooms' => $totalArea * 50
        ],
        'equipment_and_devices' => [
            'switchgear_and_panelboards' => $totalArea * 15,
            'lighting_fixtures' => $totalArea * 20,
            'receptacles_and_devices' => $totalArea * 10,
            'motor_controls' => $totalArea * 12
        ],
        'special_systems' => [
            'emergency_power' => 50000,
            'fire_alarm_system' => $totalArea * 8,
            'security_system' => $totalArea * 5,
            'telecommunications' => $totalArea * 12
        ],
        'installation_and_commissioning' => [
            'labor' => ($totalArea * 45) + 25000,
            'commissioning' => 5000,
            'permits_and_inspections' => 3000
        ]
    ];
    
    // Calculate total project cost
    $totalElectricalCost = 0;
    foreach ($costEstimation as $category => $items) {
        foreach ($items as $item => $cost) {
            $totalElectricalCost += $cost;
        }
    }
    
    // Integration with other MEP systems
    $mepIntegration = [
        'mechanical_systems' => [
            'hvac_controls' => 'DDC system integration',
            'motor_controls' => 'VFD coordination',
            'power_requirements' => 'Dedicated circuits for HVAC',
            'coordination' => 'Electrical/mechanical room sharing'
        ],
        'plumbing_systems' => [
            'pump_controls' => 'Variable speed drives',
            'water_heater_power' => 'Dedicated circuits',
            'sump_pump_power' => 'Emergency power backup',
            'coordination' => 'Electrical/plumbing chase coordination'
        ],
        'fire_protection' => [
            'fire_pump_power' => 'Dedicated emergency circuit',
            'sprinkler_controls' => 'Supervisory power',
            'fire_alarm_integration' => 'System coordination',
            'emergency_lighting' => 'Fire-rated emergency circuits'
        ]
    ];
    
    // Compliance and standards
    $complianceStandards = [
        'national_codes' => [
            'nec' => 'National Electrical Code (NFPA 70)',
            'nfpa_110' => 'Emergency Power Systems',
            'nfpa_72' => 'Fire Alarm and Signaling Code',
            'ieee_519' => 'Power Quality Standards'
        ],
        'local_requirements' => [
            'building_code' => 'Local building electrical requirements',
            'utility_requirements' => 'Utility interconnection standards',
            'fire_code' => 'Fire department electrical requirements',
            'accessibility' => 'ADA electrical accessibility requirements'
        ],
        'industry_standards' => [
            'ul_listings' => 'UL listed equipment required',
            'ieee_standards' => 'IEEE power engineering standards',
            'nema_standards' => 'NEMA motor and control standards',
            'ansi_standards' => 'ANSI electrical safety standards'
        ]
    ];
    
    // Maintenance and lifecycle
    $maintenanceLifecycle = [
        'preventive_maintenance' => [
            'frequency' => 'Annual inspection and testing',
            'tasks' => ['Thermal imaging', 'Connection inspection', 'Load testing'],
            'cost_annual' => $totalElectricalCost * 0.02
        ],
        'predictive_maintenance' => [
            'monitoring' => 'Online condition monitoring',
            'analysis' => 'Power quality and thermal analysis',
            'cost_annual' => $totalElectricalCost * 0.015
        ],
        'lifecycle_replacement' => [
            'lighting' => '15-20 years',
            'electrical_devices' => '20-25 years',
            'switchgear' => '25-30 years',
            'cable_systems' => '30-40 years'
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
        'load_analysis' => [
            'lighting_load' => round($lightingLoad, 0),
            'power_load' => round($powerLoad, 0),
            'hvac_load' => round($hvacLoad, 0),
            'misc_load' => round($miscLoad, 0),
            'total_connected' => round($totalConnectedLoad, 0),
            'demand_factor' => $demandFactor,
            'total_demand' => round($totalDemandLoad, 0)
        ],
        'system_configuration' => [
            'voltage_levels' => $voltageLevels,
            'power_quality' => $powerQuality,
            'electrical_rooms' => $electricalRooms
        ],
        'cable_requirements' => $cableRequirements,
        'protection_coordination' => $protectionCoordination,
        'energy_efficiency' => $energyEfficiency,
        'safety_requirements' => $safetyRequirements,
        'cost_estimation' => [
            'breakdown' => $costEstimation,
            'total_electrical' => round($totalElectricalCost, 0),
            'cost_per_m2' => round($totalElectricalCost / $totalArea, 2)
        ],
        'mep_integration' => $mepIntegration,
        'compliance_standards' => $complianceStandards,
        'maintenance_lifecycle' => $maintenanceLifecycle,
        'recommendations' => generateElectricalRecommendations($buildingType, $totalDemandLoad, $powerQuality, $energyEfficiency)
    ];
    
    $results = true;
}

function calculateDemandFactor($buildingType, $totalLoad) {
    $demandFactors = [
        'residential' => 0.6,
        'commercial' => 0.7,
        'industrial' => 0.8,
        'hospital' => 0.75,
        'data_center' => 0.9
    ];
    
    return $demandFactors[$buildingType] ?: 0.7;
}

function generateElectricalRecommendations($buildingType, $demandLoad, $powerQuality, $efficiency) {
    $recommendations = [];
    
    if ($demandLoad > 1000) {
        $recommendations[] = 'Consider medium voltage distribution for loads over 1000kW';
    }
    
    if ($powerQuality['power_factor'] < 0.9) {
        $recommendations[] = 'Install power factor correction capacitors to improve power factor';
    }
    
    if ($buildingType === 'data_center' || $buildingType === 'hospital') {
        $recommendations[] = 'Implement redundant electrical systems for critical loads';
        $recommendations[] = 'Install online UPS systems for sensitive equipment';
    }
    
    if ($efficiency['led_lighting']['energy_savings'] > 50) {
        $recommendations[] = 'LED lighting provides significant energy and maintenance savings';
    }
    
    $recommendations[] = 'Implement smart building automation for energy management';
    $recommendations[] = 'Consider renewable energy integration (solar PV)';
    $recommendations[] = 'Install surge protection devices for sensitive electronics';
    $recommendations[] = 'Implement electrical monitoring and analytics platform';
    $recommendations[] = 'Ensure compliance with latest electrical codes and standards';
    
    return $recommendations;
}

include '../../../themes/default/views/partials/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><i class="icon-cogs"></i> MEP Electrical Systems Summary</h1>
        <p>Comprehensive electrical systems analysis and coordination</p>
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
                                    <option value="data_center" <?php echo $buildingType === 'data_center' ? 'selected' : ''; ?>>Data Center</option>
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
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="icon-calculator"></i> Generate Electrical Summary
                        </button>
                        
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <?php if ($results): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="icon-chart-line"></i> Electrical Systems Overview</h3>
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
                            <h4>Load Analysis</h4>
                            <table class="table table-condensed">
                                <tr><td>Lighting</td><td><?php echo number_format($calculations['load_analysis']['lighting_load']); ?> kW</td></tr>
                                <tr><td>Power</td><td><?php echo number_format($calculations['load_analysis']['power_load']); ?> kW</td></tr>
                                <tr><td>HVAC</td><td><?php echo number_format($calculations['load_analysis']['hvac_load']); ?> kW</td></tr>
                                <tr><td>Miscellaneous</td><td><?php echo number_format($calculations['load_analysis']['misc_load']); ?> kW</td></tr>
                                <tr><td><strong>Total Demand</strong></td><td><strong><?php echo number_format($calculations['load_analysis']['total_demand']); ?> kW</strong></td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <h4>System Configuration</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Voltage Levels</h5>
                            <p><strong>Service:</strong> <?php echo $calculations['system_configuration']['voltage_levels']['service_voltage']; ?>V</p>
                            <p><strong>Distribution:</strong> <?php echo $calculations['system_configuration']['voltage_levels']['distribution_voltage']; ?>V</p>
                            <p><strong>Branch Circuits:</strong> <?php echo $calculations['system_configuration']['voltage_levels']['branch_circuit_voltage']; ?>V</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Power Quality</h5>
                            <p><strong>Voltage Regulation:</strong> ±<?php echo $calculations['system_configuration']['power_quality']['voltage_regulation']; ?>%</p>
                            <p><strong>Power Factor:</strong> <?php echo $calculations['system_configuration']['power_quality']['power_factor']; ?></p>
                            <p><strong>THD Limit:</strong> <?php echo $calculations['system_configuration']['power_quality']['harmonics_thd']; ?>%</p>
                        </div>
                    </div>
                    
                    <h4>Protection & Coordination</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Main Protection</h5>
                            <p><strong>Device:</strong> <?php echo $calculations['protection_coordination']['main_protection']['device_type']; ?></p>
                            <p><strong>Rating:</strong> <?php echo $calculations['protection_coordination']['main_protection']['rating_amps']; ?>A</p>
                            <p><strong>Interrupting Capacity:</strong> <?php echo number_format($calculations['protection_coordination']['main_protection']['interrupting_capacity']); ?>A</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Distribution Protection</h5>
                            <p><strong>Device:</strong> <?php echo $calculations['protection_coordination']['distribution_protection']['device_type']; ?></p>
                            <p><strong>Rating:</strong> <?php echo $calculations['protection_coordination']['distribution_protection']['rating_amps']; ?>A</p>
                            <p><strong>Interrupting Capacity:</strong> <?php echo number_format($calculations['protection_coordination']['distribution_protection']['interrupting_capacity']); ?>A</p>
                        </div>
                    </div>
                    
                    <h4>Energy Efficiency Measures</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>LED Lighting Savings:</strong> <?php echo $calculations['energy_efficiency']['led_lighting']['energy_savings']; ?>%</p>
                            <p><strong>VFD Motor Savings:</strong> <?php echo $calculations['energy_efficiency']['variable_frequency_drives']['energy_savings']; ?>%</p>
                            <p><strong>Target Power Factor:</strong> <?php echo $calculations['energy_efficiency']['power_factor_correction']['target_power_factor']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>LED Lifespan:</strong> <?php echo number_format($calculations['energy_efficiency']['led_lighting']['lifespan_hours']); ?> hours</p>
                            <p><strong>Capacitor Rating:</strong> <?php echo $calculations['energy_efficiency']['power_factor_correction']['capacitor_rating_kvar']; ?> kVAR</p>
                            <p><strong>Smart Metering:</strong> <?php echo $calculations['energy_efficiency']['smart_metering']['real_time_monitoring'] ? 'Yes' : 'No'; ?></p>
                        </div>
                    </div>
                    
                    <h4>Cost Analysis</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Infrastructure:</strong> $<?php echo number_format(array_sum($calculations['cost_estimation']['breakdown']['electrical_infrastructure'])); ?></p>
                            <p><strong>Equipment:</strong> $<?php echo number_format(array_sum($calculations['cost_estimation']['breakdown']['equipment_and_devices'])); ?></p>
                            <p><strong>Special Systems:</strong> $<?php echo number_format(array_sum($calculations['cost_estimation']['breakdown']['special_systems'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Installation:</strong> $<?php echo number_format(array_sum($calculations['cost_estimation']['breakdown']['installation_and_commissioning'])); ?></p>
                            <p><strong>Total Cost:</strong> <span class="text-primary"><strong>$<?php echo number_format($calculations['cost_estimation']['total_electrical']); ?></strong></span></p>
                            <p><strong>Cost per m²:</strong> $<?php echo $calculations['cost_estimation']['cost_per_m2']; ?></p>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><i class="icon-building"></i> Electrical Room Requirements</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Main Electrical Room</h5>
                                    <p><strong>Area:</strong> <?php echo $calculations['system_configuration']['electrical_rooms']['main_electrical_room']['area_m2']; ?> m²</p>
                                    <p><strong>Height:</strong> <?php echo $calculations['system_configuration']['electrical_rooms']['main_electrical_room']['clear_height_m']; ?> m</p>
                                    <p><strong>Fire Rating:</strong> <?php echo $calculations['system_configuration']['electrical_rooms']['main_electrical_room']['fire_rating']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Branch Rooms</h5>
                                    <p><strong>Quantity:</strong> <?php echo $calculations['system_configuration']['electrical_rooms']['branch_electrical_rooms']['quantity']; ?></p>
                                    <p><strong>Area each:</strong> <?php echo $calculations['system_configuration']['electrical_rooms']['branch_electrical_rooms']['area_m2']; ?> m²</p>
                                    <p><strong>Fire Rating:</strong> <?php echo $calculations['system_configuration']['electrical_rooms']['branch_electrical_rooms']['fire_rating']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h4><i class="icon-shield"></i> Safety Requirements</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Ground Fault Protection:</strong> <?php echo $calculations['safety_requirements']['ground_fault_protection']['required'] ? 'Required' : 'Not Required'; ?></p>
                                    <p><strong>Arc Fault Protection:</strong> <?php echo $calculations['safety_requirements']['arc_fault_protection']['required'] ? 'Required' : 'Not Required'; ?></p>
                                    <p><strong>Emergency Power:</strong> <?php echo $calculations['safety_requirements']['emergency_systems']['emergency_power']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Service SPD:</strong> <?php echo $calculations['safety_requirements']['surge_protection']['service_entrance']; ?></p>
                                    <p><strong>Distribution SPD:</strong> <?php echo $calculations['safety_requirements']['surge_protection']['distribution']; ?></p>
                                    <p><strong>Emergency Lighting:</strong> <?php echo $calculations['safety_requirements']['emergency_systems']['emergency_lighting']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h4><i class="icon-leaf"></i> MEP Integration</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5>Mechanical</h5>
                                    <ul class="list-unstyled">
                                        <li>• HVAC Controls</li>
                                        <li>• Motor Controls</li>
                                        <li>• Power Requirements</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h5>Plumbing</h5>
                                    <ul class="list-unstyled">
                                        <li>• Pump Controls</li>
                                        <li>• Water Heater Power</li>
                                        <li>• Sump Pump Power</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h5>Fire Protection</h5>
                                    <ul class="list-unstyled">
                                        <li>• Fire Pump Power</li>
                                        <li>• Sprinkler Controls</li>
                                        <li>• Fire Alarm Integration</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel panel-primary">
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
                        <button type="button" class="btn btn-success btn-lg" onclick="printSummary()">
                            <i class="icon-print"></i> Print Summary Report
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
function printSummary() {
    window.print();
}

function exportToPDF() {
    // Create a hidden form for PDF export
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'export-electrical-summary.php';
    
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
    
    localStorage.setItem('mep_electrical_summary', JSON.stringify(configData));
    
    // Show success message
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
    const saved = localStorage.getItem('mep_electrical_summary');
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

// Load saved configuration on page load
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

