<?php
// modules/hvac/load-calculation/cooling-load.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cooling Load Calculator - HVAC Engineering Tools</title>
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
            --success-color: #2ecc71;
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
            background: linear-gradient(45deg, #f093fb, #f5576c);
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
            <h1><i class="fas fa-snowflake"></i> Cooling Load Calculator</h1>
            <p>Calculate cooling load estimation for different room types and occupancy conditions</p>
        </div>

        <div class="calculator-grid">
            <!-- Cooling Load Estimation -->
            <div class="calculator-form">
                <h3>Cooling Load Estimation</h3>
                <form id="cooling-load-form">
                    <div class="form-group">
                        <label for="roomArea">Area (m²)</label>
                        <div class="input-group">
                            <span class="input-group-text">Area</span>
                            <input type="number" class="form-control" id="roomArea" placeholder="Enter area" step="0.1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="roomType">Room Type</label>
                        <div class="input-group">
                            <span class="input-group-text">Room Type</span>
                            <select class="form-control" id="roomType" required>
                                <option value="office">Office</option>
                                <option value="residential">Residential</option>
                                <option value="commercial">Commercial</option>
                                <option value="hospital">Hospital</option>
                                <option value="hotel">Hotel Room</option>
                                <option value="classroom">Classroom</option>
                                <option value="restaurant">Restaurant</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="roomOccupants">Occupants</label>
                        <div class="input-group">
                            <span class="input-group-text">People</span>
                            <input type="number" class="form-control" id="roomOccupants" placeholder="Number of people" step="1" value="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="equipmentLoad">Equipment Load (Watts)</label>
                        <div class="input-group">
                            <span class="input-group-text">Watts</span>
                            <input type="number" class="form-control" id="equipmentLoad" placeholder="Equipment power" step="10" value="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lightingLoad">Lighting Load (Watts)</label>
                        <div class="input-group">
                            <span class="input-group-text">Watts</span>
                            <input type="number" class="form-control" id="lightingLoad" placeholder="Lighting power" step="10" value="0" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-calculate">
                        <i class="fas fa-calculator"></i> Calculate Cooling Load
                    </button>
                </form>
            </div>

            <!-- Results Section -->
            <div class="results-section">
                <div class="result-card" id="coolingLoadResult">
                    <h4><i class="fas fa-snowflake"></i>Cooling Load Results</h4>
                    <div id="coolingLoadOutput"></div>
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

        // Load calculation function
        document.getElementById('cooling-load-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const area = parseFloat(document.getElementById('roomArea').value);
            const roomType = document.getElementById('roomType').value;
            const occupants = parseFloat(document.getElementById('roomOccupants').value) || 0;
            const equipmentLoad = parseFloat(document.getElementById('equipmentLoad').value) || 0;
            const lightingLoad = parseFloat(document.getElementById('lightingLoad').value) || 0;
            
            if (!area) {
                showNotification('Please enter room area', 'info');
                return;
            }
            
            // Cooling load factors (W/m²)
            const coolingFactors = {
                'office': 100,
                'residential': 120,
                'commercial': 150,
                'hospital': 180,
                'hotel': 130,
                'classroom': 140,
                'restaurant': 200
            };
            
            const baseLoad = area * (coolingFactors[roomType] || 120);
            
            // Occupant load (100W per person sensible + 50W latent)
            const occupantLoad = occupants * 150;
            
            // Total cooling load
            const totalLoad = baseLoad + occupantLoad + equipmentLoad + lightingLoad;
            const totalBTUH = totalLoad * 3.412;
            const tons = totalBTUH / 12000;
            
            const resultHTML = `
                <p><strong>Room Type:</strong> ${roomType.charAt(0).toUpperCase() + roomType.slice(1)}</p>
                <p><strong>Area:</strong> ${area} m²</p>
                <p><strong>Base Load:</strong> ${baseLoad.toFixed(0)} W</p>
                <p><strong>Occupant Load:</strong> ${occupantLoad.toFixed(0)} W</p>
                <p><strong>Equipment Load:</strong> ${equipmentLoad} W</p>
                <p><strong>Lighting Load:</strong> ${lightingLoad} W</p>
                <hr style="border: 1px solid rgba(255,255,255,0.3); margin: 1rem 0;">
                <p><strong>Total Cooling Load:</strong> ${totalLoad.toFixed(0)} W</p>
                <p><strong>Total Cooling Load:</strong> ${totalBTUH.toFixed(0)} BTU/hr</p>
                <p><strong>Required Capacity:</strong> ${tons.toFixed(2)} Tons</p>
            `;
            
            document.getElementById('coolingLoadOutput').innerHTML = resultHTML;
            document.getElementById('coolingLoadResult').style.display = 'block';
            saveCalculation('Cooling Load', `${area}m² ${roomType} → ${tons.toFixed(2)} tons`);
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
