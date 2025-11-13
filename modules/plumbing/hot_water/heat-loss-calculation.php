<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heat Loss Calculation - AEC Calculator</title>
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
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4"><i class="fas fa-temperature-high me-2"></i>Heat Loss Calculator</h2>
            
            <form id="heat-loss-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('pipe')">
                            <h5><i class="fas fa-pipe me-2"></i>Pipe Heat Loss</h5>
                        </div>
                        <div id="pipe-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="pipeSize">Pipe Size (mm)</label>
                                <select class="form-control" id="pipeSize">
                                    <option value="15">15mm (1/2")</option>
                                    <option value="20">20mm (3/4")</option>
                                    <option value="25">25mm (1")</option>
                                    <option value="32">32mm (1-1/4")</option>
                                    <option value="40">40mm (1-1/2")</option>
                                    <option value="50">50mm (2")</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="pipeLength">Pipe Length (m)</label>
                                <input type="number" class="form-control" id="pipeLength" min="0" step="0.1">
                            </div>

                            <div class="form-group">
                                <label for="insulationType">Insulation Type</label>
                                <select class="form-control" id="insulationType">
                                    <option value="none">No Insulation</option>
                                    <option value="foam">Foam (λ=0.035)</option>
                                    <option value="mineral">Mineral Wool (λ=0.040)</option>
                                    <option value="cellular">Cellular Glass (λ=0.045)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="insulationThickness">Insulation Thickness (mm)</label>
                                <input type="number" class="form-control" id="insulationThickness" min="0" step="1" value="25">
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('tank')">
                            <h5><i class="fas fa-box me-2"></i>Tank Heat Loss</h5>
                        </div>
                        <div id="tank-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="tankVolume">Tank Volume (L)</label>
                                <input type="number" class="form-control" id="tankVolume" min="0" step="10">
                            </div>

                            <div class="form-group">
                                <label for="tankInsulation">Tank Insulation R-Value</label>
                                <select class="form-control" id="tankInsulation">
                                    <option value="2">Basic (R-2)</option>
                                    <option value="4">Good (R-4)</option>
                                    <option value="6">Better (R-6)</option>
                                    <option value="8">Best (R-8)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="waterTemp">Hot Water Temperature (°C)</label>
                            <input type="number" class="form-control" id="waterTemp" min="0" step="1" value="60">
                        </div>

                        <div class="form-group">
                            <label for="ambientTemp">Ambient Temperature (°C)</label>
                            <input type="number" class="form-control" id="ambientTemp" min="0" step="1" value="20">
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Heat Loss</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Heat Loss Results</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Insulation Guide</h6>
                            <small>
                                Recommended minimum thickness:<br>
                                15-25mm pipes: 25mm insulation<br>
                                32-50mm pipes: 30mm insulation<br>
                                >50mm pipes: 40mm insulation
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentHeatLossCalculations"></div>
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
        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        document.getElementById('heat-loss-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const waterTemp = parseFloat(document.getElementById('waterTemp').value);
            const ambientTemp = parseFloat(document.getElementById('ambientTemp').value);
            let totalLoss = 0;
            let resultText = '';
            
            // Pipe heat loss calculation
            const pipeLength = parseFloat(document.getElementById('pipeLength').value);
            if (pipeLength > 0) {
                const pipeSize = parseFloat(document.getElementById('pipeSize').value);
                const insulationType = document.getElementById('insulationType').value;
                const insulationThickness = parseFloat(document.getElementById('insulationThickness').value);
                
                const pipeLoss = calculatePipeHeatLoss(
                    pipeSize,
                    pipeLength,
                    waterTemp,
                    ambientTemp,
                    insulationType,
                    insulationThickness
                );
                
                totalLoss += pipeLoss;
                resultText += `<strong>Pipe Heat Loss:</strong><br>`;
                resultText += `${pipeLoss.toFixed(1)} Watts<br>`;
                resultText += `${(pipeLoss * 0.0036).toFixed(2)} kWh/hr<br><br>`;
            }
            
            // Tank heat loss calculation
            const tankVolume = parseFloat(document.getElementById('tankVolume').value);
            if (tankVolume > 0) {
                const tankRValue = parseFloat(document.getElementById('tankInsulation').value);
                const tankLoss = calculateTankHeatLoss(
                    tankVolume,
                    tankRValue,
                    waterTemp,
                    ambientTemp
                );
                
                totalLoss += tankLoss;
                resultText += `<strong>Tank Heat Loss:</strong><br>`;
                resultText += `${tankLoss.toFixed(1)} Watts<br>`;
                resultText += `${(tankLoss * 0.0036).toFixed(2)} kWh/hr<br><br>`;
            }
            
            resultText += `<strong>Total Heat Loss:</strong><br>`;
            resultText += `${totalLoss.toFixed(1)} Watts<br>`;
            resultText += `${(totalLoss * 0.0036).toFixed(2)} kWh/hr<br>`;
            resultText += `${(totalLoss * 0.0036 * 24).toFixed(2)} kWh/day`;
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${totalLoss.toFixed(1)}W (${(totalLoss * 0.0036 * 24).toFixed(1)} kWh/day)`);
        });

        function calculatePipeHeatLoss(size, length, waterTemp, ambientTemp, insulation, thickness) {
            const tempDiff = waterTemp - ambientTemp;
            const diameter = size / 1000; // Convert mm to m
            const circumference = Math.PI * diameter;
            
            // Thermal conductivity (W/mK)
            let k;
            switch(insulation) {
                case 'foam': k = 0.035; break;
                case 'mineral': k = 0.040; break;
                case 'cellular': k = 0.045; break;
                default: k = 15; // Bare pipe (approximate)
            }
            
            // Heat loss per meter
            let U;
            if (insulation === 'none') {
                U = 8; // Approximate for bare pipe
            } else {
                const r2 = diameter/2 + thickness/1000;
                U = 2 * Math.PI * k / Math.log(2 * r2 / diameter);
            }
            
            return U * length * tempDiff;
        }

        function calculateTankHeatLoss(volume, rValue, waterTemp, ambientTemp) {
            const tempDiff = waterTemp - ambientTemp;
            
            // Approximate surface area based on volume
            const height = Math.pow(volume/1000, 1/3) * 2; // Assume height = 2 × radius
            const radius = Math.sqrt(volume/(1000 * Math.PI * height));
            const surfaceArea = 2 * Math.PI * radius * (radius + height);
            
            // Heat loss through tank walls
            return surfaceArea * tempDiff / rValue;
        }

        function saveRecent(calculation) {
            const key = 'recentHeatLossCalculations';
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
            const key = 'recentHeatLossCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentHeatLossCalculations');
            
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
        
        // Show pipe section by default
        document.getElementById('pipe-section').style.display = 'block';
    </script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

