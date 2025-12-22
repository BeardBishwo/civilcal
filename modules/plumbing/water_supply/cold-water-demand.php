<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cold Water Demand - AEC Calculator</title>
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

        .info-table {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .fixture-row {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        #fixtureList {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 1rem;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-faucet me-2"></i>Cold Water Demand Calculator
            </h2>
            
            <form id="cold-water-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="buildingType">Building Type</label>
                            <select class="form-control" id="buildingType">
                                <option value="residential">Residential</option>
                                <option value="office">Office Building</option>
                                <option value="hotel">Hotel</option>
                                <option value="hospital">Hospital</option>
                                <option value="school">School</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="occupants">Number of Occupants</label>
                            <input type="number" class="form-control" id="occupants" min="1" step="1">
                        </div>

                        <div class="form-group">
                            <label for="fixtureType">Add Fixture</label>
                            <select class="form-control" id="fixtureType">
                                <option value="">Select Fixture...</option>
                                <option value="toilet">Toilet (0.10 L/s)</option>
                                <option value="urinal">Urinal (0.10 L/s)</option>
                                <option value="lavatory">Lavatory (0.15 L/s)</option>
                                <option value="sink">Kitchen Sink (0.20 L/s)</option>
                                <option value="shower">Shower (0.15 L/s)</option>
                                <option value="bath">Bathtub (0.30 L/s)</option>
                                <option value="washer">Washing Machine (0.25 L/s)</option>
                                <option value="dishwasher">Dishwasher (0.20 L/s)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" id="quantity" min="1" value="1">
                        </div>

                        <button type="button" class="btn btn-primary mb-3" onclick="addFixture()">Add Fixture</button>

                        <div id="fixtureList">
                            <!-- Fixtures will be added here -->
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Demand</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Water Demand Results</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Design Guidelines</h6>
                            <small>
                                Residential: 150-250 L/person/day<br>
                                Office: 50-75 L/person/day<br>
                                Hotel: 200-300 L/person/day<br>
                                Hospital: 350-600 L/person/day<br>
                                School: 40-60 L/person/day
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentDemandCalculations"></div>
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
        // Fixture flow rates (L/s)
        const fixtureFlows = {
            toilet: 0.10,
            urinal: 0.10,
            lavatory: 0.15,
            sink: 0.20,
            shower: 0.15,
            bath: 0.30,
            washer: 0.25,
            dishwasher: 0.20
        };

        // Fixture display names
        const fixtureNames = {
            toilet: 'Toilet',
            urinal: 'Urinal',
            lavatory: 'Lavatory',
            sink: 'Kitchen Sink',
            shower: 'Shower',
            bath: 'Bathtub',
            washer: 'Washing Machine',
            dishwasher: 'Dishwasher'
        };

        // Daily water use per person (L/day)
        const dailyUse = {
            residential: 200,
            office: 60,
            hotel: 250,
            hospital: 450,
            school: 50
        };

        let fixtures = [];

        function addFixture() {
            const type = document.getElementById('fixtureType').value;
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            
            if (!type) {
                showNotification('Please select a fixture type', 'info');
                return;
            }
            
            fixtures.push({type, quantity});
            displayFixtures();
        }

        function removeFixture(index) {
            fixtures.splice(index, 1);
            displayFixtures();
        }

        function displayFixtures() {
            const container = document.getElementById('fixtureList');
            
            container.innerHTML = fixtures.map((fixture, index) => `
                <div class="fixture-row">
                    <span class="float-end text-danger" style="cursor: pointer;" onclick="removeFixture(${index})">
                        <i class="fas fa-times"></i>
                    </span>
                    ${fixture.quantity}x ${fixtureNames[fixture.type]}
                    <small class="text-muted">(${fixtureFlows[fixture.type]} L/s each)</small>
                </div>
            `).join('');
        }

        document.getElementById('cold-water-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const buildingType = document.getElementById('buildingType').value;
            const occupants = parseInt(document.getElementById('occupants').value);
            
            if (!occupants || occupants <= 0) {
                showNotification('Please enter the number of occupants', 'info');
                return;
            }
            
            if (fixtures.length === 0) {
                showNotification('Please add at least one fixture', 'info');
                return;
            }
            
            // Calculate peak demand
            const peakDemand = calculatePeakDemand(fixtures);
            
            // Calculate daily demand
            const dailyDemand = occupants * dailyUse[buildingType];
            
            // Calculate pipe size recommendation
            const pipeSize = calculatePipeSize(peakDemand);
            
            let resultText = `<strong>Peak Demand:</strong><br>`;
            resultText += `Maximum Flow: ${peakDemand.toFixed(2)} L/s<br>`;
            resultText += `Design Flow: ${(peakDemand * 0.6).toFixed(2)} L/s<br><br>`;
            
            resultText += `<strong>Daily Demand:</strong><br>`;
            resultText += `Total Volume: ${dailyDemand.toFixed(0)} L/day<br>`;
            resultText += `Average Flow: ${(dailyDemand / (24 * 3600)).toFixed(3)} L/s<br><br>`;
            
            resultText += `<strong>Pipe Size:</strong><br>`;
            resultText += `Recommended: ${pipeSize}mm<br>`;
            resultText += `(based on 1.5 m/s velocity)`;
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${occupants} occupants, ${fixtures.length} fixtures → ${pipeSize}mm`);
        });

        function calculatePeakDemand(fixtures) {
            // Calculate theoretical maximum flow
            let maxFlow = 0;
            fixtures.forEach(fixture => {
                maxFlow += fixtureFlows[fixture.type] * fixture.quantity;
            });
            
            // Apply diversity factor based on fixture count
            const totalFixtures = fixtures.reduce((sum, f) => sum + f.quantity, 0);
            const diversityFactor = 1 / Math.sqrt(totalFixtures - 1 || 1);
            
            return maxFlow * diversityFactor;
        }

        function calculatePipeSize(flow) {
            // Using continuity equation Q = V × A
            const velocity = 1.5; // m/s (recommended velocity)
            const flowM3s = flow / 1000; // convert L/s to m³/s
            const area = flowM3s / velocity;
            const diameter = Math.sqrt((4 * area) / Math.PI) * 1000; // mm
            
            // Standard pipe sizes (mm)
            const standardSizes = [15, 20, 25, 32, 40, 50, 65, 80, 100];
            
            // Select next largest standard size
            for (let size of standardSizes) {
                if (size >= diameter) {
                    return size;
                }
            }
            
            return Math.ceil(diameter);
        }

        function saveRecent(calculation) {
            const key = 'recentDemandCalculations';
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
            const key = 'recentDemandCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentDemandCalculations');
            
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

