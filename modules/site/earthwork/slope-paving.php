<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slope Paving Calculator - Site Tools</title>
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

        select.form-control {
            cursor: pointer;
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

        .btn-info {
            background: var(--info-color);
            color: white;
            margin-top: 1rem;
            display: inline-block;
        }

        .btn-info:hover {
            background: #138496;
        }

        .btn-block {
            width: 100%;
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

        .border-primary {
            border: 1px solid var(--primary-color);
        }

        .border-success {
            border: 1px solid var(--success-color);
        }

        .border-warning {
            border: 1px solid var(--warning-color);
        }

        .fa-concrete-bag, .fa-road, .fa-mountain {
            margin-right: 0.5rem;
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
                            <i class="fas fa-mountain"></i>
                            Slope Paving Calculator
                        </h2>
                        <p class="card-text">Calculate materials and costs for slope protection paving</p>
                        <a href="../../index.php" class="btn btn-info">
                            <i class="fas fa-arrow-left"></i> Back to Site Tools
                        </a>
                    </div>
                    <div class="card-body">
                        <form id="slopePavingForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="slopeHeight">Slope Height (ft)</label>
                                        <input type="number" class="form-control" id="slopeHeight" step="0.1" placeholder="25" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="slopeLength">Slope Length (ft)</label>
                                        <input type="number" class="form-control" id="slopeLength" step="0.1" placeholder="100" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="slopeRatio">Slope Ratio (H:V)</label>
                                        <input type="text" class="form-control" id="slopeRatio" placeholder="2:1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pavingType">Paving Type</label>
                                        <select class="form-control" id="pavingType" required>
                                            <option value="">Select Type</option>
                                            <option value="concrete">Concrete Slab</option>
                                            <option value="shotcrete">Shotcrete</option>
                                            <option value="asphalt">Asphalt Paving</option>
                                            <option value="riprap">Rock Riprap</option>
                                            <option value="gabion">Gabion Basket</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="thickness">Paving Thickness (in)</label>
                                        <input type="number" class="form-control" id="thickness" step="0.5" placeholder="6" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="overlapFactor">Overlap Factor (%)</label>
                                        <input type="number" class="form-control" id="overlapFactor" step="1" value="10" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="wasteFactor">Waste Factor (%)</label>
                                        <input type="number" class="form-control" id="wasteFactor" step="1" value="5" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-calculator"></i> Calculate Materials
                            </button>
                        </form>

                        <div id="results" class="mt-4" style="display: none;">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-chart-line"></i> Slope Paving Results</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Slope Area:</strong> <span id="slopeArea">0</span> ft²</p>
                                        <p><strong>Actual Area:</strong> <span id="actualArea">0</span> ft²</p>
                                        <p><strong>Material Volume:</strong> <span id="materialVolume">0</span> CY</p>
                                        <p><strong>Concrete/Shotcrete:</strong> <span id="concreteVolume">0</span> CY</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Reinforcement:</strong> <span id="reinforcement">0</span> lbs</p>
                                        <p><strong>Base Material:</strong> <span id="baseMaterial">0</span> CY</p>
                                        <p><strong>Total Cost:</strong> $<span id="totalCost">0</span></p>
                                        <p><strong>Cost per ft²:</strong> $<span id="costPerSqFt">0</span></p>
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
            <h5><i class="fas fa-info-circle"></i> Paving Specifications</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Typical Thickness by Type</h6>
                    <ul class="list-unstyled">
                        <li>• Concrete Slab: 6-8 inches</li>
                        <li>• Shotcrete: 4-6 inches</li>
                        <li>• Asphalt Paving: 3-4 inches</li>
                        <li>• Rock Riprap: 12-24 inches</li>
                        <li>• Gabion Basket: 18-36 inches</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Slope Stability Guidelines</h6>
                    <ul class="list-unstyled">
                        <li>• Gentle slopes (2:1-3:1): 2-4 feet height</li>
                        <li>• Moderate slopes (1.5:1-2:1): 4-8 feet height</li>
                        <li>• Steep slopes (1:1-1.5:1): 8-15 feet height</li>
                        <li>• Very steep (>1:1): Special design required</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card glass-card mt-3">
        <div class="card-header">
            <h5><i class="fas fa-tools"></i> Installation Tips</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6><i class="fas fa-concrete-bag"></i> Concrete/Shotcrete</h6>
                            <ul class="list-unstyled small">
                                <li>• Ensure proper drainage behind paving</li>
                                <li>• Include control joints every 10-15 ft</li>
                                <li>• Use #4 rebar @ 12" o.c. both ways</li>
                                <li>• Apply curing compound immediately</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6><i class="fas fa-road"></i> Asphalt Paving</h6>
                            <ul class="list-unstyled small">
                                <li>• Prime and tack coat base course</li>
                                <li>• Compact in 2-3 inch lifts</li>
                                <li>• Maintain 250°F during placement</li>
                                <li>• Allow 24-48 hours before opening</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h6><i class="fas fa-mountain"></i> Rock Riprap</h6>
                            <ul class="list-unstyled small">
                                <li>• Filter fabric underlayment required</li>
                                <li>• 3:1 slope maximum for stability</li>
                                <li>• Key rocks into slope toe</li>
                                <li>• Use angular, well-graded stone</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('slopePavingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const slopeHeight = parseFloat(document.getElementById('slopeHeight').value);
            const slopeLength = parseFloat(document.getElementById('slopeLength').value);
            const slopeRatio = document.getElementById('slopeRatio').value;
            const pavingType = document.getElementById('pavingType').value;
            const thickness = parseFloat(document.getElementById('thickness').value);
            const overlapFactor = parseFloat(document.getElementById('overlapFactor').value) / 100;
            const wasteFactor = parseFloat(document.getElementById('wasteFactor').value) / 100;
            
            // Parse slope ratio (H:V)
            const ratioParts = slopeRatio.split(':');
            const horizontalRatio = parseFloat(ratioParts[0]);
            const verticalRatio = parseFloat(ratioParts[1]);
            
            // Calculate actual slope length (hypotenuse)
            const slopeDistance = Math.sqrt(Math.pow(slopeHeight * horizontalRatio / verticalRatio, 2) + Math.pow(slopeHeight, 2));
            
            // Calculate areas
            const slopeArea = slopeLength * slopeDistance;
            const actualArea = slopeArea * (1 + overlapFactor + wasteFactor);
            
            // Calculate volumes based on paving type
            let materialVolume = 0;
            let reinforcement = 0;
            let baseMaterial = 0;
            let costPerSqFt = 0;
            
            const thicknessInFeet = thickness / 12;
            
            switch(pavingType) {
                case 'concrete':
                    materialVolume = actualArea * thicknessInFeet / 27; // Convert to CY
                    reinforcement = actualArea * 0.5; // lbs per sq ft
                    costPerSqFt = 8.50;
                    baseMaterial = actualArea * 0.25; // 3" base course
                    break;
                case 'shotcrete':
                    materialVolume = actualArea * thicknessInFeet / 27;
                    reinforcement = actualArea * 0.75; // Higher rebar density
                    costPerSqFt = 12.00;
                    baseMaterial = actualArea * 0.25;
                    break;
                case 'asphalt':
                    materialVolume = actualArea * thicknessInFeet / 27;
                    reinforcement = 0; // No rebar typically
                    costPerSqFt = 6.50;
                    baseMaterial = actualArea * 0.5; // 6" base course
                    break;
                case 'riprap':
                    materialVolume = actualArea * (thicknessInFeet * 1.5); // Void ratio consideration
                    reinforcement = 0;
                    costPerSqFt = 4.25;
                    baseMaterial = actualArea * 0.33; // 4" filter fabric/crushed stone
                    break;
                case 'gabion':
                    materialVolume = actualArea * (thicknessInFeet * 0.6); // Stone in baskets
                    reinforcement = 0;
                    costPerSqFt = 15.00;
                    baseMaterial = actualArea * 0.25;
                    break;
            }
            
            const concreteVolume = (pavingType === 'concrete' || pavingType === 'shotcrete') ? materialVolume : 0;
            const totalCost = actualArea * costPerSqFt;
            const costPerSqFtCalculated = totalCost / actualArea;
            
            // Display results
            document.getElementById('slopeArea').textContent = slopeArea.toFixed(0);
            document.getElementById('actualArea').textContent = actualArea.toFixed(0);
            document.getElementById('materialVolume').textContent = materialVolume.toFixed(1);
            document.getElementById('concreteVolume').textContent = concreteVolume.toFixed(1);
            document.getElementById('reinforcement').textContent = reinforcement.toFixed(0);
            document.getElementById('baseMaterial').textContent = baseMaterial.toFixed(1);
            document.getElementById('totalCost').textContent = totalCost.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
            document.getElementById('costPerSqFt').textContent = costPerSqFtCalculated.toFixed(2);
            
            document.getElementById('results').style.display = 'block';
            
            // Save to localStorage
            const calculation = {
                type: 'Slope Paving',
                timestamp: new Date().toISOString(),
                inputs: {
                    slopeHeight, slopeLength, slopeRatio, pavingType, 
                    thickness, overlapFactor, wasteFactor
                },
                results: {
                    slopeArea, actualArea, materialVolume, concreteVolume, 
                    reinforcement, baseMaterial, totalCost, costPerSqFtCalculated
                }
            };
            
            saveCalculation(calculation);
        });

        function saveCalculation(calculation) {
            let calculations = JSON.parse(localStorage.getItem('recentSiteCalculations') || '[]');
            calculations.unshift({
                type: calculation.type,
                calculation: `${calculation.inputs.pavingType}: ${calculation.inputs.slopeHeight}ft x ${calculation.inputs.slopeLength}ft`,
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
