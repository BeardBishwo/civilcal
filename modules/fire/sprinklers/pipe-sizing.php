<?php
// modules/fire/sprinklers/pipe-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Protection Pipe Sizing Calculator - Fire Protection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e74c3c;
            --secondary: #c0392b;
            --accent: #f39c12;
            --dark: #000000;
            --light: #ffffff;
            --glass: rgba(255, 255, 255, 0.05);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        body {
            background: linear-gradient(135deg, #2c1810, #4a2c2a, #6b3410);
            min-height: 100vh;
            color: var(--light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }

        .calculator-wrapper {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow);
            margin-top: 3rem;
        }

        .calculator-wrapper h1 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: var(--accent);
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--light);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 15px rgba(243, 156, 18, 0.3);
        }

        .btn-calculate {
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 50px;
            color: var(--light);
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-calculate:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .result-area {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
            display: none; /* Hidden by default */
        }

        .result-area h3 {
            font-size: 1.5rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        #result {
            font-size: 1.3rem;
            font-weight: 700;
            line-height: 1.6;
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: var(--accent);
            text-decoration: none;
            font-size: 1.1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .info-box {
            background: rgba(52, 73, 94, 0.3);
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            border-left: 4px solid var(--accent);
        }

        .pipe-material-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .pipe-material-option {
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
        }

        .pipe-material-option:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .pipe-material-option.selected {
            background: var(--accent);
            color: var(--dark);
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-pipe me-3"></i>Fire Protection Pipe Sizing Calculator</h1>
            <form id="pipe-sizing-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="flow-rate">Flow Rate (GPM)</label>
                            <input type="number" id="flow-rate" class="form-control" step="0.1" required>
                        </div>
                        <div class="form-group">
                            <label for="velocity-limit">Velocity Limit (FPS)</label>
                            <input type="number" id="velocity-limit" class="form-control" step="0.1" value="15">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pipe Material</label>
                            <div class="pipe-material-grid">
                                <div class="pipe-material-option" data-value="steel">Steel (C=120)</div>
                                <div class="pipe-material-option" data-value="cpvc">CPVC (C=150)</div>
                                <div class="pipe-material-option" data-value="copper">Copper (C=150)</div>
                            </div>
                            <input type="hidden" id="pipe-material" value="steel">
                        </div>
                        <div class="form-group">
                            <label for="pressure-loss-limit">Max Pressure Loss (PSI/100ft)</label>
                            <input type="number" id="pressure-loss-limit" class="form-control" step="0.1" value="3">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-calculate">Calculate Pipe Size</button>
            </form>
            
            <div class="info-box">
                <h5><i class="fas fa-info-circle me-2"></i>NFPA Guidelines</h5>
                <p><strong>Maximum Velocity:</strong> 15 fps | <strong>Max Friction Loss:</strong> 3 PSI/100ft</p>
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Pipe Sizing Results</h3>
                <div id="result"></div>
            </div>
        </div>
        <a href="../../../index.php" class="back-link">Back to Fire Protection Toolkit</a>
    </div>

    <script>
        // Pipe material selection
        const materialOptions = document.querySelectorAll('.pipe-material-option');
        const materialInput = document.getElementById('pipe-material');
        
        materialOptions.forEach(option => {
            option.addEventListener('click', function() {
                materialOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                materialInput.value = this.dataset.value;
            });
        });
        
        // Select Steel by default
        document.querySelector('.pipe-material-option[data-value="steel"]').classList.add('selected');

        document.getElementById('pipe-sizing-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const flow = parseFloat(document.getElementById('flow-rate').value);
            const velocityLimit = parseFloat(document.getElementById('velocity-limit').value);
            const material = document.getElementById('pipe-material').value;
            const pressureLossLimit = parseFloat(document.getElementById('pressure-loss-limit').value);

            if (isNaN(flow)) {
                showNotification('Please enter flow rate.', 'info');
                return;
            }
            
            // Convert flow to cubic feet per second
            const flowCFS = flow / 448.831;
            
            // Calculate required area: A = Q / V
            const requiredArea = flowCFS / velocityLimit; // ft²
            const requiredAreaIn2 = requiredArea * 144; // in²
            
            // Calculate required diameter
            const requiredDiameter = Math.sqrt(requiredAreaIn2 / Math.PI) * 2;
            
            // Standard pipe sizes
            const pipeSizes = [1, 1.25, 1.5, 2, 2.5, 3, 3.5, 4, 5, 6, 8, 10, 12];
            let recommendedSize = pipeSizes.find(size => size >= requiredDiameter) || 12;
            
            // Calculate actual velocity
            const actualArea = Math.PI * Math.pow(recommendedSize / 2, 2) / 144; // ft²
            const actualVelocity = flowCFS / actualArea;
            
            // Calculate friction loss (simplified Hazen-Williams)
            const cFactor = material === 'steel' ? 120 : 150;
            const length = 100; // Per 100 feet
            const diameter = recommendedSize;
            const frictionLoss = (4.52 * Math.pow(flow, 1.85) * length) / (Math.pow(cFactor, 1.85) * Math.pow(diameter, 4.87));
            
            const resultHTML = `
                <div style="text-align: left;">
                    <p><strong>Flow Rate:</strong> ${flow} GPM</p>
                    <p><strong>Velocity Limit:</strong> ${velocityLimit} FPS</p>
                    <p><strong>Pipe Material:</strong> ${material}</p>
                    <p><strong>Max Pressure Loss:</strong> ${pressureLossLimit} PSI/100ft</p>
                    <hr>
                    <p><strong>Required Diameter:</strong> ${requiredDiameter.toFixed(2)} inches</p>
                    <p><strong>Recommended Pipe Size:</strong> ${recommendedSize} inch</p>
                    <p><strong>Actual Velocity:</strong> ${actualVelocity.toFixed(2)} FPS</p>
                    <p><strong>Friction Loss:</strong> ${frictionLoss.toFixed(2)} PSI/100ft</p>
                    <hr>
                    <p><strong>Velocity Status:</strong> <span style="color: ${actualVelocity <= velocityLimit ? '#2ecc71' : '#e74c3c'};">${actualVelocity <= velocityLimit ? 'Acceptable' : 'Too High'}</span></p>
                    <p><strong>Friction Loss Status:</strong> <span style="color: ${frictionLoss <= pressureLossLimit ? '#2ecc71' : '#e74c3c'};">${frictionLoss <= pressureLossLimit ? 'Acceptable' : 'Exceeds Limit'}</span></p>
                    <p><strong>NFPA Compliance:</strong> <span style="color: ${(actualVelocity <= velocityLimit && frictionLoss <= pressureLossLimit) ? '#2ecc71' : '#e74c3c'};">${(actualVelocity <= velocityLimit && frictionLoss <= pressureLossLimit) ? 'Compliant' : 'Non-Compliant'}</span></p>
                </div>
            `;
            
            document.getElementById('result').innerHTML = resultHTML;
            document.getElementById('result-area').style.display = 'block';
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
