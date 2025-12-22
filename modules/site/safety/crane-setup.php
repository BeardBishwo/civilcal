<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crane Setup Calculator - Site Tools</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
        }
        
        body {
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .header h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .breadcrumb {
            color: var(--secondary-color);
            font-size: 14px;
        }
        
        .breadcrumb a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            border: none;
            padding: 25px;
        }
        
        .card-title {
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .card-text {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn {
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
        }
        
        .btn-success {
            background: var(--success-color);
            border: none;
        }
        
        .result-item {
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        
        .result-item:last-child {
            border-bottom: none;
        }
        
        .result-item strong {
            color: var(--primary-color);
            margin-right: 8px;
        }
        
        .alert {
            margin-top: 15px;
            border-radius: 8px;
        }
        
        .alert-info {
            background: rgba(52, 152, 219, 0.1);
            border-color: var(--secondary-color);
            color: var(--primary-color);
        }
        
        .mt-4 {
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .container-fluid {
                padding: 10px;
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
                <span>Crane Setup Calculator</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card glass-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-truck-crane me-2"></i>
                            Crane Setup Calculator
                        </h2>
                        <p class="card-text">Calculate crane setup requirements, load charts, and safety parameters for construction lifting operations</p>
                    </div>
                    <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>Crane Specifications</h5>
                            <div class="mb-3">
                                <label for="craneCapacity" class="form-label">Crane Capacity (tons)</label>
                                <input type="number" class="form-control" id="craneCapacity" step="0.1" min="0" placeholder="Maximum crane capacity">
                            </div>
                            <div class="mb-3">
                                <label for="craneType" class="form-label">Crane Type</label>
                                <select class="form-select" id="craneType">
                                    <option value="">Select crane type</option>
                                    <option value="mobile">Mobile Crane</option>
                                    <option value="tower">Tower Crane</option>
                                    <option value="crawler">Crawler Crane</option>
                                    <option value="rough_terrain">Rough Terrain Crane</option>
                                    <option value="all_terrain">All Terrain Crane</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="boomLength" class="form-label">Boom Length (feet)</label>
                                <input type="number" class="form-control" id="boomLength" step="0.1" min="0" placeholder="Boom or jib length">
                            </div>
                            <div class="mb-3">
                                <label for="operatingRadius" class="form-label">Operating Radius (feet)</label>
                                <input type="number" class="form-control" id="operatingRadius" step="0.1" min="0" placeholder="Load radius from crane center">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h5>Load Details</h5>
                            <div class="mb-3">
                                <label for="loadWeight" class="form-label">Load Weight (lbs)</label>
                                <input type="number" class="form-control" id="loadWeight" step="0.1" min="0" placeholder="Total load weight">
                            </div>
                            <div class="mb-3">
                                <label for="loadRadius" class="form-label">Load Radius (feet)</label>
                                <input type="number" class="form-control" id="loadRadius" step="0.1" min="0" placeholder="Radius from crane to load">
                            </div>
                            <div class="mb-3">
                                <label for="liftHeight" class="form-label">Lift Height (feet)</label>
                                <input type="number" class="form-control" id="liftHeight" step="0.1" min="0" placeholder="Required lift height">
                            </div>
                            <div class="mb-3">
                                <label for="environmental" class="form-label">Environmental Conditions</label>
                                <select class="form-select" id="environmental">
                                    <option value="">Select conditions</option>
                                    <option value="calm">Calm (< 10 mph)</option>
                                    <option value="light">Light (10-15 mph)</option>
                                    <option value="moderate">Moderate (15-20 mph)</option>
                                    <option value="strong">Strong (20-25 mph)</option>
                                    <option value="high">High (> 25 mph)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="calculateCraneSetup()">
                                <i class="fas fa-calculator me-2"></i>
                                Calculate Crane Setup
                            </button>
                            <button type="button" class="btn btn-secondary ms-2" onclick="clearForm()">
                                <i class="fas fa-eraser me-2"></i>
                                Clear
                            </button>
                            <button type="button" class="btn btn-success ms-2" onclick="saveCalculation()">
                                <i class="fas fa-save me-2"></i>
                                Save
                            </button>
                        </div>
                    </div>
                    
                    <div id="results" class="mt-4" style="display: none;">
                        <h5>Crane Setup Analysis</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="result-item">
                                    <strong>Capacity Check:</strong>
                                    <span id="capacityCheck"></span>
                                </div>
                                <div class="result-item">
                                    <strong>Load Percentage:</strong>
                                    <span id="loadPercentage"></span>
                                </div>
                                <div class="result-item">
                                    <strong>Stability Margin:</strong>
                                    <span id="stabilityMargin"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="result-item">
                                    <strong>Wind Speed Limit:</strong>
                                    <span id="windLimit"></span>
                                </div>
                                <div class="result-item">
                                    <strong>Required Counterweight:</strong>
                                    <span id="counterweight"></span>
                                </div>
                                <div class="result-item">
                                    <strong>Safety Status:</strong>
                                    <span id="safetyStatus"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Setup Requirements</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Outrigger Spread:</strong><br>
                                    <span id="outriggerSpread"></span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Ground Pressure:</strong><br>
                                    <span id="groundPressure"></span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Rigging Required:</strong><br>
                                    <span id="riggingRequired"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        // Helper functions for localStorage
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
                if (data) {
                    const parsed = JSON.parse(data);
                    // Populate form fields if needed
                    console.log('Loaded data for', key, parsed);
                }
            } catch (e) {
                console.log('Error loading from localStorage:', e);
            }
        }
        
        function calculateCraneSetup() {
            const craneCapacity = parseFloat(document.getElementById('craneCapacity').value);
            const craneType = document.getElementById('craneType').value;
            const boomLength = parseFloat(document.getElementById('boomLength').value);
            const operatingRadius = parseFloat(document.getElementById('operatingRadius').value);
            const loadWeight = parseFloat(document.getElementById('loadWeight').value);
            const loadRadius = parseFloat(document.getElementById('loadRadius').value);
            const liftHeight = parseFloat(document.getElementById('liftHeight').value);
            const environmental = document.getElementById('environmental').value;
            
            if (!craneCapacity || !craneType || !loadWeight || !loadRadius) {
                showNotification('Please fill in all required fields', 'info');
                return;
            }
            
            // Calculate crane setup parameters
            let capacityCheck = '';
            let loadPercentage = 0;
            let stabilityMargin = 0;
            let windLimit = 0;
            let counterweight = 0;
            let safetyStatus = '';
            let outriggerSpread = '';
            let groundPressure = '';
            let riggingRequired = '';
            
            // Convert tons to pounds for comparison
            const maxLoadLbs = craneCapacity * 2000;
            
            // Calculate load percentage
            loadPercentage = (loadWeight / maxLoadLbs) * 100;
            
            // Check capacity
            if (loadWeight <= maxLoadLbs) {
                capacityCheck = 'Within Capacity';
                
                if (loadPercentage <= 75) {
                    safetyStatus = 'Safe - Good capacity margin';
                } else if (loadPercentage <= 85) {
                    safetyStatus = 'Caution - Monitor closely';
                } else {
                    safetyStatus = 'Warning - At maximum capacity';
                }
            } else {
                capacityCheck = 'EXCEEDS CAPACITY - UNSAFE';
                safetyStatus = 'UNSAFE - Reduce load or use larger crane';
            }
            
            // Calculate stability margin
            const boomMoment = craneCapacity * operatingRadius;
            const loadMoment = loadWeight * loadRadius;
            stabilityMargin = ((boomMoment - loadMoment) / boomMoment) * 100;
            
            // Determine wind limits
            switch(environmental) {
                case 'calm':
                    windLimit = '25 mph';
                    break;
                case 'light':
                    windLimit = '20 mph';
                    break;
                case 'moderate':
                    windLimit = '15 mph';
                    break;
                case 'strong':
                    windLimit = '10 mph';
                    break;
                case 'high':
                    windLimit = 'DO NOT OPERATE';
                    break;
                default:
                    windLimit = '15 mph';
            }
            
            // Calculate counterweight requirements
            if (craneType === 'mobile' || craneType === 'rough_terrain') {
                const counterweightRatio = 1.3; // 30% safety margin
                counterweight = (loadWeight * loadRadius * counterweightRatio) / 20; // Approximate formula
                outriggerSpread = `${(operatingRadius * 1.5).toFixed(1)} ft minimum`;
                groundPressure = `${(loadWeight / (operatingRadius * operatingRadius * 3.14)).toFixed(1)} psf`;
            } else if (craneType === 'tower') {
                counterweight = (loadWeight * loadRadius * 1.2) / 15;
                outriggerSpread = `${(boomLength * 0.6).toFixed(1)} ft minimum`;
                groundPressure = `${(counterweight * 1.5 / 100).toFixed(1)} ksf`;
            } else {
                counterweight = (loadWeight * loadRadius * 1.25) / 18;
                outriggerSpread = `${(operatingRadius * 1.3).toFixed(1)} ft minimum`;
                groundPressure = `${(loadWeight / (operatingRadius * 1.8)).toFixed(1)} psf`;
            }
            
            // Determine rigging requirements
            if (loadWeight <= 5000) {
                riggingRequired = 'Single part line, basic rigging';
            } else if (loadWeight <= 20000) {
                riggingRequired = 'Multi-part line, certified rigging required';
            } else {
                riggingRequired = 'Heavy lift rigging, certified rigger required';
            }
            
            // Display results
            document.getElementById('capacityCheck').textContent = capacityCheck;
            document.getElementById('loadPercentage').textContent = loadPercentage.toFixed(1) + '%';
            document.getElementById('stabilityMargin').textContent = stabilityMargin.toFixed(1) + '%';
            document.getElementById('windLimit').textContent = windLimit;
            document.getElementById('counterweight').textContent = counterweight.toFixed(0) + ' lbs';
            document.getElementById('safetyStatus').textContent = safetyStatus;
            document.getElementById('outriggerSpread').textContent = outriggerSpread;
            document.getElementById('groundPressure').textContent = groundPressure;
            document.getElementById('riggingRequired').textContent = riggingRequired;
            
            document.getElementById('results').style.display = 'block';
            
            // Save to localStorage
            saveToLocalStorage('crane_setup', {
                craneCapacity: craneCapacity,
                craneType: craneType,
                boomLength: boomLength,
                operatingRadius: operatingRadius,
                loadWeight: loadWeight,
                loadRadius: loadRadius,
                liftHeight: liftHeight,
                environmental: environmental,
                results: {
                    capacityCheck: capacityCheck,
                    loadPercentage: loadPercentage,
                    stabilityMargin: stabilityMargin,
                    windLimit: windLimit,
                    counterweight: counterweight,
                    safetyStatus: safetyStatus,
                    outriggerSpread: outriggerSpread,
                    groundPressure: groundPressure,
                    riggingRequired: riggingRequired
                }
            });
        }

        function clearForm() {
            document.getElementById('craneCapacity').value = '';
            document.getElementById('craneType').value = '';
            document.getElementById('boomLength').value = '';
            document.getElementById('operatingRadius').value = '';
            document.getElementById('loadWeight').value = '';
            document.getElementById('loadRadius').value = '';
            document.getElementById('liftHeight').value = '';
            document.getElementById('environmental').value = '';
            document.getElementById('results').style.display = 'none';
        }

        function saveCalculation() {
            const calculationData = {
                type: 'Crane Setup',
                timestamp: new Date().toISOString(),
                inputs: {
                    craneCapacity: document.getElementById('craneCapacity').value,
                    craneType: document.getElementById('craneType').value,
                    boomLength: document.getElementById('boomLength').value,
                    operatingRadius: document.getElementById('operatingRadius').value,
                    loadWeight: document.getElementById('loadWeight').value,
                    loadRadius: document.getElementById('loadRadius').value,
                    liftHeight: document.getElementById('liftHeight').value,
                    environmental: document.getElementById('environmental').value
                }
            };
            
            if (document.getElementById('results').style.display !== 'none') {
                calculationData.results = {
                    capacityCheck: document.getElementById('capacityCheck').textContent,
                    loadPercentage: document.getElementById('loadPercentage').textContent,
                    stabilityMargin: document.getElementById('stabilityMargin').textContent,
                    windLimit: document.getElementById('windLimit').textContent,
                    counterweight: document.getElementById('counterweight').textContent,
                    safetyStatus: document.getElementById('safetyStatus').textContent,
                    outriggerSpread: document.getElementById('outriggerSpread').textContent,
                    groundPressure: document.getElementById('groundPressure').textContent,
                    riggingRequired: document.getElementById('riggingRequired').textContent
                };
            }
            
            saveToLocalStorage('saved_calculations', calculationData);
            showNotification('Calculation saved successfully!', 'info');
        }

        // Load saved data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFromLocalStorage('crane_setup');
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
