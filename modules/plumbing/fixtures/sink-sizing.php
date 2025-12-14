<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sink Sizing - AEC Calculator</title>
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

        .info-section {
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

        .section-toggle {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            cursor: pointer;
        }

        .section-content {
            display: none;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .pipe-size-table {
            width: 100%;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .pipe-size-table th, .pipe-size-table td {
            padding: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .pipe-size-table th {
            background: rgba(0, 0, 0, 0.2);
        }

        .pipe-size-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-sink me-2"></i>Sink Sizing Calculator
            </h2>
            
            <form id="sink-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('flow')">
                            <h5><i class="fas fa-faucet me-2"></i>Flow Parameters</h5>
                        </div>
                        <div id="flow-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="sinkType">Sink Type</label>
                                <select class="form-control" id="sinkType">
                                    <option value="lavatory">Lavatory Sink (6 L/min)</option>
                                    <option value="kitchen">Kitchen Sink (12 L/min)</option>
                                    <option value="service">Service Sink (15 L/min)</option>
                                    <option value="commercial">Commercial Sink (20 L/min)</option>
                                    <option value="custom">Custom Flow Rate</option>
                                </select>
                            </div>

                            <div id="customFlowDiv" style="display:none;">
                                <div class="form-group">
                                    <label for="customFlow">Custom Flow Rate (L/min)</label>
                                    <input type="number" class="form-control" id="customFlow" min="0" step="0.1">
                                </div>
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('velocity')">
                            <h5><i class="fas fa-tachometer-alt me-2"></i>Design Parameters</h5>
                        </div>
                        <div id="velocity-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="velocity">Design Velocity (m/s)</label>
                                <select class="form-control" id="velocity">
                                    <option value="0.6">Low Velocity (0.6 m/s)</option>
                                    <option value="1.0" selected>Standard (1.0 m/s)</option>
                                    <option value="1.5">High Flow (1.5 m/s)</option>
                                    <option value="custom">Custom Velocity</option>
                                </select>
                            </div>

                            <div id="customVelocityDiv" style="display:none;">
                                <div class="form-group">
                                    <label for="customVelocity">Custom Velocity (m/s)</label>
                                    <input type="number" class="form-control" id="customVelocity" min="0.1" step="0.1">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="material">Pipe Material</label>
                                <select class="form-control" id="material">
                                    <option value="copper">Copper</option>
                                    <option value="pvc">PVC</option>
                                    <option value="pex">PEX</option>
                                    <option value="steel">Galvanized Steel</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Pipe Size</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Sizing Results</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-section">
                            <div class="section-toggle" onclick="toggleSection('standards')">
                                <h5><i class="fas fa-ruler me-2"></i>Standard Sizes</h5>
                            </div>
                            <div id="standards-section" class="section-content">
                                <table class="pipe-size-table">
                                    <thead>
                                        <tr>
                                            <th>Nominal (mm)</th>
                                            <th>Flow Rate (L/min)</th>
                                            <th>Velocity (m/s)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sizeTable">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="info-section">
                            <h5>Recent Calculations</h5>
                            <div id="recentSinkCalculations"></div>
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
        // Standard values by sink type
        const sinkTypes = {
            lavatory: 6,
            kitchen: 12,
            service: 15,
            commercial: 20
        };

        // Standard pipe sizes and their typical applications
        const standardSizes = [15, 20, 25, 32, 40, 50, 65, 80];

        // Material roughness factors
        const materials = {
            copper: { roughness: 0.0015, maxVelocity: 2.4 },
            pvc: { roughness: 0.0015, maxVelocity: 2.0 },
            pex: { roughness: 0.0015, maxVelocity: 2.0 },
            steel: { roughness: 0.15, maxVelocity: 2.4 }
        };

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        // Event listeners for custom inputs
        document.getElementById('sinkType').addEventListener('change', function() {
            document.getElementById('customFlowDiv').style.display = 
                this.value === 'custom' ? 'block' : 'none';
        });

        document.getElementById('velocity').addEventListener('change', function() {
            document.getElementById('customVelocityDiv').style.display = 
                this.value === 'custom' ? 'block' : 'none';
        });

        // Main calculation form
        document.getElementById('sink-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get flow rate
            const sinkType = document.getElementById('sinkType').value;
            let flowRate;
            if (sinkType === 'custom') {
                flowRate = parseFloat(document.getElementById('customFlow').value);
                if (!flowRate) {
                    showNotification('Please enter a custom flow rate', 'info');
                    return;
                }
            } else {
                flowRate = sinkTypes[sinkType];
            }
            
            // Get velocity
            const velocityType = document.getElementById('velocity').value;
            let velocity;
            if (velocityType === 'custom') {
                velocity = parseFloat(document.getElementById('customVelocity').value);
                if (!velocity) {
                    showNotification('Please enter a custom velocity', 'info');
                    return;
                }
            } else {
                velocity = parseFloat(velocityType);
            }
            
            const material = document.getElementById('material').value;
            const results = calculatePipeSize(flowRate, velocity, material);
            
            // Display results
            let resultText = `<strong>Flow Parameters:</strong><br>`;
            resultText += `Flow Rate: ${flowRate.toFixed(1)} L/min (${(flowRate/60).toFixed(3)} L/s)<br>`;
            resultText += `Design Velocity: ${velocity.toFixed(1)} m/s<br><br>`;
            
            resultText += `<strong>Calculated Size:</strong><br>`;
            resultText += `Theoretical Diameter: ${results.theoreticalDiameter.toFixed(1)} mm<br>`;
            resultText += `Recommended Size: ${results.recommendedSize} mm nominal<br>`;
            resultText += `Actual Velocity: ${results.actualVelocity.toFixed(2)} m/s<br>`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Update size table
            updateSizeTable(flowRate);
            
            // Save to recent
            saveRecent(`${flowRate}L/min → ${results.recommendedSize}mm`);
        });

        function calculatePipeSize(flowRate, velocity, material) {
            const warnings = [];
            
            // Convert flow rate to m³/s
            const q = flowRate / (60 * 1000);
            
            // Calculate theoretical area and diameter
            const area = q / velocity;
            const theoreticalDiameter = 2 * Math.sqrt(area / Math.PI) * 1000; // mm
            
            // Find nearest standard size (round up)
            const recommendedSize = standardSizes.find(s => s >= theoreticalDiameter) || 
                                  Math.ceil(theoreticalDiameter);
            
            // Calculate actual velocity
            const actualArea = Math.PI * Math.pow(recommendedSize/2000, 2);
            const actualVelocity = q / actualArea;
            
            // Check velocity limits
            const maxVel = materials[material].maxVelocity;
            if (actualVelocity > maxVel) {
                warnings.push(`Warning: Velocity exceeds maximum recommended for ${material} (${maxVel} m/s)`);
            }
            
            if (actualVelocity < 0.5) {
                warnings.push('Warning: Low velocity may lead to sediment buildup');
            }
            
            return {
                theoreticalDiameter,
                recommendedSize,
                actualVelocity,
                warnings
            };
        }

        function updateSizeTable(flowRate) {
            const tbody = document.getElementById('sizeTable');
            tbody.innerHTML = standardSizes.map(size => {
                const area = Math.PI * Math.pow(size/2000, 2);
                const velocity = (flowRate/60/1000) / area;
                return `
                    <tr>
                        <td>${size}</td>
                        <td>${flowRate.toFixed(1)}</td>
                        <td>${velocity.toFixed(2)}</td>
                    </tr>
                `;
            }).join('');
        }

        function saveRecent(calculation) {
            const key = 'recentSinkCalculations';
            let recent = JSON.parse(localStorage.getItem(key) || '[]');
            recent.unshift({
                type: 'Sink Sizing',
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            recent = recent.slice(0, 5);
            localStorage.setItem(key, JSON.stringify(recent));
            displayRecent();
        }

        function displayRecent() {
            const key = 'recentSinkCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentSinkCalculations');
            
            if (recent.length === 0) {
                container.innerHTML = '<p class="text-muted">No recent calculations</p>';
                return;
            }
            
            container.innerHTML = recent.map(item => `
                <div class="card bg-dark mb-2">
                    <div class="card-body">
                        <div class="small">${item.type}</div>
                        <div class="small text-muted">${item.calculation}</div>
                        <div class="small text-muted">${item.timestamp}</div>
                    </div>
                </div>
            `).join('');
        }

        // Show flow section by default and initialize
        document.getElementById('flow-section').style.display = 'block';
        document.getElementById('standards-section').style.display = 'block';
        displayRecent();
        updateSizeTable(6); // Initialize with lavatory sink flow
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

