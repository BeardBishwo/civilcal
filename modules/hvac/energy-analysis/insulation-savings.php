<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insulation Savings Calculator - HVAC Energy Analysis</title>
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
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
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
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
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
            border-color: #ffffff;
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
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
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
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
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
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }
        
        .result-card.savings {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .result-card.improvement {
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
        
        .savings-breakdown {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .breakdown-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #ffffff;
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
        
        .roi-analysis {
            background: rgba(40, 167, 69, 0.1);
            padding: 1.5rem;
            border-radius: 12px;
            margin-top: 1.5rem;
        }
        
        .roi-title {
            color: #2c3e50;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .roi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .roi-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #28a745;
        }
        
        .roi-period {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-bottom: 0.5rem;
        }
        
        .roi-value {
            font-size: 1.2rem;
            color: #28a745;
            font-weight: 700;
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
        
        .r-value-guide {
            background: rgba(52, 73, 94, 0.05);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .r-value-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(52, 73, 94, 0.1);
        }
        
        .r-value-item:last-child {
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
            <h1>Insulation Savings Calculator</h1>
            <p>Calculate energy savings and cost benefits from improved building insulation and thermal envelope upgrades</p>
        </div>
        
        <div class="calculator-section">
            <h3 style="color: #2c3e50; margin-bottom: 1.5rem; display: flex; align-items: center;">
                <span style="font-size: 1.5rem; margin-right: 0.75rem;">🏠</span>
                Building & Insulation Parameters
            </h3>
            
            <div class="input-grid">
                <div class="input-group">
                    <label for="insulationArea">Insulated Area</label>
                    <div class="input-wrapper">
                        <input type="number" id="insulationArea" step="0.1" placeholder="Enter area">
                        <span class="unit-label">m²</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="currentR">Current R-Value</label>
                    <div class="input-wrapper">
                        <input type="number" id="currentR" step="0.1" placeholder="Current R-value">
                        <span class="unit-label">R</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="newR">New R-Value</label>
                    <div class="input-wrapper">
                        <input type="number" id="newR" step="0.1" placeholder="Target R-value">
                        <span class="unit-label">R</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="heatingDD">Heating Degree Days</label>
                    <div class="input-wrapper">
                        <input type="number" id="heatingDD" step="100" value="3000" placeholder="Annual heating degree days">
                        <span class="unit-label">°C-days</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="insulationCost">Insulation Cost (Optional)</label>
                    <div class="input-wrapper">
                        <input type="number" id="insulationCost" step="1" placeholder="Enter cost">
                        <span class="unit-label">$/m²</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="energyCost">Energy Cost (Optional)</label>
                    <div class="input-wrapper">
                        <input type="number" id="energyCost" step="0.01" value="0.10" placeholder="Enter energy cost">
                        <span class="unit-label">$/kWh</span>
                    </div>
                </div>
            </div>
            
            <button class="calculate-btn" onclick="calculateInsulationSavings()">
                Calculate Insulation Savings
            </button>
        </div>
        
        <div class="results-section" id="insulationResults">
            <div class="results-header">
                <div class="results-icon">🏠</div>
                <h3 class="results-title">Insulation Savings Analysis</h3>
            </div>
            
            <div class="results-grid" id="insulationResultsGrid">
                <!-- Results will be populated here -->
            </div>
            
            <div class="details-section">
                <h4 class="details-title">Energy Savings Breakdown</h4>
                <div class="savings-breakdown" id="savingsBreakdown">
                    <!-- Breakdown will be populated here -->
                </div>
            </div>
            
            <div class="roi-analysis" id="roiAnalysis" style="display: none;">
                <h4 class="roi-title">Return on Investment Analysis</h4>
                <div class="roi-grid" id="roiGrid">
                    <!-- ROI analysis will be populated here -->
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>About Insulation Savings Analysis</h3>
            <p>
                Improving building insulation is one of the most cost-effective ways to reduce energy consumption 
                and heating/cooling costs. This calculator helps quantify the energy savings and financial benefits 
                of insulation upgrades based on current performance and improvement targets.
            </p>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Key Features:</h4>
            <ul class="tips-list">
                <li><strong>Heat Loss Reduction:</strong> Calculate improved thermal resistance</li>
                <li><strong>Energy Savings:</strong> Quantify annual energy consumption reduction</li>
                <li><strong>Cost Analysis:</strong> Calculate annual cost savings and payback period</li>
                <li><strong>ROI Analysis:</strong> Long-term return on investment calculations</li>
                <li><strong>Environmental Impact:</strong> Reduced carbon emissions from energy savings</li>
            </ul>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Common R-Values by Location:</h4>
            <div class="r-value-guide">
                <div class="r-value-item">
                    <span>Attic Insulation</span>
                    <span>R-30 to R-60</span>
                </div>
                <div class="r-value-item">
                    <span>Wall Insulation</span>
                    <span>R-13 to R-21</span>
                </div>
                <div class="r-value-item">
                    <span>Basement Walls</span>
                    <span>R-10 to R-15</span>
                </div>
                <div class="r-value-item">
                    <span>Floor Insulation</span>
                    <span>R-13 to R-30</span>
                </div>
                <div class="r-value-item">
                    <span>Cathedral Ceiling</span>
                    <span>R-30 to R-60</span>
                </div>
            </div>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Insulation Types and R-Values per Inch:</h4>
            <ul class="tips-list">
                <li><strong>Fiberglass Batts:</strong> R-3.1 to R-4.3 per inch</li>
                <li><strong>Cellulose (Blown-in):</strong> R-3.2 to R-3.8 per inch</li>
                <li><strong>Spray Foam (Open Cell):</strong> R-3.6 to R-3.9 per inch</li>
                <li><strong>Spray Foam (Closed Cell):</strong> R-6.0 to R-6.5 per inch</li>
                <li><strong>Rigid Foam Boards:</strong> R-4.0 to R-6.5 per inch</li>
            </ul>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Energy Savings Tips:</h4>
            <ul class="tips-list">
                <li>Air sealing is as important as insulation - seal gaps and cracks first</li>
                <li>Attic insulation provides the highest return on investment</li>
                <li>Insulate ductwork in unconditioned spaces</li>
                <li>Consider upgrade timing during major renovations</li>
                <li>Check local utility rebates and tax incentives</li>
            </ul>
        </div>
    </div>

    <script>
        function calculateInsulationSavings() {
            const area = parseFloat(document.getElementById('insulationArea').value);
            const currentR = parseFloat(document.getElementById('currentR').value);
            const newR = parseFloat(document.getElementById('newR').value);
            const heatingDD = parseFloat(document.getElementById('heatingDD').value);
            const insulationCost = parseFloat(document.getElementById('insulationCost').value);
            const energyCost = parseFloat(document.getElementById('energyCost').value) || 0.10;
            
            if (!area || !currentR || !newR || !heatingDD) {
                showNotification('Please enter all required values (area, current R, new R, heating degree days)', 'info');
                return;
            }
            
            if (newR <= currentR) {
                showNotification('New R-value must be greater than current R-value', 'info');
                return;
            }
            
            // Calculate U-values (heat transfer coefficient)
            const uCurrent = 1 / currentR; // W/m²°C
            const uNew = 1 / newR; // W/m²°C
            
            // Heat loss reduction (W)
            const heatLossReduction = (uCurrent - uNew) * area;
            
            // Annual heating energy savings (kWh/year)
            // Formula: 24 hours × degree days × area × (U1 - U2) / 1000
            const annualEnergySavings = 24 * heatingDD * area * (uCurrent - uNew) / 1000;
            
            // Annual cost savings
            const annualCostSavings = annualEnergySavings * energyCost;
            
            // Calculate percentage improvement
            const heatLossReductionPercent = ((uCurrent - uNew) / uCurrent) * 100;
            
            // Calculate payback period if cost is provided
            let paybackYears = null;
            let totalInvestment = null;
            if (insulationCost) {
                totalInvestment = area * insulationCost;
                paybackYears = totalInvestment / annualCostSavings;
            }
            
            // Display results
            const resultsGrid = document.getElementById('insulationResultsGrid');
            resultsGrid.innerHTML = `
                <div class="result-card">
                    <div class="result-label">Heat Loss Reduction</div>
                    <div class="result-value">${heatLossReductionPercent.toFixed(1)}</div>
                    <div class="result-unit">% reduction</div>
                </div>
                <div class="result-card savings">
                    <div class="result-label">Annual Energy Savings</div>
                    <div class="result-value">${annualEnergySavings.toFixed(0)}</div>
                    <div class="result-unit">kWh/year</div>
                </div>
                <div class="result-card savings">
                    <div class="result-label">Annual Cost Savings</div>
                    <div class="result-value">$${annualCostSavings.toFixed(0)}</div>
                    <div class="result-unit">$/year</div>
                </div>
                <div class="result-card improvement">
                    <div class="result-label">U-Value Improvement</div>
                    <div class="result-value">${(uCurrent - uNew).toFixed(4)}</div>
                    <div class="result-unit">W/m²°C reduction</div>
                </div>
            `;
            
            if (paybackYears) {
                document.getElementById('insulationResultsGrid').innerHTML += `
                    <div class="result-card">
                        <div class="result-label">Payback Period</div>
                        <div class="result-value">${paybackYears.toFixed(1)}</div>
                        <div class="result-unit">years</div>
                    </div>
                `;
            }
            
            // Display savings breakdown
            const breakdown = document.getElementById('savingsBreakdown');
            breakdown.innerHTML = `
                <div class="breakdown-item">
                    <div class="breakdown-formula">Current U-Value = 1 / R</div>
                    <div class="breakdown-value">1 / ${currentR} = ${uCurrent.toFixed(4)} W/m²°C</div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-formula">New U-Value = 1 / R</div>
                    <div class="breakdown-value">1 / ${newR} = ${uNew.toFixed(4)} W/m²°C</div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-formula">Heat Loss Reduction</div>
                    <div class="breakdown-value">${(uCurrent - uNew).toFixed(4)} W/m²°C</div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-formula">Annual Energy Savings</div>
                    <div class="breakdown-value">${annualEnergySavings.toFixed(0)} kWh/year</div>
                </div>
            `;
            
            // ROI Analysis if cost is provided
            if (paybackYears) {
                const roiAnalysis = document.getElementById('roiAnalysis');
                const roiGrid = document.getElementById('roiGrid');
                
                // Calculate long-term savings
                const periods = [5, 10, 15, 20];
                let roiHTML = '';
                
                periods.forEach(period => {
                    const cumulativeSavings = annualCostSavings * period;
                    const netBenefit = cumulativeSavings - totalInvestment;
                    const roi = (netBenefit / totalInvestment) * 100;
                    
                    roiHTML += `
                        <div class="roi-item">
                            <div class="roi-period">${period} Year${period > 1 ? 's' : ''}</div>
                            <div class="roi-value">${roi.toFixed(0)}% ROI</div>
                            <div class="roi-value">$${netBenefit.toFixed(0)} net</div>
                        </div>
                    `;
                });
                
                roiGrid.innerHTML = roiHTML;
                roiAnalysis.style.display = 'block';
            }
            
            // Show results section
            document.getElementById('insulationResults').classList.add('show');
            
            // Save to localStorage
            const calculation = {
                type: 'Insulation Savings',
                inputs: { area, currentR, newR, heatingDD, insulationCost: insulationCost || null, energyCost },
                results: { 
                    heatLossReductionPercent,
                    annualEnergySavings, 
                    annualCostSavings,
                    uCurrent,
                    uNew,
                    paybackYears: paybackYears || null,
                    totalInvestment: totalInvestment || null
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
