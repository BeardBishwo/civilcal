<?php
// modules/fire/hydraulics/hazen-williams.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hazen-Williams Calculator - Fire Protection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e74c3c;
            --secondary: #c0392b;
            --accent: #f39c12;
            --dark: #1a202c;
            --light: #f7fafc;
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
            display: none;
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
            text-align: left;
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
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-tachometer-alt me-3"></i>Hazen-Williams Pressure Loss Calculator</h1>
            <form id="hazen-williams-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="flow-rate">Flow Rate (GPM)</label>
                            <input type="number" id="flow-rate" class="form-control" step="0.1" required>
                        </div>
                        <div class="form-group">
                            <label for="pipe-diameter">Pipe Diameter (inches)</label>
                            <input type="number" id="pipe-diameter" class="form-control" step="0.1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pipe-length">Pipe Length (feet)</label>
                            <input type="number" id="pipe-length" class="form-control" step="1" required>
                        </div>
                        <div class="form-group">
                            <label for="c-factor">C-Factor</label>
                            <input type="number" id="c-factor" class="form-control" step="1" value="120">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-calculate">Calculate Pressure Loss</button>
            </form>
            
            <div class="info-box">
                <h5><i class="fas fa-info-circle me-2"></i>Hazen-Williams Formula</h5>
                <p><strong>Pf = (4.52 × Q^1.85 × L) / (C^1.85 × D^4.87)</strong></p>
                <p>Where: Pf = Pressure loss (PSI), Q = Flow (GPM), L = Length (ft), C = C-factor, D = Diameter (inches)</p>
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Calculation Results</h3>
                <div id="result"></div>
            </div>
        </div>
        <a href="../../../index.php" class="back-link">Back to Fire Protection Toolkit</a>
    </div>

    <script>
        document.getElementById('hazen-williams-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const flow = parseFloat(document.getElementById('flow-rate').value);
            const diameter = parseFloat(document.getElementById('pipe-diameter').value);
            const length = parseFloat(document.getElementById('pipe-length').value);
            const cFactor = parseFloat(document.getElementById('c-factor').value);

            if (!flow || !diameter || !length) {
                showNotification('Please enter all required values.', 'info');
                return;
            }
            
            // Hazen-Williams formula: Pf = (4.52 × Q^1.85 × L) / (C^1.85 × D^4.87)
            const numerator = 4.52 * Math.pow(flow, 1.85) * length;
            const denominator = Math.pow(cFactor, 1.85) * Math.pow(diameter, 4.87);
            const pressureLoss = numerator / denominator;
            
            // Calculate velocity
            const area = Math.PI * Math.pow(diameter / 2, 2) / 144; // ft²
            const velocity = (flow / 448.831) / area; // fps
            
            const resultHTML = `
                <p><strong>Flow Rate:</strong> ${flow} GPM</p>
                <p><strong>Pipe Diameter:</strong> ${diameter} inch</p>
                <p><strong>Pipe Length:</strong> ${length} feet</p>
                <p><strong>C-Factor:</strong> ${cFactor}</p>
                <hr>
                <p><strong>Pressure Loss:</strong> ${pressureLoss.toFixed(2)} PSI</p>
                <p><strong>Pressure Loss per 100ft:</strong> ${(pressureLoss / length * 100).toFixed(2)} PSI</p>
                <p><strong>Velocity:</strong> ${velocity.toFixed(2)} FPS</p>
                <hr>
                <p><strong>Status:</strong> ${velocity <= 15 ? '<span style="color: #2ecc71;">Velocity Acceptable</span>' : '<span style="color: #e74c3c;">Velocity Too High</span>'}</p>
            `;
            
            document.getElementById('result').innerHTML = resultHTML;
            document.getElementById('result-area').style.display = 'block';
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
