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
    <title>Enthalpy Calculator - HVAC Calculator</title>
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
            background: linear-gradient(45deg, #fd7e14, #e83e8c);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: none;
            box-shadow: 0 4px 15px rgba(253, 126, 20, 0.3);
        }
        
        .result-card h5 {
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #fd7e14, #dc3545);
            border: none;
            box-shadow: 0 4px 15px rgba(253, 126, 20, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #e8630e, #c82333);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(253, 126, 20, 0.4);
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
        
        .enthalpy-breakdown {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .enthalpy-component {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .enthalpy-component:last-child {
            border-bottom: none;
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
            border-left: 3px solid #fd7e14;
        }
        
        .history-item:last-child {
            margin-bottom: 0;
        }
        
        .energy-flow {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            text-align: center;
        }
        
        .flow-arrow {
            font-size: 2rem;
            color: #fd7e14;
            margin: 0 1rem;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="calculator-container">
        <!-- Header -->
        <div class="page-header">
            <h1><i class="fas fa-temperature-high me-2"></i>Enthalpy Calculator</h1>
            <p class="text-white-50">Calculate total heat content of moist air using psychrometric properties</p>
        </div>
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../index.php">HVAC Module</a></li>
                <li class="breadcrumb-item"><a href="index.php">Psychrometrics</a></li>
                <li class="breadcrumb-item active">Enthalpy</li>
            </ol>
        </nav>
        
        <div class="glass-card">
            <!-- Calculator Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="calculator-form">
                        <h5><i class="fas fa-calculator me-2"></i>Enthalpy Calculation</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-thermometer-half me-1"></i>Dry Bulb
                                    </span>
                                    <input type="number" class="form-control" id="enthalpyDB" 
                                           placeholder="°C" step="0.1" value="25" min="-50" max="100">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-thermometer-full me-1"></i>Wet Bulb
                                    </span>
                                    <input type="number" class="form-control" id="wetBulb" 
                                           placeholder="°C" step="0.1" value="20" min="-50" max="100">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-compress-arrows-alt me-1"></i>Pressure
                                    </span>
                                    <input type="number" class="form-control" id="enthalpyPressure" 
                                           placeholder="kPa" step="0.1" value="101.325" min="80" max="120">
                                    <span class="input-group-text">kPa</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-vial me-1"></i>Calculation Mode
                                    </span>
                                    <select class="form-control" id="enthalpyMode">
                                        <option value="db-wb">DB + WB</option>
                                        <option value="db-rh">DB + RH</option>
                                        <option value="db-dp">DB + DP</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional input for other modes -->
                        <div id="enthalpyAdditionalInput" class="row" style="display: none;">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="enthalpyAdditionalLabel">
                                        <i class="fas fa-tint me-1"></i>Relative Humidity
                                    </span>
                                    <input type="number" class="form-control" id="enthalpyAdditionalValue" 
                                           placeholder="%" step="1" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button class="btn btn-primary" onclick="calculateEnthalpy()">
                                <i class="fas fa-calculator me-2"></i>Calculate Enthalpy
                            </button>
                        </div>
                    </div>
                    
                    <!-- Results Section -->
                    <div class="result-card" id="enthalpyResult">
                        <h5><i class="fas fa-chart-bar me-2"></i>Enthalpy Analysis</h5>
                        <div id="enthalpyOutput"></div>
                        <div class="enthalpy-breakdown">
                            <h6 class="text-white mb-3">Enthalpy Components</h6>
                            <div class="enthalpy-component">
                                <span>Sensible Heat</span>
                                <span id="sensibleHeat">0.00 kJ/kg</span>
                            </div>
                            <div class="enthalpy-component">
                                <span>Latent Heat</span>
                                <span id="latentHeat">0.00 kJ/kg</span>
                            </div>
                            <div class="enthalpy-component">
                                <span><strong>Total Enthalpy</strong></span>
                                <span id="totalEnthalpy">0.00 kJ/kg</span>
                            </div>
                        </div>
                        <div class="energy-flow">
                            <h6 class="text-white mb-3">Energy Analysis</h6>
                            <p>Humidity Ratio: <span id="flowHumidity">0.00</span> g/kg</p>
                            <p>Sensible Heat Ratio: <span id="flowSHR">0.00</span></p>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="calculator-form">
                        <h5><i class="fas fa-info-circle me-2"></i>Quick Reference</h5>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Enthalpy Definition</h6>
                            <small class="text-white-50">
                                Total heat content of moist air including sensible and latent components. Used for energy balance calculations in HVAC systems.
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Standard Values</h6>
                            <small class="text-white-50">
                                <strong>Outside Air:</strong> 35°C DB, 24°C WB<br>
                                <strong>Inside Air:</strong> 24°C DB, 17°C WB<br>
                                <strong>Cooled Air:</strong> 13°C DB, 12°C WB<br>
                                <strong>Heated Air:</strong> 40°C DB, 15°C WB
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Enthalpy Ranges</h6>
                            <small class="text-white-50">
                                <strong>Comfort Zone:</strong> 40-60 kJ/kg<br>
                                <strong>Hot/Humid:</strong> 70-85 kJ/kg<br>
                                <strong>Cool/Dry:</strong> 20-35 kJ/kg<br>
                                <strong>Very Cold:</strong> 5-20 kJ/kg
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Key Formulas</h6>
                            <small class="text-white-50">
                                <strong>Total Enthalpy:</strong> h = 1.006T + W(2501 + 1.86T)<br>
                                <strong>Sensible:</strong> hs = 1.006T<br>
                                <strong>Latent:</strong> hl = W(2501 + 1.86T)
                            </small>
                        </div>
                    </div>
                    
                    <!-- Calculation History -->
                    <div class="calculation-history">
                        <h6 class="text-white mb-3">
                            <i class="fas fa-history me-2"></i>Recent Calculations
                        </h6>
                        <div id="enthalpyCalculationHistory">
                            <small class="text-white-50">No calculations yet</small>
                        </div>
                        <button class="btn btn-outline-light btn-sm mt-2" onclick="clearEnthalpyHistory()">
                            <i class="fas fa-trash me-1"></i>Clear History
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateEnthalpy() {
            const dryBulb = parseFloat(document.getElementById('enthalpyDB').value);
            const wetBulb = parseFloat(document.getElementById('wetBulb').value);
            const pressure = parseFloat(document.getElementById('enthalpyPressure').value);
            const mode = document.getElementById('enthalpyMode').value;
            
            if (!dryBulb || !pressure) {
                showNotification('Please enter dry bulb temperature and pressure', 'info');
                return;
            }
            
            let result;
            
            if (mode === 'db-wb') {
                if (!wetBulb) {
                    showNotification('Please enter wet bulb temperature', 'info');
                    return;
                }
                result = calculateEnthalpyFromDB_WB(dryBulb, wetBulb, pressure);
            } else {
                const additional = parseFloat(document.getElementById('enthalpyAdditionalValue').value);
                if (!additional) {
                    showNotification('Please enter the additional value', 'info');
                    return;
                }
                if (mode === 'db-rh') {
                    result = calculateEnthalpyFromDB_RH(dryBulb, additional, pressure);
                } else {
                    result = calculateEnthalpyFromDB_DP(dryBulb, additional, pressure);
                }
            }
            
            // Calculate enthalpy components
            const sensibleHeat = 1.006 * dryBulb;
            const latentHeat = result.humidityRatio * (2501 + 1.86 * dryBulb);
            const totalEnthalpy = sensibleHeat + latentHeat;
            const sensibleHeatRatio = sensibleHeat / totalEnthalpy;
            
            // Display results
            const resultHTML = `
                <p><strong>Input Mode:</strong> ${mode.replace('-', ' + ').toUpperCase()}</p>
                <p><strong>Dry Bulb Temperature:</strong> ${dryBulb}°C</p>
                <p><strong>Atmospheric Pressure:</strong> ${pressure} kPa</p>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <p><strong>Relative Humidity:</strong> ${result.relativeHumidity.toFixed(1)}%</p>
                <p><strong>Humidity Ratio:</strong> ${(result.humidityRatio * 1000).toFixed(2)} g/kg</p>
                <p><strong>Dew Point:</strong> ${result.dewPoint.toFixed(1)}°C</p>
                <p><strong>Wet Bulb:</strong> ${result.wetBulb.toFixed(1)}°C</p>
            `;
            
            document.getElementById('enthalpyOutput').innerHTML = resultHTML;
            document.getElementById('sensibleHeat').textContent = sensibleHeat.toFixed(2) + ' kJ/kg';
            document.getElementById('latentHeat').textContent = latentHeat.toFixed(2) + ' kJ/kg';
            document.getElementById('totalEnthalpy').textContent = totalEnthalpy.toFixed(2) + ' kJ/kg';
            document.getElementById('flowHumidity').textContent = (result.humidityRatio * 1000).toFixed(2);
            document.getElementById('flowSHR').textContent = sensibleHeatRatio.toFixed(3);
            document.getElementById('enthalpyResult').style.display = 'block';
            
            // Save to history
            saveEnthalpyCalculation('Enthalpy', {
                dryBulb: dryBulb,
                wetBulb: wetBulb,
                pressure: pressure,
                mode: mode,
                totalEnthalpy: totalEnthalpy,
                sensibleHeat: sensibleHeat,
                latentHeat: latentHeat
            });
        }
        
        function calculateEnthalpyFromDB_WB(dryBulb, wetBulb, pressure) {
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
            
            // Dew point
            const dewPoint = calculateDewPoint(ea);
            
            return {
                relativeHumidity: rh,
                humidityRatio: W,
                dewPoint: dewPoint,
                wetBulb: wetBulb
            };
        }
        
        function calculateEnthalpyFromDB_RH(dryBulb, rh, pressure) {
            // Saturation vapor pressure (Buck equation in kPa)
            const es = 0.61121 * Math.exp((18.678 - dryBulb/234.5) * (dryBulb / (257.14 + dryBulb)));
            
            // Actual vapor pressure
            const ea = es * (rh / 100);
            
            // Humidity ratio (kg water/kg dry air)
            const W = 0.62198 * (ea / (pressure - ea));
            
            // Dew point
            const dewPoint = calculateDewPoint(ea);
            
            // Wet bulb calculation
            const wetBulb = calculateWetBulb(dryBulb, rh, pressure);
            
            return {
                relativeHumidity: rh,
                humidityRatio: W,
                dewPoint: dewPoint,
                wetBulb: wetBulb
            };
        }
        
        function calculateEnthalpyFromDB_DP(dryBulb, dewPoint, pressure) {
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
                relativeHumidity: rh,
                humidityRatio: W,
                dewPoint: dewPoint,
                wetBulb: wetBulb
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
                
                wetBulb += h_difference * 0.1;
            }
            
            return wetBulb;
        }
        
        function saveEnthalpyCalculation(type, data) {
            let history = JSON.parse(localStorage.getItem('hvacEnthalpyHistory') || '[]');
            history.unshift({
                type: type,
                data: data,
                timestamp: new Date().toLocaleString()
            });
            
            // Keep only last 10 calculations
            history = history.slice(0, 10);
            localStorage.setItem('hvacEnthalpyHistory', JSON.stringify(history));
            loadEnthalpyCalculationHistory();
        }
        
        function loadEnthalpyCalculationHistory() {
            const history = JSON.parse(localStorage.getItem('hvacEnthalpyHistory') || '[]');
            const container = document.getElementById('enthalpyCalculationHistory');
            
            if (history.length === 0) {
                container.innerHTML = '<small class="text-white-50">No calculations yet</small>';
                return;
            }
            
            container.innerHTML = history.map(calc => `
                <div class="history-item">
                    <div class="small">
                        <strong>${calc.data.dryBulb}°C DB</strong> • 
                        ${calc.data.wetBulb}°C WB
                    </div>
                    <div class="small text-white-50">
                        h = ${calc.data.totalEnthalpy.toFixed(2)} kJ/kg • 
                        SHR = ${(calc.data.sensibleHeat / calc.data.totalEnthalpy).toFixed(3)}
                    </div>
                    <div class="small text-white-50">
                        ${calc.timestamp}
                    </div>
                </div>
            `).join('');
        }
        
        function clearEnthalpyHistory() {
            showConfirmModal('Clear History', 'Are you sure you want to clear all calculation history?', function() {
                localStorage.removeItem('hvacEnthalpyHistory');
                loadEnthalpyCalculationHistory();
                showNotification('History cleared', 'success');
            });
        }
        
        function updateEnthalpyAdditionalInput() {
            const mode = document.getElementById('enthalpyMode').value;
            const additionalInput = document.getElementById('enthalpyAdditionalInput');
            const additionalLabel = document.getElementById('enthalpyAdditionalLabel');
            const additionalValue = document.getElementById('enthalpyAdditionalValue');
            
            if (mode === 'db-wb') {
                additionalInput.style.display = 'none';
            } else {
                additionalInput.style.display = 'flex';
                if (mode === 'db-rh') {
                    additionalLabel.innerHTML = '<i class="fas fa-tint me-1"></i>Relative Humidity';
                    additionalValue.placeholder = '%';
                    additionalValue.value = '50';
                } else {
                    additionalLabel.innerHTML = '<i class="fas fa-thermometer-empty me-1"></i>Dew Point';
                    additionalValue.placeholder = '°C';
                    additionalValue.value = '15';
                }
            }
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadEnthalpyCalculationHistory();
            updateEnthalpyAdditionalInput();
            
            document.getElementById('enthalpyMode').addEventListener('change', updateEnthalpyAdditionalInput);
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
