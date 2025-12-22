<?php
// modules/hvac/duct-sizing/pressure-drop.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duct Pressure Drop Calculator - HVAC Engineering Tools</title>
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
            --success-color: #f39c12;
        }

        body {
            background: linear-gradient(135deg, #000000, #000000, #000000);
            min-height: 100vh;
            color: var(--light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .calculator-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .calculator-form {
            background: var(--glass);
            backdrop-filter: blur(20px);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow);
        }

        .calculator-form h3 {
            color: #ffffff;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .input-group {
            display: flex;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--light);
            padding: 0.75rem;
            border-radius: 8px 0 0 8px;
            min-width: 120px;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-left: none;
            border-radius: 0 8px 8px 0;
            color: var(--light);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #ffffff;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }

        .form-control option {
            background: #2c3e50;
            color: white;
        }

        .btn-calculate {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(45deg, #f39c12, #e67e22);
            border: none;
            border-radius: 8px;
            color: var(--light);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-calculate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .results-section {
            grid-column: 1 / -1;
        }

        .result-card {
            background: var(--success-color);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 1rem;
            display: none;
            box-shadow: var(--shadow);
        }

        .result-card h4 {
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }

        .result-card p {
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .reference-table {
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: #ffffff;
            text-decoration: none;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            border: 1px solid #ffffff;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: #ffffff;
            color: var(--dark);
        }

        @media (max-width: 768px) {
            .calculator-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-gauge-high"></i> Duct Pressure Drop Calculator</h1>
            <p>Calculate pressure drop in duct systems based on air flow and duct characteristics</p>
        </div>

        <div class="calculator-grid">
            <!-- Duct Pressure Drop -->
            <div class="calculator-form">
                <h3>Duct Pressure Drop</h3>
                <form id="pressure-drop-form">
                    <div class="form-group">
                        <label for="pressureAirFlow">Air Flow (CFM)</label>
                        <div class="input-group">
                            <span class="input-group-text">CFM</span>
                            <input type="number" class="form-control" id="pressureAirFlow" placeholder="Enter air flow" step="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ductSize">Duct Size (inches)</label>
                        <div class="input-group">
                            <span class="input-group-text">in</span>
                            <input type="number" class="form-control" id="ductSize" placeholder="Enter duct diameter" step="0.1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ductLength">Duct Length (feet)</label>
                        <div class="input-group">
                            <span class="input-group-text">ft</span>
                            <input type="number" class="form-control" id="ductLength" placeholder="Enter duct length" step="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ductType">Duct Type</label>
                        <div class="input-group">
                            <span class="input-group-text">Type</span>
                            <select class="form-control" id="ductType" required>
                                <option value="galvanized">Galvanized Steel</option>
                                <option value="flexible">Flexible Duct</option>
                                <option value="fiberglass">Fiberglass Duct</option>
                            </select>
                        </div>
                    </div>

                    <div class="reference-table">
                        <strong>Friction Factors:</strong><br>
                        Galvanized Steel: 0.03<br>
                        Flexible Duct: 0.05<br>
                        Fiberglass Duct: 0.04<br>
                        <small>Based on Darcy-Weisbach equation</small>
                    </div>

                    <button type="submit" class="btn-calculate">
                        <i class="fas fa-calculator"></i> Calculate Pressure Drop
                    </button>
                </form>
            </div>

            <!-- Results Section -->
            <div class="results-section">
                <div class="result-card" id="pressureResult">
                    <h4><i class="fas fa-gauge-high"></i>Pressure Drop Results</h4>
                    <div id="pressureOutput"></div>
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="../index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to HVAC Tools
            </a>
        </div>
    </div>

    <script>
        // Save calculation to localStorage
        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentHVACCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            // Keep only last 10 calculations
            recent = recent.slice(0, 10);
            localStorage.setItem('recentHVACCalculations', JSON.stringify(recent));
        }

        // Duct pressure drop calculation function
        document.getElementById('pressure-drop-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const airFlow = parseFloat(document.getElementById('pressureAirFlow').value);
            const ductSize = parseFloat(document.getElementById('ductSize').value);
            const ductLength = parseFloat(document.getElementById('ductLength').value);
            const ductType = document.getElementById('ductType').value;
            
            if (!airFlow || !ductSize || !ductLength) {
                showNotification('Please enter all values', 'info');
                return;
            }
            
            // Friction factors based on duct type
            const frictionFactors = {
                'galvanized': 0.03,
                'flexible': 0.05,
                'fiberglass': 0.04
            };
            
            const frictionFactor = frictionFactors[ductType] || 0.03;
            
            // Calculate velocity
            const area = Math.PI * Math.pow(ductSize / 24, 2); // ft²
            const velocity = airFlow / area; // FPM
            
            // Calculate pressure drop (simplified Darcy-Weisbach)
            const velocityPressure = Math.pow(velocity / 4005, 2); // inches WC
            const pressureDrop = (frictionFactor * (ductLength / (ductSize / 12)) * velocityPressure).toFixed(4);
            
            const resultHTML = `
                <p><strong>Air Flow:</strong> ${airFlow} CFM</p>
                <p><strong>Duct Size:</strong> ${ductSize} inches</p>
                <p><strong>Duct Length:</strong> ${ductLength} feet</p>
                <p><strong>Duct Type:</strong> ${ductType.replace(/([A-Z])/g, ' $1').trim()}</p>
                <p><strong>Velocity:</strong> ${velocity.toFixed(0)} FPM</p>
                <p><strong>Friction Factor:</strong> ${frictionFactor}</p>
                <hr style="border: 1px solid rgba(255,255,255,0.3); margin: 1rem 0;">
                <p><strong>Pressure Drop:</strong> ${pressureDrop} inches WC</p>
                <p><strong>Pressure Drop:</strong> ${(pressureDrop * 249.089).toFixed(2)} Pa</p>
            `;
            
            document.getElementById('pressureOutput').innerHTML = resultHTML;
            document.getElementById('pressureResult').style.display = 'block';
            saveCalculation('Duct Pressure', `${airFlow}CFM in ${ductSize}" → ${pressureDrop}" WC drop`);
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
