<?php
// modules/hvac/duct-sizing/fitting-loss.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duct Fitting Loss Calculator - HVAC Engineering Tools</title>
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
            --success-color: #c0392b;
        }

        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
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
            color: #feca57;
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
            color: #f093fb;
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
            border-color: #f093fb;
            box-shadow: 0 0 15px rgba(240, 147, 251, 0.3);
        }

        .form-control option {
            background: #2c3e50;
            color: white;
        }

        .btn-calculate {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(45deg, #c0392b, #e74c3c);
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
            color: #f093fb;
            text-decoration: none;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            border: 1px solid #f093fb;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: #f093fb;
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
            <h1><i class="fas fa-link"></i> Duct Fitting Loss Calculator</h1>
            <p>Calculate pressure loss from duct fittings and components</p>
        </div>

        <div class="calculator-grid">
            <!-- Duct Fitting Loss -->
            <div class="calculator-form">
                <h3>Duct Fitting Loss</h3>
                <form id="fitting-loss-form">
                    <div class="form-group">
                        <label for="fittingVelocity">Velocity (FPM)</label>
                        <div class="input-group">
                            <span class="input-group-text">FPM</span>
                            <input type="number" class="form-control" id="fittingVelocity" placeholder="Enter velocity" step="10" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ductFittingType">Fitting Type</label>
                        <div class="input-group">
                            <span class="input-group-text">Type</span>
                            <select class="form-control" id="ductFittingType" required>
                                <option value="90elbow">90° Elbow</option>
                                <option value="45elbow">45° Elbow</option>
                                <option value="tee">Tee (straight)</option>
                                <option value="teebranch">Tee (branch)</option>
                                <option value="transition">Transition</option>
                                <option value="damper">Damper</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ductFittingCount">Number of Fittings</label>
                        <div class="input-group">
                            <span class="input-group-text">Count</span>
                            <input type="number" class="form-control" id="ductFittingCount" placeholder="Number of fittings" step="1" value="1" required>
                        </div>
                    </div>

                    <div class="reference-table">
                        <strong>C Values for Fittings:</strong><br>
                        90° Elbow: 0.5<br>
                        45° Elbow: 0.2<br>
                        Tee (straight): 0.6<br>
                        Tee (branch): 1.0<br>
                        Transition: 0.3<br>
                        Damper: 0.2<br>
                        <small>Formula: ΔP = C × (V/4005)²</small>
                    </div>

                    <button type="submit" class="btn-calculate">
                        <i class="fas fa-calculator"></i> Calculate Fitting Loss
                    </button>
                </form>
            </div>

            <!-- Results Section -->
            <div class="results-section">
                <div class="result-card" id="fittingLossResult">
                    <h4><i class="fas fa-link"></i>Fitting Loss Results</h4>
                    <div id="fittingLossOutput"></div>
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

        // Duct fitting loss calculation function
        document.getElementById('fitting-loss-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const velocity = parseFloat(document.getElementById('fittingVelocity').value);
            const fittingType = document.getElementById('ductFittingType').value;
            const count = parseFloat(document.getElementById('ductFittingCount').value);
            
            if (!velocity) {
                showNotification('Please enter velocity', 'info');
                return;
            }
            
            // C values for duct fittings
            const cValues = {
                '90elbow': 0.5,
                '45elbow': 0.2,
                'tee': 0.6,
                'teebranch': 1.0,
                'transition': 0.3,
                'damper': 0.2
            };
            
            const c = cValues[fittingType] || 0.5;
            const fittingName = {
                '90elbow': '90° Elbow',
                '45elbow': '45° Elbow',
                'tee': 'Tee (straight)',
                'teebranch': 'Tee (branch)',
                'transition': 'Transition',
                'damper': 'Damper'
            }[fittingType];
            
            // Pressure loss: ΔP = C × (V/4005)²
            const velocityPressure = Math.pow(velocity / 4005, 2);
            const fittingLoss = c * velocityPressure;
            const totalLoss = fittingLoss * count;
            
            const resultHTML = `
                <p><strong>Fitting Type:</strong> ${fittingName}</p>
                <p><strong>C-value:</strong> ${c}</p>
                <p><strong>Velocity:</strong> ${velocity} FPM</p>
                <p><strong>Velocity Pressure:</strong> ${velocityPressure.toFixed(4)}" WC</p>
                <p><strong>Loss per Fitting:</strong> ${fittingLoss.toFixed(4)}" WC</p>
                <p><strong>Number of Fittings:</strong> ${count}</p>
                <hr style="border: 1px solid rgba(255,255,255,0.3); margin: 1rem 0;">
                <p><strong>Total Loss:</strong> ${totalLoss.toFixed(4)}" WC</p>
                <p><strong>Total Loss:</strong> ${(totalLoss * 249.089).toFixed(2)} Pa</p>
            `;
            
            document.getElementById('fittingLossOutput').innerHTML = resultHTML;
            document.getElementById('fittingLossResult').style.display = 'block';
            saveCalculation('Duct Fitting Loss', `${fittingName} × ${count} → ${totalLoss.toFixed(4)}" WC`);
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
