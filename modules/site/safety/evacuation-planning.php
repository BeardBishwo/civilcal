<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evacuation Planning Calculator - Site Tools</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
</head>
<body>
    <div class="container-fluid">
        <div class="header">
            <h1><i class="fas fa-tools me-3"></i>Site Tools</h1>
            <div class="breadcrumb">
                <a href="../../index.php">Home</a> / 
                <a href="../../modules/site/index.php">Site Tools</a> / 
                <span>Evacuation Planning Calculator</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card glass-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-route me-2"></i>
                            Evacuation Planning Calculator
                        </h2>
                        <p class="card-text">Calculate evacuation routes, assembly points, and emergency egress requirements for construction sites</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5>Site Information</h5>
                                <div class="mb-3">
                                    <label for="totalPersonnel" class="form-label">Total Personnel</label>
                                    <input type="number" class="form-control" id="totalPersonnel" step="1" min="0" placeholder="Number of workers on site">
                                </div>
                                <div class="mb-3">
                                    <label for="buildingArea" class="form-label">Building/Floor Area (sq ft)</label>
                                    <input type="number" class="form-control" id="buildingArea" step="1" min="0" placeholder="Total building area">
                                </div>
                                <div class="mb-3">
                                    <label for="maxOccupancy" class="form-label">Maximum Occupancy Load</label>
                                    <input type="number" class="form-control" id="maxOccupancy" step="1" min="0" placeholder="Code maximum occupancy">
                                </div>
                                <div class="mb-3">
                                    <label for="evacuationTime" class="form-label">Required Evacuation Time (minutes)</label>
                                    <input type="number" class="form-control" id="evacuationTime" step="0.1" min="1" placeholder="Typically 2.5 minutes">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h5>Exit Details</h5>
                                <div class="mb-3">
                                    <label for="exitCount" class="form-label">Number of Exits</label>
                                    <input type="number" class="form-control" id="exitCount" step="1" min="1" placeholder="Required exits available">
                                </div>
                                <div class="mb-3">
                                    <label for="exitWidth" class="form-label">Exit Door Width (inches)</label>
                                    <input type="number" class="form-control" id="exitWidth" step="0.1" min="0" placeholder="Clear width of exits">
                                </div>
                                <div class="mb-3">
                                    <label for="travelDistance" class="form-label">Maximum Travel Distance (feet)</label>
                                    <input type="number" class="form-control" id="travelDistance" step="1" min="0" placeholder="Distance to nearest exit">
                                </div>
                                <div class="mb-3">
                                    <label for="assemblyArea" class="form-label">Assembly Area Size (sq ft)</label>
                                    <input type="number" class="form-control" id="assemblyArea" step="1" min="0" placeholder="Size of assembly area">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" onclick="calculateEvacuation()">
                                    <i class="fas fa-calculator me-2"></i>
                                    Calculate Evacuation Plan
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
                            <h5>Evacuation Analysis Results</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="result-item">
                                        <strong>Exit Capacity:</strong>
                                        <span id="exitCapacity"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Flow Rate:</strong>
                                        <span id="flowRate"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Evacuation Time:</strong>
                                        <span id="evacuationCalcTime"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="result-item">
                                        <strong>Code Compliance:</strong>
                                        <span id="codeCompliance"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Assembly Area:</strong>
                                        <span id="assemblyCheck"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Recommendations:</strong>
                                        <span id="recommendations"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6>Detailed Analysis</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Exit Requirements:</strong><br>
                                        <span id="exitRequirements"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Capacity Factor:</strong><br>
                                        <span id="capacityFactor"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Safety Status:</strong><br>
                                        <span id="safetyStatus"></span>
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
        
        function calculateEvacuation() {
            const totalPersonnel = parseFloat(document.getElementById('totalPersonnel').value);
            const buildingArea = parseFloat(document.getElementById('buildingArea').value);
            const maxOccupancy = parseFloat(document.getElementById('maxOccupancy').value);
            const evacuationTime = parseFloat(document.getElementById('evacuationTime').value);
            const exitCount = parseFloat(document.getElementById('exitCount').value);
            const exitWidth = parseFloat(document.getElementById('exitWidth').value);
            const travelDistance = parseFloat(document.getElementById('travelDistance').value);
            const assemblyArea = parseFloat(document.getElementById('assemblyArea').value);
            
            if (!totalPersonnel || !buildingArea || !exitCount || !exitWidth) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Calculate evacuation parameters
            let exitCapacity = 0;
            let flowRate = 0;
            let evacuationCalcTime = 0;
            let codeCompliance = '';
            let assemblyCheck = '';
            let recommendations = '';
            let exitRequirements = '';
            let capacityFactor = 0;
            let safetyStatus = '';
            
            // Calculate exit capacity (people per minute)
            // Standard flow rate: 100 people per minute per 36" of exit width
            const flowRatePerInch = 100 / 36; // People per minute per inch
            exitCapacity = exitCount * exitWidth * flowRatePerInch;
            
            // Calculate evacuation time
            evacuationCalcTime = totalPersonnel / exitCapacity;
            
            // Calculate flow rate per person
            flowRate = exitCapacity / totalPersonnel;
            
            // Check code compliance
            let requiredExits = 0;
            let requiredWidth = 0;
            let requiredArea = 0;
            
            // Determine exit requirements based on occupancy
            if (totalPersonnel <= 50) {
                requiredExits = 1;
                requiredWidth = 32; // inches
            } else if (totalPersonnel <= 500) {
                requiredExits = 2;
                requiredWidth = 32; // inches
            } else {
                requiredExits = Math.ceil(totalPersonnel / 500);
                requiredWidth = 32; // inches minimum
            }
            
            // Assembly area requirement (typically 5 sq ft per person)
            requiredArea = totalPersonnel * 5;
            
            exitRequirements = `${requiredExits} exits, ${requiredWidth}" width minimum`;
            
            // Check compliance
            if (exitCount >= requiredExits && exitWidth >= requiredWidth && 
                travelDistance <= 300 && evacuationCalcTime <= evacuationTime) {
                codeCompliance = 'Compliant with life safety codes';
                safetyStatus = 'Safe - Meets code requirements';
            } else {
                codeCompliance = 'Non-compliant - Requires modifications';
                if (exitCount < requiredExits) {
                    safetyStatus = 'Unsafe - Insufficient exits';
                } else if (exitWidth < requiredWidth) {
                    safetyStatus = 'Unsafe - Insufficient exit width';
                } else if (travelDistance > 300) {
                    safetyStatus = 'Unsafe - Excessive travel distance';
                } else {
                    safetyStatus = 'Unsafe - Evacuation time too long';
                }
            }
            
            // Check assembly area
            if (assemblyArea >= requiredArea) {
                assemblyCheck = 'Adequate assembly area';
            } else {
                assemblyCheck = `Insufficient - Need ${requiredArea - assemblyArea} more sq ft`;
            }
            
            // Calculate capacity factor (safety margin)
            capacityFactor = (exitCapacity / totalPersonnel).toFixed(2);
            
            // Generate recommendations
            let recommendationsList = [];
            
            if (exitCapacity < totalPersonnel / evacuationTime) {
                recommendationsList.push('Increase exit capacity or number of exits');
            }
            
            if (travelDistance > 200) {
                recommendationsList.push('Consider additional exits to reduce travel distance');
            }
            
            if (assemblyArea < requiredArea) {
                recommendationsList.push('Expand assembly area or reduce occupancy');
            }
            
            if (recommendationsList.length === 0) {
                recommendations = 'Plan meets all safety requirements';
            } else {
                recommendations = recommendationsList.join('; ');
            }
            
            // Display results
            document.getElementById('exitCapacity').textContent = exitCapacity.toFixed(0) + ' people/min';
            document.getElementById('flowRate').textContent = flowRate.toFixed(2) + ' exits/person';
            document.getElementById('evacuationCalcTime').textContent = evacuationCalcTime.toFixed(1) + ' minutes';
            document.getElementById('codeCompliance').textContent = codeCompliance;
            document.getElementById('assemblyCheck').textContent = assemblyCheck;
            document.getElementById('recommendations').textContent = recommendations;
            document.getElementById('exitRequirements').textContent = exitRequirements;
            document.getElementById('capacityFactor').textContent = capacityFactor + 'x safety margin';
            document.getElementById('safetyStatus').textContent = safetyStatus;
            
            document.getElementById('results').style.display = 'block';
            
            // Save to localStorage
            saveToLocalStorage('evacuation_planning', {
                totalPersonnel: totalPersonnel,
                buildingArea: buildingArea,
                maxOccupancy: maxOccupancy,
                evacuationTime: evacuationTime,
                exitCount: exitCount,
                exitWidth: exitWidth,
                travelDistance: travelDistance,
                assemblyArea: assemblyArea,
                results: {
                    exitCapacity: exitCapacity,
                    flowRate: flowRate,
                    evacuationCalcTime: evacuationCalcTime,
                    codeCompliance: codeCompliance,
                    assemblyCheck: assemblyCheck,
                    recommendations: recommendations,
                    exitRequirements: exitRequirements,
                    capacityFactor: capacityFactor,
                    safetyStatus: safetyStatus
                }
            });
        }

        function clearForm() {
            document.getElementById('totalPersonnel').value = '';
            document.getElementById('buildingArea').value = '';
            document.getElementById('maxOccupancy').value = '';
            document.getElementById('evacuationTime').value = '';
            document.getElementById('exitCount').value = '';
            document.getElementById('exitWidth').value = '';
            document.getElementById('travelDistance').value = '';
            document.getElementById('assemblyArea').value = '';
            document.getElementById('results').style.display = 'none';
        }

        function saveCalculation() {
            const calculationData = {
                type: 'Evacuation Planning',
                timestamp: new Date().toISOString(),
                inputs: {
                    totalPersonnel: document.getElementById('totalPersonnel').value,
                    buildingArea: document.getElementById('buildingArea').value,
                    maxOccupancy: document.getElementById('maxOccupancy').value,
                    evacuationTime: document.getElementById('evacuationTime').value,
                    exitCount: document.getElementById('exitCount').value,
                    exitWidth: document.getElementById('exitWidth').value,
                    travelDistance: document.getElementById('travelDistance').value,
                    assemblyArea: document.getElementById('assemblyArea').value
                }
            };
            
            if (document.getElementById('results').style.display !== 'none') {
                calculationData.results = {
                    exitCapacity: document.getElementById('exitCapacity').textContent,
                    flowRate: document.getElementById('flowRate').textContent,
                    evacuationCalcTime: document.getElementById('evacuationCalcTime').textContent,
                    codeCompliance: document.getElementById('codeCompliance').textContent,
                    assemblyCheck: document.getElementById('assemblyCheck').textContent,
                    recommendations: document.getElementById('recommendations').textContent,
                    exitRequirements: document.getElementById('exitRequirements').textContent,
                    capacityFactor: document.getElementById('capacityFactor').textContent,
                    safetyStatus: document.getElementById('safetyStatus').textContent
                };
            }
            
            saveToLocalStorage('saved_calculations', calculationData);
            alert('Calculation saved successfully!');
        }

        // Load saved data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFromLocalStorage('evacuation_planning');
        });
    </script>
</body>
</html>
