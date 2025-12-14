<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CO₂ Emissions Calculator - HVAC Energy Analysis</title>
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
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        
        .result-card.equivalents {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .result-card.comparison {
            background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
        }
        
        .result-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }
        
        .result-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .result-unit {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .equivalents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .equivalent-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }
        
        .equivalent-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .equivalent-value {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .equivalent-label {
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
        
        .emission-breakdown {
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
        
        .breakdown-fuel {
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
        
        .emission-factors {
            background: rgba(52, 73, 94, 0.05);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .factor-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(52, 73, 94, 0.1);
        }
        
        .factor-item:last-child {
            border-bottom: none;
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
            <h1>CO₂ Emissions Calculator</h1>
            <p>Calculate carbon dioxide emissions from HVAC energy consumption and assess environmental impact</p>
        </div>
        
        <div class="calculator-section">
            <h3 style="color: #2c3e50; margin-bottom: 1.5rem; display: flex; align-items: center;">
                <span style="font-size: 1.5rem; margin-right: 0.75rem;">🌍</span>
                Energy Consumption Parameters
            </h3>
            
            <div class="input-grid">
                <div class="input-group">
                    <label for="annualEnergy">Annual Energy Consumption</label>
                    <div class="input-wrapper">
                        <input type="number" id="annualEnergy" step="100" placeholder="Enter annual energy use">
                        <span class="unit-label">kWh/year</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="fuelType">Primary Fuel Type</label>
                    <div class="input-wrapper">
                        <select id="fuelType">
                            <option value="electricity">Grid Electricity</option>
                            <option value="naturalGas">Natural Gas</option>
                            <option value="oil">Fuel Oil</option>
                            <option value="propane">Propane</option>
                            <option value="districtHeat">District Heating</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="emissionFactor">Custom Emission Factor (Optional)</label>
                    <div class="input-wrapper">
                        <input type="number" id="emissionFactor" step="0.01" placeholder="Leave blank for default">
                        <span class="unit-label">kg CO₂/kWh</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="buildingType">Building Type (Optional)</label>
                    <div class="input-wrapper">
                        <select id="buildingType">
                            <option value="">Select building type</option>
                            <option value="residential">Residential</option>
                            <option value="commercial">Commercial</option>
                            <option value="industrial">Industrial</option>
                            <option value="office">Office</option>
                            <option value="retail">Retail</option>
                            <option value="healthcare">Healthcare</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <button class="calculate-btn" onclick="calculateCO2Emissions()">
                Calculate CO₂ Emissions
            </button>
        </div>
        
        <div class="results-section" id="co2Results">
            <div class="results-header">
                <div class="results-icon">🌍</div>
                <h3 class="results-title">CO₂ Emissions Analysis</h3>
            </div>
            
            <div class="results-grid" id="co2ResultsGrid">
                <!-- Results will be populated here -->
            </div>
            
            <div class="equivalents-grid" id="equivalentsGrid">
                <!-- Equivalents will be populated here -->
            </div>
            
            <div class="details-section">
                <h4 class="details-title">Emission Factor Breakdown</h4>
                <div class="emission-breakdown" id="emissionBreakdown">
                    <!-- Breakdown will be populated here -->
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>About CO₂ Emissions Analysis</h3>
            <p>
                Carbon dioxide emissions from HVAC systems contribute significantly to a building's overall 
                environmental impact. This calculator helps quantify emissions based on energy consumption 
                and fuel type, providing insight for sustainability planning and environmental reporting.
            </p>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Key Features:</h4>
            <ul class="tips-list">
                <li><strong>Fuel-Specific Calculations:</strong> Emission factors for different energy sources</li>
                <li><strong>Real-World Equivalents:</strong> Translate emissions to understandable comparisons</li>
                <li><strong>Environmental Impact:</strong> Context for emissions in terms of climate impact</li>
                <li><strong>Benchmarking:</strong> Compare against industry standards and targets</li>
                <li><strong>Reduction Planning:</strong> Foundation for emission reduction strategies</li>
            </ul>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Emission Factors (kg CO₂/kWh):</h4>
            <div class="emission-factors">
                <div class="factor-item">
                    <span>Grid Electricity (US Average)</span>
                    <span>0.50 kg CO₂/kWh</span>
                </div>
                <div class="factor-item">
                    <span>Natural Gas</span>
                    <span>0.185 kg CO₂/kWh</span>
                </div>
                <div class="factor-item">
                    <span>Fuel Oil</span>
                    <span>0.25 kg CO₂/kWh</span>
                </div>
                <div class="factor-item">
                    <span>Propane</span>
                    <span>0.214 kg CO₂/kWh</span>
                </div>
                <div class="factor-item">
                    <span>District Heating (varies)</span>
                    <span>0.07-0.12 kg CO₂/kWh</span>
                </div>
            </div>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Emission Reduction Strategies:</h4>
            <ul class="tips-list">
                <li>Switch to renewable energy sources (solar, wind)</li>
                <li>Improve building envelope and insulation</li>
                <li>Implement high-efficiency HVAC equipment</li>
                <li>Use district heating with renewable sources</li>
                <li>Optimize system controls and scheduling</li>
                <li>Consider carbon offset programs for unavoidable emissions</li>
            </ul>
        </div>
    </div>

    <script>
        function calculateCO2Emissions() {
            const energy = parseFloat(document.getElementById('annualEnergy').value);
            const fuelType = document.getElementById('fuelType').value;
            const customFactor = parseFloat(document.getElementById('emissionFactor').value);
            const buildingType = document.getElementById('buildingType').value;
            
            if (!energy) {
                showNotification('Please enter annual energy consumption', 'info');
                return;
            }
            
            // CO2 emission factors (kg CO2 per kWh)
            const emissionFactors = {
                'electricity': 0.50, // US Grid average
                'naturalGas': 0.185,
                'oil': 0.25,
                'propane': 0.214,
                'districtHeat': 0.09 // Assuming efficient district heating
            };
            
            const factor = customFactor || emissionFactors[fuelType] || 0.5;
            const annualCO2 = energy * factor; // kg CO2
            const annualCO2Tons = annualCO2 / 1000;
            const dailyCO2 = annualCO2 / 365;
            
            // Equivalents calculations
            const carsEquivalent = annualCO2Tons / 4.6; // Average car emits 4.6 tons CO2/year
            const treesEquivalent = annualCO2Tons / 0.022; // Average tree absorbs 22 kg CO2/year
            const homesEquivalent = annualCO2Tons / 7.5; // Average home emits 7.5 tons CO2/year
            
            // Display results
            const resultsGrid = document.getElementById('co2ResultsGrid');
            resultsGrid.innerHTML = `
                <div class="result-card">
                    <div class="result-label">Annual CO₂ Emissions</div>
                    <div class="result-value">${annualCO2.toLocaleString()}</div>
                    <div class="result-unit">kg CO₂/year</div>
                </div>
                <div class="result-card">
                    <div class="result-label">Annual CO₂ Emissions</div>
                    <div class="result-value">${annualCO2Tons.toFixed(1)}</div>
                    <div class="result-unit">metric tons/year</div>
                </div>
                <div class="result-card">
                    <div class="result-label">Daily CO₂ Emissions</div>
                    <div class="result-value">${dailyCO2.toFixed(1)}</div>
                    <div class="result-unit">kg CO₂/day</div>
                </div>
                <div class="result-card">
                    <div class="result-label">Emission Factor</div>
                    <div class="result-value">${factor.toFixed(3)}</div>
                    <div class="result-unit">kg CO₂/kWh</div>
                </div>
            `;
            
            // Display equivalents
            const equivalentsGrid = document.getElementById('equivalentsGrid');
            equivalentsGrid.innerHTML = `
                <div class="equivalent-item equivalent-card">
                    <div class="equivalent-icon">🚗</div>
                    <div class="equivalent-value">${carsEquivalent.toFixed(1)}</div>
                    <div class="equivalent-label">Cars driven for 1 year</div>
                </div>
                <div class="equivalent-item equivalent-card">
                    <div class="equivalent-icon">🌳</div>
                    <div class="equivalent-value">${Math.round(treesEquivalent)}</div>
                    <div class="equivalent-label">Trees needed to offset</div>
                </div>
                <div class="equivalent-item equivalent-card">
                    <div class="equivalent-icon">🏠</div>
                    <div class="equivalent-value">${homesEquivalent.toFixed(1)}</div>
                    <div class="equivalent-label">Average homes' annual emissions</div>
                </div>
                <div class="equivalent-item equivalent-card">
                    <div class="equivalent-icon">✈️</div>
                    <div class="equivalent-value">${(annualCO2Tons * 0.6).toFixed(0)}</div>
                    <div class="equivalent-label">Flights from NY to LA</div>
                </div>
            `;
            
            // Display emission breakdown
            const breakdown = document.getElementById('emissionBreakdown');
            breakdown.innerHTML = `
                <div class="breakdown-item">
                    <div class="breakdown-fuel">Energy Consumption</div>
                    <div class="breakdown-value">${energy.toLocaleString()} kWh/year</div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-fuel">Emission Factor (${fuelType})</div>
                    <div class="breakdown-value">${factor.toFixed(3)} kg CO₂/kWh</div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-fuel">Total Emissions</div>
                    <div class="breakdown-value">${annualCO2.toLocaleString()} kg CO₂/year</div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-fuel">In Metric Tons</div>
                    <div class="breakdown-value">${annualCO2Tons.toFixed(1)} tons CO₂/year</div>
                </div>
            `;
            
            // Show results section
            document.getElementById('co2Results').classList.add('show');
            
            // Save to localStorage
            const calculation = {
                type: 'CO₂ Emissions',
                inputs: { energy, fuelType, customFactor: customFactor || null, buildingType },
                results: { 
                    annualCO2, 
                    annualCO2Tons, 
                    dailyCO2, 
                    factor,
                    carsEquivalent,
                    treesEquivalent,
                    homesEquivalent
                },
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
