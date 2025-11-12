<?php
session_start();
$base = defined('APP_BASE') ? rtrim(APP_BASE, '/') : '/aec-calculator';
require_once rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . $base . '/modules/mep/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$db = new Database();
$page_title = "HVAC System Efficiency Analysis";

// Process form submission
$results = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $building_area = filter_input(INPUT_POST, 'building_area', FILTER_VALIDATE_FLOAT);
        $building_height = filter_input(INPUT_POST, 'building_height', FILTER_VALIDATE_FLOAT);
        $climate_zone = filter_input(INPUT_POST, 'climate_zone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $hvac_type = filter_input(INPUT_POST, 'hvac_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $system_age = filter_input(INPUT_POST, 'system_age', FILTER_VALIDATE_INT);
        $efficiency_rating = filter_input(INPUT_POST, 'efficiency_rating', FILTER_VALIDATE_FLOAT);
        $operating_hours = filter_input(INPUT_POST, 'operating_hours', FILTER_VALIDATE_INT);
        
        if (!$building_area || !$building_height || !$climate_zone || !$hvac_type || !$system_age || !$efficiency_rating || !$operating_hours) {
            $errors[] = "Please fill in all required fields with valid values.";
        }

        if (empty($errors)) {
            // Load energy efficiency database
            $energy_db_file = __DIR__ . '/../../db/energy_efficiency.json';
            if (file_exists($energy_db_file)) {
                $energy_data = json_decode(file_get_contents($energy_db_file), true);
            } else {
                $energy_data = [];
            }

            // Get climate zone data
            $climate_zones = [
                '1A' => ['temp_range' => [65, 95], 'humidity' => 'high', 'base_load' => 0.8],
                '2A' => ['temp_range' => [60, 90], 'humidity' => 'medium', 'base_load' => 0.7],
                '2B' => ['temp_range' => [50, 105], 'humidity' => 'low', 'base_load' => 0.6],
                '3A' => ['temp_range' => [40, 85], 'humidity' => 'medium', 'base_load' => 0.5],
                '3B' => ['temp_range' => [30, 95], 'humidity' => 'low', 'base_load' => 0.4],
                '3C' => ['temp_range' => [35, 80], 'humidity' => 'medium', 'base_load' => 0.4],
                '4A' => ['temp_range' => [25, 85], 'humidity' => 'medium', 'base_load' => 0.3],
                '4B' => ['temp_range' => [10, 90], 'humidity' => 'low', 'base_load' => 0.2],
                '4C' => ['temp_range' => [20, 80], 'humidity' => 'medium', 'base_load' => 0.3],
                '5A' => ['temp_range' => [10, 80], 'humidity' => 'medium', 'base_load' => 0.2],
                '5B' => ['temp_range' => [0, 85], 'humidity' => 'low', 'base_load' => 0.1],
                '5C' => ['temp_range' => [15, 75], 'humidity' => 'medium', 'base_load' => 0.2],
                '6A' => ['temp_range' => [0, 75], 'humidity' => 'medium', 'base_load' => 0.1],
                '6B' => ['temp_range' => [-10, 80], 'humidity' => 'low', 'base_load' => 0.1],
                '7A' => ['temp_range' => [-10, 70], 'humidity' => 'medium', 'base_load' => 0.1],
                '7B' => ['temp_range' => [-20, 75], 'humidity' => 'low', 'base_load' => 0.1],
                '8' => ['temp_range' => [-30, 70], 'humidity' => 'low', 'base_load' => 0.0]
            ];

            $climate_data = $climate_zones[$climate_zone] ?? $climate_zones['5A'];

            // Calculate system load requirements
            $cooling_load = $building_area * 0.025; // tons per sq ft
            $heating_load = $building_area * 0.02; // kW per sq ft
            $ventilation_load = $building_area * 0.15; // cfm per sq ft

            // Calculate energy consumption
            $seasonal_efficiency = 1.0;
            if ($system_age > 10) {
                $seasonal_efficiency = 0.85;
            } elseif ($system_age > 5) {
                $seasonal_efficiency = 0.92;
            }

            $actual_efficiency = $efficiency_rating * $seasonal_efficiency;
            
            // Annual energy consumption
            $cooling_energy = ($cooling_load * 12000 * $operating_hours * 0.5) / ($actual_efficiency * 3412); // kWh
            $heating_energy = ($heating_load * $building_area * $operating_hours * 0.6) / ($actual_efficiency * 3412); // kWh
            $ventilation_energy = ($ventilation_load * $operating_hours * 0.3) / 1000; // kWh
            
            $total_annual_energy = $cooling_energy + $heating_energy + $ventilation_energy;
            $annual_cost = $total_annual_energy * 0.12; // $0.12 per kWh average

            // Calculate environmental impact
            $co2_emissions = $total_annual_energy * 0.85; // lbs CO2 per year
            $trees_equivalent = $co2_emissions / 48; // trees needed to offset

            // Efficiency rating
            $target_efficiency = [
                'split' => 16,
                'package' => 14,
                'vrf' => 18,
                'chiller' => 20,
                'ptac' => 12,
                'water-source' => 17
            ];

            $target_eff = $target_efficiency[$hvac_type] ?? 14;
            $efficiency_score = min(100, ($actual_efficiency / $target_eff) * 100);
            
            // Performance rating
            $performance_rating = 'Poor';
            if ($efficiency_score >= 90) $performance_rating = 'Excellent';
            elseif ($efficiency_score >= 75) $performance_rating = 'Good';
            elseif ($efficiency_score >= 60) $performance_rating = 'Fair';

            // Generate recommendations
            $recommendations = [];
            
            if ($efficiency_score < 70) {
                $recommendations[] = "Consider upgrading to a higher efficiency HVAC system";
            }
            if ($system_age > 10) {
                $recommendations[] = "Schedule regular maintenance and consider system replacement";
            }
            if ($cooling_load > $building_area * 0.035) {
                $recommendations[] = "Improve building insulation to reduce cooling load";
            }
            if ($ventilation_load > $building_area * 0.2) {
                $recommendations[] = "Optimize ventilation system with demand-controlled ventilation";
            }
            
            // Calculate improvement potential
            $high_efficiency_systems = [
                'split' => 22,
                'package' => 20,
                'vrf' => 24,
                'chiller' => 28,
                'ptac' => 16,
                'water-source' => 23
            ];
            
            $high_eff = $high_efficiency_systems[$hvac_type] ?? 20;
            $potential_savings = (($high_eff - $actual_efficiency) / $high_eff) * $annual_cost;
            $potential_savings_percent = ($potential_savings / $annual_cost) * 100;

            // ROI calculation for system upgrade
            $upgrade_cost = $building_area * 25; // $25 per sq ft
            $payback_period = $upgrade_cost / $potential_savings;
            $npv = -$upgrade_cost + ($potential_savings * 10 * 0.07); // 10 years, 7% discount rate

            $results = [
                'building_area' => $building_area,
                'cooling_load' => round($cooling_load, 2),
                'heating_load' => round($heating_load, 2),
                'ventilation_load' => round($ventilation_load, 2),
                'actual_efficiency' => round($actual_efficiency, 2),
                'target_efficiency' => $target_eff,
                'efficiency_score' => round($efficiency_score, 1),
                'performance_rating' => $performance_rating,
                'total_annual_energy' => round($total_annual_energy, 0),
                'annual_cost' => round($annual_cost, 2),
                'co2_emissions' => round($co2_emissions, 0),
                'trees_equivalent' => round($trees_equivalent, 1),
                'recommendations' => $recommendations,
                'potential_savings' => round($potential_savings, 2),
                'potential_savings_percent' => round($potential_savings_percent, 1),
                'upgrade_cost' => round($upgrade_cost, 2),
                'payback_period' => round($payback_period, 1),
                'npv' => round($npv, 2),
                'climate_zone' => $climate_zone,
                'hvac_type' => $hvac_type
            ];

            // Save to history
            $history_data = [
                'user_id' => $_SESSION['user_id'],
                'calculation_type' => 'hvac_efficiency',
                'parameters' => $_POST,
                'results' => $results,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $stmt = $db->executeQuery(
                "INSERT INTO calculation_history (user_id, calculation_type, parameters, results, created_at) VALUES (?, ?, ?, ?, ?)",
                [
                    $_SESSION['user_id'],
                    'hvac_efficiency',
                    json_encode($_POST),
                    json_encode($results),
                    $history_data['created_at']
                ]
            );
        }
    } catch (Exception $e) {
        $errors[] = "Error processing request: " . $e->getMessage();
    }
}

include AEC_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>HVAC System Efficiency Analysis</h3>
                    <p class="text-muted">Comprehensive analysis of HVAC system performance and energy efficiency</p>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Building Information</h5>
                                
                                <div class="mb-3">
                                    <label for="building_area" class="form-label">Building Area (sq ft) *</label>
                                    <input type="number" class="form-control" id="building_area" name="building_area" 
                                           value="<?php echo htmlspecialchars($_POST['building_area'] ?? ''); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="building_height" class="form-label">Ceiling Height (ft) *</label>
                                    <input type="number" class="form-control" id="building_height" name="building_height" 
                                           value="<?php echo htmlspecialchars($_POST['building_height'] ?? '10'); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="climate_zone" class="form-label">Climate Zone *</label>
                                    <select class="form-control" id="climate_zone" name="climate_zone" required>
                                        <option value="">Select Climate Zone</option>
                                        <option value="1A" <?php echo ($_POST['climate_zone'] ?? '') === '1A' ? 'selected' : ''; ?>>Zone 1A - Very Hot & Humid</option>
                                        <option value="1B" <?php echo ($_POST['climate_zone'] ?? '') === '1B' ? 'selected' : ''; ?>>Zone 1B - Very Hot & Dry</option>
                                        <option value="2A" <?php echo ($_POST['climate_zone'] ?? '') === '2A' ? 'selected' : ''; ?>>Zone 2A - Hot & Humid</option>
                                        <option value="2B" <?php echo ($_POST['climate_zone'] ?? '') === '2B' ? 'selected' : ''; ?>>Zone 2B - Hot & Dry</option>
                                        <option value="3A" <?php echo ($_POST['climate_zone'] ?? '') === '3A' ? 'selected' : ''; ?>>Zone 3A - Warm & Humid</option>
                                        <option value="3B" <?php echo ($_POST['climate_zone'] ?? '') === '3B' ? 'selected' : ''; ?>>Zone 3B - Warm & Dry</option>
                                        <option value="3C" <?php echo ($_POST['climate_zone'] ?? '') === '3C' ? 'selected' : ''; ?>>Zone 3C - Warm & Marine</option>
                                        <option value="4A" <?php echo ($_POST['climate_zone'] ?? '') === '4A' ? 'selected' : ''; ?>>Zone 4A - Mixed & Humid</option>
                                        <option value="4B" <?php echo ($_POST['climate_zone'] ?? '') === '4B' ? 'selected' : ''; ?>>Zone 4B - Mixed & Dry</option>
                                        <option value="4C" <?php echo ($_POST['climate_zone'] ?? '') === '4C' ? 'selected' : ''; ?>>Zone 4C - Mixed & Marine</option>
                                        <option value="5A" <?php echo ($_POST['climate_zone'] ?? '') === '5A' ? 'selected' : ''; ?>>Zone 5A - Cool & Humid</option>
                                        <option value="5B" <?php echo ($_POST['climate_zone'] ?? '') === '5B' ? 'selected' : ''; ?>>Zone 5B - Cool & Dry</option>
                                        <option value="5C" <?php echo ($_POST['climate_zone'] ?? '') === '5C' ? 'selected' : ''; ?>>Zone 5C - Cool & Marine</option>
                                        <option value="6A" <?php echo ($_POST['climate_zone'] ?? '') === '6A' ? 'selected' : ''; ?>>Zone 6A - Cold & Humid</option>
                                        <option value="6B" <?php echo ($_POST['climate_zone'] ?? '') === '6B' ? 'selected' : ''; ?>>Zone 6B - Cold & Dry</option>
                                        <option value="7A" <?php echo ($_POST['climate_zone'] ?? '') === '7A' ? 'selected' : ''; ?>>Zone 7A - Very Cold & Humid</option>
                                        <option value="7B" <?php echo ($_POST['climate_zone'] ?? '') === '7B' ? 'selected' : ''; ?>>Zone 7B - Very Cold & Dry</option>
                                        <option value="8" <?php echo ($_POST['climate_zone'] ?? '') === '8' ? 'selected' : ''; ?>>Zone 8 - Subarctic/Arctic</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>System Information</h5>
                                
                                <div class="mb-3">
                                    <label for="hvac_type" class="form-label">HVAC System Type *</label>
                                    <select class="form-control" id="hvac_type" name="hvac_type" required>
                                        <option value="">Select System Type</option>
                                        <option value="split" <?php echo ($_POST['hvac_type'] ?? '') === 'split' ? 'selected' : ''; ?>>Split System</option>
                                        <option value="package" <?php echo ($_POST['hvac_type'] ?? '') === 'package' ? 'selected' : ''; ?>>Package Unit</option>
                                        <option value="vrf" <?php echo ($_POST['hvac_type'] ?? '') === 'vrf' ? 'selected' : ''; ?>>VRF System</option>
                                        <option value="chiller" <?php echo ($_POST['hvac_type'] ?? '') === 'chiller' ? 'selected' : ''; ?>>Chiller System</option>
                                        <option value="ptac" <?php echo ($_POST['hvac_type'] ?? '') === 'ptac' ? 'selected' : ''; ?>>PTAC Units</option>
                                        <option value="water-source" <?php echo ($_POST['hvac_type'] ?? '') === 'water-source' ? 'selected' : ''; ?>>Water Source Heat Pump</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="system_age" class="form-label">System Age (years) *</label>
                                    <input type="number" class="form-control" id="system_age" name="system_age" 
                                           value="<?php echo htmlspecialchars($_POST['system_age'] ?? '5'); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="efficiency_rating" class="form-label">System Efficiency (SEER/EER) *</label>
                                    <input type="number" class="form-control" id="efficiency_rating" name="efficiency_rating" 
                                           value="<?php echo htmlspecialchars($_POST['efficiency_rating'] ?? '14'); ?>" step="0.1" required>
                                    <small class="form-text text-muted">Enter the Seasonal Energy Efficiency Ratio</small>
                                </div>

                                <div class="mb-3">
                                    <label for="operating_hours" class="form-label">Operating Hours/Day *</label>
                                    <input type="number" class="form-control" id="operating_hours" name="operating_hours" 
                                           value="<?php echo htmlspecialchars($_POST['operating_hours'] ?? '12'); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-calculator"></i> Analyze Efficiency
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <?php if ($results): ?>
                <div class="card">
                    <div class="card-header">
                        <h5>Analysis Results</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="h2 text-<?php 
                                echo $results['efficiency_score'] >= 90 ? 'success' : 
                                     ($results['efficiency_score'] >= 75 ? 'info' : 
                                     ($results['efficiency_score'] >= 60 ? 'warning' : 'danger')); 
                            ?>">
                                <?php echo $results['efficiency_score']; ?>%
                            </div>
                            <div class="text-muted">Efficiency Score</div>
                            <div class="badge bg-<?php 
                                echo $results['efficiency_score'] >= 90 ? 'success' : 
                                     ($results['efficiency_score'] >= 75 ? 'info' : 
                                     ($results['efficiency_score'] >= 60 ? 'warning' : 'danger')); 
                            ?> fs-6">
                                <?php echo $results['performance_rating']; ?>
                            </div>
                        </div>

                        <hr>

                        <h6>System Loads</h6>
                        <ul class="list-unstyled">
                            <li><strong>Cooling Load:</strong> <?php echo $results['cooling_load']; ?> tons</li>
                            <li><strong>Heating Load:</strong> <?php echo $results['heating_load']; ?> kW</li>
                            <li><strong>Ventilation:</strong> <?php echo $results['ventilation_load']; ?> CFM</li>
                        </ul>

                        <h6>Energy Performance</h6>
                        <ul class="list-unstyled">
                            <li><strong>Current Efficiency:</strong> <?php echo $results['actual_efficiency']; ?> SEER</li>
                            <li><strong>Target Efficiency:</strong> <?php echo $results['target_efficiency']; ?> SEER</li>
                            <li><strong>Annual Energy:</strong> <?php echo number_format($results['total_annual_energy']); ?> kWh</li>
                            <li><strong>Annual Cost:</strong> $<?php echo number_format($results['annual_cost'], 2); ?></li>
                        </ul>

                        <h6>Environmental Impact</h6>
                        <ul class="list-unstyled">
                            <li><strong>CO₂ Emissions:</strong> <?php echo number_format($results['co2_emissions']); ?> lbs/year</li>
                            <li><strong>Tree Equivalent:</strong> <?php echo $results['trees_equivalent']; ?> trees</li>
                        </ul>

                        <h6>Upgrade Potential</h6>
                        <ul class="list-unstyled">
                            <li><strong>Potential Savings:</strong> $<?php echo number_format($results['potential_savings'], 2); ?>/year</li>
                            <li><strong>Savings %:</strong> <?php echo $results['potential_savings_percent']; ?>%</li>
                            <li><strong>Payback Period:</strong> <?php echo $results['payback_period']; ?> years</li>
                            <li><strong>NPV:</strong> $<?php echo number_format($results['npv'], 2); ?></li>
                        </ul>

                        <div class="alert alert-info mt-3">
                            <small><strong>Climate Zone:</strong> <?php echo $results['climate_zone']; ?> - 
                            System Type: <?php echo ucwords(str_replace('-', ' ', $results['hvac_type'])); ?></small>
                        </div>
                    </div>
                </div>

                <?php if (!empty($results['recommendations'])): ?>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6>Recommendations</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <?php foreach ($results['recommendations'] as $recommendation): ?>
                                    <li class="small"><?php echo htmlspecialchars($recommendation); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h5>HVAC Efficiency Guide</h5>
                    </div>
                    <div class="card-body">
                        <h6>Efficiency Ratings</h6>
                        <div class="mb-3">
                            <small class="text-success">● Excellent (90-100%)</small><br>
                            <small class="text-info">● Good (75-89%)</small><br>
                            <small class="text-warning">● Fair (60-74%)</small><br>
                            <small class="text-danger">● Poor (Below 60%)</small>
                        </div>

                        <h6>SEER Ratings by System Type</h6>
                        <ul class="small">
                            <li>Split Systems: 14-24 SEER</li>
                            <li>Package Units: 12-20 SEER</li>
                            <li>VRF Systems: 18-24 SEER</li>
                            <li>Chillers: 20-28 SEER</li>
                            <li>PTAC Units: 10-16 SEER</li>
                            <li>Water Source HP: 17-23 SEER</li>
                        </ul>

                        <h6>Energy Saving Tips</h6>
                        <ul class="small">
                            <li>Regular maintenance improves efficiency</li>
                            <li>Upgrade to programmable thermostats</li>
                            <li>Seal air ducts to prevent leaks</li>
                            <li>Consider zoning systems</li>
                            <li>Upgrade insulation and windows</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    margin-bottom: 1.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    padding: 1rem 1.25rem;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 0.5rem 2rem;
}

.alert {
    border: 1px solid transparent;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
    padding: 0.75rem 1.25rem;
}

.badge {
    border-radius: 0.375rem;
    color: #fff;
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 1;
    padding: 0.375rem 0.5625rem;
    text-align: center;
    vertical-align: baseline;
    white-space: nowrap;
}

.list-unstyled {
    list-style: none;
    padding-left: 0;
}

.small {
    font-size: 0.875em;
}

.text-success { color: #28a745 !important; }
.text-info { color: #17a2b8 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }

.bg-success { background-color: #28a745 !important; }
.bg-info { background-color: #17a2b8 !important; }
.bg-warning { background-color: #ffc107 !important; }
.bg-danger { background-color: #dc3545 !important; }

.h2 {
    font-size: 2rem;
    font-weight: 500;
    line-height: 1.2;
    margin-bottom: 0.5rem;
}

.fs-6 {
    font-size: 1rem !important;
}

.mt-3 {
    margin-top: 1rem !important;
}

.mb-3 {
    margin-bottom: 1rem !important;
}

.mb-0 {
    margin-bottom: 0 !important;
}

hr {
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    margin: 1rem 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Auto-calculate recommended efficiency based on system type
    const systemTypeSelect = document.getElementById('hvac_type');
    const efficiencyInput = document.getElementById('efficiency_rating');
    
    const recommendedEfficiency = {
        'split': 16,
        'package': 14,
        'vrf': 18,
        'chiller': 20,
        'ptac': 12,
        'water-source': 17
    };

    systemTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        if (recommendedEfficiency[selectedType]) {
            efficiencyInput.placeholder = `Recommended: ${recommendedEfficiency[selectedType]}`;
        }
    });

    // Add input formatting
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value < 0) {
                this.value = 0;
            }
        });
    });
});
</script>

<?php include AEC_ROOT . '/includes/footer.php'; ?>
