<?php
// modules/estimation/material-estimation/concrete-materials.php
/**
 * Concrete Materials Calculator
 * Calculates cement, sand, aggregate for given mix ratio
 * Part of AEC Estimation Suite
 */

class ConcreteMaterialsCalculator {
    private $wastage_factor;
    
    public function __construct($wastage_factor = 0.05) {
        $this->wastage_factor = $wastage_factor;
    }
    
    public function calculateMaterials($volume, $mix_ratio = '1:1.5:3') {
        // Split mix ratio (Cement:Sand:Aggregate)
        list($cement, $sand, $aggregate) = explode(':', $mix_ratio);
        $total_parts = $cement + $sand + $aggregate;
        
        // Dry volume factor (concrete shrinks when wet)
        $dry_volume = $volume * 1.54;
        $dry_volume_with_wastage = $dry_volume * (1 + $this->wastage_factor);
        
        // Calculate volumes
        $cement_volume = $dry_volume_with_wastage * ($cement / $total_parts);
        $sand_volume = $dry_volume_with_wastage * ($sand / $total_parts);
        $aggregate_volume = $dry_volume_with_wastage * ($aggregate / $total_parts);
        
        // Convert to practical units
        $cement_bags = $cement_volume / 0.035; // 1 bag = 0.035 m³
        
        return [
            'mix_ratio' => $mix_ratio,
            'cement_bags' => ceil($cement_bags),
            'sand_volume' => round($sand_volume, 2),
            'aggregate_volume' => round($aggregate_volume, 2),
            'dry_volume' => round($dry_volume, 2),
            'dry_volume_with_wastage' => round($dry_volume_with_wastage, 2),
            'units' => [
                'cement' => 'bags',
                'sand' => 'm³',
                'aggregate' => 'm³'
            ]
        ];
    }
    
    public function getStandardMixRatios() {
        return [
            '1:1:2' => 'M25 - High Strength',
            '1:1.5:3' => 'M20 - RCC Works',
            '1:2:4' => 'M15 - Beams, Slabs',
            '1:3:6' => 'M10 - Mass Concrete',
            '1:4:8' => 'M7.5 - Foundation',
        ];
    }
}

// Initialize calculator
$calculator = new ConcreteMaterialsCalculator();
$results = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $volume = floatval($_POST['volume'] ?? 0);
    $mix_ratio = $_POST['mix_ratio'] ?? '1:1.5:3';
    $wastage = floatval($_POST['wastage'] ?? 5) / 100;
    
    $calculator = new ConcreteMaterialsCalculator($wastage);
    $results = $calculator->calculateMaterials($volume, $mix_ratio);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concrete Materials Calculator - AEC Toolkit</title>
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

        .mix-ratio-info {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            text-align: left;
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
            <h1><i class="fas fa-weight-hanging"></i> Concrete Materials Calculator</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="volume">Concrete Volume (m³):</label>
                    <input type="number" name="volume" class="form-control" step="0.01" min="0.1" value="<?= $_POST['volume'] ?? 1 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mix_ratio">Concrete Mix Ratio (Cement:Sand:Aggregate):</label>
                    <select name="mix_ratio" class="form-control" required>
                        <?php
                        $standard_ratios = (new ConcreteMaterialsCalculator())->getStandardMixRatios();
                        foreach ($standard_ratios as $ratio => $description):
                        ?>
                        <option value="<?= $ratio ?>" <?= ($_POST['mix_ratio'] ?? '1:1.5:3') == $ratio ? 'selected' : '' ?>>
                            <?= $ratio ?> - <?= $description ?>
                        </option>
                        <?php endforeach; ?>
                        <option value="custom">Custom Ratio</option>
                    </select>
                </div>
                
                <div id="custom_ratio" style="display: none;">
                    <div class="form-group">
                        <label>Custom Mix Ratio (Cement:Sand:Aggregate):</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                            <input type="number" name="custom_cement" class="form-control" placeholder="Cement" min="1" value="1">
                            <input type="number" name="custom_sand" class="form-control" placeholder="Sand" min="1" value="1.5">
                            <input type="number" name="custom_aggregate" class="form-control" placeholder="Aggregate" min="1" value="3">
                        </div>
                    </div>
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
                
                <div class="mix-ratio-info">
                    <strong>Mix Ratio:</strong> <?= $results['mix_ratio'] ?> (Cement:Sand:Aggregate)<br>
                    <strong>Dry Volume:</strong> <?= $results['dry_volume'] ?> m³<br>
                    <strong>With Wastage:</strong> <?= $results['dry_volume_with_wastage'] ?> m³
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
                    <div class="material-card">
                        <div class="material-value"><?= $results['aggregate_volume'] ?></div>
                        <div class="material-label">Aggregate (m³)</div>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <h4><i class="fas fa-info-circle"></i> Calculation Notes</h4>
                    <small>
                        • Dry volume factor of 1.54 accounts for concrete shrinkage<br>
                        • 1 cement bag = 0.035 m³ or 50kg<br>
                        • Wastage of <?= ($_POST['wastage'] ?? 5) ?>% included in calculations
                    </small>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Toolkit</a>
    </div>

    <script>
        // Show/hide custom ratio inputs
        document.querySelector('select[name="mix_ratio"]').addEventListener('change', function() {
            const customDiv = document.getElementById('custom_ratio');
            customDiv.style.display = this.value === 'custom' ? 'block' : 'none';
        });
        
        // Set custom ratio when custom is selected
        document.querySelector('form').addEventListener('submit', function(e) {
            const mixRatioSelect = document.querySelector('select[name="mix_ratio"]');
            if (mixRatioSelect.value === 'custom') {
                const cement = document.querySelector('input[name="custom_cement"]').value;
                const sand = document.querySelector('input[name="custom_sand"]').value;
                const aggregate = document.querySelector('input[name="custom_aggregate"]').value;
                
                // Create hidden input with custom ratio
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'mix_ratio';
                hiddenInput.value = cement + ':' + sand + ':' + aggregate;
                this.appendChild(hiddenInput);
                
                // Remove the original select
                mixRatioSelect.disabled = true;
            }
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const mixRatioSelect = document.querySelector('select[name="mix_ratio"]');
            const customDiv = document.getElementById('custom_ratio');
            customDiv.style.display = mixRatioSelect.value === 'custom' ? 'block' : 'none';
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>