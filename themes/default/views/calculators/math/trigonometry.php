<?php $page_title = $title ?? 'Trigonometric Functions'; ?>
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
        .calc-input { font-size: 1.3rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; }
        .calc-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 12px; padding: 30px; text-align: center; margin-top: 30px; }
        .result-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; text-align: center; }
        .result-item { background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; }
        .result-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px; }
        .result-val { font-size: 1.4rem; font-weight: 700; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
        .unit-toggle .btn { width: 50%; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-activity" style="font-size: 2.5rem;"></i>
                <h2>Trigonometric Functions</h2>
                <p class="mb-0 mt-2">Calculate Sin, Cos, Tan, Csc, Sec, Cot</p>
            </div>
            <div class="calc-body">
                <div class="row g-3 justify-content-center">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Angle</label>
                        <input type="number" id="angle_input" class="form-control calc-input" value="45" step="any">
                    </div>
                </div>
                
                <div class="row mt-3 justify-content-center">
                    <div class="col-md-6">
                        <div class="btn-group w-100 unit-toggle" role="group">
                            <input type="radio" class="btn-check" name="angle_unit" id="deg" autocomplete="off" checked onchange="calculate()">
                            <label class="btn btn-outline-primary" for="deg">Degrees</label>

                            <input type="radio" class="btn-check" name="angle_unit" id="rad" autocomplete="off" onchange="calculate()">
                            <label class="btn btn-outline-primary" for="rad">Radians</label>
                        </div>
                    </div>
                </div>

                <div class="result-box mt-4">
                    <div class="result-grid">
                        <div class="result-item">
                            <div class="result-label">sin(θ)</div>
                            <div class="result-val" id="val_sin">0</div>
                        </div>
                        <div class="result-item">
                            <div class="result-label">cos(θ)</div>
                            <div class="result-val" id="val_cos">0</div>
                        </div>
                        <div class="result-item">
                            <div class="result-label">tan(θ)</div>
                            <div class="result-val" id="val_tan">0</div>
                        </div>
                        <div class="result-item">
                            <div class="result-label">csc(θ)</div>
                            <div class="result-val" id="val_csc">0</div>
                        </div>
                        <div class="result-item">
                            <div class="result-label">sec(θ)</div>
                            <div class="result-val" id="val_sec">0</div>
                        </div>
                        <div class="result-item">
                            <div class="result-label">cot(θ)</div>
                            <div class="result-val" id="val_cot">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const input = document.getElementById('angle_input');
        
        input.addEventListener('input', calculate);

        function calculate() {
            let angle = parseFloat(input.value);
            if (isNaN(angle)) return;

            const isDegree = document.getElementById('deg').checked;
            let rad = angle;
            
            if (isDegree) {
                rad = angle * (Math.PI / 180);
            }

            const sin = Math.sin(rad);
            const cos = Math.cos(rad);
            const tan = Math.tan(rad);

            updateVal('val_sin', sin);
            updateVal('val_cos', cos);
            updateVal('val_tan', tan);
            updateVal('val_csc', 1/sin);
            updateVal('val_sec', 1/cos);
            updateVal('val_cot', 1/tan);
        }

        function updateVal(id, val) {
            const el = document.getElementById(id);
            if (!isFinite(val)) {
                el.textContent = "Undefined";
            } else if (Math.abs(val) < 1e-10) {
                el.textContent = "0"; // Handle practically zero
            } else if (Math.abs(val) > 1e10) {
                el.textContent = "∞"; // Handle huge numbers
            } else {
                el.textContent = val.toFixed(6).replace(/\.?0+$/, "");
            }
        }
        
        // Init
        calculate();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
