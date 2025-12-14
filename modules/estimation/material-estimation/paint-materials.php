<?php
// modules/estimation/material-estimation/paint-materials.php
/**
 * Paint Materials Calculator
 * Calculates primer, putty, and paint layers requirement
 * Part of AEC Estimation Suite
 */

class PaintMaterialsCalculator {
    private $wastage_factor;
    
    public function __construct($wastage_factor = 0.1) {
        $this->wastage_factor = $wastage_factor;
    }
    
    public function calculatePaintRequirements($area, $coverage_per_liter, $coats = 2) {
        $total_coverage_needed = $area * $coats;
        $paint_liters = $total_coverage_needed / $coverage_per_liter;
        $paint_with_wastage = $paint_liters * (1 + $this->wastage_factor);
        
        return [
            'paint_liters' => ceil($paint_with_wastage),
            'coverage_needed' => round($total_coverage_needed, 2),
            'coats' => $coats,
            'coverage_rate' => $coverage_per_liter
        ];
    }
    
    public function calculatePrimerRequirements($area, $coverage_per_liter = 10, $coats = 1) {
        $primer_liters = ($area * $coats) / $coverage_per_liter;
        $primer_with_wastage = $primer_liters * (1 + $this->wastage_factor);
        
        return [
            'primer_liters' => ceil($primer_with_wastage),
            'coverage_needed' => round($area * $coats, 2),
            'coats' => $coats,
            'coverage_rate' => $coverage_per_liter
        ];
    }
    
    public function calculatePuttyRequirements($area, $thickness = 1.5, $coverage_per_kg = 0.8) {
        // Putty calculation (thickness in mm)
        $putty_volume = $area * ($thickness / 1000); // Convert mm to meters
        $putty_kg = $putty_volume * 1600; // Approximate density 1600 kg/m³
        $putty_with_wastage = $putty_kg * (1 + $this->wastage_factor);
        
        // Alternative calculation based on coverage
        $putty_by_coverage = $area / $coverage_per_kg;
        $putty_by_coverage_with_wastage = $putty_by_coverage * (1 + $this->wastage_factor);
        
        return [
            'putty_kg_volume' => ceil($putty_with_wastage),
            'putty_kg_coverage' => ceil($putty_by_coverage_with_wastage),
            'thickness' => $thickness,
            'coverage_rate' => $coverage_per_kg,
            'method' => 'coverage' // Use coverage method as it's more accurate
        ];
    }
    
    public function getStandardCoverageRates() {
        return [
            'emulsion' => ['min' => 10, 'max' => 12, 'recommended' => 11],
            'enamel' => ['min' => 12, 'max' => 14, 'recommended' => 13],
            'primer' => ['min' => 10, 'max' => 12, 'recommended' => 11],
            'texture' => ['min' => 4, 'max' => 6, 'recommended' => 5]
        ];
    }
}

// Initialize calculator
$calculator = new PaintMaterialsCalculator();
$paint_results = [];
$primer_results = [];
$putty_results = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $area = floatval($_POST['area'] ?? 0);
    $paint_type = $_POST['paint_type'] ?? 'emulsion';
    $paint_coats = intval($_POST['paint_coats'] ?? 2);
    $primer_coats = intval($_POST['primer_coats'] ?? 1);
    $include_primer = isset($_POST['include_primer']);
    $include_putty = isset($_POST['include_putty']);
    $putty_thickness = floatval($_POST['putty_thickness'] ?? 1.5);
    $wastage = floatval($_POST['wastage'] ?? 10) / 100;
    
    // Coverage rates
    $coverage_rates = (new PaintMaterialsCalculator())->getStandardCoverageRates();
    $paint_coverage = floatval($_POST['paint_coverage'] ?? $coverage_rates[$paint_type]['recommended']);
    $primer_coverage = floatval($_POST['primer_coverage'] ?? $coverage_rates['primer']['recommended']);
    $putty_coverage = floatval($_POST['putty_coverage'] ?? 0.8);
    
    $calculator = new PaintMaterialsCalculator($wastage);
    
    $paint_results = $calculator->calculatePaintRequirements($area, $paint_coverage, $paint_coats);
    
    if ($include_primer) {
        $primer_results = $calculator->calculatePrimerRequirements($area, $primer_coverage, $primer_coats);
    }
    
    if ($include_putty) {
        $putty_results = $calculator->calculatePuttyRequirements($area, $putty_thickness, $putty_coverage);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paint Materials Calculator - AEC Toolkit</title>
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

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .optional-fields {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1rem 0;
            display: none;
        }

        .optional-fields.active {
            display: block;
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
            display: <?php echo !empty($paint_results) ? 'block' : 'none'; ?>;
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
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-paint-brush"></i> Paint Materials Calculator</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="area">Surface Area (m²):</label>
                    <input type="number" name="area" class="form-control" step="0.01" min="0.1" value="<?= $_POST['area'] ?? 100 ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="paint_type">Paint Type:</label>
                    <select name="paint_type" id="paint_type" class="form-control" onchange="updateCoverageRates()">
                        <option value="emulsion" <?= ($_POST['paint_type'] ?? 'emulsion') == 'emulsion' ? 'selected' : '' ?>>Emulsion Paint</option>
                        <option value="enamel" <?= ($_POST['paint_type'] ?? 'emulsion') == 'enamel' ? 'selected' : '' ?>>Enamel Paint</option>
                        <option value="texture" <?= ($_POST['paint_type'] ?? 'emulsion') == 'texture' ? 'selected' : '' ?>>Texture Paint</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="paint_coverage">Paint Coverage (m² per liter):</label>
                    <input type="number" name="paint_coverage" id="paint_coverage" class="form-control" value="<?= $_POST['paint_coverage'] ?? 11 ?>" min="4" max="20" step="0.1" required>
                </div>
                
                <div class="form-group">
                    <label for="paint_coats">Number of Paint Coats:</label>
                    <select name="paint_coats" class="form-control">
                        <option value="1" <?= ($_POST['paint_coats'] ?? 2) == 1 ? 'selected' : '' ?>>1 Coat</option>
                        <option value="2" <?= ($_POST['paint_coats'] ?? 2) == 2 ? 'selected' : '' ?>>2 Coats</option>
                        <option value="3" <?= ($_POST['paint_coats'] ?? 2) == 3 ? 'selected' : '' ?>>3 Coats</option>
                    </select>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" name="include_primer" id="include_primer" <?= isset($_POST['include_primer']) ? 'checked' : '' ?> onchange="toggleOptionalFields()">
                    <label for="include_primer">Include Primer Calculation</label>
                </div>
                
                <div id="primer_fields" class="optional-fields">
                    <div class="form-group">
                        <label for="primer_coverage">Primer Coverage (m² per liter):</label>
                        <input type="number" name="primer_coverage" class="form-control" value="<?= $_POST['primer_coverage'] ?? 11 ?>" min="8" max="15" step="0.1">
                    </div>
                    
                    <div class="form-group">
                        <label for="primer_coats">Number of Primer Coats:</label>
                        <select name="primer_coats" class="form-control">
                            <option value="1" <?= ($_POST['primer_coats'] ?? 1) == 1 ? 'selected' : '' ?>>1 Coat</option>
                            <option value="2" <?= ($_POST['primer_coats'] ?? 1) == 2 ? 'selected' : '' ?>>2 Coats</option>
                        </select>
                    </div>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" name="include_putty" id="include_putty" <?= isset($_POST['include_putty']) ? 'checked' : '' ?> onchange="toggleOptionalFields()">
                    <label for="include_putty">Include Putty Calculation</label>
                </div>
                
                <div id="putty_fields" class="optional-fields">
                    <div class="form-group">
                        <label for="putty_thickness">Putty Thickness (mm):</label>
                        <input type="number" name="putty_thickness" class="form-control" value="<?= $_POST['putty_thickness'] ?? 1.5 ?>" min="0.5" max="3" step="0.1">
                    </div>
                    
                    <div class="form-group">
                        <label for="putty_coverage">Putty Coverage (m² per kg):</label>
                        <input type="number" name="putty_coverage" class="form-control" value="<?= $_POST['putty_coverage'] ?? 0.8 ?>" min="0.5" max="1.5" step="0.1">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="wastage">Wastage Percentage (%):</label>
                    <input type="number" name="wastage" class="form-control" value="<?= $_POST['wastage'] ?? 10 ?>" min="0" max="20" step="0.5" required>
                </div>

                <div class="coverage-info">
                    <h4><i class="fas fa-info-circle"></i> Standard Coverage Rates</h4>
                    <small>
                        • Emulsion Paint: 10-12 m²/liter<br>
                        • Enamel Paint: 12-14 m²/liter<br>
                        • Texture Paint: 4-6 m²/liter<br>
                        • Primer: 10-12 m²/liter<br>
                        • Putty: 0.8-1.2 m²/kg
                    </small>
                </div>

                <button type="submit" class="btn-calculate">Calculate Materials</button>
            </form>

            <?php if (!empty($paint_results)): ?>
            <div class="result-area">
                <h3><i class="fas fa-calculator"></i> Material Requirements</h3>
                
                <div class="result-item">
                    <span>Surface Area:</span>
                    <span class="result-value"><?= $paint_results['coverage_needed'] / $paint_results['coats'] ?> m²</span>
                </div>
                
                <div class="result-item">
                    <span>Total Coverage Needed:</span>
                    <span class="result-value"><?= $paint_results['coverage_needed'] ?> m²</span>
                </div>

                <div class="materials-grid">
                    <div class="material-card">
                        <div class="material-value"><?= $paint_results['paint_liters'] ?></div>
                        <div class="material-label">Paint (liters)</div>
                        <small><?= $paint_results['coats'] ?> coats</small>
                    </div>
                    
                    <?php if (!empty($primer_results)): ?>
                    <div class="material-card">
                        <div class="material-value"><?= $primer_results['primer_liters'] ?></div>
                        <div class="material-label">Primer (liters)</div>
                        <small><?= $primer_results['coats'] ?> coat(s)</small>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($putty_results)): ?>
                    <div class="material-card">
                        <div class="material-value"><?= $putty_results['putty_kg_coverage'] ?></div>
                        <div class="material-label">Putty (kg)</div>
                        <small><?= $putty_results['thickness'] ?>mm thick</small>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <h4><i class="fas fa-info-circle"></i> Calculation Details</h4>
                    <small>
                        • Paint Type: <?= ucfirst($_POST['paint_type'] ?? 'emulsion') ?><br>
                        • Paint Coverage: <?= $paint_results['coverage_rate'] ?> m²/liter<br>
                        • Wastage: <?= ($_POST['wastage'] ?? 10) ?>% included<br>
                        <?php if (!empty($primer_results)): ?>
                        • Primer Coverage: <?= $primer_results['coverage_rate'] ?> m²/liter<br>
                        <?php endif; ?>
                        <?php if (!empty($putty_results)): ?>
                        • Putty Coverage: <?= $putty_results['coverage_rate'] ?> m²/kg<br>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Toolkit</a>
    </div>

    <script>
        const coverageRates = {
            'emulsion': { min: 10, max: 12, recommended: 11 },
            'enamel': { min: 12, max: 14, recommended: 13 },
            'texture': { min: 4, max: 6, recommended: 5 }
        };

        function updateCoverageRates() {
            const paintType = document.getElementById('paint_type').value;
            const coverageInput = document.getElementById('paint_coverage');
            const recommendation = coverageRates[paintType];
            
            coverageInput.value = recommendation.recommended;
        }

        function toggleOptionalFields() {
            const primerCheckbox = document.getElementById('include_primer');
            const puttyCheckbox = document.getElementById('include_putty');
            const primerFields = document.getElementById('primer_fields');
            const puttyFields = document.getElementById('putty_fields');
            
            primerFields.classList.toggle('active', primerCheckbox.checked);
            puttyFields.classList.toggle('active', puttyCheckbox.checked);
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCoverageRates();
            toggleOptionalFields();
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>