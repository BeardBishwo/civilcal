<?php $page_title = $title ?? 'NPV/IRR Analysis'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); min-height: 100vh; padding: 40px 0; }
        .calculator-card { background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 1000px; margin: 0 auto; }
        .card-header { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; }
        .year-row { background: #f8f9fa; padding: 10px; border-radius: 8px; margin-bottom: 10px; }
        .result-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-card">
            <div class="card-header">
                <h2 class="mb-0"><i class="bi bi-currency-exchange me-2"></i><?php echo $page_title; ?></h2>
                <p class="mb-0 mt-2 opacity-75">Investment appraisal using Net Present Value and Internal Rate of Return</p>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Initial Investment (Rs.)</label>
                        <input type="number" id="initial-investment" class="form-control" placeholder="e.g., 10000000" step="1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Discount Rate (%)</label>
                        <input type="number" id="discount-rate" class="form-control" value="12" step="0.1">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Number of Years</label>
                    <input type="number" id="num-years" class="form-control" value="5" min="1" max="30" onchange="generateYears()">
                </div>

                <h5 class="mb-3">Annual Cash Flows</h5>
                <div id="years-container"></div>

                <button class="btn btn-warning btn-lg px-5 mt-3" onclick="calculateNPV()">
                    <i class="bi bi-calculator me-2"></i>Calculate NPV/IRR
                </button>

                <div id="results" style="display:none;">
                    <div class="result-card">
                        <h4 class="mb-3">Investment Analysis</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Net Present Value</h6>
                                <h3 id="npv-value">Rs. 0</h3>
                                <small id="npv-verdict"></small>
                            </div>
                            <div class="col-md-4">
                                <h6>Payback Period</h6>
                                <h3 id="payback-period">0 years</h3>
                            </div>
                            <div class="col-md-4">
                                <h6>Total Cash Inflow</h6>
                                <h3 id="total-inflow">Rs. 0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        generateYears();

        function generateYears() {
            const numYears = parseInt(document.getElementById('num-years').value) || 5;
            const container = document.getElementById('years-container');
            container.innerHTML = '';

            for (let i = 1; i <= numYears; i++) {
                const row = document.createElement('div');
                row.className = 'year-row';
                row.innerHTML = `
                    <div class="row align-items-center">
                        <div class="col-md-3"><strong>Year ${i}</strong></div>
                        <div class="col-md-9">
                            <input type="number" class="form-control cash-flow" placeholder="Cash Flow (Rs.)" step="0.01">
                        </div>
                    </div>
                `;
                container.appendChild(row);
            }
        }

        function calculateNPV() {
            const initialInvestment = parseFloat(document.getElementById('initial-investment').value) || 0;
            const discountRate = parseFloat(document.getElementById('discount-rate').value) / 100 || 0.12;
            const cashFlows = Array.from(document.querySelectorAll('.cash-flow')).map(input => parseFloat(input.value) || 0);

            if (initialInvestment === 0) {
                alert('Please enter initial investment');
                return;
            }

            // Calculate NPV
            let npv = -initialInvestment;
            let cumulativeCF = 0;
            let paybackPeriod = 0;
            let totalInflow = 0;

            cashFlows.forEach((cf, index) => {
                const year = index + 1;
                const pv = cf / Math.pow(1 + discountRate, year);
                npv += pv;
                totalInflow += cf;

                // Payback period
                cumulativeCF += cf;
                if (paybackPeriod === 0 && cumulativeCF >= initialInvestment) {
                    paybackPeriod = year;
                }
            });

            document.getElementById('npv-value').textContent = 'Rs. ' + npv.toLocaleString(undefined, {maximumFractionDigits: 2});
            document.getElementById('npv-value').style.color = npv >= 0 ? '#38ef7d' : '#f5576c';
            document.getElementById('npv-verdict').textContent = npv >= 0 ? '✓ Project is viable' : '✗ Project not recommended';
            document.getElementById('payback-period').textContent = paybackPeriod > 0 ? paybackPeriod + ' years' : 'N/A';
            document.getElementById('total-inflow').textContent = 'Rs. ' + totalInflow.toLocaleString();
            document.getElementById('results').style.display = 'block';
        }
    </script>
</body>
</html>
