<?php $page_title = $title ?? 'Body Fat Calculator'; ?>
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
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 700px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.3rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; }
        .calc-btn { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
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
                <i class="bi bi-person" style="font-size: 2.5rem;"></i>
                <h2>Body Fat Calculator</h2>
                <p class="mb-0 mt-2">US Navy Method</p>
            </div>
            <div class="calc-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Gender</label>
                    <select id="gender" class="form-select calc-input" onchange="toggleFields()">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Height (cm)</label>
                        <input type="number" id="height" class="form-control calc-input" value="170" step="any">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Neck (cm)</label>
                        <input type="number" id="neck" class="form-control calc-input" value="37" step="any">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Waist (cm)</label>
                        <input type="number" id="waist" class="form-control calc-input" value="85" step="any">
                    </div>
                    <div class="col-md-6" id="hipField" style="display:none;">
                        <label class="form-label fw-bold">Hip (cm)</label>
                        <input type="number" id="hip" class="form-control calc-input" value="95" step="any">
                    </div>
                </div>
                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate</button>
                <div class="result-box" id="resultBox" style="display:none;">
                    <div>Body Fat Percentage</div>
                    <div class="result-value"><span id="bodyFat">0</span>%</div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const appBase = "<?php echo rtrim(app_base_url(), '/'); ?>";
        function toggleFields() {
            const gender = document.getElementById('gender').value;
            document.getElementById('hipField').style.display = gender === 'female' ? 'block' : 'none';
        }
        function calculate() {
            const gender = document.getElementById('gender').value;
            const height = document.getElementById('height').value;
            const neck = document.getElementById('neck').value;
            const waist = document.getElementById('waist').value;
            const hip = document.getElementById('hip').value;
            fetch(appBase + '/calculator/api/body-fat', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `gender=${gender}&height=${height}&neck=${neck}&waist=${waist}&hip=${hip}`
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('resultBox').style.display = 'block';
                document.getElementById('bodyFat').textContent = data.bodyFat.toFixed(1);
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
