<?php $page_title = $title ?? 'Energy Calculator'; ?>
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
        .calc-header { background: linear-gradient(135deg, #16a085 0%, #f4d03f 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #16a085 0%, #f4d03f 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
        .result-box { background: #e0f2f1; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; border: 1px solid #b2dfdb; }
        .result-val { font-size: 2.5rem; font-weight: 700; color: #16a085; margin: 10px 0; }
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
                        <i class="bi bi-battery-charging" style="font-size: 2.5rem;"></i>
                        <h2>Energy Calculator</h2>
                        <p class="mb-0 mt-2">Kinetic (KE) & Potential (PE) Energy</p>
                    </div>
                    <div class="calc-body">
                        <ul class="nav nav-pills nav-fill mb-4" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold" id="ke-tab" data-bs-toggle="pill" data-bs-target="#ke-content" type="button" role="tab">Kinetic Energy (Move)</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="pe-tab" data-bs-toggle="pill" data-bs-target="#pe-content" type="button" role="tab">Potential Energy (Height)</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="pills-tabContent">
                            <!-- Kinetic Energy -->
                            <div class="tab-pane fade show active" id="ke-content" role="tabpanel">
                                <div class="row g-4 justify-content-center">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Mass (m)</label>
                                        <div class="input-group">
                                            <input type="number" id="ke_m" class="form-control calc-input" value="10">
                                            <span class="input-group-text">kg</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Velocity (v)</label>
                                        <div class="input-group">
                                            <input type="number" id="ke_v" class="form-control calc-input" value="20">
                                            <span class="input-group-text">m/s</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="calc-btn mt-4 w-100" onclick="calculateKE()"><i class="bi bi-lightning-fill me-2"></i>Calculate KE</button>
                            </div>

                            <!-- Potential Energy -->
                            <div class="tab-pane fade" id="pe-content" role="tabpanel">
                                 <div class="row g-4 justify-content-center">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Mass (m)</label>
                                        <div class="input-group">
                                            <input type="number" id="pe_m" class="form-control calc-input" value="10">
                                            <span class="input-group-text">kg</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Height (h)</label>
                                        <div class="input-group">
                                            <input type="number" id="pe_h" class="form-control calc-input" value="50">
                                            <span class="input-group-text">m</span>
                                        </div>
                                    </div>
                                     <div class="col-md-12">
                                        <label class="form-label fw-bold text-muted small">Gravity (g) ≈ 9.8 m/s²</label>
                                    </div>
                                </div>
                                <button class="calc-btn mt-4 w-100" onclick="calculatePE()"><i class="bi bi-arrow-up-circle-fill me-2"></i>Calculate PE</button>
                            </div>
                        </div>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-muted text-uppercase fw-bold small" id="resLabel">Energy</div>
                            <div class="result-val" id="resultValue">0</div>
                            <div class="fw-bold fs-6 text-muted">Joules (J)</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function calculateKE() {
            const m = parseFloat(document.getElementById('ke_m').value);
            const v = parseFloat(document.getElementById('ke_v').value);
            
            if (!m || (!v && v!==0)) return;

            const ke = 0.5 * m * v * v;
            showResult('Kinetic Energy', ke);
        }

        function calculatePE() {
            const m = parseFloat(document.getElementById('pe_m').value);
            const h = parseFloat(document.getElementById('pe_h').value);
            const g = 9.8;
            
            if (!m || (!h && h!==0)) return;

            const pe = m * g * h;
            showResult('Potential Energy', pe);
        }

        function showResult(label, val) {
            document.getElementById('resLabel').textContent = label;
            document.getElementById('resultValue').textContent = val.toFixed(2).replace(/\.?0+$/, "");
            document.getElementById('resultBox').style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
