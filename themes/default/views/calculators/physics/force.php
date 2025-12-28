<?php $page_title = $title ?? 'Force Calculator'; ?>
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
        .calc-header { background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #f3e5f5; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; border: 1px solid #e1bee7; }
        .result-val { font-size: 2.5rem; font-weight: 700; color: #8e44ad; margin: 10px 0; }
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
                        <i class="bi bi-rocket-takeoff" style="font-size: 2.5rem;"></i>
                        <h2>Force Calculator</h2>
                        <p class="mb-0 mt-2">Newton's Second Law (F = m × a)</p>
                    </div>
                    <div class="calc-body">
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold">I want to calculate:</label>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="target" id="calc_F" checked onchange="toggleInputs()">
                                <label class="btn btn-outline-primary" for="calc_F">Force (F)</label>
                                <input type="radio" class="btn-check" name="target" id="calc_m" onchange="toggleInputs()">
                                <label class="btn btn-outline-primary" for="calc_m">Mass (m)</label>
                                <input type="radio" class="btn-check" name="target" id="calc_a" onchange="toggleInputs()">
                                <label class="btn btn-outline-primary" for="calc_a">Acceleration (a)</label>
                            </div>
                        </div>

                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6" id="grp_F" style="display:none;">
                                <label class="form-label fw-bold">Force (F)</label>
                                <div class="input-group">
                                    <input type="number" id="val_F" class="form-control calc-input">
                                    <span class="input-group-text">N</span>
                                </div>
                            </div>
                            <div class="col-md-6" id="grp_m">
                                <label class="form-label fw-bold">Mass (m)</label>
                                <div class="input-group">
                                    <input type="number" id="val_m" class="form-control calc-input" value="10">
                                    <span class="input-group-text">kg</span>
                                </div>
                            </div>
                            <div class="col-md-6" id="grp_a">
                                <label class="form-label fw-bold">Acceleration (a)</label>
                                <div class="input-group">
                                    <input type="number" id="val_a" class="form-control calc-input" value="9.8">
                                    <span class="input-group-text">m/s²</span>
                                </div>
                            </div>
                        </div>

                        <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-muted text-uppercase fw-bold small">Result</div>
                            <div class="result-val" id="resultValue">0</div>
                            <div class="fw-bold fs-6 text-muted" id="resultUnit">N</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function toggleInputs() {
            const target = document.querySelector('input[name="target"]:checked').id;
            document.getElementById('grp_F').style.display = target === 'calc_F' ? 'none' : 'block';
            document.getElementById('grp_m').style.display = target === 'calc_m' ? 'none' : 'block';
            document.getElementById('grp_a').style.display = target === 'calc_a' ? 'none' : 'block';
            document.getElementById('resultBox').style.display = 'none';
        }

        function calculate() {
            const target = document.querySelector('input[name="target"]:checked').id;
            const F = parseFloat(document.getElementById('val_F').value);
            const m = parseFloat(document.getElementById('val_m').value);
            const a = parseFloat(document.getElementById('val_a').value);
            
            let result = 0;
            let unit = '';

            if (target === 'calc_F') {
                if (!m || !a) return;
                result = m * a;
                unit = 'N';
            } else if (target === 'calc_m') {
                if (!F || !a) return;
                result = F / a;
                unit = 'kg';
            } else if (target === 'calc_a') {
                if (!F || !m) return;
                result = F / m;
                unit = 'm/s²';
            }

            document.getElementById('resultValue').textContent = Number.isInteger(result) ? result : result.toFixed(2);
            document.getElementById('resultUnit').textContent = unit;
            document.getElementById('resultBox').style.display = 'block';
        }
        
        // Init
        toggleInputs();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
