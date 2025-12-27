<?php $page_title = $title ?? 'Percentage Calculator'; ?>
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
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        
        .calc-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .calc-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 16px 16px 0 0;
            text-align: center;
        }
        
        .calc-header h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .calc-body {
            padding: 40px;
        }
        
        .calc-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .calc-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .calc-input {
            font-size: 1.3rem;
            font-weight: 600;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
        }
        
        .calc-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .calc-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .calc-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .result-box {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-top: 30px;
        }
        
        .result-value {
            font-size: 3rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .tab-btn {
            background: #e9ecef;
            border: none;
            padding: 12px 25px;
            border-radius: 8px 8px 0 0;
            margin-right: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .tab-btn.active {
            background: #f8f9fa;
            color: #667eea;
        }
        
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: white;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn">
            <i class="bi bi-arrow-left me-2"></i>Back to Calculator Platform
        </a>
        
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-percent" style="font-size: 2.5rem;"></i>
                <h2>Percentage Calculator</h2>
                <p class="mb-0 mt-2">Calculate percentages easily</p>
            </div>
            
            <div class="calc-body">
                <!-- Tabs -->
                <div class="mb-3">
                    <button class="tab-btn active" onclick="switchTab('what_is')">What is X% of Y?</button>
                    <button class="tab-btn" onclick="switchTab('is_what_percent')">X is what % of Y?</button>
                    <button class="tab-btn" onclick="switchTab('percent_change')">% Change</button>
                </div>
                
                <!-- Tab 1: What is X% of Y? -->
                <div id="tab_what_is" class="tab-content">
                    <div class="calc-section">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="calc-label">Percentage (%)</label>
                                <input type="number" id="percent1" class="form-control calc-input" value="10" step="any">
                            </div>
                            <div class="col-md-6">
                                <label class="calc-label">Of Value</label>
                                <input type="number" id="value1" class="form-control calc-input" value="100" step="any">
                            </div>
                        </div>
                        <button class="calc-btn mt-4 w-100" onclick="calculate('what_is')">
                            <i class="bi bi-calculator me-2"></i>Calculate
                        </button>
                    </div>
                </div>
                
                <!-- Tab 2: X is what % of Y? -->
                <div id="tab_is_what_percent" class="tab-content" style="display:none;">
                    <div class="calc-section">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="calc-label">Value X</label>
                                <input type="number" id="value2a" class="form-control calc-input" value="25" step="any">
                            </div>
                            <div class="col-md-6">
                                <label class="calc-label">Of Value Y</label>
                                <input type="number" id="value2b" class="form-control calc-input" value="100" step="any">
                            </div>
                        </div>
                        <button class="calc-btn mt-4 w-100" onclick="calculate('is_what_percent')">
                            <i class="bi bi-calculator me-2"></i>Calculate
                        </button>
                    </div>
                </div>
                
                <!-- Tab 3: % Change -->
                <div id="tab_percent_change" class="tab-content" style="display:none;">
                    <div class="calc-section">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="calc-label">Original Value</label>
                                <input type="number" id="value3a" class="form-control calc-input" value="100" step="any">
                            </div>
                            <div class="col-md-6">
                                <label class="calc-label">New Value</label>
                                <input type="number" id="value3b" class="form-control calc-input" value="120" step="any">
                            </div>
                        </div>
                        <button class="calc-btn mt-4 w-100" onclick="calculate('percent_change')">
                            <i class="bi bi-calculator me-2"></i>Calculate
                        </button>
                    </div>
                </div>
                
                <!-- Result -->
                <div class="result-box" id="resultBox" style="display:none;">
                    <div>Result</div>
                    <div class="result-value" id="resultValue">0</div>
                    <div id="resultFormula"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const appBase = "<?php echo rtrim(app_base_url(), '/'); ?>";
        let currentTab = 'what_is';
        
        function switchTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            
            // Show selected tab
            document.getElementById('tab_' + tab).style.display = 'block';
            event.target.classList.add('active');
            currentTab = tab;
        }
        
        function calculate(type) {
            let value1, value2;
            
            if (type === 'what_is') {
                value1 = parseFloat(document.getElementById('percent1').value);
                value2 = parseFloat(document.getElementById('value1').value);
            } else if (type === 'is_what_percent') {
                value1 = parseFloat(document.getElementById('value2a').value);
                value2 = parseFloat(document.getElementById('value2b').value);
            } else {
                value1 = parseFloat(document.getElementById('value3a').value);
                value2 = parseFloat(document.getElementById('value3b').value);
            }
            
            fetch(appBase + '/calculator/api/percentage', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `type=${type}&value1=${value1}&value2=${value2}`
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('resultBox').style.display = 'block';
                document.getElementById('resultValue').textContent = data.result.toFixed(2);
                
                let formula = '';
                if (type === 'what_is') {
                    formula = `${value1}% of ${value2} = ${data.result.toFixed(2)}`;
                } else if (type === 'is_what_percent') {
                    formula = `${value1} is ${data.result.toFixed(2)}% of ${value2}`;
                } else {
                    formula = `Change from ${value1} to ${value2} = ${data.result.toFixed(2)}%`;
                }
                document.getElementById('resultFormula').textContent = formula;
            });
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
