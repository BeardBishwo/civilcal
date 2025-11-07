<?php
/**
 * HVAC Duct Sizing Calculator
 * Calculates optimal duct dimensions based on airflow and velocity requirements
 * 
 * @package MEP
 * @subpackage Mechanical
 */

require_once '../../../includes/config.php';
require_once '../../includes/db.php';

// Initialize variables
$airflow = $velocity = $pressure_drop = $duct_type = '';
$results = [];

// Handle form submission
if ($_POST) {
    $airflow = floatval($_POST['airflow'] ?? 0);
    $velocity = floatval($_POST['velocity'] ?? 0);
    $pressure_drop = floatval($_POST['pressure_drop'] ?? 0);
    $duct_type = $_POST['duct_type'] ?? 'round';
    
    // Calculate duct dimensions
    if ($airflow > 0 && $velocity > 0) {
        // Convert units
        $airflow_cfm = $airflow * 1.699; // CFM from L/s
        $airflow_cfm = $airflow_cfm * 60; // Convert to cubic feet per minute
        $velocity_fpm = $velocity * 196.85; // FPM from m/min
        
        // Calculate cross-sectional area
        $area_sqft = $airflow_cfm / $velocity_fpm;
        $area_sqin = $area_sqft * 144; // Convert to square inches
        
        if ($duct_type == 'round') {
            $diameter_in = sqrt(4 * $area_sqin / pi());
            $diameter_mm = $diameter_in * 25.4;
            
            $results = [
                'diameter_inches' => round($diameter_in, 2),
                'diameter_mm' => round($diameter_mm, 2),
                'area_sqin' => round($area_sqin, 2),
                'airflow_cfm' => round($airflow_cfm, 0),
                'velocity_fpm' => round($velocity_fpm, 0),
                'recommended' => "Round duct with " . round($diameter_in, 1) . " inch diameter"
            ];
        } else {
            // Rectangular duct sizing (assume 2:1 aspect ratio)
            $width = sqrt(2 * $area_sqin);
            $height = $area_sqin / $width;
            
            $results = [
                'width_inches' => round($width, 2),
                'height_inches' => round($height, 2),
                'width_mm' => round($width * 25.4, 2),
                'height_mm' => round($height * 25.4, 2),
                'area_sqin' => round($area_sqin, 2),
                'airflow_cfm' => round($airflow_cfm, 0),
                'velocity_fpm' => round($velocity_fpm, 0),
                'recommended' => "Rectangular duct " . round($width, 1) . " x " . round($height, 1) . " inches"
            ];
        }
        
        // Calculate pressure drop (simplified)
        if ($pressure_drop > 0) {
            $friction_factor = 0.02; // Assumed friction factor
            $length = floatval($_POST['length'] ?? 100); // Default 100 ft
            $equivalent_length = $length * (1 + $friction_factor);
            
            $pressure_drop_pa = ($friction_factor * $equivalent_length * pow($velocity_fpm / 4005, 2)) / 2;
            $pressure_drop_pa = ($pressure_drop_pa * 0.00401463); // Convert to Pa
            
            $results['pressure_drop'] = round($pressure_drop_pa, 2);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HVAC Duct Sizing Calculator - MEP Suite</title>
    <link rel="stylesheet" href="../../../assets/css/header.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .form-section { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px; }
        .input-group input:focus, .input-group select:focus { border-color: #007bff; outline: none; }
        .results { background: #f8f9fa; padding: 25px; border-radius: 8px; border-left: 5px solid #28a745; }
        .results h3 { color: #28a745; margin-top: 0; }
        .result-item { margin-bottom: 15px; }
        .result-label { font-weight: bold; color: #555; }
        .btn { background: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    </style>
</head>
<body>
    <?php include '../../../includes/header.php'; ?>
    
    <div class="container">
        <h1>HVAC Duct Sizing Calculator</h1>
        
        <form method="POST" class="form-section">
            <div class="grid">
                <div class="input-group">
                    <label>Airflow Rate:</label>
                    <input type="number" step="0.01" name="airflow" value="<?= htmlspecialchars($airflow) ?>" placeholder="e.g., 1000">
                    <small>Liters per second (L/s)</small>
                </div>
                
                <div class="input-group">
                    <label>Air Velocity:</label>
                    <input type="number" step="0.1" name="velocity" value="<?= htmlspecialchars($velocity) ?>" placeholder="e.g., 300">
                    <small>Meters per minute (m/min)</small>
                </div>
                
                <div class="input-group">
                    <label>Duct Type:</label>
                    <select name="duct_type">
                        <option value="round" <?= $duct_type == 'round' ? 'selected' : '' ?>>Round</option>
                        <option value="rectangular" <?= $duct_type == 'rectangular' ? 'selected' : '' ?>>Rectangular</option>
                    </select>
                </div>
                
                <div class="input-group">
                    <label>Duct Length (optional):</label>
                    <input type="number" step="0.1" name="length" value="<?= $_POST['length'] ?? '100' ?>" placeholder="e.g., 100">
                    <small>Feet</small>
                </div>
            </div>
            
            <button type="submit" class="btn">Calculate Duct Size</button>
        </form>
        
        <?php if (!empty($results)): ?>
            <div class="results">
                <h3>Duct Sizing Results</h3>
                <p><strong><?= $results['recommended'] ?></strong></p>
                
                <?php if ($duct_type == 'round'): ?>
                    <div class="result-item">
                        <span class="result-label">Diameter:</span> 
                        <?= $results['diameter_inches'] ?> inches (<?= $results['diameter_mm'] ?> mm)
                    </div>
                <?php else: ?>
                    <div class="result-item">
                        <span class="result-label">Dimensions:</span> 
                        <?= $results['width_inches'] ?> x <?= $results['height_inches'] ?> inches
                        (<?= $results['width_mm'] ?> x <?= $results['height_mm'] ?> mm)
                    </div>
                <?php endif; ?>
                
                <div class="result-item">
                    <span class="result-label">Cross-sectional Area:</span> 
                    <?= $results['area_sqin'] ?> sq inches
                </div>
                
                <div class="result-item">
                    <span class="result-label">Airflow:</span> 
                    <?= $results['airflow_cfm'] ?> CFM
                </div>
                
                <div class="result-item">
                    <span class="result-label">Velocity:</span> 
                    <?= $results['velocity_fpm'] ?> FPM
                </div>
                
                <?php if (isset($results['pressure_drop'])): ?>
                <div class="result-item">
                    <span class="result-label">Pressure Drop:</span> 
                    <?= $results['pressure_drop'] ?> Pa
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
