<?php $page_title = $title ?? 'Loan Calculator'; ?>
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
        .calc-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 900px; margin: 0 auto; }
        .calc-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; text-align: center; }
        .calc-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .calc-body { padding: 40px; }
        .calc-input { font-size: 1.1rem; font-weight: 600; border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; }
        .calc-btn { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border: none; border-radius: 8px; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .result-box { background: #f8f9fa; border-radius: 12px; padding: 25px; margin-top: 30px; border-left: 5px solid #11998e; }
        .result-val { font-size: 1.8rem; font-weight: 700; color: #11998e; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: white; text-decoration: none; font-weight: 600; }
        .chart-container { position: relative; height: 300px; width: 100%; margin-top: 30px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-bank" style="font-size: 2.5rem;"></i>
                <h2>Loan Calculator</h2>
                <p class="mb-0 mt-2">Calculate monthly payments and total interest</p>
            </div>
            <div class="calc-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Loan Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" id="principal" class="form-control calc-input" value="100000">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Interest Rate (%)</label>
                        <div class="input-group">
                            <input type="number" id="rate" class="form-control calc-input" value="5" step="0.1">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Loan Term</label>
                        <div class="input-group">
                            <input type="number" id="term" class="form-control calc-input" value="15">
                            <select id="term_unit" class="form-select calc-input" style="max-width: 100px;">
                                <option value="years">Years</option>
                                <option value="months">Months</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate Payment</button>

                <div class="row mt-4" id="resultSection" style="display:none;">
                    <div class="col-md-5">
                        <div class="result-box">
                            <h5 class="text-muted mb-3">Monthly Payment</h5>
                            <div class="result-val" id="monthly_payment">$0.00</div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Payment:</span>
                                <span class="fw-bold" id="total_payment">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Total Interest:</span>
                                <span class="fw-bold text-danger" id="total_interest">$0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="chart-container">
                            <canvas id="loanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let loanChart = null;

        function calculate() {
            const principal = parseFloat(document.getElementById('principal').value);
            const rate = parseFloat(document.getElementById('rate').value) / 100 / 12;
            let term = parseFloat(document.getElementById('term').value);
            const termUnit = document.getElementById('term_unit').value;

            if (termUnit === 'years') term *= 12;

            if (principal <= 0 || term <= 0) return;

            let monthly = 0;
            if (rate === 0) {
                monthly = principal / term;
            } else {
                monthly = principal * (rate * Math.pow(1 + rate, term)) / (Math.pow(1 + rate, term) - 1);
            }

            const total = monthly * term;
            const interest = total - principal;

            document.getElementById('monthly_payment').textContent = formatMoney(monthly);
            document.getElementById('total_payment').textContent = formatMoney(total);
            document.getElementById('total_interest').textContent = formatMoney(interest);
            
            document.getElementById('resultSection').style.display = 'flex';
            
            updateChart(principal, interest);
        }

        function formatMoney(amount) {
            return '$' + amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function updateChart(principal, interest) {
            const ctx = document.getElementById('loanChart').getContext('2d');
            
            if (loanChart) loanChart.destroy();

            loanChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Principal', 'Interest'],
                    datasets: [{
                        data: [principal, interest],
                        backgroundColor: ['#11998e', '#ff6b6b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
