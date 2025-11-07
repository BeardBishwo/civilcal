<?php
// modules/estimation/material-estimation/masonry-materials.php
/**
 * Masonry Materials Calculator
 * Calculates cement & sand for brick/block masonry
 * Part of AEC Estimation Suite
 */

class MasonryMaterialsCalculator {
    private $wastage_factor;
    
    public function __construct($wastage_factor = 0.05) {
        $this->wastage_factor = $wastage_factor;
    }
    
    public function calculateBrickworkMaterials($brick_quantity, $mortar_thickness = 10, $mix_ratio = '1:6') {
        // Standard brick size: 230mm x 115mm x 75mm
        $brick_volume = 0.23 * 0.115 * 0.075; // m³
        
        // Mortar volume per brick (approximate)
        $mortar_volume_per_brick = 0.00023 * $mortar_thickness / 10; // m³ per brick
        
        $total_mortar_volume = $brick_quantity * $mortar_volume_per_brick;
        $total_mortar_with_wastage = $total_mortar_volume * (1 + $this->wastage_factor);
        
        // Calculate cement and sand
        list($cement, $sand) = explode(':', $mix_ratio);
        $total_parts = $cement + $sand;
        
        $cement_volume = $total_mortar_with_wastage * ($cement / $total_parts);
        $sand_volume = $total_mortar_with_wastage * ($sand / $total_parts);
        
        // Cement in bags
        $cement_bags = $cement_volume / 0.035;
        
        return [
            'brick_quantity' => $brick_quantity,
            'mortar_volume' => round($total_mortar_volume, 3),
            'mortar_with_wastage' => round($total_mortar_with_wastage, 3),
            'cement_bags' => ceil($cement_bags),
            'sand_volume' => round($sand_volume, 3),
            'mix_ratio' => $mix_ratio,
            'units' => [
                'bricks' => 'nos',
                'mortar' => 'm³',
                'cement' => 'bags',
                'sand' => 'm³'
            ]
        ];
    }
    
    public function calculateBlockworkMaterials($block_quantity, $block_type = 'solid', $mix_ratio = '1:6') {
        // Block sizes (m)
        $block_sizes = [
            'solid' => ['length' => 0.4, 'height' => 0.2, 'thickness' => 0.2],
            'hollow' => ['length' => 0.4, 'height' => 0.2, 'thickness' => 0.15],
            'cellular' => ['length' => 0.4, 'height' => 0.2, 'thickness' => 0.2]
        ];
        
        $block = $block_sizes[$block_type] ?? $block_sizes['solid'];
        $block_area = $block['length'] * $block['height'];
        
        // Mortar volume (approx 10% of block volume)
        $block_volume = $block['length'] * $block['height'] * $block['thickness'];
        $mortar_volume_per_block = $block_volume * 0.1;
        
        $total_mortar_volume = $block_quantity * $mortar_volume_per_block;
        $total_mortar_with_wastage = $total_mortar_volume * (1 + $this->wastage_factor);
        
        // Calculate cement and sand
        list($cement, $sand) = explode(':', $mix_ratio);
        $total_parts = $cement + $sand;
        
        $cement_volume = $total_mortar_with_wastage * ($cement / $total_parts);
        $sand_volume = $total_mortar_with_wastage * ($sand / $total_parts);
        
        $cement_bags = $cement_volume / 0.035;
        
        return [
            'block_quantity' => $block_quantity,
            'block_type' => $block_type,
            'mortar_volume' => round($total_mortar_volume, 3),
            'mortar_with_wastage' => round($total_mortar_with_wastage, 3),
            'cement_bags' => ceil($cement_bags),
            'sand_volume' => round($sand_volume, 3),
            'mix_ratio' => $mix_ratio,
            'units' => [
                'blocks' => 'nos',
                'mortar' => 'm³',
                'cement' => 'bags',
                'sand' => 'm³'
            ]
        ];
    }
}

// Initialize calculator
$calculator = new MasonryMaterialsCalculator();
$results = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $masonry_type = $_POST['masonry_type'] ?? 'brick';
    $quantity = intval($_POST['quantity'] ?? 0);
    $mortar_thickness = floatval($_POST['mortar_thickness'] ?? 10);
    $mix_ratio = $_POST['mix_ratio'] ?? '1:6';
    $wastage = floatval($_POST['wastage'] ?? 5) / 100;
    
    $calculator = new MasonryMaterialsCalculator($wastage);
    
    if ($masonry_type === 'brick') {
        $results = $calculator->calculateBrickworkMaterials($quantity, $mortar_thickness, $mix_ratio);
    } else {
        $block_type = $_POST['block_type'] ?? 'solid';
        $results = $calculator->calculateBlockworkMaterials($quantity, $block_type, $mix_ratio);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masonry Materials Calculator - AEC Toolkit</title>
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

        .masonry-type-fields {
            display: none;
        }

        .masonry-type-fields.active {
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
            <h1><i class="fas fa-th"></i> Masonry Materials Calculator</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="masonry_type">Masonry Type:</label>
                    <select name="masonry_type" id="masonry_type" class="form-control" onchange="showMasonryFields()" required>
                        <option value="brick" <?= ($_POST['masonry_type'] ?? 'brick') == 'brick' ? 'selected' : '' ?>>Brick Work</option>
                        <option value="block" <?= ($_POST['masonry_type'] ?? 'brick') == 'block' ? 'selected' : '' ?>>Concrete Block Work</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="quantity" id="quantity_label">Number of Bricks:</label>
                    <input type="number" name="quantity" class="form-control" min="1" value="<?= $_POST['quantity'] ?? 1000 ?>" required>
                </div>

                <!-- Brick Fields -->
                <div id="brick_fields" class="masonry-type-fields <?= ($_POST['masonry_type'] ?? 'brick') == 'brick' ? 'active' : '' ?>">
                    <div class="form-group">
                        <label for="mortar_thickness">Mortar Thickness (mm):</label>
                        <input type="number" name="mortar_thickness" class="form-control" value="<?= $_POST['mortar_thickness'] ?? 10 ?>" min="6" max="20" step="1" required>
                    </div>
                </div>

                <!-- Block Fields -->
                <div id="block_fields" class="masonry-type-fields <?= ($_POST['masonry_type'] ?? 'brick') == 'block' ? 'active' : '' ?>">
                    <div class="form-group">
                        <label for="block_type">Block Type:</label>
                        <select name="block_type" class="form-control">
                            <option value="solid" <?= ($_POST['block_type'] ?? 'solid') == 'solid' ? 'selected' : '' ?>>Solid Concrete Block</option>
                            <option value="hollow" <?= ($_POST['block_type'] ?? 'solid') == 'hollow' ? 'selected' : '' ?>>Hollow Concrete Block</option>
                            <option value="cellular" <?= ($_POST['block_type'] ?? 'solid') == 'cellular' ? 'selected' : '' ?>>Cellular Lightweight Block</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="mix_ratio">Mortar Mix Ratio (Cement:Sand):</label>
                    <select name="mix_ratio" class="form-control">
                        <option value="1:4" <?= ($_POST['mix_ratio'] ?? '1:6') == '1:4' ? 'selected' : '' ?>>1:4 (Rich Mix)</option>
                        <option value="1:5" <?= ($_POST['mix_ratio'] ?? '1:6') == '1:5' ? 'selected' : '' ?>>1:5</option>
                        <option value="1:6" <?= ($_POST['mix_ratio'] ?? '1:6') == '1:6' ? 'selected' : '' ?>>1:6 (Standard)</option>
                        <option value="1:8" <?= ($_POST['mix_ratio'] ?? '1:6') == '1:8' ? 'selected' : '' ?>>1:8 (Lean Mix)</option>
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
                    <span>
                        <?= isset($results['brick_quantity']) ? 'Bricks' : 'Blocks' ?>:
                    </span>
                    <span class="result-value">
                        <?= number_format(isset($results['brick_quantity']) ? $results['brick_quantity'] : $results['block_quantity']) ?> nos
                    </span>
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
                        <?php if (isset($results['block_type'])): ?>
                        • Block Type: <?= ucfirst($results['block_type']) ?><br>
                        <?php else: ?>
                        • Mortar Thickness: <?= $_POST['mortar_thickness'] ?? 10 ?>mm<br>
                        <?php endif; ?>
                        • Wastage: <?= ($_POST['wastage'] ?? 5) ?>% included
                    </small>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Toolkit</a>
    </div>

    <script>
        function showMasonryFields() {
            const masonryType = document.getElementById('masonry_type').value;
            const brickFields = document.getElementById('brick_fields');
            const blockFields = document.getElementById('block_fields');
            const quantityLabel = document.getElementById('quantity_label');
            
            // Hide all fields first
            brickFields.classList.remove('active');
            blockFields.classList.remove('active');
            
            // Show relevant fields
            if (masonryType === 'brick') {
                brickFields.classList.add('active');
                quantityLabel.textContent = 'Number of Bricks:';
            } else {
                blockFields.classList.add('active');
                quantityLabel.textContent = 'Number of Blocks:';
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', showMasonryFields);
    </script>
</body>
</html>