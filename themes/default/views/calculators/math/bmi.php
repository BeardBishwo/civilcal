<?php $page_title = $title ?? 'BMI Calculator'; ?>
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
        body { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); min-height: 100vh; padding: 40px 0; }
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 700px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.3rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; }
        .calc-btn { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .result-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 30px; text-align: center; margin-top: 30px; }
        .result-value { font-size: 3rem; font-weight: 700; margin: 10px 0; }
        .bmi-category { font-size: 1.5rem; font-weight: 600; margin-top: 10px; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-heart-pulse" style="font-size: 2.5rem;"></i>
                <h2>BMI Calculator</h2>
                <p class="mb-0 mt-2">Body Mass Index Calculator</p>
            </div>
            <div class="calc-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Unit System</label>
                    <select id="unitSystem" class="form-select calc-input" onchange="toggleUnits()">
                        <option value="metric">Metric (kg, cm)</option>
                        <option value="imperial">Imperial (lb, in)</option>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Weight <span id="weightUnit">(kg)</span></label>
                        <input type="number" id="weight" class="form-control calc-input" value="70" step="any">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Height <span id="heightUnit">(cm)</span></label>
                        <input type="number" id="height" class="form-control calc-input" value="170" step="any">
                    </div>
                </div>
                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate BMI</button>
                <div class="result-box" id="resultBox" style="display:none;">
                    <div>Your BMI</div>
                    <div class="result-value" id="bmiValue">0</div>
                    <div class="bmi-category" id="bmiCategory"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const appBase = "<?php echo rtrim(app_base_url(), '/'); ?>";
        function toggleUnits() {
            const unit = document.getElementById('unitSystem').value;
            if (unit === 'imperial') {
                document.getElementById('weightUnit').textContent = '(lb)';
                document.getElementById('heightUnit').textContent = '(in)';
                document.getElementById('weight').value = '154';
                document.getElementById('height').value = '67';
            } else {
                document.getElementById('weightUnit').textContent = '(kg)';
                document.getElementById('heightUnit').textContent = '(cm)';
                document.getElementById('weight').value = '70';
                document.getElementById('height').value = '170';
            }
        }
        function calculate() {
            const weight = document.getElementById('weight').value;
            const height = document.getElementById('height').value;
            const unit = document.getElementById('unitSystem').value;
            fetch(appBase + '/calculator/api/bmi', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `weight=${weight}&height=${height}&unit=${unit}`
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('resultBox').style.display = 'block';
                document.getElementById('bmiValue').textContent = data.bmi;
                document.getElementById('bmiCategory').textContent = data.category;
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
