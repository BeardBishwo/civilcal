<?php
// modules/estimation/quantity-takeoff/concrete-quantity.php
/**
 * Concrete Quantity Calculator
 * Calculates volume for footing, column, beam, slab
 * Part of AEC Estimation Suite
 */

class ConcreteQuantityCalculator {
    private $wastage_factor;
    private $unit_preference;
    
    public function __construct($wastage_factor = 0.05, $unit_preference = 'metric') {
        $this->wastage_factor = $wastage_factor;
        $this->unit_preference = $unit_preference;
    }
    
    public function calculateFooting($length, $width, $depth, $quantity = 1) {
        $volume = $length * $width * $depth * $quantity;
        $volume_with_wastage = $volume * (1 + $this->wastage_factor);
        
        return [
            'volume' => round($volume, 3),
            'volume_with_wastage' => round($volume_with_wastage, 3),
            'type' => 'footing',
            'units' => $this->unit_preference == 'metric' ? 'm³' : 'ft³'
        ];
    }
    
    public function calculateColumn($length, $width, $height, $quantity = 1) {
        $volume = $length * $width * $height * $quantity;
        $volume_with_wastage = $volume * (1 + $this->wastage_factor);
        
        return [
            'volume' => round($volume, 3),
            'volume_with_wastage' => round($volume_with_wastage, 3),
            'type' => 'column',
            'units' => $this->unit_preference == 'metric' ? 'm³' : 'ft³'
        ];
    }
    
    public function calculateCircularColumn($diameter, $height, $quantity = 1) {
        $radius = $diameter / 2;
        $cross_section = pi() * pow($radius, 2);
        $volume = $cross_section * $height * $quantity;
        $volume_with_wastage = $volume * (1 + $this->wastage_factor);
        
        return [
            'volume' => round($volume, 3),
            'volume_with_wastage' => round($volume_with_wastage, 3),
            'type' => 'circular_column',
            'units' => $this->unit_preference == 'metric' ? 'm³' : 'ft³'
        ];
    }
    
    public function calculateBeam($length, $width, $depth, $quantity = 1) {
        $volume = $length * $width * $depth * $quantity;
        $volume_with_wastage = $volume * (1 + $this->wastage_factor);
        
        return [
            'volume' => round($volume, 3),
            'volume_with_wastage' => round($volume_with_wastage, 3),
            'type' => 'beam',
            'units' => $this->unit_preference == 'metric' ? 'm³' : 'ft³'
        ];
    }
    
    public function calculateSlab($length, $width, $thickness, $quantity = 1) {
        $volume = $length * $width * $thickness * $quantity;
        $volume_with_wastage = $volume * (1 + $this->wastage_factor);
        
        return [
            'volume' => round($volume, 3),
            'volume_with_wastage' => round($volume_with_wastage, 3),
            'type' => 'slab',
            'units' => $this->unit_preference == 'metric' ? 'm³' : 'ft³'
        ];
    }
}

// Initialize calculator
$calculator = new ConcreteQuantityCalculator();
$results = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $element_type = $_POST['element_type'] ?? '';
    $wastage = floatval($_POST['wastage'] ?? 5) / 100;
    $units = $_POST['units'] ?? 'metric';
    
    $calculator = new ConcreteQuantityCalculator($wastage, $units);
    
    switch ($element_type) {
        case 'footing':
            $length = floatval($_POST['footing_length']);
            $width = floatval($_POST['footing_width']);
            $depth = floatval($_POST['footing_depth']);
            $quantity = intval($_POST['footing_quantity']);
            $results = $calculator->calculateFooting($length, $width, $depth, $quantity);
            break;
            
        case 'column':
            $length = floatval($_POST['column_length']);
            $width = floatval($_POST['column_width']);
            $height = floatval($_POST['column_height']);
            $quantity = intval($_POST['column_quantity']);
            $results = $calculator->calculateColumn($length, $width, $height, $quantity);
            break;
            
        case 'circular_column':
            $diameter = floatval($_POST['circular_diameter']);
            $height = floatval($_POST['circular_height']);
            $quantity = intval($_POST['circular_quantity']);
            $results = $calculator->calculateCircularColumn($diameter, $height, $quantity);
            break;
            
        case 'beam':
            $length = floatval($_POST['beam_length']);
            $width = floatval($_POST['beam_width']);
            $depth = floatval($_POST['beam_depth']);
            $quantity = intval($_POST['beam_quantity']);
            $results = $calculator->calculateBeam($length, $width, $depth, $quantity);
            break;
            
        case 'slab':
            $length = floatval($_POST['slab_length']);
            $width = floatval($_POST['slab_width']);
            $thickness = floatval($_POST['slab_thickness']);
            $quantity = intval($_POST['slab_quantity']);
            $results = $calculator->calculateSlab($length, $width, $thickness, $quantity);
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concrete Quantity Calculator - AEC Toolkit</title>
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
            <h1><i class="fas fa-cube"></i> Concrete Quantity Calculator</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="element_type">Select Structural Element:</label>
                    <select name="element_type" id="element_type" class="form-control" onchange="showElementForm()" required>
                        <option value="">-- Choose Element Type --</option>
                        <option value="footing" <?= isset($_POST['element_type']) && $_POST['element_type'] == 'footing' ? 'selected' : '' ?>>Footing/Foundation</option>
                        <option value="column" <?= isset($_POST['element_type']) && $_POST['element_type'] == 'column' ? 'selected' : '' ?>>Rectangular Column</option>
                        <option value="circular_column" <?= isset($_POST['element_type']) && $_POST['element_type'] == 'circular_column' ? 'selected' : '' ?>>Circular Column</option>
                        <option value="beam" <?= isset($_POST['element_type']) && $_POST['element_type'] == 'beam' ? 'selected' : '' ?>>Beam</option>
                        <option value="slab" <?= isset($_POST['element_type']) && $_POST['element_type'] == 'slab' ? 'selected' : '' ?>>Slab</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="wastage">Wastage Percentage (%):</label>
                    <input type="number" name="wastage" class="form-control" value="<?= $_POST['wastage'] ?? 5 ?>" min="0" max="20" step="0.5" required>
                </div>

                <div class="form-group">
                    <label for="units">Measurement Units:</label>
                    <select name="units" id="units" class="form-control">
                        <option value="metric" <?= ($_POST['units'] ?? 'metric') == 'metric' ? 'selected' : '' ?>>Metric (meters, m³)</option>
                        <option value="imperial" <?= ($_POST['units'] ?? 'metric') == 'imperial' ? 'selected' : '' ?>>Imperial (feet, ft³)</option>
                    </select>
                </div>

                <!-- Footing Form -->
                <div id="footing_form" class="element-form">
                    <div class="form-group">
                        <label for="footing_length">Length (m):</label>
                        <input type="number" name="footing_length" class="form-control" step="0.01" min="0.1" value="<?= $_POST['footing_length'] ?? 2 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="footing_width">Width (m):</label>
                        <input type="number" name="footing_width" class="form-control" step="0.01" min="0.1" value="<?= $_POST['footing_width'] ?? 2 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="footing_depth">Depth (m):</label>
                        <input type="number" name="footing_depth" class="form-control" step="0.01" min="0.1" value="<?= $_POST['footing_depth'] ?? 0.5 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="footing_quantity">Quantity:</label>
                        <input type="number" name="footing_quantity" class="form-control" min="1" value="<?= $_POST['footing_quantity'] ?? 1 ?>" required>
                    </div>
                </div>

                <!-- Column Form -->
                <div id="column_form" class="element-form">
                    <div class="form-group">
                        <label for="column_length">Length (m):</label>
                        <input type="number" name="column_length" class="form-control" step="0.01" min="0.1" value="<?= $_POST['column_length'] ?? 0.3 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="column_width">Width (m):</label>
                        <input type="number" name="column_width" class="form-control" step="0.01" min="0.1" value="<?= $_POST['column_width'] ?? 0.3 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="column_height">Height (m):</label>
                        <input type="number" name="column_height" class="form-control" step="0.01" min="0.1" value="<?= $_POST['column_height'] ?? 3 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="column_quantity">Quantity:</label>
                        <input type="number" name="column_quantity" class="form-control" min="1" value="<?= $_POST['column_quantity'] ?? 1 ?>" required>
                    </div>
                </div>

                <!-- Circular Column Form -->
                <div id="circular_column_form" class="element-form">
                    <div class="form-group">
                        <label for="circular_diameter">Diameter (m):</label>
                        <input type="number" name="circular_diameter" class="form-control" step="0.01" min="0.1" value="<?= $_POST['circular_diameter'] ?? 0.3 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="circular_height">Height (m):</label>
                        <input type="number" name="circular_height" class="form-control" step="0.01" min="0.1" value="<?= $_POST['circular_height'] ?? 3 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="circular_quantity">Quantity:</label>
                        <input type="number" name="circular_quantity" class="form-control" min="1" value="<?= $_POST['circular_quantity'] ?? 1 ?>" required>
                    </div>
                </div>

                <!-- Beam Form -->
                <div id="beam_form" class="element-form">
                    <div class="form-group">
                        <label for="beam_length">Length (m):</label>
                        <input type="number" name="beam_length" class="form-control" step="0.01" min="0.1" value="<?= $_POST['beam_length'] ?? 5 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="beam_width">Width (m):</label>
                        <input type="number" name="beam_width" class="form-control" step="0.01" min="0.1" value="<?= $_POST['beam_width'] ?? 0.3 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="beam_depth">Depth (m):</label>
                        <input type="number" name="beam_depth" class="form-control" step="0.01" min="0.1" value="<?= $_POST['beam_depth'] ?? 0.5 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="beam_quantity">Quantity:</label>
                        <input type="number" name="beam_quantity" class="form-control" min="1" value="<?= $_POST['beam_quantity'] ?? 1 ?>" required>
                    </div>
                </div>

                <!-- Slab Form -->
                <div id="slab_form" class="element-form">
                    <div class="form-group">
                        <label for="slab_length">Length (m):</label>
                        <input type="number" name="slab_length" class="form-control" step="0.01" min="0.1" value="<?= $_POST['slab_length'] ?? 10 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="slab_width">Width (m):</label>
                        <input type="number" name="slab_width" class="form-control" step="0.01" min="0.1" value="<?= $_POST['slab_width'] ?? 8 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="slab_thickness">Thickness (m):</label>
                        <input type="number" name="slab_thickness" class="form-control" step="0.01" min="0.05" value="<?= $_POST['slab_thickness'] ?? 0.15 ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="slab_quantity">Quantity:</label>
                        <input type="number" name="slab_quantity" class="form-control" min="1" value="<?= $_POST['slab_quantity'] ?? 1 ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Concrete Quantity</button>
            </form>

            <?php if (!empty($results)): ?>
            <div class="result-area">
                <h3><i class="fas fa-calculator"></i> Calculation Results</h3>
                <div class="result-item">
                    <span>Element Type:</span>
                    <span class="result-value"><?= ucfirst(str_replace('_', ' ', $results['type'])) ?></span>
                </div>
                <div class="result-item">
                    <span>Net Volume:</span>
                    <span class="result-value"><?= $results['volume'] ?> <?= $results['units'] ?></span>
                </div>
                <div class="result-item">
                    <span>Volume with Wastage (<?= ($_POST['wastage'] ?? 5) ?>%):</span>
                    <span class="result-value"><?= $results['volume_with_wastage'] ?> <?= $results['units'] ?></span>
                </div>
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