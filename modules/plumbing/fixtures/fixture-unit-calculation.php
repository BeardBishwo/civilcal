<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixture Unit Calculator - AEC Calculator</title>
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

        .fixture-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }

        .fixture-item {
            background: rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .fixture-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .fixture-item label {
            margin: 0;
            cursor: pointer;
            flex: 1;
        }

        .fixture-count {
            font-size: 0.85rem;
            color: var(--light);
            min-width: 60px;
        }

        .total-display {
            font-size: 2rem;
            font-weight: bold;
            color: var(--accent);
            text-align: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            margin: 1rem 0;
        }

        .pipe-sizing-table {
            width: 100%;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .pipe-sizing-table th, .pipe-sizing-table td {
            padding: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .pipe-sizing-table th {
            background: rgba(0, 0, 0, 0.2);
        }

        .pipe-sizing-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-calculator me-2"></i>Fixture Unit Calculator
            </h2>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="section-toggle" onclick="toggleSection('fixtures')">
                        <h5><i class="fas fa-th me-2"></i>Select Fixtures</h5>
                    </div>
                    <div id="fixtures-section" class="section-content mb-4">
                        <div class="fixture-grid">
                            <div class="fixture-item">
                                <input type="checkbox" id="toilet" data-fu="4">
                                <label for="toilet">Water Closet</label>
                                <span class="fixture-count">4 FU</span>
                            </div>
                            <div class="fixture-item">
                                <input type="checkbox" id="urinal" data-fu="2">
                                <label for="urinal">Urinal</label>
                                <span class="fixture-count">2 FU</span>
                            </div>
                            <div class="fixture-item">
                                <input type="checkbox" id="lavatory" data-fu="1">
                                <label for="lavatory">Lavatory</label>
                                <span class="fixture-count">1 FU</span>
                            </div>
                            <div class="fixture-item">
                                <input type="checkbox" id="shower" data-fu="2">
                                <label for="shower">Shower</label>
                                <span class="fixture-count">2 FU</span>
                            </div>
                            <div class="fixture-item">
                                <input type="checkbox" id="bathtub" data-fu="2">
                                <label for="bathtub">Bathtub</label>
                                <span class="fixture-count">2 FU</span>
                            </div>
                            <div class="fixture-item">
                                <input type="checkbox" id="kitchenSink" data-fu="2">
                                <label for="kitchenSink">Kitchen Sink</label>
                                <span class="fixture-count">2 FU</span>
                            </div>
                            <div class="fixture-item">
                                <input type="checkbox" id="laundryTub" data-fu="2">
                                <label for="laundryTub">Laundry Tub</label>
                                <span class="fixture-count">2 FU</span>
                            </div>
                            <div class="fixture-item">
                                <input type="checkbox" id="dishwasher" data-fu="1.5">
                                <label for="dishwasher">Dishwasher</label>
                                <span class="fixture-count">1.5 FU</span>
                            </div>
                            <div class="fixture-item">
                                <input type="checkbox" id="washer" data-fu="1.5">
                                <label for="washer">Washing Machine</label>
                                <span class="fixture-count">1.5 FU</span>
                            </div>
                            <div class="fixture-item">
                                <input type="checkbox" id="floorDrain" data-fu="1">
                                <label for="floorDrain">Floor Drain</label>
                                <span class="fixture-count">1 FU</span>
                            </div>
                        </div>
                    </div>

                    <div class="section-toggle" onclick="toggleSection('quantity')">
                        <h5><i class="fas fa-sort-numeric-up me-2"></i>Quantities</h5>
                    </div>
                    <div id="quantity-section" class="section-content mb-4">
                        <div class="form-group">
                            <label for="quantity">Quantity per Fixture Type</label>
                            <input type="number" class="form-control" id="quantity" min="1" step="1" value="1">
                        </div>
                        <div class="form-group">
                            <label for="occupancyType">Building Type</label>
                            <select class="form-control" id="occupancyType">
                                <option value="residential">Residential</option>
                                <option value="commercial">Commercial</option>
                                <option value="industrial">Industrial</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn-calculate" onclick="calculateFixtureUnits()">
                        <i class="fas fa-calculator me-2"></i>Calculate Fixture Units
                    </button>
                </div>

                <div class="col-md-6">
                    <div class="total-display">
                        <div>Total Fixture Units</div>
                        <div id="totalFU">0 FU</div>
                    </div>

                    <div class="result-area" id="result-area">
                        <h4 class="mb-3">Calculation Results</h4>
                        <div id="result"></div>
                    </div>

                    <div class="info-section">
                        <div class="section-toggle" onclick="toggleSection('drain-sizing')">
                            <h5><i class="fas fa-pipe me-2"></i>Drain Pipe Sizing</h5>
                        </div>
                        <div id="drain-sizing-section" class="section-content">
                            <div class="form-group">
                                <label for="pipeSlope">Pipe Slope</label>
                                <select class="form-control" id="pipeSlope">
                                    <option value="0.01">1% (1:100)</option>
                                    <option value="0.02" selected>2% (1:50)</option>
                                    <option value="0.025">2.5% (1:40)</option>
                                    <option value="0.033">3.3% (1:30)</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="calculateDrainSize()">
                                Calculate Required Size
                            </button>
                        </div>
                    </div>

                    <div class="info-section">
                        <div class="section-toggle" onclick="toggleSection('reference-table')">
                            <h5><i class="fas fa-table me-2"></i>Reference Table</h5>
                        </div>
                        <div id="reference-table-section" class="section-content">
                            <table class="pipe-sizing-table">
                                <thead>
                                    <tr>
                                        <th>Fixture Units</th>
                                        <th>Pipe Size (mm)</th>
                                        <th>Slope</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>1-3</td><td>40mm</td><td>1:50</td></tr>
                                    <tr><td>4-8</td><td>50mm</td><td>1:50</td></tr>
                                    <tr><td>9-20</td><td>75mm</td><td>1:50</td></tr>
                                    <tr><td>21-60</td><td>100mm</td><td>1:50</td></tr>
                                    <tr><td>61-160</td><td>125mm</td><td>1:50</td></tr>
                                    <tr><td>161-360</td><td>150mm</td><td>1:50</td></tr>
                                    <tr><td>361+</td><td>200mm</td><td>1:50</td></tr>
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
        </div>
        <a href="<?php echo function_exists('app_base_url') ? app_base_url('modules/plumbing/index.php') : '../modules/plumbing/index.php'; ?>" class="back-button">
            <i class="fas fa-arrow-left me-2"></i>Back to Plumbing
        </a>
    </div>

    <script>
        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        function calculateFixtureUnits() {
            const checkboxes = document.querySelectorAll('.fixture-grid input[type="checkbox"]:checked');
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            
            if (checkboxes.length === 0) {
                alert('Please select at least one fixture');
                return;
            }

            let totalFU = 0;
            let fixtureList = [];

            checkboxes.forEach(cb => {
                const fu = parseFloat(cb.dataset.fu) || 0;
                const label = cb.nextElementSibling.textContent;
                totalFU += fu * quantity;
                fixtureList.push(`${quantity} × ${label}`);
            });

            // Update total display
            document.getElementById('totalFU').textContent = `${totalFU} FU`;

            // Display results
            let resultText = `<strong>Selected Fixtures:</strong><br>`;
            resultText += fixtureList.join('<br>') + '<br><br>';
            resultText += `<strong>Total Fixture Units:</strong> ${totalFU} FU<br>`;
            resultText += `<strong>Quantity per Type:</strong> ${quantity}<br>`;

            const occupancyType = document.getElementById('occupancyType').value;
            if (occupancyType === 'commercial' && totalFU > 50) {
                resultText += '<div class="alert alert-info mt-2">';
                resultText += 'Large system - consider dividing into multiple branches';
                resultText += '</div>';
            }

            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';

            // Save to recent calculations
            saveRecent(`Fixture Units: ${fixtureList.join(', ')} = ${totalFU} FU`);
        }

        function calculateDrainSize() {
            const totalFUText = document.getElementById('totalFU').textContent;
            const totalFU = parseFloat(totalFUText.replace(' FU', '')) || 0;
            
            if (totalFU === 0) {
                alert('Please calculate fixture units first');
                return;
            }

            const slope = parseFloat(document.getElementById('pipeSlope').value) || 0.02;

            // IPC Table 710.1(1) - Maximum number of fixture units
            let drainSize;
            if (totalFU <= 1) {
                drainSize = 40;
            } else if (totalFU <= 3) {
                drainSize = 50;
            } else if (totalFU <= 8) {
                drainSize = 50;
            } else if (totalFU <= 20) {
                drainSize = 75;
            } else if (totalFU <= 60) {
                drainSize = 100;
            } else if (totalFU <= 160) {
                drainSize = 125;
            } else if (totalFU <= 360) {
                drainSize = 150;
            } else {
                drainSize = 200;
            }

            // Slope adjustment
            if (slope >= 0.033 && drainSize > 50) {
                drainSize -= 25;
            }

            const resultHTML = `
                <strong>Fixture Units:</strong> ${totalFU} FU<br>
                <strong>Selected Slope:</strong> ${(slope * 100).toFixed(1)}%<br>
                <strong>Required Pipe Size:</strong> ${drainSize} mm<br>
                <strong>Imperial Equivalent:</strong> ${(drainSize / 25.4).toFixed(1)} inches<br><br>
                <small class="text-muted">
                    Based on IPC Table 710.1(1) - Maximum Number of Fixture Units<br>
                    Slope adjustments applied per code requirements
                </small>
            `;

            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML += '<div class="mt-3 p-3 bg-dark rounded">Drain Size Calculation:<br>' + resultHTML + '</div>';
            document.getElementById('result-area').style.display = 'block';

            // Save to recent calculations
            saveRecent(`Drain Sizing: ${totalFU} FU → ${drainSize}mm @ ${(slope*100).toFixed(1)}%`);
        }

        function saveRecent(calculation) {
            const key = 'recentFixtureUnitCalculations';
            let recent = JSON.parse(localStorage.getItem(key) || '[]');
            recent.unshift({
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            recent = recent.slice(0, 5); // Keep last 5
            localStorage.setItem(key, JSON.stringify(recent));
            displayRecent();
        }

        function displayRecent() {
            const key = 'recentFixtureUnitCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentCalculations');
            
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

        // Show sections by default and load recent calculations
        document.getElementById('fixtures-section').style.display = 'block';
        document.getElementById('quantity-section').style.display = 'block';
        document.getElementById('reference-table-section').style.display = 'block';
        displayRecent();
    </script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

