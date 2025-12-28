<?php $page_title = $title ?? 'Surface Area Calculator'; ?>
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
        .shape-btn { background: #f8f9fa; border: 2px solid #e9ecef; border-radius: 12px; padding: 15px; margin: 5px; cursor: pointer; transition: all 0.3s; text-align: center; height: 100px; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .shape-btn.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-color: #667eea; }
        .shape-btn i { font-size: 1.8rem; display: block; margin-bottom: 5px; }
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
                <i class="bi bi-layers-half" style="font-size: 2.5rem;"></i>
                <h2>Surface Area Calculator</h2>
                <p class="mb-0 mt-2">Calculate total surface area of 3D shapes</p>
            </div>
            <div class="calc-body">
                <div class="row mb-4 justify-content-center">
                    <div class="col-md-3 col-6">
                        <div class="shape-btn active" onclick="selectShape('cube', this)">
                            <i class="bi bi-box"></i>
                            <div>Cube</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="shape-btn" onclick="selectShape('cuboid', this)">
                            <i class="bi bi-bricks"></i>
                            <div>Cuboid</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="shape-btn" onclick="selectShape('cylinder', this)">
                            <i class="bi bi-database"></i>
                            <div>Cylinder</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="shape-btn" onclick="selectShape('sphere', this)">
                            <i class="bi bi-globe"></i>
                            <div>Sphere</div>
                        </div>
                    </div>
                </div>
                
                <!-- Cube -->
                <div id="shape_cube" class="shape-inputs">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Side Length</label>
                        <input type="number" id="cube_side" class="form-control calc-input" value="10" step="any">
                    </div>
                </div>
                
                <!-- Cuboid -->
                <div id="shape_cuboid" class="shape-inputs" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Length</label>
                            <input type="number" id="cuboid_length" class="form-control calc-input" value="10" step="any">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Width</label>
                            <input type="number" id="cuboid_width" class="form-control calc-input" value="5" step="any">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Height</label>
                            <input type="number" id="cuboid_height" class="form-control calc-input" value="4" step="any">
                        </div>
                    </div>
                </div>
                
                <!-- Cylinder -->
                <div id="shape_cylinder" class="shape-inputs" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Radius</label>
                            <input type="number" id="cyl_radius" class="form-control calc-input" value="5" step="any">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Height</label>
                            <input type="number" id="cyl_height" class="form-control calc-input" value="10" step="any">
                        </div>
                    </div>
                </div>

                <!-- Sphere -->
                <div id="shape_sphere" class="shape-inputs" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Radius</label>
                        <input type="number" id="sphere_radius" class="form-control calc-input" value="5" step="any">
                    </div>
                </div>
                
                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate Area</button>
                <div class="result-box" id="resultBox" style="display:none;">
                    <div>Total Surface Area</div>
                    <div class="result-value" id="areaValue">0</div>
                    <div id="formula"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let currentShape = 'cube';
        
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
            
            if (currentShape === 'cube') {
                const side = parseFloat(document.getElementById('cube_side').value);
                area = 6 * Math.pow(side, 2);
                formula = `6 × ${side}² = ${area.toFixed(2)}`;
            } else if (currentShape === 'cuboid') {
                const l = parseFloat(document.getElementById('cuboid_length').value);
                const w = parseFloat(document.getElementById('cuboid_width').value);
                const h = parseFloat(document.getElementById('cuboid_height').value);
                area = 2 * ((l * w) + (w * h) + (h * l));
                formula = `2 × ((${l}×${w}) + (${w}×${h}) + (${h}×${l})) = ${area.toFixed(2)}`;
            } else if (currentShape === 'cylinder') {
                const r = parseFloat(document.getElementById('cyl_radius').value);
                const h = parseFloat(document.getElementById('cyl_height').value);
                area = (2 * Math.PI * r * h) + (2 * Math.PI * Math.pow(r, 2));
                formula = `(2π × ${r} × ${h}) + (2π × ${r}²) = ${area.toFixed(2)}`;
            } else if (currentShape === 'sphere') {
                const r = parseFloat(document.getElementById('sphere_radius').value);
                area = 4 * Math.PI * Math.pow(r, 2);
                formula = `4π × ${r}² = ${area.toFixed(2)}`;
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
