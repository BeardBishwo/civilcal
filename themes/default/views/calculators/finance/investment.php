<?php $page_title = $title ?? 'Investment Calculator'; ?>
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
        .chart-container { position: relative; height: 350px; width: 100%; margin-top: 30px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <a href="<?php echo app_base_url('/calculator'); ?>" class="back-btn"><i class="bi bi-arrow-left me-2"></i>Back</a>
        <div class="calc-card">
            <div class="calc-header">
                <i class="bi bi-graph-up-arrow" style="font-size: 2.5rem;"></i>
                <h2>Investment Calculator</h2>
                <p class="mb-0 mt-2">Calculate compound interest and growth</p>
            </div>
            <div class="calc-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Initial Investment</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" id="initial" class="form-control calc-input" value="10000">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monthly Contribution</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" id="contribution" class="form-control calc-input" value="500">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Annual Rate (%)</label>
                        <div class="input-group">
                            <input type="number" id="rate" class="form-control calc-input" value="7" step="0.1">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                     <div class="col-md-6">
                        <label class="form-label fw-bold">Time Period (Years)</label>
                        <input type="number" id="years" class="form-control calc-input" value="10">
                    </div>
                </div>

                <button class="calc-btn mt-4 w-100" onclick="calculate()"><i class="bi bi-calculator me-2"></i>Calculate Growth</button>

                <div class="row mt-4" id="resultSection" style="display:none;">
                    <div class="col-md-4">
                        <div class="result-box">
                            <h5 class="text-muted mb-3">Future Value</h5>
                            <div class="result-val" id="future_value">$0.00</div>
                            <hr>
                            <div class="small text-muted mb-1">Total Contributions:</div>
                            <div class="fw-bold fs-5 mb-3" id="total_invested">$0.00</div>
                            <div class="small text-muted mb-1">Total Interest Earned:</div>
                            <div class="fw-bold fs-5 text-success" id="total_interest">$0.00</div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="chart-container">
                            <canvas id="growthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let growthChart = null;

        function calculate() {
            const initial = parseFloat(document.getElementById('initial').value);
            const monthly = parseFloat(document.getElementById('contribution').value);
            const rate = parseFloat(document.getElementById('rate').value) / 100;
            const years = parseFloat(document.getElementById('years').value);
            
            const labels = [];
            const dataInvested = [];
            const dataGrowth = [];
            
            let currentBalance = initial;
            let totalInvested = initial;
            
            for (let i = 0; i <= years; i++) {
                labels.push('Year ' + i);
                dataInvested.push(totalInvested);
                dataGrowth.push(currentBalance);
                
                // Advance one year
                if (i < years) {
                    for(let m=0; m<12; m++) {
                        currentBalance += monthly;
                        currentBalance *= (1 + rate/12);
                        totalInvested += monthly;
                    }
                }
            }
            
            const interest = currentBalance - totalInvested;
            
            document.getElementById('future_value').textContent = formatMoney(currentBalance);
            document.getElementById('total_invested').textContent = formatMoney(totalInvested);
            document.getElementById('total_interest').textContent = formatMoney(interest);
            document.getElementById('resultSection').style.display = 'flex';
            
            renderChart(labels, dataInvested, dataGrowth);
        }

        function formatMoney(amount) {
            return '$' + amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function renderChart(labels, invested, growth) {
            const ctx = document.getElementById('growthChart').getContext('2d');
            if(growthChart) growthChart.destroy();
            
            growthChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Total Balance',
                            data: growth,
                            borderColor: '#38ef7d',
                            backgroundColor: 'rgba(56, 239, 125, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Invested Capital',
                            data: invested,
                            borderColor: '#11998e',
                            backgroundColor: 'rgba(17, 153, 142, 0.1)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) { return '$' + value/1000 + 'k'; }
                            }
                        }
                    }
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo app_base_url('/themes/default/assets/js/floating-calculator.js'); ?>"></script>
</body>
</html>
