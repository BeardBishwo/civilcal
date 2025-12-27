<?php $page_title = $title ?? 'Labor Rate Analysis'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); min-height: 100vh; padding: 40px 0; }
        .calculator-card { background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 900px; margin: 0 auto; }
        .card-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; }
        .result-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 20px; margin-top: 20px; }
        .result-value { font-size: 2rem; font-weight: 700; }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-card">
            <div class="card-header">
                <h2 class="mb-0"><i class="bi bi-people-fill me-2"></i><?php echo $page_title; ?></h2>
                <p class="mb-0 mt-2 opacity-75">Calculate labor costs based on productivity and crew composition</p>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Task Type</label>
                        <select id="task-type" class="form-select">
                            <option value="">-- Select Task --</option>
                            <option value="brickwork">Brickwork</option>
                            <option value="plastering">Plastering</option>
                            <option value="concrete">Concrete Pouring</option>
                            <option value="excavation">Excavation</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Productivity (units/day)</label>
                        <input type="number" id="productivity" class="form-control" placeholder="e.g., 10" step="0.01">
                    </div>
                </div>

                <h5 class="mb-3">Crew Composition</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Mason (Nos.)</label>
                        <input type="number" id="mason-count" class="form-control" value="1" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Helper (Nos.)</label>
                        <input type="number" id="helper-count" class="form-control" value="1" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Laborer (Nos.)</label>
                        <input type="number" id="laborer-count" class="form-control" value="2" min="0">
                    </div>
                </div>

                <h5 class="mb-3">Daily Wage Rates (Rs.)</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Mason Rate</label>
                        <input type="number" id="mason-rate" class="form-control" value="1500" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Helper Rate</label>
                        <input type="number" id="helper-rate" class="form-control" value="1000" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Laborer Rate</label>
                        <input type="number" id="laborer-rate" class="form-control" value="800" step="0.01">
                    </div>
                </div>

                <button class="btn btn-success btn-lg px-5" onclick="calculateLabor()">
                    <i class="bi bi-calculator me-2"></i>Calculate Labor Rate
                </button>

                <div id="results" style="display:none;">
                    <div class="result-card">
                        <h4 class="mb-2">Labor Rate per Unit</h4>
                        <div class="result-value" id="labor-rate">Rs. 0.00</div>
                        <div class="mt-3">
                            <small>Daily Crew Cost: Rs. <span id="crew-cost">0.00</span></small><br>
                            <small>Daily Output: <span id="daily-output">0</span> units</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateLabor() {
            const productivity = parseFloat(document.getElementById('productivity').value) || 0;
            const masonCount = parseFloat(document.getElementById('mason-count').value) || 0;
            const helperCount = parseFloat(document.getElementById('helper-count').value) || 0;
            const laborerCount = parseFloat(document.getElementById('laborer-count').value) || 0;
            const masonRate = parseFloat(document.getElementById('mason-rate').value) || 0;
            const helperRate = parseFloat(document.getElementById('helper-rate').value) || 0;
            const laborerRate = parseFloat(document.getElementById('laborer-rate').value) || 0;

            if (productivity === 0) {
                alert('Please enter productivity');
                return;
            }

            const crewCost = (masonCount * masonRate) + (helperCount * helperRate) + (laborerCount * laborerRate);
            const laborRatePerUnit = crewCost / productivity;

            document.getElementById('labor-rate').textContent = 'Rs. ' + laborRatePerUnit.toFixed(2);
            document.getElementById('crew-cost').textContent = crewCost.toFixed(2);
            document.getElementById('daily-output').textContent = productivity.toFixed(2);
            document.getElementById('results').style.display = 'block';
        }
    </script>
</body>
</html>
