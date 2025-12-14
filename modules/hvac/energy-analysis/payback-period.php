<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payback Period Calculator - HVAC Energy Analysis</title>
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
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }
        
        .result-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .result-card.assessment {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .result-card.rate-of-return {
            background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
            color: white;
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
        
        .assessment-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 0.5rem;
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
        
        .long-term-analysis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .long-term-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .long-term-period {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-bottom: 0.5rem;
        }
        
        .long-term-value {
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
            <h1>Payback Period Calculator</h1>
            <p>Determine the time required to recover investment costs through energy savings and operational improvements</p>
        </div>
        
        <div class="calculator-section">
            <h3 style="color: #2c3e50; margin-bottom: 1.5rem; display: flex; align-items: center;">
                <span style="font-size: 1.5rem; margin-right: 0.75rem;">📊</span>
                Investment Parameters
            </h3>
            
            <div class="input-grid">
                <div class="input-group">
                    <label for="initialCost">Initial Investment Cost</label>
                    <div class="input-wrapper">
                        <input type="number" id="initialCost" step="100" placeholder="Enter total investment">
                        <span class="unit-label">$</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="annualSavings">Annual Energy Savings</label>
                    <div class="input-wrapper">
                        <input type="number" id="annualSavings" step="10" placeholder="Annual savings amount">
                        <span class="unit-label">$/year</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="maintenanceCost">Annual Maintenance Cost (Optional)</label>
                    <div class="input-wrapper">
                        <input type="number" id="maintenanceCost" step="50" placeholder="0" value="0">
                        <span class="unit-label">$/year</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="discountRate">Discount Rate (Optional)</label>
                    <div class="input-wrapper">
                        <input type="number" id="discountRate" step="0.1" placeholder="5.0" value="5.0">
                        <span class="unit-label">%</span>
                    </div>
                </div>
            </div>
            
            <button class="calculate-btn" onclick="calculatePaybackPeriod()">
                Calculate Payback Period
            </button>
        </div>
        
        <div class="results-section" id="paybackResults">
            <div class="results-header">
                <div class="results-icon">📊</div>
                <h3 class="results-title">Payback Period Analysis</h3>
            </div>
            
            <div class="results-grid" id="paybackResultsGrid">
                <!-- Results will be populated here -->
            </div>
            
            <div class="details-section">
                <h4 class="details-title">Long-term Investment Analysis</h4>
                <div class="long-term-analysis" id="longTermAnalysis">
                    <!-- Long-term analysis will be populated here -->
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>About Payback Period Analysis</h3>
            <p>
                Payback period analysis helps evaluate the financial viability of HVAC investments by determining 
                how long it takes for energy savings to recover the initial investment cost. This metric is 
                essential for making informed investment decisions and prioritizing energy efficiency projects.
            </p>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Key Features:</h4>
            <ul class="tips-list">
                <li><strong>Simple Payback:</strong> Basic time period to recover initial investment</li>
                <li><strong>Discounted Payback:</strong> Considers time value of money using discount rate</li>
                <li><strong>Investment Assessment:</strong> Categorizes projects by financial viability</li>
                <li><strong>Long-term Analysis:</strong> Shows cumulative savings over multiple years</li>
                <li><strong>Rate of Return:</strong> Annual return on investment percentage</li>
            </ul>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Investment Assessment Guidelines:</h4>
            <ul class="tips-list">
                <li><strong>Under 2 years:</strong> Excellent investment - immediate financial benefit</li>
                <li><strong>2-5 years:</strong> Good investment - solid financial return</li>
                <li><strong>5-10 years:</strong> Moderate investment - acceptable for certain projects</li>
                <li><strong>Over 10 years:</strong> Consider alternatives - long payback period</li>
            </ul>
            
            <h4 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Common HVAC Investment Projects:</h4>
            <ul class="tips-list">
                <li>High-efficiency equipment upgrades (2-5 year payback)</li>
                <li>Variable speed drives and controls (1-3 year payback)</li>
                <li>Building envelope improvements (5-15 year payback)</li>
                <li>Energy management systems (2-4 year payback)</li>
                <li>Lighting upgrades (1-3 year payback)</li>
            </ul>
        </div>
    </div>

    <script>
        function calculatePaybackPeriod() {
            const initialCost = parseFloat(document.getElementById('initialCost').value);
            const annualSavings = parseFloat(document.getElementById('annualSavings').value);
            const maintenanceCost = parseFloat(document.getElementById('maintenanceCost').value) || 0;
            const discountRate = parseFloat(document.getElementById('discountRate').value) / 100;
            
            if (!initialCost || !annualSavings) {
                showNotification('Please enter both initial cost and annual savings', 'info');
                return;
            }
            
            // Net annual savings (after maintenance)
            const netAnnualSavings = annualSavings - maintenanceCost;
            
            if (netAnnualSavings <= 0) {
                showNotification('Net annual savings must be greater than zero', 'info');
                return;
            }
            
            // Simple payback period
            const simplePaybackYears = initialCost / netAnnualSavings;
            const simplePaybackMonths = simplePaybackYears * 12;
            
            // Discounted payback period
            let cumulativeCashFlow = -initialCost;
            let discountedPaybackYears = 0;
            let year = 1;
            
            while (cumulativeCashFlow < 0 && year <= 50) { // Cap at 50 years
                const discountedCashFlow = netAnnualSavings / Math.pow(1 + discountRate, year);
                cumulativeCashFlow += discountedCashFlow;
                discountedPaybackYears = year;
                year++;
            }
            
            // Annual return on investment
            const annualROI = (netAnnualSavings / initialCost) * 100;
            
            // Investment assessment
            let assessment = '';
            let assessmentColor = '';
            if (simplePaybackYears < 2) {
                assessment = 'Excellent Investment';
                assessmentColor = 'Outstanding financial return';
            } else if (simplePaybackYears < 5) {
                assessment = 'Good Investment';
                assessmentColor = 'Solid financial return';
            } else if (simplePaybackYears < 10) {
                assessment = 'Moderate Investment';
                assessmentColor = 'Acceptable for certain projects';
            } else {
                assessment = 'Consider Alternatives';
                assessmentColor = 'Long payback period - evaluate other options';
            }
            
            // Display results
            const resultsGrid = document.getElementById('paybackResultsGrid');
            resultsGrid.innerHTML = `
                <div class="result-card primary">
                    <div class="result-label">Simple Payback Period</div>
                    <div class="result-value">${simplePaybackYears.toFixed(1)}</div>
                    <div class="result-unit">years</div>
                    <div class="result-unit">(${simplePaybackMonths.toFixed(0)} months)</div>
                </div>
                <div class="result-card primary">
                    <div class="result-label">Discounted Payback</div>
                    <div class="result-value">${discountedPaybackYears.toFixed(1)}</div>
                    <div class="result-unit">years</div>
                    <div class="assessment-badge">@ ${(discountRate * 100).toFixed(1)}% discount rate</div>
                </div>
                <div class="result-card rate-of-return">
                    <div class="result-label">Annual ROI</div>
                    <div class="result-value">${annualROI.toFixed(1)}%</div>
                    <div class="result-unit">return on investment</div>
                </div>
                <div class="result-card assessment">
                    <div class="result-label">Investment Assessment</div>
                    <div class="result-value">${assessment}</div>
                    <div class="result-unit">${assessmentColor}</div>
                </div>
            `;
            
            // Long-term analysis
            const longTerm = document.getElementById('longTermAnalysis');
            const periods = [3, 5, 10, 15, 20];
            let longTermHTML = '';
            
            periods.forEach(period => {
                const cumulativeSavings = netAnnualSavings * period;
                const netBenefit = cumulativeSavings - initialCost;
                const roi = (netBenefit / initialCost) * 100;
                
                longTermHTML += `
                    <div class="long-term-item">
                        <div class="long-term-period">${period} Year${period > 1 ? 's' : ''}</div>
                        <div class="long-term-value">Net Benefit: $${netBenefit.toLocaleString()}</div>
                        <div class="long-term-value">Total ROI: ${roi.toFixed(0)}%</div>
                    </div>
                `;
            });
            
            longTerm.innerHTML = longTermHTML;
            
            // Show results section
            document.getElementById('paybackResults').classList.add('show');
            
            // Save to localStorage
            const calculation = {
                type: 'Payback Period',
                inputs: { initialCost, annualSavings, maintenanceCost, discountRate: discountRate * 100 },
                results: { 
                    simplePaybackYears, 
                    simplePaybackMonths, 
                    discountedPaybackYears, 
                    annualROI, 
                    netAnnualSavings 
                },
                assessment: { assessment, assessmentColor },
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
