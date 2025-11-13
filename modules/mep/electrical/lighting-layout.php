<?php
/**
 * Lighting Layout Calculator
 * Professional lighting design and layout optimization
 * Includes illuminance calculations, fixture spacing, and energy efficiency
 */

require_once '../../../app/Config/config.php';

// Get room parameters
$roomLength = filter_input(INPUT_POST, 'room_length', FILTER_VALIDATE_FLOAT);
$roomWidth = filter_input(INPUT_POST, 'room_width', FILTER_VALIDATE_FLOAT);
$roomHeight = filter_input(INPUT_POST, 'room_height', FILTER_VALIDATE_FLOAT);
$ceilingHeight = filter_input(INPUT_POST, 'ceiling_height', FILTER_VALIDATE_FLOAT);
$reflectances = [
    'ceiling' => filter_input(INPUT_POST, 'ceiling_reflectance', FILTER_VALIDATE_FLOAT) ?: 80,
    'walls' => filter_input(INPUT_POST, 'wall_reflectance', FILTER_VALIDATE_FLOAT) ?: 50,
    'floor' => filter_input(INPUT_POST, 'floor_reflectance', FILTER_VALIDATE_FLOAT) ?: 20
];

$activity = filter_input(INPUT_POST, 'activity_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'office';
$lightingType = filter_input(INPUT_POST, 'lighting_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'led';
$luxLevel = filter_input(INPUT_POST, 'lux_level', FILTER_VALIDATE_FLOAT) ?: 500;

$calculations = [];
$results = null;

if ($roomLength && $roomWidth && $roomHeight && $ceilingHeight) {
    // Define illuminance requirements by activity
    $illuminanceRequirements = [
        'office' => 500, 'classroom' => 300, 'library' => 500,
        'corridor' => 100, 'stairway' => 150, 'parking' => 50,
        'warehouse' => 300, 'workshop' => 750, 'kitchen' => 500,
        'bathroom' => 200, 'living_room' => 150, 'bedroom' => 100
    ];
    
    $targetLux = $luxLevel ?: $illuminanceRequirements[$activity] ?: 500;
    
    // Calculate room cavity ratio
    $roomArea = $roomLength * $roomWidth;
    $roomPerimeter = 2 * ($roomLength + $roomWidth);
    $roomHeight = $ceilingHeight - 0.8; // Working plane height
    
    // Calculate coefficients of utilization (CU) using simplified method
    $cu = calculateCoefficientOfUtilization($reflectances, $roomHeight, $roomArea, $roomPerimeter);
    
    // Calculate lumens required
    $totalLumensRequired = ($targetLux * $roomArea) / max($cu, 0.1); // Avoid division by zero
    
    // Fixture specifications by type
    $fixtureSpecs = [
        'led' => ['lumens' => 4000, 'watts' => 40, 'efficacy' => 100, 'life_hours' => 50000],
        'fluorescent' => ['lumens' => 3200, 'watts' => 32, 'efficacy' => 100, 'life_hours' => 20000],
        'halogen' => ['lumens' => 1500, 'watts' => 75, 'efficacy' => 20, 'life_hours' => 2000],
        'incandescent' => ['lumens' => 800, 'watts' => 60, 'efficacy' => 13, 'life_hours' => 1000]
    ];
    
    $fixtureType = $fixtureSpecs[$lightingType] ?: $fixtureSpecs['led'];
    $numberOfFixtures = ceil($totalLumensRequired / $fixtureType['lumens']);
    
    // Calculate layout geometry
    $fixtureSpacing = [
        'length' => $roomLength / sqrt($numberOfFixtures / ($roomLength / $roomWidth)),
        'width' => $roomWidth / sqrt($numberOfFixtures / ($roomWidth / $roomLength))
    ];
    
    // Ensure spacing follows IES recommendations (4:1 to 1:4 ratio)
    $spacingRatio = max($fixtureSpacing['length'] / $fixtureSpacing['width'], 
                       $fixtureSpacing['width'] / $fixtureSpacing['length']);
    
    if ($spacingRatio > 4) {
        // Adjust layout for better ratio
        $aspectRatio = $roomLength / $roomWidth;
        $fixturesLength = ceil(sqrt($numberOfFixtures * $aspectRatio));
        $fixturesWidth = ceil($numberOfFixtures / $fixturesLength);
        
        $fixtureSpacing['length'] = $roomLength / $fixturesLength;
        $fixtureSpacing['width'] = $roomWidth / $fixturesWidth;
    }
    
    // Calculate actual illuminance
    $actualLux = ($numberOfFixtures * $fixtureType['lumens'] * $cu) / $roomArea;
    
    // Energy calculations
    $totalWatts = $numberOfFixtures * $fixtureType['watts'];
    $annualKWh = ($totalWatts * 3000) / 1000; // Assume 3000 operating hours
    $annualCost = $annualKWh * 0.12; // $0.12 per kWh
    
    // Maintenance factors
    $maintenanceFactor = 0.8; // Typical for LED
    $dirtDepreciation = 0.9;
    $actualLuxMaintained = $actualLux * $maintenanceFactor * $dirtDepreciation;
    
    // Glare control
    $mountingHeight = $ceilingHeight - 0.1; // 100mm from ceiling
    $glareIndex = calculateGlareIndex($fixtureType['lumens'], $mountingHeight, $roomHeight);
    
    // Emergency lighting requirements
    $emergencyFraction = 0.1; // 10% for emergency lighting
    $emergencyFixtures = max(ceil($numberOfFixtures * $emergencyFraction), 2);
    $emergencyLumens = $emergencyFixtures * $fixtureType['lumens'] * 0.1;
    
    // Create positioning grid
    $fixturePositions = [];
    $gridRows = ceil($fixtureSpacing['length']);
    $gridCols = ceil($fixtureSpacing['width']);
    
    for ($i = 1; $i <= $gridRows; $i++) {
        for ($j = 1; $j <= $gridCols; $j++) {
            if (count($fixturePositions) < $numberOfFixtures) {
                $x = ($i - 0.5) * $roomLength / $gridRows;
                $y = ($j - 0.5) * $roomWidth / $gridCols;
                $fixturePositions[] = [
                    'x' => round($x, 2),
                    'y' => round($y, 2),
                    'zone' => getLightingZone($x, $y, $roomLength, $roomWidth)
                ];
            }
        }
    }
    
    $calculations = [
        'room_analysis' => [
            'area' => $roomArea,
            'perimeter' => $roomPerimeter,
            'height' => $roomHeight,
            'reflectances' => $reflectances
        ],
        'lighting_requirements' => [
            'target_lux' => $targetLux,
            'cu' => round($cu, 3),
            'total_lumens_required' => round($totalLumensRequired, 0)
        ],
        'fixture_layout' => [
            'type' => $lightingType,
            'specifications' => $fixtureType,
            'quantity' => $numberOfFixtures,
            'spacing' => [
                'lengthwise' => round($fixtureSpacing['length'], 2),
                'widthwise' => round($fixtureSpacing['width'], 2),
                'ratio' => round($spacingRatio, 2)
            ]
        ],
        'performance' => [
            'actual_lux' => round($actualLux, 1),
            'maintained_lux' => round($actualLuxMaintained, 1),
            'uniformity_ratio' => calculateUniformityRatio($fixtureSpacing, $ceilingHeight),
            'glare_index' => round($glareIndex, 2)
        ],
        'energy_efficiency' => [
            'total_watts' => $totalWatts,
            'watts_per_sqm' => round($totalWatts / $roomArea, 2),
            'annual_kwh' => round($annualKWh, 0),
            'annual_cost' => round($annualCost, 2)
        ],
        'emergency_lighting' => [
            'fixtures_required' => $emergencyFixtures,
            'total_lumens' => round($emergencyLumens, 0),
            'duration_hours' => 3
        ],
        'layout_positions' => $fixturePositions,
        'recommendations' => generateLightingRecommendations($actualLux, $targetLux, $spacingRatio, $glareIndex)
    ];
    
    $results = true;
}

function calculateCoefficientOfUtilization($reflectances, $height, $area, $perimeter) {
    // Simplified CU calculation based on room cavity ratio
    $rcc = ($perimeter * $height) / max($area, 1); // Room cavity ratio
    
    // Base CU for different reflectance combinations
    $baseCU = 0.6; // Typical for mixed reflectance
    
    // Adjust for reflectances
    $ceilingAdj = ($reflectances['ceiling'] - 70) / 100;
    $wallAdj = ($reflectances['walls'] - 50) / 100;
    $floorAdj = ($reflectances['floor'] - 20) / 100;
    
    $cu = $baseCU + ($ceilingAdj * 0.2) + ($wallAdj * 0.15) + ($floorAdj * 0.1);
    
    // Adjust for room proportions
    if ($rcc > 5) $cu *= 0.9; // High rooms reduce CU
    if ($rcc < 1) $cu *= 1.1; // Low rooms increase CU
    
    return max(0.1, min(0.9, $cu));
}

function calculateGlareIndex($lumens, $mountingHeight, $workingHeight) {
    // Simplified glare calculation
    $viewingAngle = atan(($mountingHeight - $workingHeight) / 1.7); // Assume 1.7m viewing height
    
    if (deg2rad(45) < $viewingAngle && $viewingAngle < deg2rad(60)) {
        return 2.0; // High glare zone
    } elseif (deg2rad(30) < $viewingAngle && $viewingAngle < deg2rad(70)) {
        return 1.5; // Medium glare zone
    }
    
    return 1.0; // Low glare
}

function calculateUniformityRatio($spacing, $height) {
    // Calculate point-to-point uniformity
    $spacingAvg = ($spacing['length'] + $spacing['width']) / 2;
    $mountingRatio = $spacingAvg / $height;
    
    if ($mountingRatio < 0.5) return 0.9; // Very uniform
    if ($mountingRatio < 1.0) return 0.8; // Uniform
    if ($mountingRatio < 1.5) return 0.7; // Acceptable
    return 0.6; // Non-uniform
}

function getLightingZone($x, $y, $length, $width) {
    $centerX = $length / 2;
    $centerY = $width / 2;
    
    $distFromCenter = sqrt(pow($x - $centerX, 2) + pow($y - $centerY, 2));
    $maxDist = sqrt(pow($centerX, 2) + pow($centerY, 2));
    
    if ($distFromCenter < $maxDist * 0.3) return 'center';
    if ($distFromCenter < $maxDist * 0.7) return 'middle';
    return 'perimeter';
}

function generateLightingRecommendations($actual, $target, $spacingRatio, $glareIndex) {
    $recommendations = [];
    
    if (abs($actual - $target) > $target * 0.1) {
        $recommendations[] = $actual < $target ? 
            'Increase fixture quantity or use higher lumen output fixtures' : 
            'Consider reducing fixture quantity or using lower output fixtures';
    }
    
    if ($spacingRatio > 3) {
        $recommendations[] = 'Adjust fixture spacing for better uniformity (spacing ratio should be < 3:1)';
    }
    
    if ($glareIndex > 1.8) {
        $recommendations[] = 'Consider glare control measures (diffusers, deeper baffles, or repositioning)';
    }
    
    if ($actual >= $target * 1.1) {
        $recommendations[] = 'Current design meets illuminance requirements with good margin';
    }
    
    $recommendations[] = 'Verify local electrical codes and safety requirements';
    $recommendations[] = 'Consider dimming controls for energy savings';
    
    return $recommendations;
}

include '../../../themes/default/views/partials/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><i class="icon-lightbulb"></i> Lighting Layout Calculator</h1>
        <p>Professional lighting design and layout optimization</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Room Parameters</h3>
                </div>
                <div class="card-body">
                    <form method="POST" class="form-horizontal">
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Room Length (m)</label>
                            <div class="col-sm-6">
                                <input type="number" step="0.1" name="room_length" class="form-control" 
                                       value="<?php echo $roomLength ?: ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Room Width (m)</label>
                            <div class="col-sm-6">
                                <input type="number" step="0.1" name="room_width" class="form-control" 
                                       value="<?php echo $roomWidth ?: ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Ceiling Height (m)</label>
                            <div class="col-sm-6">
                                <input type="number" step="0.1" name="ceiling_height" class="form-control" 
                                       value="<?php echo $ceilingHeight ?: '3.0'; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Activity Type</label>
                            <div class="col-sm-6">
                                <select name="activity_type" class="form-control">
                                    <option value="office" <?php echo $activity === 'office' ? 'selected' : ''; ?>>Office</option>
                                    <option value="classroom" <?php echo $activity === 'classroom' ? 'selected' : ''; ?>>Classroom</option>
                                    <option value="library" <?php echo $activity === 'library' ? 'selected' : ''; ?>>Library</option>
                                    <option value="corridor" <?php echo $activity === 'corridor' ? 'selected' : ''; ?>>Corridor</option>
                                    <option value="stairway" <?php echo $activity === 'stairway' ? 'selected' : ''; ?>>Stairway</option>
                                    <option value="parking" <?php echo $activity === 'parking' ? 'selected' : ''; ?>>Parking</option>
                                    <option value="warehouse" <?php echo $activity === 'warehouse' ? 'selected' : ''; ?>>Warehouse</option>
                                    <option value="workshop" <?php echo $activity === 'workshop' ? 'selected' : ''; ?>>Workshop</option>
                                    <option value="kitchen" <?php echo $activity === 'kitchen' ? 'selected' : ''; ?>>Kitchen</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Lighting Type</label>
                            <div class="col-sm-6">
                                <select name="lighting_type" class="form-control">
                                    <option value="led" <?php echo $lightingType === 'led' ? 'selected' : ''; ?>>LED</option>
                                    <option value="fluorescent" <?php echo $lightingType === 'fluorescent' ? 'selected' : ''; ?>>Fluorescent</option>
                                    <option value="halogen" <?php echo $lightingType === 'halogen' ? 'selected' : ''; ?>>Halogen</option>
                                    <option value="incandescent" <?php echo $lightingType === 'incandescent' ? 'selected' : ''; ?>>Incandescent</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Target Lux Level</label>
                            <div class="col-sm-6">
                                <input type="number" step="10" name="lux_level" class="form-control" 
                                       value="<?php echo $luxLevel; ?>">
                            </div>
                        </div>
                        
                        <h4>Surface Reflectances (%)</h4>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Ceiling Reflectance</label>
                            <div class="col-sm-6">
                                <input type="number" step="5" name="ceiling_reflectance" class="form-control" 
                                       value="<?php echo $reflectances['ceiling']; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Wall Reflectance</label>
                            <div class="col-sm-6">
                                <input type="number" step="5" name="wall_reflectance" class="form-control" 
                                       value="<?php echo $reflectances['walls']; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-6">Floor Reflectance</label>
                            <div class="col-sm-6">
                                <input type="number" step="5" name="floor_reflectance" class="form-control" 
                                       value="<?php echo $reflectances['floor']; ?>">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="icon-calculator"></i> Calculate Lighting Layout
                        </button>
                        
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <?php if ($results): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="icon-chart-line"></i> Lighting Analysis Results</h3>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Room Analysis</h4>
                            <table class="table table-condensed">
                                <tr><td>Area</td><td><?php echo number_format($calculations['room_analysis']['area'], 2); ?> m²</td></tr>
                                <tr><td>Perimeter</td><td><?php echo number_format($calculations['room_analysis']['perimeter'], 2); ?> m</td></tr>
                                <tr><td>Working Height</td><td><?php echo number_format($calculations['room_analysis']['height'], 2); ?> m</td></tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Requirements</h4>
                            <table class="table table-condensed">
                                <tr><td>Target Lux</td><td><?php echo $calculations['lighting_requirements']['target_lux']; ?> lux</td></tr>
                                <tr><td>Coefficient of Utilization</td><td><?php echo $calculations['lighting_requirements']['cu']; ?></td></tr>
                                <tr><td>Lumens Required</td><td><?php echo number_format($calculations['lighting_requirements']['total_lumens_required']); ?></td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <h4>Fixture Layout</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Type:</strong> <?php echo strtoupper($calculations['fixture_layout']['type']); ?></p>
                            <p><strong>Quantity:</strong> <?php echo $calculations['fixture_layout']['quantity']; ?> fixtures</p>
                            <p><strong>Watts per Fixture:</strong> <?php echo $calculations['fixture_layout']['specifications']['watts']; ?>W</p>
                            <p><strong>Total Power:</strong> <?php echo $calculations['energy_efficiency']['total_watts']; ?>W</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Spacing (Length):</strong> <?php echo $calculations['fixture_layout']['spacing']['lengthwise']; ?>m</p>
                            <p><strong>Spacing (Width):</strong> <?php echo $calculations['fixture_layout']['spacing']['widthwise']; ?>m</p>
                            <p><strong>Spacing Ratio:</strong> <?php echo $calculations['fixture_layout']['spacing']['ratio']; ?>:1</p>
                        </div>
                    </div>
                    
                    <h4>Performance</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Average Illuminance:</strong> 
                                <span class="<?php echo $calculations['performance']['actual_lux'] >= $calculations['lighting_requirements']['target_lux'] ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $calculations['performance']['actual_lux']; ?> lux
                                </span>
                            </p>
                            <p><strong>Maintained Illuminance:</strong> <?php echo $calculations['performance']['maintained_lux']; ?> lux</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Uniformity Ratio:</strong> <?php echo $calculations['performance']['uniformity_ratio']; ?></p>
                            <p><strong>Glare Index:</strong> <?php echo $calculations['performance']['glare_index']; ?></p>
                        </div>
                    </div>
                    
                    <h4>Energy Efficiency</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Power Density:</strong> <?php echo $calculations['energy_efficiency']['watts_per_sqm']; ?> W/m²</p>
                            <p><strong>Annual Consumption:</strong> <?php echo $calculations['energy_efficiency']['annual_kwh']; ?> kWh</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Annual Cost:</strong> $<?php echo $calculations['energy_efficiency']['annual_cost']; ?></p>
                            <p><strong>Lifespan:</strong> <?php echo $calculations['fixture_layout']['specifications']['life_hours']; ?> hours</p>
                        </div>
                    </div>
                    
                    <h4>Emergency Lighting</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Emergency Fixtures:</strong> <?php echo $calculations['emergency_lighting']['fixtures_required']; ?></p>
                            <p><strong>Emergency Lumens:</strong> <?php echo number_format($calculations['emergency_lighting']['total_lumens']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Duration:</strong> <?php echo $calculations['emergency_lighting']['duration_hours']; ?> hours</p>
                            <p><strong>Type:</strong> Battery Backup</p>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><i class="icon-lightbulb"></i> Lighting Layout Diagram</h4>
                        </div>
                        <div class="panel-body">
                            <div id="lighting-diagram" style="height: 300px; background: #f9f9f9; border: 1px solid #ddd; position: relative;">
                                <canvas id="fixture-canvas" width="400" height="250"></canvas>
                            </div>
                            <div class="text-center">
                                <small>Fixture positions shown as circles. Room boundary shown as rectangle.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h4><i class="icon-warning-sign"></i> Recommendations</h4>
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
                    <h4>Lighting Layout Calculator</h4>
                    <p>This calculator provides professional lighting design calculations including:</p>
                    <ul>
                        <li><strong>Illuminance Calculations:</strong> Determine required light levels based on activity type</li>
                        <li><strong>Fixture Sizing:</strong> Calculate number and type of fixtures needed</li>
                        <li><strong>Layout Optimization:</strong> Optimal spacing and positioning</li>
                        <li><strong>Energy Analysis:</strong> Power consumption and cost estimates</li>
                        <li><strong>Emergency Lighting:</strong> Code-compliant emergency lighting requirements</li>
                        <li><strong>Visual Layout:</strong> Fixture positioning diagram</li>
                    </ul>
                    
                    <h4>Standards Compliance</h4>
                    <ul>
                        <li>IES (Illuminating Engineering Society) standards</li>
                        <li>Local electrical codes and safety requirements</li>
                        <li>Energy efficiency guidelines</li>
                        <li>Accessibility standards for emergency lighting</li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <strong>Note:</strong> This calculator provides preliminary estimates. Final design should be verified by a licensed lighting designer or electrical engineer.
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.page-header {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
    padding: 30px;
    margin-bottom: 30px;
    border-radius: 8px;
}

.icon-lightbulb:before {
    content: "💡";
    margin-right: 10px;
}

.table-condensed td {
    padding: 4px 8px;
}

#lighting-diagram {
    border: 2px solid #ddd;
    border-radius: 4px;
}

.text-success {
    color: #27ae60;
    font-weight: bold;
}

.text-danger {
    color: #e74c3c;
    font-weight: bold;
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
</style>

<script>
// Draw lighting layout diagram
function drawLightingDiagram() {
    const canvas = document.getElementById('fixture-canvas');
    const ctx = canvas.getContext('2d');
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Room dimensions (scaled)
    const roomWidth = 300;
    const roomHeight = 200;
    const offsetX = 50;
    const offsetY = 25;
    
    // Draw room boundary
    ctx.strokeStyle = '#2c3e50';
    ctx.lineWidth = 3;
    ctx.strokeRect(offsetX, offsetY, roomWidth, roomHeight);
    
    // Draw grid lines
    ctx.strokeStyle = '#ecf0f1';
    ctx.lineWidth = 1;
    for (let i = 1; i < 6; i++) {
        const x = offsetX + (roomWidth / 6) * i;
        const y = offsetY + (roomHeight / 4) * i;
        
        ctx.beginPath();
        ctx.moveTo(x, offsetY);
        ctx.lineTo(x, offsetY + roomHeight);
        ctx.stroke();
        
        ctx.beginPath();
        ctx.moveTo(offsetX, y);
        ctx.lineTo(offsetX + roomWidth, y);
        ctx.stroke();
    }
    
    <?php if ($results): ?>
    // Draw fixtures
    const fixtures = <?php echo json_encode($calculations['layout_positions']); ?>;
    
    fixtures.forEach(fixture => {
        const x = offsetX + (fixture.x / <?php echo $roomLength; ?>) * roomWidth;
        const y = offsetY + (fixture.y / <?php echo $roomWidth; ?>) * roomHeight;
        
        // Fixture circle
        ctx.fillStyle = '#f39c12';
        ctx.beginPath();
        ctx.arc(x, y, 8, 0, 2 * Math.PI);
        ctx.fill();
        
        // Fixture border
        ctx.strokeStyle = '#d35400';
        ctx.lineWidth = 2;
        ctx.stroke();
        
        // Zone indicator
        let zoneColor = '#27ae60';
        if (fixture.zone === 'perimeter') zoneColor = '#e67e22';
        if (fixture.zone === 'middle') zoneColor = '#f39c12';
        
        ctx.strokeStyle = zoneColor;
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.arc(x, y, 12, 0, 2 * Math.PI);
        ctx.stroke();
    });
    
    // Add legend
    ctx.fillStyle = '#2c3e50';
    ctx.font = '12px Arial';
    ctx.fillText('Center Zone', offsetX + roomWidth + 10, offsetY + 20);
    ctx.fillText('Middle Zone', offsetX + roomWidth + 10, offsetY + 40);
    ctx.fillText('Perimeter Zone', offsetX + roomWidth + 10, offsetY + 60);
    
    // Legend circles
    ctx.fillStyle = '#f39c12';
    ctx.beginPath();
    ctx.arc(offsetX + roomWidth + 5, offsetY + 17, 5, 0, 2 * Math.PI);
    ctx.fill();
    ctx.beginPath();
    ctx.arc(offsetX + roomWidth + 5, offsetY + 37, 5, 0, 2 * Math.PI);
    ctx.fill();
    ctx.beginPath();
    ctx.arc(offsetX + roomWidth + 5, offsetY + 57, 5, 0, 2 * Math.PI);
    ctx.fill();
    
    // Add room dimensions
    ctx.fillStyle = '#7f8c8d';
    ctx.font = '10px Arial';
    ctx.fillText('<?php echo $roomLength; ?>m', offsetX + roomWidth/2 - 10, offsetY + roomHeight + 15);
    ctx.save();
    ctx.translate(offsetX - 15, offsetY + roomHeight/2);
    ctx.rotate(-Math.PI/2);
    ctx.fillText('<?php echo $roomWidth; ?>m', -10, 0);
    ctx.restore();
    <?php endif; ?>
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    drawLightingDiagram();
});

// Redraw on window resize
window.addEventListener('resize', drawLightingDiagram);
</script>

<?php include '../../../themes/default/views/partials/footer.php'; ?>

