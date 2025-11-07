<?php
// modules/electrical/voltage-drop/three-phase-voltage-drop.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Three Phase Voltage Drop Calculator - AEC Toolkit</title>
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

        .result-item.good {
            background: rgba(76, 175, 80, 0.3);
        }

        .result-item.fair {
            background: rgba(255, 193, 7, 0.3);
        }

        .result-item.poor {
            background: rgba(244, 67, 54, 0.3);
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

        .vd-note {
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
            <h1><i class="fas fa-tachometer-alt me-2"></i>Three Phase Voltage Drop</h1>
            <form id="vd3-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="current"><i class="fas fa-bolt me-2"></i>Current (Amps)</label>
                            <input type="number" id="current" class="form-control" step="0.1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="distance"><i class="fas fa-route me-2"></i>Distance (feet)</label>
                            <input type="number" id="distance" class="form-control" step="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="wire-size"><i class="fas fa-wire me-2"></i>Wire Size</label>
                            <select id="wire-size" class="form-control" required>
                                <option value="14">14 AWG</option>
                                <option value="12">12 AWG</option>
                                <option value="10">10 AWG</option>
                                <option value="8">8 AWG</option>
                                <option value="6">6 AWG</option>
                                <option value="4">4 AWG</option>
                                <option value="2">2 AWG</option>
                                <option value="1">1 AWG</option>
                                <option value="1/0">1/0 AWG</option>
                                <option value="2/0">2/0 AWG</option>
                                <option value="3/0">3/0 AWG</option>
                                <option value="4/0">4/0 AWG</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="voltage"><i class="fas fa-plug me-2"></i>System Voltage</label>
                            <select id="voltage" class="form-control" required>
                                <option value="208">208V</option>
                                <option value="240">240V</option>
                                <option value="480">480V</option>
                                <option value="600">600V</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="material"><i class="fas fa-cog me-2"></i>Wire Material</label>
                            <select id="material" class="form-control" required>
                                <option value="copper">Copper</option>
                                <option value="aluminum">Aluminum</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="power-factor"><i class="fas fa-percentage me-2"></i>Power Factor (%)</label>
                            <input type="number" id="power-factor" class="form-control" value="100" step="5" min="50" max="100" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Voltage Drop</button>
            </form>
            
            <div class="vd-note">
                <i class="fas fa-info-circle me-2"></i>
                Three phase voltage drop formula: VD = (1.732 × I × R × D) / 1000
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Voltage Drop Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function getWireResistance(size, material) {
            const copperResistance = {
                '14': 2.525, '12': 1.588, '10': 0.9989, '8': 0.6282,
                '6': 0.3951, '4': 0.2485, '2': 0.1563, '1': 0.1239,
                '1/0': 0.0983, '2/0': 0.0779, '3/0': 0.0618, '4/0': 0.0490
            };
            
            const aluminumResistance = {
                '14': 4.106, '12': 2.525, '10': 1.588, '8': 0.9989,
                '6': 0.6282, '4': 0.3951, '2': 0.2485, '1': 0.1970,
                '1/0': 0.1563, '2/0': 0.1239, '3/0': 0.0983, '4/0': 0.0779
            };
            
            if (material === 'aluminum') {
                return aluminumResistance[size] || 4.106;
            } else {
                return copperResistance[size] || 2.525;
            }
        }

        function calculateVoltageDrop(current, distance, wireSize, voltage, material, pf) {
            const resistance = getWireResistance(wireSize, material);
            
            // Three phase voltage drop formula: VD = (1.732 × I × R × D) / 1000
            let voltageDrop = (1.732 * current * resistance * distance) / 1000;
            
            // Apply power factor correction if not 100%
            if (pf < 100) {
                voltageDrop = voltageDrop * (pf / 100);
            }
            
            const voltageDropPercent = (voltageDrop / voltage) * 100;
            const voltageAtLoad = voltage - voltageDrop;
            
            let assessment = 'good';
            if (voltageDropPercent > 5) assessment = 'poor';
            else if (voltageDropPercent > 3) assessment = 'fair';
            
            return {
                voltageDrop: voltageDrop,
                voltageDropPercent: voltageDropPercent,
                voltageAtLoad: voltageAtLoad,
                assessment: assessment,
                resistance: resistance
            };
        }

        document.getElementById('vd3-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const current = parseFloat(document.getElementById('current').value);
            const distance = parseFloat(document.getElementById('distance').value);
            const wireSize = document.getElementById('wire-size').value;
            const voltage = parseFloat(document.getElementById('voltage').value);
            const material = document.getElementById('material').value;
            const powerFactor = parseFloat(document.getElementById('power-factor').value);

            if (isNaN(current) || isNaN(distance) || isNaN(voltage) || isNaN(powerFactor)) {
                alert('Please enter valid numbers.');
                return;
            }
            
            const result = calculateVoltageDrop(current, distance, wireSize, voltage, material, powerFactor);
            
            let assessmentClass = result.assessment;
            let assessmentText = result.assessment === 'good' ? 'Good' : 
                               result.assessment === 'fair' ? 'Fair' : 'Poor';
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Current:</strong><br>${current} A
                </div>
                <div class="result-item">
                    <strong>Distance:</strong><br>${distance} feet
                </div>
                <div class="result-item">
                    <strong>Wire Size:</strong><br>${wireSize} AWG ${material}
                </div>
                <div class="result-item">
                    <strong>System Voltage:</strong><br>${voltage}V 3-Phase
                </div>
                <div class="result-item">
                    <strong>Voltage Drop:</strong><br>${result.voltageDrop.toFixed(2)}V
                </div>
                <div class="result-item ${assessmentClass}">
                    <strong>Voltage Drop:</strong><br>${result.voltageDropPercent.toFixed(2)}%
                </div>
                <div class="result-item">
                    <strong>Voltage at Load:</strong><br>${result.voltageAtLoad.toFixed(1)}V
                </div>
                <div class="result-item ${assessmentClass}">
                    <strong>Assessment:</strong><br>${assessmentText}
                </div>
                <div class="result-item">
                    <strong>Wire Resistance:</strong><br>${result.resistance.toFixed(4)} Ω/1000ft
                </div>
                <div class="result-item">
                    <strong>Power Factor:</strong><br>${powerFactor}%
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('3-Phase Voltage Drop', `${current}A, ${distance}ft → ${result.voltageDropPercent.toFixed(2)}% drop`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recent3PhaseVoltageDropCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recent3PhaseVoltageDropCalculations', JSON.stringify(recent));
        }
    </script>
</body>
</html>
