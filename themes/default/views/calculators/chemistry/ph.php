<?php $page_title = $title ?? 'pH Calculator'; ?>
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
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 700px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #00bfa5 0%, #1de9b6 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; text-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #00bfa5 0%, #1de9b6 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #e0f2f1; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; border: 1px solid #b2dfdb; }
        .result-val { font-size: 2.5rem; font-weight: 700; color: #00695c; margin: 10px 0; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: var(--text-primary); text-decoration: none; font-weight: 600; }
        .ph-scale { height: 20px; background: linear-gradient(to right, #e74c3c 0%, #f1c40f 35%, #2ecc71 50%, #3498db 65%, #9b59b6 100%); border-radius: 10px; position: relative; margin-top: 20px; }
        .ph-marker { position: absolute; top: -5px; width: 4px; height: 30px; background: #000; transition: left 0.5s; }
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
                        <i class="bi bi-droplet-half" style="font-size: 2.5rem;"></i>
                        <h2>pH Calculator</h2>
                        <p class="mb-0 mt-2">Hydrogen Ion Concentration</p>
                    </div>
                    <div class="calc-body">
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Concentration of H+ (mol/L)</label>
                                <div class="input-group">
                                    <input type="number" id="concentration" class="form-control calc-input" placeholder="e.g. 0.001" step="any" value="0.0000001">
                                    <span class="input-group-text">M</span>
                                </div>
                                <div class="form-text">Enter in decimal (0.01) or scientific notation (1e-7)</div>
                            </div>
                        </div>

                        <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate pH</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-muted text-uppercase fw-bold small">pH Level</div>
                            <div class="result-val" id="resultValue">7.00</div>
                            <div class="fw-bold fs-5" id="phType" style="color: #2ecc71;">Neutral</div>
                            
                            <div class="ph-scale">
                                <div class="ph-marker" id="marker" style="left: 50%;"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-1 fw-bold">
                                <span>0 (Acidic)</span>
                                <span>7</span>
                                <span>14 (Basic)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function calculate() {
            const h = parseFloat(document.getElementById('concentration').value);
            
            if (!h || h <= 0) {
                alert("Concentration must be greater than 0");
                return;
            }

            const ph = -Math.log10(h);
            
            if (ph < 0 || ph > 14) {
                 // Technically possible but rare in standard conditions for this basic calc
            }

            document.getElementById('resultValue').textContent = ph.toFixed(2);
            
            let type = '';
            let color = '';
            
            if (ph < 6.9) { type = 'Acidic'; color = '#e74c3c'; }
            else if (ph > 7.1) { type = 'Basic (Alkaline)'; color = '#9b59b6'; }
            else { type = 'Neutral'; color = '#2ecc71'; }
            
            const typeEl = document.getElementById('phType');
            typeEl.textContent = type;
            typeEl.style.color = color;
            
            // Marker position (0-14 map to 0-100%)
            let pos = (ph / 14) * 100;
            if (pos < 0) pos = 0; if (pos > 100) pos = 100;
            
            document.getElementById('marker').style.left = pos + '%';
            document.getElementById('resultBox').style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
