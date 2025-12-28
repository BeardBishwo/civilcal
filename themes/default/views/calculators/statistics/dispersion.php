<?php $page_title = $title ?? 'Standard Deviation Calculator'; ?>
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
        .calc-header { background: linear-gradient(135deg, #FF6B6B 0%, #556270 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #FF6B6B 0%, #556270 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #fbecec; border-radius: 12px; padding: 25px; margin-top: 30px; border: 1px solid #e6b0aa; }
        .stat-item { padding: 10px; border-bottom: 1px dashed #e6b0aa; display: flex; justify-content: space-between; }
        .stat-item:last-child { border: none; }
        .stat-label { font-weight: 600; color: #555; }
        .stat-val { font-weight: 700; color: #c0392b; font-size: 1.2rem; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: var(--text-primary); text-decoration: none; font-weight: 600; }
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
                        <i class="bi bi-graph-up" style="font-size: 2.5rem;"></i>
                        <h2>Dispersion Calculator</h2>
                        <p class="mb-0 mt-2">Standard Deviation & Variance</p>
                    </div>
                    <div class="calc-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Enter Data Set</label>
                            <textarea id="dataset" class="form-control calc-input" rows="3" placeholder="e.g. 10, 12, 23, 23, 16, 23, 21, 16"></textarea>
                            <div class="form-text">Split numbers by comma, space, or new line.</div>
                        </div>

                        <div class="form-check mb-4 text-center">
                            <input class="form-check-input float-none me-2" type="checkbox" id="isSample" checked>
                            <label class="form-check-label fw-bold" for="isSample">
                                Calculate as Sample (divide by N-1)
                            </label>
                        </div>

                        <button class="calc-btn w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate Dispersion</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <h5 class="text-center mb-3 fw-bold text-muted">Results</h5>
                             <div class="stat-item"><span class="stat-label">Count (N)</span><span class="stat-val" id="res_n">0</span></div>
                             <div class="stat-item"><span class="stat-label">Mean</span><span class="stat-val" id="res_mean">0</span></div>
                             <div class="stat-item"><span class="stat-label">Standard Deviation (σ/s)</span><span class="stat-val" id="res_sd">0</span></div>
                             <div class="stat-item"><span class="stat-label">Variance (σ²/s²)</span><span class="stat-val" id="res_var">0</span></div>
                             <div class="stat-item"><span class="stat-label">Coef. of Variation (CV)</span><span class="stat-val" id="res_cv">0%</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function calculate() {
            const raw = document.getElementById('dataset').value;
             const isSample = document.getElementById('isSample').checked;
             
            const nums = raw.split(/[\s,]+/)
                            .map(n => parseFloat(n))
                            .filter(n => !isNaN(n));
            
            if (nums.length < 2) {
                alert("Please enter at least 2 valid numbers.");
                return;
            }

            const n = nums.length;
            const sum = nums.reduce((a,b) => a + b, 0);
            const mean = sum / n;
            
            // Variance
            let sumSqDiff = 0;
            nums.forEach(num => {
                sumSqDiff += Math.pow(num - mean, 2);
            });
            
            const divisor = isSample ? (n - 1) : n;
            const variance = sumSqDiff / divisor;
            const sd = Math.sqrt(variance);
            const cv = (sd / mean) * 100;

            document.getElementById('res_n').textContent = n;
            document.getElementById('res_mean').textContent = mean.toFixed(4).replace(/\.?0+$/, "");
            document.getElementById('res_sd').textContent = sd.toFixed(4).replace(/\.?0+$/, "");
            document.getElementById('res_var').textContent = variance.toFixed(4).replace(/\.?0+$/, "");
            document.getElementById('res_cv').textContent = cv.toFixed(2).replace(/\.?0+$/, "") + '%';
            
            document.getElementById('resultBox').style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
