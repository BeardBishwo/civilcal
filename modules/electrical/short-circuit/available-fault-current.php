<?php
// modules/electrical/short-circuit/available-fault-current.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Fault Current Calculator - AEC Toolkit</title>
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

        .fault-note {
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
            <h1><i class="fas fa-bolt me-2"></i>Available Fault Current</h1>
            <form id="fault-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="xfmr-kva"><i class="fas fa-transformer me-2"></i>Transformer kVA</label>
                            <input type="number" id="xfmr-kva" class="form-control" step="0.1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="secondary-voltage"><i class="fas fa-plug me-2"></i>Secondary Voltage</label>
                            <select id="secondary-voltage" class="form-control" required>
                                <option value="208">208V</option>
                                <option value="240">240V</option>
                                <option value="480">480V</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="impedance"><i class="fas fa-percentage me-2"></i>% Impedance</label>
                            <input type="number" id="impedance" class="form-control" value="5.75" step="0.01" min="1" max="20" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="phase"><i class="fas fa-wave-square me-2"></i>Phase</label>
                            <select id="phase" class="form-control" required>
                                <option value="3">Three Phase</option>
                                <option value="1">Single Phase</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="temperature-rise"><i class="fas fa-thermometer-half me-2"></i>Temperature Rise (°C)</label>
                            <input type="number" id="temperature-rise" class="form-control" value="80" step="5" min="55" max="150" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="utility-impedance"><i class="fas fa-database me-2"></i>Utility Impedance (%)</label>
                            <input type="number" id="utility-impedance" class="form-control" value="1" step="0.1" min="0" max="10" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Fault Current</button>
            </form>
            
            <div class="fault-note">
                <i class="fas fa-info-circle me-2"></i>
                Available fault current is calculated based on transformer impedance and capacity. Required for proper equipment coordination and safety.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Fault Current Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateFaultCurrent(kva, voltage, impedance, phase, tempRise, utilityImpedance) {
            // Full load current calculation
            let flc = 0;
            if (phase === 1) {
                flc = (kva * 1000) / voltage;
            } else {
                flc = (kva * 1000) / (1.732 * voltage);
            }
            
            // Available fault current calculation
            let afc = flc / (impedance / 100);
            
            // Add utility impedance contribution
            if (utilityImpedance > 0) {
                const utilityAFC = flc / (utilityImpedance / 100);
                afc = Math.sqrt(Math.pow(afc, 2) + Math.pow(utilityAFC, 2));
            }
            
            // Convert to kA for readability
            const afcKA = afc / 1000;
            
            // Equipment ratings needed
            let equipmentRating = '10kA';
            let ratingClass = '';
            
            if (afcKA > 100) {
                equipmentRating = '200kA';
                ratingClass = 'danger';
            } else if (afcKA > 65) {
                equipmentRating = '100kA';
                ratingClass = 'danger';
            } else if (afcKA > 42) {
                equipmentRating = '65kA';
                ratingClass = 'warning';
            } else if (afcKA > 22) {
                equipmentRating = '42kA';
                ratingClass = 'warning';
            } else if (afcKA > 18) {
                equipmentRating = '22kA';
                ratingClass = '';
            } else if (afcKA > 10) {
                equipmentRating = '18kA';
                ratingClass = '';
            }
            
            // Temperature correction
            const tempCorrection = 1 + ((tempRise - 80) * 0.003);
            const correctedAFC = afc * tempCorrection;
            
            return {
                flc: flc,
                afc: afc,
                afcKA: afcKA,
                correctedAFC: correctedAFC,
                equipmentRating: equipmentRating,
                ratingClass: ratingClass,
                impedance: impedance,
                utilityImpedance: utilityImpedance,
                tempRise: tempRise
            };
        }

        document.getElementById('fault-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const kva = parseFloat(document.getElementById('xfmr-kva').value);
            const voltage = parseFloat(document.getElementById('secondary-voltage').value);
            const impedance = parseFloat(document.getElementById('impedance').value);
            const phase = parseInt(document.getElementById('phase').value);
            const tempRise = parseFloat(document.getElementById('temperature-rise').value);
            const utilityImpedance = parseFloat(document.getElementById('utility-impedance').value);

            if (isNaN(kva) || isNaN(voltage) || isNaN(impedance) || 
                isNaN(phase) || isNaN(tempRise) || isNaN(utilityImpedance)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const result = calculateFaultCurrent(kva, voltage, impedance, phase, tempRise, utilityImpedance);
            
            const phaseText = phase === 3 ? 'Three Phase' : 'Single Phase';
            const voltageText = voltage + 'V';
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Transformer:</strong><br>${kva} kVA
                </div>
                <div class="result-item">
                    <strong>Secondary:</strong><br>${voltageText} ${phaseText}
                </div>
                <div class="result-item">
                    <strong>% Impedance:</strong><br>${result.impedance}%
                </div>
                <div class="result-item">
                    <strong>Temperature Rise:</strong><br>${result.tempRise}°C
                </div>
                <div class="result-item">
                    <strong>Utility Impedance:</strong><br>${result.utilityImpedance}%
                </div>
                <div class="result-item">
                    <strong>Full Load Current:</strong><br>${result.flc.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Available Fault Current:</strong><br>${result.afc.toFixed(0)} A
                </div>
                <div class="result-item ${result.ratingClass}">
                    <strong>Available Fault Current:</strong><br>${result.afcKA.toFixed(1)} kA
                </div>
                <div class="result-item">
                    <strong>Corrected AFC (Temp):</strong><br>${result.correctedAFC.toFixed(0)} A
                </div>
                <div class="result-item ${result.ratingClass}">
                    <strong>Min. Equipment Rating:</strong><br>${result.equipmentRating}
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Fault Current', `${kva}kVA → ${result.afcKA.toFixed(1)}kA AFC`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentFaultCurrentCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentFaultCurrentCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
