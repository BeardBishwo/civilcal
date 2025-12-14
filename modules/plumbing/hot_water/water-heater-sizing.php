<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Heater Sizing - AEC Calculator</title>
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
        }

        .calculator-wrapper {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow);
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .result-area {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 10px;
            display: none;
        }

        .recent-calculations {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 10px;
        }

        .btn-calculate {
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, #f093fb, #f5576c);
            border: none;
            border-radius: 50px;
            color: var(--light);
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            width: 100%;
        }

        .btn-calculate:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .back-button {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50px;
            color: var(--light);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            margin-top: 2rem;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            color: white;
            text-decoration: none;
        }

        .info-table {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4"><i class="fas fa-hot-tub me-2"></i>Water Heater Sizing</h2>
            
            <form id="water-heater-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="buildingType">Building Type</label>
                            <select class="form-control" id="buildingType">
                                <option value="residential">Residential</option>
                                <option value="hotel">Hotel/Motel</option>
                                <option value="office">Office Building</option>
                                <option value="hospital">Hospital</option>
                                <option value="restaurant">Restaurant</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="occupants">Number of Occupants</label>
                            <input type="number" class="form-control" id="occupants" min="1" step="1">
                        </div>

                        <div class="form-group">
                            <label for="peakHours">Peak Usage Hours</label>
                            <input type="number" class="form-control" id="peakHours" min="1" max="24" step="0.5" value="2">
                        </div>

                        <div class="form-group">
                            <label for="tempRise">Temperature Rise (°C)</label>
                            <input type="number" class="form-control" id="tempRise" min="0" step="1" value="45">
                        </div>

                        <div class="form-group">
                            <label for="heaterType">Heater Type</label>
                            <select class="form-control" id="heaterType">
                                <option value="electric">Electric</option>
                                <option value="gas">Gas</option>
                                <option value="heatPump">Heat Pump</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Heater Size</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Water Heater Requirements</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Hot Water Usage Guide</h6>
                            <small>
                                Residential: 60L/person/day<br>
                                Hotel: 100L/person/day<br>
                                Office: 15L/person/day<br>
                                Hospital: 200L/bed/day<br>
                                Restaurant: 40L/meal/day
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentHeaterCalculations"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <a href="<?php echo function_exists('app_base_url') ? app_base_url('modules/plumbing/index.php') : '../modules/plumbing/index.php'; ?>" class="back-button">
            <i class="fas fa-arrow-left me-2"></i>Back to Plumbing
        </a>
    </div>

    <script>
        document.getElementById('water-heater-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const buildingType = document.getElementById('buildingType').value;
            const occupants = parseInt(document.getElementById('occupants').value);
            const peakHours = parseFloat(document.getElementById('peakHours').value);
            const tempRise = parseFloat(document.getElementById('tempRise').value);
            const heaterType = document.getElementById('heaterType').value;
            
            if (!occupants || occupants <= 0) {
                showNotification('Please enter the number of occupants', 'info');
                return;
            }
            
            // Calculate daily consumption based on building type
            const dailyUsage = calculateDailyUsage(buildingType, occupants);
            const peakHourlyUsage = (dailyUsage * 0.3) / peakHours; // Assume 30% during peak hours
            
            // Calculate required heater capacity
            const capacity = calculateHeaterCapacity(peakHourlyUsage, tempRise, heaterType);
            
            let resultText = `<strong>Daily Hot Water Demand:</strong><br>`;
            resultText += `${dailyUsage.toFixed(0)} L/day<br><br>`;
            resultText += `<strong>Peak Hour Demand:</strong><br>`;
            resultText += `${peakHourlyUsage.toFixed(1)} L/hour<br><br>`;
            resultText += `<strong>Required Heater Capacity:</strong><br>`;
            
            if (heaterType === 'electric') {
                resultText += `${capacity.kW.toFixed(1)} kW<br>`;
                resultText += `Storage Tank: ${capacity.storage.toFixed(0)} L`;
            } else if (heaterType === 'gas') {
                resultText += `${capacity.btu.toFixed(0)} BTU/hr<br>`;
                resultText += `${capacity.kW.toFixed(1)} kW equivalent<br>`;
                resultText += `Storage Tank: ${capacity.storage.toFixed(0)} L`;
            } else {
                resultText += `${capacity.kW.toFixed(1)} kW<br>`;
                resultText += `COP: ${capacity.cop}<br>`;
                resultText += `Storage Tank: ${capacity.storage.toFixed(0)} L`;
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${buildingType}: ${occupants} occ → ${capacity.kW.toFixed(1)}kW`);
        });

        function calculateDailyUsage(type, occupants) {
            const usagePerPerson = {
                'residential': 60,
                'hotel': 100,
                'office': 15,
                'hospital': 200,
                'restaurant': 40
            };
            
            return occupants * usagePerPerson[type];
        }

        function calculateHeaterCapacity(peakHourlyUsage, tempRise, type) {
            const specificHeat = 4.186; // kJ/kg°C
            const density = 1; // kg/L
            
            // Base power in kW
            const baseKW = (peakHourlyUsage * specificHeat * density * tempRise) / (3600);
            
            let capacity = {
                kW: baseKW,
                storage: peakHourlyUsage * 1.2 // 20% safety factor
            };
            
            if (type === 'gas') {
                capacity.btu = baseKW * 3412.14; // Convert kW to BTU/hr
            } else if (type === 'heatPump') {
                capacity.cop = 3.5; // Typical COP for heat pumps
                capacity.kW = baseKW / capacity.cop;
            }
            
            return capacity;
        }

        function saveRecent(calculation) {
            const key = 'recentHeaterCalculations';
            let recent = JSON.parse(localStorage.getItem(key) || '[]');
            recent.unshift({
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            recent = recent.slice(0, 5); // Keep last 5 calculations
            localStorage.setItem(key, JSON.stringify(recent));
            displayRecent();
        }

        function displayRecent() {
            const key = 'recentHeaterCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentHeaterCalculations');
            
            if (recent.length === 0) {
                container.innerHTML = '<p class="text-muted">No recent calculations</p>';
                return;
            }
            
            container.innerHTML = recent.map(item => `
                <div class="card bg-dark mb-2">
                    <div class="card-body">
                        <div class="small">${item.calculation}</div>
                        <div class="small text-muted">${item.timestamp}</div>
                    </div>
                </div>
            `).join('');
        }

        // Load recent calculations on page load
        displayRecent();
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

