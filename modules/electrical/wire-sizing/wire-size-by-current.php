<?php
// modules/electrical/wire-sizing/wire-size-by-current.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wire Size by Current Calculator - AEC Toolkit</title>
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
            display: none; /* Hidden by default */
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-wire me-2"></i>Wire Size by Current</h1>
            <form id="wire-size-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="current"><i class="fas fa-bolt me-2"></i>Load Current (Amps)</label>
                            <input type="number" id="current" class="form-control" step="0.1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="voltage"><i class="fas fa-plug me-2"></i>Voltage</label>
                            <select id="voltage" class="form-control" required>
                                <option value="120">120V</option>
                                <option value="208">208V</option>
                                <option value="240">240V</option>
                                <option value="277">277V</option>
                                <option value="480">480V</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="phase"><i class="fas fa-wave-square me-2"></i>Phase</label>
                            <select id="phase" class="form-control" required>
                                <option value="1">Single Phase</option>
                                <option value="3">Three Phase</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="insulation"><i class="fas fa-layer-group me-2"></i>Insulation Type</label>
                            <select id="insulation" class="form-control" required>
                                <option value="THHN">THHN (90°C)</option>
                                <option value="THW">THW (75°C)</option>
                                <option value="TW">TW (60°C)</option>
                                <option value="UF">UF (60°C)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="ambient-temp"><i class="fas fa-thermometer-half me-2"></i>Ambient Temperature (°C)</label>
                            <input type="number" id="ambient-temp" class="form-control" value="30" step="1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="material"><i class="fas fa-cog me-2"></i>Wire Material</label>
                            <select id="material" class="form-control" required>
                                <option value="copper">Copper</option>
                                <option value="aluminum">Aluminum</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="distance"><i class="fas fa-route me-2"></i>Distance (feet) - for voltage drop calculation</label>
                    <input type="number" id="distance" class="form-control" value="100" step="1">
                </div>

                <button type="submit" class="btn-calculate">Calculate Wire Size</button>
            </form>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Calculation Results</h3>
                <div class="result-grid" id="result-grid">
                    <!-- Results will be populated here -->
                </div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function getWireResistance(size, material) {
            // Resistance in ohms per 1000 feet
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

        function getBaseAmpacity(size, material) {
            // Base ampacity for copper wire at 30°C
            const copperAmpacity = {
                '14': 20, '12': 25, '10': 35, '8': 50, '6': 65,
                '4': 85, '2': 115, '1': 130, '1/0': 150, '2/0': 175,
                '3/0': 200, '4/0': 230
            };
            
            // Aluminum is typically one size larger for same ampacity
            const aluminumAmpacity = {
                '12': 20, '10': 25, '8': 35, '6': 50, '4': 65,
                '2': 85, '1': 100, '1/0': 115, '2/0': 135, '3/0': 155,
                '4/0': 180, '250': 205, '300': 230, '350': 250
            };
            
            if (material === 'aluminum') {
                return aluminumAmpacity[size] || 20;
            } else {
                return copperAmpacity[size] || 20;
            }
        }

        function getTemperatureCorrection(insulation, ambientTemp) {
            if (ambientTemp <= 30) return 1.0;
            
            // Temperature correction factors for different insulation types
            const corrections = {
                'THHN': { '40': 0.91, '50': 0.82, '60': 0.71 },
                'THW': { '40': 0.88, '50': 0.78, '60': 0.67 },
                'TW': { '40': 0.82, '50': 0.72, '60': 0.58 },
                'UF': { '40': 0.82, '50': 0.72, '60': 0.58 }
            };
            
            const insulationCorrections = corrections[insulation] || corrections['THHN'];
            return insulationCorrections[ambientTemp] || 0.91;
        }

        document.getElementById('wire-size-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const current = parseFloat(document.getElementById('current').value);
            const voltage = parseFloat(document.getElementById('voltage').value);
            const phase = parseInt(document.getElementById('phase').value);
            const insulation = document.getElementById('insulation').value;
            const ambientTemp = parseFloat(document.getElementById('ambient-temp').value);
            const material = document.getElementById('material').value;
            const distance = parseFloat(document.getElementById('distance').value);

            if (isNaN(current) || isNaN(voltage) || isNaN(ambientTemp)) {
                alert('Please enter valid numbers.');
                return;
            }
            
            // Apply temperature correction
            const tempCorrection = getTemperatureCorrection(insulation, ambientTemp);
            
            // Find appropriate wire size
            const wireSizes = ['14', '12', '10', '8', '6', '4', '2', '1', '1/0', '2/0', '3/0', '4/0'];
            let recommendedSize = '14';
            let wireAmpacity = 0;
            
            for (const size of wireSizes) {
                const baseAmpacity = getBaseAmpacity(size, material);
                const correctedAmpacity = baseAmpacity * tempCorrection;
                
                if (correctedAmpacity >= current) {
                    recommendedSize = size;
                    wireAmpacity = correctedAmpacity;
                    break;
                }
            }
            
            // Calculate voltage drop for recommended size
            const resistance = getWireResistance(recommendedSize, material);
            let voltageDrop = 0;
            
            if (distance && distance > 0) {
                if (phase === 1) {
                    voltageDrop = (2 * current * resistance * distance) / 1000;
                } else {
                    voltageDrop = (1.732 * current * resistance * distance) / 1000;
                }
                
                const voltageDropPercent = (voltageDrop / voltage) * 100;
                
                // Check if voltage drop is acceptable
                let vdAssessment = 'Good';
                if (voltageDropPercent > 5) vdAssessment = 'Poor - consider larger wire';
                else if (voltageDropPercent > 3) vdAssessment = 'Fair';
                
                var resultHtml = `
                    <div class="result-item">
                        <strong>Load Current:</strong><br>${current.toFixed(1)} A
                    </div>
                    <div class="result-item">
                        <strong>Recommended Wire Size:</strong><br>${recommendedSize} AWG ${material}
                    </div>
                    <div class="result-item">
                        <strong>Wire Ampacity:</strong><br>${wireAmpacity.toFixed(1)} A
                    </div>
                    <div class="result-item">
                        <strong>Voltage Drop:</strong><br>${voltageDrop.toFixed(2)}V (${voltageDropPercent.toFixed(2)}%)
                    </div>
                    <div class="result-item">
                        <strong>VD Assessment:</strong><br>${vdAssessment}
                    </div>
                    <div class="result-item">
                        <strong>Power Factor:</strong><br>125% (Continuous Load)
                    </div>
                `;
            } else {
                var resultHtml = `
                    <div class="result-item">
                        <strong>Load Current:</strong><br>${current.toFixed(1)} A
                    </div>
                    <div class="result-item">
                        <strong>Recommended Wire Size:</strong><br>${recommendedSize} AWG ${material}
                    </div>
                    <div class="result-item">
                        <strong>Wire Ampacity:</strong><br>${wireAmpacity.toFixed(1)} A
                    </div>
                    <div class="result-item">
                        <strong>Temperature Correction:</strong><br>${(tempCorrection * 100).toFixed(0)}%
                    </div>
                    <div class="result-item">
                        <strong>Insulation Type:</strong><br>${insulation}
                    </div>
                    <div class="result-item">
                        <strong>Ambient Temperature:</strong><br>${ambientTemp}°C
                    </div>
                `;
            }
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to localStorage for recent calculations
            saveCalculation('Wire Sizing', `${current}A → ${recommendedSize} AWG ${material}`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentWireCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            // Keep only last 5 calculations
            recent = recent.slice(0, 5);
            localStorage.setItem('recentWireCalculations', JSON.stringify(recent));
        }
    </script>
</body>
</html>
