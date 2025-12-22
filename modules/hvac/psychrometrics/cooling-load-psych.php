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
    <title>Cooling Load (Psychrometric) - HVAC Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/theme.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
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
            background: linear-gradient(45deg, #0d6efd, #6f42c1);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: none;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }
        
        .result-card h5 {
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #6f42c1);
            border: none;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #0b5ed7, #5a2d91);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
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
        
        .load-breakdown {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .load-component {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .load-component:last-child {
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
            border-left: 3px solid #0d6efd;
        }
        
        .history-item:last-child {
            margin-bottom: 0;
        }
        
        .energy-analysis {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            text-align: center;
        }
        
        .process-flow {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .flow-step {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
        }
        
        .flow-step:last-child {
            margin-bottom: 0;
        }
        
        .flow-icon {
            font-size: 1.2rem;
            margin-right: 0.75rem;
            color: #0d6efd;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="calculator-container">
        <!-- Header -->
        <div class="page-header">
            <h1><i class="fas fa-snowflake me-2"></i>Cooling Load (Psychrometric)</h1>
            <p class="text-white-50">Calculate cooling load using psychrometric enthalpy method for air handling systems</p>
        </div>
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../index.php">HVAC Module</a></li>
                <li class="breadcrumb-item"><a href="index.php">Psychrometrics</a></li>
                <li class="breadcrumb-item active">Cooling Load (Psychrometric)</li>
            </ol>
        </nav>
        
        <div class="glass-card">
            <!-- Calculator Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="calculator-form">
                        <h5><i class="fas fa-calculator me-2"></i>Psychrometric Cooling Load Calculation</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-wind me-1"></i>Air Flow Rate
                                    </span>
                                    <input type="number" class="form-control" id="coolingAirFlow" 
                                           placeholder="CFM" step="1" value="1000" min="0">
                                    <span class="input-group-text">CFM</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-thermometer-half me-1"></i>Entering DB
                                    </span>
                                    <input type="number" class="form-control" id="enteringDB" 
                                           placeholder="°C" step="0.1" value="32" min="-20" max="50">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-thermometer-full me-1"></i>Entering WB
                                    </span>
                                    <input type="number" class="form-control" id="enteringWB" 
                                           placeholder="°C" step="0.1" value="24" min="-20" max="50">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-thermometer-half me-1"></i>Leaving DB
                                    </span>
                                    <input type="number" class="form-control" id="leavingDB" 
                                           placeholder="°C" step="0.1" value="18" min="-20" max="50">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-thermometer-full me-1"></i>Leaving WB
                                    </span>
                                    <input type="number" class="form-control" id="leavingWB" 
                                           placeholder="°C" step="0.1" value="17" min="-20" max="50">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-compress-arrows-alt me-1"></i>Pressure
                                    </span>
                                    <input type="number" class="form-control" id="coolingPressure" 
                                           placeholder="kPa" step="0.1" value="101.325" min="80" max="120">
                                    <span class="input-group-text">kPa</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button class="btn btn-primary" onclick="calculateCoolingLoadPsych()">
                                <i class="fas fa-calculator me-2"></i>Calculate Cooling Load
                            </button>
                        </div>
                    </div>
                    
                    <!-- Results Section -->
                    <div class="result-card" id="coolingLoadPsychResult">
                        <h5><i class="fas fa-chart-bar me-2"></i>Psychrometric Cooling Load Analysis</h5>
                        <div id="coolingLoadPsychOutput"></div>
                        <div class="load-breakdown">
                            <h6 class="text-white mb-3">Load Components</h6>
                            <div class="load-component">
                                <span>Entering Enthalpy</span>
                                <span id="enteringEnthalpy">0.00 kJ/kg</span>
                            </div>
                            <div class="load-component">
                                <span>Leaving Enthalpy</span>
                                <span id="leavingEnthalpy">0.00 kJ/kg</span>
                            </div>
                            <div class="load-component">
                                <span>Enthalpy Difference</span>
                                <span id="enthalpyDifference">0.00 kJ/kg</span>
                            </div>
                            <div class="load-component">
                                <span><strong>Total Cooling Load</strong></span>
                                <span id="totalCoolingLoad">0.00 kW</span>
                            </div>
                        </div>
                        <div class="energy-analysis">
                            <h6 class="text-white mb-3">Energy Analysis</h6>
                            <p>Mass Flow Rate: <span id="massFlowRate">0.00</span> kg/s</p>
                            <p>Volumetric Flow: <span id="volumetricFlow">0.00</span> m³/s</p>
                            <p>Load in Tons: <span id="loadInTons">0.00</span> TR</p>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="calculator-form">
                        <h5><i class="fas fa-info-circle me-2"></i>Quick Reference</h5>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Psychrometric Method</h6>
                            <small class="text-white-50">
                                Uses enthalpy difference between entering and leaving air states to determine cooling load. More accurate than temperature-only methods.
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Typical Values</h6>
                            <small class="text-white-50">
                                <strong>Entering Air:</strong> 32°C DB, 24°C WB<br>
                                <strong>Leaving Air:</strong> 18°C DB, 17°C WB<br>
                                <strong>Supply Air:</strong> 13°C DB, 12°C WB<br>
                                <strong>Room Air:</strong> 24°C DB, 50% RH
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Load Calculations</h6>
                            <small class="text-white-50">
                                <strong>Formula:</strong> Q = ρ × V × (h₁ - h₂)<br>
                                <strong>Units:</strong> kW, BTU/hr, Tons<br>
                                <strong>Air Density:</strong> 1.2 kg/m³ (typical)<br>
                                <strong>Conversion:</strong> 1 TR = 3.517 kW
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-white">Process Types</h6>
                            <small class="text-white-50">
                                <strong>Sensible Cooling:</strong> Temperature ↓, RH ↑<br>
                                <strong>Dehumidification:</strong> Moisture ↓, RH ↓<br>
                                <strong>Cooling & Dehumidify:</strong> Both ↓
                            </small>
                        </div>
                    </div>
                    
                    <!-- Process Flow -->
                    <div class="process-flow">
                        <h6 class="text-white mb-3">
                            <i class="fas fa-route me-2"></i>Process Flow
                        </h6>
                        <div class="flow-step">
                            <i class="fas fa-arrow-down flow-icon"></i>
                            <span>Entering Air State (Point 1)</span>
                        </div>
                        <div class="flow-step">
                            <i class="fas fa-cog flow-icon"></i>
                            <span>Cooling Coil Process</span>
                        </div>
                        <div class="flow-step">
                            <i class="fas fa-arrow-up flow-icon"></i>
                            <span>Leaving Air State (Point 2)</span>
                        </div>
                        <div class="flow-step">
                            <i class="fas fa-calculator flow-icon"></i>
                            <span>Enthalpy Difference = Load</span>
                        </div>
                    </div>
                    
                    <!-- Calculation History -->
                    <div class="calculation-history">
                        <h6 class="text-white mb-3">
                            <i class="fas fa-history me-2"></i>Recent Calculations
                        </h6>
                        <div id="coolingLoadPsychHistory">
                            <small class="text-white-50">No calculations yet</small>
                        </div>
                        <button class="btn btn-outline-light btn-sm mt-2" onclick="clearCoolingLoadPsychHistory()">
                            <i class="fas fa-trash me-1"></i>Clear History
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateCoolingLoadPsych() {
            const airFlow = parseFloat(document.getElementById('coolingAirFlow').value);
            const enteringDB = parseFloat(document.getElementById('enteringDB').value);
            const enteringWB = parseFloat(document.getElementById('enteringWB').value);
            const leavingDB = parseFloat(document.getElementById('leavingDB').value);
            const leavingWB = parseFloat(document.getElementById('leavingWB').value);
            const pressure = parseFloat(document.getElementById('coolingPressure').value);
            
            if (!airFlow || !enteringDB || !enteringWB || !leavingDB || !leavingWB || !pressure) {
                showNotification('Please enter all required values', 'info');
                return;
            }
            
            // Calculate enthalpies for entering and leaving air
            const enteringEnthalpy = calculateEnthalpyFromDB_WB(enteringDB, enteringWB, pressure);
            const leavingEnthalpy = calculateEnthalpyFromDB_WB(leavingDB, leavingWB, pressure);
            
            // Calculate air properties
            const volumetricFlowCMS = airFlow * 0.000471947; // Convert CFM to m³/s
            const airDensity = 1.2; // kg/m³ (assumed standard conditions)
            const massFlowRate = volumetricFlowCMS * airDensity; // kg/s
            
            // Calculate cooling load: Q = ṁ × (h1 - h2)
            const coolingLoad = massFlowRate * (enteringEnthalpy - leavingEnthalpy); // kW
            const coolingLoadTons = coolingLoad / 3.517; // Convert to tons
            const coolingLoadBTUH = coolingLoad * 3412.14; // Convert to BTU/hr
            
            // Display results
            const resultHTML = `
                <p><strong>Air Flow Rate:</strong> ${airFlow.toLocaleString()} CFM</p>
                <p><strong>Entering Conditions:</strong> ${enteringDB}°C DB, ${enteringWB}°C WB</p>
                <p><strong>Leaving Conditions:</strong> ${leavingDB}°C DB, ${leavingWB}°C WB</p>
                <p><strong>Atmospheric Pressure:</strong> ${pressure} kPa</p>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <p><strong>Process Analysis:</strong></p>
                <p>• Sensible Temperature Change: ${(enteringDB - leavingDB).toFixed(1)}°C</p>
                <p>• Humidity Ratio Change: ${((enteringEnthalpy.humidityRatio - leavingEnthalpy.humidityRatio) * 1000).toFixed(2)} g/kg</p>
                <p>• Relative Humidity Change: ${(enteringEnthalpy.relativeHumidity - leavingEnthalpy.relativeHumidity).toFixed(1)}%</p>
            `;
            
            document.getElementById('coolingLoadPsychOutput').innerHTML = resultHTML;
            document.getElementById('enteringEnthalpy').textContent = enteringEnthalpy.totalEnthalpy.toFixed(2) + ' kJ/kg';
            document.getElementById('leavingEnthalpy').textContent = leavingEnthalpy.totalEnthalpy.toFixed(2) + ' kJ/kg';
            document.getElementById('enthalpyDifference').textContent = (enteringEnthalpy.totalEnthalpy - leavingEnthalpy.totalEnthalpy).toFixed(2) + ' kJ/kg';
            document.getElementById('totalCoolingLoad').textContent = coolingLoad.toFixed(2) + ' kW';
            document.getElementById('massFlowRate').textContent = massFlowRate.toFixed(3);
            document.getElementById('volumetricFlow').textContent = volumetricFlowCMS.toFixed(3);
            document.getElementById('loadInTons').textContent = coolingLoadTons.toFixed(2);
            document.getElementById('coolingLoadPsychResult').style.display = 'block';
            
            // Save to history
            saveCoolingLoadPsychCalculation('Cooling Load Psych', {
                airFlow: airFlow,
                enteringDB: enteringDB,
                enteringWB: enteringWB,
                leavingDB: leavingDB,
                leavingWB: leavingWB,
                pressure: pressure,
                coolingLoad: coolingLoad,
                coolingLoadTons: coolingLoadTons,
                enthalpyDifference: enteringEnthalpy.totalEnthalpy - leavingEnthalpy.totalEnthalpy
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
            
            // Calculate enthalpy components
            const sensibleHeat = 1.006 * dryBulb;
            const latentHeat = W * (2501 + 1.86 * dryBulb);
            const totalEnthalpy = sensibleHeat + latentHeat;
            
            // Calculate dew point
            const dewPoint = calculateDewPoint(ea);
            
            return {
                relativeHumidity: rh,
                humidityRatio: W,
                dewPoint: dewPoint,
                sensibleHeat: sensibleHeat,
                latentHeat: latentHeat,
                totalEnthalpy: totalEnthalpy,
                vaporPressure: ea
            };
        }
        
        function calculateDewPoint(ea) {
            // Magnus formula for dew point
            const a = 17.27;
            const b = 237.7;
            return (b * Math.log(ea/0.61078)) / (a - Math.log(ea/0.61078));
        }
        
        function saveCoolingLoadPsychCalculation(type, data) {
            let history = JSON.parse(localStorage.getItem('hvacCoolingLoadPsychHistory') || '[]');
            history.unshift({
                type: type,
                data: data,
                timestamp: new Date().toLocaleString()
            });
            
            // Keep only last 10 calculations
            history = history.slice(0, 10);
            localStorage.setItem('hvacCoolingLoadPsychHistory', JSON.stringify(history));
            loadCoolingLoadPsychHistory();
        }
        
        function loadCoolingLoadPsychHistory() {
            const history = JSON.parse(localStorage.getItem('hvacCoolingLoadPsychHistory') || '[]');
            const container = document.getElementById('coolingLoadPsychHistory');
            
            if (history.length === 0) {
                container.innerHTML = '<small class="text-white-50">No calculations yet</small>';
                return;
            }
            
            container.innerHTML = history.map(calc => `
                <div class="history-item">
                    <div class="small">
                        <strong>${calc.data.airFlow} CFM</strong>
                    </div>
                    <div class="small text-white-50">
                        ${calc.data.enteringDB}°C/${calc.data.leavingDB}°C DB → 
                        ${calc.data.coolingLoadTons.toFixed(2)} TR
                    </div>
                    <div class="small text-white-50">
                        Δh = ${calc.data.enthalpyDifference.toFixed(2)} kJ/kg
                    </div>
                    <div class="small text-white-50">
                        ${calc.timestamp}
                    </div>
                </div>
            `).join('');
        }
        
        function clearCoolingLoadPsychHistory() {
            showConfirmModal('Clear History', 'Are you sure you want to clear all calculation history?', function() {
                localStorage.removeItem('hvacCoolingLoadPsychHistory');
                loadCoolingLoadPsychHistory();
                showNotification('History cleared', 'success');
            });
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadCoolingLoadPsychHistory();
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
