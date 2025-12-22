<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soil Stack Sizing - AEC Calculator</title>
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
            background: linear-gradient(45deg, #ffffff, #ffffff);
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
            background: linear-gradient(45deg, #ffffff, #ffffff);
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

        .floor-inputs {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4"><i class="fas fa-sort-amount-down me-2"></i>Soil Stack Sizing</h2>
            
            <form id="soil-stack-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="buildingHeight">Building Height (floors)</label>
                            <select class="form-control" id="buildingHeight">
                                <option value="1">1 Floor</option>
                                <option value="2">2 Floors</option>
                                <option value="3">3 Floors</option>
                                <option value="4">4 Floors</option>
                                <option value="5">5 Floors</option>
                                <option value="6">6+ Floors</option>
                            </select>
                        </div>

                        <div id="floorInputs">
                            <!-- Dynamically populated -->
                        </div>

                        <div class="form-group">
                            <label for="stackMaterial">Stack Material</label>
                            <select class="form-control" id="stackMaterial">
                                <option value="pvc">PVC</option>
                                <option value="cast">Cast Iron</option>
                                <option value="copper">Copper DWV</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Stack Size</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Required Stack Size</h4>
                            <div id="result"></div>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentStackCalculations"></div>
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
        // Generate floor inputs based on building height
        document.getElementById('buildingHeight').addEventListener('change', function() {
            const floors = parseInt(this.value);
            const container = document.getElementById('floorInputs');
            let html = '';
            
            for (let i = 1; i <= floors; i++) {
                html += `
                    <div class="floor-inputs">
                        <h6>Floor ${i}</h6>
                        <div class="form-group">
                            <label for="fu_floor_${i}">Fixture Units on Floor</label>
                            <input type="number" class="form-control" id="fu_floor_${i}" min="0" step="1" value="0">
                        </div>
                    </div>
                `;
            }
            
            container.innerHTML = html;
        });

        // Initialize with 1 floor
        document.getElementById('buildingHeight').dispatchEvent(new Event('change'));

        // Calculate stack size
        document.getElementById('soil-stack-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const floors = parseInt(document.getElementById('buildingHeight').value);
            const material = document.getElementById('stackMaterial').value;
            
            let totalFU = 0;
            let floorFUs = [];
            
            // Calculate total FUs and collect per floor
            for (let i = 1; i <= floors; i++) {
                const fu = parseInt(document.getElementById(`fu_floor_${i}`).value) || 0;
                totalFU += fu;
                floorFUs.push(fu);
            }
            
            if (totalFU === 0) {
                showNotification('Please enter fixture units for at least one floor', 'info');
                return;
            }
            
            const size = calculateStackSize(totalFU, floors);
            let resultText = `<strong>Total Fixture Units:</strong> ${totalFU}<br>`;
            resultText += `<strong>Number of Floors:</strong> ${floors}<br>`;
            resultText += `<strong>Required Stack Size:</strong><br>${size.mm} mm (${size.inches} inches)<br><br>`;
            
            // Add floor breakdown
            resultText += '<strong>Floor Breakdown:</strong><br>';
            floorFUs.forEach((fu, i) => {
                resultText += `Floor ${i + 1}: ${fu} FU<br>`;
            });
            
            // Add notes based on material
            if (material === 'pvc') {
                resultText += '<br><small>Based on PVC-DWV maximum capacity</small>';
            } else if (material === 'cast') {
                resultText += '<br><small>Based on Cast Iron maximum capacity</small>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${totalFU} FU across ${floors} floors → ${size.mm}mm stack`);
        });

        function calculateStackSize(totalFU, floors) {
            // Stack sizing based on total FU and building height
            let mm;
            
            if (floors <= 3) {
                if (totalFU <= 10) mm = 75;
                else if (totalFU <= 240) mm = 100;
                else if (totalFU <= 960) mm = 150;
                else mm = 200;
            } else {
                if (totalFU <= 10) mm = 75;
                else if (totalFU <= 180) mm = 100;
                else if (totalFU <= 720) mm = 150;
                else mm = 200;
            }
            
            return {
                mm: mm,
                inches: (mm / 25.4).toFixed(1)
            };
        }

        function saveRecent(calculation) {
            const key = 'recentStackCalculations';
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
            const key = 'recentStackCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentStackCalculations');
            
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

