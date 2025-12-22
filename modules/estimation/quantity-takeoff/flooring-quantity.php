<?php
// modules/estimation/quantity-takeoff/flooring-quantity.php
/**
 * Flooring Quantity Calculator
 * Estimates tiles, marble, or floor finish materials
 * Part of AEC Estimation Suite
 */

class FlooringCalculator {
    private $wastage_factor;
    
    public function __construct($wastage_factor = 0.1) {
        $this->wastage_factor = $wastage_factor;
    }
    
    public function calculateFlooring($room_length, $room_width, $tile_length, $tile_width, $grout_width = 2) {
        $room_area = $room_length * $room_width;
        
        // Convert mm to meters for tiles
        $tile_length_m = $tile_length / 1000;
        $tile_width_m = $tile_width / 1000;
        $grout_width_m = $grout_width / 1000;
        
        // Calculate effective tile area including grout
        $effective_tile_length = $tile_length_m + $grout_width_m;
        $effective_tile_width = $tile_width_m + $grout_width_m;
        $effective_tile_area = $effective_tile_length * $effective_tile_width;
        
        // Calculate number of tiles
        $number_of_tiles = $room_area / $effective_tile_area;
        $number_of_tiles_with_wastage = $number_of_tiles * (1 + $this->wastage_factor);
        
        // Calculate grout volume (approximate)
        $grout_length = ($room_length / $tile_length_m + $room_width / $tile_width_m) * 2;
        $grout_volume = $grout_length * $grout_width_m * $tile_length_m * 0.001; // Simplified calculation
        
        return [
            'room_area' => round($room_area, 2),
            'tiles_required' => ceil($number_of_tiles_with_wastage),
            'grout_volume' => round(max($grout_volume, 0.01), 3),
            'tile_area' => round($tile_length_m * $tile_width_m, 4),
            'units' => [
                'area' => 'm²',
                'tiles' => 'nos',
                'grout' => 'm³'
            ]
        ];
    }
    
    public function calculateAdhesive($area, $coverage_per_kg = 4) {
        // Coverage: 4 m² per kg for standard tile adhesive
        $adhesive_kg = $area / $coverage_per_kg;
        return ceil($adhesive_kg);
    }
}

// Initialize calculator
$calculator = new FlooringCalculator();
$results = [];
$adhesive_required = 0;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_length = floatval($_POST['room_length'] ?? 0);
    $room_width = floatval($_POST['room_width'] ?? 0);
    $tile_length = floatval($_POST['tile_length'] ?? 600);
    $tile_width = floatval($_POST['tile_width'] ?? 600);
    $grout_width = floatval($_POST['grout_width'] ?? 2);
    $wastage = floatval($_POST['wastage'] ?? 10) / 100;
    $adhesive_coverage = floatval($_POST['adhesive_coverage'] ?? 4);
    
    $calculator = new FlooringCalculator($wastage);
    $results = $calculator->calculateFlooring($room_length, $room_width, $tile_length, $tile_width, $grout_width);
    
    if (!empty($results)) {
        $adhesive_required = $calculator->calculateAdhesive($results['room_area'], $adhesive_coverage);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flooring Quantity Calculator - AEC Toolkit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ffffff;
            --secondary: #ffffff;
            --accent: #ffffff;
            --dark: #000000;
            --light: #ffffff;
            --glass: rgba(255, 255, 255, 0.05);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        body {
            background: linear-gradient(135deg, #000000, #000000, #000000);
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
            color: #ffffff;
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
            border-color: #ffffff;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }

        .btn-calculate {
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, #ffffff, #ffffff);
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
            color: #ffffff;
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
            color: #ffffff;
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
            color: #ffffff;
        }

        .material-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: #ffffff;
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
            <h1><i class="fas fa-border-all"></i> Flooring Quantity Calculator</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="room_length">Room Length (meters):</label>
                    <input type="number" name="room_length" class="form-control" step="0.01" min="0.1" value="<?= $_POST['room_length'] ?? 5 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="room_width">Room Width (meters):</label>
                    <input type="number" name="room_width" class="form-control" step="0.01" min="0.1" value="<?= $_POST['room_width'] ?? 4 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tile_length">Tile Length (mm):</label>
                    <input type="number" name="tile_length" class="form-control" value="<?= $_POST['tile_length'] ?? 600 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tile_width">Tile Width (mm):</label>
                    <input type="number" name="tile_width" class="form-control" value="<?= $_POST['tile_width'] ?? 600 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="grout_width">Grout Width (mm):</label>
                    <input type="number" name="grout_width" class="form-control" value="<?= $_POST['grout_width'] ?? 2 ?>" min="1" max="10" step="0.5">
                </div>
                
                <div class="form-group">
                    <label for="wastage">Wastage Percentage (%):</label>
                    <input type="number" name="wastage" class="form-control" value="<?= $_POST['wastage'] ?? 10 ?>" min="0" max="20" step="0.5" required>
                </div>
                
                <div class="form-group">
                    <label for="adhesive_coverage">Adhesive Coverage (m² per kg):</label>
                    <input type="number" name="adhesive_coverage" class="form-control" value="<?= $_POST['adhesive_coverage'] ?? 4 ?>" min="1" max="10" step="0.1" required>
                </div>

                <button type="submit" class="btn-calculate">Calculate Flooring Quantity</button>
            </form>

            <?php if (!empty($results)): ?>
            <div class="result-area">
                <h3><i class="fas fa-calculator"></i> Calculation Results</h3>
                
                <div class="result-item">
                    <span>Room Area:</span>
                    <span class="result-value"><?= $results['room_area'] ?> m²</span>
                </div>
                
                <div class="result-item">
                    <span>Tiles Required:</span>
                    <span class="result-value"><?= number_format($results['tiles_required']) ?> nos</span>
                </div>
                
                <div class="result-item">
                    <span>Tile Area (each):</span>
                    <span class="result-value"><?= $results['tile_area'] ?> m²</span>
                </div>

                <div style="margin-top: 2rem;">
                    <h4><i class="fas fa-tools"></i> Material Requirements</h4>
                    <div class="materials-grid">
                        <div class="material-card">
                            <div class="material-value"><?= number_format($results['tiles_required']) ?></div>
                            <div class="material-label">Tiles</div>
                        </div>
                        <div class="material-card">
                            <div class="material-value"><?= $adhesive_required ?></div>
                            <div class="material-label">Adhesive (kg)</div>
                        </div>
                        <div class="material-card">
                            <div class="material-value"><?= $results['grout_volume'] ?></div>
                            <div class="material-label">Grout (m³)</div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <small><i class="fas fa-info-circle"></i> Note: Grout volume is approximate. Actual requirement may vary based on application method.</small>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Toolkit</a>
    </div>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
