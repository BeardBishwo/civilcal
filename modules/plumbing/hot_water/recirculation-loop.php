<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recirculation Loop - AEC Calculator</title>
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
        }

        .calculator-wrapper {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow);
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .result-area {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
            display: none;
        }

        .recent-calculations {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 10px;
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
            width: 100%;
        }

        .btn-calculate:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .back-button {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50px;
            color: var(--light);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            margin-top: 2rem;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            color: white;
            text-decoration: none;
        }

        .info-table {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4"><i class="fas fa-sync me-2"></i>Recirculation Loop Calculator</h2>
            
            <form id="recirc-loop-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="loopLength">Total Loop Length (m)</label>
                            <input type="number" class="form-control" id="loopLength" min="0" step="0.1">
                        </div>

                        <div class="form-group">
                            <label for="pipeSize">Supply Pipe Size</label>
                            <select class="form-control" id="pipeSize">
                                <option value="15">15mm (1/2")</option>
                                <option value="20">20mm (3/4")</option>
                                <option value="25">25mm (1")</option>
                                <option value="32">32mm (1-1/4")</option>
                                <option value="40">40mm (1-1/2")</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="returnSize">Return Pipe Size</label>
                            <select class="form-control" id="returnSize">
                                <option value="15">15mm (1/2")</option>
                                <option value="20" selected>20mm (3/4")</option>
                                <option value="25">25mm (1")</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="targetTemp">Target Temperature (°C)</label>
                            <input type="number" class="form-control" id="targetTemp" min="0" step="1" value="60">
                        </div>

                        <div class="form-group">
                            <label for="tempDrop">Maximum Temperature Drop (°C)</label>
                            <input type="number" class="form-control" id="tempDrop" min="0" step="0.5" value="5">
                        </div>

                        <div class="form-group">
                            <label for="fittings">Number of Major Fittings</label>
                            <input type="number" class="form-control" id="fittings" min="0" step="1" value="10">
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Requirements</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Recirculation Requirements</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Design Guidelines</h6>
                            <small>
                                Min. Flow Velocity: 0.3 m/s<br>
                                Max. Flow Velocity: 1.5 m/s<br>
                                Typical Temp. Drop: 3-5°C<br>
                                Return Line: One size smaller than supply
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentRecircCalculations"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <a href="<?php echo function_exists('app_base_url') ? app_base_url('modules/plumbing/index.php') : '../modules/plumbing/index.php'; ?>" class="back-button">
            <i class="fas fa-arrow-left me-2"></i>Back to Plumbing
        </a>
    </div>

    <script>
        document.getElementById('recirc-loop-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const length = parseFloat(document.getElementById('loopLength').value);
            const supplySize = parseFloat(document.getElementById('pipeSize').value);
            const returnSize = parseFloat(document.getElementById('returnSize').value);
            const targetTemp = parseFloat(document.getElementById('targetTemp').value);
            const tempDrop = parseFloat(document.getElementById('tempDrop').value);
            const fittings = parseInt(document.getElementById('fittings').value);
            
            if (!length || length <= 0) {
                alert('Please enter a valid loop length');
                return;
            }
            
            // Calculate requirements
            const reqs = calculateRecirculation(
                length,
                supplySize,
                returnSize,
                targetTemp,
                tempDrop,
                fittings
            );
            
            let resultText = `<strong>Flow Requirements:</strong><br>`;
            resultText += `Minimum Flow Rate: ${reqs.minFlow.toFixed(1)} L/min<br>`;
            resultText += `Design Flow Rate: ${reqs.designFlow.toFixed(1)} L/min<br>`;
            resultText += `Supply Velocity: ${reqs.supplyVelocity.toFixed(2)} m/s<br>`;
            resultText += `Return Velocity: ${reqs.returnVelocity.toFixed(2)} m/s<br><br>`;
            
            resultText += `<strong>Pump Requirements:</strong><br>`;
            resultText += `Head Loss: ${reqs.headLoss.toFixed(1)} kPa<br>`;
            resultText += `Pump Power: ${reqs.pumpPower.toFixed(2)} W<br>`;
            resultText += `Daily Energy: ${(reqs.pumpPower * 24 / 1000).toFixed(2)} kWh/day<br><br>`;
            
            resultText += `<strong>Temperature Control:</strong><br>`;
            resultText += `Heat Loss Rate: ${reqs.heatLoss.toFixed(0)} W<br>`;
            resultText += `Response Time: ${reqs.responseTime.toFixed(1)} minutes`;
            
            if (reqs.warnings.length > 0) {
                resultText += '<br><br><div class="alert alert-warning">';
                resultText += reqs.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${length}m loop → ${reqs.designFlow.toFixed(1)} L/min`);
        });

        function calculateRecirculation(length, supplySize, returnSize, targetTemp, tempDrop, fittings) {
            const warnings = [];
            
            // Calculate pipe areas
            const supplyArea = Math.PI * Math.pow(supplySize/2000, 2);
            const returnArea = Math.PI * Math.pow(returnSize/2000, 2);
            
            // Minimum flow based on heat loss
            const specificHeat = 4186; // J/kg·°C
            const density = 1000; // kg/m³
            const heatLoss = calculateHeatLoss(length, supplySize, targetTemp);
            const minFlow = (heatLoss / (specificHeat * density * tempDrop)) * 60000; // L/min
            
            // Design flow (1.2 × minimum for safety)
            const designFlow = minFlow * 1.2;
            
            // Calculate velocities
            const supplyVelocity = (designFlow/60000) / supplyArea;
            const returnVelocity = (designFlow/60000) / returnArea;
            
            if (supplyVelocity < 0.3) warnings.push('Warning: Supply velocity below 0.3 m/s');
            if (supplyVelocity > 1.5) warnings.push('Warning: Supply velocity above 1.5 m/s');
            if (returnVelocity > 1.5) warnings.push('Warning: Return velocity above 1.5 m/s');
            
            // Calculate head loss
            const totalLength = length * 2; // Supply + return
            const equivalentLength = totalLength + (fittings * 2); // Add fitting losses
            const headLoss = calculateHeadLoss(equivalentLength, designFlow/60, supplySize/1000);
            
            // Calculate pump power
            const efficiency = 0.5; // Assume 50% pump efficiency
            const pumpPower = (headLoss * (designFlow/60000) * 1000 * 9.81) / efficiency;
            
            // Calculate response time
            const volume = (supplyArea + returnArea) * length;
            const responseTime = (volume / (designFlow/60000)) / 60; // minutes
            
            return {
                minFlow,
                designFlow,
                supplyVelocity,
                returnVelocity,
                headLoss,
                pumpPower,
                heatLoss,
                responseTime,
                warnings
            };
        }

        function calculateHeatLoss(length, diameter, temp) {
            // Simple heat loss calculation (W)
            const U = 2.0; // Overall heat transfer coefficient (W/m²·°C)
            const ambientTemp = 20; // Assumed ambient temperature
            const surfaceArea = Math.PI * (diameter/1000) * length;
            return U * surfaceArea * (temp - ambientTemp);
        }

        function calculateHeadLoss(length, flow, diameter) {
            // Darcy-Weisbach equation with Blasius friction factor approximation
            const velocity = (4 * flow) / (Math.PI * Math.pow(diameter, 2));
            const reynolds = (velocity * diameter) / (1.004e-6); // Kinematic viscosity of water
            const friction = 0.316 * Math.pow(reynolds, -0.25);
            
            return (friction * length * Math.pow(velocity, 2)) / (2 * diameter * 9.81);
        }

        function saveRecent(calculation) {
            const key = 'recentRecircCalculations';
            let recent = JSON.parse(localStorage.getItem(key) || '[]');
            recent.unshift({
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            recent = recent.slice(0, 5); // Keep last 5 calculations
            localStorage.setItem(key, JSON.stringify(recent));
            displayRecent();
        }

        function displayRecent() {
            const key = 'recentRecircCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentRecircCalculations');
            
            if (recent.length === 0) {
                container.innerHTML = '<p class="text-muted">No recent calculations</p>';
                return;
            }
            
            container.innerHTML = recent.map(item => `
                <div class="card bg-dark mb-2">
                    <div class="card-body">
                        <div class="small">${item.calculation}</div>
                        <div class="small text-muted">${item.timestamp}</div>
                    </div>
                </div>
            `).join('');
        }

        // Load recent calculations on page load
        displayRecent();
    </script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

