<?php $page_title = $title ?? 'Right Triangle Solver'; ?>
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
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 0; }
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 900px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 10px; }
        .calc-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; padding: 12px 30px; font-weight: 600; cursor: pointer; }
        .result-box { background: #f8f9fa; border-radius: 12px; padding: 20px; border: 1px solid #e9ecef; }
        .result-title { font-weight: 700; color: #667eea; margin-bottom: 10px; border-bottom: 2px solid #e9ecef; padding-bottom: 5px; }
        .triangle-container { position: relative; height: 300px; margin: 0 auto; width: 300px; border-bottom: 2px solid #333; border-left: 2px solid #333; }
        .hypotenuse { position: absolute; height: 2px; background: #333; transform-origin: top left; width: 0; top: 0; left: 0; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
        .input-group-text { width: 45px; justify-content: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-triangle-half" style="font-size: 2.5rem;"></i>
                <h2>Right Triangle Solver</h2>
                <p class="mb-0 mt-2">Enter any 2 values to solve the triangle</p>
            </div>
            <div class="calc-body">
                <div class="row">
                    <!-- Diagram -->
                    <div class="col-md-5 text-center d-flex align-items-center justify-content-center mb-4 mb-md-0">
                        <div style="position: relative;">
                            <!-- SVG Triangle for better scaling -->
                            <svg width="250" height="250" viewBox="-10 -10 120 120">
                                <polygon points="0,0 0,100 100,100" fill="none" stroke="#333" stroke-width="2"/>
                                <rect x="0" y="90" width="10" height="10" fill="none" stroke="#333" />
                                <text x="-8" y="50" font-weight="bold" fill="#667eea">a</text>
                                <text x="50" y="115" font-weight="bold" fill="#667eea">b</text>
                                <text x="55" y="45" font-weight="bold" fill="#667eea">c</text>
                                <text x="85" y="95" font-size="8" fill="#e83e8c">A</text>
                                <text x="5" y="15" font-size="8" fill="#e83e8c">B</text>
                                <text x="5" y="95" font-size="8">C=90°</text>
                            </svg>
                        </div>
                    </div>

                    <!-- Inputs & Results -->
                    <div class="col-md-7">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted small">Side a (Leg)</label>
                                <div class="input-group">
                                    <span class="input-group-text">a</span>
                                    <input type="number" id="side_a" class="form-control calc-input" oninput="solve()">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">Side b (Leg)</label>
                                <div class="input-group">
                                    <span class="input-group-text">b</span>
                                    <input type="number" id="side_b" class="form-control calc-input" oninput="solve()">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">Side c (Hypotenuse)</label>
                                <div class="input-group">
                                    <span class="input-group-text">c</span>
                                    <input type="number" id="side_c" class="form-control calc-input" oninput="solve()">
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <label class="form-label text-muted small">Angle A (deg)</label>
                                <div class="input-group">
                                    <span class="input-group-text">A</span>
                                    <input type="number" id="angle_A" class="form-control calc-input" oninput="solve()">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">Angle B (deg)</label>
                                <div class="input-group">
                                    <span class="input-group-text">B</span>
                                    <input type="number" id="angle_B" class="form-control calc-input" oninput="solve()">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <button class="btn btn-sm btn-outline-secondary" onclick="clearAll()">Clear</button>
                            <!-- Auto-solve on input, no button needed -->
                        </div>

                        <div class="result-box" id="resultBox" style="opacity: 0.5;">
                            <div class="result-title">Computed Results</div>
                            <div class="row small">
                                <div class="col-6 mb-1">Area: <span id="res_area" class="fw-bold">-</span></div>
                                <div class="col-6 mb-1">Perimeter: <span id="res_perimeter" class="fw-bold">-</span></div>
                                <div class="col-12 text-muted fst-italic mt-2" id="status_msg">Enter any 2 values</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Store last user inputs to detect what changed.
        // Simplified Logic: Check how many meaningful inputs are present.
        
        function solve() {
            const inputs = {
                a: parseFloat(document.getElementById('side_a').value),
                b: parseFloat(document.getElementById('side_b').value),
                c: parseFloat(document.getElementById('side_c').value),
                A: parseFloat(document.getElementById('angle_A').value),
                B: parseFloat(document.getElementById('angle_B').value)
            };

            // Count valid inputs
            let count = 0;
            for (let k in inputs) {
                if (!isNaN(inputs[k]) && inputs[k] > 0) count++;
            }

            if (count < 2) {
                document.getElementById('resultBox').style.opacity = '0.5';
                document.getElementById('status_msg').textContent = "Enter at least 2 values (e.g., 2 sides, or 1 side + 1 angle)";
                return;
            }

            // Calculations
            let a = inputs.a, b = inputs.b, c = inputs.c, A = inputs.A, B = inputs.B;
            const toRad = Math.PI / 180;
            const toDeg = 180 / Math.PI;

            // Scenario 1: Two Sides
            if (!isNaN(a) && !isNaN(b)) {
                c = Math.sqrt(a*a + b*b);
                A = Math.atan(a/b) * toDeg;
                B = 90 - A;
            } else if (!isNaN(a) && !isNaN(c)) {
                if (c <= a) { setError("Hypotenuse (c) must be greater than leg (a)"); return; }
                b = Math.sqrt(c*c - a*a);
                A = Math.asin(a/c) * toDeg;
                B = 90 - A;
            } else if (!isNaN(b) && !isNaN(c)) {
                if (c <= b) { setError("Hypotenuse (c) must be greater than leg (b)"); return; }
                a = Math.sqrt(c*c - b*b);
                B = Math.asin(b/c) * toDeg;
                A = 90 - B;
            }
            // Scenario 2: One Side and One Angle
            else if (!isNaN(a) && !isNaN(A)) {
                B = 90 - A;
                c = a / Math.sin(A * toRad);
                b = c * Math.cos(A * toRad);
            } else if (!isNaN(a) && !isNaN(B)) {
                A = 90 - B;
                c = a / Math.cos(B * toRad);
                b = c * Math.sin(B * toRad);
            } else if (!isNaN(b) && !isNaN(A)) {
                B = 90 - A;
                c = b / Math.cos(A * toRad);
                a = c * Math.sin(A * toRad);
            } else if (!isNaN(b) && !isNaN(B)) {
                A = 90 - B;
                c = b / Math.sin(B * toRad);
                a = c * Math.cos(B * toRad);
            } else if (!isNaN(c) && !isNaN(A)) {
                B = 90 - A;
                a = c * Math.sin(A * toRad);
                b = c * Math.cos(A * toRad);
            } else if (!isNaN(c) && !isNaN(B)) {
                A = 90 - B;
                b = c * Math.sin(B * toRad);
                a = c * Math.cos(B * toRad);
            }

            // Update UI (only fill empty or computed values, avoid overwriting user input while typing if possible, but for simple solver we fill all to be safe)
            // Ideally, we clarify which use inputs vs computed, but filling all allows correction.
            
            // To prevent cursor jumping, only update fields that were empty or different significantly? 
            // For simplicity, we define 'outputs' as the ones NOT currently focused or simply update results panel.
            // BUT, users expect the fields to populate.
            
            // Better UX: Show results in the result box primarily, but maybe fill standard fields if it makes sense.
            // Let's populate specific result spans if we want to avoid input interference, OR simply update inputs that are empty.
            
            fillIfNotFocused('side_a', a);
            fillIfNotFocused('side_b', b);
            fillIfNotFocused('side_c', c);
            fillIfNotFocused('angle_A', A);
            fillIfNotFocused('angle_B', B);

            // Derived
            const area = 0.5 * a * b;
            const perimeter = a + b + c;

            document.getElementById('res_area').textContent = area.toFixed(2);
            document.getElementById('res_perimeter').textContent = perimeter.toFixed(2);
            
            document.getElementById('resultBox').style.opacity = '1';
            document.getElementById('status_msg').textContent = "Triangle solved successfully.";
        }

        function fillIfNotFocused(id, val) {
            const el = document.getElementById(id);
            if (document.activeElement !== el && !isNaN(val)) {
                el.value = Number.isInteger(val) ? val : val.toFixed(2);
            }
        }
        
        function setError(msg) {
             document.getElementById('status_msg').textContent = "Error: " + msg;
             document.getElementById('resultBox').style.opacity = '1';
        }

        function clearAll() {
            document.querySelectorAll('input').forEach(i => i.value = '');
            document.getElementById('resultBox').style.opacity = '0.5';
            document.getElementById('res_area').textContent = '-';
            document.getElementById('res_perimeter').textContent = '-';
            document.getElementById('status_msg').textContent = "Enter any 2 values";
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
