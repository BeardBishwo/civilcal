<?php $page_title = $title ?? 'Salary Calculator'; ?>
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
        body { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); min-height: 100vh; padding: 40px 0; }
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 800px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #f8f9fa; border-radius: 12px; padding: 25px; margin-top: 30px; }
        .pay-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e9ecef; }
        .pay-row:last-child { border-bottom: none; }
        .pay-val { font-weight: 700; }
        .net-pay { background: #e8fff3; color: #11998e; border-radius: 8px; padding: 15px; margin-top: 10px; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-wallet2" style="font-size: 2.5rem;"></i>
                <h2>Salary Calculator</h2>
                <p class="mb-0 mt-2">Estimate net pay after tax and deductions</p>
            </div>
            <div class="calc-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Gross Annual Salary</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" id="gross" class="form-control calc-input" value="60000">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Effective Tax Rate (%)</label>
                        <div class="input-group">
                            <input type="number" id="tax_rate" class="form-control calc-input" value="20">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Period</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="period" id="p_year" checked onchange="calculate()">
                            <label class="btn btn-outline-success" for="p_year">Yearly</label>
                            <input type="radio" class="btn-check" name="period" id="p_month" onchange="calculate()">
                            <label class="btn btn-outline-success" for="p_month">Monthly</label>
                            <input type="radio" class="btn-check" name="period" id="p_biweek" onchange="calculate()">
                            <label class="btn btn-outline-success" for="p_biweek">Bi-Weekly</label>
                            <input type="radio" class="btn-check" name="period" id="p_week" onchange="calculate()">
                            <label class="btn btn-outline-success" for="p_week">Weekly</label>
                        </div>
                    </div>
                </div>

                <div class="result-box mt-4">
                    <h5 class="text-muted mb-3">Estimated Take-Home Pay</h5>
                    
                    <div class="pay-row">
                        <span>Gross Pay</span>
                        <span class="pay-val" id="res_gross">$60,000.00</span>
                    </div>
                    <div class="pay-row text-danger">
                        <span>Total Tax & Deductions</span>
                        <span class="pay-val" id="res_tax">-$12,000.00</span>
                    </div>
                    
                    <div class="pay-row net-pay">
                        <span class="fw-bold fs-5">Net Pay</span>
                        <span class="fw-bold fs-4" id="res_net">$48,000.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const inputIds = ['gross', 'tax_rate'];
        inputIds.forEach(id => document.getElementById(id).addEventListener('input', calculate));

        function calculate() {
            const grossYearly = parseFloat(document.getElementById('gross').value) || 0;
            const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
            
            // Calculate Yearly Stats
            const taxYearly = grossYearly * (taxRate / 100);
            const netYearly = grossYearly - taxYearly;
            
            // Determine divisor based on period
            let divisor = 1;
            if (document.getElementById('p_month').checked) divisor = 12;
            else if (document.getElementById('p_biweek').checked) divisor = 26;
            else if (document.getElementById('p_week').checked) divisor = 52;
            
            document.getElementById('res_gross').textContent = formatMoney(grossYearly / divisor);
            document.getElementById('res_tax').textContent = '-' + formatMoney(taxYearly / divisor);
            document.getElementById('res_net').textContent = formatMoney(netYearly / divisor);
        }

        function formatMoney(amount) {
            return '$' + amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        
        // Init
        calculate();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
