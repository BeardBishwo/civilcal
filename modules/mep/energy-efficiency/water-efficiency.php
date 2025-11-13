<?php
session_start();
require_once '../../../app/Core/DatabaseLegacy.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$db = new Database();
$page_title = "Water Efficiency Analysis";

// Process form submission
$results = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $building_area = filter_input(INPUT_POST, 'building_area', FILTER_VALIDATE_FLOAT);
        $occupancy_count = filter_input(INPUT_POST, 'occupancy_count', FILTER_VALIDATE_INT);
        $building_type = filter_input(INPUT_POST, 'building_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $water_usage_type = filter_input(INPUT_POST, 'water_usage_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $current_fixtures = filter_input(INPUT_POST, 'current_fixtures', FILTER_VALIDATE_INT);
        $efficient_fixtures = filter_input(INPUT_POST, 'efficient_fixtures', FILTER_VALIDATE_INT);
        $irrigation_area = filter_input(INPUT_POST, 'irrigation_area', FILTER_VALIDATE_FLOAT);
        $landscape_type = filter_input(INPUT_POST, 'landscape_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if (!$building_area || !$occupancy_count || !$building_type || !$water_usage_type || !$current_fixtures || !$efficient_fixtures) {
            $errors[] = "Please fill in all required fields with valid values.";
        }

        if (empty($errors)) {
            // Load water efficiency database
            $water_db_file = __DIR__ . '/../../db/water_efficiency.json';
            if (file_exists($water_db_file)) {
                $water_data = json_decode(file_get_contents($water_db_file), true);
            } else {
                $water_data = [];
            }

            // Water usage rates by building type (gallons per person per day)
            $base_usage_rates = [
                'office' => 15,
                'retail' => 12,
                'restaurant' => 35,
                'hotel' => 60,
                'hospital' => 75,
                'school' => 18,
                'residential' => 80,
                'warehouse' => 8,
                'mixed-use' => 25
            ];

            // Fixture efficiency factors
            $fixture_efficiency = [
                'standard' => 1.0,
                'low-flow' => 0.8,
                'ultra-low' => 0.6,
                'dual-flush' => 0.7,
                'sensor-operated' => 0.75
            ];

            // Irrigation water usage (gallons per sq ft per week)
            $irrigation_rates = [
                'turf' => 2.5,
                'native-plants' => 0.8,
                'xeriscape' => 0.5,
                'vegetable-garden' => 1.8,
                'mixed-landscape' => 1.5
            ];

            $base_usage = $base_usage_rates[$building_type] ?? 20;
            $fixture_factor = $fixture_efficiency[$water_usage_type] ?? 0.8;
            $irrigation_rate = $irrigation_rates[$landscape_type ?? 'mixed-landscape'] ?? 1.5;

            // Calculate daily water consumption
            $occupancy_demand = $occupancy_count * $base_usage;
            
            // Fixture-based calculation
            $fixture_factor_efficient = $fixture_efficiency['ultra-low']; // Most efficient baseline
            
            $current_daily_usage = $current_fixtures * $base_usage * 0.5; // Base fixture usage
            $efficient_daily_usage = $efficient_fixtures * $base_usage * $fixture_factor_efficient;
            
            // HVAC water usage (cooling tower, etc.)
            $hvac_water_usage = $building_area * 0.02; // gallons per sq ft per day
            
            // Irrigation water usage
            $irrigation_daily = ($irrigation_area * $irrigation_rate) / 7; // Convert weekly to daily
            
            $total_current_usage = $occupancy_demand + $current_daily_usage + $hvac_water_usage + $irrigation_daily;
            $total_efficient_usage = ($occupancy_daily * $fixture_factor) + $efficient_daily_usage + $hvac_water_usage + $irrigation_daily;
            
            // Annual calculations
            $days_per_year = 365;
            $current_annual_usage = $total_current_usage * $days_per_year;
            $efficient_annual_usage = $total_efficient_usage * $days_per_year;
            $water_savings = $current_annual_usage - $efficient_annual_usage;
            
            // Cost calculations (average $0.004 per gallon)
            $cost_per_gallon = 0.004;
            $current_annual_cost = $current_annual_usage * $cost_per_gallon;
            $efficient_annual_cost = $efficient_annual_usage * $cost_per_gallon;
            $annual_cost_savings = $current_annual_cost - $efficient_annual_cost;
            
            // Environmental impact
            $gallons_to_cuft = 0.133681; // Conversion factor
            $cuft_saved = $water_savings * $gallons_to_cuft;
            $co2_avoided = ($water_savings / 1000) * 0.005; // tons CO2 avoided
            
            // Efficiency rating
            $efficiency_improvement = ($water_savings / $current_annual_usage) * 100;
            $efficiency_score = min(100, 60 + ($efficiency_improvement * 0.4)); // Base 60, max 100
            
            // Performance rating
            $performance_rating = 'Poor';
            if ($efficiency_score >= 90) $performance_rating = 'Excellent';
            elseif ($efficiency_score >= 75) $performance_rating = 'Good';
            elseif ($efficiency_score >= 60) $performance_rating = 'Fair';
            
            // Calculate ROI for efficiency upgrades
            $fixture_upgrade_cost = $efficient_fixtures * 150; // $150 per fixture
            $irrigation_upgrade_cost = $irrigation_area * 2; // $2 per sq ft
            $total_upgrade_cost = $fixture_upgrade_cost + $irrigation_upgrade_cost;
            
            $payback_period = $total_upgrade_cost / $annual_cost_savings;
            $npv = -$total_upgrade_cost + ($annual_cost_savings * 15 * 0.07); // 15 years, 7% discount
            
            // Generate recommendations
            $recommendations = [];
            
            if ($efficiency_score < 70) {
                $recommendations[] = "Install low-flow fixtures to reduce water consumption";
            }
            if ($irrigation_area > 0 && $landscape_type === 'turf') {
                $recommendations[] = "Consider xeriscaping to reduce irrigation needs";
            }
            if ($occupancy_count > 0) {
                $recommendations[] = "Implement water-efficient practices for occupants";
            }
            if ($water_savings > ($current_annual_usage * 0.3)) {
                $recommendations[] = "High potential savings justify fixture upgrades";
            }
            
            // Water conservation categories breakdown
            $conservation_categories = [
                'fixture_upgrades' => ($current_daily_usage - $efficient_daily_usage) * $days_per_year,
                'occupant_efficiency' => $occupancy_daily * (1 - $fixture_factor) * $days_per_year,
                'irrigation_optimization' => ($irrigation_area > 0) ? $irrigation_daily * 0.3 * $days_per_year : 0,
                'hvac_optimization' => $hvac_water_usage * 0.2 * $days_per_year // Assume 20% HVAC savings possible
            ];

            $results = [
                'building_area' => $building_area,
                'occupancy_count' => $occupancy_count,
                'building_type' => $building_type,
                'water_usage_type' => $water_usage_type,
                'current_daily_usage' => round($total_current_usage, 0),
                'efficient_daily_usage' => round($total_efficient_usage, 0),
                'current_annual_usage' => round($current_annual_usage, 0),
                'efficient_annual_usage' => round($efficient_annual_usage, 0),
                'water_savings' => round($water_savings, 0),
                'efficiency_improvement' => round($efficiency_improvement, 1),
                'efficiency_score' => round($efficiency_score, 1),
                'performance_rating' => $performance_rating,
                'current_annual_cost' => round($current_annual_cost, 2),
                'efficient_annual_cost' => round($efficient_annual_cost, 2),
                'annual_cost_savings' => round($annual_cost_savings, 2),
                'cuft_saved' => round($cuft_saved, 0),
                'co2_avoided' => round($co2_avoided, 3),
                'total_upgrade_cost' => round($total_upgrade_cost, 2),
                'payback_period' => round($payback_period, 1),
                'npv' => round($npv, 2),
                'conservation_categories' => $conservation_categories,
                'irrigation_area' => $irrigation_area,
                'landscape_type' => $landscape_type
            ];

            // Save to history
            $history_data = [
                'user_id' => $_SESSION['user_id'],
                'calculation_type' => 'water_efficiency',
                'parameters' => $_POST,
                'results' => $results,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $stmt = $db->executeQuery(
                "INSERT INTO calculation_history (user_id, calculation_type, parameters, results, created_at) VALUES (?, ?, ?, ?, ?)",
                [
                    $_SESSION['user_id'],
                    'water_efficiency',
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

include '../../../themes/default/views/partials/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Water Efficiency Analysis</h3>
                    <p class="text-muted">Comprehensive water conservation and efficiency analysis for buildings</p>
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
                                    <label for="occupancy_count" class="form-label">Daily Occupancy *</label>
                                    <input type="number" class="form-control" id="occupancy_count" name="occupancy_count" 
                                           value="<?php echo htmlspecialchars($_POST['occupancy_count'] ?? ''); ?>" required>
                                    <small class="form-text text-muted">Average number of people using the facility daily</small>
                                </div>

                                <div class="mb-3">
                                    <label for="building_type" class="form-label">Building Type *</label>
                                    <select class="form-control" id="building_type" name="building_type" required>
                                        <option value="">Select Building Type</option>
                                        <option value="office" <?php echo ($_POST['building_type'] ?? '') === 'office' ? 'selected' : ''; ?>>Office Building</option>
                                        <option value="retail" <?php echo ($_POST['building_type'] ?? '') === 'retail' ? 'selected' : ''; ?>>Retail Store</option>
                                        <option value="restaurant" <?php echo ($_POST['building_type'] ?? '') === 'restaurant' ? 'selected' : ''; ?>>Restaurant</option>
                                        <option value="hotel" <?php echo ($_POST['building_type'] ?? '') === 'hotel' ? 'selected' : ''; ?>>Hotel</option>
                                        <option value="hospital" <?php echo ($_POST['building_type'] ?? '') === 'hospital' ? 'selected' : ''; ?>>Hospital</option>
                                        <option value="school" <?php echo ($_POST['building_type'] ?? '') === 'school' ? 'selected' : ''; ?>>School</option>
                                        <option value="residential" <?php echo ($_POST['building_type'] ?? '') === 'residential' ? 'selected' : ''; ?>>Residential</option>
                                        <option value="warehouse" <?php echo ($_POST['building_type'] ?? '') === 'warehouse' ? 'selected' : ''; ?>>Warehouse</option>
                                        <option value="mixed-use" <?php echo ($_POST['building_type'] ?? '') === 'mixed-use' ? 'selected' : ''; ?>>Mixed Use</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="water_usage_type" class="form-label">Current Fixture Type *</label>
                                    <select class="form-control" id="water_usage_type" name="water_usage_type" required>
                                        <option value="">Select Fixture Type</option>
                                        <option value="standard" <?php echo ($_POST['water_usage_type'] ?? '') === 'standard' ? 'selected' : ''; ?>>Standard Fixtures</option>
                                        <option value="low-flow" <?php echo ($_POST['water_usage_type'] ?? '') === 'low-flow' ? 'selected' : ''; ?>>Low-Flow Fixtures</option>
                                        <option value="ultra-low" <?php echo ($_POST['water_usage_type'] ?? '') === 'ultra-low' ? 'selected' : ''; ?>>Ultra-Low Flow</option>
                                        <option value="dual-flush" <?php echo ($_POST['water_usage_type'] ?? '') === 'dual-flush' ? 'selected' : ''; ?>>Dual Flush Toilets</option>
                                        <option value="sensor-operated" <?php echo ($_POST['water_usage_type'] ?? '') === 'sensor-operated' ? 'selected' : ''; ?>>Sensor Operated</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>Fixture Information</h5>
                                
                                <div class="mb-3">
                                    <label for="current_fixtures" class="form-label">Number of Fixtures (Current) *</label>
                                    <input type="number" class="form-control" id="current_fixtures" name="current_fixtures" 
                                           value="<?php echo htmlspecialchars($_POST['current_fixtures'] ?? ''); ?>" required>
                                    <small class="form-text text-muted">Total water fixtures in the building</small>
                                </div>

                                <div class="mb-3">
                                    <label for="efficient_fixtures" class="form-label">Number of Fixtures (Efficient) *</label>
                                    <input type="number" class="form-control" id="efficient_fixtures" name="efficient_fixtures" 
                                           value="<?php echo htmlspecialchars($_POST['efficient_fixtures'] ?? ''); ?>" required>
                                    <small class="form-text text-muted">Fixtures after efficiency upgrades</small>
                                </div>

                                <div class="mb-3">
                                    <label for="irrigation_area" class="form-label">Irrigation Area (sq ft)</label>
                                    <input type="number" class="form-control" id="irrigation_area" name="irrigation_area" 
                                           value="<?php echo htmlspecialchars($_POST['irrigation_area'] ?? '0'); ?>">
                                    <small class="form-text text-muted">Landscape area requiring irrigation (0 if none)</small>
                                </div>

                                <div class="mb-3">
                                    <label for="landscape_type" class="form-label">Landscape Type</label>
                                    <select class="form-control" id="landscape_type" name="landscape_type">
                                        <option value="">Select Landscape Type</option>
                                        <option value="turf" <?php echo ($_POST['landscape_type'] ?? '') === 'turf' ? 'selected' : ''; ?>>Turf Grass</option>
                                        <option value="native-plants" <?php echo ($_POST['landscape_type'] ?? '') === 'native-plants' ? 'selected' : ''; ?>>Native Plants</option>
                                        <option value="xeriscape" <?php echo ($_POST['landscape_type'] ?? '') === 'xeriscape' ? 'selected' : ''; ?>>Xeriscape</option>
                                        <option value="vegetable-garden" <?php echo ($_POST['landscape_type'] ?? '') === 'vegetable-garden' ? 'selected' : ''; ?>>Vegetable Garden</option>
                                        <option value="mixed-landscape" <?php echo ($_POST['landscape_type'] ?? '') === 'mixed-landscape' ? 'selected' : ''; ?>>Mixed Landscape</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-tint"></i> Analyze Water Efficiency
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

                        <h6>Water Usage</h6>
                        <ul class="list-unstyled">
                            <li><strong>Current Usage:</strong> <?php echo number_format($results['current_annual_usage']); ?> gal/year</li>
                            <li><strong>Efficient Usage:</strong> <?php echo number_format($results['efficient_annual_usage']); ?> gal/year</li>
                            <li><strong>Water Savings:</strong> <?php echo number_format($results['water_savings']); ?> gal/year</li>
                            <li><strong>Improvement:</strong> <?php echo $results['efficiency_improvement']; ?>%</li>
                        </ul>

                        <h6>Cost Analysis</h6>
                        <ul class="list-unstyled">
                            <li><strong>Current Cost:</strong> $<?php echo number_format($results['current_annual_cost'], 2); ?></li>
                            <li><strong>Efficient Cost:</strong> $<?php echo number_format($results['efficient_annual_cost'], 2); ?></li>
                            <li><strong>Annual Savings:</strong> $<?php echo number_format($results['annual_cost_savings'], 2); ?></li>
                        </ul>

                        <h6>Environmental Impact</h6>
                        <ul class="list-unstyled">
                            <li><strong>Water Saved:</strong> <?php echo number_format($results['cuft_saved']); ?> cu ft/year</li>
                            <li><strong>CO₂ Avoided:</strong> <?php echo $results['co2_avoided']; ?> tons/year</li>
                        </ul>

                        <h6>ROI Analysis</h6>
                        <ul class="list-unstyled">
                            <li><strong>Upgrade Cost:</strong> $<?php echo number_format($results['total_upgrade_cost'], 2); ?></li>
                            <li><strong>Payback Period:</strong> <?php echo $results['payback_period']; ?> years</li>
                            <li><strong>NPV:</strong> $<?php echo number_format($results['npv'], 2); ?></li>
                        </ul>

                        <div class="alert alert-info mt-3">
                            <small><strong>Building Type:</strong> <?php echo ucwords(str_replace('-', ' ', $results['building_type'])); ?><br>
                            <strong>Fixture Type:</strong> <?php echo ucwords(str_replace('-', ' ', $results['water_usage_type'])); ?></small>
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

                <?php if ($results['conservation_categories']['fixture_upgrades'] > 0): ?>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6>Conservation Breakdown</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small">
                                <li><strong>Fixture Upgrades:</strong> <?php echo number_format($results['conservation_categories']['fixture_upgrades']); ?> gal</li>
                                <li><strong>Occupant Efficiency:</strong> <?php echo number_format($results['conservation_categories']['occupant_efficiency']); ?> gal</li>
                                <li><strong>Irrigation Optimization:</strong> <?php echo number_format($results['conservation_categories']['irrigation_optimization']); ?> gal</li>
                                <li><strong>HVAC Optimization:</strong> <?php echo number_format($results['conservation_categories']['hvac_optimization']); ?> gal</li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h5>Water Efficiency Guide</h5>
                    </div>
                    <div class="card-body">
                        <h6>Water Usage by Building Type</h6>
                        <ul class="small">
                            <li>Office: 15 gal/person/day</li>
                            <li>Retail: 12 gal/person/day</li>
                            <li>Restaurant: 35 gal/person/day</li>
                            <li>Hotel: 60 gal/person/day</li>
                            <li>Hospital: 75 gal/person/day</li>
                            <li>School: 18 gal/person/day</li>
                        </ul>

                        <h6>Efficiency Improvements</h6>
                        <ul class="small">
                            <li>Low-flow fixtures: 20% savings</li>
                            <li>Ultra-low flow: 40% savings</li>
                            <li>Dual flush toilets: 30% savings</li>
                            <li>Sensor operation: 25% savings</li>
                        </ul>

                        <h6>Landscape Watering</h6>
                        <ul class="small">
                            <li>Turf: 2.5 gal/sq ft/week</li>
                            <li>Native plants: 0.8 gal/sq ft/week</li>
                            <li>Xeriscape: 0.5 gal/sq ft/week</li>
                            <li>Mixed landscape: 1.5 gal/sq ft/week</li>
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

    // Auto-calculate efficient fixtures based on current fixtures
    const currentFixturesInput = document.getElementById('current_fixtures');
    const efficientFixturesInput = document.getElementById('efficient_fixtures');

    currentFixturesInput.addEventListener('input', function() {
        const currentValue = parseInt(this.value) || 0;
        // Assume same number of fixtures but more efficient
        if (currentValue > 0) {
            efficientFixturesInput.value = currentValue;
        }
    });

    // Auto-populate landscape recommendations based on irrigation area
    const irrigationAreaInput = document.getElementById('irrigation_area');
    const landscapeTypeSelect = document.getElementById('landscape_type');

    irrigationAreaInput.addEventListener('input', function() {
        const area = parseFloat(this.value) || 0;
        if (area > 5000) {
            landscapeTypeSelect.value = 'xeriscape';
        } else if (area > 1000) {
            landscapeTypeSelect.value = 'native-plants';
        } else if (area > 0) {
            landscapeTypeSelect.value = 'mixed-landscape';
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

<?php include '../../../themes/default/views/partials/footer.php'; ?>


