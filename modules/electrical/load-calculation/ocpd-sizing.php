<?php
// modules/electrical/overcurrent/ocpd-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCPD Sizing Calculator - AEC Toolkit</title>
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

        .ocpd-note {
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
            <h1><i class="fas fa-shield-alt me-2"></i>OCPD Sizing</h1>
            <form id="ocpd-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="load-type"><i class="fas fa-cog me-2"></i>Load Type</label>
                            <select id="load-type" class="form-control" required>
                                <option value="resistive">Resistive (Fixed Load)</option>
                                <option value="motor">Motor Load</option>
                                <option value="continuous">Continuous Load</option>
                                <option value="motor-continuous">Motor + Continuous</option>
                                <option value="fuse">Fuse Protection</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="load-current"><i class="fas fa-bolt me-2"></i>Load Current (A)</label>
                            <input type="number" id="load-current" class="form-control" step="0.1" min="0.1" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="wire-size"><i class="fas fa-wire me-2"></i>Wire Size (AWG)</label>
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
                            <label for="insulation"><i class="fas fa-layer-group me-2"></i>Insulation Rating</label>
                            <select id="insulation" class="form-control" required>
                                <option value="60">60°C (TW)</option>
                                <option value="75">75°C (THW, THWN)</option>
                                <option value="90">90°C (THHN, XHHW)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="ambient-temp"><i class="fas fa-thermometer-half me-2"></i>Ambient Temp (°F)</label>
                            <input type="number" id="ambient-temp" class="form-control" value="86" step="5" min="32" max="140" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="conductors"><i class="fas fa-list me-2"></i>Number of Conductors</label>
                            <input type="number" id="conductors" class="form-control" value="3" step="1" min="2" max="6" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate OCPD Size</button>
            </form>
            
            <div class="ocpd-note">
                <i class="fas fa-info-circle me-2"></i>
                Overcurrent protective device sizing per NEC 240.4. Must not exceed wire ampacity except for motor and welder applications.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>OCPD Sizing Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function getWireAmpacity(wireSize, insulation, ambientTemp, conductors) {
            // NEC Table 310.16 base ampacities for copper conductors
            const copperAmpacity = {
                '14': { '60': 15, '75': 20, '90': 25 },
                '12': { '60': 20, '75': 25, '90': 30 },
                '10': { '60': 30, '75': 35, '90': 40 },
                '8': { '60': 40, '50': 50, '90': 55 },
                '6': { '60': 55, '75': 65, '90': 75 },
                '4': { '60': 70, '75': 85, '90': 95 },
                '2': { '60': 95, '75': 115, '90': 130 },
                '1': { '60': 110, '75': 130, '90': 150 },
                '1/0': { '60': 125, '75': 150, '90': 170 },
                '2/0': { '60': 145, '75': 175, '90': 195 },
                '3/0': { '60': 165, '75': 200, '90': 225 },
                '4/0': { '60': 195, '75': 230, '90': 260 }
            };
            
            let ampacity = copperAmpacity[wireSize] ? copperAmpacity[wireSize][insulation] : 0;
            
            // Temperature correction factor (NEC Table 310.16)
            const tempFactors = {
                '60': [1.0, 0.94, 0.88, 0.82, 0.75, 0.67, 0.58, 0.47, 0.35, 0.22],
                '75': [1.0, 0.96, 0.91, 0.87, 0.82, 0.76, 0.69, 0.61, 0.50, 0.40],
                '90': [1.0, 0.97, 0.94, 0.90, 0.87, 0.83, 0.78, 0.72, 0.65, 0.58]
            };
            
            // Convert ambient temp to adjustment factor (for temps 86-140°F)
            const tempIndex = Math.floor((ambientTemp - 86) / 5);
            if (tempIndex >= 0 && tempIndex < tempFactors[insulation].length) {
                ampacity *= tempFactors[insulation][tempIndex];
            }
            
            // Adjust for number of conductors in raceway (NEC 310.15)
            if (conductors > 3) {
                const adjustmentFactor = 0.8; // 80% adjustment for 4-6 conductors
                ampacity *= adjustmentFactor;
            }
            
            return ampacity;
        }

        function calculateOCPDSize(loadType, loadCurrent, wireSize, insulation, ambientTemp, conductors) {
            const wireAmpacity = getWireAmpacity(wireSize, insulation, ambientTemp, conductors);
            
            let ocpdSize = 0;
            let calculation = '';
            
            switch (loadType) {
                case 'resistive':
                    ocpdSize = loadCurrent;
                    calculation = `OCPD = Load Current (${loadCurrent}A)`;
                    break;
                case 'motor':
                    ocpdSize = loadCurrent * 1.75; // 175% for motor circuits
                    calculation = `OCPD = Load × 175% (${loadCurrent}A × 1.75)`;
                    break;
                case 'continuous':
                    ocpdSize = loadCurrent * 1.25; // 125% for continuous loads
                    calculation = `OCPD = Load × 125% (${loadCurrent}A × 1.25)`;
                    break;
                case 'motor-continuous':
                    ocpdSize = Math.max(loadCurrent * 1.25, loadCurrent * 1.75);
                    calculation = `OCPD = Max(Load×125%, Load×175%)`;
                    break;
                case 'fuse':
                    ocpdSize = loadCurrent * 1.25;
                    calculation = `OCPD = Load × 125% (${loadCurrent}A × 1.25)`;
                    break;
            }
            
            // Find standard breaker size
            const standardSizes = [15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 110, 125, 150, 175, 200, 225, 250, 300, 350, 400, 450, 500, 600, 700, 800, 1000];
            let breakerSize = standardSizes.find(size => size >= ocpdSize) || 1000;
            
            // Check if breaker size is acceptable
            let status = 'acceptable';
            let statusClass = '';
            
            if (breakerSize > wireAmpacity) {
                status = 'exceeds wire capacity';
                statusClass = 'danger';
            } else if (breakerSize > wireAmpacity * 0.8) {
                status = 'marginal';
                statusClass = 'warning';
            }
            
            return {
                wireAmpacity: wireAmpacity,
                ocpdSize: ocpdSize,
                breakerSize: breakerSize,
                calculation: calculation,
                status: status,
                statusClass: statusClass,
                loadCurrent: loadCurrent,
                loadType: loadType
            };
        }

        document.getElementById('ocpd-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const loadType = document.getElementById('load-type').value;
            const loadCurrent = parseFloat(document.getElementById('load-current').value);
            const wireSize = document.getElementById('wire-size').value;
            const insulation = document.getElementById('insulation').value;
            const ambientTemp = parseFloat(document.getElementById('ambient-temp').value);
            const conductors = parseInt(document.getElementById('conductors').value);

            if (isNaN(loadCurrent) || isNaN(ambientTemp) || isNaN(conductors)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const result = calculateOCPDSize(loadType, loadCurrent, wireSize, insulation, ambientTemp, conductors);
            
            const loadTypeText = {
                'resistive': 'Resistive (Fixed)',
                'motor': 'Motor Load',
                'continuous': 'Continuous Load',
                'motor-continuous': 'Motor + Continuous',
                'fuse': 'Fuse Protection'
            };
            
            const insulationText = {
                '60': '60°C (TW)',
                '75': '75°C (THW, THWN)',
                '90': '90°C (THHN, XHHW)'
            };
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Load Type:</strong><br>${loadTypeText[loadType]}
                </div>
                <div class="result-item">
                    <strong>Load Current:</strong><br>${result.loadCurrent} A
                </div>
                <div class="result-item">
                    <strong>Wire Size:</strong><br>${wireSize} AWG
                </div>
                <div class="result-item">
                    <strong>Insulation Rating:</strong><br>${insulationText[insulation]}
                </div>
                <div class="result-item">
                    <strong>Ambient Temperature:</strong><br>${ambientTemp}°F
                </div>
                <div class="result-item">
                    <strong>Conductors:</strong><br>${conductors} in raceway
                </div>
                <div class="result-item">
                    <strong>Wire Ampacity:</strong><br>${result.wireAmpacity.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Calculated OCPD:</strong><br>${result.ocpdSize.toFixed(1)} A
                </div>
                <div class="result-item ${result.statusClass}">
                    <strong>Standard OCPD:</strong><br>${result.breakerSize} A
                </div>
                <div class="result-item ${result.statusClass}">
                    <strong>Status:</strong><br>${result.status}
                </div>
                <div class="result-item">
                    <strong>Calculation:</strong><br>${result.calculation}
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('OCPD Sizing', `${loadTypeText[loadType]} ${loadCurrent}A → ${result.breakerSize}A breaker`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentOCPDCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentOCPDCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
