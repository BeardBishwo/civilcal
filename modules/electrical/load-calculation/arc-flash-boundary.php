<?php
// modules/electrical/safety/arc-flash-boundary.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arc Flash Boundary Calculator - AEC Toolkit</title>
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

        .result-item.danger {
            background: rgba(244, 67, 54, 0.3);
        }

        .result-item.warning {
            background: rgba(255, 193, 7, 0.3);
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

        .flash-note {
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
            <h1><i class="fas fa-bolt me-2"></i>Arc Flash Boundary</h1>
            <form id="flash-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="available-fault"><i class="fas fa-exclamation-triangle me-2"></i>Available Fault Current (kA)</label>
                            <input type="number" id="available-fault" class="form-control" step="0.1" min="0.1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="clearing-time"><i class="fas fa-clock me-2"></i>Fault Clearing Time (sec)</label>
                            <input type="number" id="clearing-time" class="form-control" step="0.01" min="0.01" value="0.5" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="working-distance"><i class="fas fa-ruler me-2"></i>Working Distance (inches)</label>
                            <input type="number" id="working-distance" class="form-control" step="1" min="12" value="18" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="voltage-level"><i class="fas fa-plug me-2"></i>Voltage Level</label>
                            <select id="voltage-level" class="form-control" required>
                                <option value="600">600V and below</option>
                                <option value="2400">2.4kV</option>
                                <option value="4160">4.16kV</option>
                                <option value="13800">13.8kV</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="arcing-current"><i class="fas fa-bolt me-2"></i>Arcing Current (kA)</label>
                            <input type="number" id="arcing-current" class="form-control" step="0.1" min="0.1" value="15.6" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="equipment-type"><i class="fas fa-cog me-2"></i>Equipment Type</label>
                            <select id="equipment-type" class="form-control" required>
                                <option value="panel">Panel/Switchboard</option>
                                <option value="switchgear">Switchgear</option>
                                <option value="mcc">Motor Control Center</option>
                                <option value="transformer">Transformer</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Arc Flash</button>
            </form>
            
            <div class="flash-note">
                <i class="fas fa-info-circle me-2"></i>
                Arc flash boundary calculations per IEEE 1584. Provides working distance and incident energy calculations for safe work practices.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Arc Flash Boundary Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateArcFlashBoundary(faultCurrentKA, clearingTime, workingDistance, voltageLevel, arcingCurrent, equipmentType) {
            // Calculate incident energy (IEEE 1584 simplified method)
            // IE = 2.142 × (t / D^1.959)^0.0867 × (F)
            const IE = 2.142 * Math.pow(clearingTime / Math.pow(workingDistance, 1.959), 0.0867) * (arcingCurrent / faultCurrentKA);
            
            // Arc flash boundary distance (where incident energy = 5 cal/cm²)
            // DB = 2.74 × (IE)^0.5625
            const flashBoundary = 2.74 * Math.pow(5, 0.5625) * (1 + Math.log10(IE));
            
            // Determine PPE category
            let ppeCategory = '';
            let ppeClass = '';
            if (IE <= 4) {
                ppeCategory = 'Category 1 - 4 cal/cm²';
                ppeClass = 'warning';
            } else if (IE <= 8) {
                ppeCategory = 'Category 2 - 8 cal/cm²';
                ppeClass = 'warning';
            } else if (IE <= 25) {
                ppeCategory = 'Category 3 - 25 cal/cm²';
                ppeClass = 'danger';
            } else {
                ppeCategory = 'Category 4 - 40+ cal/cm²';
                ppeClass = 'danger';
            }
            
            // Calculate shock hazard
            const shockHazard = {
                '600': '42" (to head) / 48" (to back)',
                '2400': '60" (to head) / 72" (to back)',
                '4160': '72" (to head) / 84" (to back)',
                '13800': '120" (to head) / 144" (to back)'
            };
            
            return {
                incidentEnergy: IE,
                flashBoundary: flashBoundary,
                ppeCategory: ppeCategory,
                ppeClass: ppeClass,
                shockHazard: shockHazard[voltageLevel.toString()],
                faultCurrentKA: faultCurrentKA,
                clearingTime: clearingTime,
                workingDistance: workingDistance,
                voltageLevel: voltageLevel,
                arcingCurrent: arcingCurrent,
                equipmentType: equipmentType
            };
        }

        document.getElementById('flash-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const faultCurrentKA = parseFloat(document.getElementById('available-fault').value);
            const clearingTime = parseFloat(document.getElementById('clearing-time').value);
            const workingDistance = parseFloat(document.getElementById('working-distance').value);
            const voltageLevel = parseFloat(document.getElementById('voltage-level').value);
            const arcingCurrent = parseFloat(document.getElementById('arcing-current').value);
            const equipmentType = document.getElementById('equipment-type').value;

            if (isNaN(faultCurrentKA) || isNaN(clearingTime) || isNaN(workingDistance) || 
                isNaN(voltageLevel) || isNaN(arcingCurrent)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const result = calculateArcFlashBoundary(faultCurrentKA, clearingTime, workingDistance, voltageLevel, arcingCurrent, equipmentType);
            
            const equipmentText = {
                'panel': 'Panel/Switchboard',
                'switchgear': 'Switchgear',
                'mcc': 'Motor Control Center',
                'transformer': 'Transformer'
            };
            
            const voltageText = {
                '600': '600V and below',
                '2400': '2.4kV',
                '4160': '4.16kV',
                '13800': '13.8kV'
            };
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Available Fault Current:</strong><br>${result.faultCurrentKA} kA
                </div>
                <div class="result-item">
                    <strong>Clearing Time:</strong><br>${result.clearingTime} sec
                </div>
                <div class="result-item">
                    <strong>Arcing Current:</strong><br>${result.arcingCurrent} kA
                </div>
                <div class="result-item">
                    <strong>Working Distance:</strong><br>${result.workingDistance} inches
                </div>
                <div class="result-item">
                    <strong>Voltage Level:</strong><br>${voltageText[voltageLevel.toString()]}
                </div>
                <div class="result-item">
                    <strong>Equipment Type:</strong><br>${equipmentText[equipmentType]}
                </div>
                <div class="result-item">
                    <strong>Incident Energy:</strong><br>${result.incidentEnergy.toFixed(2)} cal/cm²
                </div>
                <div class="result-item">
                    <strong>Arc Flash Boundary:</strong><br>${result.flashBoundary.toFixed(1)} inches
                </div>
                <div class="result-item ${result.ppeClass}">
                    <strong>Required PPE:</strong><br>${result.ppeCategory}
                </div>
                <div class="result-item">
                    <strong>Shock Hazard Distance:</strong><br>${result.shockHazard}
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Arc Flash Boundary', `${faultCurrentKA}kA → ${result.incidentEnergy.toFixed(2)}cal/cm², ${result.flashBoundary.toFixed(1)}" boundary`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentArcFlashCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentArcFlashCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
