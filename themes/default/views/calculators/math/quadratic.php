<?php $page_title = $title ?? 'Quadratic Equation Solver'; ?>
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
        .calc-input { font-size: 1.3rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; text-align: center;}
        .calc-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 12px; padding: 30px; text-align: center; margin-top: 30px; }
        .result-value { font-size: 1.5rem; font-weight: 700; margin: 10px 0; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
        .equation-display { font-size: 1.5rem; color: #555; margin-bottom: 25px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-x-diamond-fill" style="font-size: 2.5rem;"></i>
                <h2>Quadratic Equation Solver</h2>
                <p class="mb-0 mt-2">Solve for x in ax² + bx + c = 0</p>
            </div>
            <div class="calc-body">
                <div class="equation-display">
                    <span id="disp_a">a</span>x² + <span id="disp_b">b</span>x + <span id="disp_c">c</span> = 0
                </div>

                <div class="row g-3 justify-content-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-center w-100">a</label>
                        <input type="number" id="input_a" class="form-control calc-input" value="1" step="any" oninput="updateEquation()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-center w-100">b</label>
                        <input type="number" id="input_b" class="form-control calc-input" value="-3" step="any" oninput="updateEquation()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-center w-100">c</label>
                        <input type="number" id="input_c" class="form-control calc-input" value="2" step="any" oninput="updateEquation()">
                    </div>
                </div>
                
                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Solve Equation</button>
                <div class="result-box" id="resultBox" style="display:none;">
                    <div class="mb-2">Roots</div>
                    <div class="result-value" id="rootsValue">x₁ = 1, x₂ = 2</div>
                    <div id="discriminant" class="mt-2 text-white-50"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function updateEquation() {
            const a = document.getElementById('input_a').value || 'a';
            const b = document.getElementById('input_b').value || 'b';
            const c = document.getElementById('input_c').value || 'c';
            
            document.getElementById('disp_a').textContent = a;
            document.getElementById('disp_b').textContent = b >= 0 ? `+ ${b}` : `- ${Math.abs(b)}`;
            document.getElementById('disp_c').textContent = c >= 0 ? `+ ${c}` : `- ${Math.abs(c)}`;
        }

        function calculate() {
            const a = parseFloat(document.getElementById('input_a').value);
            const b = parseFloat(document.getElementById('input_b').value);
            const c = parseFloat(document.getElementById('input_c').value);

            if (isNaN(a) || isNaN(b) || isNaN(c)) return;
            if (a === 0) {
                alert("Coefficient 'a' cannot be zero for a quadratic equation.");
                return;
            }

            const discriminant = (b * b) - (4 * a * c);
            let resultText = '';
            let discText = `Discriminant (Δ) = ${discriminant.toFixed(2)}`;

            if (discriminant > 0) {
                const x1 = (-b + Math.sqrt(discriminant)) / (2 * a);
                const x2 = (-b - Math.sqrt(discriminant)) / (2 * a);
                resultText = `x₁ = ${x1.toFixed(4)}<br>x₂ = ${x2.toFixed(4)}`;
                discText += " (Two Real Roots)";
            } else if (discriminant === 0) {
                const x = -b / (2 * a);
                resultText = `x = ${x.toFixed(4)}`;
                discText += " (One Real Root)";
            } else {
                const realPart = (-b / (2 * a)).toFixed(4);
                const imagPart = (Math.sqrt(Math.abs(discriminant)) / (2 * a)).toFixed(4);
                resultText = `x₁ = ${realPart} + ${imagPart}i<br>x₂ = ${realPart} - ${imagPart}i`;
                discText += " (Complex Roots)";
            }
            
            document.getElementById('resultBox').style.display = 'block';
            document.getElementById('rootsValue').innerHTML = resultText;
            document.getElementById('discriminant').textContent = discText;
        }
        
        // Init display
        updateEquation();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
