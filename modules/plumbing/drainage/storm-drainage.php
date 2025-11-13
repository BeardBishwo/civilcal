<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storm Drainage - AEC Calculator</title>
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
            <h2 class="text-center mb-4"><i class="fas fa-cloud-rain me-2"></i>Storm Drainage Calculator</h2>
            
            <form id="storm-drainage-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="roofArea">Roof Area (m²)</label>
                            <input type="number" class="form-control" id="roofArea" min="0" step="0.1">
                        </div>

                        <div class="form-group">
                            <label for="rainfallIntensity">Rainfall Intensity (mm/hr)</label>
                            <select class="form-control" id="rainfallIntensity">
                                <option value="50">50 mm/hr (Light)</option>
                                <option value="75">75 mm/hr (Moderate)</option>
                                <option value="100" selected>100 mm/hr (Heavy)</option>
                                <option value="150">150 mm/hr (Intense)</option>
                                <option value="200">200 mm/hr (Extreme)</option>
                                <option value="custom">Custom Value...</option>
                            </select>
                        </div>

                        <div id="customRainfallInput" style="display: none;">
                            <div class="form-group">
                                <label for="customRainfall">Custom Rainfall (mm/hr)</label>
                                <input type="number" class="form-control" id="customRainfall" min="0" step="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="gutterSlope">Gutter Slope (%)</label>
                            <select class="form-control" id="gutterSlope">
                                <option value="0.5">0.5% (1:200)</option>
                                <option value="1.0" selected>1.0% (1:100)</option>
                                <option value="1.5">1.5% (1:67)</option>
                                <option value="2.0">2.0% (1:50)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pipeMaterial">Pipe Material</label>
                            <select class="form-control" id="pipeMaterial">
                                <option value="pvc">PVC</option>
                                <option value="metal">Metal</option>
                                <option value="hdpe">HDPE</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Drainage</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Drainage Requirements</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Rainfall Categories</h6>
                            <small>
                                Light: 1-50 mm/hr<br>
                                Moderate: 51-75 mm/hr<br>
                                Heavy: 76-100 mm/hr<br>
                                Intense: 101-150 mm/hr<br>
                                Extreme: >150 mm/hr
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentStormCalculations"></div>
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
        // Toggle custom rainfall input
        document.getElementById('rainfallIntensity').addEventListener('change', function() {
            const customInput = document.getElementById('customRainfallInput');
            customInput.style.display = this.value === 'custom' ? 'block' : 'none';
        });

        // Calculate storm drainage requirements
        document.getElementById('storm-drainage-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const area = parseFloat(document.getElementById('roofArea').value);
            const intensitySelect = document.getElementById('rainfallIntensity');
            const intensity = intensitySelect.value === 'custom' 
                ? parseFloat(document.getElementById('customRainfall').value)
                : parseFloat(intensitySelect.value);
            const slope = parseFloat(document.getElementById('gutterSlope').value);
            const material = document.getElementById('pipeMaterial').value;
            
            if (!area || area <= 0) {
                alert('Please enter a valid roof area');
                return;
            }
            
            if (!intensity || intensity <= 0) {
                alert('Please enter a valid rainfall intensity');
                return;
            }
            
            // Calculate flow rate (Q = C × i × A)
            const runoffCoeff = 0.95; // Typical for roof surfaces
            const flowRate = (runoffCoeff * intensity * area) / 3600; // Convert to L/s
            
            // Calculate sizes
            const sizes = calculateDrainageSizes(flowRate, slope, material);
            
            let resultText = `<strong>Design Parameters:</strong><br>`;
            resultText += `Roof Area: ${area.toFixed(1)} m²<br>`;
            resultText += `Rainfall: ${intensity} mm/hr<br>`;
            resultText += `Flow Rate: ${flowRate.toFixed(1)} L/s<br><br>`;
            
            resultText += `<strong>Required Sizes:</strong><br>`;
            resultText += `Downpipe: ${sizes.downpipe.mm}mm (${sizes.downpipe.inches}")<br>`;
            resultText += `Main Drain: ${sizes.mainDrain.mm}mm (${sizes.mainDrain.inches}")<br>`;
            resultText += `Gutter Width: ${sizes.gutter}mm<br>`;
            
            if (intensity > 150) {
                resultText += '<br><div class="alert alert-warning">Note: Extreme rainfall intensity - consider additional overflow provisions</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${area}m² @ ${intensity}mm/hr → ${sizes.downpipe.mm}mm`);
        });

        function calculateDrainageSizes(flowRate, slope, material) {
            // Downpipe sizing (vertical flow)
            let downpipeMM;
            if (flowRate <= 1) downpipeMM = 50;
            else if (flowRate <= 2.5) downpipeMM = 65;
            else if (flowRate <= 4.5) downpipeMM = 75;
            else if (flowRate <= 9.0) downpipeMM = 90;
            else if (flowRate <= 14.0) downpipeMM = 100;
            else downpipeMM = 150;
            
            // Main drain (using larger size due to partial flow)
            const mainDrainMM = Math.min(downpipeMM + 25, 150);
            
            // Gutter width (approximate)
            const gutterWidth = Math.max(100, Math.ceil(flowRate * 30));
            
            return {
                downpipe: {
                    mm: downpipeMM,
                    inches: (downpipeMM / 25.4).toFixed(1)
                },
                mainDrain: {
                    mm: mainDrainMM,
                    inches: (mainDrainMM / 25.4).toFixed(1)
                },
                gutter: gutterWidth
            };
        }

        function saveRecent(calculation) {
            const key = 'recentStormCalculations';
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
            const key = 'recentStormCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentStormCalculations');
            
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

