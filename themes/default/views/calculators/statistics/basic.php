<?php $page_title = $title ?? 'Basic Statistics Calculator'; ?>
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
        .calc-header { background: linear-gradient(135deg, #1abc9c 0%, #2980b9 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #1abc9c 0%, #2980b9 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #e0f2f1; border-radius: 12px; padding: 25px; margin-top: 30px; border: 1px solid #b2dfdb; }
        .stat-item { padding: 10px; border-bottom: 1px dashed #b2dfdb; display: flex; justify-content: space-between; }
        .stat-item:last-child { border: none; }
        .stat-label { font-weight: 600; color: #555; }
        .stat-val { font-weight: 700; color: #16a085; font-size: 1.2rem; }
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
                        <i class="bi bi-bar-chart-steps" style="font-size: 2.5rem;"></i>
                        <h2>Basic Statistics</h2>
                        <p class="mb-0 mt-2">Mean, Median, Mode, Range</p>
                    </div>
                    <div class="calc-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Enter Data Set</label>
                            <textarea id="dataset" class="form-control calc-input" rows="3" placeholder="e.g. 12, 5, 8, 12, 20, 5, 8"></textarea>
                            <div class="form-text">Split numbers by comma, space, or new line.</div>
                        </div>

                        <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate Statistics</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="stat-item"><span class="stat-label">Count (N)</span><span class="stat-val" id="res_n">0</span></div>
                                    <div class="stat-item"><span class="stat-label">Sum</span><span class="stat-val" id="res_sum">0</span></div>
                                    <div class="stat-item"><span class="stat-label">Min</span><span class="stat-val" id="res_min">0</span></div>
                                    <div class="stat-item"><span class="stat-label">Max</span><span class="stat-val" id="res_max">0</span></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="stat-item"><span class="stat-label">Mean (Average)</span><span class="stat-val" id="res_mean">0</span></div>
                                    <div class="stat-item"><span class="stat-label">Median</span><span class="stat-val" id="res_median">0</span></div>
                                    <div class="stat-item"><span class="stat-label">Mode</span><span class="stat-val" id="res_mode">0</span></div>
                                    <div class="stat-item"><span class="stat-label">Range</span><span class="stat-val" id="res_range">0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function calculate() {
            const raw = document.getElementById('dataset').value;
            // Split by comma, space, newline, tab
            const nums = raw.split(/[\s,]+/)
                            .map(n => parseFloat(n))
                            .filter(n => !isNaN(n))
                            .sort((a,b) => a - b);
            
            if (nums.length === 0) {
                alert("Please enter valid numbers.");
                return;
            }

            const n = nums.length;
            const sum = nums.reduce((a,b) => a + b, 0);
            const mean = sum / n;
            const min = nums[0];
            const max = nums[n-1];
            const range = max - min;
            
            // Median
            let median = 0;
            if (n % 2 === 0) {
                median = (nums[n/2 - 1] + nums[n/2]) / 2;
            } else {
                median = nums[Math.floor(n/2)];
            }

            // Mode
            const freq = {};
            let maxFreq = 0;
            nums.forEach(num => {
                freq[num] = (freq[num] || 0) + 1;
                if (freq[num] > maxFreq) maxFreq = freq[num];
            });
            
            let modes = [];
            if (maxFreq > 1) {
                for (const k in freq) {
                    if (freq[k] === maxFreq) modes.push(k);
                }
            }

            document.getElementById('res_n').textContent = n;
            document.getElementById('res_sum').textContent = sum;
            document.getElementById('res_min').textContent = min;
            document.getElementById('res_max').textContent = max;
            document.getElementById('res_mean').textContent = mean.toFixed(4).replace(/\.?0+$/, "");
            document.getElementById('res_median').textContent = median;
            document.getElementById('res_mode').textContent = modes.length > 0 ? modes.join(', ') : 'None';
            document.getElementById('res_range').textContent = range;
            
            document.getElementById('resultBox').style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
