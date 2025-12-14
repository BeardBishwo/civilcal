<?php
// modules/electrical/load-calculation/panel-schedule.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Schedule Calculator - AEC Toolkit</title>
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
            max-width: 1000px;
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

        .panel-schedule {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
            overflow-x: auto;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.5rem;
            text-align: center;
        }

        .schedule-table th {
            background: rgba(255, 255, 255, 0.1);
            font-weight: bold;
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

        .panel-note {
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
            <h1><i class="fas fa-th me-2"></i>Panel Schedule</h1>
            <form id="panel-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="lighting-load"><i class="fas fa-lightbulb me-2"></i>Lighting Load (VA)</label>
                            <input type="number" id="lighting-load" class="form-control" step="100" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="receptacle-load"><i class="fas fa-plug me-2"></i>Receptacle Load (VA)</label>
                            <input type="number" id="receptacle-load" class="form-control" step="100" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="appliance-load"><i class="fas fa-blender me-2"></i>Appliance Load (VA)</label>
                            <input type="number" id="appliance-load" class="form-control" step="100" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="motor-load"><i class="fas fa-cog me-2"></i>Motor Load (VA)</label>
                            <input type="number" id="motor-load" class="form-control" step="100" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="voltage"><i class="fas fa-plug me-2"></i>Panel Voltage</label>
                            <select id="voltage" class="form-control" required>
                                <option value="120">120V</option>
                                <option value="208">208V</option>
                                <option value="240">240V</option>
                                <option value="480">480V</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="phase"><i class="fas fa-wave-square me-2"></i>Phase</label>
                            <select id="phase" class="form-control" required>
                                <option value="1">Single Phase</option>
                                <option value="3">Three Phase</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="diversity-factor"><i class="fas fa-percentage me-2"></i>Diversity Factor (%)</label>
                            <input type="number" id="diversity-factor" class="form-control" value="80" step="5" min="50" max="100" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="continuous-load"><i class="fas fa-clock me-2"></i>Continuous Load (%)</label>
                            <input type="number" id="continuous-load" class="form-control" value="125" step="5" min="100" max="150" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Panel Schedule</button>
            </form>
            
            <div class="panel-note">
                <i class="fas fa-info-circle me-2"></i>
                Panel schedule calculates total load including diversity and continuous load factors per NEC requirements.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Panel Schedule Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>

            <div class="panel-schedule" id="panel-schedule" style="display: none;">
                <h3><i class="fas fa-table me-2"></i>Panel Schedule Example</h3>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Circuit</th>
                            <th>Load Type</th>
                            <th>VA</th>
                            <th>A</th>
                            <th>OCPD</th>
                        </tr>
                    </thead>
                    <tbody id="schedule-tbody">
                        <!-- Schedule will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculatePanelSchedule(lighting, receptacles, appliances, motors, voltage, phase, diversity, continuousPercent) {
            // Total load calculation
            const totalLoad = lighting + receptacles + appliances + motors;
            
            // Apply diversity factor
            const diversityLoad = totalLoad * (diversity / 100);
            
            // Apply continuous load factor
            const continuousLoad = diversityLoad * (continuousPercent / 100);
            
            // Calculate current
            let current = 0;
            if (phase === 1) {
                current = continuousLoad / voltage;
            } else {
                current = continuousLoad / (1.732 * voltage);
            }
            
            // Panel sizing
            const standardSizes = [100, 125, 150, 200, 225, 400, 600, 800, 1200];
            let panelSize = standardSizes.find(size => size >= current) || 1200;
            
            // Calculate main breaker (typically 100% of service)
            const mainBreaker = panelSize;
            
            // Calculate required circuits
            const circuits = Math.ceil(continuousLoad / (panelSize * voltage / 1000));
            
            return {
                totalLoad: totalLoad,
                diversityLoad: diversityLoad,
                continuousLoad: continuousLoad,
                current: current,
                panelSize: panelSize,
                mainBreaker: mainBreaker,
                circuits: circuits,
                lighting: lighting,
                receptacles: receptacles,
                appliances: appliances,
                motors: motors,
                voltage: voltage,
                phase: phase
            };
        }

        function generatePanelSchedule(result) {
            const tbody = document.getElementById('schedule-tbody');
            const circuits = [];
            
            // Basic circuit allocation
            circuits.push({ circuit: '1-2', load: 'L1', va: result.lighting, current: result.lighting / result.voltage, ocpd: '15A' });
            circuits.push({ circuit: '3-4', load: 'L2', va: result.receptacles, current: result.receptacles / result.voltage, ocpd: '20A' });
            circuits.push({ circuit: '5-6', load: 'General', va: result.appliances, current: result.appliances / result.voltage, ocpd: '20A' });
            circuits.push({ circuit: '7-8', load: 'Motors', va: result.motors, current: result.motors / result.voltage, ocpd: '30A' });
            
            let html = '';
            circuits.forEach(circuit => {
                html += `
                    <tr>
                        <td>${circuit.circuit}</td>
                        <td>${circuit.load}</td>
                        <td>${circuit.va}</td>
                        <td>${circuit.current.toFixed(1)}</td>
                        <td>${circuit.ocpd}</td>
                    </tr>
                `;
            });
            
            // Add main breaker row
            html += `
                <tr>
                    <td><strong>Main</strong></td>
                    <td><strong>Total</strong></td>
                    <td><strong>${result.continuousLoad.toLocaleString()}</strong></td>
                    <td><strong>${result.current.toFixed(1)}</strong></td>
                    <td><strong>${result.mainBreaker}A</strong></td>
                </tr>
            `;
            
            tbody.innerHTML = html;
        }

        document.getElementById('panel-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const lighting = parseFloat(document.getElementById('lighting-load').value);
            const receptacles = parseFloat(document.getElementById('receptacle-load').value);
            const appliances = parseFloat(document.getElementById('appliance-load').value);
            const motors = parseFloat(document.getElementById('motor-load').value);
            const voltage = parseFloat(document.getElementById('voltage').value);
            const phase = parseInt(document.getElementById('phase').value);
            const diversity = parseFloat(document.getElementById('diversity-factor').value);
            const continuousPercent = parseFloat(document.getElementById('continuous-load').value);

            if (isNaN(lighting) || isNaN(receptacles) || isNaN(appliances) || 
                isNaN(motors) || isNaN(voltage) || isNaN(phase) || 
                isNaN(diversity) || isNaN(continuousPercent)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const result = calculatePanelSchedule(lighting, receptacles, appliances, motors, 
                                                voltage, phase, diversity, continuousPercent);
            
            const phaseText = phase === 3 ? '3-Phase' : '1-Phase';
            const voltageText = voltage + 'V';
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Lighting Load:</strong><br>${result.lighting.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Receptacle Load:</strong><br>${result.receptacles.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Appliance Load:</strong><br>${result.appliances.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Motor Load:</strong><br>${result.motors.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Total Load:</strong><br>${result.totalLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Diversity Load:</strong><br>${result.diversityLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Continuous Load:</strong><br>${result.continuousLoad.toLocaleString()} VA
                </div>
                <div class="result-item">
                    <strong>Demand Current:</strong><br>${result.current.toFixed(1)} A
                </div>
                <div class="result-item">
                    <strong>Panel Voltage:</strong><br>${voltageText} ${phaseText}
                </div>
                <div class="result-item">
                    <strong>Recommended Panel:</strong><br>${result.panelSize} A
                </div>
                <div class="result-item">
                    <strong>Main Breaker:</strong><br>${result.mainBreaker} A
                </div>
                <div class="result-item">
                    <strong>Required Circuits:</strong><br>${result.circuits} circuits
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Generate panel schedule
            generatePanelSchedule(result);
            document.getElementById('panel-schedule').style.display = 'block';
            
            // Save calculation
            saveCalculation('Panel Schedule', `${result.totalLoad.toLocaleString()}VA → ${result.panelSize}A panel`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentPanelScheduleCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentPanelScheduleCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
