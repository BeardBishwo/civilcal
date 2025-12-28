<?php $page_title = $title ?? "Ohm's Law Calculator"; ?>
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
        .calc-header { background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; color: #2c3e50; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; color: #2c3e50; }
        .result-box { background: #fffde7; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; border: 1px solid #fff9c4; }
        .result-val { font-size: 2.5rem; font-weight: 700; color: #f39c12; margin: 10px 0; }
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
                        <i class="bi bi-lightning-charge" style="font-size: 2.5rem; color: #2c3e50;"></i>
                        <h2>Ohm's Law</h2>
                        <p class="mb-0 mt-2 text-dark">Calculate Voltage, Current, or Resistance (V = I × R)</p>
                    </div>
                    <div class="calc-body">
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold">I want to calculate:</label>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="target" id="calc_V" checked onchange="toggleInputs()">
                                <label class="btn btn-outline-warning text-dark" for="calc_V">Voltage (V)</label>
                                <input type="radio" class="btn-check" name="target" id="calc_I" onchange="toggleInputs()">
                                <label class="btn btn-outline-warning text-dark" for="calc_I">Current (I)</label>
                                <input type="radio" class="btn-check" name="target" id="calc_R" onchange="toggleInputs()">
                                <label class="btn btn-outline-warning text-dark" for="calc_R">Resistance (R)</label>
                            </div>
                        </div>

                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6" id="grp_V" style="display:none;">
                                <label class="form-label fw-bold">Voltage (V)</label>
                                <div class="input-group">
                                    <input type="number" id="val_V" class="form-control calc-input">
                                    <span class="input-group-text">V</span>
                                </div>
                            </div>
                            <div class="col-md-6" id="grp_I">
                                <label class="form-label fw-bold">Current (I)</label>
                                <div class="input-group">
                                    <input type="number" id="val_I" class="form-control calc-input" value="2">
                                    <span class="input-group-text">A</span>
                                </div>
                            </div>
                            <div class="col-md-6" id="grp_R">
                                <label class="form-label fw-bold">Resistance (R)</label>
                                <div class="input-group">
                                    <input type="number" id="val_R" class="form-control calc-input" value="10">
                                    <span class="input-group-text">Ω</span>
                                </div>
                            </div>
                        </div>

                        <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-muted text-uppercase fw-bold small">Result</div>
                            <div class="result-val" id="resultValue">0</div>
                            <div class="fw-bold fs-6 text-muted" id="resultUnit">V</div>
                            
                            <div class="mt-3 small text-muted">
                                Power (P) = <span id="res_power" class="fw-bold">0</span> W
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function toggleInputs() {
            const target = document.querySelector('input[name="target"]:checked').id;
            document.getElementById('grp_V').style.display = target === 'calc_V' ? 'none' : 'block';
            document.getElementById('grp_I').style.display = target === 'calc_I' ? 'none' : 'block';
            document.getElementById('grp_R').style.display = target === 'calc_R' ? 'none' : 'block';
            document.getElementById('resultBox').style.display = 'none';
        }

        function calculate() {
            const target = document.querySelector('input[name="target"]:checked').id;
            const V = parseFloat(document.getElementById('val_V').value);
            const I = parseFloat(document.getElementById('val_I').value);
            const R = parseFloat(document.getElementById('val_R').value);
            
            let result = 0;
            let unit = '';
            let power = 0;

            if (target === 'calc_V') {
                if (!I || !R) return;
                result = I * R;
                unit = 'V';
                power = I * result;
            } else if (target === 'calc_I') {
                if (!V || !R) return;
                result = V / R;
                unit = 'A';
                power = V * result;
            } else if (target === 'calc_R') {
                if (!V || !I) return;
                result = V / I;
                unit = 'Ω';
                power = V * I;
            }

            document.getElementById('resultValue').textContent = Number.isInteger(result) ? result : result.toFixed(2);
            document.getElementById('resultUnit').textContent = unit;
            document.getElementById('res_power').textContent = power.toFixed(2);
            document.getElementById('resultBox').style.display = 'block';
        }
        
        // Init
        toggleInputs();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
