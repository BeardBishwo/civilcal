<?php $page_title = $title ?? 'Gas Laws Calculator'; ?>
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
        .calc-header { background: linear-gradient(135deg, #7f8c8d 0%, #2c3e50 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #7f8c8d 0%, #2c3e50 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #eceff1; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; border: 1px solid #cfd8dc; }
        .result-val { font-size: 2.5rem; font-weight: 700; color: #37474f; margin: 10px 0; }
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
                        <i class="bi bi-cloud-haze2" style="font-size: 2.5rem;"></i>
                        <h2>Gas Laws Calculator</h2>
                        <p class="mb-0 mt-2">Boyle's, Charles's, and Ideal Gas Laws</p>
                    </div>
                    <div class="calc-body">
                        <ul class="nav nav-pills nav-fill mb-4" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active fw-bold border" id="boyle-tab" data-bs-toggle="pill" data-bs-target="#boyle-content">Boyle's Law</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold border mx-2" id="charles-tab" data-bs-toggle="pill" data-bs-target="#charles-content">Charles's Law</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold border" id="ideal-tab" data-bs-toggle="pill" data-bs-target="#ideal-content">Ideal Gas</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="pills-tabContent">
                            <!-- Boyle's Law: P1V1 = P2V2 -->
                            <div class="tab-pane fade show active" id="boyle-content">
                                <h5 class="text-center mb-3">P₁V₁ = P₂V₂</h5>
                                <div class="row g-3">
                                    <div class="col-6"><label class="fw-bold">P1</label><input type="number" id="b_p1" class="form-control calc-input" placeholder="Initial Pressure"></div>
                                    <div class="col-6"><label class="fw-bold">V1</label><input type="number" id="b_v1" class="form-control calc-input" placeholder="Initial Volume"></div>
                                    <div class="col-6"><label class="fw-bold">P2</label><input type="number" id="b_p2" class="form-control calc-input" placeholder="Final Pressure"></div>
                                    <div class="col-6"><label class="fw-bold">V2</label><input type="number" id="b_v2" class="form-control calc-input" placeholder="Final Volume (Leave empty)"></div>
                                </div>
                                <button class="calc-btn mt-4 w-100" onclick="calcBoyle()">Calculate Missing Value</button>
                            </div>

                            <!-- Charles's Law: V1/T1 = V2/T2 -->
                            <div class="tab-pane fade" id="charles-content">
                                <h5 class="text-center mb-3">V₁/T₁ = V₂/T₂</h5>
                                 <div class="row g-3">
                                    <div class="col-6"><label class="fw-bold">V1</label><input type="number" id="c_v1" class="form-control calc-input" placeholder="Initial Volume"></div>
                                    <div class="col-6"><label class="fw-bold">T1 (K)</label><input type="number" id="c_t1" class="form-control calc-input" placeholder="Initial Temp (K)"></div>
                                    <div class="col-6"><label class="fw-bold">V2</label><input type="number" id="c_v2" class="form-control calc-input" placeholder="Final Volume"></div>
                                    <div class="col-6"><label class="fw-bold">T2 (K)</label><input type="number" id="c_t2" class="form-control calc-input" placeholder="Final Temp (K)"></div>
                                </div>
                                <button class="calc-btn mt-4 w-100" onclick="calcCharles()">Calculate Missing Value</button>
                            </div>

                            <!-- Ideal Gas Law: PV = nRT -->
                            <div class="tab-pane fade" id="ideal-content">
                                <h5 class="text-center mb-3">PV = nRT</h5>
                                 <div class="row g-3">
                                    <div class="col-6"><label class="fw-bold">Pressure (P)</label><input type="number" id="i_p" class="form-control calc-input" placeholder="atm"></div>
                                    <div class="col-6"><label class="fw-bold">Volume (V)</label><input type="number" id="i_v" class="form-control calc-input" placeholder="L"></div>
                                    <div class="col-6"><label class="fw-bold">Moles (n)</label><input type="number" id="i_n" class="form-control calc-input" placeholder="mol"></div>
                                    <div class="col-6"><label class="fw-bold">Temp (T)</label><input type="number" id="i_t" class="form-control calc-input" placeholder="K"></div>
                                    <div class="col-12 text-center text-muted small">R = 0.0821 L·atm/(mol·K)</div>
                                </div>
                                <button class="calc-btn mt-4 w-100" onclick="calcIdeal()">Calculate Missing Value</button>
                            </div>
                        </div>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-muted text-uppercase fw-bold small">Result</div>
                            <div class="result-val" id="resultValue">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function showRes(val) {
            document.getElementById('resultValue').textContent = parseFloat(val).toFixed(4).replace(/\.?0+$/, "");
            document.getElementById('resultBox').style.display = 'block';
        }

        function calcBoyle() {
            const p1 = document.getElementById('b_p1').value;
            const v1 = document.getElementById('b_v1').value;
            const p2 = document.getElementById('b_p2').value;
            const v2 = document.getElementById('b_v2').value;
            
            if (p1 && v1 && p2 && !v2) showRes((p1 * v1) / p2);
            else if (p1 && v1 && !p2 && v2) showRes((p1 * v1) / v2);
            else if (p1 && !v1 && p2 && v2) showRes((p2 * v2) / p1);
            else if (!p1 && v1 && p2 && v2) showRes((p2 * v2) / v1);
            else alert("Fill exactly 3 fields");
        }

        function calcCharles() {
            const v1 = document.getElementById('c_v1').value;
            const t1 = document.getElementById('c_t1').value;
            const v2 = document.getElementById('c_v2').value;
            const t2 = document.getElementById('c_t2').value;

            if (v1 && t1 && v2 && !t2) showRes((v2 * t1) / v1);
            else if (v1 && t1 && !v2 && t2) showRes((v1 * t2) / t1);
            else if (v1 && !t1 && v2 && t2) showRes((v1 * t2) / v2);
            else if (!v1 && t1 && v2 && t2) showRes((v2 * t1) / t2);
            else alert("Fill exactly 3 fields");
        }

        function calcIdeal() {
            const P = document.getElementById('i_p').value;
            const V = document.getElementById('i_v').value;
            const n = document.getElementById('i_n').value;
            const T = document.getElementById('i_t').value;
            const R = 0.0821;

            if (!P && V && n && T) showRes((n * R * T) / V); // Find P
            else if (P && !V && n && T) showRes((n * R * T) / P); // Find V
            else if (P && V && !n && T) showRes((P * V) / (R * T)); // Find n
            else if (P && V && n && !T) showRes((P * V) / (n * R)); // Find T
            else alert("Fill exactly 3 fields");
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
