<?php $page_title = $title ?? 'Equipment Hourly Rate'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 100vh; padding: 40px 0; }
        .calculator-card { background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 900px; margin: 0 auto; }
        .card-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; }
        .result-card { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; border-radius: 12px; padding: 20px; margin-top: 20px; }
        .result-value { font-size: 2rem; font-weight: 700; }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-card">
            <div class="card-header">
                <h2 class="mb-0"><i class="bi bi-truck me-2"></i><?php echo $page_title; ?></h2>
                <p class="mb-0 mt-2 opacity-75">Calculate equipment hourly rate including depreciation and operating costs</p>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Equipment Type</label>
                        <select id="equipment-type" class="form-select">
                            <option value="">-- Select Equipment --</option>
                            <option value="excavator">Excavator</option>
                            <option value="loader">Loader</option>
                            <option value="dozer">Dozer</option>
                            <option value="mixer">Concrete Mixer</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Purchase Price (Rs.)</label>
                        <input type="number" id="purchase-price" class="form-control" placeholder="e.g., 5000000" step="1">
                    </div>
                </div>

                <h5 class="mb-3">Depreciation Parameters</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Economic Life (years)</label>
                        <input type="number" id="economic-life" class="form-control" value="10" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Salvage Value (Rs.)</label>
                        <input type="number" id="salvage-value" class="form-control" value="500000" step="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Annual Working Hours</label>
                        <input type="number" id="annual-hours" class="form-control" value="2000" min="1">
                    </div>
                </div>

                <h5 class="mb-3">Operating Costs (per hour)</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Fuel Cost (Rs.)</label>
                        <input type="number" id="fuel-cost" class="form-control" value="500" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Maintenance (Rs.)</label>
                        <input type="number" id="maintenance-cost" class="form-control" value="200" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Operator Wage (Rs.)</label>
                        <input type="number" id="operator-wage" class="form-control" value="300" step="0.01">
                    </div>
                </div>

                <button class="btn btn-danger btn-lg px-5" onclick="calculateEquipment()">
                    <i class="bi bi-calculator me-2"></i>Calculate Hourly Rate
                </button>

                <div id="results" style="display:none;">
                    <div class="result-card">
                        <h4 class="mb-2">Equipment Hourly Rate</h4>
                        <div class="result-value" id="hourly-rate">Rs. 0.00</div>
                        <div class="mt-3">
                            <small>Depreciation/hr: Rs. <span id="depreciation-rate">0.00</span></small><br>
                            <small>Operating Cost/hr: Rs. <span id="operating-rate">0.00</span></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateEquipment() {
            const purchasePrice = parseFloat(document.getElementById('purchase-price').value) || 0;
            const economicLife = parseFloat(document.getElementById('economic-life').value) || 10;
            const salvageValue = parseFloat(document.getElementById('salvage-value').value) || 0;
            const annualHours = parseFloat(document.getElementById('annual-hours').value) || 2000;
            const fuelCost = parseFloat(document.getElementById('fuel-cost').value) || 0;
            const maintenanceCost = parseFloat(document.getElementById('maintenance-cost').value) || 0;
            const operatorWage = parseFloat(document.getElementById('operator-wage').value) || 0;

            if (purchasePrice === 0) {
                alert('Please enter purchase price');
                return;
            }

            // Depreciation per hour
            const totalDepreciation = purchasePrice - salvageValue;
            const totalLifeHours = economicLife * annualHours;
            const depreciationPerHour = totalDepreciation / totalLifeHours;

            // Operating cost per hour
            const operatingCostPerHour = fuelCost + maintenanceCost + operatorWage;

            // Total hourly rate
            const hourlyRate = depreciationPerHour + operatingCostPerHour;

            document.getElementById('hourly-rate').textContent = 'Rs. ' + hourlyRate.toFixed(2);
            document.getElementById('depreciation-rate').textContent = depreciationPerHour.toFixed(2);
            document.getElementById('operating-rate').textContent = operatingCostPerHour.toFixed(2);
            document.getElementById('results').style.display = 'block';
        }
    </script>
</body>
</html>
