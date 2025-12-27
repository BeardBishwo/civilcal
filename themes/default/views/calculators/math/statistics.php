<?php $page_title = $title ?? 'Statistics Calculator'; ?>
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
        body { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 100vh; padding: 40px 0; }
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 800px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.3rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; min-height: 150px; }
        .calc-btn { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 12px; padding: 30px; margin-top: 30px; }
        .stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .stat-item { text-align: center; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 8px; }
        .stat-value { font-size: 2rem; font-weight: 700; }
        .stat-label { font-size: 0.9rem; opacity: 0.9; margin-top: 5px; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-bar-chart-line" style="font-size: 2.5rem;"></i>
                <h2>Statistics Calculator</h2>
                <p class="mb-0 mt-2">Calculate mean, median, std dev & more</p>
            </div>
            <div class="calc-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Enter Numbers (comma-separated)</label>
                    <textarea id="numbers" class="form-control calc-input" placeholder="e.g., 10, 20, 30, 40, 50">10, 20, 30, 40, 50</textarea>
                    <small class="text-muted">Enter numbers separated by commas</small>
                </div>
                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate Statistics</button>
                <div class="result-box" id="resultBox" style="display:none;">
                    <div class="stat-grid">
                        <div class="stat-item">
                            <div class="stat-value" id="count">0</div>
                            <div class="stat-label">Count</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="sum">0</div>
                            <div class="stat-label">Sum</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="mean">0</div>
                            <div class="stat-label">Mean</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="median">0</div>
                            <div class="stat-label">Median</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="min">0</div>
                            <div class="stat-label">Minimum</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="max">0</div>
                            <div class="stat-label">Maximum</div>
                        </div>
                        <div class="stat-item" style="grid-column: span 2;">
                            <div class="stat-value" id="stdDev">0</div>
                            <div class="stat-label">Standard Deviation</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const appBase = "<?php echo rtrim(app_base_url(), '/'); ?>";
        
        function calculate() {
            const input = document.getElementById('numbers').value;
            const numbers = input.split(',').map(n => parseFloat(n.trim())).filter(n => !isNaN(n));
            
            if (numbers.length === 0) {
                alert('Please enter valid numbers');
                return;
            }
            
            fetch(appBase + '/calculator/api/statistics', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `numbers=${JSON.stringify(numbers)}`
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('resultBox').style.display = 'block';
                document.getElementById('count').textContent = data.count;
                document.getElementById('sum').textContent = data.sum;
                document.getElementById('mean').textContent = data.mean;
                document.getElementById('median').textContent = data.median;
                document.getElementById('min').textContent = data.min;
                document.getElementById('max').textContent = data.max;
                document.getElementById('stdDev').textContent = data.stdDev;
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
