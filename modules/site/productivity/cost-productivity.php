<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cost-Productivity Analysis Calculator - Site Tools</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 25px 30px;
            border-bottom: none;
        }

        .card-title {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .card-subtitle {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
        }

        .card-body {
            padding: 30px;
        }

        .section-header {
            color: var(--secondary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 8px;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 600;
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

        .result-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .result-title {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .result-value {
            font-size: 1.4rem;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 5px;
        }

        .quick-ref-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
        }

        .quick-ref-card h6 {
            color: var(--secondary-color);
            margin-bottom: 15px;
            font-weight: 600;
        }

        .quick-ref-card ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .quick-ref-card li {
            margin-bottom: 8px;
            color: #495057;
        }

        .recommendations {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }

        .recommendations .result-title {
            color: #d63384;
        }

        .recommendations ul {
            padding-left: 20px;
        }

        .recommendations li {
            margin-bottom: 8px;
            color: #6f42c1;
            font-weight: 500;
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
            
            .result-card {
                padding: 15px;
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
                <span>Cost-Productivity Analysis</span>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-calculator me-2"></i>
                            Cost-Productivity Analysis Calculator
                        </h2>
                        <p class="card-subtitle">Analyze cost-effectiveness and productivity metrics for construction activities</p>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Input Parameters -->
                            <div class="col-lg-6">
                                <h5 class="section-header">
                                    <i class="fas fa-wrench me-2"></i>Activity Parameters
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="activity_name" class="form-label">Activity Name</label>
                                    <input type="text" class="form-control" id="activity_name" placeholder="e.g., Concrete Placement">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="daily_cost" class="form-label">Daily Cost ($)</label>
                                            <input type="number" class="form-control" id="daily_cost" step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="production_rate" class="form-label">Production Rate</label>
                                            <input type="number" class="form-control" id="production_rate" step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="rate_unit" class="form-label">Production Unit</label>
                                    <select class="form-select" id="rate_unit">
                                        <option value="cy/day">Cubic Yards/Day</option>
                                        <option value="sf/day">Square Feet/Day</option>
                                        <option value="lf/day">Linear Feet/Day</option>
                                        <option value="tons/day">Tons/Day</option>
                                        <option value="units/day">Units/Day</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="quality_score" class="form-label">Quality Score (1-10)</label>
                                            <input type="number" class="form-control" id="quality_score" min="1" max="10" step="0.1" placeholder="8.0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="safety_rating" class="form-label">Safety Rating (1-10)</label>
                                            <input type="number" class="form-control" id="safety_rating" min="1" max="10" step="0.1" placeholder="9.0">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="delay_days" class="form-label">Delay Days</label>
                                            <input type="number" class="form-control" id="delay_days" min="0" step="1" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="change_orders" class="form-label">Change Orders</label>
                                            <input type="number" class="form-control" id="change_orders" min="0" step="1" placeholder="0">
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary w-100" onclick="calculateProductivity()">
                                    <i class="fas fa-calculator me-2"></i>Calculate Cost-Productivity
                                </button>
                            </div>

                            <!-- Results -->
                            <div class="col-lg-6">
                                <h5 class="section-header">
                                    <i class="fas fa-chart-line me-2"></i>Analysis Results
                                </h5>
                                
                                <div id="results" style="display: none;">
                                    <div class="result-card">
                                        <h6 class="result-title">Cost Analysis</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-white-50">Unit Cost:</small>
                                                <div class="result-value" id="unit_cost">$0.00</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-white-50">Total Daily Cost:</small>
                                                <div class="result-value" id="total_daily_cost">$0.00</div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <small class="text-white-50">Cost per Unit:</small>
                                                <div class="result-value" id="cost_per_unit">$0.00</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-white-50">Productivity Index:</small>
                                                <div class="result-value" id="productivity_index">0.00</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="result-card">
                                        <h6 class="result-title">Performance Metrics</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-white-50">Efficiency Score:</small>
                                                <div class="result-value" id="efficiency_score">0.0%</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-white-50">Quality Index:</small>
                                                <div class="result-value" id="quality_index">0.0</div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <small class="text-white-50">Safety Score:</small>
                                                <div class="result-value" id="safety_score">0.0%</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-white-50">Overall Rating:</small>
                                                <div class="result-value" id="overall_rating">0.0</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="result-card">
                                        <h6 class="result-title">Risk Analysis</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-white-50">Delay Impact:</small>
                                                <div class="result-value" id="delay_impact">$0.00</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-white-50">Change Order Cost:</small>
                                                <div class="result-value" id="change_order_cost">$0.00</div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-white-50">Risk Level:</small>
                                            <div class="result-value" id="risk_level">Low</div>
                                        </div>
                                    </div>

                                    <div class="recommendations">
                                        <h6 class="result-title">Recommendations</h6>
                                        <div id="recommendations_content">
                                            <!-- Recommendations will be populated here -->
                                        </div>
                                    </div>
                                </div>

                                <div id="quick_reference" class="mt-4">
                                    <h6 class="section-header">
                                        <i class="fas fa-lightbulb me-2"></i>Quick Reference
                                    </h6>
                                    <div class="quick-ref-card">
                                        <h6>Cost-Productivity Formulas</h6>
                                        <ul>
                                            <li><strong>Unit Cost:</strong> Daily Cost ÷ Production Rate</li>
                                            <li><strong>Productivity Index:</strong> (Quality Score × Safety Rating) ÷ 100</li>
                                            <li><strong>Efficiency Score:</strong> (Production Rate ÷ Target Rate) × 100%</li>
                                            <li><strong>Overall Rating:</strong> Weighted average of all metrics</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
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

        function calculateProductivity() {
            // Get input values
            const activityName = document.getElementById('activity_name').value || 'Activity';
            const dailyCost = parseFloat(document.getElementById('daily_cost').value) || 0;
            const productionRate = parseFloat(document.getElementById('production_rate').value) || 0;
            const rateUnit = document.getElementById('rate_unit').value;
            const qualityScore = parseFloat(document.getElementById('quality_score').value) || 5;
            const safetyRating = parseFloat(document.getElementById('safety_rating').value) || 5;
            const delayDays = parseInt(document.getElementById('delay_days').value) || 0;
            const changeOrders = parseInt(document.getElementById('change_orders').value) || 0;

            if (dailyCost === 0 || productionRate === 0) {
                showNotification('Please enter valid daily cost and production rate values.', 'info');
                return;
            }

            // Calculate cost metrics
            const unitCost = dailyCost / productionRate;
            const totalDailyCost = dailyCost;
            const costPerUnit = unitCost;
            const productivityIndex = (qualityScore * safetyRating) / 100;

            // Calculate performance metrics
            const efficiencyScore = (productionRate / (productionRate * 1.1)) * 100; // 10% above actual as target
            const qualityIndex = qualityScore;
            const safetyScore = (safetyRating / 10) * 100;
            const overallRating = (productivityIndex * 0.3 + efficiencyScore * 0.25 + qualityIndex * 0.25 + safetyScore * 0.2) / 10;

            // Calculate risk analysis
            const delayImpact = delayDays * dailyCost * 1.5; // 50% penalty for delays
            const changeOrderCost = changeOrders * dailyCost * 0.2; // 20% of daily cost per change order
            let riskLevel = 'Low';
            
            const riskScore = (delayDays * 10) + (changeOrders * 15) + (10 - qualityScore) + (10 - safetyRating);
            if (riskScore > 50) {
                riskLevel = 'High';
            } else if (riskScore > 25) {
                riskLevel = 'Medium';
            }

            // Generate recommendations
            let recommendations = '';
            if (efficiencyScore < 85) {
                recommendations += '<li>Improve productivity through better resource allocation and workflow optimization.</li>';
            }
            if (qualityScore < 8) {
                recommendations += '<li>Focus on quality control measures and training to improve output quality.</li>';
            }
            if (safetyRating < 9) {
                recommendations += '<li>Implement additional safety protocols and training programs.</li>';
            }
            if (delayDays > 0) {
                recommendations += '<li>Review scheduling and identify bottlenecks causing delays.</li>';
            }
            if (changeOrders > 2) {
                recommendations += '<li>Improve planning and specification clarity to reduce change orders.</li>';
            }
            if (unitCost > dailyCost / (productionRate * 1.1)) {
                recommendations += '<li>Analyze cost structure and identify opportunities for cost reduction.</li>';
            }
            
            if (recommendations === '') {
                recommendations = '<li>Activity is performing well. Continue monitoring and maintain current practices.</li>';
            }

            // Display results
            document.getElementById('unit_cost').textContent = '$' + unitCost.toFixed(2);
            document.getElementById('total_daily_cost').textContent = '$' + totalDailyCost.toFixed(2);
            document.getElementById('cost_per_unit').textContent = '$' + costPerUnit.toFixed(2);
            document.getElementById('productivity_index').textContent = productivityIndex.toFixed(2);
            
            document.getElementById('efficiency_score').textContent = efficiencyScore.toFixed(1) + '%';
            document.getElementById('quality_index').textContent = qualityIndex.toFixed(1);
            document.getElementById('safety_score').textContent = safetyScore.toFixed(1) + '%';
            document.getElementById('overall_rating').textContent = overallRating.toFixed(1);
            
            document.getElementById('delay_impact').textContent = '$' + delayImpact.toFixed(2);
            document.getElementById('change_order_cost').textContent = '$' + changeOrderCost.toFixed(2);
            document.getElementById('risk_level').textContent = riskLevel;
            
            document.getElementById('recommendations_content').innerHTML = '<ul>' + recommendations + '</ul>';
            document.getElementById('results').style.display = 'block';

            // Save to localStorage
            saveCalculation('cost-productivity', {
                activityName: activityName,
                dailyCost: dailyCost,
                productionRate: productionRate,
                rateUnit: rateUnit,
                qualityScore: qualityScore,
                safetyRating: safetyRating,
                delayDays: delayDays,
                changeOrders: changeOrders,
                results: {
                    unitCost: unitCost,
                    totalDailyCost: totalDailyCost,
                    costPerUnit: costPerUnit,
                    productivityIndex: productivityIndex,
                    efficiencyScore: efficiencyScore,
                    qualityIndex: qualityIndex,
                    safetyScore: safetyScore,
                    overallRating: overallRating,
                    delayImpact: delayImpact,
                    changeOrderCost: changeOrderCost,
                    riskLevel: riskLevel
                }
            });
        }

        function saveCalculation(calculatorType, data) {
            const history = JSON.parse(localStorage.getItem('calculationHistory') || '[]');
            const entry = {
                type: calculatorType,
                timestamp: new Date().toISOString(),
                data: data
            };
            history.unshift(entry);
            if (history.length > 10) history.pop();
            localStorage.setItem('calculationHistory', JSON.stringify(history));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
