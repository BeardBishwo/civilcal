<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Rate Calculator - Site Tools</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
            --light-bg: rgba(255, 255, 255, 0.95);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --border-color: rgba(255, 255, 255, 0.2);
        }

        body {
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: var(--light-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        .header h1 {
            color: var(--secondary-color);
            margin: 0 0 10px 0;
            font-size: 2.2rem;
            font-weight: 600;
        }

        .breadcrumb {
            color: #666;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .card {
            background: var(--light-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 25px;
            border-bottom: none;
        }

        .card-title {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 600;
        }

        .card-text {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
        }

        .card-body {
            padding: 25px;
        }

        .form-label {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn {
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 20px;
        }

        .alert-info {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            color: #2d3436;
        }

        .border-primary {
            border: 2px solid var(--primary-color) !important;
        }

        .border-warning {
            border: 2px solid var(--warning-color) !important;
        }

        .border-success {
            border: 2px solid var(--success-color) !important;
        }

        .border-info {
            border: 2px solid var(--info-color) !important;
        }

        .small {
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .card-body {
                padding: 20px;
            }
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container-fluid">
        <div class="header">
            <h1><i class="fas fa-tools me-3"></i>Site Tools</h1>
            <div class="breadcrumb">
                <a href="../../index.php">Home</a> / 
                <a href="../../modules/site/index.php">Site Tools</a> / 
                <span>Placement Rate</span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Placement Rate Calculator
                </h2>
                <p class="card-text">Calculate concrete placement rates and equipment requirements</p>
            </div>
            <div class="card-body">
                <form id="placementForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="placementMethod" class="form-label">Placement Method</label>
                                <select class="form-control" id="placementMethod" required>
                                    <option value="">Select Method</option>
                                    <option value="crane">Crane & Bucket</option>
                                    <option value="pump">Concrete Pump</option>
                                    <option value="chute">Chute Direct</option>
                                    <option value="tremie">Tremie</option>
                                    <option value="conveyor">Conveyor Belt</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="concreteVolume" class="form-label">Total Concrete Volume (CY)</label>
                                <input type="number" class="form-control" id="concreteVolume" step="0.1" placeholder="250" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pourArea" class="form-label">Pour Area (ft²)</label>
                                <input type="number" class="form-control" id="pourArea" step="1" placeholder="2000" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="layerThickness" class="form-label">Layer Thickness (in)</label>
                                <input type="number" class="form-control" id="layerThickness" step="0.5" placeholder="12" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="crewSize" class="form-label">Placement Crew Size</label>
                                <input type="number" class="form-control" id="crewSize" step="1" value="6" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="travelDistance" class="form-label">Equipment Travel Distance (ft)</label>
                                <input type="number" class="form-control" id="travelDistance" step="10" placeholder="200">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="accessDifficulty" class="form-label">Access Difficulty</label>
                                <select class="form-control" id="accessDifficulty">
                                    <option value="easy">Easy (Open Area)</option>
                                    <option value="moderate">Moderate (Some Obstacles)</option>
                                    <option value="difficult">Difficult (Constrained)</option>
                                    <option value="extreme">Extreme (Very Constrained)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-calculator"></i> Calculate Placement Rate
                    </button>
                </form>

                <div id="results" class="mt-4" style="display: none;">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-chart-line"></i> Placement Rate Results</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Volume per Layer:</strong> <span id="volumePerLayer">0</span> CY</p>
                                <p><strong>Base Placement Rate:</strong> <span id="baseRate">0</span> CY/hr</p>
                                <p><strong>Adjusted Rate:</strong> <span id="adjustedRate">0</span> CY/hr</p>
                                <p><strong>Estimated Duration:</strong> <span id="duration">0</span> hours</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Truck Requirements:</strong> <span id="truckReq">0</span> trucks</p>
                                <p><strong>Equipment Capacity:</strong> <span id="equipmentCapacity">0</span> CY/batch</p>
                                <p><strong>Delivery Interval:</strong> <span id="deliveryInterval">0</span> min</p>
                                <p><strong>Productivity Rating:</strong> <span id="productivityRating">0</span>%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Placement Rate Guidelines</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Base Placement Rates (CY/hr)</h6>
                        <ul class="list-unstyled">
                            <li>• Crane & Bucket: 30-50 CY/hr</li>
                            <li>• Concrete Pump: 80-120 CY/hr</li>
                            <li>• Chute Direct: 25-40 CY/hr</li>
                            <li>• Tremie: 15-25 CY/hr</li>
                            <li>• Conveyor Belt: 40-60 CY/hr</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Placement Method Selection</h6>
                        <ul class="list-unstyled">
                            <li>• High-rise: Crane or pump required</li>
                            <li>• Foundations: Chute or pump efficient</li>
                            <li>• Underwater: Tremie method necessary</li>
                            <li>• Tunnels: Conveyor or pump preferred</li>
                            <li>• Slabs: Pump or crane commonly used</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-tools"></i> Rate Adjustment Factors</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6><i class="fas fa-layer-group"></i> Layer Thickness</h6>
                                <ul class="list-unstyled small">
                                    <li>• 6" or less: Reduce 20%</li>
                                    <li>• 8-12": Normal rate</li>
                                    <li>• 12-18": Increase 15%</li>
                                    <li>• 18"+: Increase 25%</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body">
                                <h6><i class="fas fa-users"></i> Crew Efficiency</h6>
                                <ul class="list-unstyled small">
                                    <li>• 3-4 workers: Reduce 30%</li>
                                    <li>• 5-6 workers: Normal rate</li>
                                    <li>• 7-8 workers: Increase 15%</li>
                                    <li>• 9+ workers: Increase 25%</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6><i class="fas fa-road"></i> Access Conditions</h6>
                                <ul class="list-unstyled small">
                                    <li>• Easy access: Normal rate</li>
                                    <li>• Moderate: Reduce 15%</li>
                                    <li>• Difficult: Reduce 30%</li>
                                    <li>• Extreme: Reduce 50%</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body">
                                <h6><i class="fas fa-clock"></i> Weather Impact</h6>
                                <ul class="list-unstyled small">
                                    <li>• Ideal weather: Normal rate</li>
                                    <li>• Hot/Cold: Reduce 10%</li>
                                    <li>• Rain: Reduce 25%</li>
                                    <li>• Wind: Reduce 15%</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-exclamation-triangle"></i> Critical Planning Notes</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-calendar-check"></i> Scheduling Considerations</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Equipment Requirements:</strong>
                            <ul>
                                <li>• Verify equipment capacity and reach</li>
                                <li>• Plan equipment mobilization time</li>
                                <li>• Consider backup equipment needs</li>
                                <li>• Schedule equipment inspections</li>
                                <li>• Ensure operator certifications</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Delivery Coordination:</strong>
                            <ul>
                                <li>• Match truck capacity to placement rate</li>
                                <li>• Plan truck staging areas</li>
                                <li>• Coordinate with traffic control</li>
                                <li>• Schedule deliveries around peak hours</li>
                                <li>• Maintain 1-hour concrete supply buffer</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function saveToLocalStorage(key, data) {
            try {
                localStorage.setItem(key, JSON.stringify(data));
            } catch (e) {
                console.log('Error saving to localStorage:', e);
            }
        }

        function loadFromLocalStorage(key) {
            try {
                const data = localStorage.getItem(key);
                return data ? JSON.parse(data) : null;
            } catch (e) {
                console.log('Error loading from localStorage:', e);
                return null;
            }
        }

        document.getElementById('placementForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const placementMethod = document.getElementById('placementMethod').value;
            const concreteVolume = parseFloat(document.getElementById('concreteVolume').value);
            const pourArea = parseFloat(document.getElementById('pourArea').value);
            const layerThickness = parseFloat(document.getElementById('layerThickness').value);
            const crewSize = parseFloat(document.getElementById('crewSize').value);
            const travelDistance = parseFloat(document.getElementById('travelDistance').value) || 0;
            const accessDifficulty = document.getElementById('accessDifficulty').value;
            
            // Base placement rates by method
            const baseRates = {
                'crane': 40,      // CY/hr
                'pump': 100,      // CY/hr
                'chute': 35,      // CY/hr
                'tremie': 20,     // CY/hr
                'conveyor': 50    // CY/hr
            };
            
            // Equipment capacities
            const equipmentCapacities = {
                'crane': 4,       // CY/bucket
                'pump': 8,        // CY/load
                'chute': 10,      // CY/load
                'tremie': 6,      // CY/load
                'conveyor': 3     // CY/batch
            };
            
            const baseRate = baseRates[placementMethod] || 40;
            const volumePerLayer = pourArea * (layerThickness / 12) / 27; // Convert to CY
            
            // Calculate adjustments
            let adjustedRate = baseRate;
            
            // Layer thickness adjustment
            if (layerThickness <= 6) {
                adjustedRate *= 0.8; // Reduce 20%
            } else if (layerThickness >= 18) {
                adjustedRate *= 1.25; // Increase 25%
            } else if (layerThickness >= 12) {
                adjustedRate *= 1.15; // Increase 15%
            }
            
            // Crew size adjustment
            if (crewSize <= 4) {
                adjustedRate *= 0.7; // Reduce 30%
            } else if (crewSize >= 9) {
                adjustedRate *= 1.25; // Increase 25%
            } else if (crewSize >= 7) {
                adjustedRate *= 1.15; // Increase 15%
            }
            
            // Access difficulty adjustment
            switch(accessDifficulty) {
                case 'moderate':
                    adjustedRate *= 0.85; // Reduce 15%
                    break;
                case 'difficult':
                    adjustedRate *= 0.7; // Reduce 30%
                    break;
                case 'extreme':
                    adjustedRate *= 0.5; // Reduce 50%
                    break;
            }
            
            // Travel distance penalty (for crane/pump)
            if (travelDistance > 100) {
                const travelPenalty = Math.min(travelDistance / 1000, 0.3); // Max 30% penalty
                adjustedRate *= (1 - travelPenalty);
            }
            
            // Calculate duration and equipment requirements
            const duration = concreteVolume / adjustedRate;
            const equipmentCapacity = equipmentCapacities[placementMethod] || 8;
            const truckCapacity = Math.min(equipmentCapacity * 1.2, 10); // Typical truck capacity
            
            // Calculate truck requirements and delivery interval
            const truckReq = Math.ceil(truckCapacity / equipmentCapacity);
            const deliveryInterval = (truckCapacity / adjustedRate) * 60; // Minutes between deliveries
            const productivityRating = Math.min((adjustedRate / baseRate) * 100, 120); // Max 120%
            
            // Display results
            document.getElementById('volumePerLayer').textContent = volumePerLayer.toFixed(1);
            document.getElementById('baseRate').textContent = baseRate.toFixed(0);
            document.getElementById('adjustedRate').textContent = adjustedRate.toFixed(0);
            document.getElementById('duration').textContent = duration.toFixed(1);
            document.getElementById('truckReq').textContent = truckReq;
            document.getElementById('equipmentCapacity').textContent = equipmentCapacity.toFixed(1);
            document.getElementById('deliveryInterval').textContent = Math.round(deliveryInterval);
            document.getElementById('productivityRating').textContent = Math.round(productivityRating);
            
            document.getElementById('results').style.display = 'block';
            
            // Save to localStorage
            const calculation = {
                type: 'Placement Rate',
                timestamp: new Date().toISOString(),
                inputs: {
                    placementMethod, concreteVolume, pourArea, layerThickness, 
                    crewSize, travelDistance, accessDifficulty
                },
                results: {
                    volumePerLayer, baseRate, adjustedRate, duration, 
                    truckReq, equipmentCapacity, deliveryInterval, productivityRating
                }
            };
            
            saveCalculation(calculation);
        });

        function saveCalculation(calculation) {
            const history = JSON.parse(localStorage.getItem('calculationHistory') || '[]');
            const entry = {
                type: calculation.type,
                timestamp: calculation.timestamp,
                data: calculation
            };
            history.unshift(entry);
            if (history.length > 10) history.pop();
            localStorage.setItem('calculationHistory', JSON.stringify(history));
        }

        // Load saved calculations
        function loadCalculations(type) {
            const history = JSON.parse(localStorage.getItem('calculationHistory') || '[]');
            const calculations = history.filter(entry => entry.data.type === type);
            console.log('Loaded ' + calculations.length + ' calculations for ' + type);
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
