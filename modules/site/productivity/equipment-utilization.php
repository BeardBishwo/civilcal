<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Utilization Calculator - Site Tools</title>
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
                <span>Equipment Utilization Calculator</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card glass-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-tools me-2"></i>
                            Equipment Utilization Calculator
                        </h2>
                        <p class="card-text">Calculate equipment utilization rates, operating costs, and fleet efficiency for construction equipment</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5>Equipment Details</h5>
                                <div class="mb-3">
                                    <label for="equipmentType" class="form-label">Equipment Type</label>
                                    <select class="form-select" id="equipmentType">
                                        <option value="">Select equipment</option>
                                        <option value="excavator">Excavator</option>
                                        <option value="bulldozer">Bulldozer</option>
                                        <option value="loader">Wheel Loader</option>
                                        <option value="grader">Motor Grader</option>
                                        <option value="compactor">Compactor</option>
                                        <option value="crane">Mobile Crane</option>
                                        <option value="forklift">Forklift</option>
                                        <option value="generator">Generator</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="purchaseCost" class="form-label">Purchase Cost ($)</label>
                                    <input type="number" class="form-control" id="purchaseCost" step="0.01" min="0" placeholder="Equipment purchase price">
                                </div>
                                <div class="mb-3">
                                    <label for="expectedLife" class="form-label">Expected Life (years)</label>
                                    <input type="number" class="form-control" id="expectedLife" step="1" min="1" placeholder="Typical 5-15 years">
                                </div>
                                <div class="mb-3">
                                    <label for="dailyOperatingCost" class="form-label">Daily Operating Cost ($)</label>
                                    <input type="number" class="form-control" id="dailyOperatingCost" step="0.01" min="0" placeholder="Fuel, maintenance, operator">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h5>Operating Parameters</h5>
                                <div class="mb-3">
                                    <label for="availableDays" class="form-label">Available Days per Year</label>
                                    <input type="number" class="form-control" id="availableDays" step="1" min="0" placeholder="Typically 250-300 days">
                                </div>
                                <div class="mb-3">
                                    <label for="actualOperatingDays" class="form-label">Actual Operating Days</label>
                                    <input type="number" class="form-control" id="actualOperatingDays" step="1" min="0" placeholder="Days actually worked">
                                </div>
                                <div class="mb-3">
                                    <label for="dailyHours" class="form-label">Operating Hours per Day</label>
                                    <input type="number" class="form-control" id="dailyHours" step="0.1" min="0" placeholder="Typically 8-10 hours">
                                </div>
                                <div class="mb-3">
                                    <label for="downtimeHours" class="form-label">Downtime Hours per Year</label>
                                    <input type="number" class="form-control" id="downtimeHours" step="0.1" min="0" placeholder="Maintenance, repairs, weather">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" onclick="calculateEquipmentUtilization()">
                                    <i class="fas fa-calculator me-2"></i>
                                    Calculate Equipment Utilization
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
                            <h5>Equipment Utilization Analysis</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="result-item">
                                        <strong>Availability Factor:</strong>
                                        <span id="availabilityFactor"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Utilization Rate:</strong>
                                        <span id="utilizationRate"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Annual Operating Hours:</strong>
                                        <span id="annualHours"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="result-item">
                                        <strong>Cost per Hour:</strong>
                                        <span id="costPerHour"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Total Annual Cost:</strong>
                                        <span id="annualCost"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Return on Investment:</strong>
                                        <span id="roi"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6>Performance Metrics</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Depreciation:</strong><br>
                                        <span id="depreciation"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Break-Even Analysis:</strong><br>
                                        <span id="breakEven"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Efficiency Rating:</strong><br>
                                        <span id="efficiencyRating"></span>
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

        function calculateEquipmentUtilization() {
            const equipmentType = document.getElementById('equipmentType').value;
            const purchaseCost = parseFloat(document.getElementById('purchaseCost').value);
            const expectedLife = parseFloat(document.getElementById('expectedLife').value);
            const dailyOperatingCost = parseFloat(document.getElementById('dailyOperatingCost').value);
            const availableDays = parseFloat(document.getElementById('availableDays').value);
            const actualOperatingDays = parseFloat(document.getElementById('actualOperatingDays').value);
            const dailyHours = parseFloat(document.getElementById('dailyHours').value);
            const downtimeHours = parseFloat(document.getElementById('downtimeHours').value);
            
            if (!equipmentType || !purchaseCost || !expectedLife || !dailyOperatingCost || !availableDays) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Calculate equipment utilization parameters
            let availabilityFactor = 0;
            let utilizationRate = 0;
            let annualHours = 0;
            let costPerHour = 0;
            let annualCost = 0;
            let roi = 0;
            let depreciation = 0;
            let breakEven = 0;
            let efficiencyRating = '';
            
            // Calculate availability factor
            const totalAvailableHours = availableDays * dailyHours;
            const effectiveAvailableHours = totalAvailableHours - downtimeHours;
            availabilityFactor = (effectiveAvailableHours / totalAvailableHours) * 100;
            
            // Calculate utilization rate
            utilizationRate = (actualOperatingDays / availableDays) * 100;
            
            // Calculate annual operating hours
            annualHours = actualOperatingDays * dailyHours;
            
            // Calculate depreciation (straight line)
            depreciation = purchaseCost / expectedLife;
            
            // Calculate annual cost
            annualCost = (actualOperatingDays * dailyOperatingCost) + depreciation;
            
            // Calculate cost per hour
            costPerHour = annualHours > 0 ? annualCost / annualHours : 0;
            
            // Calculate ROI (assuming rental income or productivity value)
            let equipmentValue = 0;
            const equipmentValues = {
                excavator: 500,
                bulldozer: 600,
                loader: 450,
                grader: 550,
                compactor: 300,
                crane: 800,
                forklift: 200,
                generator: 150
            };
            
            equipmentValue = equipmentValues[equipmentType] || 400;
            const annualRevenue = annualHours * equipmentValue;
            roi = annualCost > 0 ? ((annualRevenue - annualCost) / annualCost) * 100 : 0;
            
            // Calculate break-even point
            const netAnnualRevenue = annualRevenue - (actualOperatingDays * dailyOperatingCost);
            breakEven = netAnnualRevenue > 0 ? depreciation / netAnnualRevenue : 0;
            
            // Determine efficiency rating
            if (utilizationRate >= 80) {
                efficiencyRating = 'Excellent (80%+)';
            } else if (utilizationRate >= 65) {
                efficiencyRating = 'Good (65-80%)';
            } else if (utilizationRate >= 50) {
                efficiencyRating = 'Fair (50-65%)';
            } else if (utilizationRate >= 35) {
                efficiencyRating = 'Poor (35-50%)';
            } else {
                efficiencyRating = 'Very Poor (<35%)';
            }
            
            // Display results
            document.getElementById('availabilityFactor').textContent = availabilityFactor.toFixed(1) + '%';
            document.getElementById('utilizationRate').textContent = utilizationRate.toFixed(1) + '%';
            document.getElementById('annualHours').textContent = annualHours.toFixed(0) + ' hours';
            document.getElementById('costPerHour').textContent = '$' + costPerHour.toFixed(2) + '/hour';
            document.getElementById('annualCost').textContent = '$' + annualCost.toLocaleString('en-US', {minimumFractionDigits: 0});
            document.getElementById('roi').textContent = roi.toFixed(1) + '%';
            document.getElementById('depreciation').textContent = '$' + depreciation.toLocaleString('en-US', {minimumFractionDigits: 0}) + '/year';
            document.getElementById('breakEven').textContent = breakEven.toFixed(1) + ' years';
            document.getElementById('efficiencyRating').textContent = efficiencyRating;
            
            document.getElementById('results').style.display = 'block';
            
            // Save to localStorage
            saveToLocalStorage('equipment_utilization', {
                equipmentType: equipmentType,
                purchaseCost: purchaseCost,
                expectedLife: expectedLife,
                dailyOperatingCost: dailyOperatingCost,
                availableDays: availableDays,
                actualOperatingDays: actualOperatingDays,
                dailyHours: dailyHours,
                downtimeHours: downtimeHours,
                results: {
                    availabilityFactor: availabilityFactor,
                    utilizationRate: utilizationRate,
                    annualHours: annualHours,
                    costPerHour: costPerHour,
                    annualCost: annualCost,
                    roi: roi,
                    depreciation: depreciation,
                    breakEven: breakEven,
                    efficiencyRating: efficiencyRating
                }
            });
        }

        function clearForm() {
            document.getElementById('equipmentType').value = '';
            document.getElementById('purchaseCost').value = '';
            document.getElementById('expectedLife').value = '';
            document.getElementById('dailyOperatingCost').value = '';
            document.getElementById('availableDays').value = '';
            document.getElementById('actualOperatingDays').value = '';
            document.getElementById('dailyHours').value = '';
            document.getElementById('downtimeHours').value = '';
            document.getElementById('results').style.display = 'none';
        }

        function saveCalculation() {
            const calculationData = {
                type: 'Equipment Utilization',
                timestamp: new Date().toISOString(),
                inputs: {
                    equipmentType: document.getElementById('equipmentType').value,
                    purchaseCost: document.getElementById('purchaseCost').value,
                    expectedLife: document.getElementById('expectedLife').value,
                    dailyOperatingCost: document.getElementById('dailyOperatingCost').value,
                    availableDays: document.getElementById('availableDays').value,
                    actualOperatingDays: document.getElementById('actualOperatingDays').value,
                    dailyHours: document.getElementById('dailyHours').value,
                    downtimeHours: document.getElementById('downtimeHours').value
                }
            };
            
            if (document.getElementById('results').style.display !== 'none') {
                calculationData.results = {
                    availabilityFactor: document.getElementById('availabilityFactor').textContent,
                    utilizationRate: document.getElementById('utilizationRate').textContent,
                    annualHours: document.getElementById('annualHours').textContent,
                    costPerHour: document.getElementById('costPerHour').textContent,
                    annualCost: document.getElementById('annualCost').textContent,
                    roi: document.getElementById('roi').textContent,
                    depreciation: document.getElementById('depreciation').textContent,
                    breakEven: document.getElementById('breakEven').textContent,
                    efficiencyRating: document.getElementById('efficiencyRating').textContent
                };
            }
            
            saveToLocalStorage('saved_calculations', calculationData);
            alert('Calculation saved successfully!');
        }

        // Load saved data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFromLocalStorage('equipment_utilization');
        });
    </script>
</body>
</html>
