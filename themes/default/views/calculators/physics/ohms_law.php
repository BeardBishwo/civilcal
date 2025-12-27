<?php $page_title = $title ?? 'Ohms Law Calculator'; ?>
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
        body { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); min-height: 100vh; padding: 40px 0; }
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 700px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.3rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; }
        .calc-btn { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 12px; padding: 30px; text-align: center; margin-top: 30px; }
        .result-value { font-size: 3rem; font-weight: 700; margin: 10px 0; }
        .tab-btn { background: #e9ecef; border: none; padding: 12px 25px; border-radius: 8px 8px 0 0; margin-right: 5px; cursor: pointer; font-weight: 600; }
        .tab-btn.active { background: #f8f9fa; color: #fa709a; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-lightning-charge" style="font-size: 2.5rem;"></i>
                <h2>Ohm's Law Calculator</h2>
                <p class="mb-0 mt-2">V = IR</p>
            </div>
            <div class="calc-body">
                <div class="mb-3">
                    <button class="tab-btn active" onclick="switchTab('voltage')">Calculate Voltage</button>
                    <button class="tab-btn" onclick="switchTab('current')">Calculate Current</button>
                    <button class="tab-btn" onclick="switchTab('resistance')">Calculate Resistance</button>
                </div>
                
                <div id="tab_voltage" class="tab-content">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Current (A)</label>
                            <input type="number" id="current_v" class="form-control calc-input" value="2" step="any">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Resistance (Ω)</label>
                            <input type="number" id="resistance_v" class="form-control calc-input" value="5" step="any">
                        </div>
                    </div>
                </div>
                
                <div id="tab_current" class="tab-content" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Voltage (V)</label>
                            <input type="number" id="voltage_c" class="form-control calc-input" value="10" step="any">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Resistance (Ω)</label>
                            <input type="number" id="resistance_c" class="form-control calc-input" value="5" step="any">
                        </div>
                    </div>
                </div>
                
                <div id="tab_resistance" class="tab-content" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Voltage (V)</label>
                            <input type="number" id="voltage_r" class="form-control calc-input" value="10" step="any">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Current (A)</label>
                            <input type="number" id="current_r" class="form-control calc-input" value="2" step="any">
                        </div>
                    </div>
                </div>
                
                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate</button>
                <div class="result-box" id="resultBox" style="display:none;">
                    <div id="resultLabel">Result</div>
                    <div class="result-value"><span id="result">0</span> <span id="unit"></span></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const appBase = "<?php echo rtrim(app_base_url(), '/'); ?>";
        let currentTab = 'voltage';
        
        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab_' + tab).style.display = 'block';
            event.target.classList.add('active');
            currentTab = tab;
        }
        
        function calculate() {
            let value1, value2;
            
            if (currentTab === 'voltage') {
                value1 = document.getElementById('current_v').value;
                value2 = document.getElementById('resistance_v').value;
            } else if (currentTab === 'current') {
                value1 = document.getElementById('voltage_c').value;
                value2 = document.getElementById('resistance_c').value;
            } else {
                value1 = document.getElementById('voltage_r').value;
                value2 = document.getElementById('current_r').value;
            }
            
            fetch(appBase + '/calculator/api/ohms-law', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `type=${currentTab}&value1=${value1}&value2=${value2}`
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('resultBox').style.display = 'block';
                document.getElementById('result').textContent = data.result;
                
                if (currentTab === 'voltage') {
                    document.getElementById('resultLabel').textContent = 'Voltage';
                    document.getElementById('unit').textContent = 'V';
                } else if (currentTab === 'current') {
                    document.getElementById('resultLabel').textContent = 'Current';
                    document.getElementById('unit').textContent = 'A';
                } else {
                    document.getElementById('resultLabel').textContent = 'Resistance';
                    document.getElementById('unit').textContent = 'Ω';
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
