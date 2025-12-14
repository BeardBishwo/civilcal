<?php
// modules/estimation/quantity-takeoff/formwork-quantity.php
/**
 * Formwork Quantity Calculator
 * Calculates formwork area and shuttering requirements
 * Part of AEC Estimation Suite
 */

class FormworkCalculator {
    private $reuse_factor;
    
    public function __construct($reuse_factor = 0.8) {
        $this->reuse_factor = $reuse_factor;
    }
    
    public function calculateFormworkArea($elements = []) {
        $total_area = 0;
        $elements_detail = [];
        
        foreach ($elements as $element) {
            $area = 0;
            
            switch ($element['type']) {
                case 'footing':
                    // Footing: perimeter × depth (both sides)
                    $perimeter = 2 * ($element['length'] + $element['width']);
                    $area = $perimeter * $element['depth'];
                    break;
                    
                case 'column':
                    // Column: perimeter × height
                    $perimeter = 2 * ($element['length'] + $element['width']);
                    $area = $perimeter * $element['height'];
                    break;
                    
                case 'circular_column':
                    // Circular column: circumference × height
                    $circumference = pi() * $element['diameter'];
                    $area = $circumference * $element['height'];
                    break;
                    
                case 'beam':
                    // Beam: (2 × height + width) × length
                    $perimeter_section = 2 * $element['height'] + $element['width'];
                    $area = $perimeter_section * $element['length'];
                    break;
                    
                case 'slab':
                    // Slab: area (bottom only)
                    $area = $element['length'] * $element['width'];
                    break;
                    
                case 'wall':
                    // Wall: both sides
                    $area = 2 * $element['length'] * $element['height'];
                    break;
            }
            
            $area_with_reuse = $area * (1 - $this->reuse_factor);
            $total_area += $area;
            
            $elements_detail[] = [
                'type' => $element['type'],
                'area' => round($area, 2),
                'area_with_reuse' => round($area_with_reuse, 2)
            ];
        }
        
        return [
            'total_area' => round($total_area, 2),
            'elements_detail' => $elements_detail,
            'reuse_factor' => $this->reuse_factor * 100
        ];
    }
    
    public function calculatePlywoodSheets($area, $sheet_size = '8x4') {
        // Standard plywood sheet sizes in feet (converted to m²)
        $sheet_sizes = [
            '8x4' => 2.44 * 1.22, // 8ft x 4ft in meters
            '6x4' => 1.83 * 1.22, // 6ft x 4ft in meters
            '8x3' => 2.44 * 0.91  // 8ft x 3ft in meters
        ];
        
        $sheet_area = $sheet_sizes[$sheet_size] ?? $sheet_sizes['8x4'];
        $number_of_sheets = $area / $sheet_area;
        
        return [
            'sheets_required' => ceil($number_of_sheets),
            'sheet_area' => round($sheet_area, 2),
            'sheet_size' => $sheet_size
        ];
    }
}

// Initialize calculator
$calculator = new FormworkCalculator();
$area_results = [];
$plywood_results = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reuse_factor = floatval($_POST['reuse_factor'] ?? 80) / 100;
    $sheet_size = $_POST['sheet_size'] ?? '8x4';
    
    // Process elements
    $elements = [];
    $element_count = intval($_POST['element_count'] ?? 1);
    
    for ($i = 1; $i <= $element_count; $i++) {
        $element_type = $_POST["element_type_$i"] ?? '';
        if (!empty($element_type)) {
            $element = ['type' => $element_type];
            
            switch ($element_type) {
                case 'footing':
                    $element['length'] = floatval($_POST["footing_length_$i"] ?? 0);
                    $element['width'] = floatval($_POST["footing_width_$i"] ?? 0);
                    $element['depth'] = floatval($_POST["footing_depth_$i"] ?? 0);
                    break;
                    
                case 'column':
                    $element['length'] = floatval($_POST["column_length_$i"] ?? 0);
                    $element['width'] = floatval($_POST["column_width_$i"] ?? 0);
                    $element['height'] = floatval($_POST["column_height_$i"] ?? 0);
                    break;
                    
                case 'circular_column':
                    $element['diameter'] = floatval($_POST["circular_diameter_$i"] ?? 0);
                    $element['height'] = floatval($_POST["circular_height_$i"] ?? 0);
                    break;
                    
                case 'beam':
                    $element['length'] = floatval($_POST["beam_length_$i"] ?? 0);
                    $element['width'] = floatval($_POST["beam_width_$i"] ?? 0);
                    $element['height'] = floatval($_POST["beam_height_$i"] ?? 0);
                    break;
                    
                case 'slab':
                    $element['length'] = floatval($_POST["slab_length_$i"] ?? 0);
                    $element['width'] = floatval($_POST["slab_width_$i"] ?? 0);
                    break;
                    
                case 'wall':
                    $element['length'] = floatval($_POST["wall_length_$i"] ?? 0);
                    $element['height'] = floatval($_POST["wall_height_$i"] ?? 0);
                    break;
            }
            
            $elements[] = $element;
        }
    }
    
    $calculator = new FormworkCalculator($reuse_factor);
    $area_results = $calculator->calculateFormworkArea($elements);
    
    if (!empty($area_results)) {
        $plywood_results = $calculator->calculatePlywoodSheets($area_results['total_area'], $sheet_size);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formwork Quantity Calculator - AEC Toolkit</title>
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

        .element-section {
            background: rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .element-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
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
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-cubes"></i> Formwork Quantity Calculator</h1>
            <form method="POST" action="">
                <input type="hidden" name="element_count" id="element_count" value="<?= $_POST['element_count'] ?? 1 ?>">
                
                <div class="form-group">
                    <label for="reuse_factor">Formwork Reuse Percentage (%):</label>
                    <input type="number" name="reuse_factor" class="form-control" value="<?= $_POST['reuse_factor'] ?? 80 ?>" min="0" max="100" step="1" required>
                    <small style="opacity: 0.7;">Percentage of formwork that can be reused (typically 70-80%)</small>
                </div>
                
                <div class="form-group">
                    <label for="sheet_size">Plywood Sheet Size:</label>
                    <select name="sheet_size" class="form-control">
                        <option value="8x4" <?= ($_POST['sheet_size'] ?? '8x4') == '8x4' ? 'selected' : '' ?>>8ft × 4ft (2.44m × 1.22m)</option>
                        <option value="6x4" <?= ($_POST['sheet_size'] ?? '8x4') == '6x4' ? 'selected' : '' ?>>6ft × 4ft (1.83m × 1.22m)</option>
                        <option value="8x3" <?= ($_POST['sheet_size'] ?? '8x4') == '8x3' ? 'selected' : '' ?>>8ft × 3ft (2.44m × 0.91m)</option>
                    </select>
                </div>
                
                <div id="elements-container">
                    <?php
                    $element_count = $_POST['element_count'] ?? 1;
                    for ($i = 1; $i <= $element_count; $i++):
                        $element_type = $_POST["element_type_$i"] ?? 'footing';
                    ?>
                    <div class="element-section" id="element_<?= $i ?>">
                        <div class="form-group">
                            <label for="element_type_<?= $i ?>">Structural Element Type:</label>
                            <select name="element_type_<?= $i ?>" class="form-control" onchange="showElementFields(<?= $i ?>, this.value)">
                                <option value="footing" <?= $element_type == 'footing' ? 'selected' : '' ?>>Footing</option>
                                <option value="column" <?= $element_type == 'column' ? 'selected' : '' ?>>Rectangular Column</option>
                                <option value="circular_column" <?= $element_type == 'circular_column' ? 'selected' : '' ?>>Circular Column</option>
                                <option value="beam" <?= $element_type == 'beam' ? 'selected' : '' ?>>Beam</option>
                                <option value="slab" <?= $element_type == 'slab' ? 'selected' : '' ?>>Slab</option>
                                <option value="wall" <?= $element_type == 'wall' ? 'selected' : '' ?>>Wall</option>
                            </select>
                        </div>
                        
                        <div id="element_fields_<?= $i ?>">
                            <?php include "formwork-element-fields.php"; ?>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                
                <div style="margin: 2rem 0;">
                    <button type="button" class="btn-secondary" onclick="addElement()">+ Add Element</button>
                    <button type="button" class="btn-secondary" onclick="removeElement()">- Remove Element</button>
                </div>

                <button type="submit" class="btn-calculate">Calculate Formwork Quantity</button>
            </form>

            <?php if (!empty($area_results)): ?>
            <div class="result-area">
                <h3><i class="fas fa-calculator"></i> Calculation Results</h3>
                
                <div class="result-item">
                    <span>Total Formwork Area:</span>
                    <span class="result-value"><?= $area_results['total_area'] ?> m²</span>
                </div>
                
                <div class="result-item">
                    <span>Reuse Factor:</span>
                    <span class="result-value"><?= $area_results['reuse_factor'] ?>%</span>
                </div>

                <?php if (!empty($plywood_results)): ?>
                <div style="margin-top: 2rem;">
                    <h4><i class="fas fa-border-style"></i> Plywood Requirements (<?= $plywood_results['sheet_size'] ?>)</h4>
                    <div class="materials-grid">
                        <div class="material-card">
                            <div class="material-value"><?= $plywood_results['sheets_required'] ?></div>
                            <div class="material-label">Plywood Sheets</div>
                        </div>
                        <div class="material-card">
                            <div class="material-value"><?= $plywood_results['sheet_area'] ?></div>
                            <div class="material-label">Sheet Area (m²)</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (count($area_results['elements_detail']) > 0): ?>
                <div style="margin-top: 2rem;">
                    <h4>Element-wise Breakdown</h4>
                    <?php foreach ($area_results['elements_detail'] as $index => $element): ?>
                    <div class="result-item">
                        <span><?= ucfirst(str_replace('_', ' ', $element['type'])) ?>:</span>
                        <span class="result-value"><?= $element['area'] ?> m²</span>
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
        let elementCount = <?= $element_count ?>;
        
        function addElement() {
            elementCount++;
            document.getElementById('element_count').value = elementCount;
            
            const container = document.getElementById('elements-container');
            const newElement = document.createElement('div');
            newElement.className = 'element-section';
            newElement.id = 'element_' + elementCount;
            newElement.innerHTML = `
                <div class="form-group">
                    <label for="element_type_${elementCount}">Structural Element Type:</label>
                    <select name="element_type_${elementCount}" class="form-control" onchange="showElementFields(${elementCount}, this.value)">
                        <option value="footing">Footing</option>
                        <option value="column">Rectangular Column</option>
                        <option value="circular_column">Circular Column</option>
                        <option value="beam">Beam</option>
                        <option value="slab">Slab</option>
                        <option value="wall">Wall</option>
                    </select>
                </div>
                <div id="element_fields_${elementCount}">
                    <!-- Fields will be loaded by showElementFields -->
                </div>
            `;
            
            container.appendChild(newElement);
            showElementFields(elementCount, 'footing');
        }
        
        function removeElement() {
            if (elementCount > 1) {
                const container = document.getElementById('elements-container');
                container.removeChild(container.lastChild);
                elementCount--;
                document.getElementById('element_count').value = elementCount;
            }
        }
        
        function showElementFields(elementIndex, elementType) {
            const container = document.getElementById('element_fields_' + elementIndex);
            
            let fieldsHTML = '';
            
            switch (elementType) {
                case 'footing':
                    fieldsHTML = `
                        <div class="element-fields">
                            <div class="form-group">
                                <label for="footing_length_${elementIndex}">Length (m):</label>
                                <input type="number" name="footing_length_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['footing_length_1'] ?? 2 ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="footing_width_${elementIndex}">Width (m):</label>
                                <input type="number" name="footing_width_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['footing_width_1'] ?? 2 ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="footing_depth_${elementIndex}">Depth (m):</label>
                                <input type="number" name="footing_depth_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['footing_depth_1'] ?? 0.5 ?>" required>
                            </div>
                        </div>
                    `;
                    break;
                    
                case 'column':
                    fieldsHTML = `
                        <div class="element-fields">
                            <div class="form-group">
                                <label for="column_length_${elementIndex}">Length (m):</label>
                                <input type="number" name="column_length_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['column_length_1'] ?? 0.3 ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="column_width_${elementIndex}">Width (m):</label>
                                <input type="number" name="column_width_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['column_width_1'] ?? 0.3 ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="column_height_${elementIndex}">Height (m):</label>
                                <input type="number" name="column_height_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['column_height_1'] ?? 3 ?>" required>
                            </div>
                        </div>
                    `;
                    break;
                    
                case 'circular_column':
                    fieldsHTML = `
                        <div class="element-fields">
                            <div class="form-group">
                                <label for="circular_diameter_${elementIndex}">Diameter (m):</label>
                                <input type="number" name="circular_diameter_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['circular_diameter_1'] ?? 0.3 ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="circular_height_${elementIndex}">Height (m):</label>
                                <input type="number" name="circular_height_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['circular_height_1'] ?? 3 ?>" required>
                            </div>
                        </div>
                    `;
                    break;
                    
                case 'beam':
                    fieldsHTML = `
                        <div class="element-fields">
                            <div class="form-group">
                                <label for="beam_length_${elementIndex}">Length (m):</label>
                                <input type="number" name="beam_length_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['beam_length_1'] ?? 5 ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="beam_width_${elementIndex}">Width (m):</label>
                                <input type="number" name="beam_width_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['beam_width_1'] ?? 0.3 ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="beam_height_${elementIndex}">Height (m):</label>
                                <input type="number" name="beam_height_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['beam_height_1'] ?? 0.5 ?>" required>
                            </div>
                        </div>
                    `;
                    break;
                    
                case 'slab':
                    fieldsHTML = `
                        <div class="element-fields">
                            <div class="form-group">
                                <label for="slab_length_${elementIndex}">Length (m):</label>
                                <input type="number" name="slab_length_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['slab_length_1'] ?? 10 ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="slab_width_${elementIndex}">Width (m):</label>
                                <input type="number" name="slab_width_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['slab_width_1'] ?? 8 ?>" required>
                            </div>
                        </div>
                    `;
                    break;
                    
                case 'wall':
                    fieldsHTML = `
                        <div class="element-fields">
                            <div class="form-group">
                                <label for="wall_length_${elementIndex}">Length (m):</label>
                                <input type="number" name="wall_length_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['wall_length_1'] ?? 10 ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="wall_height_${elementIndex}">Height (m):</label>
                                <input type="number" name="wall_height_${elementIndex}" class="form-control" step="0.01" min="0.1" value="<?= $_POST['wall_height_1'] ?? 3 ?>" required>
                            </div>
                        </div>
                    `;
                    break;
            }
            
            container.innerHTML = fieldsHTML;
        }
        
        // Initialize fields for existing elements
        document.addEventListener('DOMContentLoaded', function() {
            <?php for ($i = 1; $i <= $element_count; $i++): ?>
                showElementFields(<?= $i ?>, '<?= $_POST["element_type_$i"] ?? 'footing' ?>');
            <?php endfor; ?>
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>