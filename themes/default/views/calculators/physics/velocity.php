<?php $page_title = $title ?? 'Velocity Calculator'; ?>
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
        .calc-header { background: linear-gradient(135deg, #2980b9 0%, #2c3e50 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #2980b9 0%, #2c3e50 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #ecf0f1; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; border: 1px solid #bdc3c7; }
        .result-val { font-size: 2.5rem; font-weight: 700; color: #2c3e50; margin: 10px 0; }
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
                        <i class="bi bi-speedometer2" style="font-size: 2.5rem;"></i>
                        <h2>Velocity Calculator</h2>
                        <p class="mb-0 mt-2">Calculate Speed, Distance, or Time (v = d / t)</p>
                    </div>
                    <div class="calc-body">
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold">I want to calculate:</label>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="target" id="calc_v" checked onchange="toggleInputs()">
                                <label class="btn btn-outline-primary" for="calc_v">Velocity (v)</label>
                                <input type="radio" class="btn-check" name="target" id="calc_d" onchange="toggleInputs()">
                                <label class="btn btn-outline-primary" for="calc_d">Distance (d)</label>
                                <input type="radio" class="btn-check" name="target" id="calc_t" onchange="toggleInputs()">
                                <label class="btn btn-outline-primary" for="calc_t">Time (t)</label>
                            </div>
                        </div>

                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6" id="grp_v" style="display:none;">
                                <label class="form-label fw-bold">Velocity (v)</label>
                                <div class="input-group">
                                    <input type="number" id="val_v" class="form-control calc-input">
                                    <span class="input-group-text">m/s</span>
                                </div>
                            </div>
                            <div class="col-md-6" id="grp_d">
                                <label class="form-label fw-bold">Distance (d)</label>
                                <div class="input-group">
                                    <input type="number" id="val_d" class="form-control calc-input" value="100">
                                    <span class="input-group-text">m</span>
                                </div>
                            </div>
                            <div class="col-md-6" id="grp_t">
                                <label class="form-label fw-bold">Time (t)</label>
                                <div class="input-group">
                                    <input type="number" id="val_t" class="form-control calc-input" value="10">
                                    <span class="input-group-text">s</span>
                                </div>
                            </div>
                        </div>

                        <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-muted text-uppercase fw-bold small">Result</div>
                            <div class="result-val" id="resultValue">0</div>
                            <div class="fw-bold fs-6 text-muted" id="resultUnit">m/s</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function toggleInputs() {
            const target = document.querySelector('input[name="target"]:checked').id;
            document.getElementById('grp_v').style.display = target === 'calc_v' ? 'none' : 'block';
            document.getElementById('grp_d').style.display = target === 'calc_d' ? 'none' : 'block';
            document.getElementById('grp_t').style.display = target === 'calc_t' ? 'none' : 'block';
            document.getElementById('resultBox').style.display = 'none';
        }

        function calculate() {
            const target = document.querySelector('input[name="target"]:checked').id;
            const v = parseFloat(document.getElementById('val_v').value);
            const d = parseFloat(document.getElementById('val_d').value);
            const t = parseFloat(document.getElementById('val_t').value);
            
            let result = 0;
            let unit = '';

            if (target === 'calc_v') {
                if (!d || !t) return;
                result = d / t;
                unit = 'm/s';
            } else if (target === 'calc_d') {
                if (!v || !t) return;
                result = v * t;
                unit = 'm';
            } else if (target === 'calc_t') {
                if (!d || !v) return;
                result = d / v;
                unit = 's';
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
