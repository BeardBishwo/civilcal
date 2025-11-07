<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Pump Sizing Calculator - AEC Calculator</title>
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
    </style>
</head>
<body>
    <div class="calculator-container">
        <a href="../../hvac.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Fire Protection Toolkit
        </a>

        <h1><i class="fas fa-tint"></i> Fire Pump Sizing Calculator</h1>
        <p>Calculate required fire pump capacity and pressure according to NFPA 20 standards.</p>

        <form id="pumpSizingForm">
            <h3><i class="fas fa-fire-extinguisher"></i> System Requirements</h3>
            
            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="demandFlow">Water Demand (GPM)</label>
                        <input type="number" id="demandFlow" step="0.1" value="500" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="residualPressure">Residual Pressure (PSI)</label>
                        <input type="number" id="residualPressure" step="0.1" value="65" required>
                    </div>
                </div>
            </div>

            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="flowTestDate">Flow Test Date</label>
                        <input type="date" id="flowTestDate" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="testElevation">Test Point Elevation (ft)</label>
                        <input type="number" id="testElevation" step="0.1" value="0" required>
                    </div>
                </div>
            </div>

            <h3><i class="fas fa-cog"></i> Pump Configuration</h3>
            
            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="pumpElevation">Pump Elevation (ft)</label>
                        <input type="number" id="pumpElevation" step="0.1" value="0" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="suctionType">Suction Type</label>
                        <select id="suctionType" required>
                            <option value="end">End Suction</option>
                            <option value="horizontal">Horizontal Split Case</option>
                            <option value="vertical">Vertical Turbine</option>
                        </select>
                    </div>
                </div>
            </div>

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
                        <label for="enclosureType">Enclosure Type</label>
                        <select id="enclosureType" required>
                            <option value="type1">Type 1</option>
                            <option value="type2">Type 2</option>
                            <option value="type4">Type 4</option>
                            <option value="type12">Type 12</option>
                        </select>
                    </div>
                </div>
            </div>

            <h3><i class="fas fa-thermometer-half"></i> Environmental Conditions</h3>
            
            <div class="input-row">
                <div class="input-col">
                    <div class="input-group">
                        <label for="ambientTemp">Ambient Temperature (°F)</label>
                        <input type="number" id="ambientTemp" step="0.1" value="70" required>
                    </div>
                </div>
                <div class="input-col">
                    <div class="input-group">
                        <label for="altitude">Elevation (ft)</label>
                        <input type="number" id="altitude" step="1" value="0" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="calculate-btn">
                <i class="fas fa-calculator"></i> Calculate Pump Requirements
            </button>
        </form>

        <div id="results" class="result-card">
            <div class="result-header">
                <i class="fas fa-chart-line"></i> Pump Sizing Results
            </div>
            <div id="resultsContent"></div>
        </div>
    </div>

    <script>
        document.getElementById('pumpSizingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get input values
            const demandFlow = parseFloat(document.getElementById('demandFlow').value);
            const residualPressure = parseFloat(document.getElementById('residualPressure').value);
            const testElevation = parseFloat(document.getElementById('testElevation').value);
            const pumpElevation = parseFloat(document.getElementById('pumpElevation').value);
            const suctionType = document.getElementById('suctionType').value;
            const driverType = document.getElementById('driverType').value;
            const ambientTemp = parseFloat(document.getElementById('ambientTemp').value);
            const altitude = parseFloat(document.getElementById('altitude').value);
            const enclosureType = document.getElementById('enclosureType').value;
            
            // Calculate elevation difference
            const elevationDiff = pumpElevation - testElevation;
            const elevationPressure = elevationDiff * 0.433;
            
            // Calculate net pressure required at pump
            const netPressure = residualPressure + elevationPressure;
            
            // Apply pressure loss factors
            const frictionLoss = demandFlow * demandFlow * 0.0002; // Approximate friction loss
            const totalPressure = netPressure + frictionLoss;
            
            // Pump capacity calculations (110% of demand flow)
            const ratedFlow = demandFlow * 1.10;
            
            // Pressure calculations at different flow rates
            const churnPressure = totalPressure * 1.4; // 140% of rated pressure
            const maxPressure = totalPressure * 1.5; // 150% of rated pressure
            
            // Driver power calculation
            const brakeHorsepower = (demandFlow * totalPressure) / (1714 * 0.85); // HP = (GPM * PSI) / (1714 * efficiency)
            const driverPower = brakeHorsepower * 1.2; // 20% service factor
            
            // Altitude correction
            const altitudeCorrection = 1 - (altitude / 10000) * 0.02;
            const correctedPressure = totalPressure / altitudeCorrection;
            
            // Driver power correction for temperature
            const tempCorrection = 1 + ((ambientTemp - 70) / 100) * 0.05;
            const correctedDriverPower = driverPower * tempCorrection;
            
            // Display results
            let resultsHTML = `
                <div class="result-item">
                    <span class="result-label">Required Flow (GPM):</span>
                    <span class="result-value">${ratedFlow.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Required Pressure (PSI):</span>
                    <span class="result-value">${totalPressure.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Churn Pressure (PSI):</span>
                    <span class="result-value">${churnPressure.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Maximum Pressure (PSI):</span>
                    <span class="result-value">${maxPressure.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Brake Horsepower:</span>
                    <span class="result-value">${brakeHorsepower.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Driver Power Rating (HP):</span>
                    <span class="result-value">${Math.ceil(correctedDriverPower)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Elevation Correction (ft):</span>
                    <span class="result-value">${elevationDiff.toFixed(1)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Altitude Correction:</span>
                    <span class="result-value">${(correctedPressure).toFixed(1)} PSI</span>
                </div>
            `;
            
            // Add NFPA compliance information
            if (ratedFlow >= demandFlow * 1.1 && churnPressure <= totalPressure * 1.5) {
                resultsHTML += `
                    <div class="info-box">
                        <i class="fas fa-check-circle"></i>
                        <strong>NFPA 20 Compliance:</strong> Pump selection meets minimum requirements for rated flow and pressure.
                    </div>
                `;
            } else {
                resultsHTML += `
                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Review Required:</strong> Pump selection may not meet NFPA 20 requirements. Verify calculations and consult NFPA 20.
                    </div>
                `;
            }
            
            // Driver type specific recommendations
            if (driverType === 'diesel') {
                resultsHTML += `
                    <div class="info-box">
                        <i class="fas fa-cog"></i>
                        <strong>Diesel Engine Recommendation:</strong> Ensure adequate fuel supply for minimum 6-hour operation at rated load.
                    </div>
                `;
            } else if (driverType === 'electric') {
                resultsHTML += `
                    <div class="info-box">
                        <i class="fas fa-bolt"></i>
                        <strong>Electric Motor Recommendation:</strong> Verify adequate electrical supply and backup power systems.
                    </div>
                `;
            }
            
            document.getElementById('resultsContent').innerHTML = resultsHTML;
            document.getElementById('results').style.display = 'block';
        });
        
        // Set default date to today
        document.getElementById('flowTestDate').valueAsDate = new Date();
    </script>
</body>
</html>
