<?php $page_title = $title ?? 'Probability Calculator'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo app_base_url('/themes/default/assets/css/theme.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo app_base_url('/themes/default/assets/css/calculator-platform.css'); ?>?v=<?php echo time(); ?>">
    <style>
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 800px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #a8c0ff 0%, #3f2b96 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #a8c0ff 0%, #3f2b96 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #e8eaf6; border-radius: 12px; padding: 25px; margin-top: 30px; border: 1px solid #c5cae9; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: var(--text-primary); text-decoration: none; font-weight: 600; }
        .formula-box { background: #f5f5f5; padding: 10px; border-radius: 8px; text-align: center; font-family: monospace; font-size: 1.1em; color: #555; }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include __DIR__ . '/../../partials/calculator_sidebar.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid">
                <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Dashboard</a>
                <div class="calc-card">
                    <div class="calc-header">
                        <i class="bi bi-dice-5" style="font-size: 2.5rem;"></i>
                        <h2>Probability Calculator</h2>
                        <p class="mb-0 mt-2">Permutations & Combinations</p>
                    </div>
                    <div class="calc-body">
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">n (Total items)</label>
                                <input type="number" id="val_n" class="form-control calc-input" value="10" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">r (Selected items)</label>
                                <input type="number" id="val_r" class="form-control calc-input" value="3" min="0">
                            </div>
                        </div>

                        <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="row text-center">
                                <div class="col-md-6 mb-3">
                                    <h5 class="fw-bold text-dark mb-3">Permutations (Order matters)</h5>
                                    <div class="formula-box mb-2">nPr = n! / (n-r)!</div>
                                    <div class="display-4 fw-bold text-primary" id="res_p">0</div>
                                    <div class="small text-muted mt-2">Ways to arrange r items from n</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h5 class="fw-bold text-dark mb-3">Combinations (Order doesn't matter)</h5>
                                    <div class="formula-box mb-2">nCr = n! / [r!(n-r)!]</div>
                                    <div class="display-4 fw-bold text-success" id="res_c">0</div>
                                    <div class="small text-muted mt-2">Ways to choose r items from n</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function factorial(num) {
            if (num < 0) return -1;
            if (num === 0) return 1;
            let res = 1;
            for (let i = 2; i <= num; i++) res *= i;
            return res;
        }

        function calculate() {
            const n = parseInt(document.getElementById('val_n').value);
            const r = parseInt(document.getElementById('val_r').value);
            
            if (isNaN(n) || isNaN(r) || n < 0 || r < 0) {
                alert("Please enter valid positive integers.");
                return;
            }
            if (r > n) {
                alert("r cannot be greater than n");
                return;
            }

            // Permutations P(n,r)
            const p = factorial(n) / factorial(n - r);
            
            // Combinations C(n,r)
            const c = factorial(n) / (factorial(r) * factorial(n - r));

            document.getElementById('res_p').textContent = p.toLocaleString();
            document.getElementById('res_c').textContent = c.toLocaleString();
            
            document.getElementById('resultBox').style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
