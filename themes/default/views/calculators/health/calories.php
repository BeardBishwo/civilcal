<?php $page_title = $title ?? 'Calorie Calculator'; ?>
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
        .calc-header { background: linear-gradient(135deg, #FF9966 0%, #FF5E62 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #FF9966 0%, #FF5E62 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #fff3e0; border-radius: 12px; padding: 25px; margin-top: 30px; border: 1px solid #ffe0b2; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: var(--text-primary); text-decoration: none; font-weight: 600; }
        .calorie-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #ffcc80; align-items: center; }
        .calorie-row:last-child { border: none; }
        .cal-val { font-weight: 800; font-size: 1.2rem; background: white; padding: 5px 15px; border-radius: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
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
                        <i class="bi bi-egg-fried" style="font-size: 2.5rem;"></i>
                        <h2>Calorie Calculator</h2>
                        <p class="mb-0 mt-2">Calculate daily calorie needs (TDEE)</p>
                    </div>
                    <div class="calc-body">
                        <div class="row g-4 mb-4">
                             <!-- Minimal Age/Weight/Height Inputs reused logic -->
                             <div class="col-md-12 text-center gender-select">
                                <label class="form-label fw-bold d-block mb-2">Gender</label>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="gender" id="male" checked>
                                    <label class="btn btn-outline-danger px-4" for="male">Male</label>
                                    <input type="radio" class="btn-check" name="gender" id="female">
                                    <label class="btn btn-outline-danger px-4" for="female">Female</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Age</label>
                                <input type="number" id="age" class="form-control calc-input" value="25">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Height (cm)</label>
                                <input type="number" id="height" class="form-control calc-input" value="175">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Weight (kg)</label>
                                <input type="number" id="weight" class="form-control calc-input" value="70">
                            </div>
                            
                            <div class="col-md-12">
                                 <label class="form-label fw-bold text-dark">Activity Level</label>
                                 <select id="activity" class="form-select calc-input">
                                     <option value="1.2">Sedentary (office job)</option>
                                     <option value="1.375">Light Exercise (1-2 days/week)</option>
                                     <option value="1.55" selected>Moderate Exercise (3-5 days/week)</option>
                                     <option value="1.725">Heavy Exercise (6-7 days/week)</option>
                                     <option value="1.9">Athlete (2x per day)</option>
                                 </select>
                            </div>
                        </div>

                        <button class="calc-btn w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate TDEE</button>

                        <div class="result-box" id="resultBox" style="display:none;">
                            <div class="text-center mb-4">
                                <div class="small text-muted text-uppercase fw-bold">Maintenance Calories</div>
                                <div class="fs-1 fw-bold text-danger" id="tdee">2,500</div>
                                <div class="small text-muted">Calories per day</div>
                            </div>
                            
                            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-bullseye me-2"></i>Goal: Weight Loss</h6>
                            <div class="calorie-row">
                                <span>Mild Weight Loss (-0.25 kg/week)</span>
                                <span class="cal-val text-primary" id="loss_mild">2,250</span>
                            </div>
                             <div class="calorie-row">
                                <span>Weight Loss (-0.5 kg/week)</span>
                                <span class="cal-val text-primary" id="loss_normal">2,000</span>
                            </div>
                             <div class="calorie-row">
                                <span>Extreme Weight Loss (-1 kg/week)</span>
                                <span class="cal-val text-danger" id="loss_extreme">1,500</span>
                            </div>
                            
                            <h6 class="fw-bold text-muted mt-4 mb-3"><i class="bi bi-graph-up-arrow me-2"></i>Goal: Weight Gain</h6>
                             <div class="calorie-row">
                                <span>Mild Weight Gain (+0.25 kg/week)</span>
                                <span class="cal-val text-success" id="gain_mild">2,750</span>
                            </div>
                             <div class="calorie-row">
                                <span>Fast Weight Gain (+0.5 kg/week)</span>
                                <span class="cal-val text-success" id="gain_normal">3,000</span>
                            </div>
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
            const activity = parseFloat(document.getElementById('activity').value);

            if (!age || !height || !weight) return;

            // BMR
            let bmr = (10 * weight) + (6.25 * height) - (5 * age);
            if (isMale) bmr += 5; else bmr -= 161;

            // TDEE
            const tdee = Math.round(bmr * activity);
            
            document.getElementById('tdee').textContent = tdee.toLocaleString();
            
            // Weight Loss Steps (approx 500 cal deficit = 0.5kg/week loss)
            // 0.25kg = ~250 cal
            // 0.5kg = ~500 cal
            // 1kg = ~1000 cal
            
            document.getElementById('loss_mild').textContent = (tdee - 250).toLocaleString();
            document.getElementById('loss_normal').textContent = (tdee - 500).toLocaleString();
            document.getElementById('loss_extreme').textContent = (tdee - 1000).toLocaleString();
            
            document.getElementById('gain_mild').textContent = (tdee + 250).toLocaleString();
            document.getElementById('gain_normal').textContent = (tdee + 500).toLocaleString();
            
            document.getElementById('resultBox').style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
