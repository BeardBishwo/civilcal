<?php $page_title = $title ?? 'Linear Equations Solver'; ?>
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
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 10px; text-align: center; width: 80px; display: inline-block;}
        .calc-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 12px; padding: 30px; text-align: center; margin-top: 30px; }
        .result-value { font-size: 1.5rem; font-weight: 700; margin: 10px 0; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
        .equation-row { font-size: 1.5rem; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; gap: 10px;}
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-list-ol" style="font-size: 2.5rem;"></i>
                <h2>Linear Equations Solver</h2>
                <p class="mb-0 mt-2">Solve system of two linear equations</p>
            </div>
            <div class="calc-body">
                <div class="text-center mb-4 text-muted">Enter coefficients for: ax + by = c</div>
                
                <div class="equation-row">
                    <input type="number" id="a1" class="form-control calc-input" paceholder="a1" value="2"> x + 
                    <input type="number" id="b1" class="form-control calc-input" placeholder="b1" value="3"> y = 
                    <input type="number" id="c1" class="form-control calc-input" placeholder="c1" value="13">
                </div>
                
                <div class="equation-row">
                    <input type="number" id="a2" class="form-control calc-input" placeholder="a2" value="5"> x + 
                    <input type="number" id="b2" class="form-control calc-input" placeholder="b2" value="-1"> y = 
                    <input type="number" id="c2" class="form-control calc-input" placeholder="c2" value="7">
                </div>

                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Solve System</button>
                <div class="result-box" id="resultBox" style="display:none;">
                    <div class="mb-2">Solution</div>
                    <div class="result-value" id="rootsValue">x = 2, y = 3</div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function calculate() {
            const a1 = parseFloat(document.getElementById('a1').value);
            const b1 = parseFloat(document.getElementById('b1').value);
            const c1 = parseFloat(document.getElementById('c1').value);
            
            const a2 = parseFloat(document.getElementById('a2').value);
            const b2 = parseFloat(document.getElementById('b2').value);
            const c2 = parseFloat(document.getElementById('c2').value);

            if (isNaN(a1) || isNaN(b1) || isNaN(c1) || isNaN(a2) || isNaN(b2) || isNaN(c2)) return;

            // Using Cramer's Rule
            const D = (a1 * b2) - (a2 * b1);
            const Dx = (c1 * b2) - (c2 * b1);
            const Dy = (a1 * c2) - (a2 * c1);

            let resultText = '';

            if (D !== 0) {
                const x = Dx / D;
                const y = Dy / D;
                resultText = `x = ${x.toFixed(4)}<br>y = ${y.toFixed(4)}`;
            } else {
                if (Dx === 0 && Dy === 0) {
                    resultText = "Infinite Solutions (Dependent System)";
                } else {
                    resultText = "No Solution (Inconsistent System)";
                }
            }
            
            document.getElementById('resultBox').style.display = 'block';
            document.getElementById('rootsValue').innerHTML = resultText;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
