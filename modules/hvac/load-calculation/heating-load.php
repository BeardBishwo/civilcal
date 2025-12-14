<?php
// modules/hvac/load-calculation/heating-load.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heating Load Calculator - HVAC Engineering Tools</title>
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
            --success-color: #e74c3c;
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
            background: linear-gradient(45deg, #e74c3c, #f39c12);
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
            <h1><i class="fas fa-fire"></i> Heating Load Calculator</h1>
            <p>Calculate heating load estimation based on room volume and construction type</p>
        </div>

        <div class="calculator-grid">
            <!-- Heating Load Estimation -->
            <div class="calculator-form">
                <h3>Heating Load Estimation</h3>
                <form id="heating-load-form">
                    <div class="form-group">
                        <label for="roomVolume">Volume (m³)</label>
                        <div class="input-group">
                            <span class="input-group-text">Volume</span>
                            <input type="number" class="form-control" id="roomVolume" placeholder="Enter volume" step="0.1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tempDifference">Design Temperature Difference (°C)</label>
                        <div class="input-group">
                            <span class="input-group-text">°C</span>
                            <input type="number" class="form-control" id="tempDifference" placeholder="Temperature difference" step="0.1" value="20" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="buildingConstruction">Building Construction Type</label>
                        <div class="input-group">
                            <span class="input-group-text">Construction</span>
                            <select class="form-control" id="buildingConstruction" required>
                                <option value="wellInsulated">Well Insulated</option>
                                <option value="average">Average Construction</option>
                                <option value="poorlyInsulated">Poorly Insulated</option>
                                <option value="singlePane">Single Pane Windows</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn-calculate">
                        <i class="fas fa-calculator"></i> Calculate Heating Load
                    </button>
                </form>
            </div>

            <!-- Results Section -->
            <div class="results-section">
                <div class="result-card" id="heatingLoadResult">
                    <h4><i class="fas fa-fire"></i>Heating Load Results</h4>
                    <div id="heatingLoadOutput"></div>
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

        // Heating load calculation function
        document.getElementById('heating-load-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const volume = parseFloat(document.getElementById('roomVolume').value);
            const tempDiff = parseFloat(document.getElementById('tempDifference').value);
            const construction = document.getElementById('buildingConstruction').value;
            
            if (!volume || !tempDiff) {
                showNotification('Please enter room volume and temperature difference', 'info');
                return;
            }
            
            // U-value factors based on construction (W/m²°C)
            const uValues = {
                'wellInsulated': 0.3,
                'average': 0.6,
                'poorlyInsulated': 1.0,
                'singlePane': 1.5
            };
            
            const uValue = uValues[construction] || 0.6;
            
            // Estimate surface area from volume (assuming cube: A = 6 × V^(2/3))
            const surfaceArea = 6 * Math.pow(volume, 2/3);
            
            // Heating load: Q = U × A × ΔT
            const heatingLoad = uValue * surfaceArea * tempDiff;
            const heatingBTUH = heatingLoad * 3.412;
            
            const resultHTML = `
                <p><strong>Construction Type:</strong> ${construction.replace(/([A-Z])/g, ' $1').trim()}</p>
                <p><strong>Volume:</strong> ${volume} m³</p>
                <p><strong>Estimated Surface Area:</strong> ${surfaceArea.toFixed(1)} m²</p>
                <p><strong>Temperature Difference:</strong> ${tempDiff}°C</p>
                <p><strong>U-value:</strong> ${uValue} W/m²°C</p>
                <hr style="border: 1px solid rgba(255,255,255,0.3); margin: 1rem 0;">
                <p><strong>Heating Load:</strong> ${heatingLoad.toFixed(0)} W</p>
                <p><strong>Heating Load:</strong> ${heatingBTUH.toFixed(0)} BTU/hr</p>
            `;
            
            document.getElementById('heatingLoadOutput').innerHTML = resultHTML;
            document.getElementById('heatingLoadResult').style.display = 'block';
            saveCalculation('Heating Load', `${volume}m³ → ${heatingLoad.toFixed(0)}W`);
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
