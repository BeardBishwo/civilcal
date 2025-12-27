<?php $page_title = $title ?? 'Age Calculator'; ?>
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
        body { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); min-height: 100vh; padding: 40px 0; }
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 700px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.3rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; }
        .calc-btn { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 30px; text-align: center; margin-top: 30px; }
        .age-display { font-size: 3rem; font-weight: 700; margin: 20px 0; }
        .age-details { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px; }
        .age-item { text-align: center; }
        .age-value { font-size: 2rem; font-weight: 700; }
        .age-label { font-size: 0.9rem; opacity: 0.9; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-calendar-event" style="font-size: 2.5rem;"></i>
                <h2>Age Calculator</h2>
                <p class="mb-0 mt-2">Calculate your exact age</p>
            </div>
            <div class="calc-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Date of Birth</label>
                    <input type="date" id="birthdate" class="form-control calc-input" value="1990-01-01">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Calculate Age On</label>
                    <input type="date" id="targetDate" class="form-control calc-input">
                </div>
                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate Age</button>
                <div class="result-box" id="resultBox" style="display:none;">
                    <div>Your Age</div>
                    <div class="age-display" id="ageDisplay">0 years</div>
                    <div class="age-details">
                        <div class="age-item">
                            <div class="age-value" id="years">0</div>
                            <div class="age-label">Years</div>
                        </div>
                        <div class="age-item">
                            <div class="age-value" id="months">0</div>
                            <div class="age-label">Months</div>
                        </div>
                        <div class="age-item">
                            <div class="age-value" id="days">0</div>
                            <div class="age-label">Days</div>
                        </div>
                    </div>
                    <div class="mt-3">Total: <span id="totalDays">0</span> days</div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Set today's date as default
        document.getElementById('targetDate').valueAsDate = new Date();
        
        function calculate() {
            const birth = new Date(document.getElementById('birthdate').value);
            const target = new Date(document.getElementById('targetDate').value);
            
            let years = target.getFullYear() - birth.getFullYear();
            let months = target.getMonth() - birth.getMonth();
            let days = target.getDate() - birth.getDate();
            
            if (days < 0) {
                months--;
                const prevMonth = new Date(target.getFullYear(), target.getMonth(), 0);
                days += prevMonth.getDate();
            }
            
            if (months < 0) {
                years--;
                months += 12;
            }
            
            const totalDays = Math.floor((target - birth) / (1000 * 60 * 60 * 24));
            
            document.getElementById('resultBox').style.display = 'block';
            document.getElementById('ageDisplay').textContent = `${years} years`;
            document.getElementById('years').textContent = years;
            document.getElementById('months').textContent = months;
            document.getElementById('days').textContent = days;
            document.getElementById('totalDays').textContent = totalDays.toLocaleString();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
