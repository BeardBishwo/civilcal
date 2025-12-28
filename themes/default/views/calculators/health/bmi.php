<?php $page_title = $title ?? 'BMI Calculator'; ?>
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
        /* Custom Calculator Styles Overlay */
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 800px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #12c2e9 0%, #c471ed 50%, #f64f59 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #f64f59 0%, #c471ed 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #f8f9fa; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; }
        .result-val { font-size: 2.5rem; font-weight: 700; color: #f64f59; margin: 10px 0; }
        .bmi-scale { height: 10px; background: linear-gradient(to right, #3498db 0 18.5%, #2ecc71 18.5% 25%, #f1c40f 25% 30%, #e74c3c 30% 100%); border-radius: 5px; margin-top: 15px; position: relative;}
        .bmi-marker { position: absolute; top: -5px; width: 4px; height: 20px; background: #000; transition: left 0.5s; }
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
                        <i class="bi bi-heart-pulse" style="font-size: 2.5rem;"></i>
                        <h2>BMI Calculator</h2>
                        <p class="mb-0 mt-2">Calculate Body Mass Index</p>
                    </div>
                    <div class="calc-body">
                        <div class="row g-3 justify-content-center mb-3">
                            <div class="col-md-6">
                                 <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="unit" id="metric" checked onchange="toggleUnit()">
                                    <label class="btn btn-outline-danger" for="metric">Metric (kg/cm)</label>
                                    <input type="radio" class="btn-check" name="unit" id="imperial" onchange="toggleUnit()">
                                    <label class="btn btn-outline-danger" for="imperial">Imperial (lbs/ft)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Height</label>
                                <div class="input-group" id="height_metric">
                                    <input type="number" id="height_cm" class="form-control calc-input" placeholder="cm" value="175">
                                    <span class="input-group-text">cm</span>
                                </div>
                                <div class="input-group" id="height_imperial" style="display:none;">
                                    <input type="number" id="height_ft" class="form-control calc-input" placeholder="ft" value="5">
                                    <span class="input-group-text">ft</span>
                                    <input type="number" id="height_in" class="form-control calc-input" placeholder="in" value="9">
                                    <span class="input-group-text">in</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Weight</label>
                                <div class="input-group">
                                    <input type="number" id="weight" class="form-control calc-input" value="70">
                                    <span class="input-group-text" id="weight_unit">kg</span>
                                </div>
                            </div>
                        </div>

                        <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate BMI</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-muted text-uppercase fw-bold small">Your BMI</div>
                            <div class="result-val" id="bmiValue">22.9</div>
                            <div class="fw-bold fs-5" id="bmiStatus" style="color: #2ecc71;">Normal Weight</div>
                            
                            <div class="bmi-scale mt-3">
                                <div class="bmi-marker" id="marker" style="left: 0;"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-1">
                                <span>18.5</span>
                                <span>25.0</span>
                                <span>30.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function toggleUnit() {
            const isMetric = document.getElementById('metric').checked;
            document.getElementById('height_metric').style.display = isMetric ? 'flex' : 'none';
            document.getElementById('height_imperial').style.display = isMetric ? 'none' : 'flex';
            document.getElementById('weight_unit').textContent = isMetric ? 'kg' : 'lbs';
        }

        function calculate() {
            const isMetric = document.getElementById('metric').checked;
            let weight = parseFloat(document.getElementById('weight').value);
            let height = 0;

            if (isMetric) {
                height = parseFloat(document.getElementById('height_cm').value) / 100; // cm to m
            } else {
                weight = weight * 0.453592; // lbs to kg
                const ft = parseFloat(document.getElementById('height_ft').value);
                const inch = parseFloat(document.getElementById('height_in').value);
                height = ((ft * 12) + inch) * 0.0254; // in to m
            }

            if (height <= 0 || weight <= 0) return;

            const bmi = weight / (height * height);
            const bmiFormatted = bmi.toFixed(1);

            let status = '';
            let color = '';
            let percent = 0;

            if (bmi < 18.5) {
                status = 'Underweight';
                color = '#3498db';
                percent = (bmi / 18.5) * 18.5; // Scale relative to 18.5% width approx
            } else if (bmi < 25) {
                status = 'Normal Weight';
                color = '#2ecc71';
                percent = 18.5 + ((bmi - 18.5) / 6.5) * 6.5; 
            } else if (bmi < 30) {
                status = 'Overweight';
                color = '#f1c40f';
            } else {
                status = 'Obese';
                color = '#e74c3c';
            }
            
            // Simplified marker position logic for visual
            // Map 0-40 BMI to 0-100% just for visualization
            let pos = (bmi / 40) * 100;
            if (pos > 100) pos = 100;
            if (pos < 0) pos = 0;

            document.getElementById('bmiValue').textContent = bmiFormatted;
            const statusEl = document.getElementById('bmiStatus');
            statusEl.textContent = status;
            statusEl.style.color = color;
            document.getElementById('marker').style.left = pos + '%';
            document.getElementById('resultBox').style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
