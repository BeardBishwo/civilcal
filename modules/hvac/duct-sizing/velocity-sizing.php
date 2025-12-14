<?php
// modules/hvac/duct-sizing/velocity-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duct Sizing by Velocity Calculator - HVAC Engineering Tools</title>
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
            --success-color: #1abc9c;
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
            background: linear-gradient(45deg, #1abc9c, #16a085);
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

        .status-good { color: #2ecc71; }
        .status-warning { color: #f39c12; }
        .status-bad { color: #e74c3c; }

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
            <h1><i class="fas fa-ruler"></i> Duct Sizing by Velocity</h1>
            <p>Calculate duct size based on air flow and velocity requirements</p>
        </div>

        <div class="calculator-grid">
            <!-- Duct Sizing by Velocity -->
            <div class="calculator-form">
                <h3>Duct Sizing by Velocity</h3>
                <form id="velocity-sizing-form">
                    <div class="form-group">
                        <label for="ductAirFlow">Air Flow (CFM)</label>
                        <div class="input-group">
                            <span class="input-group-text">CFM</span>
                            <input type="number" class="form-control" id="ductAirFlow" placeholder="Enter air flow" step="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ductVelocity">Velocity (FPM)</label>
                        <div class="input-group">
                            <span class="input-group-text">FPM</span>
                            <input type="number" class="form-control" id="ductVelocity" placeholder="Enter velocity" step="10" value="800" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="aspectRatio">Aspect Ratio</label>
                        <div class="input-group">
                            <span class="input-group-text">Ratio</span>
                            <select class="form-control" id="aspectRatio" required>
                                <option value="1">1:1 (Square)</option>
                                <option value="2">2:1</option>
                                <option value="3">3:1</option>
                                <option value="4">4:1</option>
                            </select>
                        </div>
                    </div>

                    <div class="reference-table">
                        <strong>Recommended Velocities:</strong><br>
                        Main Ducts: 1000-1500 FPM<br>
                        Branch Ducts: 600-900 FPM<br>
                        Low Velocity: 400-600 FPM<br>
                        Return Air: 500-800 FPM<br>
                        Avoid > 2000 FPM (noise)
                    </div>

                    <button type="submit" class="btn-calculate">
                        <i class="fas fa-calculator"></i> Calculate Duct Size
                    </button>
                </form>
            </div>

            <!-- Results Section -->
            <div class="results-section">
                <div class="result-card" id="velocityResult">
                    <h4><i class="fas fa-ruler"></i>Duct Sizing Results</h4>
                    <div id="velocityOutput"></div>
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

        // Duct sizing by velocity function
        document.getElementById('velocity-sizing-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const airFlow = parseFloat(document.getElementById('ductAirFlow').value);
            const velocity = parseFloat(document.getElementById('ductVelocity').value);
            const aspectRatio = parseFloat(document.getElementById('aspectRatio').value);
            
            if (!airFlow || !velocity) {
                showNotification('Please enter air flow and velocity', 'info');
                return;
            }
            
            // Calculate duct area: A = Q / V
            const area = airFlow / velocity; // ft²
            
            // Calculate equivalent round diameter
            const diameter = Math.sqrt(area * 4 / Math.PI) * 12; // inches
            
            // Calculate rectangular dimensions based on aspect ratio
            const height = Math.sqrt(area / aspectRatio) * 12; // inches
            const width = height * aspectRatio; // inches
            
            // Check velocity limits
            let velocityStatus = 'Good';
            let statusClass = 'status-good';
            if (velocity > 2000) {
                velocityStatus = 'Too high - noise and pressure issues';
                statusClass = 'status-bad';
            } else if (velocity < 600) {
                velocityStatus = 'Low - potential dust settlement';
                statusClass = 'status-warning';
            }
            
            const resultHTML = `
                <p><strong>Air Flow:</strong> ${airFlow} CFM</p>
                <p><strong>Velocity:</strong> ${velocity} FPM</p>
                <p><strong>Required Area:</strong> ${area.toFixed(3)} ft²</p>
                <p><strong>Equivalent Round:</strong> ${diameter.toFixed(1)} inches</p>
                <p><strong>Rectangular Size:</strong> ${width.toFixed(1)}" × ${height.toFixed(1)}"</p>
                <p><strong>Aspect Ratio:</strong> ${aspectRatio}:1</p>
                <hr style="border: 1px solid rgba(255,255,255,0.3); margin: 1rem 0;">
                <p><strong>Velocity Status:</strong> <span class="${statusClass}">${velocityStatus}</span></p>
            `;
            
            document.getElementById('velocityOutput').innerHTML = resultHTML;
            document.getElementById('velocityResult').style.display = 'block';
            saveCalculation('Duct Sizing', `${airFlow}CFM @ ${velocity}FPM → ${width.toFixed(0)}"×${height.toFixed(0)}"`);
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
