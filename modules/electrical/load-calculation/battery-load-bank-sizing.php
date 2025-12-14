<?php
// modules/electrical/measurement/battery-load-bank-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battery Load Bank Sizing Calculator - AEC Toolkit</title>
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

        .battery-note {
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
            <h1><i class="fas fa-battery-full me-2"></i>Battery Load Bank Sizing</h1>
            <form id="battery-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="battery-voltage"><i class="fas fa-battery-three-quarters me-2"></i>Battery Voltage (V)</label>
                            <input type="number" id="battery-voltage" class="form-control" step="1" value="120" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="battery-capacity"><i class="fas fa-thermometer-half me-2"></i>Battery Capacity (Ah)</label>
                            <input type="number" id="battery-capacity" class="form-control" step="1" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="test-current"><i class="fas fa-bolt me-2"></i>Test Current (% of Capacity)</label>
                            <input type="number" id="test-current" class="form-control" step="1" min="10" max="100" value="50" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="test-duration"><i class="fas fa-clock me-2"></i>Test Duration (hours)</label>
                            <input type="number" id="test-duration" class="form-control" step="0.5" min="0.5" value="4" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="battery-type"><i class="fas fa-cog me-2"></i>Battery Type</label>
                            <select id="battery-type" class="form-control" required>
                                <option value="lead-acid">Lead Acid</option>
                                <option value="lithium">Lithium Ion</option>
                                <option value="nickel-cadmium">Nickel Cadmium</option>
                                <option value="gel">Gel Cell</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="test-voltage"><i class="fas fa-exclamation-triangle me-2"></i>End Test Voltage (V)</label>
                            <input type="number" id="test-voltage" class="form-control" step="0.1" value="105" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Load Bank</button>
            </form>
            
            <div class="battery-note">
                <i class="fas fa-info-circle me-2"></i>
                Load bank sizing for battery testing includes capacity verification and discharge testing per IEEE standards.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Load Bank Sizing Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateLoadBankSizing(batteryVoltage, batteryCapacity, testCurrentPercent, testDuration, batteryType, endTestVoltage) {
            // Calculate test current
            const testCurrent = (batteryCapacity * testCurrentPercent) / 100;
            
            // Calculate load bank power
            const initialPower = batteryVoltage * testCurrent;
            const averagePower = ((batteryVoltage + endTestVoltage) / 2) * testCurrent;
            const totalEnergy = averagePower * testDuration;
            
            // Calculate efficiency factors
            let efficiencyFactor = 0.95; // Default 95%
            switch (batteryType) {
                case 'lithium':
                    efficiencyFactor = 0.98;
                    break;
                case 'gel':
                    efficiencyFactor = 0.92;
                    break;
                case 'nickel-cadmium':
                    efficiencyFactor = 0.94;
                    break;
            }
            
            // Calculate required load bank size
            const requiredPower = initialPower / efficiencyFactor;
            const requiredResistance = batteryVoltage / testCurrent;
            
            // Calculate thermal considerations
            const heatDissipation = totalEnergy * 0.1; // 10% loss as heat
            const requiredCooling = heatDissipation * 0.5; // Cooling requirement
            
            // Standard load bank sizes (closest higher power)
            const standardSizes = [5, 10, 15, 20, 25, 30, 40, 50, 60, 75, 100, 150, 200, 300];
            const selectedLoadBank = standardSizes.find(size => size >= requiredPower) || 300;
            
            return {
                testCurrent: testCurrent,
                initialPower: initialPower,
                averagePower: averagePower,
                totalEnergy: totalEnergy,
                efficiencyFactor: efficiencyFactor,
                requiredPower: requiredPower,
                requiredResistance: requiredResistance,
                heatDissipation: heatDissipation,
                requiredCooling: requiredCooling,
                selectedLoadBank: selectedLoadBank,
                batteryVoltage: batteryVoltage,
                batteryCapacity: batteryCapacity,
                testCurrentPercent: testCurrentPercent,
                testDuration: testDuration,
                batteryType: batteryType
            };
        }

        document.getElementById('battery-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const batteryVoltage = parseFloat(document.getElementById('battery-voltage').value);
            const batteryCapacity = parseFloat(document.getElementById('battery-capacity').value);
            const testCurrentPercent = parseFloat(document.getElementById('test-current').value);
            const testDuration = parseFloat(document.getElementById('test-duration').value);
            const batteryType = document.getElementById('battery-type').value;
            const endTestVoltage = parseFloat(document.getElementById('test-voltage').value);

            if (isNaN(batteryVoltage) || isNaN(batteryCapacity) || isNaN(testCurrentPercent) || 
                isNaN(testDuration) || isNaN(endTestVoltage)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const result = calculateLoadBankSizing(batteryVoltage, batteryCapacity, testCurrentPercent, testDuration, batteryType, endTestVoltage);
            
            const batteryText = {
                'lead-acid': 'Lead Acid',
                'lithium': 'Lithium Ion',
                'nickel-cadmium': 'Nickel Cadmium',
                'gel': 'Gel Cell'
            };
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Battery Voltage:</strong><br>${result.batteryVoltage} V
                </div>
                <div class="result-item">
                    <strong>Battery Capacity:</strong><br>${result.batteryCapacity} Ah
                </div>
                <div class="result-item">
                    <strong>Battery Type:</strong><br>${batteryText[batteryType]}
                </div>
                <div class="result-item">
                    <strong>Test Current:</strong><br>${result.testCurrent.toFixed(1)} A (${result.testCurrentPercent}%)
                </div>
                <div class="result-item">
                    <strong>Test Duration:</strong><br>${result.testDuration} hours
                </div>
                <div class="result-item">
                    <strong>Initial Power:</strong><br>${result.initialPower.toFixed(1)} kW
                </div>
                <div class="result-item">
                    <strong>Average Power:</strong><br>${result.averagePower.toFixed(1)} kW
                </div>
                <div class="result-item">
                    <strong>Total Energy:</strong><br>${result.totalEnergy.toFixed(1)} kWh
                </div>
                <div class="result-item">
                    <strong>Efficiency Factor:</strong><br>${(result.efficiencyFactor * 100).toFixed(0)}%
                </div>
                <div class="result-item">
                    <strong>Required Load Bank:</strong><br>${result.selectedLoadBank} kW
                </div>
                <div class="result-item">
                    <strong>Required Resistance:</strong><br>${result.requiredResistance.toFixed(1)} Ω
                </div>
                <div class="result-item">
                    <strong>Heat Dissipation:</strong><br>${result.heatDissipation.toFixed(1)} kW
                </div>
                <div class="result-item">
                    <strong>Cooling Required:</strong><br>${result.requiredCooling.toFixed(1)} kW
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Load Bank Sizing', `${batteryVoltage}V ${batteryCapacity}Ah → ${result.selectedLoadBank}kW load bank`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentLoadBankCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentLoadBankCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
