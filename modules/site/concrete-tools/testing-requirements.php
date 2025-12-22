<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Requirements Calculator - Site Tools</title>
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

        .border-warning {
            border: 2px solid var(--warning-color) !important;
        }

        .border-success {
            border: 2px solid var(--success-color) !important;
        }

        .border-primary {
            border: 2px solid var(--primary-color) !important;
        }

        .border-danger {
            border: 2px solid var(--accent-color) !important;
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
                <span>Testing Requirements</span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-flask"></i>
                    Testing Requirements Calculator
                </h2>
                <p class="card-text">Calculate concrete testing frequency and sample requirements</p>
            </div>
            <div class="card-body">
                <form id="testingForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="structuralElement" class="form-label">Structural Element</label>
                                <select class="form-control" id="structuralElement" required>
                                    <option value="">Select Element</option>
                                    <option value="footings">Footings</option>
                                    <option value="beams">Beams</option>
                                    <option value="slabs">Slabs</option>
                                    <option value="columns">Columns</option>
                                    <option value="walls">Walls</option>
                                    <option value="pavements">Pavements</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="concreteVolume" class="form-label">Total Concrete Volume (CY)</label>
                                <input type="number" class="form-control" id="concreteVolume" step="0.1" placeholder="500" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fcRequirement" class="form-label">Required Strength (psi)</label>
                                <input type="number" class="form-control" id="fcRequirement" step="100" placeholder="3000" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="projectType" class="form-label">Project Type</label>
                                <select class="form-control" id="projectType" required>
                                    <option value="">Select Type</option>
                                    <option value="commercial">Commercial Building</option>
                                    <option value="residential">Residential</option>
                                    <option value="infrastructure">Infrastructure</option>
                                    <option value="industrial">Industrial</option>
                                    <option value="bridge">Bridge/Highway</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="testType" class="form-label">Testing Frequency</label>
                                <select class="form-control" id="testType" required>
                                    <option value="standard">Standard (1 per 50 CY)</option>
                                    <option value="enhanced">Enhanced (1 per 25 CY)</option>
                                    <option value="critical">Critical (1 per 10 CY)</option>
                                    <option value="special">Special Mix Design</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numberOfPours" class="form-label">Number of Placement Days</label>
                                <input type="number" class="form-control" id="numberOfPours" step="1" placeholder="5" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mixVariations" class="form-label">Number of Mix Designs</label>
                                <input type="number" class="form-control" id="mixVariations" step="1" value="1" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-calculator"></i> Calculate Testing Requirements
                    </button>
                </form>

                <div id="results" class="mt-4" style="display: none;">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-chart-line"></i> Testing Requirements Results</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Required Cylinder Sets:</strong> <span id="cylinderSets">0</span> sets</p>
                                <p><strong>Total Cylinder Count:</strong> <span id="cylinderCount">0</span> cylinders</p>
                                <p><strong>Compression Tests:</strong> <span id="compressionTests">0</span> tests</p>
                                <p><strong>7-Day Tests:</strong> <span id="sevenDayTests">0</span> tests</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>28-Day Tests:</strong> <span id="twentyEightDayTests">0</span> tests</p>
                                <p><strong>Slump Tests:</strong> <span id="slumpTests">0</span> tests</p>
                                <p><strong>Air Content Tests:</strong> <span id="airTests">0</span> tests</p>
                                <p><strong>Estimated Cost:</strong> $<span id="testingCost">0</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Testing Standards & Guidelines</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Standard Testing Requirements</h6>
                        <ul class="list-unstyled">
                            <li>• Minimum 1 set per 50 CY or fraction thereof</li>
                            <li>• 5 cylinders per set (4 tested, 1 hold)</li>
                            <li>• Test 2 cylinders at 7 days</li>
                            <li>• Test 2 cylinders at 28 days</li>
                            <li>• Air content test for exterior concrete</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Critical Elements Requiring Enhanced Testing</h6>
                        <ul class="list-unstyled">
                            <li>• Post-tensioned members</li>
                            <li>• Precast/prestressed elements</li>
                            <li>• Structural members > 12" thick</li>
                            <li>• High-strength concrete (>5000 psi)</li>
                            <li>• Specialized mixes or admixtures</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-tools"></i> Testing Schedule & Timeline</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body">
                                <h6><i class="fas fa-eye-dropper"></i> Fresh Concrete Tests</h6>
                                <ul class="list-unstyled small">
                                    <li>• Slump test each delivery</li>
                                    <li>• Air content (if required)</li>
                                    <li>• Temperature measurement</li>
                                    <li>• Unit weight (if specified)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6><i class="fas fa-vial"></i> Cylinder Curing</h6>
                                <ul class="list-unstyled small">
                                    <li>• Initial curing: Field conditions</li>
                                    <li>• Final curing: Lab or field cure</li>
                                    <li>• Temperature: 60-80°F</li>
                                    <li>• Maintain moisture content</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6><i class="fas fa-calendar"></i> Test Ages</h6>
                                <ul class="list-unstyled small">
                                    <li>• 7-day: Early strength indicator</li>
                                    <li>• 28-day: Design strength verification</li>
                                    <li>• 56-day: If specified</li>
                                    <li>• Hold specimens as needed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h6><i class="fas fa-chart-bar"></i> Acceptance Criteria</h6>
                                <ul class="list-unstyled small">
                                    <li>• Average of 3 consecutive tests ≥ f'c</li>
                                    <li>• Individual tests ≥ 0.90 f'c</li>
                                    <li>• Rapid test correlation if needed</li>
                                    <li>• Core testing for disputes</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-exclamation-triangle"></i> Compliance & Documentation</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-clipboard-list"></i> Required Documentation</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Pre-Placement Requirements:</strong>
                            <ul>
                                <li>• Mix design submittal approval</li>
                                <li>• Materials testing certificates</li>
                                <li>• QC/QA plan approval</li>
                                <li>• Technician certification verification</li>
                                <li>• Equipment calibration records</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Post-Placement Documentation:</strong>
                            <ul>
                                <li>• Daily placement reports</li>
                                <li>• Test results and trend analysis</li>
                                <li>• Cylinder identification logs</li>
                                <li>• Non-conformance reports</li>
                                <li>• Final acceptance certification</li>
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

        document.getElementById('testingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const structuralElement = document.getElementById('structuralElement').value;
            const concreteVolume = parseFloat(document.getElementById('concreteVolume').value);
            const fcRequirement = parseFloat(document.getElementById('fcRequirement').value);
            const projectType = document.getElementById('projectType').value;
            const testType = document.getElementById('testType').value;
            const numberOfPours = parseFloat(document.getElementById('numberOfPours').value);
            const mixVariations = parseFloat(document.getElementById('mixVariations').value);
            
            // Base testing frequency
            let cylindersPerSet = 5; // Standard: 4 tested + 1 hold
            let baseFrequency = 50; // Base: 1 set per 50 CY
            
            // Adjust based on test type
            switch(testType) {
                case 'enhanced':
                    baseFrequency = 25;
                    break;
                case 'critical':
                    baseFrequency = 10;
                    cylindersPerSet = 6; // More samples for critical work
                    break;
                case 'special':
                    baseFrequency = 25;
                    cylindersPerSet = 7; // Extra hold samples
                    break;
            }
            
            // Element-specific adjustments
            const elementMultipliers = {
                'footings': 1.0,
                'beams': 1.2,
                'slabs': 0.8,
                'columns': 1.5,
                'walls': 1.1,
                'pavements': 0.9
            };
            
            const elementMultiplier = elementMultipliers[structuralElement] || 1.0;
            
            // Calculate testing requirements
            const baseSets = Math.ceil(concreteVolume / baseFrequency);
            const adjustedSets = Math.ceil(baseSets * elementMultiplier * mixVariations);
            const cylinderSets = Math.max(adjustedSets, numberOfPours); // Minimum 1 set per pour day
            const cylinderCount = cylinderSets * cylindersPerSet;
            
            // Test distribution
            const compressionTests = cylinderSets * 4; // 4 cylinders tested per set
            const sevenDayTests = Math.floor(compressionTests * 0.4); // ~40% at 7 days
            const twentyEightDayTests = compressionTests - sevenDayTests; // Remainder at 28 days
            
            // Additional tests
            const slumpTests = Math.ceil(concreteVolume / 25); // 1 slump test per 25 CY
            const airTests = structuralElement !== 'footings' ? Math.ceil(concreteVolume / 100) : 0; // Air tests for exposed concrete
            
            // Calculate testing costs
            const cylinderTestCost = 25; // $25 per compression test
            const slumpTestCost = 15; // $15 per slump test
            const airTestCost = 20; // $20 per air test
            
            const totalCylinderCost = compressionTests * cylinderTestCost;
            const totalSlumpCost = slumpTests * slumpTestCost;
            const totalAirCost = airTests * airTestCost;
            const testingCost = totalCylinderCost + totalSlumpCost + totalAirCost;
            
            // Display results
            document.getElementById('cylinderSets').textContent = cylinderSets;
            document.getElementById('cylinderCount').textContent = cylinderCount;
            document.getElementById('compressionTests').textContent = compressionTests;
            document.getElementById('sevenDayTests').textContent = sevenDayTests;
            document.getElementById('twentyEightDayTests').textContent = twentyEightDayTests;
            document.getElementById('slumpTests').textContent = slumpTests;
            document.getElementById('airTests').textContent = airTests;
            document.getElementById('testingCost').textContent = testingCost.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
            
            document.getElementById('results').style.display = 'block';
            
            // Save to localStorage
            const calculation = {
                type: 'Testing Requirements',
                timestamp: new Date().toISOString(),
                inputs: {
                    structuralElement, concreteVolume, fcRequirement, 
                    projectType, testType, numberOfPours, mixVariations
                },
                results: {
                    cylinderSets, cylinderCount, compressionTests, sevenDayTests, 
                    twentyEightDayTests, slumpTests, airTests, testingCost
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
