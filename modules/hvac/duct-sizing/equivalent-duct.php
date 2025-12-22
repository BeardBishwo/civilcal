<?php
// modules/hvac/duct-sizing/equivalent-duct.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equivalent Round Duct Calculator - HVAC Engineering Tools</title>
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
            --success-color: #e67e22;
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
            background: linear-gradient(45deg, #e67e22, #d35400);
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
            <h1><i class="fas fa-circle"></i> Equivalent Round Duct Calculator</h1>
            <p>Convert rectangular duct dimensions to equivalent round duct diameter</p>
        </div>

        <div class="calculator-grid">
            <!-- Equivalent Round Duct -->
            <div class="calculator-form">
                <h3>Equivalent Round Duct Calculation</h3>
                <form id="equivalent-duct-form">
                    <div class="form-group">
                        <label for="rectWidth">Width (inches)</label>
                        <div class="input-group">
                            <span class="input-group-text">in</span>
                            <input type="number" class="form-control" id="rectWidth" placeholder="Enter width" step="0.1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="rectHeight">Height (inches)</label>
                        <div class="input-group">
                            <span class="input-group-text">in</span>
                            <input type="number" class="form-control" id="rectHeight" placeholder="Enter height" step="0.1" required>
                        </div>
                    </div>

                    <div class="formula-info">
                        <strong>Calculation Methods:</strong><br>
                        <strong>Hydraulic Method:</strong><br>
                        de = (4 × A) / P<br>
                        <small>Where A = area, P = perimeter</small><br><br>
                        <strong>Alternative Method:</strong><br>
                        de = 1.3 × (a × b)^0.625 / (a + b)^0.25<br>
                        <small>Where a = width, b = height</small>
                    </div>

                    <button type="submit" class="btn-calculate">
                        <i class="fas fa-calculator"></i> Calculate Equivalent Round
                    </button>
                </form>
            </div>

            <!-- Results Section -->
            <div class="results-section">
                <div class="result-card" id="equivalentDuctResult">
                    <h4><i class="fas fa-circle"></i>Equivalent Round Duct Results</h4>
                    <div id="equivalentDuctOutput"></div>
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

        // Equivalent duct calculation function
        document.getElementById('equivalent-duct-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const width = parseFloat(document.getElementById('rectWidth').value);
            const height = parseFloat(document.getElementById('rectHeight').value);
            
            if (!width || !height) {
                showNotification('Please enter both width and height', 'info');
                return;
            }
            
            // Calculate equivalent round diameter using hydraulic diameter formula
            const area = (width * height) / 144; // ft²
            const perimeter = 2 * (width + height) / 12; // feet
            const hydraulicDiameter = (4 * area) / perimeter; // feet
            const equivalentRound = hydraulicDiameter * 12; // inches
            
            // Alternative method: de = 1.3 × (a × b)^0.625 / (a + b)^0.25
            const altEquivalent = 1.3 * Math.pow(width * height, 0.625) / Math.pow(width + height, 0.25);
            
            const resultHTML = `
                <p><strong>Rectangular Duct:</strong> ${width}" × ${height}"</p>
                <p><strong>Cross-sectional Area:</strong> ${area.toFixed(3)} ft²</p>
                <p><strong>Hydraulic Diameter:</strong> ${hydraulicDiameter.toFixed(3)} ft</p>
                <hr style="border: 1px solid rgba(255,255,255,0.3); margin: 1rem 0;">
                <p><strong>Equivalent Round Diameter:</strong> ${equivalentRound.toFixed(1)} inches</p>
                <p><strong>Alternative Method:</strong> ${altEquivalent.toFixed(1)} inches</p>
                <p><strong>Recommended:</strong> ${((equivalentRound + altEquivalent) / 2).toFixed(1)} inches</p>
            `;
            
            document.getElementById('equivalentDuctOutput').innerHTML = resultHTML;
            document.getElementById('equivalentDuctResult').style.display = 'block';
            saveCalculation('Equivalent Duct', `${width}"×${height}" → ${equivalentRound.toFixed(1)}" round`);
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
