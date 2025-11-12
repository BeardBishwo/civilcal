<?php
session_start();
require_once __DIR__ . '/../../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expansion Loop Sizing - AEC Calculator</title>
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
                <i class="fas-expand-arrows-alt me-2"></i>Expansion Loop Sizing
            </h2>
            
            <form id="expansion-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('pipe-details')">
                            <h5><i class="fas fa-ruler-combined me-2"></i>Pipe Details</h5>
                        </div>
                        <div id="pipe-details-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="pipeLength">Pipe Length (m)</label>
                                <input type="number" class="form-control" id="pipeLength" min="0" step="0.1" value="30">
                            </div>

                            <div class="form-group">
                                <label for="pipeDiameter">Pipe Diameter (mm)</label>
                                <input type="number" class="form-control" id="pipeDiameter" min="0" step="1" value="50">
                            </div>

                            <div class="form-group">
                                <label for="pipeMaterial">Pipe Material</label>
                                <select class="form-control" id="pipeMaterial">
                                    <option value="copper">Copper</option>
                                    <option value="steel">Steel</option>
                                    <option value="pvc">PVC</option>
                                    <option value="cpvc">CPVC</option>
                                    <option value="pex">PEX</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('temperature')">
                            <h5><i class="fas fa-thermometer-half me-2"></i>Temperature</h5>
                        </div>
                        <div id="temperature-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="tempChange">Temperature Change (°C)</label>
                                <input type="number" class="form-control" id="tempChange" min="0" step="1" value="50">
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Expansion</button>
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
                                            <th>Material</th>
                                            <th>Coefficient (mm/m°C)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="coefficientTable">
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
        // Expansion coefficients
        const coefficients = {
            copper: 0.0167,
            steel: 0.0117,
            pvc: 0.07,
            cpvc: 0.066,
            pex: 0.18
        };

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        document.getElementById('expansion-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const length = parseFloat(document.getElementById('pipeLength').value);
            const diameter = parseFloat(document.getElementById('pipeDiameter').value);
            const tempChange = parseFloat(document.getElementById('tempChange').value);
            const material = document.getElementById('pipeMaterial').value;
            
            if (!length || !diameter || !tempChange) {
                alert('Please enter all required values');
                return;
            }
            
            const results = calculateExpansion(length, diameter, tempChange, material);
            
            let resultText = `<strong>Input Parameters:</strong><br>`;
            resultText += `Pipe Length: ${length} m<br>`;
            resultText += `Temperature Change: ${tempChange}°C<br>`;
            resultText += `Material: ${material.toUpperCase()}<br><br>`;
            
            resultText += `<strong>Expansion Calculation:</strong><br>`;
            resultText += `Total Expansion: ${results.expansion.toFixed(1)} mm<br><br>`;
            
            resultText += `<strong>Recommended Loop Size:</strong><br>`;
            resultText += `Loop Width: ${results.loopWidth.toFixed(0)} mm<br>`;
            resultText += `Loop Length: ${results.loopLength.toFixed(0)} mm<br>`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            saveRecent(`${length}m ${material} → ${results.expansion.toFixed(1)}mm`);
        });

        function calculateExpansion(length, diameter, tempChange, material) {
            const warnings = [];
            
            const coefficient = coefficients[material];
            const expansion = length * coefficient * tempChange;
            
            // Loop sizing formula (empirical)
            const loopWidth = 2 * Math.sqrt(expansion * diameter);
            const loopLength = 4 * Math.sqrt(expansion * diameter);
            
            if (expansion > 100) {
                warnings.push('High expansion - consider expansion joints');
            }
            
            if (length > 100 && material === 'pvc') {
                warnings.push('Long PVC runs require careful support');
            }
            
            return {
                expansion,
                loopWidth,
                loopLength,
                warnings
            };
        }

        function populateTable() {
            const table = document.getElementById('coefficientTable');
            table.innerHTML = Object.entries(coefficients).map(([key, value]) => `
                <tr>
                    <td>${key.toUpperCase()}</td>
                    <td>${value}</td>
                </tr>
            `).join('');
        }

        function saveRecent(calculation) {
            const key = 'recentExpansionCalculations';
            let recent = JSON.parse(localStorage.getItem(key) || '[]');
            recent.unshift({
                type: 'Expansion Loop',
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            recent = recent.slice(0, 5);
            localStorage.setItem(key, JSON.stringify(recent));
            displayRecent();
        }

        function displayRecent() {
            const key = 'recentExpansionCalculations';
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
        document.getElementById('pipe-details-section').style.display = 'block';
        document.getElementById('reference-section').style.display = 'block';
        populateTable();
        displayRecent();
    </script>
</body>
</html>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
