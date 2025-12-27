<?php $page_title = $title ?? 'Fraction Calculator'; ?>
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        
        .fraction-input {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }
        
        .fraction-input input {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px;
            width: 100px;
        }
        
        .fraction-line {
            width: 100%;
            height: 2px;
            background: #333;
        }
        
        .operation-btn {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .operation-btn.active {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-color: #f093fb;
        }
        
        .calc-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            box-shadow: 0 6px 20px rgba(240, 147, 251, 0.4);
        }
        
        .result-box {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-top: 30px;
        }
        
        .result-fraction {
            font-size: 3rem;
            font-weight: 700;
            margin: 20px 0;
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
                <i class="bi bi-slash-square" style="font-size: 2.5rem;"></i>
                <h2>Fraction Calculator</h2>
                <p class="mb-0 mt-2">Add, subtract, multiply, and divide fractions</p>
            </div>
            
            <div class="calc-body">
                <div class="row align-items-center g-4">
                    <!-- Fraction 1 -->
                    <div class="col-md-3 text-center">
                        <div class="fraction-input">
                            <input type="number" id="num1" value="1" class="form-control">
                            <div class="fraction-line"></div>
                            <input type="number" id="den1" value="2" class="form-control">
                        </div>
                    </div>
                    
                    <!-- Operation -->
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column gap-2">
                            <button class="operation-btn active" onclick="selectOperation('add', this)">+</button>
                            <button class="operation-btn" onclick="selectOperation('subtract', this)">−</button>
                            <button class="operation-btn" onclick="selectOperation('multiply', this)">×</button>
                            <button class="operation-btn" onclick="selectOperation('divide', this)">÷</button>
                        </div>
                    </div>
                    
                    <!-- Fraction 2 -->
                    <div class="col-md-3 text-center">
                        <div class="fraction-input">
                            <input type="number" id="num2" value="1" class="form-control">
                            <div class="fraction-line"></div>
                            <input type="number" id="den2" value="3" class="form-control">
                        </div>
                    </div>
                    
                    <!-- Equals -->
                    <div class="col-md-1 text-center">
                        <h2>=</h2>
                    </div>
                    
                    <!-- Result -->
                    <div class="col-md-3 text-center">
                        <div class="fraction-input">
                            <div id="resultNum" style="font-size:2rem; font-weight:700;">?</div>
                            <div class="fraction-line"></div>
                            <div id="resultDen" style="font-size:2rem; font-weight:700;">?</div>
                        </div>
                    </div>
                </div>
                
                <button class="calc-btn mt-4 w-100" onclick="calculate()">
                    <i class="bi bi-calculator me-2"></i>Calculate
                </button>
                
                <!-- Result Box -->
                <div class="result-box" id="resultBox" style="display:none;">
                    <div>Decimal Value</div>
                    <div class="result-fraction" id="decimalValue">0</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const appBase = "<?php echo rtrim(app_base_url(), '/'); ?>";
        let currentOperation = 'add';
        
        function selectOperation(op, btn) {
            document.querySelectorAll('.operation-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentOperation = op;
        }
        
        function calculate() {
            const n1 = parseInt(document.getElementById('num1').value);
            const d1 = parseInt(document.getElementById('den1').value);
            const n2 = parseInt(document.getElementById('num2').value);
            const d2 = parseInt(document.getElementById('den2').value);
            
            fetch(appBase + '/calculator/api/fraction', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `operation=${currentOperation}&numerator1=${n1}&denominator1=${d1}&numerator2=${n2}&denominator2=${d2}`
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('resultNum').textContent = data.numerator;
                document.getElementById('resultDen').textContent = data.denominator;
                document.getElementById('decimalValue').textContent = data.decimal.toFixed(6);
                document.getElementById('resultBox').style.display = 'block';
            });
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
