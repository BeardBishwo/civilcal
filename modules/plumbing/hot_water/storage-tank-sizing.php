<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage Tank Sizing - AEC Calculator</title>
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
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-box me-2"></i>Hot Water Storage Tank Sizing Calculator
            </h2>
            
            <form id="storage-tank-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="buildingType">Building Type</label>
                            <select class="form-control" id="buildingType">
                                <option value="residential">Residential Building</option>
                                <option value="hotel">Hotel</option>
                                <option value="office">Office Building</option>
                                <option value="hospital">Hospital</option>
                                <option value="school">School</option>
                                <option value="gym">Gym/Sports Facility</option>
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
                            <label for="fixtureType">Add Hot Water Fixture</label>
                            <select class="form-control" id="fixtureType">
                                <option value="">Select Fixture...</option>
                                <option value="shower">Shower (8 L/min)</option>
                                <option value="bath">Bath (60 L/use)</option>
                                <option value="basin">Basin Tap (3 L/min)</option>
                                <option value="sink">Kitchen Sink (6 L/min)</option>
                                <option value="dishwasher">Dishwasher (15 L/cycle)</option>
                                <option value="washer">Washing Machine (40 L/cycle)</option>
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

                        <div class="form-group">
                            <label for="storageTemp">Storage Temperature (°C)</label>
                            <input type="number" class="form-control" id="storageTemp" min="50" max="80" value="60">
                        </div>

                        <div class="form-group">
                            <label for="deliveryTemp">Delivery Temperature (°C)</label>
                            <input type="number" class="form-control" id="deliveryTemp" min="40" max="60" value="45">
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Tank Size</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Storage Requirements</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Usage Guidelines (L/person/day)</h6>
                            <small>
                                Residential: 60-80L<br>
                                Hotel: 100-120L<br>
                                Office: 10-15L<br>
                                Hospital: 150-200L<br>
                                School: 15-20L<br>
                                Gym: 40-50L
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentStorageCalculations"></div>
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
        // Fixture flow rates and usage patterns
        const fixtures = {
            shower: { flow: 8, duration: 8, type: 'flow' },
            bath: { flow: 60, duration: 1, type: 'volume' },
            basin: { flow: 3, duration: 2, type: 'flow' },
            sink: { flow: 6, duration: 3, type: 'flow' },
            dishwasher: { flow: 15, duration: 1, type: 'volume' },
            washer: { flow: 40, duration: 1, type: 'volume' }
        };

        const fixtureNames = {
            shower: 'Shower',
            bath: 'Bath',
            basin: 'Basin Tap',
            sink: 'Kitchen Sink',
            dishwasher: 'Dishwasher',
            washer: 'Washing Machine'
        };

        // Daily hot water use per person (L)
        const dailyUse = {
            residential: 70,
            hotel: 110,
            office: 12,
            hospital: 175,
            school: 18,
            gym: 45
        };

        let addedFixtures = [];

        function addFixture() {
            const type = document.getElementById('fixtureType').value;
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            
            if (!type) {
                alert('Please select a fixture type');
                return;
            }
            
            addedFixtures.push({type, quantity});
            displayFixtures();
        }

        function removeFixture(index) {
            addedFixtures.splice(index, 1);
            displayFixtures();
        }

        function displayFixtures() {
            const container = document.getElementById('fixtureList');
            
            container.innerHTML = addedFixtures.map((fixture, index) => `
                <div class="fixture-row">
                    <span class="float-end text-danger" style="cursor: pointer;" onclick="removeFixture(${index})">
                        <i class="fas fa-times"></i>
                    </span>
                    ${fixture.quantity}x ${fixtureNames[fixture.type]}
                    <small class="text-muted">
                        (${fixtures[fixture.type].type === 'flow' ? 
                            fixtures[fixture.type].flow + ' L/min' : 
                            fixtures[fixture.type].flow + ' L/use'})
                    </small>
                </div>
            `).join('');
        }

        document.getElementById('storage-tank-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const buildingType = document.getElementById('buildingType').value;
            const occupants = parseInt(document.getElementById('occupants').value);
            const peakHours = parseFloat(document.getElementById('peakHours').value);
            const storageTemp = parseFloat(document.getElementById('storageTemp').value);
            const deliveryTemp = parseFloat(document.getElementById('deliveryTemp').value);
            
            if (!occupants || occupants <= 0) {
                alert('Please enter the number of occupants');
                return;
            }
            
            if (addedFixtures.length === 0) {
                alert('Please add at least one fixture');
                return;
            }
            
            // Calculate requirements
            const results = calculateStorage(
                buildingType,
                occupants,
                peakHours,
                storageTemp,
                deliveryTemp,
                addedFixtures
            );
            
            let resultText = `<strong>Tank Sizing:</strong><br>`;
            resultText += `Minimum Storage: ${results.minStorage.toFixed(0)} L<br>`;
            resultText += `Recommended Size: ${results.recommendedStorage.toFixed(0)} L<br>`;
            resultText += `Recovery Rate: ${results.recoveryRate.toFixed(1)} L/hr<br><br>`;
            
            resultText += `<strong>Peak Demand:</strong><br>`;
            resultText += `Peak Hour Demand: ${results.peakHourDemand.toFixed(0)} L/hr<br>`;
            resultText += `Daily Demand: ${results.dailyDemand.toFixed(0)} L/day<br><br>`;
            
            resultText += `<strong>System Parameters:</strong><br>`;
            resultText += `Heater Power: ${results.heaterPower.toFixed(1)} kW<br>`;
            resultText += `Recovery Time: ${results.recoveryTime.toFixed(1)} hours<br>`;
            resultText += `Daily Energy: ${results.dailyEnergy.toFixed(0)} kWh<br>`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${occupants} occupants → ${results.recommendedStorage.toFixed(0)}L tank`);
        });

        function calculateStorage(buildingType, occupants, peakHours, storageTemp, deliveryTemp, fixtures) {
            const warnings = [];
            
            // Calculate base daily demand
            const baseDaily = occupants * dailyUse[buildingType];
            
            // Calculate peak fixture demand
            let peakFixtureDemand = 0;
            fixtures.forEach(fixture => {
                const spec = fixtures[fixture.type];
                if (spec.type === 'flow') {
                    peakFixtureDemand += spec.flow * fixture.quantity;
                } else {
                    // Convert volume fixtures to equivalent flow rate during peak
                    peakFixtureDemand += (spec.flow * fixture.quantity) / (peakHours * 60);
                }
            });
            
            // Temperature adjustment factor
            const tempFactor = (storageTemp - 10) / (deliveryTemp - 10);
            
            // Calculate peak hour demand
            const peakHourDemand = baseDaily / 24 * 3; // Assume 3x average in peak hour
            
            // Size storage tank
            const minStorage = Math.max(
                peakHourDemand * 0.7, // 70% of peak hour demand
                peakFixtureDemand * 30 // 30 minutes of fixture demand
            );
            
            // Add 20% safety factor
            const recommendedStorage = minStorage * 1.2;
            
            // Calculate recovery requirements
            const recoveryRate = peakHourDemand * 0.6; // 60% of peak demand
            const recoveryTime = recommendedStorage / recoveryRate;
            
            // Energy calculations
            const specificHeat = 4.18; // kJ/kg·°C
            const heaterPower = (recoveryRate * specificHeat * (storageTemp - 10)) / 3600;
            const dailyEnergy = (baseDaily * specificHeat * (deliveryTemp - 10)) / 3600;
            
            // Warnings
            if (recoveryTime > 2) {
                warnings.push('Warning: Long recovery time - consider higher capacity heater');
            }
            if (recommendedStorage > 5000) {
                warnings.push('Warning: Large storage volume - consider multiple tanks');
            }
            if (storageTemp < 60) {
                warnings.push('Warning: Storage temperature below 60°C may risk Legionella growth');
            }
            
            return {
                minStorage,
                recommendedStorage,
                recoveryRate,
                peakHourDemand,
                dailyDemand: baseDaily,
                heaterPower,
                recoveryTime,
                dailyEnergy,
                warnings
            };
        }

        function saveRecent(calculation) {
            const key = 'recentStorageCalculations';
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
            const key = 'recentStorageCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentStorageCalculations');
            
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
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

