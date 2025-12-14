<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Pump Driver Power Calculator - AEC Calculator</title>
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
        .power-curve {
            background: #374151;
            border: 1px solid #4b5563;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }
        .curve-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="calculator-container">
        <a href="../../hvac.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Fire Protection Toolkit
        </a>

        <h1><i class="fas fa-bolt"></i> Fire Pump Driver Power Calculator</h1>
        <p>Calculate required driver power for fire pumps including electric motors, diesel engines, and steam turbines.</p>

        <form id="driverPowerForm">
            <h3><i class="fas fa-fire-extinguisher"></i> Pump Performance Data</h3>
            
            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="ratedFlow">Rated Flow (GPM)</label>
                        <input type="number" id="ratedFlow" step="0.1" value="1500" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="ratedPressure">Rated Pressure (PSI)</label>
                        <input type="number" id="ratedPressure" step="0.1" value="100" required>
                    </div>
                </div>
            </div>

            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="churnPressure">Churn Pressure (PSI)</label>
                        <input type="number" id="churnPressure" step="0.1" value="120" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="pumpEfficiency">Pump Efficiency (%)</label>
                        <input type="number" id="pumpEfficiency" step="0.1" value="75" min="0" max="100" required>
                    </div>
                </div>
            </div>

            <h3><i class="fas fa-cog"></i> Driver Configuration</h3>
            
            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="driverType">Driver Type</label>
                        <select id="driverType" required>
                            <option value="electric">Electric Motor</option>
                            <option value="diesel">Diesel Engine</option>
                            <option value="steam">Steam Turbine</option>
                        </select>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="serviceFactor">Service Factor</label>
                        <input type="number" id="serviceFactor" step="0.1" value="1.15" required>
                    </div>
                </div>
            </div>

            <h3><i class="fas fa-thermometer-half"></i> Operating Conditions</h3>
            
            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="ambientTemp">Ambient Temperature (°F)</label>
                        <input type="number" id="ambientTemp" step="0.1" value="70" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="altitude">Operating Elevation (ft)</label>
                        <input type="number" id="altitude" step="1" value="0" required>
                    </div>
                </div>
            </div>

            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="dutyCycle">Duty Cycle</label>
                        <select id="dutyCycle" required>
                            <option value="intermittent">Intermittent (30 min max)</option>
                            <option value="continuous">Continuous</option>
                            <option value="emergency">Emergency Only</option>
                        </select>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="startupFrequency">Startup Frequency (per day)</label>
                        <input type="number" id="startupFrequency" step="1" value="1" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="calculate-btn">
                <i class="fas fa-calculator"></i> Calculate Driver Power Requirements
            </button>
        </form>

        <div id="results" class="result-card">
            <div class="result-header">
                <i class="fas fa-chart-bar"></i> Driver Power Analysis
            </div>
            <div id="resultsContent"></div>
        </div>
    </div>

    <script>
        document.getElementById('driverPowerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get input values
            const ratedFlow = parseFloat(document.getElementById('ratedFlow').value);
            const ratedPressure = parseFloat(document.getElementById('ratedPressure').value);
            const churnPressure = parseFloat(document.getElementById('churnPressure').value);
            const pumpEfficiency = parseFloat(document.getElementById('pumpEfficiency').value) / 100;
            const driverType = document.getElementById('driverType').value;
            const serviceFactor = parseFloat(document.getElementById('serviceFactor').value);
            const ambientTemp = parseFloat(document.getElementById('ambientTemp').value);
            const altitude = parseFloat(document.getElementById('altitude').value);
            const dutyCycle = document.getElementById('dutyCycle').value;
            const startupFrequency = parseInt(document.getElementById('startupFrequency').value);
            
            // Calculate hydraulic power at rated conditions
            const hydraulicPower = (ratedFlow * ratedPressure) / 1714; // HP = (GPM * PSI) / 1714
            
            // Calculate brake horsepower (BHP)
            const brakeHorsepower = hydraulicPower / pumpEfficiency;
            
            // Calculate power at churn conditions
            const churnHydraulicPower = (100 * churnPressure) / 1714; // Assuming 100 GPM at churn
            const churnBHP = churnHydraulicPower / pumpEfficiency;
            
            // Select driver efficiency based on type
            let driverEfficiency, driverPower;
            switch(driverType) {
                case 'electric':
                    driverEfficiency = 0.92; // 92% for premium efficiency motor
                    driverPower = brakeHorsepower / driverEfficiency;
                    break;
                case 'diesel':
                    driverEfficiency = 0.38; // 38% for diesel engine
                    driverPower = brakeHorsepower / driverEfficiency;
                    break;
                case 'steam':
                    driverEfficiency = 0.75; // 75% for steam turbine
                    driverPower = brakeHorsepower / driverEfficiency;
                    break;
            }
            
            // Apply service factor
            const requiredDriverPower = driverPower * serviceFactor;
            
            // Temperature corrections
            const tempCorrection = 1 + ((ambientTemp - 70) / 100) * 0.05; // 5% per 100°F above 70°F
            const correctedDriverPower = requiredDriverPower * tempCorrection;
            
            // Altitude correction
            const altitudeCorrection = 1 - (altitude / 10000) * 0.03; // 3% per 1000 ft
            const finalDriverPower = correctedDriverPower / altitudeCorrection;
            
            // Duty cycle factors
            let dutyFactor = 1.0;
            if (dutyCycle === 'continuous') dutyFactor = 1.1;
            else if (dutyCycle === 'intermittent') dutyFactor = 1.0;
            else if (dutyCycle === 'emergency') dutyFactor = 0.9;
            
            // Startup frequency factors
            const startupFactor = 1 + (startupFrequency * 0.02); // 2% per startup per day
            const adjustedPower = finalDriverPower * dutyFactor * startupFactor;
            
            // Power curve analysis
            let powerCurveHTML = '<div class="power-curve"><h4><i class="fas fa-chart-line"></i> Power Curve Analysis</h4>';
            
            // Points for power curve
            const flowPoints = [0, 25, 50, 75, 100];
            const pressurePoints = [churnPressure, ratedPressure * 1.1, ratedPressure, ratedPressure * 0.9, ratedPressure * 0.7];
            
            flowPoints.forEach((percent, index) => {
                const flow = (ratedFlow * percent) / 100;
                const pressure = pressurePoints[index];
                const curveHP = (flow * pressure) / (1714 * pumpEfficiency);
                const curveDriverHP = curveHP / driverEfficiency;
                
                powerCurveHTML += `
                    <div class="curve-item">
                        <span>${percent}% Flow (${flow.toFixed(0)} GPM):</span>
                        <span>${curveDriverHP.toFixed(1)} HP</span>
                    </div>
                `;
            });
            
            powerCurveHTML += '</div>';
            
            // Display results
            let resultsHTML = `
                <div class="result-item">
                    <span class="result-label">Hydraulic Power (HP):</span>
                    <span class="result-value">${hydraulicPower.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Brake Horsepower (BHP):</span>
                    <span class="result-value">${brakeHorsepower.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Churn Power (HP):</span>
                    <span class="result-value">${churnBHP.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Driver Efficiency (%):</span>
                    <span class="result-value">${(driverEfficiency * 100).toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Required Driver Power (HP):</span>
                    <span class="result-value">${Math.ceil(requiredDriverPower)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Temperature Corrected (HP):</span>
                    <span class="result-value">${Math.ceil(correctedDriverPower)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Altitude Corrected (HP):</span>
                    <span class="result-value">${Math.ceil(finalDriverPower)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Final Rating (HP):</span>
                    <span class="result-value">${Math.ceil(adjustedPower)}</span>
                </div>
            `;
            
            // Add power curve analysis
            resultsHTML += powerCurveHTML;
            
            // NFPA compliance check
            if (adjustedPower >= brakeHorsepower * serviceFactor) {
                resultsHTML += `
                    <div class="info-box">
                        <i class="fas fa-check-circle"></i>
                        <strong>NFPA 20 Compliance:</strong> Driver power rating meets or exceeds required capacity including all correction factors.
                    </div>
                `;
            } else {
                resultsHTML += `
                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Review Required:</strong> Driver power rating may be insufficient. Verify all correction factors and consult NFPA 20.
                    </div>
                `;
            }
            
            // Driver-specific recommendations
            if (driverType === 'electric') {
                resultsHTML += `
                    <div class="info-box">
                        <i class="fas fa-bolt"></i>
                        <strong>Electric Motor Notes:</strong>
                        <ul style="margin: 5px 0 0 20px;">
                            <li>Consider premium efficiency motors (NEMA Premium)</li>
                            <li>Verify adequate starting current and voltage drop</li>
                            <li>Ensure backup power supply capacity</li>
                            <li>Check enclosure type for environmental conditions</li>
                        </ul>
                    </div>
                `;
            } else if (driverType === 'diesel') {
                resultsHTML += `
                    <div class="info-box">
                        <i class="fas fa-cog"></i>
                        <strong>Diesel Engine Notes:</strong>
                        <ul style="margin: 5px 0 0 20px;">
                            <li>Ensure 6-hour fuel supply at rated load</li>
                            <li>Verify adequate cooling and ventilation</li>
                            <li>Check battery capacity for starting</li>
                            <li>Consider local emissions requirements</li>
                        </ul>
                    </div>
                `;
            } else if (driverType === 'steam') {
                resultsHTML += `
                    <div class="info-box">
                        <i class="fas fa-fire"></i>
                        <strong>Steam Turbine Notes:</strong>
                        <ul style="margin: 5px 0 0 20px;">
                            <li>Verify adequate steam supply pressure and flow</li>
                            <li>Check condensate removal system capacity</li>
                            <li>Consider steam header capacity and pressure</li>
                            <li>Verify governor and overspeed protection</li>
                        </ul>
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
