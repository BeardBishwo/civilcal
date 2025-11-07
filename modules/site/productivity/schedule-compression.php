<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Compression Calculator - Site Tools</title>
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
                <span>Schedule Compression Calculator</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card glass-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-clock me-2"></i>
                            Schedule Compression Calculator
                        </h2>
                        <p class="card-text">Calculate schedule compression options, fast-tracking costs, and project acceleration strategies</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5>Project Schedule</h5>
                                <div class="mb-3">
                                    <label for="originalDuration" class="form-label">Original Duration (days)</label>
                                    <input type="number" class="form-control" id="originalDuration" step="1" min="1" placeholder="Original project duration">
                                </div>
                                <div class="mb-3">
                                    <label for="targetDuration" class="form-label">Target Duration (days)</label>
                                    <input type="number" class="form-control" id="targetDuration" step="1" min="1" placeholder="Desired compressed duration">
                                </div>
                                <div class="mb-3">
                                    <label for="criticalPath" class="form-label">Critical Path Activities (%)</label>
                                    <input type="number" class="form-control" id="criticalPath" step="1" min="1" max="100" placeholder="% of work on critical path">
                                </div>
                                <div class="mb-3">
                                    <label for="projectValue" class="form-label">Project Value ($)</label>
                                    <input type="number" class="form-control" id="projectValue" step="0.01" min="0" placeholder="Total project contract value">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h5>Compression Parameters</h5>
                                <div class="mb-3">
                                    <label for="compressionMethod" class="form-label">Compression Method</label>
                                    <select class="form-select" id="compressionMethod">
                                        <option value="">Select method</option>
                                        <option value="fast_track">Fast Tracking</option>
                                        <option value="crashing">Crashing</option>
                                        <option value="parallel_work">Parallel Work</option>
                                        <option value="overtime">Overtime</option>
                                        <option value="additional_crews">Additional Crews</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="dailyCost" class="form-label">Daily Project Cost ($)</label>
                                    <input type="number" class="form-control" id="dailyCost" step="0.01" min="0" placeholder="Cost per day of project delay">
                                </div>
                                <div class="mb-3">
                                    <label for="accelerationCost" class="form-label">Acceleration Cost Factor (%)</label>
                                    <input type="number" class="form-control" id="accelerationCost" step="0.1" min="0" placeholder="Additional cost for compression">
                                </div>
                                <div class="mb-3">
                                    <label for="riskLevel" class="form-label">Risk Level</label>
                                    <select class="form-select" id="riskLevel">
                                        <option value="">Select risk level</option>
                                        <option value="low">Low Risk</option>
                                        <option value="medium">Medium Risk</option>
                                        <option value="high">High Risk</option>
                                        <option value="very_high">Very High Risk</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" onclick="calculateScheduleCompression()">
                                    <i class="fas fa-calculator me-2"></i>
                                    Calculate Schedule Compression
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
                            <h5>Schedule Compression Analysis</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="result-item">
                                        <strong>Time Savings:</strong>
                                        <span id="timeSavings"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Compression Percentage:</strong>
                                        <span id="compressionPercentage"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Acceleration Cost:</strong>
                                        <span id="accelerationCost"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="result-item">
                                        <strong>Total Cost Impact:</strong>
                                        <span id="totalCostImpact"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Net Benefit:</strong>
                                        <span id="netBenefit"></span>
                                    </div>
                                    <div class="result-item">
                                        <strong>Recommendation:</strong>
                                        <span id="recommendation"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6>Detailed Analysis</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Cost per Day Saved:</strong><br>
                                        <span id="costPerDay"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Risk Assessment:</strong><br>
                                        <span id="riskAssessment"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Feasibility Score:</strong><br>
                                        <span id="feasibilityScore"></span>
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

        function calculateScheduleCompression() {
            const originalDuration = parseFloat(document.getElementById('originalDuration').value);
            const targetDuration = parseFloat(document.getElementById('targetDuration').value);
            const criticalPath = parseFloat(document.getElementById('criticalPath').value);
            const projectValue = parseFloat(document.getElementById('projectValue').value);
            const compressionMethod = document.getElementById('compressionMethod').value;
            const dailyCost = parseFloat(document.getElementById('dailyCost').value);
            const accelerationCost = parseFloat(document.getElementById('accelerationCost').value) || 0;
            const riskLevel = document.getElementById('riskLevel').value;
            
            if (!originalDuration || !targetDuration || !compressionMethod || !dailyCost) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Calculate schedule compression parameters
            let timeSavings = 0;
            let compressionPercentage = 0;
            let accelerationCostValue = 0;
            let totalCostImpact = 0;
            let netBenefit = 0;
            let recommendation = '';
            let costPerDay = 0;
            let riskAssessment = '';
            let feasibilityScore = 0;
            
            // Calculate time savings
            timeSavings = originalDuration - targetDuration;
            
            // Calculate compression percentage
            compressionPercentage = (timeSavings / originalDuration) * 100;
            
            // Calculate acceleration cost based on method
            const methodFactors = {
                fast_track: 0.05, // 5% additional cost
                crashing: 0.15,   // 15% additional cost
                parallel_work: 0.10, // 10% additional cost
                overtime: 0.25,   // 25% additional cost (overtime premiums)
                additional_crews: 0.20 // 20% additional cost
            };
            
            const methodFactor = methodFactors[compressionMethod] || 0.10;
            accelerationCostValue = projectValue * methodFactor * (compressionPercentage / 100);
            
            // Calculate total cost impact
            const delaySavings = timeSavings * dailyCost;
            totalCostImpact = accelerationCostValue - delaySavings;
            
            // Calculate net benefit
            netBenefit = -totalCostImpact; // Positive means benefit
            
            // Calculate cost per day saved
            costPerDay = timeSavings > 0 ? accelerationCostValue / timeSavings : 0;
            
            // Assess risk based on compression percentage and method
            let riskScore = 0;
            if (compressionPercentage > 30) riskScore += 3;
            else if (compressionPercentage > 20) riskScore += 2;
            else if (compressionPercentage > 10) riskScore += 1;
            
            if (compressionMethod === 'overtime') riskScore += 2;
            else if (compressionMethod === 'crashing') riskScore += 1;
            
            if (riskLevel === 'very_high') riskScore += 3;
            else if (riskLevel === 'high') riskScore += 2;
            else if (riskLevel === 'medium') riskScore += 1;
            
            // Determine risk assessment
            if (riskScore <= 2) {
                riskAssessment = 'Low Risk - Manageable';
            } else if (riskScore <= 4) {
                riskAssessment = 'Medium Risk - Monitor closely';
            } else if (riskScore <= 6) {
                riskAssessment = 'High Risk - Requires mitigation';
            } else {
                riskAssessment = 'Very High Risk - Not recommended';
            }
            
            // Calculate feasibility score (0-100)
            feasibilityScore = Math.max(0, 100 - (compressionPercentage * 2) - (riskScore * 10));
            
            // Generate recommendation
            if (netBenefit > 0 && feasibilityScore >= 60) {
                recommendation = 'Recommended - Good cost-benefit ratio';
            } else if (netBenefit > 0 && feasibilityScore >= 40) {
                recommendation = 'Caution - Monitor risks closely';
            } else if (netBenefit <= 0 && feasibilityScore >= 60) {
                recommendation = 'Consider alternative methods';
            } else {
                recommendation = 'Not recommended - High risk/low benefit';
            }
            
            // Display results
            document.getElementById('timeSavings').textContent = timeSavings + ' days';
            document.getElementById('compressionPercentage').textContent = compressionPercentage.toFixed(1) + '%';
            document.getElementById('accelerationCost').textContent = '$' + accelerationCostValue.toLocaleString('en-US', {minimumFractionDigits: 0});
            document.getElementById('totalCostImpact').textContent = '$' + totalCostImpact.toLocaleString('en-US', {minimumFractionDigits: 0});
            document.getElementById('netBenefit').textContent = '$' + netBenefit.toLocaleString('en-US', {minimumFractionDigits: 0});
            document.getElementById('recommendation').textContent = recommendation;
            document.getElementById('costPerDay').textContent = '$' + costPerDay.toFixed(0) + '/day saved';
            document.getElementById('riskAssessment').textContent = riskAssessment;
            document.getElementById('feasibilityScore').textContent = feasibilityScore.toFixed(0) + '/100';
            
            document.getElementById('results').style.display = 'block';
            
            // Save to localStorage
            saveToLocalStorage('schedule_compression', {
                originalDuration: originalDuration,
                targetDuration: targetDuration,
                criticalPath: criticalPath,
                projectValue: projectValue,
                compressionMethod: compressionMethod,
                dailyCost: dailyCost,
                accelerationCost: accelerationCost,
                riskLevel: riskLevel,
                results: {
                    timeSavings: timeSavings,
                    compressionPercentage: compressionPercentage,
                    accelerationCost: accelerationCostValue,
                    totalCostImpact: totalCostImpact,
                    netBenefit: netBenefit,
                    recommendation: recommendation,
                    costPerDay: costPerDay,
                    riskAssessment: riskAssessment,
                    feasibilityScore: feasibilityScore
                }
            });
        }

        function clearForm() {
            document.getElementById('originalDuration').value = '';
            document.getElementById('targetDuration').value = '';
            document.getElementById('criticalPath').value = '';
            document.getElementById('projectValue').value = '';
            document.getElementById('compressionMethod').value = '';
            document.getElementById('dailyCost').value = '';
            document.getElementById('accelerationCost').value = '';
            document.getElementById('riskLevel').value = '';
            document.getElementById('results').style.display = 'none';
        }

        function saveCalculation() {
            const calculationData = {
                type: 'Schedule Compression',
                timestamp: new Date().toISOString(),
                inputs: {
                    originalDuration: document.getElementById('originalDuration').value,
                    targetDuration: document.getElementById('targetDuration').value,
                    criticalPath: document.getElementById('criticalPath').value,
                    projectValue: document.getElementById('projectValue').value,
                    compressionMethod: document.getElementById('compressionMethod').value,
                    dailyCost: document.getElementById('dailyCost').value,
                    accelerationCost: document.getElementById('accelerationCost').value,
                    riskLevel: document.getElementById('riskLevel').value
                }
            };
            
            if (document.getElementById('results').style.display !== 'none') {
                calculationData.results = {
                    timeSavings: document.getElementById('timeSavings').textContent,
                    compressionPercentage: document.getElementById('compressionPercentage').textContent,
                    accelerationCost: document.getElementById('accelerationCost').textContent,
                    totalCostImpact: document.getElementById('totalCostImpact').textContent,
                    netBenefit: document.getElementById('netBenefit').textContent,
                    recommendation: document.getElementById('recommendation').textContent,
                    costPerDay: document.getElementById('costPerDay').textContent,
                    riskAssessment: document.getElementById('riskAssessment').textContent,
                    feasibilityScore: document.getElementById('feasibilityScore').textContent
                };
            }
            
            saveToLocalStorage('saved_calculations', calculationData);
            alert('Calculation saved successfully!');
        }

        // Load saved data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFromLocalStorage('schedule_compression');
        });
    </script>
</body>
</html>
