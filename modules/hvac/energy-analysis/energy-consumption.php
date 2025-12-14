<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Consumption Calculator - HVAC Energy Analysis</title>
    <link rel="stylesheet" href="../../assets/css/theme.css">
    <link rel="stylesheet" href="../../assets/css/hvac.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .back-nav {
            margin-bottom: 2rem;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .calculator-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .input-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .input-group {
            margin-bottom: 1rem;
        }
        
        .input-group label {
            display: block;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-wrapper input,
        .input-wrapper select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .input-wrapper input:focus,
        .input-wrapper select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .unit-label {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 0.9rem;
            pointer-events: none;
        }
        
        .calculate-btn {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .calculate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .results-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: none;
        }
        
        .results-section.show {
            display: block;
        }
        
        .results-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .results-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-right: 1rem;
        }
        
        .results-title {
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .result-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }
        
        .result-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }
        
        .result-value {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .result-unit {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .details-section {
            background: rgba(52, 73, 94, 0.1);
            padding: 1.5rem;
            border-radius: 12px;
            margin-top: 1.5rem;
        }
        
        .details-title {
            color: #2c3e50;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .calculation-breakdown {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .breakdown-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .breakdown-formula {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-bottom: 0.5rem;
        }
        
        .breakdown-value {
            font-size: 1.1rem;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .info-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .info-section h3 {
            color: #2c3e50;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .info-section p {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .tips-list {
            list-style: none;
            padding: 0;
        }
        
        .tips-list li {
            color: #34495e;
            margin-bottom: 0.75rem;
            padding-left: 1.5rem;
            position: relative;
        }
        
        .tips-list li::before {
            content: '💡';
            position: absolute;
            left: 0;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="back-nav">
            <a href="../index.php" class="back-btn">← Back to HVAC Categories</a>
        </div>
        
        <div class="header">
            <h1>Energy Consumption Calculator</h1>
            <p>Calculate energy consumption, costs, and usage patterns for HVAC equipment and systems</p>
        </div>
        
        <div class="calculator-section">
            <h3 style="color: #2c3e50; margin-bottom: 1.5rem; display: flex; align-items: center;">
                <span style="font-size: 1.5rem; margin-right: 0.75rem;">⚡</span>
                Energy Consumption Parameters
            </h3>
            
            <div class="input-grid">
                <div class="input-group">
                    <label for="equipmentPower">Equipment Power Rating</label>
                    <div class="input-wrapper">
                        <input type="number" id="equipmentPower" step="0.1" placeholder="Enter power rating">
                        <span class="unit-label">kW</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="dailyHours">Daily Operating Hours</label>
                    <div class="input-wrapper">
                        <input type="number" id="dailyHours" step="0.5" value="8" placeholder="Hours per day">
                        <span class="unit-label">hours</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="daysWeek">Operating Days per Week</label>
                    <div class="input-wrapper">
                        <input type="number" id="daysWeek" step="1" value="5" placeholder="Days per week">
                        <span class="unit-label">days</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="electricityCost">Electricity Rate</label>
                    <div class="input-wrapper">
                        <input type="number" id="electricityCost" step="0.01" value="0.12" placeholder="Cost per kWh">
                        <span class="unit-label">$/kWh</span>
                    </div>
                </div>
            </div>
            
            <button class="calculate-btn" onclick="calculateEnergyConsumption()">
                Calculate Energy Consumption
            </button>
        </div>
        
        <div class="results-section" id="energyConsumptionResults">
            <div class="results-header">
                <div class="results-icon">⚡</div>
                <h3 class="results-title">Energy Consumption Analysis</h3>
            </div>
            
            <div class="results-grid" id="energyResultsGrid">
                <!-- Results will be populated here -->
            </div>
            
            <div class="details-section">
                <h4 class="details-title">Calculation Breakdown</h4>
                <div class="calculation-breakdown" id="calculationBreakdown">
                    <!-- Breakdown will be populated here -->
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>About Energy Consumption</h3>
            <p>
                Energy consumption analysis is crucial for understanding the operating costs and environmental impact 
                of HVAC systems. This calculator helps estimate energy usage patterns and associated costs for 
                budgeting and optimization purposes.
            </p>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Key Features:</h4>
            <ul class="tips-list">
                <li><strong>Multiple Time Periods:</strong> Daily, weekly, monthly, and annual energy consumption estimates</li>
                <li><strong>Cost Analysis:</strong> Calculate operating costs across different time periods</li>
                <li><strong>Usage Patterns:</strong> Account for variable operating schedules and duty cycles</li>
                <li><strong>Rate Sensitivity:</strong> Analyze cost impacts of different electricity rates</li>
                <li><strong>Budget Planning:</strong> Provide annual cost estimates for budgeting purposes</li>
            </ul>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Energy Efficiency Tips:</h4>
            <ul class="tips-list">
                <li>Consider variable speed drives (VSDs) for partial load operation</li>
                <li>Implement demand control ventilation based on occupancy</li>
                <li>Regular maintenance can improve efficiency by 5-15%</li>
                <li>Right-sizing equipment prevents energy waste from over-sizing</li>
                <li>Monitor energy consumption to identify optimization opportunities</li>
            </ul>
        </div>
    </div>

    <script>
        function calculateEnergyConsumption() {
            const power = parseFloat(document.getElementById('equipmentPower').value);
            const dailyHours = parseFloat(document.getElementById('dailyHours').value);
            const daysWeek = parseFloat(document.getElementById('daysWeek').value);
            const cost = parseFloat(document.getElementById('electricityCost').value);
            
            if (!power || !dailyHours || !daysWeek || !cost) {
                showNotification('Please enter all required values', 'info');
                return;
            }
            
            // Calculate energy consumption
            const dailyEnergy = power * dailyHours; // kWh/day
            const weeklyEnergy = dailyEnergy * daysWeek; // kWh/week
            const monthlyEnergy = weeklyEnergy * 4.33; // kWh/month (average weeks per month)
            const annualEnergy = monthlyEnergy * 12; // kWh/year
            
            // Calculate costs
            const dailyCost = dailyEnergy * cost;
            const weeklyCost = weeklyEnergy * cost;
            const monthlyCost = monthlyEnergy * cost;
            const annualCost = annualEnergy * cost;
            
            // Display results
            const resultsGrid = document.getElementById('energyResultsGrid');
            resultsGrid.innerHTML = `
                <div class="result-card">
                    <div class="result-label">Daily Energy</div>
                    <div class="result-value">${dailyEnergy.toFixed(1)}</div>
                    <div class="result-unit">kWh/day</div>
                </div>
                <div class="result-card">
                    <div class="result-label">Weekly Energy</div>
                    <div class="result-value">${weeklyEnergy.toFixed(0)}</div>
                    <div class="result-unit">kWh/week</div>
                </div>
                <div class="result-card">
                    <div class="result-label">Monthly Energy</div>
                    <div class="result-value">${monthlyEnergy.toFixed(0)}</div>
                    <div class="result-unit">kWh/month</div>
                </div>
                <div class="result-card">
                    <div class="result-label">Annual Energy</div>
                    <div class="result-value">${annualEnergy.toLocaleString()}</div>
                    <div class="result-unit">kWh/year</div>
                </div>
                <div class="result-card">
                    <div class="result-label">Daily Cost</div>
                    <div class="result-value">$${dailyCost.toFixed(2)}</div>
                    <div class="result-unit">$/day</div>
                </div>
                <div class="result-card">
                    <div class="result-label">Annual Cost</div>
                    <div class="result-value">$${annualCost.toLocaleString()}</div>
                    <div class="result-unit">$/year</div>
                </div>
            `;
            
            // Display calculation breakdown
            const breakdown = document.getElementById('calculationBreakdown');
            breakdown.innerHTML = `
                <div class="breakdown-item">
                    <div class="breakdown-formula">Daily Energy = Power × Hours</div>
                    <div class="breakdown-value">${power} kW × ${dailyHours} hours = ${dailyEnergy.toFixed(1)} kWh</div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-formula">Weekly Energy = Daily × Operating Days</div>
                    <div class="breakdown-value">${dailyEnergy.toFixed(1)} kWh × ${daysWeek} days = ${weeklyEnergy.toFixed(0)} kWh</div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-formula">Annual Energy = Monthly × 12</div>
                    <div class="breakdown-value">${monthlyEnergy.toFixed(0)} kWh × 12 = ${annualEnergy.toLocaleString()} kWh</div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-formula">Annual Cost = Annual Energy × Rate</div>
                    <div class="breakdown-value">${annualEnergy.toLocaleString()} kWh × $${cost}/kWh = $${annualCost.toLocaleString()}</div>
                </div>
            `;
            
            // Show results section
            document.getElementById('energyConsumptionResults').classList.add('show');
            
            // Save to localStorage
            const calculation = {
                type: 'Energy Consumption',
                inputs: { power, dailyHours, daysWeek, cost },
                results: { dailyEnergy, weeklyEnergy, monthlyEnergy, annualEnergy, dailyCost, weeklyCost, monthlyCost, annualCost },
                timestamp: new Date().toISOString()
            };
            
            saveToHistory(calculation);
        }
        
        function saveToHistory(calculation) {
            let history = JSON.parse(localStorage.getItem('hvacEnergyHistory') || '[]');
            history.unshift(calculation);
            
            // Keep only last 20 calculations
            history = history.slice(0, 20);
            localStorage.setItem('hvacEnergyHistory', JSON.stringify(history));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
