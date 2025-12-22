<?php
// modules/estimation/quantity-takeoff/paint-quantity.php
/**
 * Paint Quantity Calculator
 * Calculates paint coverage and material requirements
 * Part of AEC Estimation Suite
 */

class PaintCalculator {
    private $wastage_factor;
    
    public function __construct($wastage_factor = 0.1) {
        $this->wastage_factor = $wastage_factor;
    }
    
    public function calculateSurfaceArea($walls = []) {
        $total_area = 0;
        $walls_detail = [];
        
        foreach ($walls as $wall) {
            $length = $wall['length'];
            $height = $wall['height'];
            $openings_area = 0;
            
            // Calculate openings area
            foreach ($wall['openings'] as $opening) {
                $openings_area += $opening['width'] * $opening['height'];
            }
            
            $wall_area = ($length * $height) - $openings_area;
            $total_area += $wall_area;
            
            $walls_detail[] = [
                'length' => $length,
                'height' => $height,
                'area' => $wall_area,
                'openings_area' => $openings_area
            ];
        }
        
        $area_with_wastage = $total_area * (1 + $this->wastage_factor);
        
        return [
            'total_area' => round($total_area, 2),
            'area_with_wastage' => round($area_with_wastage, 2),
            'walls_detail' => $walls_detail
        ];
    }
    
    public function calculatePaintRequirements($area, $coverage_per_liter, $coats = 2) {
        $total_coverage_needed = $area * $coats;
        $paint_liters = $total_coverage_needed / $coverage_per_liter;
        
        return [
            'paint_liters' => ceil($paint_liters),
            'coverage_needed' => round($total_coverage_needed, 2),
            'coats' => $coats
        ];
    }
    
    public function calculatePrimerRequirements($area, $coverage_per_liter = 10) {
        $primer_liters = $area / $coverage_per_liter;
        return ceil($primer_liters);
    }
}

// Initialize calculator
$calculator = new PaintCalculator();
$area_results = [];
$paint_results = [];
$primer_required = 0;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coverage = floatval($_POST['coverage'] ?? 10);
    $coats = intval($_POST['coats'] ?? 2);
    $wastage = floatval($_POST['wastage'] ?? 10) / 100;
    $include_primer = isset($_POST['include_primer']);
    $primer_coverage = floatval($_POST['primer_coverage'] ?? 10);
    
    // Process walls
    $walls = [];
    $wall_count = intval($_POST['wall_count'] ?? 1);
    
    for ($i = 1; $i <= $wall_count; $i++) {
        if (!empty($_POST["wall_length_$i"]) && !empty($_POST["wall_height_$i"])) {
            $wall = [
                'length' => floatval($_POST["wall_length_$i"]),
                'height' => floatval($_POST["wall_height_$i"]),
                'openings' => []
            ];
            
            // Process openings for this wall
            $opening_count = intval($_POST["opening_count_$i"] ?? 0);
            for ($j = 1; $j <= $opening_count; $j++) {
                if (!empty($_POST["opening_width_{$i}_{$j}"]) && !empty($_POST["opening_height_{$i}_{$j}"])) {
                    $wall['openings'][] = [
                        'width' => floatval($_POST["opening_width_{$i}_{$j}"]),
                        'height' => floatval($_POST["opening_height_{$i}_{$j}"])
                    ];
                }
            }
            
            $walls[] = $wall;
        }
    }
    
    $calculator = new PaintCalculator($wastage);
    $area_results = $calculator->calculateSurfaceArea($walls);
    
    if (!empty($area_results)) {
        $paint_results = $calculator->calculatePaintRequirements($area_results['area_with_wastage'], $coverage, $coats);
        
        if ($include_primer) {
            $primer_required = $calculator->calculatePrimerRequirements($area_results['area_with_wastage'], $primer_coverage);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paint Quantity Calculator - AEC Toolkit</title>
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
            max-width: 900px;
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

        .wall-section {
            background: rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .wall-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .opening-item {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            display: flex;
            gap: 1rem;
            align-items: center;
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

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-paint-roller"></i> Paint Quantity Calculator</h1>
            <form method="POST" action="">
                <input type="hidden" name="wall_count" id="wall_count" value="<?= $_POST['wall_count'] ?? 1 ?>">
                
                <div id="walls-container">
                    <?php
                    $wall_count = $_POST['wall_count'] ?? 1;
                    for ($i = 1; $i <= $wall_count; $i++):
                    ?>
                    <div class="wall-section" id="wall_<?= $i ?>">
                        <div class="wall-header">
                            <h3>Wall <?= $i ?></h3>
                        </div>
                        <div class="form-group">
                            <label for="wall_length_<?= $i ?>">Wall Length (meters):</label>
                            <input type="number" name="wall_length_<?= $i ?>" class="form-control" step="0.01" min="0.1" value="<?= $_POST["wall_length_$i"] ?? 4 ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="wall_height_<?= $i ?>">Wall Height (meters):</label>
                            <input type="number" name="wall_height_<?= $i ?>" class="form-control" step="0.01" min="0.1" value="<?= $_POST["wall_height_$i"] ?? 3 ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Openings (Doors, Windows) for Wall <?= $i ?>:</label>
                            <input type="hidden" name="opening_count_<?= $i ?>" id="opening_count_<?= $i ?>" value="<?= $_POST["opening_count_$i"] ?? 0 ?>">
                            <div id="openings-container_<?= $i ?>">
                                <?php
                                $opening_count = $_POST["opening_count_$i"] ?? 0;
                                for ($j = 1; $j <= $opening_count; $j++):
                                ?>
                                <div class="opening-item">
                                    <input type="number" name="opening_width_<?= $i ?>_<?= $j ?>" class="form-control" placeholder="Width (m)" step="0.01" value="<?= $_POST["opening_width_{$i}_{$j}"] ?? '' ?>">
                                    <input type="number" name="opening_height_<?= $i ?>_<?= $j ?>" class="form-control" placeholder="Height (m)" step="0.01" value="<?= $_POST["opening_height_{$i}_{$j}"] ?? '' ?>">
                                </div>
                                <?php endfor; ?>
                            </div>
                            <button type="button" class="btn-secondary" onclick="addOpening(<?= $i ?>)">+ Add Opening</button>
                            <button type="button" class="btn-secondary" onclick="removeOpening(<?= $i ?>)">- Remove Opening</button>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                
                <div style="margin: 2rem 0;">
                    <button type="button" class="btn-secondary" onclick="addWall()">+ Add Wall</button>
                    <button type="button" class="btn-secondary" onclick="removeWall()">- Remove Wall</button>
                </div>

                <div class="form-group">
                    <label for="coverage">Paint Coverage (m² per liter):</label>
                    <input type="number" name="coverage" class="form-control" value="<?= $_POST['coverage'] ?? 10 ?>" min="5" max="20" step="0.1" required>
                </div>
                
                <div class="form-group">
                    <label for="coats">Number of Coats:</label>
                    <select name="coats" class="form-control">
                        <option value="1" <?= ($_POST['coats'] ?? 2) == 1 ? 'selected' : '' ?>>1 Coat</option>
                        <option value="2" <?= ($_POST['coats'] ?? 2) == 2 ? 'selected' : '' ?>>2 Coats</option>
                        <option value="3" <?= ($_POST['coats'] ?? 2) == 3 ? 'selected' : '' ?>>3 Coats</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="wastage">Wastage Percentage (%):</label>
                    <input type="number" name="wastage" class="form-control" value="<?= $_POST['wastage'] ?? 10 ?>" min="0" max="20" step="0.5" required>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" name="include_primer" id="include_primer" <?= isset($_POST['include_primer']) ? 'checked' : '' ?>>
                    <label for="include_primer">Include Primer Calculation</label>
                </div>
                
                <div class="form-group" id="primer_coverage_group" style="<?= isset($_POST['include_primer']) ? '' : 'display: none;' ?>">
                    <label for="primer_coverage">Primer Coverage (m² per liter):</label>
                    <input type="number" name="primer_coverage" class="form-control" value="<?= $_POST['primer_coverage'] ?? 10 ?>" min="5" max="20" step="0.1">
                </div>

                <button type="submit" class="btn-calculate">Calculate Paint Quantity</button>
            </form>

            <?php if (!empty($area_results)): ?>
            <div class="result-area">
                <h3><i class="fas fa-calculator"></i> Calculation Results</h3>
                
                <div class="result-item">
                    <span>Total Surface Area:</span>
                    <span class="result-value"><?= $area_results['total_area'] ?> m²</span>
                </div>
                
                <div class="result-item">
                    <span>Area with Wastage (<?= ($_POST['wastage'] ?? 10) ?>%):</span>
                    <span class="result-value"><?= $area_results['area_with_wastage'] ?> m²</span>
                </div>

                <?php if (!empty($paint_results)): ?>
                <div style="margin-top: 2rem;">
                    <h4><i class="fas fa-paint-brush"></i> Paint Requirements (<?= $paint_results['coats'] ?> coats)</h4>
                    <div class="materials-grid">
                        <div class="material-card">
                            <div class="material-value"><?= $paint_results['paint_liters'] ?></div>
                            <div class="material-label">Paint (liters)</div>
                        </div>
                        <?php if ($primer_required > 0): ?>
                        <div class="material-card">
                            <div class="material-value"><?= $primer_required ?></div>
                            <div class="material-label">Primer (liters)</div>
                        </div>
                        <?php endif; ?>
                        <div class="material-card">
                            <div class="material-value"><?= $paint_results['coverage_needed'] ?></div>
                            <div class="material-label">Total Coverage (m²)</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (count($area_results['walls_detail']) > 0): ?>
                <div style="margin-top: 2rem;">
                    <h4>Wall-wise Breakdown</h4>
                    <?php foreach ($area_results['walls_detail'] as $index => $wall): ?>
                    <div class="result-item">
                        <span>Wall <?= $index + 1 ?> (<?= $wall['length'] ?>m × <?= $wall['height'] ?>m):</span>
                        <span class="result-value"><?= $wall['area'] ?> m²</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Toolkit</a>
    </div>

    <script>
        let wallCount = <?= $wall_count ?>;
        
        function addWall() {
            wallCount++;
            document.getElementById('wall_count').value = wallCount;
            
            const container = document.getElementById('walls-container');
            const newWall = document.createElement('div');
            newWall.className = 'wall-section';
            newWall.id = 'wall_' + wallCount;
            newWall.innerHTML = `
                <div class="wall-header">
                    <h3>Wall ${wallCount}</h3>
                </div>
                <div class="form-group">
                    <label for="wall_length_${wallCount}">Wall Length (meters):</label>
                    <input type="number" name="wall_length_${wallCount}" class="form-control" step="0.01" min="0.1" value="4" required>
                </div>
                <div class="form-group">
                    <label for="wall_height_${wallCount}">Wall Height (meters):</label>
                    <input type="number" name="wall_height_${wallCount}" class="form-control" step="0.01" min="0.1" value="3" required>
                </div>
                
                <div class="form-group">
                    <label>Openings (Doors, Windows) for Wall ${wallCount}:</label>
                    <input type="hidden" name="opening_count_${wallCount}" id="opening_count_${wallCount}" value="0">
                    <div id="openings-container_${wallCount}"></div>
                    <button type="button" class="btn-secondary" onclick="addOpening(${wallCount})">+ Add Opening</button>
                    <button type="button" class="btn-secondary" onclick="removeOpening(${wallCount})">- Remove Opening</button>
                </div>
            `;
            
            container.appendChild(newWall);
        }
        
        function removeWall() {
            if (wallCount > 1) {
                const container = document.getElementById('walls-container');
                container.removeChild(container.lastChild);
                wallCount--;
                document.getElementById('wall_count').value = wallCount;
            }
        }
        
        function addOpening(wallIndex) {
            const container = document.getElementById('openings-container_' + wallIndex);
            const countInput = document.getElementById('opening_count_' + wallIndex);
            const newIndex = parseInt(countInput.value) + 1;
            
            const newOpening = document.createElement('div');
            newOpening.className = 'opening-item';
            newOpening.innerHTML = `
                <input type="number" name="opening_width_${wallIndex}_${newIndex}" class="form-control" placeholder="Width (m)" step="0.01">
                <input type="number" name="opening_height_${wallIndex}_${newIndex}" class="form-control" placeholder="Height (m)" step="0.01">
            `;
            
            container.appendChild(newOpening);
            countInput.value = newIndex;
        }
        
        function removeOpening(wallIndex) {
            const container = document.getElementById('openings-container_' + wallIndex);
            const countInput = document.getElementById('opening_count_' + wallIndex);
            
            if (parseInt(countInput.value) > 0) {
                container.removeChild(container.lastChild);
                countInput.value = parseInt(countInput.value) - 1;
            }
        }
        
        // Toggle primer coverage field
        document.getElementById('include_primer').addEventListener('change', function() {
            document.getElementById('primer_coverage_group').style.display = this.checked ? 'block' : 'none';
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
