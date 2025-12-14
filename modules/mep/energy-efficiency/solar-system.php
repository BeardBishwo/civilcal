<?php
/**
 * Solar Energy System Design & Analysis
 * Comprehensive solar energy system design, calculations, and optimization
 * Solar panel sizing, energy generation, financial analysis, and system integration
 */

require_once '../../../app/Config/config.php';
require_once '../../../app/Core/DatabaseLegacy.php';
require_once '../../../app/Helpers/functions.php';

// Initialize database connection
$db = new Database();

// Get project data
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

// Handle form submissions
$message = '';
$message_type = '';

if ($_POST) {
    try {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'calculate_system':
                $result = calculateSolarSystem($_POST);
                $message = 'Solar system calculated successfully!';
                $message_type = 'success';
                $solar_data = $result;
                break;
                
            case 'optimize_system':
                $result = optimizeSolarSystem($_POST);
                $message = 'Solar system optimization completed!';
                $message_type = 'success';
                $optimization_data = $result;
                break;
                
            case 'financial_analysis':
                $result = calculateFinancialAnalysis($_POST);
                $message = 'Financial analysis completed!';
                $message_type = 'success';
                $financial_data = $result;
                break;
                
            case 'save_project':
                $result = saveSolarProject($_POST, $project_id);
                if ($result) {
                    $message = 'Project saved successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error saving project.';
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get saved projects
$saved_projects = [];
if ($project_id > 0) {
    $query = "SELECT * FROM mep_solar_projects WHERE id = ?";
    $stmt = $db->executeQuery($query, [$project_id]);
    $saved_projects = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

/**
 * Calculate comprehensive solar system design
 */
function calculateSolarSystem($data) {
    // Building and location parameters
    $building_area = floatval($data['building_area'] ?? 0);
    $roof_area = floatval($data['roof_area'] ?? 0);
    $latitude = floatval($data['latitude'] ?? 0);
    $longitude = floatval($data['longitude'] ?? 0);
    $climate_zone = $data['climate_zone'] ?? 'temperate';
    
    // System requirements
    $daily_energy_consumption = floatval($data['daily_energy_consumption'] ?? 0);
    $peak_power_demand = floatval($data['peak_power_demand'] ?? 0);
    $backup_days = intval($data['backup_days'] ?? 3);
    
    // System configuration
    $system_type = $data['system_type'] ?? 'grid_tied';
    $panel_type = $data['panel_type'] ?? 'monocrystalline';
    $tilt_angle = floatval($data['tilt_angle'] ?? 30);
    $azimuth = floatval($data['azimuth'] ?? 180); // South-facing
    
    // Economic parameters
    $electricity_rate = floatval($data['electricity_rate'] ?? 0.12);
    $electricity_escalation = floatval($data['electricity_escalation'] ?? 3);
    
    // Calculate solar irradiance based on location and climate
    $solar_irradiance = calculateSolarIrradiance($latitude, $climate_zone);
    
    // Calculate optimal system sizing
    $required_capacity = calculateRequiredCapacity($daily_energy_consumption, $peak_power_demand);
    
    // Panel calculations
    $panel_specs = getPanelSpecifications($panel_type);
    $number_of_panels = calculateNumberOfPanels($required_capacity, $panel_specs);
    $actual_capacity = $number_of_panels * $panel_specs['capacity'];
    
    // Energy generation calculations
    $daily_generation = calculateDailyGeneration($actual_capacity, $solar_irradiance, $tilt_angle, $azimuth);
    $annual_generation = $daily_generation * 365;
    
    // Battery calculations (if applicable)
    $battery_capacity = 0;
    $battery_cost = 0;
    if ($system_type === 'off_grid' || $system_type === 'hybrid') {
        $battery_type = $data['battery_type'] ?? 'lithium';
        $battery_specs = getBatterySpecifications($battery_type);
        $battery_capacity = calculateBatteryCapacity($daily_energy_consumption, $backup_days, $battery_specs, $battery_type);
        $battery_cost = $battery_capacity * $battery_specs['cost_per_kwh'];
    }
    
    // System costs
    $panel_cost = $number_of_panels * $panel_specs['cost'];
    $inverter_cost = calculateInverterCost($actual_capacity);
    $installation_cost = ($panel_cost + $inverter_cost) * 0.3; // 30% installation cost
    $balance_of_system = ($panel_cost + $inverter_cost) * 0.2; // 20% BOS cost
    
    $total_system_cost = $panel_cost + $inverter_cost + $installation_cost + $balance_of_system + $battery_cost;
    
    // Performance metrics
    $capacity_factor = ($daily_generation / $actual_capacity) / 24 * 100; // Percentage
    $system_efficiency = calculateSystemEfficiency($panel_specs['efficiency'], $system_type);
    
    // Coverage analysis
    $energy_coverage = ($daily_generation / $daily_energy_consumption) * 100;
    
    return [
        'system_specs' => [
            'total_capacity' => $actual_capacity,
            'number_of_panels' => $number_of_panels,
            'panel_type' => $panel_type,
            'system_type' => $system_type,
            'roof_utilization' => ($number_of_panels * 2) / $roof_area * 100, // Assuming 2m² per panel
        ],
        'performance' => [
            'daily_generation' => $daily_generation,
            'annual_generation' => $annual_generation,
            'capacity_factor' => $capacity_factor,
            'system_efficiency' => $system_efficiency,
            'energy_coverage' => $energy_coverage
        ],
        'costs' => [
            'panel_cost' => $panel_cost,
            'inverter_cost' => $inverter_cost,
            'installation_cost' => $installation_cost,
            'balance_of_system' => $balance_of_system,
            'battery_cost' => $battery_cost,
            'total_system_cost' => $total_system_cost,
            'cost_per_watt' => $total_system_cost / $actual_capacity
        ],
        'environmental_impact' => [
            'co2_avoided_annual' => $annual_generation * 0.5, // kg CO2/year
            'co2_avoided_lifetime' => $annual_generation * 25 * 0.5, // 25-year lifetime
            'trees_equivalent' => ($annual_generation * 25 * 0.5) / 21.77 // Trees to offset lifetime CO2
        ]
    ];
}

/**
 * Calculate solar irradiance based on location and climate
 */
function calculateSolarIrradiance($latitude, $climate_zone) {
    // Base irradiance values (kWh/m²/day) by climate zone
    $climate_factors = [
        'desert' => 6.5,
        'tropical' => 5.8,
        'temperate' => 4.2,
        'continental' => 3.8,
        'maritime' => 3.2,
        'polar' => 2.1
    ];
    
    $base_irradiance = $climate_factors[$climate_zone] ?? 4.0;
    
    // Latitude adjustment (simplified model)
    $latitude_factor = 1 - (abs($latitude) / 90) * 0.3;
    
    return $base_irradiance * $latitude_factor;
}

/**
 * Calculate required system capacity
 */
function calculateRequiredCapacity($daily_consumption, $peak_demand) {
    // Estimate required capacity based on daily consumption and peak demand
    $capacity_from_consumption = $daily_consumption / 5; // Assuming 5 hours of peak sun
    $capacity_from_peak = $peak_demand * 1.3; // 30% safety margin
    
    return max($capacity_from_consumption, $capacity_from_peak);
}

/**
 * Get panel specifications by type
 */
function getPanelSpecifications($panel_type) {
    $specifications = [
        'monocrystalline' => [
            'capacity' => 0.4, // kW
            'efficiency' => 0.22,
            'cost' => 180,
            'lifespan' => 25,
            'degradation' => 0.005
        ],
        'polycrystalline' => [
            'capacity' => 0.35,
            'efficiency' => 0.18,
            'cost' => 150,
            'lifespan' => 20,
            'degradation' => 0.007
        ],
        'thin_film' => [
            'capacity' => 0.3,
            'efficiency' => 0.12,
            'cost' => 120,
            'lifespan' => 15,
            'degradation' => 0.01
        ]
    ];
    
    return $specifications[$panel_type] ?? $specifications['monocrystalline'];
}

/**
 * Calculate number of panels required
 */
function calculateNumberOfPanels($required_capacity, $panel_specs) {
    return ceil($required_capacity / $panel_specs['capacity']);
}

/**
 * Calculate daily energy generation
 */
function calculateDailyGeneration($capacity, $solar_irradiance, $tilt_angle, $azimuth) {
    // Tilt angle optimization factor
    $tilt_factor = 1 - (abs($tilt_angle - 30) / 30) * 0.1;
    
    // Azimuth factor (optimal at 180° for northern hemisphere)
    $azimuth_factor = 1 - (abs($azimuth - 180) / 180) * 0.15;
    
    // System losses (inverter, wiring, soiling, etc.)
    $system_losses = 0.85;
    
    $daily_generation = $capacity * $solar_irradiance * $tilt_factor * $azimuth_factor * $system_losses;
    
    return $daily_generation;
}

/**
 * Calculate inverter cost
 */
function calculateInverterCost($system_capacity) {
    // Inverter cost decreases with larger systems
    $cost_per_kw = 250;
    if ($system_capacity > 10) $cost_per_kw = 200;
    if ($system_capacity > 50) $cost_per_kw = 150;
    if ($system_capacity > 100) $cost_per_kw = 120;
    
    return $system_capacity * $cost_per_kw;
}

/**
 * Get battery specifications
 */
function getBatterySpecifications($battery_type) {
    $specifications = [
        'lithium' => [
            'cost_per_kwh' => 600,
            'efficiency' => 0.95,
            'cycles' => 5000,
            'lifespan' => 10
        ],
        'lead_acid' => [
            'cost_per_kwh' => 150,
            'efficiency' => 0.85,
            'cycles' => 1500,
            'lifespan' => 5
        ],
        'flow' => [
            'cost_per_kwh' => 400,
            'efficiency' => 0.75,
            'cycles' => 10000,
            'lifespan' => 15
        ]
    ];
    
    return $specifications[$battery_type] ?? $specifications['lithium'];
}

/**
 * Calculate battery capacity requirement
 */
function calculateBatteryCapacity($daily_consumption, $backup_days, $battery_specs, $battery_type) {
    // Account for battery efficiency
    $usable_capacity = $daily_consumption * $backup_days / $battery_specs['efficiency'];
    
    // Add depth of discharge limitation (80% for lithium, 50% for lead acid)
    $max_discharge = $battery_type === 'lithium' ? 0.8 : 0.5;
    
    return $usable_capacity / $max_discharge;
}

/**
 * Calculate system efficiency
 */
function calculateSystemEfficiency($panel_efficiency, $system_type) {
    $system_efficiency = $panel_efficiency;
    
    // Apply system losses
    switch ($system_type) {
        case 'grid_tied':
            $system_efficiency *= 0.85; // 15% system losses
            break;
        case 'off_grid':
            $system_efficiency *= 0.75; // 25% system losses (battery + inverter)
            break;
        case 'hybrid':
            $system_efficiency *= 0.80; // 20% system losses
            break;
    }
    
    return $system_efficiency;
}

/**
 * Optimize solar system design
 */
function optimizeSolarSystem($data) {
    $base_calculation = calculateSolarSystem($data);
    
    $optimizations = [];
    
    // Panel orientation optimization
    $optimal_tilt = calculateOptimalTilt($data['latitude'] ?? 0);
    $optimal_azimuth = calculateOptimalAzimuth($data['latitude'] ?? 0);
    
    $optimizations['orientation'] = [
        'current_tilt' => $data['tilt_angle'] ?? 30,
        'optimal_tilt' => $optimal_tilt,
        'current_azimuth' => $data['azimuth'] ?? 180,
        'optimal_azimuth' => $optimal_azimuth,
        'improvement_potential' => 15 // Estimated 15% improvement
    ];
    
    // Panel type optimization
    $best_panel_type = determineBestPanelType($data['budget_range'] ?? 'medium');
    $current_panel_cost = getPanelSpecifications($data['panel_type'] ?? 'monocrystalline')['cost'];
    $best_panel_cost = getPanelSpecifications($best_panel_type)['cost'];
    
    $optimizations['panel_type'] = [
        'current_type' => $data['panel_type'] ?? 'monocrystalline',
        'recommended_type' => $best_panel_type,
        'cost_difference' => $best_panel_cost - $current_panel_cost,
        'efficiency_improvement' => getPanelSpecifications($best_panel_type)['efficiency'] - 
                                  getPanelSpecifications($data['panel_type'] ?? 'monocrystalline')['efficiency']
    ];
    
    // System configuration optimization
    $system_config = optimizeSystemConfiguration($data);
    $optimizations['system_config'] = $system_config;
    
    return [
        'base_calculation' => $base_calculation,
        'optimizations' => $optimizations,
        'total_improvement' => calculateTotalImprovement($optimizations),
        'implementation_cost' => estimateOptimizationCost($optimizations)
    ];
}

/**
 * Calculate optimal tilt angle based on latitude
 */
function calculateOptimalTilt($latitude) {
    // Simplified model: optimal tilt = latitude ± 15° depending on season
    return abs($latitude) + 15;
}

/**
 * Calculate optimal azimuth
 */
function calculateOptimalAzimuth($latitude) {
    // South-facing for northern hemisphere (180°), North-facing for southern hemisphere (0°)
    return $latitude >= 0 ? 180 : 0;
}

/**
 * Determine best panel type based on budget
 */
function determineBestPanelType($budget_range) {
    switch ($budget_range) {
        case 'low':
            return 'polycrystalline';
        case 'high':
            return 'monocrystalline';
        default:
            return 'monocrystalline';
    }
}

/**
 * Optimize system configuration
 */
function optimizeSystemConfiguration($data) {
    $building_area = floatval($data['building_area'] ?? 0);
    $daily_consumption = floatval($data['daily_energy_consumption'] ?? 0);
    
    // Recommend system type based on building characteristics
    $recommended_system = 'grid_tied';
    if ($building_area > 1000 && $daily_consumption > 100) {
        $recommended_system = 'hybrid';
    }
    
    return [
        'recommended_system_type' => $recommended_system,
        'current_system_type' => $data['system_type'] ?? 'grid_tied',
        'reasoning' => 'Recommended based on building size and energy consumption patterns'
    ];
}

/**
 * Calculate total improvement potential
 */
function calculateTotalImprovement($optimizations) {
    $total_improvement = 0;
    
    // Add improvements from each optimization
    if (isset($optimizations['orientation']['improvement_potential'])) {
        $total_improvement += $optimizations['orientation']['improvement_potential'];
    }
    
    if (isset($optimizations['panel_type']['efficiency_improvement'])) {
        $total_improvement += $optimizations['panel_type']['efficiency_improvement'] * 100;
    }
    
    return $total_improvement;
}

/**
 * Estimate optimization implementation cost
 */
function estimateOptimizationCost($optimizations) {
    $cost = 0;
    
    // Cost for reorientation
    if (isset($optimizations['orientation'])) {
        $cost += 2000; // Estimated cost for optimization
    }
    
    // Cost for panel upgrade
    if (isset($optimizations['panel_type'])) {
        $cost += $optimizations['panel_type']['cost_difference'] * 20; // 20 panels estimated
    }
    
    return $cost;
}

/**
 * Calculate financial analysis
 */
function calculateFinancialAnalysis($data) {
    $system_calculation = calculateSolarSystem($data);
    
    // Economic parameters
    $system_cost = $system_calculation['costs']['total_system_cost'];
    $annual_generation = $system_calculation['performance']['annual_generation'];
    $electricity_rate = floatval($data['electricity_rate'] ?? 0.12);
    $electricity_escalation = floatval($data['electricity_escalation'] ?? 3);
    $system_lifespan = 25;
    
    // Annual savings
    $annual_savings = $annual_generation * $electricity_rate;
    
    // Calculate NPV (Net Present Value)
    $discount_rate = 0.07; // 7% discount rate
    $npv = 0;
    $cumulative_savings = 0;
    
    for ($year = 1; $year <= $system_lifespan; $year++) {
        $escalated_rate = $electricity_rate * pow(1 + $electricity_escalation/100, $year - 1);
        $yearly_savings = $annual_generation * $escalated_rate;
        $cumulative_savings += $yearly_savings;
        
        // Apply system degradation
        $degradation = 1 - (0.005 * ($year - 1)); // 0.5% annual degradation
        $adjusted_savings = $yearly_savings * $degradation;
        
        $npv += $adjusted_savings / pow(1 + $discount_rate, $year);
    }
    
    $net_benefit = $npv - $system_cost;
    
    // Payback period calculation
    $payback_period = $system_cost / $annual_savings;
    
    // ROI calculations
    $simple_roi = ($cumulative_savings - $system_cost) / $system_cost * 100;
    
    // Additional financial metrics
    $levelized_cost_of_energy = $system_cost / ($annual_generation * $system_lifespan);
    $internal_rate_of_return = calculateIRR($system_cost, $annual_savings, $system_lifespan);
    
    return [
        'basic_metrics' => [
            'total_investment' => $system_cost,
            'annual_savings' => $annual_savings,
            'payback_period' => $payback_period,
            'net_present_value' => $npv,
            'net_benefit' => $net_benefit,
            'simple_roi' => $simple_roi
        ],
        'advanced_metrics' => [
            'levelized_cost_of_energy' => $levelized_cost_of_energy,
            'internal_rate_of_return' => $internal_rate_of_return,
            'cumulative_savings' => $cumulative_savings,
            'break_even_year' => ceil($payback_period)
        ],
        'sensitivity_analysis' => performSensitivityAnalysis($system_cost, $annual_generation, $electricity_rate),
        'financing_options' => calculateFinancingOptions($system_cost)
    ];
}

/**
 * Calculate Internal Rate of Return (simplified)
 */
function calculateIRR($initial_investment, $annual_cash_flow, $years) {
    $irr = 0.05; // Start with 5%
    
    for ($i = 0; $i < 100; $i++) { // 100 iterations
        $npv = 0;
        for ($year = 1; $year <= $years; $year++) {
            $npv += $annual_cash_flow / pow(1 + $irr, $year);
        }
        $npv -= $initial_investment;
        
        if (abs($npv) < 1) break;
        
        // Adjust IRR based on NPV
        if ($npv > 0) {
            $irr += 0.001;
        } else {
            $irr -= 0.001;
        }
    }
    
    return $irr * 100; // Return as percentage
}

/**
 * Perform sensitivity analysis
 */
function performSensitivityAnalysis($base_cost, $base_generation, $base_rate) {
    $scenarios = [
        'pessimistic' => [
            'cost_factor' => 1.2,
            'generation_factor' => 0.8,
            'rate_factor' => 0.9
        ],
        'base_case' => [
            'cost_factor' => 1.0,
            'generation_factor' => 1.0,
            'rate_factor' => 1.0
        ],
        'optimistic' => [
            'cost_factor' => 0.9,
            'generation_factor' => 1.2,
            'rate_factor' => 1.1
        ]
    ];
    
    $results = [];
    foreach ($scenarios as $scenario => $factors) {
        $adjusted_cost = $base_cost * $factors['cost_factor'];
        $adjusted_generation = $base_generation * $factors['generation_factor'];
        $adjusted_rate = $base_rate * $factors['rate_factor'];
        
        $annual_savings = $adjusted_generation * $adjusted_rate;
        $payback_period = $adjusted_cost / $annual_savings;
        
        $results[$scenario] = [
            'cost' => $adjusted_cost,
            'generation' => $adjusted_generation,
            'rate' => $adjusted_rate,
            'payback_period' => $payback_period,
            'roi' => ($annual_savings * 25 - $adjusted_cost) / $adjusted_cost * 100
        ];
    }
    
    return $results;
}

/**
 * Calculate financing options
 */
function calculateFinancingOptions($system_cost) {
    $financing_options = [
        'cash_purchase' => [
            'down_payment' => $system_cost,
            'monthly_payment' => 0,
            'total_interest' => 0,
            'total_cost' => $system_cost
        ],
        'solar_loan' => [
            'down_payment' => $system_cost * 0.2, // 20% down
            'loan_amount' => $system_cost * 0.8,
            'interest_rate' => 0.06, // 6% APR
            'term_years' => 10,
            'monthly_payment' => 0,
            'total_interest' => 0,
            'total_cost' => 0
        ],
        'lease' => [
            'down_payment' => 0,
            'monthly_payment' => $system_cost * 0.002, // Estimated lease payment
            'term_years' => 20,
            'total_cost' => 0
        ],
        'power_purchase_agreement' => [
            'down_payment' => 0,
            'monthly_payment' => $system_cost * 0.0015, // Estimated PPA rate
            'term_years' => 20,
            'total_cost' => 0
        ]
    ];
    
    // Calculate loan payments
    if (isset($financing_options['solar_loan'])) {
        $loan = $financing_options['solar_loan'];
        $monthly_rate = $loan['interest_rate'] / 12;
        $num_payments = $loan['term_years'] * 12;
        $monthly_payment = $loan['loan_amount'] * ($monthly_rate * pow(1 + $monthly_rate, $num_payments)) / 
                          (pow(1 + $monthly_rate, $num_payments) - 1);
        
        $total_payments = $monthly_payment * $num_payments;
        $total_interest = $total_payments - $loan['loan_amount'];
        
        $financing_options['solar_loan']['monthly_payment'] = $monthly_payment;
        $financing_options['solar_loan']['total_interest'] = $total_interest;
        $financing_options['solar_loan']['total_cost'] = $loan['down_payment'] + $total_payments;
    }
    
    // Calculate other options total costs
    foreach ($financing_options as $option => &$details) {
        if ($option !== 'cash_purchase' && $option !== 'solar_loan') {
            $total_cost = $details['monthly_payment'] * $details['term_years'] * 12;
            $details['total_cost'] = $total_cost;
        }
    }
    
    return $financing_options;
}

/**
 * Save solar project data
 */
function saveSolarProject($data, $project_id) {
    global $db;
    
    $project_data = [
        'building_area' => floatval($data['building_area']),
        'roof_area' => floatval($data['roof_area']),
        'latitude' => floatval($data['latitude']),
        'longitude' => floatval($data['longitude']),
        'climate_zone' => $data['climate_zone'],
        'daily_energy_consumption' => floatval($data['daily_energy_consumption']),
        'peak_power_demand' => floatval($data['peak_power_demand']),
        'backup_days' => intval($data['backup_days']),
        'system_type' => $data['system_type'],
        'panel_type' => $data['panel_type'],
        'tilt_angle' => floatval($data['tilt_angle']),
        'azimuth' => floatval($data['azimuth']),
        'electricity_rate' => floatval($data['electricity_rate']),
        'electricity_escalation' => floatval($data['electricity_escalation']),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if ($project_id > 0) {
        $query = "UPDATE mep_solar_projects SET " . 
                 implode(', ', array_map(fn($k) => "$k = ?", array_keys($project_data))) . 
                 " WHERE id = ?";
        $stmt = $db->executeQuery($query, array_merge(array_values($project_data), [$project_id]));
        return $stmt !== false;
    } else {
        $query = "INSERT INTO mep_solar_projects (" . 
                 implode(', ', array_keys($project_data)) . 
                 ", created_at) VALUES (" . 
                 implode(', ', array_fill(0, count($project_data), '?')) . 
                 ", NOW())";
        $stmt = $db->executeQuery($query, array_values($project_data));
        return $stmt !== false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solar Energy System Design - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
    <style>
        .solar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #FF6F00, #FF8F00);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .solar-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #FF6F00;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn {
            background: #FF6F00;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 10px 10px 0;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #E65100;
        }
        
        .btn-secondary {
            background: #666;
        }
        
        .btn-secondary:hover {
            background: #555;
        }
        
        .results-section {
            display: none;
            margin-top: 30px;
        }
        
        .results-section.active {
            display: block;
        }
        
        .system-specs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .spec-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .spec-value {
            font-size: 24px;
            font-weight: 600;
            color: #FF6F00;
        }
        
        .spec-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .performance-metrics {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .cost-breakdown {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .cost-item {
            background: #f3e5f5;
            border: 1px solid #e1bee7;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .cost-amount {
            font-size: 20px;
            font-weight: 600;
            color: #7B1FA2;
        }
        
        .cost-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .environmental-impact {
            background: #e8f5e8;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .optimization-item {
            background: #fff3e0;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #FF6F00;
        }
        
        .financial-summary {
            background: #f1f8e9;
            border: 1px solid #dcedc8;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .savings-highlight {
            font-size: 28px;
            font-weight: 600;
            color: #388E3C;
        }
        
        .chart-container {
            height: 300px;
            margin: 20px 0;
            position: relative;
        }
        
        .financing-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .financing-option {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        
        .financing-option:hover {
            border-color: #FF6F00;
        }
        
        .financing-option.selected {
            border-color: #FF6F00;
            background: #fff3e0;
        }
        
        .option-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .option-details {
            font-size: 14px;
            color: #666;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .solar-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <?php include '../../../themes/default/views/partials/header.php'; ?>
    
    <div class="solar-container">
        <div class="page-header">
            <h1>Solar Energy System Design & Analysis</h1>
            <p>Comprehensive solar system design, optimization, and financial analysis</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="solar-grid">
            <!-- Input Form -->
            <div class="card">
                <div class="card-header">Building & Location Parameters</div>
                
                <form method="POST" id="solar-form">
                    <input type="hidden" name="action" value="calculate_system">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="building_area">Building Area (m²)</label>
                            <input type="number" id="building_area" name="building_area" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['building_area'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="roof_area">Available Roof Area (m²)</label>
                            <input type="number" id="roof_area" name="roof_area" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['roof_area'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="latitude">Latitude (°)</label>
                            <input type="number" id="latitude" name="latitude" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['latitude'] ?? ''); ?>" 
                                   step="0.01" min="-90" max="90" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="longitude">Longitude (°)</label>
                            <input type="number" id="longitude" name="longitude" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['longitude'] ?? ''); ?>" 
                                   step="0.01" min="-180" max="180" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="climate_zone">Climate Zone</label>
                            <select id="climate_zone" name="climate_zone" required>
                                <option value="desert" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'desert') ? 'selected' : ''; ?>>Desert</option>
                                <option value="tropical" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'tropical') ? 'selected' : ''; ?>>Tropical</option>
                                <option value="temperate" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'temperate') ? 'selected' : ''; ?>>Temperate</option>
                                <option value="continental" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'continental') ? 'selected' : ''; ?>>Continental</option>
                                <option value="maritime" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'maritime') ? 'selected' : ''; ?>>Maritime</option>
                                <option value="polar" <?php echo (($saved_projects[0]['climate_zone'] ?? '') === 'polar') ? 'selected' : ''; ?>>Polar</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="system_type">System Type</label>
                            <select id="system_type" name="system_type" required>
                                <option value="grid_tied" <?php echo (($saved_projects[0]['system_type'] ?? '') === 'grid_tied') ? 'selected' : ''; ?>>Grid-Tied</option>
                                <option value="off_grid" <?php echo (($saved_projects[0]['system_type'] ?? '') === 'off_grid') ? 'selected' : ''; ?>>Off-Grid</option>
                                <option value="hybrid" <?php echo (($saved_projects[0]['system_type'] ?? '') === 'hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="panel_type">Panel Type</label>
                            <select id="panel_type" name="panel_type" required>
                                <option value="monocrystalline" <?php echo (($saved_projects[0]['panel_type'] ?? '') === 'monocrystalline') ? 'selected' : ''; ?>>Monocrystalline</option>
                                <option value="polycrystalline" <?php echo (($saved_projects[0]['panel_type'] ?? '') === 'polycrystalline') ? 'selected' : ''; ?>>Polycrystalline</option>
                                <option value="thin_film" <?php echo (($saved_projects[0]['panel_type'] ?? '') === 'thin_film') ? 'selected' : ''; ?>>Thin Film</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="budget_range">Budget Range</label>
                            <select id="budget_range" name="budget_range">
                                <option value="low">Low ($0 - $50,000)</option>
                                <option value="medium" selected>Medium ($50,000 - $200,000)</option>
                                <option value="high">High ($200,000+)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tilt_angle">Tilt Angle (°)</label>
                            <input type="number" id="tilt_angle" name="tilt_angle" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['tilt_angle'] ?? '30'); ?>" 
                                   min="0" max="90" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="azimuth">Azimuth (°)</label>
                            <input type="number" id="azimuth" name="azimuth" 
                                   value="<?php echo htmlspecialchars($saved_projects[0]['azimuth'] ?? '180'); ?>" 
                                   min="0" max="360" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Calculate Solar System</button>
                    <button type="button" class="btn btn-secondary" onclick="optimizeSystem()">Optimize System</button>
                </form>
            </div>
            
            <!-- System Overview Chart -->
            <div class="card">
                <div class="card-header">System Performance Overview</div>
                <div class="chart-container">
                    <canvas id="solarChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Results Section -->
        <div id="results-section" class="results-section">
            <div class="card">
                <div class="card-header">System Design Results</div>
                
                <div id="solar-results"></div>
            </div>
        </div>
        
        <!-- Financial Analysis Section -->
        <div id="financial-section" class="results-section">
            <div class="card">
                <div class="card-header">Financial Analysis</div>
                
                <div id="financial-results"></div>
            </div>
        </div>
    </div>
    
    <?php include '../../../themes/default/views/partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let solarChart = null;
        
        function calculateSystem() {
            const formData = new FormData(document.getElementById('solar-form'));
            formData.append('action', 'calculate_system');
            
            fetch('solar-system.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayResults(data.results);
                } else {
                    showNotification('Error calculating solar system: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error calculating solar system', 'danger');
            });
        }
        
        function optimizeSystem() {
            const formData = new FormData(document.getElementById('solar-form'));
            formData.append('action', 'optimize_system');
            
            fetch('solar-system.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayOptimizationResults(data.results);
                } else {
                    showNotification('Error optimizing system: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error optimizing system', 'danger');
            });
        }
        
        function displayResults(results) {
            document.getElementById('results-section').classList.add('active');
            
            const specsHtml = `
                <div class="system-specs">
                    <div class="spec-item">
                        <div class="spec-value">${results.system_specs.total_capacity.toFixed(1)} kW</div>
                        <div class="spec-label">System Capacity</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-value">${results.system_specs.number_of_panels}</div>
                        <div class="spec-label">Number of Panels</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-value">${results.performance.daily_generation.toFixed(1)} kWh</div>
                        <div class="spec-label">Daily Generation</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-value">${(results.performance.annual_generation/1000).toFixed(0)} MWh</div>
                        <div class="spec-label">Annual Generation</div>
                    </div>
                </div>
                
                <div class="performance-metrics">
                    <h3>Performance Metrics</h3>
                    <div class="system-specs">
                        <div class="spec-item">
                            <div class="spec-value">${results.performance.capacity_factor.toFixed(1)}%</div>
                            <div class="spec-label">Capacity Factor</div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-value">${(results.performance.system_efficiency*100).toFixed(1)}%</div>
                            <div class="spec-label">System Efficiency</div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-value">${results.performance.energy_coverage.toFixed(1)}%</div>
                            <div class="spec-label">Energy Coverage</div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-value">${results.system_specs.roof_utilization.toFixed(1)}%</div>
                            <div class="spec-label">Roof Utilization</div>
                        </div>
                    </div>
                </div>
                
                <div class="cost-breakdown">
                    <div class="cost-item">
                        <div class="cost-amount">$${results.costs.panel_cost.toLocaleString()}</div>
                        <div class="cost-label">Panel Cost</div>
                    </div>
                    <div class="cost-item">
                        <div class="cost-amount">$${results.costs.inverter_cost.toLocaleString()}</div>
                        <div class="cost-label">Inverter Cost</div>
                    </div>
                    <div class="cost-item">
                        <div class="cost-amount">$${results.costs.installation_cost.toLocaleString()}</div>
                        <div class="cost-label">Installation Cost</div>
                    </div>
                    <div class="cost-item">
                        <div class="cost-amount">$${results.costs.total_system_cost.toLocaleString()}</div>
                        <div class="cost-label">Total System Cost</div>
                    </div>
                </div>
                
                <div class="environmental-impact">
                    <h3>Environmental Impact</h3>
                    <div class="system-specs">
                        <div class="spec-item">
                            <div class="spec-value">${(results.environmental_impact.co2_avoided_annual/1000).toFixed(1)} t</div>
                            <div class="spec-label">CO₂ Avoided (Annual)</div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-value">${(results.environmental_impact.co2_avoided_lifetime/1000).toFixed(0)} t</div>
                            <div class="spec-label">CO₂ Avoided (Lifetime)</div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-value">${Math.round(results.environmental_impact.trees_equivalent)}</div>
                            <div class="spec-label">Trees Equivalent</div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('solar-results').innerHTML = specsHtml;
            createPerformanceChart(results);
        }
        
        function displayOptimizationResults(optimization) {
            const optimizationsHtml = Object.entries(optimization.optimizations).map(([type, opt]) => 
                `<div class="optimization-item">
                    <h4>${type.charAt(0).toUpperCase() + type.slice(1).replace('_', ' ')} Optimization</h4>
                    ${Object.entries(opt).map(([key, value]) => 
                        `<p><strong>${key.replace('_', ' ').toUpperCase()}:</strong> ${typeof value === 'number' ? value.toFixed(1) : value}</p>`
                    ).join('')}
                </div>`
            ).join('');
            
            const optimizationHtml = `
                <div class="financial-summary">
                    <h3>Optimization Summary</h3>
                    <div class="savings-highlight">${optimization.total_improvement.toFixed(1)}%</div>
                    <div class="spec-label">Total Performance Improvement</div>
                    <p><strong>Implementation Cost:</strong> $${optimization.implementation_cost.toLocaleString()}</p>
                </div>
                
                ${optimizationsHtml}
            `;
            
            document.getElementById('results-section').innerHTML = optimizationHtml;
            document.getElementById('results-section').classList.add('active');
        }
        
        function createPerformanceChart(results) {
            const ctx = document.getElementById('solarChart').getContext('2d');
            
            if (solarChart) {
                solarChart.destroy();
            }
            
            solarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Daily Generation (kWh)', 'Annual Generation (MWh)', 'Capacity Factor (%)'],
                    datasets: [{
                        label: 'Solar System Performance',
                        data: [
                            results.performance.daily_generation,
                            results.performance.annual_generation / 1000,
                            results.performance.capacity_factor
                        ],
                        backgroundColor: '#FF6F00'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Solar System Performance Metrics'
                        }
                    }
                }
            });
        }
        
        // Initialize chart on page load
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('solarChart').getContext('2d');
            
            solarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Daily Generation (kWh)', 'Annual Generation (MWh)', 'Capacity Factor (%)'],
                    datasets: [{
                        label: 'Solar System Performance',
                        data: [0, 0, 0],
                        backgroundColor: '#FF6F00'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Solar System Performance Metrics'
                        }
                    }
                }
            });
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>



