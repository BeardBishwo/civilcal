<?php
// modules/electrical/voltage-drop/voltage-drop-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voltage Drop Wire Sizing Calculator - AEC Toolkit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ffffff;
            --secondary: #ffffff;
            --accent: #ffffff;
            --dark: #000000;
            --light: #ffffff;
            --glass: rgba(255, 255, 255, 0.05);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            --yellow: #ffffff;
        }

        body {
            background: linear-gradient(135deg, #000000, #000000, #000000);
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
            border-color: #ffffff;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }

        .btn-calculate {
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, #ffffff, #ffffff);
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

        .result-item.warning {
            background: rgba(255, 193, 7, 0.3);
        }

        .result-item.danger {
            background: rgba(244, 67, 54, 0.3);
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: #ffffff;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .voltage-note {
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
            <h1><i class="fas fa-calculator me-2"></i>Voltage Drop Wire Sizing</h1>
            <form id="sizing-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="current"><i class="fas fa-bolt me-2"></i>Load Current (A)</label>
                            <input type="number" id="current" class="form-control" step="0.1" min="0.1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="distance"><i class="fas fa-route me-2"></i>Distance (feet)</label>
                            <input type="number" id="distance" class="form-control" step="1" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="voltage"><i class="fas fa-plug me-2"></i>System Voltage (V)</label>
                            <input type="number" id="voltage" class="form-control" step="1" value="120" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="max-drop"><i class="fas fa-percentage me-2"></i>Max Voltage Drop (%)</label>
                            <input type="number" id="max-drop" class="form-control" step="0.1" value="3.0" min="0.1" max="10" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="phase"><i class="fas fa-plug me-2"></i>Phase</label>
                            <select id="phase" class="form-control" required>
                                <option value="1">Single Phase</option>
                                <option value="3">Three Phase</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="material"><i class="fas fa-cog me-2"></i>Conductor Material</label>
                            <select id="material" class="form-control" required>
                                <option value="copper">Copper</option>
                                <option value="aluminum">Aluminum</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Wire Size</button>
            </form>
            
            <div class="voltage-note">
                <i class="fas fa-info-circle me-2"></i>
                Wire sizing based on voltage drop limits per NEC recommendations for optimal system performance.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Wire Sizing Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateVoltageDropSizing(current, distance, voltage, maxDropPercent, phase, material) {
            // Maximum allowable voltage drop
            const maxVoltageDrop = voltage * (maxDropPercent / 100);
            
            // K factor (12.9 for copper, 21.2 for aluminum)
            const kFactor = material === 'copper' ? 12.9 : 21.2;
            
            // Calculate required circular mils based on phase
            let requiredCM;
            if (phase === 1) {
                // Single phase: cmil = (2 × K × I × D) / VD
                requiredCM = (2 * kFactor * current * distance) / maxVoltageDrop;
            } else {
                // Three phase: cmil = (1.732 × K × I × D) / VD
                requiredCM = (1.732 * kFactor * current * distance) / maxVoltageDrop;
            }
            
            // Standard wire sizes in circular mils
            const wireSizes = {
                '14': 4107, '12': 6530, '10': 10380, '8': 16510, '6': 26240,
                '4': 41740, '2': 66360, '1': 83690, '1/0': 105600, '2/0': 133100,
                '3/0': 167800, '4/0': 211600, '250': 250000, '300': 300000,
                '350': 350000, '400': 400000, '500': 500000, '600': 600000
            };
            
            // Find appropriate wire size
            let recommendedSize = '14';
            let actualCM = 4107;
            
            for (const [size, cm] of Object.entries(wireSizes)) {
                if (cm >= requiredCM) {
                    recommendedSize = size;
                    actualCM = cm;
                    break;
                }
            }
            
            // Calculate actual voltage drop with selected wire
            const actualK = kFactor * (requiredCM / actualCM);
            let actualVoltageDrop;
            
            if (phase === 1) {
                actualVoltageDrop = (2 * actualK * current * distance) / 1000;
            } else {
                actualVoltageDrop = (1.732 * actualK * current * distance) / 1000;
            }
            
            const actualVoltageDropPercent = (actualVoltageDrop / voltage) * 100;
            
            // Assess result quality
            let assessment = 'Excellent';
            let assessmentClass = '';
            
            if (actualVoltageDropPercent > maxDropPercent) {
                assessment = 'Exceeds Limit';
                assessmentClass = 'danger';
            } else if (actualVoltageDropPercent > maxDropPercent * 0.8) {
                assessment = 'Good';
                assessmentClass = 'warning';
            }
            
            return {
                requiredCM: requiredCM,
                recommendedSize: recommendedSize,
                actualCM: actualCM,
                actualVoltageDrop: actualVoltageDrop,
                actualVoltageDropPercent: actualVoltageDropPercent,
                maxVoltageDrop: maxVoltageDrop,
                maxDropPercent: maxDropPercent,
                assessment: assessment,
                assessmentClass: assessmentClass,
                current: current,
                distance: distance,
                voltage: voltage,
                phase: phase,
                material: material
            };
        }

        document.getElementById('sizing-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const current = parseFloat(document.getElementById('current').value);
            const distance = parseFloat(document.getElementById('distance').value);
            const voltage = parseFloat(document.getElementById('voltage').value);
            const maxDropPercent = parseFloat(document.getElementById('max-drop').value);
            const phase = document.getElementById('phase').value;
            const material = document.getElementById('material').value;

            if (isNaN(current) || isNaN(distance) || isNaN(voltage) || isNaN(maxDropPercent)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const result = calculateVoltageDropSizing(current, distance, voltage, maxDropPercent, phase, material);
            
            const phaseText = phase === '1' ? 'Single Phase' : 'Three Phase';
            const materialText = material === 'copper' ? 'Copper' : 'Aluminum';
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Load Current:</strong><br>${result.current} A
                </div>
                <div class="result-item">
                    <strong>Distance:</strong><br>${result.distance} feet
                </div>
                <div class="result-item">
                    <strong>System Voltage:</strong><br>${result.voltage}V ${phaseText}
                </div>
                <div class="result-item">
                    <strong>Conductor Material:</strong><br>${materialText}
                </div>
                <div class="result-item">
                    <strong>Required Circular Mils:</strong><br>${result.requiredCM.toFixed(0)} CM
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Recommended Wire Size:</strong><br>${result.recommendedSize} AWG ${materialText}
                </div>
                <div class="result-item">
                    <strong>Max Voltage Drop:</strong><br>${result.maxVoltageDrop.toFixed(2)}V (${result.maxDropPercent}%)
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Actual Voltage Drop:</strong><br>${result.actualVoltageDrop.toFixed(2)}V (${result.actualVoltageDropPercent.toFixed(2)}%)
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Assessment:</strong><br>${result.assessment}
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('VD Wire Sizing', `${result.current}A, ${result.distance}ft → ${result.recommendedSize} AWG for ${result.maxDropPercent}% VD`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentVDSizingCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentVDSizingCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
