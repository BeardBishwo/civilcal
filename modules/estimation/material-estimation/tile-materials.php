<?php
// modules/estimation/material-estimation/tile-materials.php
/**
 * Tile Materials Calculator
 * Calculates adhesive & grout estimation
 * Part of AEC Estimation Suite
 */

class TileMaterialsCalculator {
    private $wastage_factor;
    
    public function __construct($wastage_factor = 0.1) {
        $this->wastage_factor = $wastage_factor;
    }
    
    public function calculateAdhesive($area, $tile_type = 'ceramic', $substrate = 'concrete') {
        // Coverage rates in m² per kg
        $coverage_rates = [
            'ceramic' => ['concrete' => 4, 'plaster' => 3.5, 'existing_tiles' => 3],
            'porcelain' => ['concrete' => 3.5, 'plaster' => 3, 'existing_tiles' => 2.5],
            'natural_stone' => ['concrete' => 3, 'plaster' => 2.5, 'existing_tiles' => 2],
            'mosaic' => ['concrete' => 2.5, 'plaster' => 2, 'existing_tiles' => 1.8]
        ];
        
        $coverage = $coverage_rates[$tile_type][$substrate] ?? 4;
        $adhesive_kg = $area / $coverage;
        $adhesive_with_wastage = $adhesive_kg * (1 + $this->wastage_factor);
        
        return [
            'adhesive_kg' => ceil($adhesive_with_wastage),
            'coverage_rate' => $coverage,
            'area' => $area,
            'tile_type' => $tile_type,
            'substrate' => $substrate
        ];
    }
    
    public function calculateGrout($area, $tile_size, $grout_width = 2) {
        // Grout calculation based on tile size and joint width
        $tiles_per_m2 = 1 / ($tile_size * $tile_size / 1000000); // Convert mm² to m²
        $grout_length_per_tile = 4 * $tile_size / 1000; // Perimeter in meters
        $total_grout_length = $tiles_per_m2 * $area * $grout_length_per_tile;
        
        // Grout volume (simplified calculation)
        $grout_volume = $total_grout_length * ($grout_width / 1000) * ($grout_width / 1000) * 2;
        $grout_with_wastage = $grout_volume * (1 + $this->wastage_factor);
        
        // Convert to kg (approx 1.6 kg per liter)
        $grout_kg = $grout_with_wastage * 1600;
        
        return [
            'grout_kg' => ceil($grout_kg),
            'grout_volume' => round($grout_with_wastage, 3),
            'tile_size' => $tile_size,
            'grout_width' => $grout_width,
            'tiles_per_m2' => round($tiles_per_m2)
        ];
    }
    
    public function calculateTileQuantity($area, $tile_size) {
        $tile_area = ($tile_size * $tile_size) / 1000000; // m²
        $tiles_required = $area / $tile_area;
        $tiles_with_wastage = $tiles_required * (1 + $this->wastage_factor);
        
        return [
            'tiles_required' => ceil($tiles_with_wastage),
            'tile_area' => round($tile_area, 4),
            'area' => $area
        ];
    }
}

// Initialize calculator
$calculator = new TileMaterialsCalculator();
$adhesive_results = [];
$grout_results = [];
$tile_results = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $area = floatval($_POST['area'] ?? 0);
    $tile_size = floatval($_POST['tile_size'] ?? 300);
    $tile_type = $_POST['tile_type'] ?? 'ceramic';
    $substrate = $_POST['substrate'] ?? 'concrete';
    $grout_width = floatval($_POST['grout_width'] ?? 2);
    $wastage = floatval($_POST['wastage'] ?? 10) / 100;
    
    $calculator = new TileMaterialsCalculator($wastage);
    
    $adhesive_results = $calculator->calculateAdhesive($area, $tile_type, $substrate);
    $grout_results = $calculator->calculateGrout($area, $tile_size, $grout_width);
    $tile_results = $calculator->calculateTileQuantity($area, $tile_size);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tile Materials Calculator - AEC Toolkit</title>
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

        .coverage-info {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            text-align: left;
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
            display: <?php echo !empty($adhesive_results) ? 'block' : 'none'; ?>;
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
            transition: transform 0.3s ease;
        }

        .material-card:hover {
            transform: translateY(-5px);
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
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-border-all"></i> Tile Materials Calculator</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="area">Floor/Wall Area (m²):</label>
                    <input type="number" name="area" class="form-control" step="0.01" min="0.1" value="<?= $_POST['area'] ?? 20 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tile_size">Tile Size (mm):</label>
                    <input type="number" name="tile_size" class="form-control" value="<?= $_POST['tile_size'] ?? 300 ?>" min="50" max="1200" required>
                </div>
                
                <div class="form-group">
                    <label for="tile_type">Tile Type:</label>
                    <select name="tile_type" class="form-control">
                        <option value="ceramic" <?= ($_POST['tile_type'] ?? 'ceramic') == 'ceramic' ? 'selected' : '' ?>>Ceramic Tiles</option>
                        <option value="porcelain" <?= ($_POST['tile_type'] ?? 'ceramic') == 'porcelain' ? 'selected' : '' ?>>Porcelain Tiles</option>
                        <option value="natural_stone" <?= ($_POST['tile_type'] ?? 'ceramic') == 'natural_stone' ? 'selected' : '' ?>>Natural Stone</option>
                        <option value="mosaic" <?= ($_POST['tile_type'] ?? 'ceramic') == 'mosaic' ? 'selected' : '' ?>>Mosaic Tiles</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="substrate">Substrate Type:</label>
                    <select name="substrate" class="form-control">
                        <option value="concrete" <?= ($_POST['substrate'] ?? 'concrete') == 'concrete' ? 'selected' : '' ?>>Concrete</option>
                        <option value="plaster" <?= ($_POST['substrate'] ?? 'concrete') == 'plaster' ? 'selected' : '' ?>>Plaster</option>
                        <option value="existing_tiles" <?= ($_POST['substrate'] ?? 'concrete') == 'existing_tiles' ? 'selected' : '' ?>>Existing Tiles</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="grout_width">Grout Width (mm):</label>
                    <input type="number" name="grout_width" class="form-control" value="<?= $_POST['grout_width'] ?? 2 ?>" min="1" max="10" step="0.5" required>
                </div>
                
                <div class="form-group">
                    <label for="wastage">Wastage Percentage (%):</label>
                    <input type="number" name="wastage" class="form-control" value="<?= $_POST['wastage'] ?? 10 ?>" min="0" max="20" step="0.5" required>
                </div>

                <div class="coverage-info">
                    <h4><i class="fas fa-info-circle"></i> Coverage Information</h4>
                    <small>
                        • Ceramic: 3.5-4 m²/kg<br>
                        • Porcelain: 3-3.5 m²/kg<br>
                        • Natural Stone: 2.5-3 m²/kg<br>
                        • Mosaic: 2-2.5 m²/kg
                    </small>
                </div>

                <button type="submit" class="btn-calculate">Calculate Materials</button>
            </form>

            <?php if (!empty($adhesive_results)): ?>
            <div class="result-area">
                <h3><i class="fas fa-calculator"></i> Material Requirements</h3>
                
                <div class="result-item">
                    <span>Area to be Tiled:</span>
                    <span class="result-value"><?= $adhesive_results['area'] ?> m²</span>
                </div>
                
                <div class="result-item">
                    <span>Tile Size:</span>
                    <span class="result-value"><?= $tile_results['tile_size'] ?? 300 ?> × <?= $tile_results['tile_size'] ?? 300 ?> mm</span>
                </div>
                
                <div class="result-item">
                    <span>Tiles Required:</span>
                    <span class="result-value"><?= number_format($tile_results['tiles_required'] ?? 0) ?> nos</span>
                </div>

                <div class="materials-grid">
                    <div class="material-card">
                        <div class="material-value"><?= $adhesive_results['adhesive_kg'] ?></div>
                        <div class="material-label">Adhesive (kg)</div>
                        <small>Coverage: <?= $adhesive_results['coverage_rate'] ?> m²/kg</small>
                    </div>
                    <div class="material-card">
                        <div class="material-value"><?= $grout_results['grout_kg'] ?? 0 ?></div>
                        <div class="material-label">Grout (kg)</div>
                        <small>Width: <?= $grout_results['grout_width'] ?? 2 ?>mm</small>
                    </div>
                    <div class="material-card">
                        <div class="material-value"><?= number_format($tile_results['tiles_required'] ?? 0) ?></div>
                        <div class="material-label">Tiles</div>
                        <small><?= $tile_results['tile_area'] ?? 0 ?> m² each</small>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <h4><i class="fas fa-info-circle"></i> Project Details</h4>
                    <small>
                        • Tile Type: <?= ucfirst(str_replace('_', ' ', $adhesive_results['tile_type'])) ?><br>
                        • Substrate: <?= ucfirst(str_replace('_', ' ', $adhesive_results['substrate'])) ?><br>
                        • Grout Width: <?= $grout_results['grout_width'] ?? 2 ?>mm<br>
                        • Tiles per m²: <?= $grout_results['tiles_per_m2'] ?? 0 ?><br>
                        • Wastage: <?= ($_POST['wastage'] ?? 10) ?>% included
                    </small>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Toolkit</a>
    </div>
</body>
</html>