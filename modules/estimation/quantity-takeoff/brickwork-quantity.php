<?php
// modules/estimation/quantity-takeoff/brickwork-quantity.php
/**
 * Brickwork Quantity Calculator
 * Estimates bricks and mortar quantity for walls
 * Part of AEC Estimation Suite
 */

class BrickworkCalculator {
    private $wastage_factor;
    
    public function __construct($wastage_factor = 0.05) {
        $this->wastage_factor = $wastage_factor;
    }
    
    public function calculateBrickQuantity($wall_length, $wall_height, $brick_length, $brick_height, $brick_width, $mortar_thickness, $opening_area = 0) {
        // Convert mm to meters
        $brick_length_m = $brick_length / 1000;
        $brick_height_m = $brick_height / 1000;
        $mortar_thickness_m = $mortar_thickness / 1000;
        
        // Calculate wall area (subtract openings)
        $gross_wall_area = $wall_length * $wall_height;
        $net_wall_area = $gross_wall_area - $opening_area;
        
        // Calculate brick area with mortar
        $brick_area_with_mortar = ($brick_length_m + $mortar_thickness_m) * ($brick_height_m + $mortar_thickness_m);
        
        // Number of bricks
        $number_of_bricks = $net_wall_area / $brick_area_with_mortar;
        $number_of_bricks_with_wastage = $number_of_bricks * (1 + $this->wastage_factor);
        
        // Calculate mortar volume (per cubic meter)
        $brick_volume = ($brick_length_m * $brick_height_m * $brick_width / 1000);
        $brick_volume_with_mortar = ($brick_length_m + $mortar_thickness_m) * ($brick_height_m + $mortar_thickness_m) * ($brick_width / 1000 + $mortar_thickness_m);
        $mortar_volume_per_brick = $brick_volume_with_mortar - $brick_volume;
        $total_mortar_volume = $mortar_volume_per_brick * $number_of_bricks_with_wastage;
        
        return [
            'bricks_required' => ceil($number_of_bricks_with_wastage),
            'mortar_volume' => round($total_mortar_volume, 3),
            'wall_area' => round($net_wall_area, 2),
            'units' => [
                'bricks' => 'nos',
                'mortar' => 'm³',
                'area' => 'm²'
            ]
        ];
    }
    
    public function estimateMortarMaterials($mortar_volume, $mix_ratio = '1:6') {
        // Cement:Sand ratio
        list($cement, $sand) = explode(':', $mix_ratio);
        $total_parts = $cement + $sand;
        
        $cement_volume = $mortar_volume * ($cement / $total_parts);
        $sand_volume = $mortar_volume * ($sand / $total_parts);
        
        // Cement in bags (1 bag = 0.035 m³)
        $cement_bags = $cement_volume / 0.035;
        
        return [
            'cement_bags' => ceil($cement_bags),
            'sand_volume' => round($sand_volume, 2),
            'mix_ratio' => $mix_ratio
        ];
    }
}

// Initialize calculator
$calculator = new BrickworkCalculator();
$results = [];
$mortar_materials = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wall_length = floatval($_POST['wall_length'] ?? 0);
    $wall_height = floatval($_POST['wall_height'] ?? 0);
    $brick_length = floatval($_POST['brick_length'] ?? 230);
    $brick_height = floatval($_POST['brick_height'] ?? 75);
    $brick_width = floatval($_POST['brick_width'] ?? 115);
    $mortar_thickness = floatval($_POST['mortar_thickness'] ?? 10);
    $opening_area = floatval($_POST['opening_area'] ?? 0);
    $wastage = floatval($_POST['wastage'] ?? 5) / 100;
    $mortar_mix = $_POST['mortar_mix'] ?? '1:6';
    
    $calculator = new BrickworkCalculator($wastage);
    $results = $calculator->calculateBrickQuantity($wall_length, $wall_height, $brick_length, $brick_height, $brick_width, $mortar_thickness, $opening_area);
    
    if (!empty($results)) {
        $mortar_materials = $calculator->estimateMortarMaterials($results['mortar_volume'], $mortar_mix);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brickwork Quantity Calculator - AEC Toolkit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #f093fb;
            --dark: #1a202c;
            --light: #f7fafc;
            --glass: rgba(255, 255, 255, 0.05);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            color: var(--light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }

        .calculator-wrapper {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow);
            margin-top: 3rem;
        }

        .calculator-wrapper h1 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: #feca57;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--light);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #f093fb;
            box-shadow: 0 0 15px rgba(240, 147, 251, 0.3);
        }

        .btn-calculate {
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, #f093fb, #f5576c);
            border: none;
            border-radius: 50px;
            color: var(--light);
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-calculate:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .result-area {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
            display: <?php echo !empty($results) ? 'block' : 'none'; ?>;
        }

        .result-area h3 {
            font-size: 1.5rem;
            color: #feca57;
            margin-bottom: 1rem;
        }

        .result-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .result-value {
            font-weight: 700;
            color: #feca57;
            font-size: 1.2rem;
        }

        .materials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .material-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }

        .material-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #feca57;
        }

        .material-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: #f093fb;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-bricks"></i> Brickwork Quantity Calculator</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="wall_length">Wall Length (meters):</label>
                    <input type="number" name="wall_length" class="form-control" step="0.01" min="0.1" value="<?= $_POST['wall_length'] ?? 10 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="wall_height">Wall Height (meters):</label>
                    <input type="number" name="wall_height" class="form-control" step="0.01" min="0.1" value="<?= $_POST['wall_height'] ?? 3 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="opening_area">Openings Area (m² - doors, windows):</label>
                    <input type="number" name="opening_area" class="form-control" step="0.01" min="0" value="<?= $_POST['opening_area'] ?? 0 ?>">
                </div>
                
                <div class="form-group">
                    <label for="brick_length">Brick Length (mm):</label>
                    <input type="number" name="brick_length" class="form-control" value="<?= $_POST['brick_length'] ?? 230 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="brick_height">Brick Height (mm):</label>
                    <input type="number" name="brick_height" class="form-control" value="<?= $_POST['brick_height'] ?? 75 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="brick_width">Brick Width (mm):</label>
                    <input type="number" name="brick_width" class="form-control" value="<?= $_POST['brick_width'] ?? 115 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mortar_thickness">Mortar Thickness (mm):</label>
                    <input type="number" name="mortar_thickness" class="form-control" value="<?= $_POST['mortar_thickness'] ?? 10 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="wastage">Wastage Percentage (%):</label>
                    <input type="number" name="wastage" class="form-control" value="<?= $_POST['wastage'] ?? 5 ?>" min="0" max="20" step="0.5" required>
                </div>
                
                <div class="form-group">
                    <label for="mortar_mix">Mortar Mix Ratio (Cement:Sand):</label>
                    <select name="mortar_mix" class="form-control">
                        <option value="1:4" <?= ($_POST['mortar_mix'] ?? '1:6') == '1:4' ? 'selected' : '' ?>>1:4 (Rich Mix)</option>
                        <option value="1:5" <?= ($_POST['mortar_mix'] ?? '1:6') == '1:5' ? 'selected' : '' ?>>1:5</option>
                        <option value="1:6" <?= ($_POST['mortar_mix'] ?? '1:6') == '1:6' ? 'selected' : '' ?>>1:6 (Standard)</option>
                        <option value="1:8" <?= ($_POST['mortar_mix'] ?? '1:6') == '1:8' ? 'selected' : '' ?>>1:8 (Lean Mix)</option>
                    </select>
                </div>

                <button type="submit" class="btn-calculate">Calculate Brickwork Quantity</button>
            </form>

            <?php if (!empty($results)): ?>
            <div class="result-area">
                <h3><i class="fas fa-calculator"></i> Calculation Results</h3>
                
                <div class="result-item">
                    <span>Wall Area (Net):</span>
                    <span class="result-value"><?= $results['wall_area'] ?> m²</span>
                </div>
                
                <div class="result-item">
                    <span>Bricks Required:</span>
                    <span class="result-value"><?= number_format($results['bricks_required']) ?> nos</span>
                </div>
                
                <div class="result-item">
                    <span>Mortar Volume:</span>
                    <span class="result-value"><?= $results['mortar_volume'] ?> m³</span>
                </div>

                <?php if (!empty($mortar_materials)): ?>
                <div style="margin-top: 2rem;">
                    <h4><i class="fas fa-tools"></i> Mortar Materials Required (<?= $mortar_materials['mix_ratio'] ?> mix)</h4>
                    <div class="materials-grid">
                        <div class="material-card">
                            <div class="material-value"><?= $mortar_materials['cement_bags'] ?></div>
                            <div class="material-label">Cement Bags</div>
                        </div>
                        <div class="material-card">
                            <div class="material-value"><?= $mortar_materials['sand_volume'] ?></div>
                            <div class="material-label">Sand (m³)</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Toolkit</a>
    </div>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>