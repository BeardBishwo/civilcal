<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Pump Jockey Pump Calculator - AEC Calculator</title>
    <link rel="stylesheet" href="../../assets/css/theme.css">
    <link rel="stylesheet" href="../../assets/css/fire.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .calculator-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #fff;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 5px;
            background: #2c2c2c;
            color: #fff;
            font-size: 16px;
        }
        .input-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .input-col {
            flex: 1;
        }
        .calculate-btn {
            background: linear-gradient(45deg, #dc2626, #b91c1c);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .calculate-btn:hover {
            background: linear-gradient(45deg, #b91c1c, #991b1b);
            transform: translateY(-2px);
        }
        .result-card {
            background: linear-gradient(135deg, #1f2937, #374151);
            border: 2px solid #dc2626;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            color: #fff;
            display: none;
        }
        .result-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #dc2626;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .result-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #444;
        }
        .result-label {
            font-weight: bold;
            color: #f3f4f6;
        }
        .result-value {
            color: #dc2626;
            font-weight: bold;
        }
        .warning-box {
            background: #fbbf24;
            color: #92400e;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            border-left: 4px solid #f59e0b;
        }
        .info-box {
            background: #3b82f6;
            color: #1e40af;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            border-left: 4px solid #2563eb;
        }
        .back-btn {
            background: #6b7280;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
            transition: background 0.3s ease;
        }
        .back-btn:hover {
            background: #4b5563;
        }
        .operating-cycle {
            background: #374151;
            border: 1px solid #4b5563;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }
        .cycle-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
        }
        .flow-graph {
            background: #1f2937;
            border: 1px solid #4b5563;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
            text-align: center;
        }
        .graph-line {
            font-family: monospace;
            color: #10b981;
            font-size: 12px;
            margin: 2px 0;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="calculator-container">
        <a href="../../hvac.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Fire Protection Toolkit
        </a>

        <h1><i class="fas fa-cog"></i> Fire Pump Jockey Pump Calculator</h1>
        <p>Calculate jockey pump requirements for maintaining system pressure and minimizing main pump cycling.</p>

        <form id="jockeyPumpForm">
            <h3><i class="fas fa-fire-extinguisher"></i> System Parameters</h3>
            
            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="systemPressure">System Pressure (PSI)</label>
                        <input type="number" id="systemPressure" step="0.1" value="100" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="pressureLoss">Allowable Pressure Loss (PSI)</label>
                        <input type="number" id="pressureLoss" step="0.1" value="5" required>
                    </div>
                </div>
            </div>

            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="systemVolume">System Volume (gallons)</label>
                        <input type="number" id="systemVolume" step="1" value="1000" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="mainPumpFlow">Main Pump Flow (GPM)</label>
                        <input type="number" id="mainPumpFlow" step="1" value="1500" required>
                    </div>
                </div>
            </div>

            <h3><i class="fas fa-tachometer-alt"></i> Pressure Control Settings</h3>
            
            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="startPressure">Jockey Start Pressure (PSI)</label>
                        <input type="number" id="startPressure" step="0.1" value="98" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="stopPressure">Jockey Stop Pressure (PSI)</label>
                        <input type="number" id="stopPressure" step="0.1" value="102" required>
                    </div>
                </div>
            </div>

            <h3><i class="fas fa-thermometer-half"></i> Operating Conditions</h3>
            
            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="minFlowRate">Minimum Flow Rate (GPM)</label>
                        <input type="number" id="minFlowRate" step="0.1" value="10" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="ambientTemp">Ambient Temperature (°F)</label>
                        <input type="number" id="ambientTemp" step="0.1" value="70" required>
                    </div>
                </div>
            </div>

            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="pumpType">Jockey Pump Type</label>
                        <select id="pumpType" required>
                            <option value="centrifugal">Centrifugal</option>
                            <option value="vertical">Vertical Turbine</option>
                            <option value="positive">Positive Displacement</option>
                        </select>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="efficiency">Pump Efficiency (%)</label>
                        <input type="number" id="efficiency" step="0.1" value="60" min="0" max="100" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="calculate-btn">
                <i class="fas fa-calculator"></i> Calculate Jockey Pump Requirements
            </button>
        </form>

        <div id="results" class="result-card">
            <div class="result-header">
                <i class="fas fa-chart-area"></i> Jockey Pump Analysis
            </div>
            <div id="resultsContent"></div>
        </div>
    </div>

    <script>
        document.getElementById('jockeyPumpForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get input values
            const systemPressure = parseFloat(document.getElementById('systemPressure').value);
            const pressureLoss = parseFloat(document.getElementById('pressureLoss').value);
            const systemVolume = parseFloat(document.getElementById('systemVolume').value);
            const mainPumpFlow = parseFloat(document.getElementById('mainPumpFlow').value);
            const startPressure = parseFloat(document.getElementById('startPressure').value);
            const stopPressure = parseFloat(document.getElementById('stopPressure').value);
            const minFlowRate = parseFloat(document.getElementById('minFlowRate').value);
            const ambientTemp = parseFloat(document.getElementById('ambientTemp').value);
            const pumpType = document.getElementById('pumpType').value;
            const efficiency = parseFloat(document.getElementById('efficiency').value) / 100;
            
            // Calculate pressure differential
            const pressureDifferential = stopPressure - startPressure;
            
            // Calculate jockey pump flow requirements
            const jockeyFlowRate = Math.max(
                minFlowRate,
                (systemVolume * pressureDifferential) / (60 * pressureLoss) // GPM = (gal * PSI_diff) / (min * PSI_loss)
            );
            
            // Calculate cycling frequency
            const pressureTimeConstant = systemVolume / (jockeyFlowRate * 0.8); // Time to lose pressure
            const cycleTime = (pressureDifferential / pressureLoss) * pressureTimeConstant;
            const cyclesPerHour = 3600 / cycleTime;
            
            // Calculate jockey pump power
            const jockeyPower = (jockeyFlowRate * systemPressure) / (1714 * efficiency);
            
            // NFPA 20 requirements
            const nfpaMinFlow = Math.max(10, mainPumpFlow * 0.01); // 1% of main pump flow or 10 GPM minimum
            const nfpaMaxCycles = 6; // Maximum 6 starts per hour for jockey pumps
            
            // Horsepower calculations
            const brakeHorsepower = jockeyPower;
            const driverPower = jockeyPower / 0.85; // Assuming 85% motor efficiency
            
            // Pressure control calculations
            const proportionalBand = (pressureDifferential / systemPressure) * 100;
            const deadbandPercent = (pressureDifferential / (startPressure + stopPressure) / 2) * 100;
            
            // Operating cycle analysis
            let operatingCycleHTML = '<div class="operating-cycle"><h4><i class="fas fa-sync"></i> Operating Cycle Analysis</h4>';
            
            // Simulate pressure cycling over time
            let currentPressure = stopPressure;
            let cycleCount = 0;
            const timeStep = 30; // 30 second intervals
            const maxTime = 3600; // 1 hour
            
            operatingCycleHTML += '<div class="flow-graph"><h5>Pressure vs Time (Hourly Cycle)</h5>';
            
            for (let time = 0; time <= maxTime; time += timeStep) {
                currentPressure -= (jockeyFlowRate * timeStep * 0.0003); // Pressure decay
                
                if (currentPressure <= startPressure && cycleCount < 24) {
                    // Jockey pump starts
                    currentPressure = stopPressure;
                    cycleCount++;
                    
                    if (cycleCount <= 10) { // Show first 10 cycles
                        operatingCycleHTML += `<div class="graph-line">Cycle ${cycleCount}: Pump ON at ${currentPressure.toFixed(1)} PSI</div>`;
                    }
                }
                
                if (cycleCount >= 24 && time >= 1800) {
                    break; // Stop simulation after reasonable cycles
                }
            }
            
            operatingCycleHTML += '</div></div>';
            
            // Display results
            let resultsHTML = `
                <div class="result-item">
                    <span class="result-label">Required Jockey Flow (GPM):</span>
                    <span class="result-value">${jockeyFlowRate.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Pressure Differential (PSI):</span>
                    <span class="result-value">${pressureDifferential.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Cycling Frequency (cycles/hour):</span>
                    <span class="result-value">${cyclesPerHour.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Cycle Time (minutes):</span>
                    <span class="result-value">${cycleTime.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Hydraulic Power (HP):</span>
                    <span class="result-value">${jockeyPower.toFixed(2)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Brake Horsepower (HP):</span>
                    <span class="result-value">${brakeHorsepower.toFixed(2)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Driver Power Rating (HP):</span>
                    <span class="result-value">${Math.ceil(driverPower)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Proportional Band (%):</span>
                    <span class="result-value">${proportionalBand.toFixed(1)}%</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Deadband (%):</span>
                    <span class="result-value">${deadbandPercent.toFixed(2)}%</span>
                </div>
            `;
            
            // Add operating cycle analysis
            resultsHTML += operatingCycleHTML;
            
            // NFPA compliance checks
            let complianceHTML = '';
            
            if (jockeyFlowRate >= nfpaMinFlow) {
                complianceHTML += `
                    <div class="info-box">
                        <i class="fas fa-check-circle"></i>
                        <strong>NFPA 20 Flow Compliance:</strong> Jockey pump flow (${jockeyFlowRate.toFixed(1)} GPM) meets minimum requirement (${nfpaMinFlow} GPM).
                    </div>
                `;
            } else {
                complianceHTML += `
                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Flow Review Required:</strong> Jockey pump flow (${jockeyFlowRate.toFixed(1)} GPM) below NFPA 20 minimum (${nfpaMinFlow} GPM).
                    </div>
                `;
            }
            
            if (cyclesPerHour <= nfpaMaxCycles) {
                complianceHTML += `
                    <div class="info-box">
                        <i class="fas fa-check-circle"></i>
                        <strong>Cycling Frequency Compliance:</strong> ${cyclesPerHour.toFixed(1)} cycles/hour within NFPA 20 limit of ${nfpaMaxCycles} cycles/hour.
                    </div>
                `;
            } else {
                complianceHTML += `
                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Cycling Review Required:</strong> ${cyclesPerHour.toFixed(1)} cycles/hour exceeds NFPA 20 limit of ${nfpaMaxCycles} cycles/hour.
                    </div>
                `;
            }
            
            resultsHTML += complianceHTML;
            
            // Pump type specific recommendations
            if (pumpType === 'centrifugal') {
                resultsHTML += `
                    <div class="info-box">
                        <i class="fas fa-cog"></i>
                        <strong>Centrifugal Jockey Pump Notes:</strong>
                        <ul style="margin: 5px 0 0 20px;">
                            <li>Most common type for jockey pump applications</li>
                            <li>Good efficiency at low flow rates</li>
                            <li>Consider variable speed drive for better control</li>
                            <li>Ensure adequate NPSH for suction conditions</li>
                        </ul>
                    </div>
                `;
            } else if (pumpType === 'vertical') {
                resultsHTML += `
                    <div class="info-box">
                        <i class="fas fa-arrow-up"></i>
                        <strong>Vertical Turbine Notes:</strong>
                        <ul style="margin: 5px 0 0 20px;">
                            <li>Good for deep well suction applications</li>
                            <li>Excellent for high pressure requirements</li>
                            <li>Consider cavitation at low flow rates</li>
                            <li>More expensive but reliable operation</li>
                        </ul>
                    </div>
                `;
            } else if (pumpType === 'positive') {
                resultsHTML += `
                    <div class="info-box">
                        <i class="fas fa-compress-arrows-alt"></i>
                        <strong>Positive Displacement Notes:</strong>
                        <ul style="margin: 5px 0 0 20px;">
                            <li>Excellent for very low flow rates</li>
                            <li>Constant flow regardless of pressure</li>
                            <li>Higher maintenance requirements</li>
                            <li>Consider for systems with very small leak rates</li>
                        </ul>
                    </div>
                `;
            }
            
            // Additional recommendations
            if (cyclesPerHour > 4) {
                resultsHTML += `
                    <div class="warning-box">
                        <i class="fas fa-clock"></i>
                        <strong>High Cycling Warning:</strong> Consider increasing system volume, pressure settings, or pump capacity to reduce cycling frequency.
                    </div>
                `;
            }
            
            document.getElementById('resultsContent').innerHTML = resultsHTML;
            document.getElementById('results').style.display = 'block';
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
