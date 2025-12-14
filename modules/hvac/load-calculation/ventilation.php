<?php
// modules/hvac/load-calculation/ventilation.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventilation Calculator - HVAC Engineering Tools</title>
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
            --success-color: #3498db;
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
            background: linear-gradient(45deg, #3498db, #2980b9);
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
            <h1><i class="fas fa-wind"></i> Ventilation Calculator</h1>
            <p>Calculate ventilation requirements based on occupancy and space type according to ASHRAE standards</p>
        </div>

        <div class="calculator-grid">
            <!-- Ventilation Requirements -->
            <div class="calculator-form">
                <h3>Ventilation Requirements</h3>
                <form id="ventilation-form">
                    <div class="form-group">
                        <label for="ventArea">Area (m²)</label>
                        <div class="input-group">
                            <span class="input-group-text">Area</span>
                            <input type="number" class="form-control" id="ventArea" placeholder="Enter area" step="0.1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ventOccupants">Number of Occupants</label>
                        <div class="input-group">
                            <span class="input-group-text">People</span>
                            <input type="number" class="form-control" id="ventOccupants" placeholder="Number of people" step="1" value="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ventSpaceType">Space Type</label>
                        <div class="input-group">
                            <span class="input-group-text">Space Type</span>
                            <select class="form-control" id="ventSpaceType" required>
                                <option value="office">Office Space</option>
                                <option value="conference">Conference Room</option>
                                <option value="classroom">Classroom</option>
                                <option value="retail">Retail Space</option>
                                <option value="restaurant">Restaurant</option>
                                <option value="kitchen">Kitchen</option>
                                <option value="bathroom">Bathroom</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="airChanges">Air Changes per Hour (ACH)</label>
                        <div class="input-group">
                            <span class="input-group-text">ACH</span>
                            <input type="number" class="form-control" id="airChanges" placeholder="Air changes per hour" step="0.1" value="4" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-calculate">
                        <i class="fas fa-calculator"></i> Calculate Ventilation
                    </button>
                </form>
            </div>

            <!-- Results Section -->
            <div class="results-section">
                <div class="result-card" id="ventilationResult">
                    <h4><i class="fas fa-wind"></i>Ventilation Results</h4>
                    <div id="ventilationOutput"></div>
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

        // Ventilation calculation function
        document.getElementById('ventilation-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const area = parseFloat(document.getElementById('ventArea').value);
            const occupants = parseFloat(document.getElementById('ventOccupants').value) || 0;
            const spaceType = document.getElementById('ventSpaceType').value;
            const airChanges = parseFloat(document.getElementById('airChanges').value);
            
            if (!area) {
                showNotification('Please enter area', 'info');
                return;
            }
            
            // Ventilation rates (CFM per person)
            const ventilationRates = {
                'office': 20,
                'conference': 15,
                'classroom': 15,
                'retail': 15,
                'restaurant': 20,
                'kitchen': 50,
                'bathroom': 50
            };
            
            // Area-based ventilation (CFM per m²)
            const areaRates = {
                'office': 0.06,
                'conference': 0.06,
                'classroom': 0.12,
                'retail': 0.12,
                'restaurant': 0.18,
                'kitchen': 0.36,
                'bathroom': 0.50
            };
            
            const ratePerPerson = ventilationRates[spaceType] || 20;
            const ratePerArea = areaRates[spaceType] || 0.06;
            
            // Calculate ventilation requirements
            const occupantVentilation = occupants * ratePerPerson;
            const areaVentilation = area * 10.764 * ratePerArea; // Convert m² to ft²
            const totalVentilation = Math.max(occupantVentilation, areaVentilation);
            
            // Air changes method
            const volume = area * 2.7; // Assume 2.7m ceiling height
            const airChangesCFM = (volume * 35.315 * airChanges) / 60; // Convert m³ to ft³ and per hour to per minute
            
            const resultHTML = `
                <p><strong>Space Type:</strong> ${spaceType.replace(/([A-Z])/g, ' $1').trim()}</p>
                <p><strong>Area:</strong> ${area} m²</p>
                <p><strong>Occupants:</strong> ${occupants}</p>
                <hr style="border: 1px solid rgba(255,255,255,0.3); margin: 1rem 0;">
                <p><strong>Ventilation by Occupants:</strong> ${occupantVentilation.toFixed(0)} CFM</p>
                <p><strong>Ventilation by Area:</strong> ${areaVentilation.toFixed(0)} CFM</p>
                <p><strong>Required Ventilation:</strong> ${totalVentilation.toFixed(0)} CFM</p>
                <p><strong>Air Changes Method:</strong> ${airChangesCFM.toFixed(0)} CFM</p>
                <p><strong>Recommended:</strong> ${Math.max(totalVentilation, airChangesCFM).toFixed(0)} CFM</p>
            `;
            
            document.getElementById('ventilationOutput').innerHTML = resultHTML;
            document.getElementById('ventilationResult').style.display = 'block';
            saveCalculation('Ventilation', `${area}m² ${spaceType} → ${Math.max(totalVentilation, airChangesCFM).toFixed(0)} CFM`);
        });
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
