<?php $page_title = $title ?? 'BMR Calculator'; ?>
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
        .calc-header { background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #f8f9fa; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; }
        .result-val { font-size: 2.5rem; font-weight: 700; color: #DD2476; margin: 10px 0; }
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
                        <i class="bi bi-fire" style="font-size: 2.5rem;"></i>
                        <h2>BMR Calculator</h2>
                        <p class="mb-0 mt-2">Basal Metabolic Rate</p>
                    </div>
                    <div class="calc-body">
                        <!-- Content -->
                        <div class="row g-3 justify-content-center mb-3">
                             <div class="col-md-6">
                                 <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="gender" id="male" checked>
                                    <label class="btn btn-outline-danger" for="male">Male</label>
                                    <input type="radio" class="btn-check" name="gender" id="female">
                                    <label class="btn btn-outline-danger" for="female">Female</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 justify-content-center">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Height (cm)</label>
                                <input type="number" id="height" class="form-control calc-input" value="175">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Weight (kg)</label>
                                <input type="number" id="weight" class="form-control calc-input" value="70">
                            </div>
                             <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Age</label>
                                <input type="number" id="age" class="form-control calc-input" value="25">
                            </div>
                        </div>

                        <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate BMR</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-muted text-uppercase fw-bold small">Your BMR</div>
                            <div class="result-val" id="bmrValue">1,700</div>
                            <div class="text-muted">Calories / Day</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        function calculate() {
            const isMale = document.getElementById('male').checked;
            const age = parseFloat(document.getElementById('age').value);
            const height = parseFloat(document.getElementById('height').value);
            const weight = parseFloat(document.getElementById('weight').value);

            if (!age || !height || !weight) return;

            // Mifflin-St Jeor Equation
            let bmr = (10 * weight) + (6.25 * height) - (5 * age);
            
            if (isMale) {
                bmr += 5;
            } else {
                bmr -= 161;
            }

            document.getElementById('bmrValue').textContent = Math.round(bmr).toLocaleString();
            document.getElementById('resultBox').style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
