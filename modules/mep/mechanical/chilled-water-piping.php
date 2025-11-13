<?php
/**
 * Chilled Water Piping Calculator
 * Calculates chilled water flow, velocity, and pipe sizing
 * 
 * @package MEP
 * @subpackage Mechanical
 */

require_once '../../../app/Config/config.php';
require_once '../../../app/Config/db.php';

// Initialize variables
$cooling_load = $delta_t = $pipe_length = $fittings = '';
$results = [];

// Handle form submission
if ($_POST) {
    $cooling_load = floatval($_POST['cooling_load'] ?? 0);
    $delta_t = floatval($_POST['delta_t'] ?? 0);
    $pipe_length = floatval($_POST['pipe_length'] ?? 0);
    $fittings = intval($_POST['fittings'] ?? 0);
    
    if ($cooling_load > 0 && $delta_t > 0) {
        // Calculate water flow rate (GPM)
        // Formula: GPM = (Load in BTU/hr) / (500 * ΔT)
        $flow_rate_gpm = $cooling_load / (500 * $delta_t);
        
        // Convert to other units
        $flow_rate_lps = $flow_rate_gpm * 0.0630902; // L/s
        $flow_rate_lpm = $flow_rate_lps * 60; // L/min
        
        // Pipe sizing based on velocity (2-10 ft/s typical for chilled water)
        $target_velocity = 5.0; // ft/s (good balance)
        $velocity_factor = 2.4; // Conversion factor for flow in GPM to pipe area
        
        // Calculate required pipe area and diameter
        $pipe_area_sqin = $flow_rate_gpm / ($target_velocity * velocity_factor);
        $pipe_diameter_in = sqrt(4 * $pipe_area_sqin / pi());
        $pipe_diameter_mm = $pipe_diameter_in * 25.4;
        
        // Actual velocity with sized pipe
        $actual_velocity = $flow_rate_gpm / ($pipe_area_sqin * velocity_factor);
        
        // Pressure drop calculation (simplified Hazen-Williams)
        if ($pipe_length > 0) {
            $c_factor = 120; // Hazen-Williams C factor for new steel pipe
            $equivalent_length = $pipe_length + ($fittings * 10); // 10 ft per fitting
            
            // Pressure drop in ft of water per 100 ft of pipe
            $pressure_drop_per_100ft = (1.318 * pow($c_factor, -1.852) * pow($flow_rate_gpm, 1.852) * pow($pipe_diameter_in, -4.871)) * 100;
            
            // Total pressure drop
            $total_pressure_drop = $pressure_drop_per_100ft * ($equivalent_length / 100);
            
            // Convert to PSI
            $pressure_drop_psi = $total_pressure_drop * 0.433;
            
            // Pumping power requirement
            $pumping_power_hp = ($flow_rate_gpm * $total_pressure_drop) / (3960 * 0.65); // 65% pump efficiency
            $pumping_power_kw = $pumping_power_hp * 0.746;
        }
        
        // Standard pipe sizes (schedule 40)
        $standard_sizes = [0.5, 0.75, 1, 1.25, 1.5, 2, 2.5, 3, 4, 5, 6, 8, 10, 12];
        $selected_size = 2; // Default
        
        foreach ($standard_sizes as $size) {
            if ($size >= $pipe_diameter_in) {
                $selected_size = $size;
                break;
            }
        }
        
        $selected_area = pi() * pow($selected_size/2, 2);
        $selected_velocity = $flow_rate_gpm / ($selected_area * $velocity_factor);
        
        $results = [
            'flow_rate_gpm' => round($flow_rate_gpm, 1),
            'flow_rate_lps' => round($flow_rate_lps, 2),
            'flow_rate_lpm' => round($flow_rate_lpm, 0),
            'calculated_diameter' => round($pipe_diameter_in, 2),
            'calculated_diameter_mm' => round($pipe_diameter_mm, 1),
            'selected_diameter' => $selected_size,
            'selected_diameter_mm' => round($selected_size * 25.4, 1),
            'actual_velocity' => round($actual_velocity, 2),
            'selected_velocity' => round($selected_velocity, 2),
            'target_velocity' => $target_velocity
        ];
        
        if (isset($pressure_drop_psi)) {
            $results['pressure_drop_psi'] = round($pressure_drop_psi, 2);
            $results['pumping_power_hp'] = round($pumping_power_hp, 2);
            $results['pumping_power_kw'] = round($pumping_power_kw, 2);
            $results['equivalent_length'] = $equivalent_length;
        }
        
        // Recommendations
        $results['recommendations'] = [];
        
        if ($actual_velocity < 2) {
            $results['recommendations'][] = "Velocity is low - consider smaller pipe to increase velocity";
        } elseif ($actual_velocity > 10) {
            $results['recommendations'][] = "Velocity is high - consider larger pipe to reduce velocity";
        }
        
        if (isset($pressure_drop_psi) && $pressure_drop_psi > 10) {
            $results['recommendations'][] = "High pressure drop - consider larger pipe or shorter runs";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chilled Water Piping Calculator - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .form-section { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .input-group input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px; }
        .results { background: #f8f9fa; padding: 25px; border-radius: 8px; border-left: 5px solid #17a2b8; }
        .results h3 { color: #17a2b8; margin-top: 0; }
        .result-item { margin-bottom: 15px; padding: 10px; background: white; border-radius: 4px; }
        .result-label { font-weight: bold; color: #555; }
        .recommendations { background: #d4edda; padding: 15px; border-radius: 6px; border-left: 5px solid #28a745; }
        .recommendations h4 { color: #155724; margin-top: 0; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .btn { background: #17a2b8; color: white; padding: 12px 30px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; }
        .highlight { background: #e7f3ff; padding: 10px; border-radius: 4px; border-left: 4px solid #007bff; }
    </style>
</head>
<body>
    <?php include '../../../themes/default/views/partials/header.php'; ?>
    
    <div class="container">
        <h1>Chilled Water Piping Calculator</h1>
        
        <form method="POST" class="form-section">
            <div class="grid">
                <div class="input-group">
                    <label>Cooling Load:</label>
                    <input type="number" step="1" name="cooling_load" value="<?= htmlspecialchars($cooling_load) ?>" placeholder="e.g., 100000" required>
                    <small>BTU per hour</small>
                </div>
                
                <div class="input-group">
                    <label>Temperature Difference (ΔT):</label>
                    <input type="number" step="0.1" name="delta_t" value="<?= htmlspecialchars($delta_t) ?>" placeholder="e.g., 10" required>
                    <small>Degrees Fahrenheit</small>
                </div>
                
                <div class="input-group">
                    <label>Pipe Length:</label>
                    <input type="number" step="1" name="pipe_length" value="<?= htmlspecialchars($pipe_length) ?>" placeholder="e.g., 200">
                    <small>Feet (optional for pressure drop)</small>
                </div>
                
                <div class="input-group">
                    <label>Number of Fittings:</label>
                    <input type="number" step="1" name="fittings" value="<?= htmlspecialchars($fittings) ?>" placeholder="e.g., 12">
                    <small>Elbows, tees, valves, etc.</small>
                </div>
            </div>
            
            <button type="submit" class="btn">Calculate Pipe Sizing</button>
        </form>
        
        <?php if (!empty($results)): ?>
            <div class="results">
                <h3>Chilled Water System Analysis</h3>
                
                <div class="highlight">
                    <strong>Recommended Pipe Size:</strong> <?= $results['selected_diameter'] ?>" Schedule 40 Steel Pipe
                </div>
                
                <div class="grid">
                    <div class="result-item">
                        <span class="result-label">Flow Rate:</span> 
                        <?= $results['flow_rate_gpm'] ?> GPM
                        <br><small><?= $results['flow_rate_lpm'] ?> L/min | <?= $results['flow_rate_lps'] ?> L/s</small>
                    </div>
                    
                    <div class="result-item">
                        <span class="result-label">Calculated Diameter:</span> 
                        <?= $results['calculated_diameter'] ?> inches (<?= $results['calculated_diameter_mm'] ?> mm)
                    </div>
                    
                    <div class="result-item">
                        <span class="result-label">Actual Water Velocity:</span> 
                        <?= $results['actual_velocity'] ?> ft/s
                    </div>
                    
                    <div class="result-item">
                        <span class="result-label">Target Velocity:</span> 
                        <?= $results['target_velocity'] ?> ft/s
                    </div>
                </div>
                
                <?php if (isset($results['pressure_drop_psi'])): ?>
                    <h4>Hydraulic Analysis</h4>
                    <div class="grid">
                        <div class="result-item">
                            <span class="result-label">Total Pressure Drop:</span> 
                            <?= $results['pressure_drop_psi'] ?> PSI
                        </div>
                        
                        <div class="result-item">
                            <span class="result-label">Pumping Power Required:</span> 
                            <?= $results['pumping_power_hp'] ?> HP (<?= $results['pumping_power_kw'] ?> kW)
                        </div>
                        
                        <div class="result-item">
                            <span class="result-label">Equivalent Length:</span> 
                            <?= $results['equivalent_length'] ?> feet
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($results['recommendations'])): ?>
                    <div class="recommendations">
                        <h4>Design Recommendations</h4>
                        <ul>
                            <?php foreach ($results['recommendations'] as $recommendation): ?>
                                <li><?= htmlspecialchars($recommendation) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

