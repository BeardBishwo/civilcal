<?php $page_title = $title ?? 'Body Fat Calculator'; ?>
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
        .calc-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #e8f5e9; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; border: 1px solid #c8e6c9; }
        .result-val { font-size: 2.5rem; font-weight: 700; color: #2e7d32; margin: 10px 0; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: var(--text-primary); text-decoration: none; font-weight: 600; }
        .gender-select .btn-check:checked + .btn { background-color: #2e7d32; border-color: #2e7d32; color: white; }
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
                        <i class="bi bi-person-check" style="font-size: 2.5rem;"></i>
                        <h2>Body Fat Calculator</h2>
                        <p class="mb-0 mt-2">US Navy Method</p>
                    </div>
                    <div class="calc-body">
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-12 text-center gender-select">
                                <label class="form-label fw-bold d-block mb-2">Gender</label>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="gender" id="male" checked onchange="toggleGender()">
                                    <label class="btn btn-outline-success px-4" for="male">Male</label>
                                    <input type="radio" class="btn-check" name="gender" id="female" onchange="toggleGender()">
                                    <label class="btn btn-outline-success px-4" for="female">Female</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Height</label>
                                <div class="input-group">
                                    <input type="number" id="height" class="form-control calc-input" value="175">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Neck Circumference</label>
                                <div class="input-group">
                                    <input type="number" id="neck" class="form-control calc-input" value="38">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Waist Circumference</label>
                                <div class="input-group">
                                    <input type="number" id="waist" class="form-control calc-input" value="85">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <div class="col-md-6" id="hip_con" style="display:none;">
                                <label class="form-label fw-bold text-dark">Hip Circumference</label>
                                <div class="input-group">
                                    <input type="number" id="hip" class="form-control calc-input" value="95">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>

                        <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate Body Fat</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-muted text-uppercase fw-bold small">Estimated Body Fat</div>
                            <div class="result-val" id="bfValue">15%</div>
                            <div class="fw-bold fs-6 text-muted" id="bfCategory">Fitness</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function toggleGender() {
            const isFemale = document.getElementById('female').checked;
            document.getElementById('hip_con').style.display = isFemale ? 'block' : 'none';
        }

        function calculate() {
            const isMale = document.getElementById('male').checked;
            const height = parseFloat(document.getElementById('height').value);
            const neck = parseFloat(document.getElementById('neck').value);
            const waist = parseFloat(document.getElementById('waist').value);
            const hip = parseFloat(document.getElementById('hip').value);

            if (!height || !neck || !waist) return;
            if (!isMale && !hip) return;

            let bf = 0;

            if (isMale) {
                // US Navy Method Male
                bf = 495 / (1.0324 - 0.19077 * Math.log10(waist - neck) + 0.15456 * Math.log10(height)) - 450;
            } else {
                // US Navy Method Female
                bf = 495 / (1.29579 - 0.35004 * Math.log10(waist + hip - neck) + 0.22100 * Math.log10(height)) - 450;
            }

            if (bf < 0) bf = 0; // Edge case
            
            document.getElementById('bfValue').textContent = bf.toFixed(1) + '%';
            
            let cat = '';
            if (isMale) {
                if (bf < 6) cat = 'Essential Fat';
                else if (bf < 14) cat = 'Athletes';
                else if (bf < 18) cat = 'Fitness';
                else if (bf < 25) cat = 'Average';
                else cat = 'Obese';
            } else {
                if (bf < 14) cat = 'Essential Fat';
                else if (bf < 21) cat = 'Athletes';
                else if (bf < 25) cat = 'Fitness';
                else if (bf < 32) cat = 'Average';
                else cat = 'Obese';
            }
            
            document.getElementById('bfCategory').textContent = cat;
            document.getElementById('resultBox').style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
