<?php
// modules/electrical/wire-sizing/wire-ampacity.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wire Ampacity Calculator - AEC Toolkit</title>
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

        .ampacity-table {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
        }

        .table-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            padding: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table-row:first-child {
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
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-bolt me-2"></i>Wire Ampacity Calculator</h1>
            <form id="ampacity-form">
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
                            <label for="insulation-type"><i class="fas fa-layer-group me-2"></i>Insulation Type</label>
                            <select id="insulation-type" class="form-control" required>
                                <option value="THHN">THHN (90°C)</option>
                                <option value="THW">THW (75°C)</option>
                                <option value="TW">TW (60°C)</option>
                                <option value="XHHW">XHHW (90°C)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="wire-material"><i class="fas fa-cog me-2"></i>Wire Material</label>
                            <select id="wire-material" class="form-control" required>
                                <option value="copper">Copper</option>
                                <option value="aluminum">Aluminum</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="conductor-count"><i class="fas fa-list-ol me-2"></i>Current-Carrying Conductors</label>
                            <select id="conductor-count" class="form-control" required>
                                <option value="1">1 conductor</option>
                                <option value="2">2 conductors</option>
                                <option value="3">3 conductors</option>
                                <option value="4">4 conductors</option>
                                <option value="5">5 conductors</option>
                                <option value="6">6 conductors</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="conduit-type"><i class="fas fa-pipe me-2"></i>Conduit Type</label>
                            <select id="conduit-type" class="form-control" required>
                                <option value="free-air">Free Air</option>
                                <option value="pvc">PVC</option>
                                <option value="emt">EMT</option>
                                <option value="rigid">Rigid Metal</option>
                                <option value="flex">Flexible</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="ambient-temp"><i class="fas fa-thermometer-half me-2"></i>Ambient Temperature (°C)</label>
                            <input type="number" id="ambient-temp" class="form-control" value="30" step="1" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Ampacity</button>
            </form>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Ampacity Calculation Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>

            <div class="ampacity-table" id="ampacity-table" style="display: none;">
                <h3><i class="fas fa-table me-2"></i>Wire Ampacity Reference Table</h3>
                <div class="table-row">
                    <div>Wire Size</div>
                    <div>THHN (90°C)</div>
                    <div>THW (75°C)</div>
                </div>
                <div id="ampacity-table-content"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function getBaseAmpacity(size, material, insulation) {
            // Base ampacity tables (NEC Table 310.16)
            const copperAmpacity = {
                'THHN': {
                    '14': 25, '12': 30, '10': 40, '8': 55, '6': 75,
                    '4': 95, '2': 130, '1': 150, '1/0': 170, '2/0': 195,
                    '3/0': 225, '4/0': 260
                },
                'THW': {
                    '14': 20, '12': 25, '10': 35, '8': 50, '6': 65,
                    '4': 85, '2': 115, '1': 130, '1/0': 150, '2/0': 175,
                    '3/0': 200, '4/0': 230
                },
                'TW': {
                    '14': 15, '12': 20, '10': 30, '8': 40, '6': 55,
                    '4': 70, '2': 95, '1': 110, '1/0': 125, '2/0': 145,
                    '3/0': 165, '4/0': 190
                }
            };

            const aluminumAmpacity = {
                'THHN': {
                    '12': 25, '10': 30, '8': 40, '6': 55, '4': 75,
                    '2': 95, '1': 110, '1/0': 125, '2/0': 145, '3/0': 165,
                    '4/0': 190, '250': 205
                },
                'THW': {
                    '12': 20, '10': 25, '8': 35, '6': 50, '4': 65,
                    '2': 85, '1': 100, '1/0': 115, '2/0': 135, '3/0': 155,
                    '4/0': 180, '250': 205
                },
                'TW': {
                    '12': 15, '10': 25, '8': 30, '6': 40, '4': 55,
                    '2': 75, '1': 85, '1/0': 100, '2/0': 115, '3/0': 130,
                    '4/0': 150, '250': 170
                }
            };

            const ampacityTables = {
                'copper': copperAmpacity,
                'aluminum': aluminumAmpacity
            };

            return ampacityTables[material][insulation][size] || 0;
        }

        function getTemperatureCorrection(insulation, ambientTemp) {
            if (ambientTemp <= 30) return 1.0;
            
            // Temperature correction factors (NEC Table 310.15(B)(1))
            const corrections = {
                'THHN': { '40': 0.91, '45': 0.87, '50': 0.82, '55': 0.76, '60': 0.71 },
                'THW': { '40': 0.88, '45': 0.84, '50': 0.78, '55': 0.72, '60': 0.67 },
                'TW': { '40': 0.82, '45': 0.77, '50': 0.72, '55': 0.66, '60': 0.58 }
            };
            
            const tempRounded = Math.round(ambientTemp / 5) * 5;
            return corrections[insulation][tempRounded] || 1.0;
        }

        function getConduitAdjustment(conduitType, conductorCount) {
            // Adjustment factors for more than 3 current-carrying conductors (NEC 310.15(C)(1))
            const countFactors = {
                '4-6': 0.80,
                '7-9': 0.70,
                '10-20': 0.50,
                '21-30': 0.45,
                '31-40': 0.40,
                '41+': 0.35
            };

            if (conductorCount <= 3) return 1.0;
            if (conductorCount <= 6) return countFactors['4-6'];
            if (conductorCount <= 9) return countFactors['7-9'];
            if (conductorCount <= 20) return countFactors['10-20'];
            if (conductorCount <= 30) return countFactors['21-30'];
            if (conductorCount <= 40) return countFactors['31-40'];
            return countFactors['41+'];
        }

        document.getElementById('ampacity-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const wireSize = document.getElementById('wire-size').value;
            const insulationType = document.getElementById('insulation-type').value;
            const wireMaterial = document.getElementById('wire-material').value;
            const conductorCount = parseInt(document.getElementById('conductor-count').value);
            const conduitType = document.getElementById('conduit-type').value;
            const ambientTemp = parseFloat(document.getElementById('ambient-temp').value);

            if (isNaN(ambientTemp)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            // Get base ampacity
            const baseAmpacity = getBaseAmpacity(wireSize, wireMaterial, insulationType);
            
            // Apply corrections
            const tempCorrection = getTemperatureCorrection(insulationType, ambientTemp);
            const conduitAdjustment = getConduitAdjustment(conduitType, conductorCount);
            
            // Calculate final ampacity
            const finalAmpacity = baseAmpacity * tempCorrection * conduitAdjustment;
            
            // Calculate maximum overcurrent protection
            const maxOCP = Math.ceil(finalAmpacity / 10) * 10;
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Wire Size:</strong><br>${wireSize} AWG
                </div>
                <div class="result-item">
                    <strong>Wire Material:</strong><br>${wireMaterial}
                </div>
                <div class="result-item">
                    <strong>Insulation:</strong><br>${insulationType}
                </div>
                <div class="result-item">
                    <strong>Base Ampacity:</strong><br>${baseAmpacity} A
                </div>
                <div class="result-item">
                    <strong>Temperature Correction:</strong><br>${(tempCorrection * 100).toFixed(0)}%
                </div>
                <div class="result-item">
                    <strong>Conduit Adjustment:</strong><br>${(conduitAdjustment * 100).toFixed(0)}%
                </div>
                <div class="result-item">
                    <strong>Final Ampacity:</strong><br>${finalAmpacity.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Max OCPD:</strong><br>${maxOCP} A
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Show ampacity reference table
            showAmpacityTable();
            
            // Save calculation
            saveCalculation('Wire Ampacity', `${wireSize} AWG ${wireMaterial} → ${finalAmpacity.toFixed(1)}A`);
        });

        function showAmpacityTable() {
            const tableContent = document.getElementById('ampacity-table-content');
            const sizes = ['14', '12', '10', '8', '6', '4', '2', '1', '1/0', '2/0', '3/0', '4/0'];
            
            let html = '';
            sizes.forEach(size => {
                const thhn = getBaseAmpacity(size, 'copper', 'THHN');
                const thw = getBaseAmpacity(size, 'copper', 'THW');
                html += `
                    <div class="table-row">
                        <div>${size} AWG</div>
                        <div>${thhn}A</div>
                        <div>${thw}A</div>
                    </div>
                `;
            });
            
            tableContent.innerHTML = html;
            document.getElementById('ampacity-table').style.display = 'block';
        }

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentAmpacityCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentAmpacityCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
