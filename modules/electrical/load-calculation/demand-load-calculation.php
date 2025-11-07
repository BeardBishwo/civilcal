<?php
// modules/electrical/load-calculation/demand-load-calculation.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demand Load Calculation Calculator - AEC Toolkit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #f093fb;
            --dark: #1a202c;
            --light: #f7fafc;
            --glass: rgba(255, 255, 255, 0.05);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            --yellow: #feca57;
        }

        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            color: var(--light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }

        .calculator-wrapper {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow);
            margin-top: 3rem;
        }

        .calculator-wrapper h1 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: var(--yellow);
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-col {
            flex: 1;
        }

        .form-group label {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--light);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #f093fb;
            box-shadow: 0 0 15px rgba(240, 147, 251, 0.3);
        }

        .btn-calculate {
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, #f093fb, #f5576c);
            border: none;
            border-radius: 50px;
            color: var(--light);
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-calculate:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .result-area {
            margin-top: 2rem;
            background: linear-gradient(45deg, #4ecdc4, #44a08d);
            padding: 2rem;
            border-radius: 10px;
            display: none;
            text-align: left;
        }

        .result-area h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }

        .result-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .result-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
        }

        .result-item strong {
            color: var(--yellow);
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: #f093fb;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .demand-note {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-calculator me-2"></i>Demand Load Calculation</h1>
            <form id="demand-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="connected-load"><i class="fas fa-plug me-2"></i>Connected Load (VA)</label>
                            <input type="number" id="connected-load" class="form-control" step="1" min="1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="demand-factor"><i class="fas fa-percentage me-2"></i>Demand Factor (%)</label>
                            <input type="number" id="demand-factor" class="form-control" step="1" min="1" max="100" value="100" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="diversity-factor"><i class="fas fa-chart-line me-2"></i>Diversity Factor (%)</label>
                            <input type="number" id="diversity-factor" class="form-control" step="1" min="1" max="100" value="100" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="load-type"><i class="fas fa-cog me-2"></i>Load Type</label>
                            <select id="load-type" class="form-control" required>
                                <option value="residential">Residential</option>
                                <option value="commercial">Commercial</option>
                                <option value="industrial">Industrial</option>
                                <option value="motor">Motor</option>
                                <option value="lighting">Lighting</option>
                                <option value="heating">Heating</option>
                                <option value="cooking">Cooking</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="continuous-load"><i class="fas fa-clock me-2"></i>Continuous Load (%)</label>
                            <input type="number" id="continuous-load" class="form-control" step="1" min="0" max="150" value="0" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="system-voltage"><i class="fas fa-plug me-2"></i>System Voltage (V)</label>
                            <input type="number" id="system-voltage" class="form-control" step="1" value="120" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Demand Load</button>
            </form>
            
            <div class="demand-note">
                <i class="fas fa-info-circle me-2"></i>
                Demand load calculation accounts for diversity and utilization factors per NEC Articles 220 and requirements for different load types.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Demand Load Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateDemandLoad(connectedVA, demandFactor, diversityFactor, loadType, continuousPercent, systemVoltage) {
            // Calculate diversified load
            const diversifiedLoad = connectedVA * (diversityFactor / 100);
            
            // Apply demand factor
            const demandLoad = diversifiedLoad * (demandFactor / 100);
            
            // Apply continuous load factor (125% if continuous)
            const continuousLoad = continuousPercent > 0 ? demandLoad * (continuousPercent / 100) : demandLoad;
            
            // Apply NEC specific demand factors based on load type
            let necDemandFactor = 1.0;
            switch (loadType) {
                case 'residential':
                    necDemandFactor = 0.75; // Typical residential demand factor
                    break;
                case 'commercial':
                    necDemandFactor = 0.85; // Typical commercial demand factor
                    break;
                case 'industrial':
                    necDemandFactor = 0.90; // Typical industrial demand factor
                    break;
                case 'motor':
                    necDemandFactor = 0.80; // Motor loads typically 80% demand
                    break;
                case 'lighting':
                    necDemandFactor = 1.0; // Lighting typically full demand
                    break;
                case 'heating':
                    necDemandFactor = 1.0; // Heating typically full demand
                    break;
                case 'cooking':
                    necDemandFactor = 0.65; // Cooking equipment reduced demand
                    break;
            }
            
            const necAdjustedLoad = demandLoad * necDemandFactor;
            
            // Calculate current requirements
            const current = necAdjustedLoad / systemVoltage;
            
            // Calculate required conductor sizing (125% for continuous loads)
            const conductorLoad = continuousPercent > 0 ? necAdjustedLoad * 1.25 : necAdjustedLoad;
            const conductorCurrent = conductorLoad / systemVoltage;
            
            return {
                connectedVA: connectedVA,
                diversifiedLoad: diversifiedLoad,
                demandLoad: demandLoad,
                continuousLoad: continuousLoad,
                necDemandFactor: necDemandFactor,
                necAdjustedLoad: necAdjustedLoad,
                current: current,
                conductorLoad: conductorLoad,
                conductorCurrent: conductorCurrent,
                demandFactor: demandFactor,
                diversityFactor: diversityFactor,
                loadType: loadType,
                continuousPercent: continuousPercent,
                systemVoltage: systemVoltage
            };
        }

        document.getElementById('demand-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const connectedVA = parseFloat(document.getElementById('connected-load').value);
            const demandFactor = parseFloat(document.getElementById('demand-factor').value);
            const diversityFactor = parseFloat(document.getElementById('diversity-factor').value);
            const loadType = document.getElementById('load-type').value;
            const continuousPercent = parseFloat(document.getElementById('continuous-load').value);
            const systemVoltage = parseFloat(document.getElementById('system-voltage').value);

            if (isNaN(connectedVA) || isNaN(demandFactor) || isNaN(diversityFactor) || 
                isNaN(continuousPercent) || isNaN(systemVoltage)) {
                alert('Please enter valid numbers.');
                return;
            }
            
            const result = calculateDemandLoad(connectedVA, demandFactor, diversityFactor, loadType, continuousPercent, systemVoltage);
            
            const loadTypeText = {
                'residential': 'Residential',
                'commercial': 'Commercial',
                'industrial': 'Industrial',
                'motor': 'Motor',
                'lighting': 'Lighting',
                'heating': 'Heating',
                'cooking': 'Cooking'
            };
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Load Type:</strong><br>${loadTypeText[loadType]}
                </div>
                <div class="result-item">
                    <strong>Connected Load:</strong><br>${result.connectedVA.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Diversity Factor:</strong><br>${result.diversityFactor}%
                </div>
                <div class="result-item">
                    <strong>Diversified Load:</strong><br>${result.diversifiedLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Demand Factor:</strong><br>${result.demandFactor}%
                </div>
                <div class="result-item">
                    <strong>Demand Load:</strong><br>${result.demandLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Continuous Load:</strong><br>${result.continuousPercent}%
                </div>
                <div class="result-item">
                    <strong>Continuous Load (VA):</strong><br>${result.continuousLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>NEC Demand Factor:</strong><br>${(result.necDemandFactor * 100).toFixed(0)}%
                </div>
                <div class="result-item">
                    <strong>NEC Adjusted Load:</strong><br>${result.necAdjustedLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>System Current:</strong><br>${result.current.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Conductor Load:</strong><br>${result.conductorLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Conductor Current:</strong><br>${result.conductorCurrent.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>System Voltage:</strong><br>${result.systemVoltage} V
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Demand Load', `${loadTypeText[loadType]} ${connectedVA.toLocaleString()}VA → ${result.necAdjustedLoad.toLocaleString()}VA demand`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentDemandLoadCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentDemandLoadCalculations', JSON.stringify(recent));
        }
    </script>
</body>
</html>
