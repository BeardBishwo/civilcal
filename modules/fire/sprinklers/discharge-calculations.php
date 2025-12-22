<?php
// modules/fire/sprinklers/discharge-calculations.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sprinkler Discharge Calculator - Fire Protection</title>
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

        .k-factor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .k-factor-option {
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
        }

        .k-factor-option:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .k-factor-option.selected {
            background: var(--accent);
            color: var(--dark);
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-water me-3"></i>Sprinkler Discharge Calculator</h1>
            <form id="discharge-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sprinkler K-Factor</label>
                            <div class="k-factor-grid">
                                <div class="k-factor-option" data-value="5.6">K-5.6</div>
                                <div class="k-factor-option" data-value="8.0">K-8.0</div>
                                <div class="k-factor-option" data-value="11.2">K-11.2</div>
                                <div class="k-factor-option" data-value="14.0">K-14.0</div>
                                <div class="k-factor-option" data-value="16.8">K-16.8</div>
                                <div class="k-factor-option" data-value="25.2">K-25.2</div>
                            </div>
                            <input type="hidden" id="k-factor" value="11.2">
                        </div>
                        <div class="form-group">
                            <label for="operating-pressure">Operating Pressure (PSI)</label>
                            <input type="number" id="operating-pressure" class="form-control" step="0.1" value="7" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="required-density">Required Density (gpm/ft²)</label>
                            <input type="number" id="required-density" class="form-control" step="0.01" value="0.10">
                        </div>
                        <div class="form-group">
                            <label for="design-area">Design Area (ft²)</label>
                            <input type="number" id="design-area" class="form-control" step="1" value="1500">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-calculate">Calculate Discharge</button>
            </form>
            
            <div class="info-box">
                <h5><i class="fas fa-info-circle me-2"></i>Discharge Formula</h5>
                <p><strong>Q = K × √P</strong> where Q = Flow (gpm), K = K-factor, P = Pressure (PSI)</p>
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Discharge Results</h3>
                <div id="result"></div>
            </div>
        </div>
        <a href="../../../index.php" class="back-link">Back to Fire Protection Toolkit</a>
    </div>

    <script>
        // K-factor selection
        const kFactorOptions = document.querySelectorAll('.k-factor-option');
        const kFactorInput = document.getElementById('k-factor');
        
        kFactorOptions.forEach(option => {
            option.addEventListener('click', function() {
                kFactorOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                kFactorInput.value = this.dataset.value;
            });
        });
        
        // Select K-11.2 by default
        document.querySelector('.k-factor-option[data-value="11.2"]').classList.add('selected');

        document.getElementById('discharge-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const kFactor = parseFloat(document.getElementById('k-factor').value);
            const pressure = parseFloat(document.getElementById('operating-pressure').value);
            const requiredDensity = parseFloat(document.getElementById('required-density').value);
            const designArea = parseFloat(document.getElementById('design-area').value);

            if (isNaN(pressure)) {
                showNotification('Please enter valid pressure value.', 'info');
                return;
            }
            
            // Flow calculation: Q = K × √P
            const flow = kFactor * Math.sqrt(pressure);
            const flowLPM = flow * 3.78541; // Convert to L/min
            
            // Calculate coverage area
            const coverageArea = flow / requiredDensity;
            
            // Check minimum requirements
            const minFlow = 15; // gpm for light hazard
            const meetsMinFlow = flow >= minFlow;
            const meetsDensity = coverageArea <= designArea;
            
            let flowStatus = meetsMinFlow ? 'Adequate' : 'Inadequate';
            let densityStatus = meetsDensity ? 'Meets Requirement' : 'Exceeds Area';
            
            // Calculate velocity (simplified)
            const sprinklerOrifice = (kFactor / 100) * 2; // Rough orifice diameter in inches
            const areaOrifice = Math.PI * Math.pow(sprinklerOrifice / 2, 2) / 144; // ft²
            const velocity = (flow / 448.831) / areaOrifice; // fps
            
            const resultHTML = `
                <div style="text-align: left;">
                    <p><strong>K-Factor:</strong> ${kFactor}</p>
                    <p><strong>Operating Pressure:</strong> ${pressure} PSI</p>
                    <p><strong>Required Density:</strong> ${requiredDensity} gpm/ft²</p>
                    <p><strong>Design Area:</strong> ${designArea} ft²</p>
                    <hr>
                    <p><strong>Flow Rate:</strong> ${flow.toFixed(2)} GPM</p>
                    <p><strong>Flow Rate:</strong> ${flowLPM.toFixed(2)} L/min</p>
                    <p><strong>Coverage Area:</strong> ${coverageArea.toFixed(0)} ft²</p>
                    <p><strong>Estimated Velocity:</strong> ${velocity.toFixed(1)} fps</p>
                    <hr>
                    <p><strong>Flow Status:</strong> <span style="color: ${meetsMinFlow ? '#2ecc71' : '#e74c3c'};">${flowStatus}</span></p>
                    <p><strong>Density Check:</strong> <span style="color: ${meetsDensity ? '#2ecc71' : '#e74c3c'};">${densityStatus}</span></p>
                    <p class="small text-muted">Formula: Q = K × √P</p>
                </div>
            `;
            
            document.getElementById('result').innerHTML = resultHTML;
            document.getElementById('result-area').style.display = 'block';
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
