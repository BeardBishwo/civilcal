<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shower Sizing - AEC Calculator</title>
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
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-shower me-2"></i>Shower Sizing Calculator
            </h2>
            
            <form id="shower-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('shower-type')">
                            <h5><i class="fas fa-water me-2"></i>Shower Type</h5>
                        </div>
                        <div id="shower-type-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="showerType">Type of Shower</label>
                                <select class="form-control" id="showerType">
                                    <option value="standard">Standard Shower (9 L/min)</option>
                                    <option value="eco">Eco-Friendly (6 L/min)</option>
                                    <option value="deluxe">Deluxe/Rain (12 L/min)</option>
                                    <option value="multi">Multi-Head (15 L/min)</option>
                                    <option value="custom">Custom Flow Rate</option>
                                </select>
                            </div>

                            <div id="customFlowDiv" style="display:none;">
                                <div class="form-group">
                                    <label for="customFlow">Custom Flow Rate (L/min)</label>
                                    <input type="number" class="form-control" id="customFlow" min="0" step="0.1">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Hot Water Required</label>
                                <select class="form-control" id="hotWater">
                                    <option value="yes">Yes (38°C target)</option>
                                    <option value="no">No (Cold only)</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('installation')">
                            <h5><i class="fas fa-tools me-2"></i>Installation Details</h5>
                        </div>
                        <div id="installation-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="showerCount">Number of Showers</label>
                                <input type="number" class="form-control" id="showerCount" min="1" step="1" value="1">
                            </div>

                            <div class="form-group">
                                <label for="usagePattern">Usage Pattern</label>
                                <select class="form-control" id="usagePattern">
                                    <option value="residential">Residential (Low Coincidence)</option>
                                    <option value="gym">Gym/Sports (High Coincidence)</option>
                                    <option value="industrial">Industrial (Shift Changes)</option>
                                    <option value="custom">Custom Pattern</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="material">Pipe Material</label>
                                <select class="form-control" id="material">
                                    <option value="copper">Copper</option>
                                    <option value="pex">PEX</option>
                                    <option value="cpvc">CPVC</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Requirements</button>
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
                                            <th>Shower Type</th>
                                            <th>Flow Rate</th>
                                            <th>Fixture Units</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Standard</td>
                                            <td>9 L/min</td>
                                            <td>2.0 FU</td>
                                        </tr>
                                        <tr>
                                            <td>Eco</td>
                                            <td>6 L/min</td>
                                            <td>1.5 FU</td>
                                        </tr>
                                        <tr>
                                            <td>Deluxe</td>
                                            <td>12 L/min</td>
                                            <td>3.0 FU</td>
                                        </tr>
                                        <tr>
                                            <td>Multi-Head</td>
                                            <td>15 L/min</td>
                                            <td>4.0 FU</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="info-section">
                            <h5>Recent Calculations</h5>
                            <div id="recentShowerCalculations"></div>
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
        // Standard values by shower type
        const showerTypes = {
            standard: { flow: 9, fu: 2.0 },
            eco: { flow: 6, fu: 1.5 },
            deluxe: { flow: 12, fu: 3.0 },
            multi: { flow: 15, fu: 4.0 }
        };

        // Usage pattern factors
        const patterns = {
            residential: { coincidence: 0.5, peakFactor: 2.0 },
            gym: { coincidence: 0.8, peakFactor: 3.0 },
            industrial: { coincidence: 0.9, peakFactor: 4.0 },
            custom: { coincidence: 0.7, peakFactor: 2.5 }
        };

        // Material properties
        const materials = {
            copper: { maxVelocity: 2.4, roughness: 0.0015 },
            pex: { maxVelocity: 2.0, roughness: 0.007 },
            cpvc: { maxVelocity: 2.1, roughness: 0.0015 }
        };

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        document.getElementById('showerType').addEventListener('change', function() {
            document.getElementById('customFlowDiv').style.display = 
                this.value === 'custom' ? 'block' : 'none';
        });

        document.getElementById('shower-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get shower type and flow
            const type = document.getElementById('showerType').value;
            let flowRate;
            if (type === 'custom') {
                flowRate = parseFloat(document.getElementById('customFlow').value);
                if (!flowRate) {
                    alert('Please enter a custom flow rate');
                    return;
                }
            } else {
                flowRate = showerTypes[type].flow;
            }
            
            // Get other parameters
            const count = parseInt(document.getElementById('showerCount').value) || 1;
            const pattern = document.getElementById('usagePattern').value;
            const material = document.getElementById('material').value;
            const needsHot = document.getElementById('hotWater').value === 'yes';
            
            const results = calculateRequirements(
                type,
                flowRate,
                count,
                pattern,
                material,
                needsHot
            );
            
            // Display results
            let resultText = `<strong>Flow Requirements:</strong><br>`;
            resultText += `Base Flow per Shower: ${flowRate.toFixed(1)} L/min<br>`;
            resultText += `Design Flow Rate: ${results.designFlow.toFixed(1)} L/min<br>`;
            resultText += `Peak Flow: ${results.peakFlow.toFixed(1)} L/min<br><br>`;
            
            resultText += `<strong>Pipe Sizing:</strong><br>`;
            resultText += `Main Supply: ${results.mainSize} mm<br>`;
            resultText += `Branch Lines: ${results.branchSize} mm<br>`;
            
            if (needsHot) {
                resultText += `Hot Water Flow: ${results.hotWaterFlow.toFixed(1)} L/min<br>`;
            }
            
            if (results.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent
            saveRecent(`${count}× ${type} → ${results.mainSize}mm`);
        });

        function calculateRequirements(type, flow, count, pattern, material, needsHot) {
            const warnings = [];
            
            // Get pattern factors
            const { coincidence, peakFactor } = patterns[pattern];
            
            // Calculate flows
            const baseFlow = flow * count;
            const designFlow = baseFlow * coincidence;
            const peakFlow = designFlow * peakFactor;
            
            // Calculate hot water requirement if needed
            const hotWaterFlow = needsHot ? designFlow * 0.6 : 0;
            
            // Size pipes using velocity method
            const velocity = materials[material].maxVelocity;
            
            // Convert peak flow to m³/s for pipe sizing
            const q = peakFlow / (60 * 1000);
            const area = q / velocity;
            const diameter = 2 * Math.sqrt(area / Math.PI) * 1000;
            
            // Standard sizes
            const sizes = [15, 20, 25, 32, 40, 50, 65];
            const mainSize = sizes.find(s => s >= diameter) || Math.ceil(diameter);
            const branchSize = sizes.find(s => s >= diameter * 0.6) || 15;
            
            // Validation checks
            if (count > 1 && coincidence === 1) {
                warnings.push('High coincidence factor may lead to oversizing');
            }
            
            if (peakFlow / designFlow > 4) {
                warnings.push('Large peak factor - consider staged delivery');
            }
            
            if (count > 20 && pattern !== 'gym') {
                warnings.push('Consider splitting into multiple supply zones');
            }
            
            return {
                designFlow,
                peakFlow,
                mainSize,
                branchSize,
                hotWaterFlow,
                warnings
            };
        }

        function saveRecent(calculation) {
            const key = 'recentShowerCalculations';
            let recent = JSON.parse(localStorage.getItem(key) || '[]');
            recent.unshift({
                type: 'Shower Sizing',
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            recent = recent.slice(0, 5);
            localStorage.setItem(key, JSON.stringify(recent));
            displayRecent();
        }

        function displayRecent() {
            const key = 'recentShowerCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentShowerCalculations');
            
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

        // Show shower type section by default and initialize
        document.getElementById('shower-type-section').style.display = 'block';
        document.getElementById('reference-section').style.display = 'block';
        displayRecent();
    </script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

