<?php
// modules/estimation/material-estimation/plaster-materials.php
/**
 * Plaster Materials Calculator
 * Calculates material consumption for given thickness & area
 * Part of AEC Estimation Suite
 */

class PlasterMaterialsCalculator {
    private $wastage_factor;
    
    public function __construct($wastage_factor = 0.05) {
        $this->wastage_factor = $wastage_factor;
    }
    
    public function calculateMaterials($area, $thickness, $mix_ratio = '1:4') {
        // Convert thickness from mm to meters
        $thickness_m = $thickness / 1000;
        
        // Calculate mortar volume
        $mortar_volume = $area * $thickness_m;
        $mortar_volume_with_wastage = $mortar_volume * (1 + $this->wastage_factor);
        
        // Calculate materials based on mix ratio
        list($cement, $sand) = explode(':', $mix_ratio);
        $total_parts = $cement + $sand;
        
        $cement_volume = $mortar_volume_with_wastage * ($cement / $total_parts);
        $sand_volume = $mortar_volume_with_wastage * ($sand / $total_parts);
        
        // Convert to practical units
        $cement_bags = $cement_volume / 0.035; // 1 bag = 0.035 m³
        
        return [
            'area' => round($area, 2),
            'thickness' => $thickness,
            'mortar_volume' => round($mortar_volume, 3),
            'mortar_with_wastage' => round($mortar_volume_with_wastage, 3),
            'cement_bags' => ceil($cement_bags),
            'sand_volume' => round($sand_volume, 3),
            'mix_ratio' => $mix_ratio,
            'units' => [
                'area' => 'm²',
                'mortar' => 'm³',
                'cement' => 'bags',
                'sand' => 'm³'
            ]
        ];
    }
    
    public function getRecommendedThickness() {
        return [
            'internal' => ['min' => 10, 'max' => 12, 'recommended' => 12],
            'external' => ['min' => 15, 'max' => 20, 'recommended' => 18],
            'ceiling' => ['min' => 6, 'max' => 10, 'recommended' => 8]
        ];
    }
}

// Initialize calculator
$calculator = new PlasterMaterialsCalculator();
$results = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $area = floatval($_POST['area'] ?? 0);
    $thickness = floatval($_POST['thickness'] ?? 12);
    $mix_ratio = $_POST['mix_ratio'] ?? '1:4';
    $wastage = floatval($_POST['wastage'] ?? 5) / 100;
    $surface_type = $_POST['surface_type'] ?? 'internal';
    
    $calculator = new PlasterMaterialsCalculator($wastage);
    $results = $calculator->calculateMaterials($area, $thickness, $mix_ratio);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plaster Materials Calculator - AEC Toolkit</title>
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

        .thickness-recommendation {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            text-align: left;
        }

        .thickness-recommendation h4 {
            color: #ffffff;
            margin-bottom: 0.5rem;
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
            transition: transform 0.3s ease;
        }

        .material-card:hover {
            transform: translateY(-5px);
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
            <h1><i class="fas fa-trowel"></i> Plaster Materials Calculator</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="area">Plaster Area (m²):</label>
                    <input type="number" name="area" class="form-control" step="0.01" min="0.1" value="<?= $_POST['area'] ?? 100 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="surface_type">Surface Type:</label>
                    <select name="surface_type" id="surface_type" class="form-control" onchange="updateThicknessRecommendation()">
                        <option value="internal" <?= ($_POST['surface_type'] ?? 'internal') == 'internal' ? 'selected' : '' ?>>Internal Walls</option>
                        <option value="external" <?= ($_POST['surface_type'] ?? 'internal') == 'external' ? 'selected' : '' ?>>External Walls</option>
                        <option value="ceiling" <?= ($_POST['surface_type'] ?? 'internal') == 'ceiling' ? 'selected' : '' ?>>Ceiling</option>
                    </select>
                </div>
                
                <div class="thickness-recommendation" id="thickness_recommendation">
                    <h4><i class="fas fa-lightbulb"></i> Recommended Thickness</h4>
                    <div id="recommendation_text">
                        <!-- Will be filled by JavaScript -->
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="thickness">Plaster Thickness (mm):</label>
                    <input type="number" name="thickness" id="thickness" class="form-control" value="<?= $_POST['thickness'] ?? 12 ?>" min="6" max="25" step="1" required>
                </div>
                
                <div class="form-group">
                    <label for="mix_ratio">Plaster Mix Ratio (Cement:Sand):</label>
                    <select name="mix_ratio" class="form-control">
                        <option value="1:3" <?= ($_POST['mix_ratio'] ?? '1:4') == '1:3' ? 'selected' : '' ?>>1:3 (Rich Mix)</option>
                        <option value="1:4" <?= ($_POST['mix_ratio'] ?? '1:4') == '1:4' ? 'selected' : '' ?>>1:4 (Standard)</option>
                        <option value="1:5" <?= ($_POST['mix_ratio'] ?? '1:4') == '1:5' ? 'selected' : '' ?>>1:5 (Lean Mix)</option>
                        <option value="1:6" <?= ($_POST['mix_ratio'] ?? '1:4') == '1:6' ? 'selected' : '' ?>>1:6</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="wastage">Wastage Percentage (%):</label>
                    <input type="number" name="wastage" class="form-control" value="<?= $_POST['wastage'] ?? 5 ?>" min="0" max="20" step="0.5" required>
                </div>

                <button type="submit" class="btn-calculate">Calculate Materials</button>
            </form>

            <?php if (!empty($results)): ?>
            <div class="result-area">
                <h3><i class="fas fa-calculator"></i> Material Requirements</h3>
                
                <div class="result-item">
                    <span>Plaster Area:</span>
                    <span class="result-value"><?= $results['area'] ?> m²</span>
                </div>
                
                <div class="result-item">
                    <span>Plaster Thickness:</span>
                    <span class="result-value"><?= $results['thickness'] ?> mm</span>
                </div>
                
                <div class="result-item">
                    <span>Mortar Volume:</span>
                    <span class="result-value"><?= $results['mortar_volume'] ?> m³</span>
                </div>
                
                <div class="result-item">
                    <span>Mortar with Wastage:</span>
                    <span class="result-value"><?= $results['mortar_with_wastage'] ?> m³</span>
                </div>

                <div class="materials-grid">
                    <div class="material-card">
                        <div class="material-value"><?= $results['cement_bags'] ?></div>
                        <div class="material-label">Cement Bags</div>
                        <small>@ 50kg per bag</small>
                    </div>
                    <div class="material-card">
                        <div class="material-value"><?= $results['sand_volume'] ?></div>
                        <div class="material-label">Sand (m³)</div>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <h4><i class="fas fa-info-circle"></i> Calculation Details</h4>
                    <small>
                        • Mix Ratio: <?= $results['mix_ratio'] ?> (Cement:Sand)<br>
                        • Surface Type: <?= ucfirst($_POST['surface_type'] ?? 'internal') ?><br>
                        • Wastage: <?= ($_POST['wastage'] ?? 5) ?>% included<br>
                        • Coverage: Approximately <?= round($results['area'] / $results['cement_bags'], 2) ?> m² per cement bag
                    </small>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Toolkit</a>
    </div>

    <script>
        const thicknessRecommendations = {
            'internal': { min: 10, max: 12, recommended: 12 },
            'external': { min: 15, max: 20, recommended: 18 },
            'ceiling': { min: 6, max: 10, recommended: 8 }
        };

        function updateThicknessRecommendation() {
            const surfaceType = document.getElementById('surface_type').value;
            const recommendation = thicknessRecommendations[surfaceType];
            const thicknessInput = document.getElementById('thickness');
            const recommendationText = document.getElementById('recommendation_text');
            
            // Update recommendation text
            recommendationText.innerHTML = `
                Recommended: <strong>${recommendation.recommended}mm</strong><br>
                Range: ${recommendation.min}mm - ${recommendation.max}mm
            `;
            
            // Update thickness input value
            thicknessInput.value = recommendation.recommended;
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateThicknessRecommendation);
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
