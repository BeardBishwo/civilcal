<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cut-Fill Balancing Calculator - Site Tools</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --glass-bg: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            line-height: 1.6;
        }

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }

        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 15px;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
        }

        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 0 15px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1.5rem;
            border-radius: 15px 15px 0 0;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .card-text {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-footer {
            background: rgba(255, 255, 255, 0.05);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 1.5rem;
            border-radius: 0 0 15px 15px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--primary-color);
        }

        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.9);
            color: var(--dark-color);
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-block {
            width: 100%;
        }

        .btn-info {
            background: var(--info-color);
            color: white;
        }

        .btn-info:hover {
            background: #138496;
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .alert-info {
            background: rgba(23, 162, 184, 0.2);
            border: 1px solid rgba(23, 162, 184, 0.3);
            color: white;
        }

        h5 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        h6 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        p {
            margin-bottom: 0.5rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 3px;
        }

        .badge-success {
            background: var(--success-color);
            color: white;
        }

        .badge-warning {
            background: var(--warning-color);
            color: var(--dark-color);
        }

        .badge-danger {
            background: var(--danger-color);
            color: white;
        }

        .list-unstyled {
            list-style: none;
            padding-left: 0;
        }

        .small {
            font-size: 0.875rem;
        }

        .text-center {
            text-align: center;
        }

        .text-warning {
            color: var(--warning-color);
        }

        .text-success {
            color: var(--success-color);
        }

        .text-info {
            color: var(--info-color);
        }

        .fa-2x {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .border-warning {
            border: 1px solid var(--warning-color);
        }

        .border-success {
            border: 1px solid var(--success-color);
        }

        .border-info {
            border: 1px solid var(--info-color);
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .col-md-6, .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .row {
                margin: 0 -10px;
            }
            
            .col-12, .col-md-6, .col-md-4 {
                padding: 0 10px;
            }
            
            .glass-card, .card {
                padding: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card glass-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-balance-scale"></i>
                            Cut-Fill Balancing Calculator
                        </h2>
                        <p class="card-text">Calculate optimal cut and fill balance for earthwork projects</p>
                        <div class="mt-3">
                            <a href="../../index.php" class="btn btn-info">
                                <i class="fas fa-arrow-left"></i> Back to Site Tools
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="cutFillForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="totalCut">Total Cut Volume (BCY)</label>
                                        <input type="number" class="form-control" id="totalCut" step="0.1" placeholder="5000" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="totalFill">Total Fill Volume (BCY)</label>
                                        <input type="number" class="form-control" id="totalFill" step="0.1" placeholder="4500" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="swellFactor">Swell Factor</label>
                                        <input type="number" class="form-control" id="swellFactor" step="0.01" min="1" value="1.25" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="shrinkFactor">Shrink Factor</label>
                                        <input type="number" class="form-control" id="shrinkFactor" step="0.01" min="0.5" max="1" value="0.90" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="wasteFactor">Waste Factor (%)</label>
                                        <input type="number" class="form-control" id="wasteFactor" step="0.1" value="5" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="haulDistance">Average Haul Distance (ft)</label>
                                        <input type="number" class="form-control" id="haulDistance" step="1" placeholder="1000">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="importCost">Import Cost ($/BCY)</label>
                                        <input type="number" class="form-control" id="importCost" step="0.01" placeholder="15.00">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-calculator"></i> Calculate Balance
                            </button>
                        </form>

                        <div id="results" class="mt-4" style="display: none;">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-chart-line"></i> Cut-Fill Balance Results</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Net Balance:</strong> <span id="netBalance">0</span> BCY</p>
                                        <p><strong>Adjusted Cut:</strong> <span id="adjustedCut">0</span> BCY</p>
                                        <p><strong>Adjusted Fill:</strong> <span id="adjustedFill">0</span> BCY</p>
                                        <p><strong>Balance Status:</strong> <span id="balanceStatus" class="badge">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Haul Cost:</strong> $<span id="haulCost">0</span></p>
                                        <p><strong>Import/Export Cost:</strong> $<span id="materialCost">0</span></p>
                                        <p><strong>Total Cost Impact:</strong> $<span id="totalCostImpact">0</span></p>
                                        <p><strong>Efficiency Rating:</strong> <span id="efficiencyRating">0</span>%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card glass-card mt-3">
        <div class="card-header">
            <h5><i class="fas fa-info-circle"></i> Balance Guidelines</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Typical Volume Adjustment Factors</h6>
                    <ul class="list-unstyled">
                        <li>• Sand: Swell 1.10, Shrink 0.95</li>
                        <li>• Clay: Swell 1.30, Shrink 0.85</li>
                        <li>• Gravel: Swell 1.15, Shrink 0.90</li>
                        <li>• Rock: Swell 1.50, Shrink 0.75</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Balance Optimization Tips</h6>
                    <ul class="list-unstyled">
                        <li>• Target ±5% balance for efficiency</li>
                        <li>• Minimize haul distances when possible</li>
                        <li>• Consider staging areas for temporary storage</li>
                        <li>• Account for weather and seasonal factors</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card glass-card mt-3">
        <div class="card-header">
            <h5><i class="fas fa-tools"></i> Quick Balance Recommendations</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                            <h6 class="mt-2">Excess Cut (>10%)</h6>
                            <p>Consider using excess material for:</p>
                            <ul class="list-unstyled small">
                                <li>• Site grading improvements</li>
                                <li>• Landscape berms</li>
                                <li>• Roadway approaches</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle text-success fa-2x"></i>
                            <h6 class="mt-2">Well Balanced (±5%)</h6>
                            <p>Optimal earthwork balance achieved:</p>
                            <ul class="list-unstyled small">
                                <li>• Minimal hauling costs</li>
                                <li>• Efficient equipment usage</li>
                                <li>• Reduced schedule impacts</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-info-circle text-info fa-2x"></i>
                            <h6 class="mt-2">Excess Fill (>10%)</h6>
                            <p>Consider importing options:</p>
                            <ul class="list-unstyled small">
                                <li>• Nearby borrow sources</li>
                                <li>• Crushed stone alternatives</li>
                                <li>• Recycled materials</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('cutFillForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const totalCut = parseFloat(document.getElementById('totalCut').value);
            const totalFill = parseFloat(document.getElementById('totalFill').value);
            const swellFactor = parseFloat(document.getElementById('swellFactor').value);
            const shrinkFactor = parseFloat(document.getElementById('shrinkFactor').value);
            const wasteFactor = parseFloat(document.getElementById('wasteFactor').value) / 100;
            const haulDistance = parseFloat(document.getElementById('haulDistance').value) || 0;
            const importCost = parseFloat(document.getElementById('importCost').value) || 0;
            
            // Calculate adjusted volumes
            const adjustedCut = totalCut * swellFactor;
            const adjustedFill = totalFill / shrinkFactor;
            
            // Calculate net balance
            const netBalance = adjustedCut - adjustedFill;
            const balancePercent = (netBalance / Math.max(totalCut, totalFill)) * 100;
            
            // Determine balance status and badge class
            let balanceStatus = '';
            let badgeClass = '';
            let efficiencyRating = 0;
            
            if (Math.abs(balancePercent) <= 5) {
                balanceStatus = 'Well Balanced';
                badgeClass = 'badge-success';
                efficiencyRating = 95;
            } else if (Math.abs(balancePercent) <= 10) {
                balanceStatus = 'Acceptable';
                badgeClass = 'badge-warning';
                efficiencyRating = 80;
            } else {
                balanceStatus = 'Needs Adjustment';
                badgeClass = 'badge-danger';
                efficiencyRating = 60;
            }
            
            // Calculate costs
            const wasteVolume = Math.abs(netBalance) * wasteFactor;
            const haulCost = haulDistance > 0 ? Math.abs(netBalance) * haulDistance * 0.05 : 0; // $0.05 per BCY per 1000ft
            const materialCost = netBalance > 0 ? wasteVolume * importCost : Math.abs(netBalance) * importCost;
            const totalCostImpact = haulCost + materialCost;
            
            // Display results
            document.getElementById('netBalance').textContent = Math.abs(netBalance).toFixed(1);
            document.getElementById('adjustedCut').textContent = adjustedCut.toFixed(1);
            document.getElementById('adjustedFill').textContent = adjustedFill.toFixed(1);
            
            const statusElement = document.getElementById('balanceStatus');
            statusElement.textContent = balanceStatus;
            statusElement.className = `badge ${badgeClass}`;
            
            document.getElementById('haulCost').textContent = haulCost.toFixed(2);
            document.getElementById('materialCost').textContent = materialCost.toFixed(2);
            document.getElementById('totalCostImpact').textContent = totalCostImpact.toFixed(2);
            document.getElementById('efficiencyRating').textContent = efficiencyRating;
            
            document.getElementById('results').style.display = 'block';
            
            // Save to localStorage
            const calculation = {
                type: 'Cut-Fill Balancing',
                timestamp: new Date().toISOString(),
                inputs: {
                    totalCut, totalFill, swellFactor, shrinkFactor, 
                    wasteFactor, haulDistance, importCost
                },
                results: {
                    netBalance, balancePercent, balanceStatus, haulCost, 
                    materialCost, totalCostImpact, efficiencyRating
                }
            };
            
            saveCalculation(calculation);
        });

        function saveCalculation(calculation) {
            let calculations = JSON.parse(localStorage.getItem('recentSiteCalculations') || '[]');
            calculations.unshift({
                type: calculation.type,
                calculation: `Cut: ${calculation.inputs.totalCut} BCY, Fill: ${calculation.inputs.totalFill} BCY`,
                timestamp: new Date().toLocaleString()
            });
            
            // Keep only last 10 calculations
            calculations = calculations.slice(0, 10);
            localStorage.setItem('recentSiteCalculations', JSON.stringify(calculations));
        }

        // Load saved calculations
        function loadCalculations(type) {
            const calculations = JSON.parse(localStorage.getItem('recentSiteCalculations') || '[]');
            const siteCalculations = calculations.filter(calc => calc.type === type);
            // Display recent calculations if needed
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
