<?php
// modules/estimation/quantity-takeoff/rebar-quantity.php
/**
 * Rebar Quantity Calculator
 * Calculates steel reinforcement quantity and weight
 * Part of AEC Estimation Suite
 */

class RebarCalculator {
    private $lap_length_factor;
    private $wastage_factor;
    
    public function __construct($lap_length_factor = 50, $wastage_factor = 0.05) {
        $this->lap_length_factor = $lap_length_factor; // In bar diameters
        $this->wastage_factor = $wastage_factor;
    }
    
    public function calculateRebarWeight($diameter, $length, $quantity = 1) {
        // Weight in kg/m = (diameter²) / 162
        $weight_per_meter = pow($diameter, 2) / 162;
        $total_weight = $weight_per_meter * $length * $quantity;
        $total_weight_with_wastage = $total_weight * (1 + $this->wastage_factor);
        
        return [
            'weight_per_meter' => round($weight_per_meter, 3),
            'total_weight' => round($total_weight, 2),
            'total_weight_with_wastage' => round($total_weight_with_wastage, 2),
            'diameter' => $diameter,
            'length' => $length,
            'quantity' => $quantity
        ];
    }
    
    public function calculateLapLength($diameter) {
        return $diameter * $this->lap_length_factor;
    }
    
    public function calculateBeamReinforcement($length, $width, $depth, $main_bars, $distribution_bars, $stirrups) {
        $results = [];
        
        // Main bars (top and bottom)
        if ($main_bars['diameter'] > 0 && $main_bars['quantity'] > 0) {
            $main_bar_length = $length + $this->calculateLapLength($main_bars['diameter']);
            $main_bars_total = $main_bars['quantity'] * 2; // Top and bottom
            $results['main_bars'] = $this->calculateRebarWeight(
                $main_bars['diameter'], 
                $main_bar_length, 
                $main_bars_total
            );
        }
        
        // Distribution bars
        if ($distribution_bars['diameter'] > 0 && $distribution_bars['quantity'] > 0) {
            $distribution_bar_length = $width;
            $results['distribution_bars'] = $this->calculateRebarWeight(
                $distribution_bars['diameter'], 
                $distribution_bar_length, 
                $distribution_bars['quantity']
            );
        }
        
        // Stirrups
        if ($stirrups['diameter'] > 0 && $stirrups['spacing'] > 0) {
            $stirrup_length = 2 * ($width - 2 * 0.025) + 2 * ($depth - 2 * 0.025) + 0.15; // 25mm cover + hook length
            $number_of_stirrups = ceil($length / ($stirrups['spacing'] / 1000)); // Convert mm to meters
            $results['stirrups'] = $this->calculateRebarWeight(
                $stirrups['diameter'], 
                $stirrup_length, 
                $number_of_stirrups
            );
        }
        
        // Calculate totals
        $total_weight = 0;
        $total_weight_with_wastage = 0;
        foreach ($results as $bar_type) {
            $total_weight += $bar_type['total_weight'];
            $total_weight_with_wastage += $bar_type['total_weight_with_wastage'];
        }
        
        $results['summary'] = [
            'total_weight' => round($total_weight, 2),
            'total_weight_with_wastage' => round($total_weight_with_wastage, 2)
        ];
        
        return $results;
    }
    
    public function calculateSlabReinforcement($length, $width, $main_bars_x, $main_bars_y) {
        $results = [];
        
        // Main bars in X direction
        if ($main_bars_x['diameter'] > 0 && $main_bars_x['spacing'] > 0) {
            $number_of_bars_x = ceil($width / ($main_bars_x['spacing'] / 1000)) + 1;
            $results['main_bars_x'] = $this->calculateRebarWeight(
                $main_bars_x['diameter'], 
                $length, 
                $number_of_bars_x
            );
        }
        
        // Main bars in Y direction
        if ($main_bars_y['diameter'] > 0 && $main_bars_y['spacing'] > 0) {
            $number_of_bars_y = ceil($length / ($main_bars_y['spacing'] / 1000)) + 1;
            $results['main_bars_y'] = $this->calculateRebarWeight(
                $main_bars_y['diameter'], 
                $width, 
                $number_of_bars_y
            );
        }
        
        // Calculate totals
        $total_weight = 0;
        $total_weight_with_wastage = 0;
        foreach ($results as $bar_type) {
            $total_weight += $bar_type['total_weight'];
            $total_weight_with_wastage += $bar_type['total_weight_with_wastage'];
        }
        
        $results['summary'] = [
            'total_weight' => round($total_weight, 2),
            'total_weight_with_wastage' => round($total_weight_with_wastage, 2)
        ];
        
        return $results;
    }
}

// Initialize calculator
$calculator = new RebarCalculator();
$results = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $element_type = $_POST['element_type'] ?? '';
    $lap_length = floatval($_POST['lap_length'] ?? 50);
    $wastage = floatval($_POST['wastage'] ?? 5) / 100;
    
    $calculator = new RebarCalculator($lap_length, $wastage);
    
    switch ($element_type) {
        case 'single_bar':
            $diameter = floatval($_POST['bar_diameter'] ?? 0);
            $length = floatval($_POST['bar_length'] ?? 0);
            $quantity = intval($_POST['bar_quantity'] ?? 1);
            $results = [
                'single_bar' => $calculator->calculateRebarWeight($diameter, $length, $quantity)
            ];
            break;
            
        case 'beam':
            $length = floatval($_POST['beam_length'] ?? 0);
            $width = floatval($_POST['beam_width'] ?? 0);
            $depth = floatval($_POST['beam_depth'] ?? 0);
            
            $main_bars = [
                'diameter' => floatval($_POST['main_bar_diameter'] ?? 0),
                'quantity' => intval($_POST['main_bar_quantity'] ?? 0)
            ];
            
            $distribution_bars = [
                'diameter' => floatval($_POST['distribution_bar_diameter'] ?? 0),
                'quantity' => intval($_POST['distribution_bar_quantity'] ?? 0)
            ];
            
            $stirrups = [
                'diameter' => floatval($_POST['stirrup_diameter'] ?? 0),
                'spacing' => floatval($_POST['stirrup_spacing'] ?? 0)
            ];
            
            $results = $calculator->calculateBeamReinforcement($length, $width, $depth, $main_bars, $distribution_bars, $stirrups);
            break;
            
        case 'slab':
            $length = floatval($_POST['slab_length'] ?? 0);
            $width = floatval($_POST['slab_width'] ?? 0);
            
            $main_bars_x = [
                'diameter' => floatval($_POST['main_bar_diameter_x'] ?? 0),
                'spacing' => floatval($_POST['main_bar_spacing_x'] ?? 0)
            ];
            
            $main_bars_y = [
                'diameter' => floatval($_POST['main_bar_diameter_y'] ?? 0),
                'spacing' => floatval($_POST['main_bar_spacing_y'] ?? 0)
            ];
            
            $results = $calculator->calculateSlabReinforcement($length, $width, $main_bars_x, $main_bars_y);
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rebar Quantity Calculator - AEC Toolkit</title>
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

        .element-form {
            display: none;
            background: rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
        }

        .element-form.active {
            display: block;
        }

        .bar-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
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

        .bar-type-section {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .bar-type-section h4 {
            color: #feca57;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
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
            <h1><i class="fas fa-link"></i> Rebar Quantity Calculator</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="element_type">Select Element Type:</label>
                    <select name="element_type" id="element_type" class="form-control" onchange="showElementForm()" required>
                        <option value="">-- Choose Element Type --</option>
                        <option value="single_bar" <?= isset($_POST['element_type']) && $_POST['element_type'] == 'single_bar' ? 'selected' : '' ?>>Single Bar Calculation</option>
                        <option value="beam" <?= isset($_POST['element_type']) && $_POST['element_type'] == 'beam' ? 'selected' : '' ?>>Beam Reinforcement</option>
                        <option value="slab" <?= isset($_POST['element_type']) && $_POST['element_type'] == 'slab' ? 'selected' : '' ?>>Slab Reinforcement</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="lap_length">Lap Length (in bar diameters):</label>
                    <input type="number" name="lap_length" class="form-control" value="<?= $_POST['lap_length'] ?? 50 ?>" min="30" max="80" step="1" required>
                    <small style="opacity: 0.7;">Typically 40-50 times bar diameter for tension</small>
                </div>
                
                <div class="form-group">
                    <label for="wastage">Wastage Percentage (%):</label>
                    <input type="number" name="wastage" class="form-control" value="<?= $_POST['wastage'] ?? 5 ?>" min="0" max="20" step="0.5" required>
                </div>

                <!-- Single Bar Form -->
                <div id="single_bar_form" class="element-form">
                    <div class="bar-fields">
                        <div class="form-group">
                            <label for="bar_diameter">Bar Diameter (mm):</label>
                            <input type="number" name="bar_diameter" class="form-control" value="<?= $_POST['bar_diameter'] ?? 12 ?>" min="6" max="40" step="2" required>
                        </div>
                        <div class="form-group">
                            <label for="bar_length">Bar Length (meters):</label>
                            <input type="number" name="bar_length" class="form-control" step="0.01" min="0.1" value="<?= $_POST['bar_length'] ?? 12 ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="bar_quantity">Quantity:</label>
                            <input type="number" name="bar_quantity" class="form-control" min="1" value="<?= $_POST['bar_quantity'] ?? 1 ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Beam Form -->
                <div id="beam_form" class="element-form">
                    <div class="bar-fields">
                        <div class="form-group">
                            <label for="beam_length">Beam Length (m):</label>
                            <input type="number" name="beam_length" class="form-control" step="0.01" min="0.1" value="<?= $_POST['beam_length'] ?? 5 ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="beam_width">Beam Width (m):</label>
                            <input type="number" name="beam_width" class="form-control" step="0.01" min="0.1" value="<?= $_POST['beam_width'] ?? 0.3 ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="beam_depth">Beam Depth (m):</label>
                            <input type="number" name="beam_depth" class="form-control" step="0.01" min="0.1" value="<?= $_POST['beam_depth'] ?? 0.5 ?>" required>
                        </div>
                    </div>
                    
                    <div class="bar-type-section">
                        <h4>Main Reinforcement</h4>
                        <div class="bar-fields">
                            <div class="form-group">
                                <label for="main_bar_diameter">Main Bar Diameter (mm):</label>
                                <select name="main_bar_diameter" class="form-control">
                                    <option value="0">None</option>
                                    <?php for ($d = 8; $d <= 32; $d += 2): ?>
                                        <option value="<?= $d ?>" <?= ($_POST['main_bar_diameter'] ?? 16) == $d ? 'selected' : '' ?>><?= $d ?>mm</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="main_bar_quantity">Number of Bars:</label>
                                <input type="number" name="main_bar_quantity" class="form-control" min="0" value="<?= $_POST['main_bar_quantity'] ?? 4 ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bar-type-section">
                        <h4>Distribution Bars</h4>
                        <div class="bar-fields">
                            <div class="form-group">
                                <label for="distribution_bar_diameter">Distribution Bar Diameter (mm):</label>
                                <select name="distribution_bar_diameter" class="form-control">
                                    <option value="0">None</option>
                                    <?php for ($d = 8; $d <= 16; $d += 2): ?>
                                        <option value="<?= $d ?>" <?= ($_POST['distribution_bar_diameter'] ?? 10) == $d ? 'selected' : '' ?>><?= $d ?>mm</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="distribution_bar_quantity">Number of Bars:</label>
                                <input type="number" name="distribution_bar_quantity" class="form-control" min="0" value="<?= $_POST['distribution_bar_quantity'] ?? 2 ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bar-type-section">
                        <h4>Stirrups</h4>
                        <div class="bar-fields">
                            <div class="form-group">
                                <label for="stirrup_diameter">Stirrup Diameter (mm):</label>
                                <select name="stirrup_diameter" class="form-control">
                                    <option value="0">None</option>
                                    <?php for ($d = 6; $d <= 12; $d += 2): ?>
                                        <option value="<?= $d ?>" <?= ($_POST['stirrup_diameter'] ?? 8) == $d ? 'selected' : '' ?>><?= $d ?>mm</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="stirrup_spacing">Stirrup Spacing (mm):</label>
                                <input type="number" name="stirrup_spacing" class="form-control" min="0" value="<?= $_POST['stirrup_spacing'] ?? 150 ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slab Form -->
                <div id="slab_form" class="element-form">
                    <div class="bar-fields">
                        <div class="form-group">
                            <label for="slab_length">Slab Length (m):</label>
                            <input type="number" name="slab_length" class="form-control" step="0.01" min="0.1" value="<?= $_POST['slab_length'] ?? 10 ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="slab_width">Slab Width (m):</label>
                            <input type="number" name="slab_width" class="form-control" step="0.01" min="0.1" value="<?= $_POST['slab_width'] ?? 8 ?>" required>
                        </div>
                    </div>
                    
                    <div class="bar-type-section">
                        <h4>Main Bars (X Direction)</h4>
                        <div class="bar-fields">
                            <div class="form-group">
                                <label for="main_bar_diameter_x">Bar Diameter (mm):</label>
                                <select name="main_bar_diameter_x" class="form-control">
                                    <option value="0">None</option>
                                    <?php for ($d = 8; $d <= 16; $d += 2): ?>
                                        <option value="<?= $d ?>" <?= ($_POST['main_bar_diameter_x'] ?? 10) == $d ? 'selected' : '' ?>><?= $d ?>mm</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="main_bar_spacing_x">Bar Spacing (mm):</label>
                                <input type="number" name="main_bar_spacing_x" class="form-control" min="0" value="<?= $_POST['main_bar_spacing_x'] ?? 150 ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bar-type-section">
                        <h4>Main Bars (Y Direction)</h4>
                        <div class="bar-fields">
                            <div class="form-group">
                                <label for="main_bar_diameter_y">Bar Diameter (mm):</label>
                                <select name="main_bar_diameter_y" class="form-control">
                                    <option value="0">None</option>
                                    <?php for ($d = 8; $d <= 16; $d += 2): ?>
                                        <option value="<?= $d ?>" <?= ($_POST['main_bar_diameter_y'] ?? 10) == $d ? 'selected' : '' ?>><?= $d ?>mm</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="main_bar_spacing_y">Bar Spacing (mm):</label>
                                <input type="number" name="main_bar_spacing_y" class="form-control" min="0" value="<?= $_POST['main_bar_spacing_y'] ?? 150 ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Rebar Quantity</button>
            </form>

            <?php if (!empty($results)): ?>
            <div class="result-area">
                <h3><i class="fas fa-calculator"></i> Calculation Results</h3>
                
                <?php if (isset($results['single_bar'])): ?>
                    <div class="bar-type-section">
                        <h4>Single Bar Calculation</h4>
                        <div class="result-item">
                            <span>Bar Diameter:</span>
                            <span class="result-value"><?= $results['single_bar']['diameter'] ?> mm</span>
                        </div>
                        <div class="result-item">
                            <span>Weight per Meter:</span>
                            <span class="result-value"><?= $results['single_bar']['weight_per_meter'] ?> kg/m</span>
                        </div>
                        <div class="result-item">
                            <span>Total Weight:</span>
                            <span class="result-value"><?= $results['single_bar']['total_weight'] ?> kg</span>
                        </div>
                        <div class="result-item">
                            <span>Weight with Wastage (<?= ($_POST['wastage'] ?? 5) ?>%):</span>
                            <span class="result-value"><?= $results['single_bar']['total_weight_with_wastage'] ?> kg</span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($results['summary'])): ?>
                    <div class="bar-type-section">
                        <h4>Summary</h4>
                        <div class="materials-grid">
                            <div class="material-card">
                                <div class="material-value"><?= $results['summary']['total_weight'] ?></div>
                                <div class="material-label">Total Weight (kg)</div>
                            </div>
                            <div class="material-card">
                                <div class="material-value"><?= $results['summary']['total_weight_with_wastage'] ?></div>
                                <div class="material-label">With Wastage (kg)</div>
                            </div>
                        </div>
                    </div>
                    
                    <?php foreach ($results as $key => $value): ?>
                        <?php if ($key !== 'summary' && is_array($value)): ?>
                        <div class="bar-type-section">
                            <h4><?= ucfirst(str_replace('_', ' ', $key)) ?></h4>
                            <div class="result-item">
                                <span>Weight:</span>
                                <span class="result-value"><?= $value['total_weight'] ?> kg</span>
                            </div>
                            <div class="result-item">
                                <span>Weight with Wastage:</span>
                                <span class="result-value"><?= $value['total_weight_with_wastage'] ?> kg</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Toolkit</a>
    </div>

    <script>
        function showElementForm() {
            const elementType = document.getElementById('element_type').value;
            const forms = document.querySelectorAll('.element-form');
            
            forms.forEach(form => {
                form.classList.remove('active');
            });
            
            if (elementType) {
                document.getElementById(elementType + '_form').classList.add('active');
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', showElementForm);
    </script>
</body>
</html>