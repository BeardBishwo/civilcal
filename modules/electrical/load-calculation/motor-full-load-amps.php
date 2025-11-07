<?php
// modules/electrical/motor/motor-full-load-amps.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motor Full Load Amps Calculator - AEC Toolkit</title>
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

        .motor-note {
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
            <h1><i class="fas fa-cog me-2"></i>Motor Full Load Amps</h1>
            <form id="motor-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="motor-hp"><i class="fas fa-horse-head me-2"></i>Motor HP</label>
                            <input type="number" id="motor-hp" class="form-control" step="0.25" min="0.25" max="1000" required>
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
                            <label for="motor-efficiency"><i class="fas fa-percentage me-2"></i>Efficiency (%)</label>
                            <input type="number" id="motor-efficiency" class="form-control" value="85" step="1" min="70" max="100" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="motor-power-factor"><i class="fas fa-bolt me-2"></i>Power Factor (%)</label>
                            <input type="number" id="motor-power-factor" class="form-control" value="80" step="1" min="60" max="100" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="motor-type"><i class="fas fa-cogs me-2"></i>Motor Type</label>
                            <select id="motor-type" class="form-control" required>
                                <option value="standard">Standard</option>
                                <option value="premium">Premium Efficiency</option>
                                <option value="vfd">VFD Duty</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Motor FLA</button>
            </form>
            
            <div class="motor-note">
                <i class="fas fa-info-circle me-2"></i>
                Full load amps calculated per NEC Tables 430.248 (Single Phase) and 430.250 (Three Phase). Efficiency and power factor adjustments included.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Motor FLA Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateMotorFLA(hp, voltage, phase, efficiency, powerFactor, motorType) {
            // Standard NEC table values (approximate)
            let standardFLA = 0;
            
            if (phase === 1) {
                // Single Phase Motor FLA (NEC Table 430.248)
                const singlePhase = {
                    0.25: 4.2, 0.33: 4.8, 0.5: 6.0, 0.75: 8.4, 1: 9.6, 1.5: 13.0, 2: 16.0,
                    3: 20.0, 5: 28.0, 7.5: 40.0, 10: 50.0, 15: 70.0, 20: 80.0
                };
                standardFLA = singlePhase[hp] || (hp * 5.5);
            } else {
                // Three Phase Motor FLA (NEC Table 430.250)
                const threePhase = {
                    0.25: 1.04, 0.33: 1.18, 0.5: 1.60, 0.75: 2.20, 1: 2.80, 1.5: 4.20, 2: 5.60,
                    3: 8.40, 5: 14.0, 7.5: 21.0, 10: 28.0, 15: 42.0, 20: 54.0, 25: 68.0,
                    30: 80.0, 40: 104.0, 50: 130.0, 60: 154.0, 75: 192.0, 100: 248.0
                };
                standardFLA = threePhase[hp] || (hp * 2.5);
            }
            
            // Adjust for voltage (NEC assumes 230V for single phase, 230V/460V for three phase)
            let voltageMultiplier = 1;
            if (phase === 1) {
                voltageMultiplier = 230 / voltage;
            } else {
                voltageMultiplier = (voltage === 460 || voltage === 480) ? 0.5 : (230 / voltage);
            }
            
            // Adjust for efficiency and power factor
            const efficiencyFactor = efficiency / 100;
            const powerFactorFactor = powerFactor / 100;
            
            const adjustedFLA = standardFLA * voltageMultiplier / (efficiencyFactor * powerFactorFactor);
            
            // Motor type adjustments
            let typeMultiplier = 1;
            switch (motorType) {
                case 'premium':
                    typeMultiplier = 0.95; // 5% less current
                    break;
                case 'vfd':
                    typeMultiplier = 1.1; // 10% more current
                    break;
            }
            
            const finalFLA = adjustedFLA * typeMultiplier;
            
            // Calculate wire size recommendation
            const wireFLA = finalFLA * 1.25; // 125% for motor circuits
            
            return {
                standardFLA: standardFLA,
                adjustedFLA: adjustedFLA,
                finalFLA: finalFLA,
                wireFLA: wireFLA,
                voltageMultiplier: voltageMultiplier,
                hp: hp,
                voltage: voltage,
                phase: phase,
                efficiency: efficiency,
                powerFactor: powerFactor,
                motorType: motorType
            };
        }

        document.getElementById('motor-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const hp = parseFloat(document.getElementById('motor-hp').value);
            const voltage = parseFloat(document.getElementById('motor-voltage').value);
            const phase = parseInt(document.getElementById('motor-phase').value);
            const efficiency = parseFloat(document.getElementById('motor-efficiency').value);
            const powerFactor = parseFloat(document.getElementById('motor-power-factor').value);
            const motorType = document.getElementById('motor-type').value;

            if (isNaN(hp) || isNaN(voltage) || isNaN(phase) || 
                isNaN(efficiency) || isNaN(powerFactor)) {
                alert('Please enter valid numbers.');
                return;
            }
            
            const result = calculateMotorFLA(hp, voltage, phase, efficiency, powerFactor, motorType);
            
            const phaseText = phase === 3 ? 'Three Phase' : 'Single Phase';
            const motorTypeText = {
                'standard': 'Standard',
                'premium': 'Premium Efficiency',
                'vfd': 'VFD Duty'
            };
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Motor HP:</strong><br>${result.hp} HP
                </div>
                <div class="result-item">
                    <strong>Motor Voltage:</strong><br>${result.voltage}V ${phaseText}
                </div>
                <div class="result-item">
                    <strong>Efficiency:</strong><br>${result.efficiency}%
                </div>
                <div class="result-item">
                    <strong>Power Factor:</strong><br>${result.powerFactor}%
                </div>
                <div class="result-item">
                    <strong>Motor Type:</strong><br>${motorTypeText[motorType]}
                </div>
                <div class="result-item">
                    <strong>Standard FLA:</strong><br>${result.standardFLA.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Voltage Adj Factor:</strong><br>${result.voltageMultiplier.toFixed(3)}
                </div>
                <div class="result-item">
                    <strong>Adjusted FLA:</strong><br>${result.adjustedFLA.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Final FLA:</strong><br>${result.finalFLA.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Wire Current (125%):</strong><br>${result.wireFLA.toFixed(1)} A
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Motor FLA', `${hp}HP ${phaseText} → ${result.finalFLA.toFixed(1)}A FLA`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentMotorFLACalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentMotorFLACalculations', JSON.stringify(recent));
        }
    </script>
</body>
</html>
