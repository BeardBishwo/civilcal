<?php
// modules/electrical/load-calculation/feeder-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feeder Sizing Calculator - AEC Toolkit</title>
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

        .feeder-note {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .load-section {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .load-section h4 {
            color: var(--yellow);
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 0.5rem;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-sitemap me-2"></i>Feeder Sizing Calculator</h1>
            <form id="feeder-form">
                <div class="load-section">
                    <h4><i class="fas fa-bolt me-2"></i>Load Information</h4>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="total-lighting"><i class="fas fa-lightbulb me-2"></i>Total Lighting Load (VA)</label>
                                <input type="number" id="total-lighting" class="form-control" step="100" min="0" placeholder="Enter lighting load">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="total-receptacle"><i class="fas fa-plug me-2"></i>Total Receptacle Load (VA)</label>
                                <input type="number" id="total-receptacle" class="form-control" step="100" min="0" placeholder="Enter receptacle load">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="appliance-load"><i class="fas fa-blender me-2"></i>Appliance Load (VA)</label>
                                <input type="number" id="appliance-load" class="form-control" step="100" min="0" placeholder="Enter appliance load">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="motor-load"><i class="fas fa-cog me-2"></i>Motor Load (VA)</label>
                                <input type="number" id="motor-load" class="form-control" step="100" min="0" placeholder="Enter motor load">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="heating-cooling"><i class="fas fa-thermometer-half me-2"></i>Heating/Cooling Load (VA)</label>
                                <input type="number" id="heating-cooling" class="form-control" step="100" min="0" placeholder="Enter HVAC load">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="other-load"><i class="fas fa-plus me-2"></i>Other Load (VA)</label>
                                <input type="number" id="other-load" class="form-control" step="100" min="0" placeholder="Enter other loads">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="load-section">
                    <h4><i class="fas fa-cog me-2"></i>Feeder Configuration</h4>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="system-voltage"><i class="fas fa-plug me-2"></i>System Voltage (V)</label>
                                <select id="system-voltage" class="form-control" required>
                                    <option value="120">120V</option>
                                    <option value="208">208V</option>
                                    <option value="240">240V</option>
                                    <option value="480">480V</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="phase-type"><i class="fas fa-wave-square me-2"></i>Phase</label>
                                <select id="phase-type" class="form-control" required>
                                    <option value="1">Single Phase</option>
                                    <option value="3">Three Phase</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="feeder-length"><i class="fas fa-route me-2"></i>Feeder Length (feet)</label>
                                <input type="number" id="feeder-length" class="form-control" step="1" min="1" placeholder="Enter feeder length">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="conductor-material"><i class="fas fa-cog me-2"></i>Conductor Material</label>
                                <select id="conductor-material" class="form-control" required>
                                    <option value="copper">Copper</option>
                                    <option value="aluminum">Aluminum</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="insulation-rating"><i class="fas fa-layer-group me-2"></i>Insulation Rating</label>
                                <select id="insulation-rating" class="form-control" required>
                                    <option value="75">75°C (THW, THWN)</option>
                                    <option value="90">90°C (THHN, XHHW)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="ambient-temp"><i class="fas fa-thermometer-half me-2"></i>Ambient Temperature (°F)</label>
                                <input type="number" id="ambient-temp" class="form-control" value="86" step="5" min="32" max="140" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="diversity-factor"><i class="fas fa-percentage me-2"></i>Diversity Factor (%)</label>
                                <input type="number" id="diversity-factor" class="form-control" value="80" step="5" min="50" max="100" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="continuous-load"><i class="fas fa-clock me-2"></i>Continuous Load (%)</label>
                                <input type="number" id="continuous-load" class="form-control" value="125" step="5" min="100" max="150" required>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Feeder Size</button>
            </form>
            
            <div class="feeder-note">
                <i class="fas fa-info-circle me-2"></i>
                Feeder sizing per NEC Article 215. Includes demand factors, continuous load adjustments, and voltage drop considerations.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Feeder Sizing Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function getWireAmpacity(wireSize, material, insulation, ambientTemp, conductors) {
            // NEC Table 310.16 base ampacities
            const copperAmpacity = {
                '60': { '14': 15, '12': 20, '10': 30, '8': 40, '6': 55, '4': 70, '2': 95, '1': 110, '1/0': 125, '2/0': 145, '3/0': 165, '4/0': 195, '250': 215, '300': 240, '350': 260, '400': 280, '500': 320 },
                '75': { '14': 20, '12': 25, '10': 35, '8': 50, '6': 65, '4': 85, '2': 115, '1': 130, '1/0': 150, '2/0': 175, '3/0': 200, '4/0': 230, '250': 255, '300': 285, '350': 310, '400': 335, '500': 380 },
                '90': { '14': 25, '12': 30, '10': 40, '8': 55, '6': 75, '4': 95, '2': 130, '1': 150, '1/0': 170, '2/0': 195, '3/0': 225, '4/0': 260, '250': 290, '300': 320, '350': 350, '400': 380, '500': 420 }
            };

            const aluminumAmpacity = {
                '60': { '8': 30, '6': 40, '4': 55, '2': 75, '1/0': 100, '2/0': 115, '3/0': 135, '4/0': 155, '250': 170, '300': 190, '350': 210, '400': 225, '500': 260 },
                '75': { '8': 40, '6': 50, '4': 65, '2': 90, '1/0': 120, '2/0': 135, '3/0': 155, '4/0': 180, '250': 205, '300': 230, '350': 250, '400': 270, '500': 310 },
                '90': { '8': 45, '6': 60, '4': 75, '2': 100, '1/0': 135, '2/0': 150, '3/0': 175, '4/0': 205, '250': 230, '300': 255, '350': 280, '400': 305, '500': 350 }
            };
            
            const ampacityTable = material === 'copper' ? copperAmpacity : aluminumAmpacity;
            let ampacity = ampacityTable[insulation] ? ampacityTable[insulation][wireSize] : 0;
            
            // Temperature correction factor
            const tempFactors = {
                '75': [1.0, 0.96, 0.91, 0.87, 0.82, 0.76, 0.69, 0.61, 0.50, 0.40],
                '90': [1.0, 0.97, 0.94, 0.90, 0.87, 0.83, 0.78, 0.72, 0.65, 0.58]
            };
            
            const tempIndex = Math.floor((ambientTemp - 86) / 5);
            if (tempIndex >= 0 && tempIndex < tempFactors[insulation].length) {
                ampacity *= tempFactors[insulation][tempIndex];
            }
            
            // Adjust for number of conductors
            if (conductors > 3) {
                ampacity *= 0.8;
            }
            
            return ampacity;
        }

        function calculateVoltageDrop(current, length, voltage, phase, material, wireSize) {
            const kFactor = material === 'copper' ? 12.9 : 21.2;
            
            // Get wire resistance per 1000 ft (approximate values)
            const resistance = {
                '14': 8.45, '12': 5.31, '10': 3.36, '8': 2.11, '6': 1.33, '4': 0.835, '2': 0.525,
                '1': 0.395, '1/0': 0.313, '2/0': 0.248, '3/0': 0.197, '4/0': 0.156, '250': 0.132,
                '300': 0.110, '350': 0.094, '400': 0.082, '500': 0.066
            };

            const wireResistance = resistance[wireSize] || 0.156;
            let voltageDrop = 0;
            
            if (phase === 1) {
                // Single phase: VD = (2 × I × R × D) / 1000
                voltageDrop = (2 * current * wireResistance * length) / 1000;
            } else {
                // Three phase: VD = (1.732 × I × R × D) / 1000
                voltageDrop = (1.732 * current * wireResistance * length) / 1000;
            }
            
            return voltageDrop;
        }

        function calculateFeederSizing() {
            const lighting = parseFloat(document.getElementById('total-lighting').value) || 0;
            const receptacle = parseFloat(document.getElementById('total-receptacle').value) || 0;
            const appliance = parseFloat(document.getElementById('appliance-load').value) || 0;
            const motor = parseFloat(document.getElementById('motor-load').value) || 0;
            const hvac = parseFloat(document.getElementById('heating-cooling').value) || 0;
            const other = parseFloat(document.getElementById('other-load').value) || 0;
            
            const voltage = parseFloat(document.getElementById('system-voltage').value);
            const phase = parseInt(document.getElementById('phase-type').value);
            const length = parseFloat(document.getElementById('feeder-length').value) || 100;
            const material = document.getElementById('conductor-material').value;
            const insulation = document.getElementById('insulation-rating').value;
            const ambientTemp = parseFloat(document.getElementById('ambient-temp').value);
            const diversity = parseFloat(document.getElementById('diversity-factor').value);
            const continuousPercent = parseFloat(document.getElementById('continuous-load').value);
            
            // Total load calculation
            const totalLoad = lighting + receptacle + appliance + motor + hvac + other;
            
            // Apply diversity factor
            const demandLoad = totalLoad * (diversity / 100);
            
            // Apply continuous load factor
            const continuousLoad = demandLoad * (continuousPercent / 100);
            
            // Calculate current
            let loadCurrent = 0;
            if (phase === 1) {
                loadCurrent = continuousLoad / voltage;
            } else {
                loadCurrent = continuousLoad / (1.732 * voltage);
            }
            
            // Find minimum wire size by ampacity
            const wireSizes = ['14', '12', '10', '8', '6', '4', '2', '1', '1/0', '2/0', '3/0', '4/0', '250', '300', '350', '400', '500'];
            let minWireSize = '14';
            let wireAmpacity = getWireAmpacity('14', material, insulation, ambientTemp, 4);
            
            for (const size of wireSizes) {
                const ampacity = getWireAmpacity(size, material, insulation, ambientTemp, 4);
                if (ampacity >= loadCurrent) {
                    minWireSize = size;
                    wireAmpacity = ampacity;
                    break;
                }
            }
            
            // Calculate voltage drop
            const voltageDrop = calculateVoltageDrop(loadCurrent, length, voltage, phase, material, minWireSize);
            const voltageDropPercent = (voltageDrop / voltage) * 100;
            
            // Find optimum wire size based on voltage drop (<= 3%)
            let optimumSize = minWireSize;
            let optimumVoltageDrop = voltageDrop;
            let optimumPercent = voltageDropPercent;
            
            for (const size of wireSizes) {
                const vd = calculateVoltageDrop(loadCurrent, length, voltage, phase, material, size);
                const vdPercent = (vd / voltage) * 100;
                if (vdPercent <= 3.0) {
                    optimumSize = size;
                    optimumVoltageDrop = vd;
                    optimumPercent = vdPercent;
                    break;
                }
            }
            
            return {
                totalLoad: totalLoad,
                demandLoad: demandLoad,
                continuousLoad: continuousLoad,
                loadCurrent: loadCurrent,
                minWireSize: minWireSize,
                minWireAmpacity: wireAmpacity,
                optimumWireSize: optimumSize,
                optimumVoltageDrop: optimumVoltageDrop,
                optimumVoltageDropPercent: optimumPercent,
                voltage: voltage,
                phase: phase,
                length: length,
                material: material,
                insulation: insulation,
                ambientTemp: ambientTemp,
                diversity: diversity,
                continuousPercent: continuousPercent,
                voltageDrop: voltageDrop,
                voltageDropPercent: voltageDropPercent,
                loads: { lighting, receptacle, appliance, motor, hvac, other }
            };
        }

        document.getElementById('feeder-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const result = calculateFeederSizing();
            
            const phaseText = result.phase === 3 ? '3-Phase' : '1-Phase';
            const materialText = result.material === 'copper' ? 'Copper' : 'Aluminum';
            const insulationText = result.insulation === '75' ? '75°C' : '90°C';
            
            const loadSummary = Object.entries(result.loads)
                .filter(([key, value]) => value > 0)
                .map(([key, value]) => `<li>${key.charAt(0).toUpperCase() + key.slice(1).replace('-', ' ')}: ${value.toLocaleString()} VA</li>`)
                .join('');
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Load Summary:</strong><br>
                    <ul style="margin: 0.5rem 0; padding-left: 1rem;">
                        ${loadSummary || '<li>No specific loads entered</li>'}
                    </ul>
                </div>
                <div class="result-item">
                    <strong>Total Load:</strong><br>${result.totalLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Demand Load:</strong><br>${result.demandLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Continuous Load:</strong><br>${result.continuousLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Load Current:</strong><br>${result.loadCurrent.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>System:</strong><br>${result.voltage}V ${phaseText}
                </div>
                <div class="result-item">
                    <strong>Diversity Factor:</strong><br>${result.diversity}%
                </div>
                <div class="result-item">
                    <strong>Continuous Factor:</strong><br>${result.continuousPercent}%
                </div>
                <div class="result-item">
                    <strong>Feeder Length:</strong><br>${result.length} feet
                </div>
                <div class="result-item">
                    <strong>Minimum Wire Size:</strong><br>${result.minWireSize} AWG ${materialText}
                </div>
                <div class="result-item">
                    <strong>Wire Ampacity:</strong><br>${result.minWireAmpacity.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Recommended Wire Size:</strong><br>${result.optimumWireSize} AWG ${materialText}
                </div>
                <div class="result-item">
                    <strong>Voltage Drop (Min Size):</strong><br>${result.voltageDrop.toFixed(2)}V (${result.voltageDropPercent.toFixed(2)}%)
                </div>
                <div class="result-item">
                    <strong>Voltage Drop (Recommended):</strong><br>${result.optimumVoltageDrop.toFixed(2)}V (${result.optimumVoltageDropPercent.toFixed(2)}%)
                </div>
                <div class="result-item">
                    <strong>Insulation Rating:</strong><br>${insulationText} @ ${result.ambientTemp}°F
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            const summary = `${result.loadCurrent.toFixed(1)}A @ ${result.voltage}V → ${result.optimumWireSize} AWG`;
            saveCalculation('Feeder Sizing', summary);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentFeederCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentFeederCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
