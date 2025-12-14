<?php
// modules/hvac/load-calculation/infiltration.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infiltration Load Calculator - HVAC Engineering Tools</title>
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
            --success-color: #9b59b6;
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
            background: linear-gradient(45deg, #9b59b6, #8e44ad);
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

        .formula-info {
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
            opacity: 0.8;
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
            <h1><i class="fas fa-door-open"></i> Infiltration Load Calculator</h1>
            <p>Calculate infiltration load based on room volume, air changes, and temperature difference</p>
        </div>

        <div class="calculator-grid">
            <!-- Infiltration Load Estimation -->
            <div class="calculator-form">
                <h3>Infiltration Load Estimation</h3>
                <form id="infiltration-form">
                    <div class="form-group">
                        <label for="infilVolume">Room Volume (m³)</label>
                        <div class="input-group">
                            <span class="input-group-text">Volume</span>
                            <input type="number" class="form-control" id="infilVolume" placeholder="Enter volume" step="0.1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="infilACH">Air Changes per Hour (ACH)</label>
                        <div class="input-group">
                            <span class="input-group-text">ACH</span>
                            <input type="number" class="form-control" id="infilACH" placeholder="Air changes per hour" step="0.1" value="0.5" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="infilTempDiff">Temperature Difference (°C)</label>
                        <div class="input-group">
                            <span class="input-group-text">°C</span>
                            <input type="number" class="form-control" id="infilTempDiff" placeholder="Temperature difference" step="0.1" value="20" required>
                        </div>
                    </div>

                    <div class="formula-info">
                        <strong>Infiltration Load Formula:</strong><br>
                        Q = 0.33 × ACH × Volume × ΔT<br>
                        <small>Where Q is in BTU/hr, 0.33 is heat capacity of air</small>
                    </div>

                    <button type="submit" class="btn-calculate">
                        <i class="fas fa-calculator"></i> Calculate Infiltration
                    </button>
                </form>
            </div>

            <!-- Results Section -->
            <div class="results-section">
                <div class="result-card" id="infiltrationResult">
                    <h4><i class="fas fa-door-open"></i>Infiltration Load Results</h4>
                    <div id="infiltrationOutput"></div>
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

        // Infiltration load calculation function
        document.getElementById('infiltration-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const volume = parseFloat(document.getElementById('infilVolume').value);
            const ach = parseFloat(document.getElementById('infilACH').value);
            const tempDiff = parseFloat(document.getElementById('infilTempDiff').value);
            
            if (!volume || !ach || !tempDiff) {
                showNotification('Please enter all values', 'info');
                return;
            }
            
            // Infiltration load: Q = 0.33 × ACH × V × ΔT
            // Where: 0.33 is the heat capacity of air in BTU/hr per ft³°F
            const volumeCF = volume * 35.315; // Convert m³ to ft³
            const infiltrationBTUH = 0.33 * ach * volumeCF * (tempDiff * 1.8); // Convert °C to °F
            const infiltrationWatts = infiltrationBTUH / 3.412;
            
            const resultHTML = `
                <p><strong>Room Volume:</strong> ${volume} m³ (${volumeCF.toFixed(0)} ft³)</p>
                <p><strong>Air Changes per Hour:</strong> ${ach} ACH</p>
                <p><strong>Temperature Difference:</strong> ${tempDiff}°C (${(tempDiff * 1.8).toFixed(1)}°F)</p>
                <hr style="border: 1px solid rgba(255,255,255,0.3); margin: 1rem 0;">
                <p><strong>Infiltration Load:</strong> ${infiltrationBTUH.toFixed(0)} BTU/hr</p>
                <p><strong>Infiltration Load:</strong> ${infiltrationWatts.toFixed(0)} W</p>
                <p class="small text-light" style="margin-top: 1rem; opacity: 0.8;">
                    <strong>Formula:</strong> 0.33 × ACH × Volume × ΔT
                </p>
            `;
            
            document.getElementById('infiltrationOutput').innerHTML = resultHTML;
            document.getElementById('infiltrationResult').style.display = 'block';
            saveCalculation('Infiltration', `${volume}m³ @ ${ach}ACH → ${infiltrationWatts.toFixed(0)}W`);
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
