<?php
// modules/electrical/wire-sizing/motor-circuit-wiring.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motor Circuit Wiring Calculator - AEC Toolkit</title>
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
                /* Removed inline background - using theme.css instead */
                min-height: 100vh;
                color: var(--light);
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                overflow-x: hidden;
            }        .container {
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

        .motor-chart {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
        }

        .chart-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            padding: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chart-row:first-child {
            font-weight: bold;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
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

        .motor-note {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-cog me-2"></i>Motor Circuit Wiring</h1>
            <form id="motor-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="motor-hp"><i class="fas fa-bolt me-2"></i>Motor Horsepower</label>
                            <input type="number" id="motor-hp" class="form-control" step="0.1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="motor-voltage"><i class="fas fa-plug me-2"></i>Motor Voltage</label>
                            <select id="motor-voltage" class="form-control" required>
                                <option value="120">120V</option>
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
                            <label for="motor-phase"><i class="fas fa-wave-square me-2"></i>Phase</label>
                            <select id="motor-phase" class="form-control" required>
                                <option value="1">Single Phase</option>
                                <option value="3">Three Phase</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="motor-efficiency"><i class="fas fa-tachometer-alt me-2"></i>Motor Efficiency (%)</label>
                            <input type="number" id="motor-efficiency" class="form-control" value="85" step="1" min="70" max="100" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="power-factor"><i class="fas fa-percentage me-2"></i>Power Factor (%)</label>
                            <input type="number" id="power-factor" class="form-control" value="80" step="1" min="50" max="100" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="service-factor"><i class="fas fa-exclamation-circle me-2"></i>Service Factor</label>
                            <select id="service-factor" class="form-control">
                                <option value="1.15">1.15</option>
                                <option value="1.25">1.25</option>
                                <option value="1.35">1.35</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="distance"><i class="fas fa-route me-2"></i>Distance (feet)</label>
                            <input type="number" id="distance" class="form-control" value="100" step="1">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="wire-material"><i class="fas fa-cog me-2"></i>Wire Material</label>
                            <select id="wire-material" class="form-control">
                                <option value="copper">Copper</option>
                                <option value="aluminum">Aluminum</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Motor Wiring</button>
            </form>
            
            <div class="motor-note">
                <i class="fas fa-info-circle me-2"></i>
                NEC 430.6(A) requires motor circuit conductors to have an ampacity of at least 125% of the motor full-load current (FLC). Circuit breakers are typically sized at 250% of FLC for motors.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Motor Circuit Wiring Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>

            <div class="motor-chart" id="motor-chart" style="display: none;">
                <h3><i class="fas fa-chart-bar me-2"></i>Motor Full Load Currents (Typical Values)</h3>
                <div class="chart-row">
                    <div>HP</div>
                    <div>120V Single Phase</div>
                    <div>240V Single Phase</div>
                </div>
                <div id="motor-chart-content"></div>
                <div class="chart-row">
                    <div>HP</div>
                    <div>208V Three Phase</div>
                    <div>480V Three Phase</div>
                </div>
                <div id="motor-chart-content-3phase"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function getMotorFLC(hp, voltage, phase) {
            // Simplified motor full load current values (typical values)
            const singlePhaseFLC = {
                '120': { '0.5': 4.4, '0.75': 6.4, '1': 8.0, '1.5': 10.0, '2': 12.0, '3': 16.0, '5': 24.0, '7.5': 35.0, '10': 46.0 },
                '240': { '0.5': 2.2, '0.75': 3.2, '1': 4.0, '1.5': 5.0, '2': 6.0, '3': 8.0, '5': 12.0, '7.5': 17.5, '10': 23.0 }
            };

            const threePhaseFLC = {
                '208': { '0.5': 2.2, '0.75': 3.2, '1': 3.7, '1.5': 5.2, '2': 6.8, '3': 9.6, '5': 15.2, '7.5': 22.0, '10': 28.0, '15': 42.0, '20': 54.0, '25': 68.0, '30': 80.0, '40': 104.0, '50': 130.0, '60': 154.0, '75': 192.0, '100': 248.0 },
                '240': { '0.5': 2.0, '0.75': 3.0, '1': 3.4, '1.5': 4.8, '2': 6.0, '3': 8.4, '5': 13.2, '7.5': 19.0, '10': 24.0, '15': 36.0, '20': 47.0, '25': 59.0, '30': 70.0, '40': 90.0, '50': 112.0, '60': 133.0, '75': 166.0, '100': 214.0 },
                '480': { '0.5': 1.0, '0.75': 1.5, '1': 1.7, '1.5': 2.4, '2': 3.0, '3': 4.2, '5': 6.6, '7.5': 9.5, '10': 12.0, '15': 18.0, '20': 23.5, '25': 29.5, '30': 35.0, '40': 45.0, '50': 56.0, '60': 66.5, '75': 83.0, '100': 107.0 }
            };

            const hpString = hp.toString();
            if (phase === 1) {
                return (singlePhaseFLC[voltage] && singlePhaseFLC[voltage][hpString]) || 0;
            } else {
                return (threePhaseFLC[voltage] && threePhaseFLC[voltage][hpString]) || 0;
            }
        }

        function calculateMotorWiring(hp, voltage, phase, efficiency, pf, serviceFactor) {
            // Calculate motor full load current
            const flc = getMotorFLC(hp, voltage, phase);
            
            if (flc === 0) {
                // Use formula-based calculation
                if (phase === 1) {
                    return (hp * 746) / (voltage * (efficiency / 100) * (pf / 100));
                } else {
                    return (hp * 746) / (1.732 * voltage * (efficiency / 100) * (pf / 100));
                }
            }
            
            return flc;
        }

        function getWireResistance(size, material) {
            const copperResistance = {
                '14': 2.525, '12': 1.588, '10': 0.9989, '8': 0.6282,
                '6': 0.3951, '4': 0.2485, '2': 0.1563, '1': 0.1239,
                '1/0': 0.0983, '2/0': 0.0779, '3/0': 0.0618, '4/0': 0.0490
            };
            
            if (material === 'aluminum') {
                return copperResistance[size] * 1.28; // Aluminum has ~28% higher resistance
            } else {
                return copperResistance[size] || 2.525;
            }
        }

        function getStarterSize(flc) {
            if (flc <= 9) return '00';
            if (flc <= 16) return '0';
            if (flc <= 27) return '1';
            if (flc <= 45) return '2';
            if (flc <= 90) return '3';
            if (flc <= 135) return '4';
            if (flc <= 270) return '5';
            return '6';
        }

        document.getElementById('motor-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const hp = parseFloat(document.getElementById('motor-hp').value);
            const voltage = parseFloat(document.getElementById('motor-voltage').value);
            const phase = parseInt(document.getElementById('motor-phase').value);
            const efficiency = parseFloat(document.getElementById('motor-efficiency').value);
            const pf = parseFloat(document.getElementById('power-factor').value);
            const serviceFactor = parseFloat(document.getElementById('service-factor').value);
            const distance = parseFloat(document.getElementById('distance').value);
            const wireMaterial = document.getElementById('wire-material').value;

            if (isNaN(hp) || isNaN(efficiency) || isNaN(pf)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            // Calculate motor full load current
            const flc = calculateMotorWiring(hp, voltage, phase, efficiency, pf, serviceFactor);
            
            // Wire sizing current (125% of FLC per NEC 430.22)
            const wireCurrent = flc * 1.25;
            
            // Find appropriate wire size
            const wireSizes = ['14', '12', '10', '8', '6', '4', '2', '1', '1/0', '2/0', '3/0', '4/0'];
            let wireSize = '14';
            let wireAmpacity = 0;
            
            for (const size of wireSizes) {
                const resistance = getWireResistance(size, wireMaterial);
                // For motor circuits, we use the base ampacity without temperature correction for simplicity
                const baseAmpacity = size === '14' ? 20 : size === '12' ? 25 : size === '10' ? 35 : 
                                   size === '8' ? 50 : size === '6' ? 65 : size === '4' ? 85 :
                                   size === '2' ? 115 : size === '1' ? 130 : size === '1/0' ? 150 :
                                   size === '2/0' ? 175 : size === '3/0' ? 200 : 230;
                
                if (baseAmpacity >= wireCurrent) {
                    wireSize = size;
                    wireAmpacity = baseAmpacity;
                    break;
                }
            }
            
            // Circuit breaker sizing (250% of FLC per NEC 430.52)
            const breakerSize = flc * 2.5;
            const standardBreakerSizes = [15, 20, 30, 40, 50, 60, 70, 80, 90, 100, 125, 150, 175, 200, 225, 250, 300, 350, 400, 450, 500, 600];
            const recommendedBreaker = standardBreakerSizes.find(size => size >= breakerSize) || 600;
            
            // Calculate voltage drop if distance provided
            let voltageDrop = 0;
            let voltageDropPercent = 0;
            if (distance && distance > 0) {
                const resistance = getWireResistance(wireSize, wireMaterial);
                if (phase === 1) {
                    voltageDrop = (2 * flc * resistance * distance) / 1000;
                } else {
                    voltageDrop = (1.732 * flc * resistance * distance) / 1000;
                }
                voltageDropPercent = (voltageDrop / voltage) * 100;
            }
            
            const starterSize = getStarterSize(flc);
            const serviceFactorAmps = flc * serviceFactor;
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Motor:</strong><br>${hp} HP @ ${voltage}V ${phase === 3 ? '3-Phase' : '1-Phase'}
                </div>
                <div class="result-item">
                    <strong>Full Load Current:</strong><br>${flc.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Efficiency:</strong><br>${efficiency}%
                </div>
                <div class="result-item">
                    <strong>Power Factor:</strong><br>${pf}%
                </div>
                <div class="result-item">
                    <strong>Recommended Wire:</strong><br>${wireSize} AWG ${wireMaterial}
                </div>
                <div class="result-item">
                    <strong>Wire Ampacity:</strong><br>${wireAmpacity} A
                </div>
                <div class="result-item">
                    <strong>Circuit Breaker:</strong><br>${recommendedBreaker} A
                </div>
                <div class="result-item">
                    <strong>Starter Size:</strong><br>NEMA Size ${starterSize}
                </div>
                <div class="result-item">
                    <strong>Service Factor Amps:</strong><br>${serviceFactorAmps.toFixed(1)} A
                </div>
                ${distance && distance > 0 ? `
                <div class="result-item">
                    <strong>Voltage Drop (${distance}ft):</strong><br>${voltageDrop.toFixed(2)}V (${voltageDropPercent.toFixed(2)}%)
                </div>
                <div class="result-item">
                    <strong>VD Assessment:</strong><br>${voltageDropPercent > 5 ? 'Poor' : voltageDropPercent > 3 ? 'Fair' : 'Good'}
                </div>
                ` : ''}
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Show motor chart
            showMotorChart();
            
            // Save calculation
            saveCalculation('Motor Wiring', `${hp}HP → ${wireSize} AWG, ${recommendedBreaker}A breaker`);
        });

        function showMotorChart() {
            const singlePhaseContent = document.getElementById('motor-chart-content');
            const threePhaseContent = document.getElementById('motor-chart-content-3phase');
            
            const singlePhaseHPs = ['0.5', '0.75', '1', '1.5', '2', '3', '5', '7.5', '10'];
            const threePhaseHPs = ['0.5', '0.75', '1', '1.5', '2', '3', '5', '7.5', '10', '15', '20', '25', '30', '40', '50', '60', '75', '100'];
            
            let singleHtml = '';
            singlePhaseHPs.forEach(hp => {
                const flc120 = getMotorFLC(parseFloat(hp), '120', 1);
                const flc240 = getMotorFLC(parseFloat(hp), '240', 1);
                singleHtml += `
                    <div class="chart-row">
                        <div>${hp}</div>
                        <div>${flc120 ? flc120.toFixed(1) + 'A' : '-'}</div>
                        <div>${flc240 ? flc240.toFixed(1) + 'A' : '-'}</div>
                    </div>
                `;
            });
            
            let threeHtml = '';
            threePhaseHPs.forEach(hp => {
                const flc208 = getMotorFLC(parseFloat(hp), '208', 3);
                const flc480 = getMotorFLC(parseFloat(hp), '480', 3);
                threeHtml += `
                    <div class="chart-row">
                        <div>${hp}</div>
                        <div>${flc208 ? flc208.toFixed(1) + 'A' : '-'}</div>
                        <div>${flc480 ? flc480.toFixed(1) + 'A' : '-'}</div>
                    </div>
                `;
            });
            
            singlePhaseContent.innerHTML = singleHtml;
            threePhaseContent.innerHTML = threeHtml;
            document.getElementById('motor-chart').style.display = 'block';
        }

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentMotorCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentMotorCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
