<?php
// Start session if needed
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Properties Calculator - HVAC Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/theme.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .calculator-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .calculator-form {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .calculator-form h5 {
            color: #fff;
            margin-bottom: 1rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }
        
        .result-card {
            background: linear-gradient(45deg, #6f42c1, #e83e8c);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: none;
            box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3);
        }
        
        .result-card h5 {
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #6f42c1, #9c27b0);
            border: none;
            box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #5a2d91, #7b1fa2);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(111, 66, 193, 0.4);
        }
        
        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            font-weight: 500;
        }
        
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.5);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 0.5rem;
        }
        
        .breadcrumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }
        
        .breadcrumb a {
            color: #fff;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .psychrometric-chart {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            text-align: center;
        }
        
        .calculation-history {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
        }
        
        .history-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            border-left: 3px solid #6f42c1;
        }
        
        .history-item:last-child {
            margin-bottom: 0;
        }
        
        .property-display {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .property-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.75rem;
            border-radius: 8px;
            text-align: center;
        }
        
        .property-value {
            font-size: 1.2rem;
            font-weight: bold;
            color: #fff;
        }
        
        .property-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="calculator-container">
        <!-- Header -->
        <div class="page-header">
            <h1><i class="fas fa-cloud me-2"></i>Air Properties Calculator</h1>
            <p class="text-white-50">Calculate psychrometric properties of air using temperature and humidity data</p>
        </div>
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../index.php">HVAC Module</a></li>
                <li class="breadcrumb-item"><a href="index.php">Psychrometrics</a></li>
                <li class="breadcrumb-item active">Air Properties</li>
            </ol>
        </nav>
        
        <div class="glass-card">
            <!-- Calculator Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="calculator-form">
                        <h5><i class="fas fa-calculator me-2"></i>Air Properties Calculation</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-thermometer-half me-1"></i>Dry Bulb Temp
                                    </span>
                                    <input type="number" class="form-control" id="dryBulb" 
                                           placeholder="°C" step="0.1" value="25" min="-50" max="100">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-tint me-1"></i>Relative Humidity
                                    </span>
                                    <input type="number" class="form-control" id="relativeHumidity" 
                                           placeholder="%" step="1" value="50" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-compress-arrows-alt me-1"></i>Pressure
                                    </span>
                                    <input type="number" class="form-control" id="airPressure" 
                                           placeholder="kPa" step="0.1" value="101.325" min="80" max="120">
                                    <span class="input-group-text">kPa</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-vial me-1"></i>Calculation Mode
                                    </span>
                                    <select class="form-control" id="calculationMode">
                                        <option value="db-rh">DB + RH</option>
                                        <option value="db-wb">DB + WB</option>
                                        <option value="db-dp">DB + DP</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional input for other modes -->
                        <div id="additionalInput" class="row" style="display: none;">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="additionalLabel">
                                        <i class="fas fa-thermometer-full me-1"></i>Wet Bulb
                                    </span>
                                    <input type="number" class="form-control" id="additionalValue" 
                                           placeholder="°C" step="0.1" min="-50" max="100">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button class="btn btn-primary" onclick="calculateAirProperties()">
                                <i class="fas fa-calculator me-2"></i>Calculate Air Properties
                            </button>
                        </div>
                    </div>
                    
                    <!-- Results Section -->
                    <div class="result-card" id="airPropertiesResult">
                        <h5><i class="fas fa-chart-bar me-2"></i>Air Properties Results</h5>
                        <div id="airPropertiesOutput"></div>
                        <div class="property-display" id="propertyDisplay"></div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="calculator-form">
                        <h5><i class="fas fa-info-circle me-2"></i>Quick Reference</h5>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Standard Conditions</h6>
                            <small class="text-white-50">
                                <strong>Temperature:</strong> 20-25°C (68-77°F)<br>
                                <strong>Relative Humidity:</strong> 40-60%<br>
                                <strong>Pressure:</strong> 101.325 kPa (sea level)<br>
                                <strong>Density:</strong> 1.2 kg/m³
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Comfort Range</h6>
                            <small class="text-white-50">
                                <strong>DB Temperature:</strong> 22-26°C<br>
                                <strong>RH Range:</strong> 30-65%<br>
                                <strong>Dew Point:</strong> 9-15°C<br>
                                <strong>Humidity Ratio:</strong> 8-15 g/kg
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Calculation Methods</h6>
                            <small class="text-white-50">
                                <strong>Buck Equation:</strong> Vapor pressure<br>
                                <strong>ASHRAE:</strong> Psychrometric properties<br>
                                <strong>Moist Air:</strong> Ideal gas behavior
                            </small>
                        </div>
                    </div>
                    
                    <!-- Calculation History -->
                    <div class="calculation-history">
                        <h6 class="text-white mb-3">
                            <i class="fas fa-history me-2"></i>Recent Calculations
                        </h6>
                        <div id="calculationHistory">
                            <small class="text-white-50">No calculations yet</small>
                        </div>
                        <button class="btn btn-outline-light btn-sm mt-2" onclick="clearHistory()">
                            <i class="fas fa-trash me-1"></i>Clear History
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateAirProperties() {
            const dryBulb = parseFloat(document.getElementById('dryBulb').value);
            const rh = parseFloat(document.getElementById('relativeHumidity').value);
            const pressure = parseFloat(document.getElementById('airPressure').value);
            const mode = document.getElementById('calculationMode').value;
            
            if (!dryBulb || !pressure) {
                showNotification('Please enter dry bulb temperature and pressure', 'info');
                return;
            }
            
            let result;
            
            if (mode === 'db-rh') {
                if (!rh) {
                    showNotification('Please enter relative humidity', 'info');
                    return;
                }
                result = calculateFromDB_RH(dryBulb, rh, pressure);
            } else {
                const additional = parseFloat(document.getElementById('additionalValue').value);
                if (!additional) {
                    showNotification('Please enter the additional value', 'info');
                    return;
                }
                if (mode === 'db-wb') {
                    result = calculateFromDB_WB(dryBulb, additional, pressure);
                } else {
                    result = calculateFromDB_DP(dryBulb, additional, pressure);
                }
            }
            
            // Display results
            const resultHTML = `
                <p><strong>Input Mode:</strong> ${mode.replace('-', ' + ').toUpperCase()}</p>
                <p><strong>Dry Bulb Temperature:</strong> ${dryBulb}°C</p>
                <p><strong>Atmospheric Pressure:</strong> ${pressure} kPa</p>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <p><strong>Specific Volume:</strong> ${result.specificVolume.toFixed(3)} m³/kg</p>
                <p><strong>Density:</strong> ${result.density.toFixed(3)} kg/m³</p>
                <p><strong>Enthalpy:</strong> ${result.enthalpy.toFixed(2)} kJ/kg</p>
                <p><strong>Internal Energy:</strong> ${result.internalEnergy.toFixed(2)} kJ/kg</p>
            `;
            
            const propertyDisplay = `
                <div class="property-item">
                    <div class="property-value">${result.temperature.toFixed(1)}°C</div>
                    <div class="property-label">Dry Bulb</div>
                </div>
                <div class="property-item">
                    <div class="property-value">${result.relativeHumidity.toFixed(1)}%</div>
                    <div class="property-label">Rel. Humidity</div>
                </div>
                <div class="property-item">
                    <div class="property-value">${result.dewPoint.toFixed(1)}°C</div>
                    <div class="property-label">Dew Point</div>
                </div>
                <div class="property-item">
                    <div class="property-value">${result.wetBulb.toFixed(1)}°C</div>
                    <div class="property-label">Wet Bulb</div>
                </div>
                <div class="property-item">
                    <div class="property-value">${(result.humidityRatio * 1000).toFixed(2)} g/kg</div>
                    <div class="property-label">Humidity Ratio</div>
                </div>
                <div class="property-item">
                    <div class="property-value">${result.vaporPressure.toFixed(3)} kPa</div>
                    <div class="property-label">Vapor Pressure</div>
                </div>
            `;
            
            document.getElementById('airPropertiesOutput').innerHTML = resultHTML;
            document.getElementById('propertyDisplay').innerHTML = propertyDisplay;
            document.getElementById('airPropertiesResult').style.display = 'block';
            
            // Save to history
            saveCalculation('Air Properties', {
                dryBulb: dryBulb,
                rh: rh,
                pressure: pressure,
                mode: mode,
                dewPoint: result.dewPoint,
                humidityRatio: result.humidityRatio
            });
        }
        
        function calculateFromDB_RH(dryBulb, rh, pressure) {
            // Saturation vapor pressure (Buck equation in kPa)
            const es = 0.61121 * Math.exp((18.678 - dryBulb/234.5) * (dryBulb / (257.14 + dryBulb)));
            
            // Actual vapor pressure
            const ea = es * (rh / 100);
            
            // Humidity ratio (kg water/kg dry air)
            const W = 0.62198 * (ea / (pressure - ea));
            
            // Enthalpy (kJ/kg dry air)
            const h = 1.006 * dryBulb + W * (2501 + 1.86 * dryBulb);
            
            // Specific volume (m³/kg dry air)
            const v = 0.287 * (dryBulb + 273.15) * (1 + 1.6078 * W) / pressure;
            
            // Density (kg/m³)
            const density = 1 / v;
            
            // Dew point temperature
            const dewPoint = calculateDewPoint(ea);
            
            // Wet bulb temperature (approximation)
            const wetBulb = calculateWetBulb(dryBulb, rh, pressure);
            
            // Internal energy approximation
            const internalEnergy = 0.718 * dryBulb + W * 2501;
            
            return {
                temperature: dryBulb,
                relativeHumidity: rh,
                humidityRatio: W,
                vaporPressure: ea,
                enthalpy: h,
                specificVolume: v,
                density: density,
                dewPoint: dewPoint,
                wetBulb: wetBulb,
                internalEnergy: internalEnergy
            };
        }
        
        function calculateFromDB_WB(dryBulb, wetBulb, pressure) {
            // Simplified psychrometric calculation from DB and WB
            // This is a basic approximation - in practice, iterative methods are used
            
            // Saturation vapor pressure at wet bulb
            const es_wb = 0.61121 * Math.exp((18.678 - wetBulb/234.5) * (wetBulb / (257.14 + wetBulb)));
            
            // Approximate humidity ratio from psychrometric equation
            const Ws = 0.62198 * (es_wb / (pressure - es_wb));
            const W = ((2501 - 2.381 * wetBulb) * Ws - 1.006 * (dryBulb - wetBulb)) / (2501 + 1.805 * dryBulb - 4.186 * wetBulb);
            
            // Saturation vapor pressure at dry bulb
            const es_db = 0.61121 * Math.exp((18.678 - dryBulb/234.5) * (dryBulb / (257.14 + dryBulb)));
            
            // Relative humidity
            const ea = W * pressure / (0.62198 + W);
            const rh = (ea / es_db) * 100;
            
            // Other properties
            const h = 1.006 * dryBulb + W * (2501 + 1.86 * dryBulb);
            const v = 0.287 * (dryBulb + 273.15) * (1 + 1.6078 * W) / pressure;
            const density = 1 / v;
            const dewPoint = calculateDewPoint(ea);
            const internalEnergy = 0.718 * dryBulb + W * 2501;
            
            return {
                temperature: dryBulb,
                relativeHumidity: rh,
                humidityRatio: W,
                vaporPressure: ea,
                enthalpy: h,
                specificVolume: v,
                density: density,
                dewPoint: dewPoint,
                wetBulb: wetBulb,
                internalEnergy: internalEnergy
            };
        }
        
        function calculateFromDB_DP(dryBulb, dewPoint, pressure) {
            // Vapor pressure from dew point
            const ea = 0.61121 * Math.exp((18.678 - dewPoint/234.5) * (dewPoint / (257.14 + dewPoint)));
            
            // Humidity ratio
            const W = 0.62198 * (ea / (pressure - ea));
            
            // Saturation vapor pressure at dry bulb
            const es = 0.61121 * Math.exp((18.678 - dryBulb/234.5) * (dryBulb / (257.14 + dryBulb)));
            
            // Relative humidity
            const rh = (ea / es) * 100;
            
            // Wet bulb calculation
            const wetBulb = calculateWetBulb(dryBulb, rh, pressure);
            
            return {
                temperature: dryBulb,
                relativeHumidity: rh,
                humidityRatio: W,
                vaporPressure: ea,
                enthalpy: h,
                specificVolume: v,
                density: density,
                dewPoint: dewPoint,
                wetBulb: wetBulb,
                internalEnergy: internalEnergy
            };
        }
        
        function calculateDewPoint(ea) {
            // Magnus formula for dew point
            const a = 17.27;
            const b = 237.7;
            return (b * Math.log(ea/0.61078)) / (a - Math.log(ea/0.61078));
        }
        
        function calculateWetBulb(dryBulb, rh, pressure) {
            // Approximate wet bulb calculation
            // This is simplified - accurate calculation requires psychrometric relationships
            
            const saturationPressure = 0.61121 * Math.exp((18.678 - dryBulb/234.5) * (dryBulb / (257.14 + dryBulb)));
            const actualVaporPressure = saturationPressure * rh / 100;
            
            // Simple approximation
            let wetBulb = dryBulb - (dryBulb - (actualVaporPressure * 1000 / 2.5));
            
            // Iterative refinement
            for (let i = 0; i < 3; i++) {
                const es_wb = 0.61121 * Math.exp((18.678 - wetBulb/234.5) * (wetBulb / (257.14 + wetBulb)));
                const ws = 0.62198 * (es_wb / (pressure - es_wb));
                const actualWs = 0.62198 * (actualVaporPressure / (pressure - actualVaporPressure));
                
                const h_difference = 1.006 * (dryBulb - wetBulb) + (ws - actualWs) * 2501;
                if (Math.abs(h_difference) < 0.1) break;
                
                wetBulb += h_difference * 0.1; // Adjustment
            }
            
            return wetBulb;
        }
        
        function saveCalculation(type, data) {
            let history = JSON.parse(localStorage.getItem('hvacAirPropertiesHistory') || '[]');
            history.unshift({
                type: type,
                data: data,
                timestamp: new Date().toLocaleString()
            });
            
            // Keep only last 10 calculations
            history = history.slice(0, 10);
            localStorage.setItem('hvacAirPropertiesHistory', JSON.stringify(history));
            loadCalculationHistory();
        }
        
        function loadCalculationHistory() {
            const history = JSON.parse(localStorage.getItem('hvacAirPropertiesHistory') || '[]');
            const container = document.getElementById('calculationHistory');
            
            if (history.length === 0) {
                container.innerHTML = '<small class="text-white-50">No calculations yet</small>';
                return;
            }
            
            container.innerHTML = history.map(calc => `
                <div class="history-item">
                    <div class="small">
                        <strong>${calc.data.dryBulb}°C</strong> • 
                        ${calc.data.rh ? calc.data.rh + '%' : 'N/A'} RH
                    </div>
                    <div class="small text-white-50">
                        DP: ${calc.data.dewPoint ? calc.data.dewPoint.toFixed(1) : 'N/A'}°C • 
                        HR: ${calc.data.humidityRatio ? (calc.data.humidityRatio * 1000).toFixed(1) : 'N/A'} g/kg
                    </div>
                    <div class="small text-white-50">
                        ${calc.timestamp}
                    </div>
                </div>
            `).join('');
        }
        
        function clearHistory() {
            showConfirmModal('Clear History', 'Are you sure you want to clear all calculation history?', function() {
                localStorage.removeItem('hvacAirPropertiesHistory');
                loadCalculationHistory();
                showNotification('History cleared', 'success');
            });
        }
        
        function updateAdditionalInput() {
            const mode = document.getElementById('calculationMode').value;
            const additionalInput = document.getElementById('additionalInput');
            const additionalLabel = document.getElementById('additionalLabel');
            const additionalValue = document.getElementById('additionalValue');
            
            if (mode === 'db-rh') {
                additionalInput.style.display = 'none';
            } else {
                additionalInput.style.display = 'flex';
                if (mode === 'db-wb') {
                    additionalLabel.innerHTML = '<i class="fas fa-thermometer-full me-1"></i>Wet Bulb';
                    additionalValue.placeholder = '°C';
                } else {
                    additionalLabel.innerHTML = '<i class="fas fa-thermometer-empty me-1"></i>Dew Point';
                    additionalValue.placeholder = '°C';
                }
            }
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadCalculationHistory();
            updateAdditionalInput();
            
            document.getElementById('calculationMode').addEventListener('change', updateAdditionalInput);
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
