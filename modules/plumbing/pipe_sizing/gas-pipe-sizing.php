<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gas Pipe Sizing - AEC Calculator</title>
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

        .reference-table {
            width: 100%;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .reference-table th, .reference-table td {
            padding: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .reference-table th {
            background: rgba(0, 0, 0, 0.2);
        }

        .reference-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-fire me-2"></i>Gas Pipe Sizing
            </h2>
            
            <form id="gas-pipe-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('gas-details')">
                            <h5><i class="fas fa-burn me-2"></i>Gas Details</h5>
                        </div>
                        <div id="gas-details-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="gasType">Gas Type</label>
                                <select class="form-control" id="gasType">
                                    <option value="natural">Natural Gas</option>
                                    <option value="propane">Propane</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="gasFlow">Gas Flow (m³/hr)</label>
                                <input type="number" class="form-control" id="gasFlow" min="0" step="0.1" value="5">
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('pipe-details')">
                            <h5><i class="fas fa-ruler-horizontal me-2"></i>Pipe Details</h5>
                        </div>
                        <div id="pipe-details-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="pipeLength">Pipe Length (m)</label>
                                <input type="number" class="form-control" id="pipeLength" min="0" step="1" value="20">
                            </div>

                            <div class="form-group">
                                <label for="pressureDrop">Allowable Pressure Drop (Pa)</label>
                                <input type="number" class="form-control" id="pressureDrop" min="0" step="10" value="100">
                            </div>

                            <div class="form-group">
                                <label for="pipeMaterial">Pipe Material</label>
                                <select class="form-control" id="pipeMaterial">
                                    <option value="steel">Steel</option>
                                    <option value="copper">Copper</option>
                                    <option value="csst">CSST</option>
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
                            <div class="section-toggle" onclick="toggleSection('reference')">
                                <h5><i class="fas fa-info-circle me-2"></i>Reference Data</h5>
                            </div>
                            <div id="reference-section" class="section-content">
                                <table class="reference-table">
                                    <thead>
                                        <tr>
                                            <th>Gas Type</th>
                                            <th>Density (kg/m³)</th>
                                            <th>Viscosity (Pa·s)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Natural Gas</td>
                                            <td>0.8</td>
                                            <td>1.1e-5</td>
                                        </tr>
                                        <tr>
                                            <td>Propane</td>
                                            <td>1.8</td>
                                            <td>0.8e-5</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="info-section">
                            <h5>Recent Calculations</h5>
                            <div id="recentCalculations"></div>
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
        // Gas properties
        const gasProps = {
            natural: { density: 0.8, viscosity: 1.1e-5 },
            propane: { density: 1.8, viscosity: 0.8e-5 }
        };

        // Material roughness
        const materials = {
            steel: 0.045,
            copper: 0.0015,
            csst: 0.015
        };

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        document.getElementById('gas-pipe-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const gasType = document.getElementById('gasType').value;
            const flow = parseFloat(document.getElementById('gasFlow').value);
            const length = parseFloat(document.getElementById('pipeLength').value);
            const pressureDrop = parseFloat(document.getElementById('pressureDrop').value);
            const material = document.getElementById('pipeMaterial').value;
            
            if (!flow || !length || !pressureDrop) {
                showNotification('Please enter all required values', 'info');
                return;
            }
            
            const results = calculatePipeSize(gasType, flow, length, pressureDrop, material);
            
            let resultText = `<strong>Input Parameters:</strong><br>`;
            resultText += `Gas Type: ${gasType.charAt(0).toUpperCase() + gasType.slice(1)}<br>`;
            resultText += `Flow Rate: ${flow} m³/hr<br>`;
            resultText += `Pipe Length: ${length} m<br><br>`;
            
            resultText += `<strong>Sizing Calculation:</strong><br>`;
            resultText += `Theoretical Diameter: ${results.diameter.toFixed(1)} mm<br>`;
            resultText += `Recommended Size: ${results.recommendedSize} mm<br>`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            saveRecent(`${flow}m³/hr ${gasType} → ${results.recommendedSize}mm`);
        });

        function calculatePipeSize(gas, flow, length, drop, material) {
            const warnings = [];
            
            const { density, viscosity } = gasProps[gas];
            const roughness = materials[material];
            
            // Darcy-Weisbach equation for gas flow
            const flow_m3s = flow / 3600;
            const diameter_m = Math.pow(
                (128 * viscosity * length * flow_m3s) / (Math.PI * drop),
                0.25
            );
            const diameter_mm = diameter_m * 1000;
            
            // Standard sizes
            const standardSizes = [15, 20, 25, 32, 40, 50, 65, 80];
            const recommendedSize = standardSizes.find(s => s >= diameter_mm) || 80;
            
            if (drop > 250) {
                warnings.push('High pressure drop - check appliance requirements');
            }
            
            if (length > 50) {
                warnings.push('Long pipe run - verify pressure at delivery point');
            }
            
            return {
                diameter: diameter_mm,
                recommendedSize,
                warnings
            };
        }

        function saveRecent(calculation) {
            const key = 'recentGasPipeCalculations';
            let recent = JSON.parse(localStorage.getItem(key) || '[]');
            recent.unshift({
                type: 'Gas Pipe Sizing',
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            recent = recent.slice(0, 5);
            localStorage.setItem(key, JSON.stringify(recent));
            displayRecent();
        }

        function displayRecent() {
            const key = 'recentGasPipeCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentCalculations');
            
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

        // Initialize
        document.getElementById('gas-details-section').style.display = 'block';
        document.getElementById('reference-section').style.display = 'block';
        displayRecent();
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

