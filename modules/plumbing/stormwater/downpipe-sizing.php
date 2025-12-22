<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downpipe Sizing - AEC Calculator</title>
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

        .back-button {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(45deg, #ffffff, #ffffff);
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
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-arrow-down me-2"></i>Downpipe Sizing Calculator
            </h2>
            
            <form id="downpipe-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('flow')">
                            <h5><i class="fas fa-water me-2"></i>Flow Parameters</h5>
                        </div>
                        <div id="flow-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="catchmentArea">Catchment Area (m²)</label>
                                <input type="number" class="form-control" id="catchmentArea" min="0" step="0.1">
                            </div>

                            <div class="form-group">
                                <label for="rainfallIntensity">Rainfall Intensity (mm/hr)</label>
                                <input type="number" class="form-control" id="rainfallIntensity" min="0" step="1" value="150">
                            </div>

                            <div class="form-group">
                                <label for="runoffCoeff">Runoff Coefficient</label>
                                <select class="form-control" id="runoffCoeff">
                                    <option value="1.0">Metal/Tile Roof (1.0)</option>
                                    <option value="0.95">Asphalt Shingles (0.95)</option>
                                    <option value="0.9">Built-up Roof (0.9)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="downpipesCount">Number of Downpipes</label>
                                <input type="number" class="form-control" id="downpipesCount" min="1" step="1" value="1">
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('pipe')">
                            <h5><i class="fas fa-pipe me-2"></i>Pipe Details</h5>
                        </div>
                        <div id="pipe-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="pipeType">Pipe Type</label>
                                <select class="form-control" id="pipeType">
                                    <option value="round">Round PVC/Metal</option>
                                    <option value="rectangular">Rectangular</option>
                                    <option value="square">Square</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="pipeHeight">Vertical Height (m)</label>
                                <input type="number" class="form-control" id="pipeHeight" min="0" step="0.1">
                            </div>

                            <div class="form-group">
                                <label for="offsetCount">Number of Offsets/Bends</label>
                                <input type="number" class="form-control" id="offsetCount" min="0" step="1" value="2">
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Downpipe Size</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Downpipe Requirements</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Design Guidelines</h6>
                            <small>
                                Max. Fill Ratio: 33%<br>
                                Min. Velocity: 1.0 m/s<br>
                                Max. Velocity: 2.5 m/s<br>
                                Add 20% safety margin
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentDownpipeCalculations"></div>
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
        // Standard pipe sizes (mm)
        const standardSizes = {
            round: [65, 75, 90, 100, 125, 150],
            rectangular: [
                {width: 100, height: 50},
                {width: 100, height: 75},
                {width: 150, height: 75},
                {width: 150, height: 100}
            ],
            square: [75, 90, 100, 125]
        };

        // Flow capacity coefficients
        const flowCoeff = {
            round: 0.0144,
            rectangular: 0.0133,
            square: 0.0138
        };

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        document.getElementById('downpipe-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const area = parseFloat(document.getElementById('catchmentArea').value);
            const rainfall = parseFloat(document.getElementById('rainfallIntensity').value);
            const runoff = parseFloat(document.getElementById('runoffCoeff').value);
            const downpipes = parseInt(document.getElementById('downpipesCount').value);
            const type = document.getElementById('pipeType').value;
            const height = parseFloat(document.getElementById('pipeHeight').value);
            const offsets = parseInt(document.getElementById('offsetCount').value);
            
            if (!area || !height) {
                showNotification('Please enter catchment area and pipe height', 'info');
                return;
            }
            
            // Calculate requirements
            const results = calculateDownpipe(
                area,
                rainfall,
                runoff,
                downpipes,
                type,
                height,
                offsets
            );
            
            let resultText = `<strong>Flow Requirements:</strong><br>`;
            resultText += `Total Flow: ${results.totalFlow.toFixed(1)} L/s<br>`;
            resultText += `Per Downpipe: ${results.flowPerPipe.toFixed(1)} L/s<br><br>`;
            
            resultText += `<strong>Recommended Size:</strong><br>`;
            if (type === 'rectangular') {
                resultText += `${results.recommendedSize.width}mm × ${results.recommendedSize.height}mm<br>`;
            } else {
                resultText += `${results.recommendedSize}mm diameter<br>`;
            }
            resultText += `Flow Capacity: ${results.capacity.toFixed(1)} L/s<br>`;
            resultText += `Flow Velocity: ${results.velocity.toFixed(1)} m/s<br><br>`;
            
            resultText += `<strong>System Details:</strong><br>`;
            resultText += `Head Loss: ${results.headLoss.toFixed(1)} kPa<br>`;
            resultText += `Fill Ratio: ${(results.fillRatio * 100).toFixed(0)}%`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${area}m² → ${type === 'rectangular' ? 
                `${results.recommendedSize.width}×${results.recommendedSize.height}mm` : 
                `${results.recommendedSize}mm Ø`}`);
        });

        function calculateDownpipe(area, rainfall, runoff, count, type, height, offsets) {
            const warnings = [];
            
            // Calculate total flow rate
            const totalFlow = (area * rainfall * runoff) / (3600 * 1000);
            const flowPerPipe = totalFlow / count;
            const designFlow = flowPerPipe * 1.2; // 20% safety factor
            
            // Select pipe size based on flow
            let recommendedSize;
            let capacity = 0;
            let velocity = 0;
            
            if (type === 'rectangular') {
                for (const size of standardSizes[type]) {
                    const area = (size.width * size.height) / 1000000; // m²
                    capacity = flowCoeff[type] * area * Math.sqrt(2 * 9.81 * height);
                    
                    if (capacity >= designFlow) {
                        recommendedSize = size;
                        velocity = designFlow / area;
                        break;
                    }
                }
                
                if (!recommendedSize) {
                    recommendedSize = standardSizes[type][standardSizes[type].length - 1];
                    warnings.push('Warning: Flow exceeds largest standard size capacity');
                }
            } else {
                for (const diameter of standardSizes[type]) {
                    const area = Math.PI * Math.pow(diameter/2000, 2);
                    capacity = flowCoeff[type] * area * Math.sqrt(2 * 9.81 * height);
                    
                    if (capacity >= designFlow) {
                        recommendedSize = diameter;
                        velocity = designFlow / area;
                        break;
                    }
                }
                
                if (!recommendedSize) {
                    recommendedSize = standardSizes[type][standardSizes[type].length - 1];
                    warnings.push('Warning: Flow exceeds largest standard size capacity');
                }
            }
            
            // Calculate head loss
            const headLoss = calculateHeadLoss(height, offsets, velocity);
            
            // Calculate fill ratio
            const fillRatio = designFlow / capacity;
            
            // Check for warnings
            if (velocity < 1.0) {
                warnings.push('Warning: Flow velocity below minimum 1.0 m/s');
            }
            if (velocity > 2.5) {
                warnings.push('Warning: Flow velocity exceeds maximum 2.5 m/s');
            }
            if (fillRatio > 0.33) {
                warnings.push('Warning: Fill ratio exceeds recommended 33%');
            }
            
            return {
                totalFlow,
                flowPerPipe,
                recommendedSize,
                capacity,
                velocity,
                headLoss,
                fillRatio,
                warnings
            };
        }

        function calculateHeadLoss(height, offsets, velocity) {
            // Simple head loss calculation
            const gravityLoss = height * 9.81;
            const offsetLoss = offsets * (0.5 * Math.pow(velocity, 2));
            return (gravityLoss + offsetLoss) / 1000; // Convert to kPa
        }

        function saveRecent(calculation) {
            const key = 'recentDownpipeCalculations';
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
            const key = 'recentDownpipeCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentDownpipeCalculations');
            
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

        // Show flow section by default and load recent calculations
        document.getElementById('flow-section').style.display = 'block';
        displayRecent();
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

