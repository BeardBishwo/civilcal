<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labor Productivity Calculator - Site Tools</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --background-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--background-gradient);
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.8);
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .breadcrumb a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb span {
            color: #666;
            font-weight: 500;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .card-text {
            color: #666;
            margin-bottom: 0;
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
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            background: rgba(255, 255, 255, 1);
        }

        .btn {
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), #2980b9);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #229954);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(149, 165, 166, 0.4);
        }

        .result-item {
            padding: 15px;
            margin-bottom: 10px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            border-left: 4px solid var(--secondary-color);
            transition: all 0.3s ease;
        }

        .result-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .result-item strong {
            color: var(--primary-color);
            display: block;
            margin-bottom: 5px;
        }

        #results {
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px;
            margin-top: 15px;
        }

        .h5, h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
        }

        .h6, h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 0 15px;
            }
            
            .header {
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .btn + .btn {
                margin-left: 0 !important;
                margin-top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="header">
            <h1><i class="fas fa-tools me-3"></i>Site Tools</h1>
            <div class="breadcrumb">
                <a href="../../index.php">Home</a> / 
                <a href="../../modules/site/index.php">Site Tools</a> / 
                <span>Labor Productivity Calculator</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card glass-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-users me-2"></i>
                            Labor Productivity Calculator
                        </h2>
                        <p class="card-text">Calculate labor productivity rates, crew efficiency, and workforce planning for construction projects</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5>Work Task Details</h5>
                                <div class="mb-3">
                                    <label for="workType" class="form-label">Work Type</label>
                                    <select class="form-select" id="workType">
                                        <option value="">Select work type</option>
                                        <option value="concrete">Concrete Work</option>
                                        <option value="framing">Framing</option>
                                        <option value="electrical">Electrical</option>
                                        <option value="plumbing">Plumbing</option>
                                        <option value="drywall">Drywall</option>
                                        <option value="painting">Painting</option>
                                        <option value="roofing">Roofing</option>
                                        <option value="flooring">Flooring</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="taskQuantity" class="form-label">Task Quantity</label>
                                    <input type="number" class="form-control" id="taskQuantity" step="0.01" min="0" placeholder="Amount of work">
                                </div>
                                <div class="mb-3">
                                    <label for="quantityUnit" class="form-label">Unit</label>
                                    <select class="form-select" id="quantityUnit">
                                        <option value="">Select unit</option>
                                        <option value="sqft">Square Feet</option>
                                        <option value="cubic">Cubic Yards</option>
                                        <option value="linear">Linear Feet</option>
                                        <option value="each">Each</option>
                                        <option value="sq">Square (100 sq ft)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="crewSize" class="form-label">Crew Size (workers)</label>
                                    <input type="number" class="form-control" id="crewSize" step="1" min="1" placeholder="Number of workers">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h5>Labor Parameters</h5>
                                <div class="mb-3">
                                    <label for="baseRate" class="form-label">Base Labor Rate ($/hour)</label>
                                    <input type="number" class="form-control" id="baseRate" step="0.01" min="0" placeholder="Base hourly rate">
                                </div>
                                <div class="mb-3">
                                    <label for="overhead" class="form-label">Overhead & Benefits (%)</label>
                                    <input type="number" class="form-control" id="overhead" step="0.1" min="0" placeholder="Typically 35-45%">
                                </div>
                                <div class="mb-3">
                                    <label for="efficiency" class="form-label">Crew Efficiency (%)</label>
                                    <input type="number" class="form-control" id="efficiency" step="1" min="1" max="150" placeholder="100% = standard">
                                </div>
                                <div class="mb-3">
                                    <label for="projectLocation" class="form-label">Project Location Factor</label>
                                    <select class="form-select" id="projectLocation">
                                        <option value="">Select location</option>
                                        <option value="urban">Urban (1.0)</option>
                                        <option value="suburban">Suburban (0.95)</option>
                                        <option value="rural">Rural (1.1)</option>
                                        <option value="remote">Remote (1.25)</option>
                                        <option value="harsh">Harsh Conditions (1.4)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" onclick="calculateLaborProductivity()">
                                    <i class="fas fa-calculator me-2"></i>
                                    Calculate Labor Productivity
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
                            <h5>Labor Productivity Analysis</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="result-item">
                                        <strong>Standard Productivity Rate:</strong>
                                        <span id="standardRate"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Adjusted Productivity Rate:</strong>
                                        <span id="adjustedRate"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Labor Hours Required:</strong>
                                        <span id="laborHours"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="result-item">
                                        <strong>Total Labor Cost:</strong>
                                        <span id="totalCost"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Cost per Unit:</strong>
                                        <span id="costPerUnit"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Productivity Index:</strong>
                                        <span id="productivityIndex"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6>Detailed Breakdown</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Base Labor Cost:</strong><br>
                                        <span id="baseCost"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Overhead Cost:</strong><br>
                                        <span id="overheadCost"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Location Adjustment:</strong><br>
                                        <span id="locationCost"></span>
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
                localStorage.setItem('aec_calc_' + key, JSON.stringify(data));
            } catch (e) {
                console.error('Error saving to localStorage:', e);
            }
        }

        function loadFromLocalStorage(key) {
            try {
                const data = localStorage.getItem('aec_calc_' + key);
                if (data) {
                    const parsed = JSON.parse(data);
                    // Populate form fields if they exist
                    Object.keys(parsed).forEach(fieldId => {
                        const element = document.getElementById(fieldId);
                        if (element && parsed[fieldId] !== null && parsed[fieldId] !== '') {
                            element.value = parsed[fieldId];
                        }
                    });
                }
            } catch (e) {
                console.error('Error loading from localStorage:', e);
            }
        }

        function getStandardProductivityRate(workType, unit) {
            const rates = {
                concrete: {
                    sqft: 25, // sq ft per hour
                    cubic: 2.5, // cubic yards per hour
                    each: 4
                },
                framing: {
                    sqft: 50,
                    linear: 100,
                    each: 6
                },
                electrical: {
                    sqft: 200,
                    each: 8
                },
                plumbing: {
                    sqft: 150,
                    linear: 80,
                    each: 6
                },
                drywall: {
                    sqft: 80,
                    each: 10
                },
                painting: {
                    sqft: 150,
                    each: 12
                },
                roofing: {
                    sqft: 100,
                    sq: 4
                },
                flooring: {
                    sqft: 120,
                    each: 8
                }
            };
            
            return rates[workType] && rates[workType][unit] ? rates[workType][unit] : 50;
        }

        function calculateLaborProductivity() {
            const workType = document.getElementById('workType').value;
            const taskQuantity = parseFloat(document.getElementById('taskQuantity').value);
            const quantityUnit = document.getElementById('quantityUnit').value;
            const crewSize = parseFloat(document.getElementById('crewSize').value);
            const baseRate = parseFloat(document.getElementById('baseRate').value);
            const overhead = parseFloat(document.getElementById('overhead').value) || 40;
            const efficiency = parseFloat(document.getElementById('efficiency').value) || 100;
            const projectLocation = document.getElementById('projectLocation').value;
            
            if (!workType || !taskQuantity || !crewSize || !baseRate || !quantityUnit) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Calculate labor parameters
            let standardRate = 0;
            let adjustedRate = 0;
            let laborHours = 0;
            let totalCost = 0;
            let costPerUnit = 0;
            let productivityIndex = 0;
            let baseCost = 0;
            let overheadCost = 0;
            let locationCost = 0;
            
            // Get standard productivity rate
            standardRate = getStandardProductivityRate(workType, quantityUnit);
            
            // Calculate adjusted productivity rate based on efficiency
            adjustedRate = standardRate * (efficiency / 100);
            
            // Calculate labor hours required
            laborHours = taskQuantity / adjustedRate;
            
            // Calculate base labor cost
            baseCost = laborHours * baseRate * crewSize;
            
            // Calculate overhead cost
            overheadCost = baseCost * (overhead / 100);
            
            // Apply location factor
            const locationFactors = {
                urban: 1.0,
                suburban: 0.95,
                rural: 1.1,
                remote: 1.25,
                harsh: 1.4
            };
            
            const locationFactor = locationFactors[projectLocation] || 1.0;
            locationCost = baseCost * locationFactor;
            
            // Calculate total cost
            totalCost = baseCost + overheadCost + locationCost;
            
            // Calculate cost per unit
            costPerUnit = totalCost / taskQuantity;
            
            // Calculate productivity index (industry benchmark = 100)
            productivityIndex = (adjustedRate / standardRate) * 100;
            
            // Display results
            document.getElementById('standardRate').textContent = standardRate.toFixed(1) + ' ' + quantityUnit + '/hour';
            document.getElementById('adjustedRate').textContent = adjustedRate.toFixed(1) + ' ' + quantityUnit + '/hour';
            document.getElementById('laborHours').textContent = laborHours.toFixed(1) + ' hours';
            document.getElementById('totalCost').textContent = '$' + totalCost.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('costPerUnit').textContent = '$' + costPerUnit.toFixed(2) + ' per ' + quantityUnit;
            document.getElementById('productivityIndex').textContent = productivityIndex.toFixed(0);
            document.getElementById('baseCost').textContent = '$' + baseCost.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('overheadCost').textContent = '$' + overheadCost.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('locationCost').textContent = '$' + locationCost.toLocaleString('en-US', {minimumFractionDigits: 2});
            
            document.getElementById('results').style.display = 'block';
            
            // Save to localStorage
            saveToLocalStorage('labor_productivity', {
                workType: workType,
                taskQuantity: taskQuantity,
                quantityUnit: quantityUnit,
                crewSize: crewSize,
                baseRate: baseRate,
                overhead: overhead,
                efficiency: efficiency,
                projectLocation: projectLocation,
                results: {
                    standardRate: standardRate,
                    adjustedRate: adjustedRate,
                    laborHours: laborHours,
                    totalCost: totalCost,
                    costPerUnit: costPerUnit,
                    productivityIndex: productivityIndex,
                    baseCost: baseCost,
                    overheadCost: overheadCost,
                    locationCost: locationCost
                }
            });
        }

        function clearForm() {
            document.getElementById('workType').value = '';
            document.getElementById('taskQuantity').value = '';
            document.getElementById('quantityUnit').value = '';
            document.getElementById('crewSize').value = '';
            document.getElementById('baseRate').value = '';
            document.getElementById('overhead').value = '';
            document.getElementById('efficiency').value = '';
            document.getElementById('projectLocation').value = '';
            document.getElementById('results').style.display = 'none';
        }

        function saveCalculation() {
            const calculationData = {
                type: 'Labor Productivity',
                timestamp: new Date().toISOString(),
                inputs: {
                    workType: document.getElementById('workType').value,
                    taskQuantity: document.getElementById('taskQuantity').value,
                    quantityUnit: document.getElementById('quantityUnit').value,
                    crewSize: document.getElementById('crewSize').value,
                    baseRate: document.getElementById('baseRate').value,
                    overhead: document.getElementById('overhead').value,
                    efficiency: document.getElementById('efficiency').value,
                    projectLocation: document.getElementById('projectLocation').value
                }
            };
            
            if (document.getElementById('results').style.display !== 'none') {
                calculationData.results = {
                    standardRate: document.getElementById('standardRate').textContent,
                    adjustedRate: document.getElementById('adjustedRate').textContent,
                    laborHours: document.getElementById('laborHours').textContent,
                    totalCost: document.getElementById('totalCost').textContent,
                    costPerUnit: document.getElementById('costPerUnit').textContent,
                    productivityIndex: document.getElementById('productivityIndex').textContent,
                    baseCost: document.getElementById('baseCost').textContent,
                    overheadCost: document.getElementById('overheadCost').textContent,
                    locationCost: document.getElementById('locationCost').textContent
                };
            }
            
            saveToLocalStorage('saved_calculations', calculationData);
            alert('Calculation saved successfully!');
        }

        // Load saved data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFromLocalStorage('labor_productivity');
        });
    </script>
</body>
</html>
