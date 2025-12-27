<?php $page_title = $title ?? 'Area Calculator'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo app_base_url('/themes/default/assets/css/floating-calculator.css'); ?>">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 0; }
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 800px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .shape-btn { background: #f8f9fa; border: 2px solid #e9ecef; border-radius: 12px; padding: 20px; margin: 10px; cursor: pointer; transition: all 0.3s; text-align: center; }
        .shape-btn.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-color: #667eea; }
        .shape-btn i { font-size: 2rem; display: block; margin-bottom: 10px; }
        .calc-input { font-size: 1.3rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; }
        .calc-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 12px; padding: 30px; text-align: center; margin-top: 30px; }
        .result-value { font-size: 3rem; font-weight: 700; margin: 10px 0; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-bounding-box" style="font-size: 2.5rem;"></i>
                <h2>Area Calculator</h2>
                <p class="mb-0 mt-2">Calculate area of different shapes</p>
            </div>
            <div class="calc-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="shape-btn active" onclick="selectShape('square', this)">
                            <i class="bi bi-square"></i>
                            <div>Square</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="shape-btn" onclick="selectShape('rectangle', this)">
                            <i class="bi bi-square"></i>
                            <div>Rectangle</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="shape-btn" onclick="selectShape('circle', this)">
                            <i class="bi bi-circle"></i>
                            <div>Circle</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="shape-btn" onclick="selectShape('triangle', this)">
                            <i class="bi bi-triangle"></i>
                            <div>Triangle</div>
                        </div>
                    </div>
                </div>
                
                <!-- Square -->
                <div id="shape_square" class="shape-inputs">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Side Length</label>
                        <input type="number" id="square_side" class="form-control calc-input" value="10" step="any">
                    </div>
                </div>
                
                <!-- Rectangle -->
                <div id="shape_rectangle" class="shape-inputs" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Length</label>
                            <input type="number" id="rect_length" class="form-control calc-input" value="10" step="any">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Width</label>
                            <input type="number" id="rect_width" class="form-control calc-input" value="5" step="any">
                        </div>
                    </div>
                </div>
                
                <!-- Circle -->
                <div id="shape_circle" class="shape-inputs" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Radius</label>
                        <input type="number" id="circle_radius" class="form-control calc-input" value="5" step="any">
                    </div>
                </div>
                
                <!-- Triangle -->
                <div id="shape_triangle" class="shape-inputs" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Base</label>
                            <input type="number" id="tri_base" class="form-control calc-input" value="10" step="any">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Height</label>
                            <input type="number" id="tri_height" class="form-control calc-input" value="8" step="any">
                        </div>
                    </div>
                </div>
                
                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate Area</button>
                <div class="result-box" id="resultBox" style="display:none;">
                    <div>Area</div>
                    <div class="result-value" id="areaValue">0</div>
                    <div id="formula"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let currentShape = 'square';
        
        function selectShape(shape, btn) {
            document.querySelectorAll('.shape-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('.shape-inputs').forEach(s => s.style.display = 'none');
            document.getElementById('shape_' + shape).style.display = 'block';
            currentShape = shape;
        }
        
        function calculate() {
            let area = 0;
            let formula = '';
            
            if (currentShape === 'square') {
                const side = parseFloat(document.getElementById('square_side').value);
                area = side * side;
                formula = `${side} × ${side} = ${area.toFixed(2)}`;
            } else if (currentShape === 'rectangle') {
                const length = parseFloat(document.getElementById('rect_length').value);
                const width = parseFloat(document.getElementById('rect_width').value);
                area = length * width;
                formula = `${length} × ${width} = ${area.toFixed(2)}`;
            } else if (currentShape === 'circle') {
                const radius = parseFloat(document.getElementById('circle_radius').value);
                area = Math.PI * radius * radius;
                formula = `π × ${radius}² = ${area.toFixed(2)}`;
            } else if (currentShape === 'triangle') {
                const base = parseFloat(document.getElementById('tri_base').value);
                const height = parseFloat(document.getElementById('tri_height').value);
                area = 0.5 * base * height;
                formula = `½ × ${base} × ${height} = ${area.toFixed(2)}`;
            }
            
            document.getElementById('resultBox').style.display = 'block';
            document.getElementById('areaValue').textContent = area.toFixed(2);
            document.getElementById('formula').textContent = formula;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
