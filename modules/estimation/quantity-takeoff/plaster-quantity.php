<?php
// modules/estimation/quantity-takeoff/plaster-quantity.php
/**
 * Plaster Quantity Calculator
 * Calculates plaster area and material requirements
 * Part of AEC Estimation Suite
 */

class PlasterCalculator {
    private $wastage_factor;
    
    public function __construct($wastage_factor = 0.05) {
        $this->wastage_factor = $wastage_factor;
    }
    
    public function calculatePlasterArea($length, $height, $openings = []) {
        $total_openings_area = 0;
        foreach ($openings as $opening) {
            $total_openings_area += $opening['width'] * $opening['height'];
        }
        
        $gross_area = $length * $height;
        $net_area = $gross_area - $total_openings_area;
        $area_with_wastage = $net_area * (1 + $this->wastage_factor);
        
        return [
            'gross_area' => round($gross_area, 2),
            'net_area' => round($net_area, 2),
            'area_with_wastage' => round($area_with_wastage, 2),
            'openings_area' => round($total_openings_area, 2)
        ];
    }
    
    public function calculateMaterials($area, $thickness, $mix_ratio = '1:4') {
        // Calculate mortar volume
        $mortar_volume = $area * ($thickness / 1000); // Convert mm to meters
        
        // Calculate materials based on mix ratio
        list($cement, $sand) = explode(':', $mix_ratio);
        $total_parts = $cement + $sand;
        
        $cement_volume = $mortar_volume * ($cement / $total_parts);
        $sand_volume = $mortar_volume * ($sand / $total_parts);
        
        // Cement in bags (1 bag = 0.035 m³)
        $cement_bags = $cement_volume / 0.035;
        
        return [
            'mortar_volume' => round($mortar_volume, 3),
            'cement_bags' => ceil($cement_bags),
            'sand_volume' => round($sand_volume, 3),
            'mix_ratio' => $mix_ratio
        ];
    }
}

// Initialize calculator
$calculator = new PlasterCalculator();
$area_results = [];
$material_results = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $length = floatval($_POST['length'] ?? 0);
    $height = floatval($_POST['height'] ?? 0);
    $thickness = floatval($_POST['thickness'] ?? 12);
    $wastage = floatval($_POST['wastage'] ?? 5) / 100;
    $mix_ratio = $_POST['mix_ratio'] ?? '1:4';
    
    // Process openings
    $openings = [];
    $opening_count = intval($_POST['opening_count'] ?? 0);
    for ($i = 1; $i <= $opening_count; $i++) {
        if (!empty($_POST["opening_width_$i"]) && !empty($_POST["opening_height_$i"])) {
            $openings[] = [
                'width' => floatval($_POST["opening_width_$i"]),
                'height' => floatval($_POST["opening_height_$i"])
            ];
        }
    }
    
    $calculator = new PlasterCalculator($wastage);
    $area_results = $calculator->calculatePlasterArea($length, $height, $openings);
    
    if (!empty($area_results)) {
        $material_results = $calculator->calculateMaterials($area_results['area_with_wastage'], $thickness, $mix_ratio);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plaster Quantity Calculator - AEC Toolkit</title>
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

        .opening-item {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .opening-item input {
            flex: 1;
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

        .btn-secondary {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            color: var(--light);
            cursor: pointer;
            margin: 0.5rem;
        }

        .result-area {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
            display: <?php echo !empty($area_results) ? 'block' : 'none'; ?>;
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
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-trowel"></i> Plaster Quantity Calculator</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="length">Wall Length (meters):</label>
                    <input type="number" name="length" class="form-control" step="0.01" min="0.1" value="<?= $_POST['length'] ?? 10 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="height">Wall Height (meters):</label>
                    <input type="number" name="height" class="form-control" step="0.01" min="0.1" value="<?= $_POST['height'] ?? 3 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="thickness">Plaster Thickness (mm):</label>
                    <input type="number" name="thickness" class="form-control" value="<?= $_POST['thickness'] ?? 12 ?>" min="6" max="25" required>
                </div>
                
                <div class="form-group">
                    <label for="wastage">Wastage Percentage (%):</label>
                    <input type="number" name="wastage" class="form-control" value="<?= $_POST['wastage'] ?? 5 ?>" min="0" max="20" step="0.5" required>
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
                    <label>Openings (Doors, Windows):</label>
                    <div id="openings-container">
                        <?php
                        $opening_count = $_POST['opening_count'] ?? 1;
                        for ($i = 1; $i <= $opening_count; $i++):
                        ?>
                        <div class="opening-item">
                            <input type="number" name="opening_width_<?= $i ?>" class="form-control" placeholder="Width (m)" step="0.01" value="<?= $_POST["opening_width_$i"] ?? '' ?>">
                            <input type="number" name="opening_height_<?= $i ?>" class="form-control" placeholder="Height (m)" step="0.01" value="<?= $_POST["opening_height_$i"] ?? '' ?>">
                        </div>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="opening_count" id="opening_count" value="<?= $opening_count ?>">
                    <button type="button" class="btn-secondary" onclick="addOpening()">+ Add Opening</button>
                    <button type="button" class="btn-secondary" onclick="removeOpening()">- Remove Opening</button>
                </div>

                <button type="submit" class="btn-calculate">Calculate Plaster Quantity</button>
            </form>

            <?php if (!empty($area_results)): ?>
            <div class="result-area">
                <h3><i class="fas fa-calculator"></i> Calculation Results</h3>
                
                <div class="result-item">
                    <span>Gross Wall Area:</span>
                    <span class="result-value"><?= $area_results['gross_area'] ?> m²</span>
                </div>
                
                <?php if ($area_results['openings_area'] > 0): ?>
                <div class="result-item">
                    <span>Openings Area:</span>
                    <span class="result-value"><?= $area_results['openings_area'] ?> m²</span>
                </div>
                <?php endif; ?>
                
                <div class="result-item">
                    <span>Net Plaster Area:</span>
                    <span class="result-value"><?= $area_results['net_area'] ?> m²</span>
                </div>
                
                <div class="result-item">
                    <span>Area with Wastage (<?= ($_POST['wastage'] ?? 5) ?>%):</span>
                    <span class="result-value"><?= $area_results['area_with_wastage'] ?> m²</span>
                </div>

                <?php if (!empty($material_results)): ?>
                <div style="margin-top: 2rem;">
                    <h4><i class="fas fa-tools"></i> Material Requirements (<?= $material_results['mix_ratio'] ?> mix)</h4>
                    <div class="materials-grid">
                        <div class="material-card">
                            <div class="material-value"><?= $material_results['cement_bags'] ?></div>
                            <div class="material-label">Cement Bags</div>
                        </div>
                        <div class="material-card">
                            <div class="material-value"><?= $material_results['sand_volume'] ?></div>
                            <div class="material-label">Sand (m³)</div>
                        </div>
                        <div class="material-card">
                            <div class="material-value"><?= $material_results['mortar_volume'] ?></div>
                            <div class="material-label">Mortar Volume (m³)</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Toolkit</a>
    </div>

    <script>
        function addOpening() {
            const container = document.getElementById('openings-container');
            const count = document.getElementById('opening_count');
            const newIndex = parseInt(count.value) + 1;
            
            const newOpening = document.createElement('div');
            newOpening.className = 'opening-item';
            newOpening.innerHTML = `
                <input type="number" name="opening_width_${newIndex}" class="form-control" placeholder="Width (m)" step="0.01">
                <input type="number" name="opening_height_${newIndex}" class="form-control" placeholder="Height (m)" step="0.01">
            `;
            
            container.appendChild(newOpening);
            count.value = newIndex;
        }
        
        function removeOpening() {
            const container = document.getElementById('openings-container');
            const count = document.getElementById('opening_count');
            
            if (parseInt(count.value) > 1) {
                container.removeChild(container.lastChild);
                count.value = parseInt(count.value) - 1;
            }
        }
    </script>
</body>
</html>