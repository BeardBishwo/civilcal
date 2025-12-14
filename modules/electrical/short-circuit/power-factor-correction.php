<?php
// modules/electrical/energy/power-factor-correction.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Power Factor Correction Calculator - AEC Toolkit</title>
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

        .pf-note {
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
            <h1><i class="fas fa-chart-line me-2"></i>Power Factor Correction</h1>
            <form id="pf-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="real-power"><i class="fas fa-bolt me-2"></i>Real Power (kW)</label>
                            <input type="number" id="real-power" class="form-control" step="0.1" min="0.1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="current-pf"><i class="fas fa-percentage me-2"></i>Current Power Factor</label>
                            <input type="number" id="current-pf" class="form-control" step="0.01" min="0.1" max="1.0" value="0.8" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="target-pf"><i class="fas fa-target me-2"></i>Target Power Factor</label>
                            <input type="number" id="target-pf" class="form-control" step="0.01" min="0.1" max="1.0" value="0.95" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="system-voltage"><i class="fas fa-plug me-2"></i>System Voltage (V)</label>
                            <input type="number" id="system-voltage" class="form-control" step="1" value="480" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="operating-hours"><i class="fas fa-clock me-2"></i>Operating Hours/Year</label>
                            <input type="number" id="operating-hours" class="form-control" step="1" value="8760" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="energy-rate"><i class="fas fa-dollar-sign me-2"></i>Energy Rate ($/kWh)</label>
                            <input type="number" id="energy-rate" class="form-control" step="0.01" value="0.12" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate PF Correction</button>
            </form>
            
            <div class="pf-note">
                <i class="fas fa-info-circle me-2"></i>
                Power factor correction reduces reactive power and improves system efficiency. Capacitor sizing based on target power factor improvement.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Power Factor Correction Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculatePowerFactorCorrection(realPowerKW, currentPF, targetPF, systemVoltage, operatingHours, energyRate) {
            // Calculate apparent power
            const apparentPower = realPowerKW / currentPF;
            
            // Calculate reactive power before correction
            const reactivePowerQ1 = Math.sqrt(Math.pow(apparentPower, 2) - Math.pow(realPowerKW, 2));
            
            // Calculate apparent power after correction
            const correctedApparentPower = realPowerKW / targetPF;
            
            // Calculate reactive power after correction
            const reactivePowerQ2 = Math.sqrt(Math.pow(correctedApparentPower, 2) - Math.pow(realPowerKW, 2));
            
            // Calculate capacitor kVAR required
            const capacitorKVAR = reactivePowerQ1 - reactivePowerQ2;
            
            // Calculate current reduction
            const currentBefore = apparentPower / systemVoltage;
            const currentAfter = correctedApparentPower / systemVoltage;
            const currentReduction = currentBefore - currentAfter;
            const currentReductionPercent = (currentReduction / currentBefore) * 100;
            
            // Calculate energy savings
            const reactivePowerReduction = capacitorKVAR;
            const energySavingsKWH = reactivePowerReduction * operatingHours * 0.3; // 30% efficiency factor
            const annualSavings = energySavingsKWH * energyRate;
            
            // Calculate cost of capacitor bank
            const capacitorCost = capacitorKVAR * 50; // $50 per kVAR estimate
            const paybackPeriod = capacitorCost / annualSavings;
            
            return {
                apparentPower: apparentPower,
                reactivePowerQ1: reactivePowerQ1,
                correctedApparentPower: correctedApparentPower,
                reactivePowerQ2: reactivePowerQ2,
                capacitorKVAR: capacitorKVAR,
                currentBefore: currentBefore,
                currentAfter: currentAfter,
                currentReduction: currentReduction,
                currentReductionPercent: currentReductionPercent,
                energySavingsKWH: energySavingsKWH,
                annualSavings: annualSavings,
                capacitorCost: capacitorCost,
                paybackPeriod: paybackPeriod,
                realPowerKW: realPowerKW,
                currentPF: currentPF,
                targetPF: targetPF
            };
        }

        document.getElementById('pf-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const realPowerKW = parseFloat(document.getElementById('real-power').value);
            const currentPF = parseFloat(document.getElementById('current-pf').value);
            const targetPF = parseFloat(document.getElementById('target-pf').value);
            const systemVoltage = parseFloat(document.getElementById('system-voltage').value);
            const operatingHours = parseInt(document.getElementById('operating-hours').value);
            const energyRate = parseFloat(document.getElementById('energy-rate').value);

            if (isNaN(realPowerKW) || isNaN(currentPF) || isNaN(targetPF) || 
                isNaN(systemVoltage) || isNaN(operatingHours) || isNaN(energyRate)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            if (currentPF >= targetPF) {
                showNotification('Target power factor must be higher than current power factor.', 'info');
                return;
            }
            
            const result = calculatePowerFactorCorrection(realPowerKW, currentPF, targetPF, systemVoltage, operatingHours, energyRate);
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Real Power:</strong><br>${result.realPowerKW} kW
                </div>
                <div class="result-item">
                    <strong>Current PF:</strong><br>${result.currentPF.toFixed(3)}
                </div>
                <div class="result-item">
                    <strong>Target PF:</strong><br>${result.targetPF.toFixed(3)}
                </div>
                <div class="result-item">
                    <strong>Apparent Power (Before):</strong><br>${result.apparentPower.toFixed(1)} kVA
                </div>
                <div class="result-item">
                    <strong>Reactive Power (Before):</strong><br>${result.reactivePowerQ1.toFixed(1)} kVAR
                </div>
                <div class="result-item">
                    <strong>Apparent Power (After):</strong><br>${result.correctedApparentPower.toFixed(1)} kVA
                </div>
                <div class="result-item">
                    <strong>Reactive Power (After):</strong><br>${result.reactivePowerQ2.toFixed(1)} kVAR
                </div>
                <div class="result-item">
                    <strong>Capacitor Required:</strong><br>${result.capacitorKVAR.toFixed(1)} kVAR
                </div>
                <div class="result-item">
                    <strong>Current (Before):</strong><br>${result.currentBefore.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Current (After):</strong><br>${result.currentAfter.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Current Reduction:</strong><br>${result.currentReductionPercent.toFixed(1)}%
                </div>
                <div class="result-item">
                    <strong>Annual Savings:</strong><br>${result.annualSavings.toLocaleString('en-US', {style: 'currency', currency: 'USD'})}
                </div>
                <div class="result-item">
                    <strong>Capacitor Cost:</strong><br>${result.capacitorCost.toLocaleString('en-US', {style: 'currency', currency: 'USD'})}
                </div>
                <div class="result-item">
                    <strong>Payback Period:</strong><br>${result.paybackPeriod.toFixed(1)} years
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Power Factor Correction', `${realPowerKW}kW ${currentPF}→${targetPF}PF → ${result.capacitorKVAR.toFixed(1)}kVAR`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentPowerFactorCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentPowerFactorCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
