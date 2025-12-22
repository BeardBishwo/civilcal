<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drainage Pipe Sizing - AEC Calculator</title>
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
            background: linear-gradient(45deg, #ffffff, #ffffff);
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
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4"><i class="fas fa-pipe me-2"></i>Drainage Pipe Sizing</h2>
            
            <form id="drainage-pipe-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="calculationType">Calculation Method</label>
                            <select class="form-control" id="calculationType">
                                <option value="fixtureUnits">Fixture Units (DFU)</option>
                                <option value="flow">Flow Rate</option>
                            </select>
                        </div>

                        <div id="fixtureUnitInputs">
                            <div class="form-group">
                                <label for="fixtureUnits">Total Fixture Units</label>
                                <input type="number" class="form-control" id="fixtureUnits" min="0" step="1">
                            </div>
                        </div>

                        <div id="flowInputs" style="display: none;">
                            <div class="form-group">
                                <label for="flowRate">Flow Rate</label>
                                <input type="number" class="form-control" id="flowRate" min="0" step="0.1">
                            </div>
                            <div class="form-group">
                                <label for="flowUnit">Flow Unit</label>
                                <select class="form-control" id="flowUnit">
                                    <option value="lps">L/s</option>
                                    <option value="gpm">GPM</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="pipeSlope">Pipe Slope (%)</label>
                            <select class="form-control" id="pipeSlope">
                                <option value="0.5">0.5% (1:200)</option>
                                <option value="1.0">1.0% (1:100)</option>
                                <option value="1.5">1.5% (1:67)</option>
                                <option value="2.0" selected>2.0% (1:50)</option>
                                <option value="2.5">2.5% (1:40)</option>
                                <option value="3.0">3.0% (1:33)</option>
                                <option value="4.0">4.0% (1:25)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pipeMaterial">Pipe Material</label>
                            <select class="form-control" id="pipeMaterial">
                                <option value="pvc">PVC</option>
                                <option value="cast">Cast Iron</option>
                                <option value="copper">Copper DWV</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Pipe Size</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Required Pipe Size</h4>
                            <div id="result"></div>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentDrainageCalculations"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <a href="<?php echo function_exists('app_base_url') ? app_base_url('modules/plumbing/index.php') : '../modules/plumbing/index.php'; ?>" class="btn btn-outline-light mt-3">Back to Plumbing</a>
    </div>

    <script>
        // Toggle between fixture units and flow rate inputs
        document.getElementById('calculationType').addEventListener('change', function() {
            const fixtureInputs = document.getElementById('fixtureUnitInputs');
            const flowInputs = document.getElementById('flowInputs');
            
            if (this.value === 'fixtureUnits') {
                fixtureInputs.style.display = 'block';
                flowInputs.style.display = 'none';
            } else {
                fixtureInputs.style.display = 'none';
                flowInputs.style.display = 'block';
            }
        });

        // Calculate pipe size
        document.getElementById('drainage-pipe-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const calculationType = document.getElementById('calculationType').value;
            const slope = parseFloat(document.getElementById('pipeSlope').value);
            const material = document.getElementById('pipeMaterial').value;
            
            let flow = 0;
            let resultText = '';
            
            if (calculationType === 'fixtureUnits') {
                const fu = parseInt(document.getElementById('fixtureUnits').value);
                if (!fu) {
                    showNotification('Please enter the number of fixture units', 'info');
                    return;
                }
                flow = convertFUtoFlow(fu);
                resultText = `Based on ${fu} fixture units`;
            } else {
                const flowRate = parseFloat(document.getElementById('flowRate').value);
                const flowUnit = document.getElementById('flowUnit').value;
                if (!flowRate) {
                    showNotification('Please enter the flow rate', 'info');
                    return;
                }
                flow = flowUnit === 'gpm' ? flowRate * 0.0631 : flowRate; // Convert GPM to L/s
                resultText = `Based on ${flowRate} ${flowUnit}`;
            }
            
            const size = calculatePipeSize(flow, slope, material);
            resultText += `<br>Flow: ${flow.toFixed(2)} L/s<br>Slope: ${slope}%`;
            resultText += `<br><strong>Minimum Pipe Size:</strong><br>${size.mm} mm (${size.inches} inches)`;
            
            if (material === 'pvc') {
                resultText += '<br><br><small>Based on PVC-DWV pipe roughness coefficient n=0.011</small>';
            } else if (material === 'cast') {
                resultText += '<br><br><small>Based on Cast Iron pipe roughness coefficient n=0.013</small>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(resultText);
        });

        function convertFUtoFlow(fu) {
            // Hunter's curve approximation for drainage fixture units to flow
            return Math.sqrt(fu) * 0.316; // L/s
        }

        function calculatePipeSize(flow, slope, material) {
            // Manning's equation solving for diameter
            const n = material === 'pvc' ? 0.011 : 0.013; // Roughness coefficient
            const s = slope / 100; // Convert percent to decimal
            
            // Manning formula rearranged for diameter (full flow condition)
            const d = Math.pow((flow * n) / (0.312 * Math.pow(s, 0.5)), 0.375);
            const mm = Math.ceil(d * 1000); // Convert m to mm and round up
            
            // Standard pipe sizes (mm)
            const standardSizes = [32, 40, 50, 65, 75, 100, 125, 150, 200];
            let selectedSize = standardSizes[0];
            
            for (let size of standardSizes) {
                if (size >= mm) {
                    selectedSize = size;
                    break;
                }
            }
            
            return {
                mm: selectedSize,
                inches: (selectedSize / 25.4).toFixed(1)
            };
        }

        function saveRecent(calculation) {
            const key = 'recentDrainageCalculations';
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
            const key = 'recentDrainageCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentDrainageCalculations');
            
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
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

