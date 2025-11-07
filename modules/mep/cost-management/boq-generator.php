<?php
session_start();
require_once '../../../includes/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$db = new Database();
$page_title = "MEP Bill of Quantities (BOQ) Generator";

// Process form submission
$results = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $project_name = filter_input(INPUT_POST, 'project_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $building_area = filter_input(INPUT_POST, 'building_area', FILTER_VALIDATE_FLOAT);
        $building_type = filter_input(INPUT_POST, 'building_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $floors_count = filter_input(INPUT_POST, 'floors_count', FILTER_VALIDATE_INT);
        $hvac_scope = filter_input(INPUT_POST, 'hvac_scope', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $electrical_scope = filter_input(INPUT_POST, 'electrical_scope', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $plumbing_scope = filter_input(INPUT_POST, 'plumbing_scope', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $fire_protection_scope = filter_input(INPUT_POST, 'fire_protection_scope', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $quality_level = filter_input(INPUT_POST, 'quality_level', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if (!$project_name || !$building_area || !$building_type || !$floors_count || !$hvac_scope || !$electrical_scope || !$plumbing_scope) {
            $errors[] = "Please fill in all required fields with valid values.";
        }

        if (empty($errors)) {
            // Load MEP cost database
            $cost_db_file = __DIR__ . '/../../db/mep_cost_data.json';
            if (file_exists($cost_db_file)) {
                $cost_data = json_decode(file_get_contents($cost_db_file), true);
            } else {
                $cost_data = [];
            }

            // Unit costs per sq ft for different MEP systems (base 2024 prices)
            $unit_costs = [
                'basic' => [
                    'hvac' => 15, 'electrical' => 12, 'plumbing' => 8, 'fire_protection' => 5
                ],
                'standard' => [
                    'hvac' => 22, 'electrical' => 18, 'plumbing' => 12, 'fire_protection' => 8
                ],
                'premium' => [
                    'hvac' => 35, 'electrical' => 28, 'plumbing' => 18, 'fire_protection' => 12
                ],
                'luxury' => [
                    'hvac' => 50, 'electrical' => 40, 'plumbing' => 25, 'fire_protection' => 18
                ]
            ];

            // Multipliers based on building type
            $building_multipliers = [
                'office' => 1.0,
                'retail' => 0.8,
                'restaurant' => 1.2,
                'hotel' => 1.4,
                'hospital' => 1.8,
                'school' => 1.1,
                'residential' => 0.9,
                'warehouse' => 0.6,
                'industrial' => 1.3,
                'mixed-use' => 1.2
            ];

            // HVAC scope multipliers
            $hvac_scopes = [
                'basic' => 1.0, 'standard' => 1.3, 'premium' => 1.8, 'custom' => 2.2
            ];

            // Electrical scope multipliers
            $electrical_scopes = [
                'basic' => 1.0, 'standard' => 1.2, 'premium' => 1.6, 'smart-building' => 2.0
            ];

            // Plumbing scope multipliers
            $plumbing_scopes = [
                'basic' => 1.0, 'standard' => 1.2, 'premium' => 1.5, 'green-building' => 1.8
            ];

            // Fire protection scope multipliers
            $fire_protection_scopes = [
                'basic' => 1.0, 'standard' => 1.3, 'premium' => 1.7, 'advanced' => 2.2
            ];

            $quality_multiplier = $unit_costs[$quality_level] ?? $unit_costs['standard'];
            $building_multiplier = $building_multipliers[$building_type] ?? 1.0;
            $hvac_multiplier = $hvac_scopes[$hvac_scope] ?? 1.0;
            $electrical_multiplier = $electrical_scopes[$electrical_scope] ?? 1.0;
            $plumbing_multiplier = $plumbing_scopes[$plumbing_scope] ?? 1.0;
            $fire_protection_multiplier = $fire_protection_scopes[$fire_protection_scope] ?? 1.0;

            // Floor height multiplier
            $floor_height_multiplier = $floors_count > 10 ? 1.15 : ($floors_count > 5 ? 1.08 : 1.0);

            // Calculate system costs
            $hvac_cost = $building_area * $quality_multiplier['hvac'] * $building_multiplier * $hvac_multiplier * $floor_height_multiplier;
            $electrical_cost = $building_area * $quality_multiplier['electrical'] * $building_multiplier * $electrical_multiplier * $floor_height_multiplier;
            $plumbing_cost = $building_area * $quality_multiplier['plumbing'] * $building_multiplier * $plumbing_multiplier * $floor_height_multiplier;
            $fire_protection_cost = $building_area * $quality_multiplier['fire_protection'] * $building_multiplier * $fire_protection_multiplier * $floor_height_multiplier;

            $subtotal_cost = $hvac_cost + $electrical_cost + $plumbing_cost + $fire_protection_cost;

            // Add additional costs
            $design_fee = $subtotal_cost * 0.08; // 8% for design
            $permit_fee = $subtotal_cost * 0.03; // 3% for permits
            $contingency = $subtotal_cost * 0.10; // 10% contingency
            $testing_commissioning = $subtotal_cost * 0.05; // 5% for testing and commissioning

            $total_mep_cost = $subtotal_cost + $design_fee + $permit_fee + $contingency + $testing_commissioning;

            // Generate detailed BOQ items
            $boq_items = [];

            // HVAC Items
            if ($hvac_cost > 0) {
                $boq_items[] = [
                    'category' => 'HVAC Systems',
                    'item' => 'HVAC Equipment',
                    'quantity' => 1,
                    'unit' => 'LS',
                    'unit_rate' => round($hvac_cost * 0.4, 2),
                    'amount' => round($hvac_cost * 0.4, 2),
                    'description' => 'Chillers, AHUs, pumps, and major equipment'
                ];

                $boq_items[] = [
                    'category' => 'HVAC Systems',
                    'item' => 'HVAC Ductwork',
                    'quantity' => $building_area * 1.2,
                    'unit' => 'SF',
                    'unit_rate' => round($hvac_cost * 0.3 / ($building_area * 1.2), 2),
                    'amount' => round($hvac_cost * 0.3, 2),
                    'description' => 'Supply and return air ductwork'
                ];

                $boq_items[] = [
                    'category' => 'HVAC Systems',
                    'item' => 'HVAC Controls',
                    'quantity' => $floors_count,
                    'unit' => 'Floor',
                    'unit_rate' => round($hvac_cost * 0.15 / $floors_count, 2),
                    'amount' => round($hvac_cost * 0.15, 2),
                    'description' => 'Building automation and control systems'
                ];

                $boq_items[] = [
                    'category' => 'HVAC Systems',
                    'item' => 'HVAC Installation',
                    'quantity' => 1,
                    'unit' => 'LS',
                    'unit_rate' => round($hvac_cost * 0.15, 2),
                    'amount' => round($hvac_cost * 0.15, 2),
                    'description' => 'Labor and installation'
                ];
            }

            // Electrical Items
            if ($electrical_cost > 0) {
                $boq_items[] = [
                    'category' => 'Electrical Systems',
                    'item' => 'Electrical Panels',
                    'quantity' => ceil($floors_count / 2),
                    'unit' => 'EA',
                    'unit_rate' => round($electrical_cost * 0.2 / ceil($floors_count / 2), 2),
                    'amount' => round($electrical_cost * 0.2, 2),
                    'description' => 'Main distribution panels and sub-panels'
                ];

                $boq_items[] = [
                    'category' => 'Electrical Systems',
                    'item' => 'Electrical Wiring',
                    'quantity' => $building_area * 4,
                    'unit' => 'LF',
                    'unit_rate' => round($electrical_cost * 0.35 / ($building_area * 4), 2),
                    'amount' => round($electrical_cost * 0.35, 2),
                    'description' => 'Power and lighting circuits'
                ];

                $boq_items[] = [
                    'category' => 'Electrical Systems',
                    'item' => 'Lighting Fixtures',
                    'quantity' => $building_area * 0.8,
                    'unit' => 'SF',
                    'unit_rate' => round($electrical_cost * 0.25 / ($building_area * 0.8), 2),
                    'amount' => round($electrical_cost * 0.25, 2),
                    'description' => 'LED lighting fixtures and controls'
                ];

                $boq_items[] = [
                    'category' => 'Electrical Systems',
                    'item' => 'Electrical Installation',
                    'quantity' => 1,
                    'unit' => 'LS',
                    'unit_rate' => round($electrical_cost * 0.2, 2),
                    'amount' => round($electrical_cost * 0.2, 2),
                    'description' => 'Labor and installation'
                ];
            }

            // Plumbing Items
            if ($plumbing_cost > 0) {
                $boq_items[] = [
                    'category' => 'Plumbing Systems',
                    'item' => 'Plumbing Fixtures',
                    'quantity' => ceil($building_area / 500),
                    'unit' => 'EA',
                    'unit_rate' => round($plumbing_cost * 0.3 / ceil($building_area / 500), 2),
                    'amount' => round($plumbing_cost * 0.3, 2),
                    'description' => 'Water closets, lavatories, sinks'
                ];

                $boq_items[] = [
                    'category' => 'Plumbing Systems',
                    'item' => 'Plumbing Piping',
                    'quantity' => $building_area * 2.5,
                    'unit' => 'LF',
                    'unit_rate' => round($plumbing_cost * 0.4 / ($building_area * 2.5), 2),
                    'amount' => round($plumbing_cost * 0.4, 2),
                    'description' => 'Water supply and drainage piping'
                ];

                $boq_items[] = [
                    'category' => 'Plumbing Systems',
                    'item' => 'Plumbing Equipment',
                    'quantity' => 1,
                    'unit' => 'LS',
                    'unit_rate' => round($plumbing_cost * 0.3, 2),
                    'amount' => round($plumbing_cost * 0.3, 2),
                    'description' => 'Pumps, tanks, and water heaters'
                ];
            }

            // Fire Protection Items
            if ($fire_protection_cost > 0) {
                $boq_items[] = [
                    'category' => 'Fire Protection Systems',
                    'item' => 'Sprinkler System',
                    'quantity' => $building_area,
                    'unit' => 'SF',
                    'unit_rate' => round($fire_protection_cost * 0.6 / $building_area, 2),
                    'amount' => round($fire_protection_cost * 0.6, 2),
                    'description' => 'Wet sprinkler system with heads'
                ];

                $boq_items[] = [
                    'category' => 'Fire Protection Systems',
                    'item' => 'Fire Alarm System',
                    'quantity' => 1,
                    'unit' => 'LS',
                    'unit_rate' => round($fire_protection_cost * 0.4, 2),
                    'amount' => round($fire_protection_cost * 0.4, 2),
                    'description' => 'Fire alarm, detection, and notification'
                ];
            }

            // Cost breakdown
            $cost_breakdown = [
                'hvac_cost' => round($hvac_cost, 2),
                'electrical_cost' => round($electrical_cost, 2),
                'plumbing_cost' => round($plumbing_cost, 2),
                'fire_protection_cost' => round($fire_protection_cost, 2),
                'subtotal' => round($subtotal_cost, 2),
                'design_fee' => round($design_fee, 2),
                'permit_fee' => round($permit_fee, 2),
                'contingency' => round($contingency, 2),
                'testing_commissioning' => round($testing_commissioning, 2),
                'total_cost' => round($total_mep_cost, 2),
                'cost_per_sqft' => round($total_mep_cost / $building_area, 2)
            ];

            $results = [
                'project_name' => $project_name,
                'building_area' => $building_area,
                'building_type' => $building_type,
                'floors_count' => $floors_count,
                'quality_level' => $quality_level,
                'hvac_scope' => $hvac_scope,
                'electrical_scope' => $electrical_scope,
                'plumbing_scope' => $plumbing_scope,
                'fire_protection_scope' => $fire_protection_scope,
                'boq_items' => $boq_items,
                'cost_breakdown' => $cost_breakdown,
                'generated_date' => date('Y-m-d H:i:s')
            ];

            // Save to history
            $history_data = [
                'user_id' => $_SESSION['user_id'],
                'calculation_type' => 'boq_generator',
                'parameters' => $_POST,
                'results' => $results,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $stmt = $db->executeQuery(
                "INSERT INTO calculation_history (user_id, calculation_type, parameters, results, created_at) VALUES (?, ?, ?, ?, ?)",
                [
                    $_SESSION['user_id'],
                    'boq_generator',
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

include '../../../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>MEP Bill of Quantities (BOQ) Generator</h3>
                    <p class="text-muted">Generate comprehensive bill of quantities for MEP systems</p>
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
                                <h5>Project Information</h5>
                                
                                <div class="mb-3">
                                    <label for="project_name" class="form-label">Project Name *</label>
                                    <input type="text" class="form-control" id="project_name" name="project_name" 
                                           value="<?php echo htmlspecialchars($_POST['project_name'] ?? ''); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="building_area" class="form-label">Building Area (sq ft) *</label>
                                    <input type="number" class="form-control" id="building_area" name="building_area" 
                                           value="<?php echo htmlspecialchars($_POST['building_area'] ?? ''); ?>" required>
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
                                        <option value="industrial" <?php echo ($_POST['building_type'] ?? '') === 'industrial' ? 'selected' : ''; ?>>Industrial</option>
                                        <option value="mixed-use" <?php echo ($_POST['building_type'] ?? '') === 'mixed-use' ? 'selected' : ''; ?>>Mixed Use</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="floors_count" class="form-label">Number of Floors *</label>
                                    <input type="number" class="form-control" id="floors_count" name="floors_count" 
                                           value="<?php echo htmlspecialchars($_POST['floors_count'] ?? '1'); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="quality_level" class="form-label">Quality Level *</label>
                                    <select class="form-control" id="quality_level" name="quality_level" required>
                                        <option value="">Select Quality Level</option>
                                        <option value="basic" <?php echo ($_POST['quality_level'] ?? '') === 'basic' ? 'selected' : ''; ?>>Basic</option>
                                        <option value="standard" <?php echo ($_POST['quality_level'] ?? '') === 'standard' ? 'selected' : ''; ?>>Standard</option>
                                        <option value="premium" <?php echo ($_POST['quality_level'] ?? '') === 'premium' ? 'selected' : ''; ?>>Premium</option>
                                        <option value="luxury" <?php echo ($_POST['quality_level'] ?? '') === 'luxury' ? 'selected' : ''; ?>>Luxury</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>Scope Selection</h5>
                                
                                <div class="mb-3">
                                    <label for="hvac_scope" class="form-label">HVAC Scope *</label>
                                    <select class="form-control" id="hvac_scope" name="hvac_scope" required>
                                        <option value="">Select HVAC Scope</option>
                                        <option value="basic" <?php echo ($_POST['hvac_scope'] ?? '') === 'basic' ? 'selected' : ''; ?>>Basic HVAC</option>
                                        <option value="standard" <?php echo ($_POST['hvac_scope'] ?? '') === 'standard' ? 'selected' : ''; ?>>Standard HVAC</option>
                                        <option value="premium" <?php echo ($_POST['hvac_scope'] ?? '') === 'premium' ? 'selected' : ''; ?>>Premium HVAC</option>
                                        <option value="custom" <?php echo ($_POST['hvac_scope'] ?? '') === 'custom' ? 'selected' : ''; ?>>Custom HVAC</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="electrical_scope" class="form-label">Electrical Scope *</label>
                                    <select class="form-control" id="electrical_scope" name="electrical_scope" required>
                                        <option value="">Select Electrical Scope</option>
                                        <option value="basic" <?php echo ($_POST['electrical_scope'] ?? '') === 'basic' ? 'selected' : ''; ?>>Basic Electrical</option>
                                        <option value="standard" <?php echo ($_POST['electrical_scope'] ?? '') === 'standard' ? 'selected' : ''; ?>>Standard Electrical</option>
                                        <option value="premium" <?php echo ($_POST['electrical_scope'] ?? '') === 'premium' ? 'selected' : ''; ?>>Premium Electrical</option>
                                        <option value="smart-building" <?php echo ($_POST['electrical_scope'] ?? '') === 'smart-building' ? 'selected' : ''; ?>>Smart Building</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="plumbing_scope" class="form-label">Plumbing Scope *</label>
                                    <select class="form-control" id="plumbing_scope" name="plumbing_scope" required>
                                        <option value="">Select Plumbing Scope</option>
                                        <option value="basic" <?php echo ($_POST['plumbing_scope'] ?? '') === 'basic' ? 'selected' : ''; ?>>Basic Plumbing</option>
                                        <option value="standard" <?php echo ($_POST['plumbing_scope'] ?? '') === 'standard' ? 'selected' : ''; ?>>Standard Plumbing</option>
                                        <option value="premium" <?php echo ($_POST['plumbing_scope'] ?? '') === 'premium' ? 'selected' : ''; ?>>Premium Plumbing</option>
                                        <option value="green-building" <?php echo ($_POST['plumbing_scope'] ?? '') === 'green-building' ? 'selected' : ''; ?>>Green Building</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="fire_protection_scope" class="form-label">Fire Protection Scope</label>
                                    <select class="form-control" id="fire_protection_scope" name="fire_protection_scope">
                                        <option value="">No Fire Protection</option>
                                        <option value="basic" <?php echo ($_POST['fire_protection_scope'] ?? '') === 'basic' ? 'selected' : ''; ?>>Basic Fire Protection</option>
                                        <option value="standard" <?php echo ($_POST['fire_protection_scope'] ?? '') === 'standard' ? 'selected' : ''; ?>>Standard Fire Protection</option>
                                        <option value="premium" <?php echo ($_POST['fire_protection_scope'] ?? '') === 'premium' ? 'selected' : ''; ?>>Premium Fire Protection</option>
                                        <option value="advanced" <?php echo ($_POST['fire_protection_scope'] ?? '') === 'advanced' ? 'selected' : ''; ?>>Advanced Fire Protection</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-calculator"></i> Generate BOQ
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
                        <h5>Cost Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="h2 text-primary">
                                $<?php echo number_format($results['cost_breakdown']['total_cost'], 0); ?>
                            </div>
                            <div class="text-muted">Total MEP Cost</div>
                            <div class="small text-info">
                                $<?php echo number_format($results['cost_breakdown']['cost_per_sqft'], 2); ?> / sq ft
                            </div>
                        </div>

                        <hr>

                        <h6>System Breakdown</h6>
                        <ul class="list-unstyled">
                            <li><strong>HVAC:</strong> $<?php echo number_format($results['cost_breakdown']['hvac_cost']); ?></li>
                            <li><strong>Electrical:</strong> $<?php echo number_format($results['cost_breakdown']['electrical_cost']); ?></li>
                            <li><strong>Plumbing:</strong> $<?php echo number_format($results['cost_breakdown']['plumbing_cost']); ?></li>
                            <li><strong>Fire Protection:</strong> $<?php echo number_format($results['cost_breakdown']['fire_protection_cost']); ?></li>
                        </ul>

                        <h6>Additional Costs</h6>
                        <ul class="list-unstyled">
                            <li><strong>Design Fee:</strong> $<?php echo number_format($results['cost_breakdown']['design_fee']); ?></li>
                            <li><strong>Permits:</strong> $<?php echo number_format($results['cost_breakdown']['permit_fee']); ?></li>
                            <li><strong>Contingency:</strong> $<?php echo number_format($results['cost_breakdown']['contingency']); ?></li>
                            <li><strong>T&C:</strong> $<?php echo number_format($results['cost_breakdown']['testing_commissioning']); ?></li>
                        </ul>

                        <div class="alert alert-info mt-3">
                            <small><strong>Project:</strong> <?php echo htmlspecialchars($results['project_name']); ?><br>
                            <strong>Type:</strong> <?php echo ucwords($results['building_type']); ?><br>
                            <strong>Area:</strong> <?php echo number_format($results['building_area']); ?> sq ft<br>
                            <strong>Generated:</strong> <?php echo date('M d, Y', strtotime($results['generated_date'])); ?></small>
                        </div>

                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-success btn-sm" onclick="window.print()">
                                <i class="fas fa-print"></i> Print BOQ
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h5>BOQ Generation Guide</h5>
                    </div>
                    <div class="card-body">
                        <h6>Cost Ranges (per sq ft)</h6>
                        <ul class="small">
                            <li><strong>Basic:</strong> $40/sq ft</li>
                            <li><strong>Standard:</strong> $60/sq ft</li>
                            <li><strong>Premium:</strong> $93/sq ft</li>
                            <li><strong>Luxury:</strong> $133/sq ft</li>
                        </ul>

                        <h6>Building Type Factors</h6>
                        <ul class="small">
                            <li>Office: 1.0x (baseline)</li>
                            <li>Hospital: 1.8x (highest complexity)</li>
                            <li>Warehouse: 0.6x (lowest complexity)</li>
                        </ul>

                        <h6>Scope Variations</h6>
                        <ul class="small">
                            <li>HVAC custom systems: +120%</li>
                            <li>Smart building integration: +100%</li>
                            <li>Green building features: +80%</li>
                            <li>Advanced fire protection: +120%</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($results && !empty($results['boq_items'])): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Detailed Bill of Quantities</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Category</th>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Unit Rate</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $current_category = '';
                                    foreach ($results['boq_items'] as $item): 
                                        if ($item['category'] !== $current_category):
                                            $current_category = $item['category'];
                                    ?>
                                        <tr class="table-primary">
                                            <td colspan="7"><strong><?php echo $current_category; ?></strong></td>
                                        </tr>
                                    <?php endif; ?>
                                        <tr>
                                            <td></td>
                                            <td><?php echo htmlspecialchars($item['item']); ?></td>
                                            <td class="text-end"><?php echo number_format($item['quantity'], 1); ?></td>
                                            <td><?php echo htmlspecialchars($item['unit']); ?></td>
                                            <td class="text-end">$<?php echo number_format($item['unit_rate'], 2); ?></td>
                                            <td class="text-end"><strong>$<?php echo number_format($item['amount'], 2); ?></strong></td>
                                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5" class="text-end">Subtotal:</th>
                                        <th class="text-end">$<?php echo number_format($results['cost_breakdown']['subtotal']); ?></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="text-end">Design Fee (8%):</th>
                                        <th class="text-end">$<?php echo number_format($results['cost_breakdown']['design_fee']); ?></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="text-end">Permits (3%):</th>
                                        <th class="text-end">$<?php echo number_format($results['cost_breakdown']['permit_fee']); ?></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="text-end">Contingency (10%):</th>
                                        <th class="text-end">$<?php echo number_format($results['cost_breakdown']['contingency']); ?></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="text-end">Testing & Commissioning (5%):</th>
                                        <th class="text-end">$<?php echo number_format($results['cost_breakdown']['testing_commissioning']); ?></th>
                                        <th></th>
                                    </tr>
                                    <tr class="table-success">
                                        <th colspan="5" class="text-end">TOTAL:</th>
                                        <th class="text-end">$<?php echo number_format($results['cost_breakdown']['total_cost']); ?></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.alert {
    border: 1px solid transparent;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
    padding: 0.75rem 1.25rem;
}

.list-unstyled {
    list-style: none;
    padding-left: 0;
}

.small {
    font-size: 0.875em;
}

.text-primary { color: #007bff !important; }
.text-info { color: #17a2b8 !important; }

.h2 {
    font-size: 2rem;
    font-weight: 500;
    line-height: 1.2;
    margin-bottom: 0.5rem;
}

.mt-3 {
    margin-top: 1rem !important;
}

.mt-4 {
    margin-top: 1.5rem !important;
}

.mb-3 {
    margin-bottom: 1rem !important;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    --bs-table-bg: transparent;
    --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
    --bs-table-striped-color: #212529;
    --bs-table-active-bg: rgba(0, 0, 0, 0.1);
    --bs-table-active-color: #212529;
    --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
    --bs-table-hover-color: #212529;
    border-color: #dee2e6;
    color: #212529;
    margin-bottom: 0;
    vertical-align: middle;
    width: 100%;
}

.table-striped > tbody > tr:nth-of-type(odd) > td {
    background-color: rgba(0, 0, 0, 0.05);
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
}

.table-dark {
    --bs-table-bg: #212529;
    --bs-table-striped-bg: #2c3034;
    --bs-table-striped-color: #fff;
    --bs-table-active-bg: #373b3e;
    --bs-table-active-color: #fff;
    --bs-table-hover-bg: #323539;
    --bs-table-hover-color: #fff;
    background-color: #212529;
    color: #fff;
}

.table-light {
    --bs-table-bg: #f8f9fa;
    --bs-table-striped-bg: #e9ecef;
    --bs-table-striped-color: #000;
    --bs-table-active-bg: #dde2e6;
    --bs-table-active-color: #000;
    --bs-table-hover-bg: #e5e7eb;
    --bs-table-hover-color: #000;
    background-color: #f8f9fa;
    color: #000;
}

.table-primary {
    --bs-table-bg: #cfe2ff;
    --bs-table-striped-bg: #c2d7f7;
    --bs-table-striped-color: #000;
    --bs-table-active-bg: #b8cfe9;
    --bs-table-active-color: #000;
    --bs-table-hover-bg: #bdd3f1;
    --bs-table-hover-color: #000;
    background-color: #cfe2ff;
    color: #000;
}

.table-success {
    --bs-table-bg: #d1e7dd;
    --bs-table-striped-bg: #c6e6d3;
    --bs-table-striped-color: #000;
    --bs-table-active-bg: #bbd9c7;
    --bs-table-active-color: #000;
    --bs-table-hover-bg: #c2dfcf;
    --bs-table-hover-color: #000;
    background-color: #d1e7dd;
    color: #000;
}

.text-end {
    text-align: right !important;
}

@media print {
    .btn {
        display: none !important;
    }
    .card-header {
        background-color: #fff !important;
        color: #000 !important;
    }
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

    // Auto-calculate floor-based estimates
    const floorsInput = document.getElementById('floors_count');
    const areaInput = document.getElementById('building_area');
    
    floorsInput.addEventListener('input', function() {
        const floors = parseInt(this.value) || 1;
        const area = parseFloat(areaInput.value) || 0;
        
        if (floors > 10) {
            this.style.borderColor = '#ffc107';
            this.title = 'High-rise building - additional costs may apply';
        } else {
            this.style.borderColor = '';
            this.title = '';
        }
    });

    // Auto-adjust building type based on area
    areaInput.addEventListener('input', function() {
        const area = parseFloat(this.value) || 0;
        const buildingTypeSelect = document.getElementById('building_type');
        
        if (area > 500000) {
            buildingTypeSelect.value = 'mixed-use';
        } else if (area > 100000) {
            buildingTypeSelect.value = 'office';
        } else if (area > 10000) {
            buildingTypeSelect.value = 'retail';
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

<?php include '../../../includes/footer.php'; ?>
