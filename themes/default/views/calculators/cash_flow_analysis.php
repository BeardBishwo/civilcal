<?php $page_title = $title ?? 'Cash Flow Analysis'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); min-height: 100vh; padding: 40px 0; }
        .calculator-card { background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 1000px; margin: 0 auto; }
        .card-header { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; }
        .month-row { background: #f8f9fa; padding: 10px; border-radius: 8px; margin-bottom: 10px; }
        .result-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-card">
            <div class="card-header">
                <h2 class="mb-0"><i class="bi bi-graph-up me-2"></i><?php echo $page_title; ?></h2>
                <p class="mb-0 mt-2 opacity-75">Project monthly cash flow projections</p>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Number of Months</label>
                    <input type="number" id="num-months" class="form-control" value="12" min="1" max="60" onchange="generateMonths()">
                </div>

                <div id="months-container"></div>

                <button class="btn btn-primary btn-lg px-5 mt-3" onclick="calculateCashFlow()">
                    <i class="bi bi-calculator me-2"></i>Calculate Cash Flow
                </button>

                <div id="results" style="display:none;">
                    <div class="result-card">
                        <h4 class="mb-3">Cash Flow Summary</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Total Inflow</h6>
                                <h3 id="total-inflow">Rs. 0</h3>
                            </div>
                            <div class="col-md-4">
                                <h6>Total Outflow</h6>
                                <h3 id="total-outflow">Rs. 0</h3>
                            </div>
                            <div class="col-md-4">
                                <h6>Net Cash Flow</h6>
                                <h3 id="net-flow">Rs. 0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        generateMonths();

        function generateMonths() {
            const numMonths = parseInt(document.getElementById('num-months').value) || 12;
            const container = document.getElementById('months-container');
            container.innerHTML = '';

            for (let i = 1; i <= numMonths; i++) {
                const row = document.createElement('div');
                row.className = 'month-row';
                row.innerHTML = `
                    <div class="row align-items-center">
                        <div class="col-md-2"><strong>Month ${i}</strong></div>
                        <div class="col-md-5">
                            <input type="number" class="form-control form-control-sm inflow" placeholder="Inflow (Rs.)" step="0.01">
                        </div>
                        <div class="col-md-5">
                            <input type="number" class="form-control form-control-sm outflow" placeholder="Outflow (Rs.)" step="0.01">
                        </div>
                    </div>
                `;
                container.appendChild(row);
            }
        }

        function calculateCashFlow() {
            const inflows = document.querySelectorAll('.inflow');
            const outflows = document.querySelectorAll('.outflow');

            let totalInflow = 0;
            let totalOutflow = 0;

            inflows.forEach(input => {
                totalInflow += parseFloat(input.value) || 0;
            });

            outflows.forEach(input => {
                totalOutflow += parseFloat(input.value) || 0;
            });

            const netFlow = totalInflow - totalOutflow;

            document.getElementById('total-inflow').textContent = 'Rs. ' + totalInflow.toLocaleString();
            document.getElementById('total-outflow').textContent = 'Rs. ' + totalOutflow.toLocaleString();
            document.getElementById('net-flow').textContent = 'Rs. ' + netFlow.toLocaleString();
            document.getElementById('net-flow').style.color = netFlow >= 0 ? '#38ef7d' : '#f5576c';
            document.getElementById('results').style.display = 'block';
        }
    </script>
</body>
</html>
