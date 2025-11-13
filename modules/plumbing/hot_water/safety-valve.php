<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safety Valve & Expansion Vessel - AEC Calculator</title>
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

        .requirements-list {
            list-style: none;
            padding-left: 0;
        }

        .requirements-list li {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .requirements-list li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-shield-alt me-2"></i>Safety Valve & Expansion Vessel Calculator
            </h2>
            
            <form id="safety-valve-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="heaterCapacity">Water Heater Capacity (L)</label>
                            <input type="number" class="form-control" id="heaterCapacity" min="0" step="1">
                        </div>

                        <div class="form-group">
                            <label for="maxTemp">Maximum Temperature (°C)</label>
                            <input type="number" class="form-control" id="maxTemp" min="0" max="100" step="1" value="65">
                        </div>

                        <div class="form-group">
                            <label for="coldTemp">Cold Water Temperature (°C)</label>
                            <input type="number" class="form-control" id="coldTemp" min="0" max="30" step="1" value="15">
                        </div>

                        <div class="form-group">
                            <label for="systemPressure">System Working Pressure (kPa)</label>
                            <input type="number" class="form-control" id="systemPressure" min="0" step="1" value="350">
                        </div>

                        <div class="form-group">
                            <label for="supplyPressure">Supply Pressure (kPa)</label>
                            <input type="number" class="form-control" id="supplyPressure" min="0" step="1" value="500">
                        </div>

                        <div class="form-group">
                            <label for="heaterRating">Heater Power Rating (kW)</label>
                            <input type="number" class="form-control" id="heaterRating" min="0" step="0.1">
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Requirements</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Safety Requirements</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Design Guidelines</h6>
                            <ul class="requirements-list mb-0">
                                <li>Pressure relief valve must be accessible</li>
                                <li>Install vessel in cold water line</li>
                                <li>Maximum relief valve setting: 1000 kPa</li>
                                <li>Typical safety margin: 10% above working pressure</li>
                            </ul>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentSafetyCalculations"></div>
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
        document.getElementById('safety-valve-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const capacity = parseFloat(document.getElementById('heaterCapacity').value);
            const maxTemp = parseFloat(document.getElementById('maxTemp').value);
            const coldTemp = parseFloat(document.getElementById('coldTemp').value);
            const systemPressure = parseFloat(document.getElementById('systemPressure').value);
            const supplyPressure = parseFloat(document.getElementById('supplyPressure').value);
            const heaterRating = parseFloat(document.getElementById('heaterRating').value);
            
            if (!capacity || capacity <= 0) {
                alert('Please enter a valid heater capacity');
                return;
            }
            
            // Calculate requirements
            const reqs = calculateSafetyRequirements(
                capacity,
                maxTemp,
                coldTemp,
                systemPressure,
                supplyPressure,
                heaterRating
            );
            
            let resultText = `<strong>Pressure Relief Valve:</strong><br>`;
            resultText += `Set Pressure: ${reqs.valvePressure.toFixed(0)} kPa<br>`;
            resultText += `Minimum Size: DN${reqs.valveSize}<br>`;
            resultText += `Relief Flow Rate: ${reqs.relievingRate.toFixed(1)} L/min<br><br>`;
            
            resultText += `<strong>Expansion Vessel:</strong><br>`;
            resultText += `Minimum Volume: ${reqs.vesselVolume.toFixed(1)} L<br>`;
            resultText += `Precharge Pressure: ${reqs.preCharge.toFixed(0)} kPa<br>`;
            resultText += `Expansion Volume: ${reqs.expansionVolume.toFixed(1)} L<br><br>`;
            
            resultText += `<strong>Installation Requirements:</strong><br>`;
            resultText += `Required Line Size: DN${reqs.pipeDN}<br>`;
            resultText += `Temperature Range: ${coldTemp}°C - ${maxTemp}°C<br>`;
            
            if (reqs.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += reqs.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${capacity}L heater → ${reqs.valveSize}mm PRV, ${reqs.vesselVolume.toFixed(1)}L vessel`);
        });

        function calculateSafetyRequirements(capacity, maxTemp, coldTemp, systemPressure, supplyPressure, heaterRating) {
            const warnings = [];
            
            // Expansion calculations
            const expansionCoeff = calculateExpansionCoefficient(maxTemp, coldTemp);
            const expansionVolume = capacity * expansionCoeff;
            
            // Vessel sizing
            const preCharge = Math.min(systemPressure * 0.9, supplyPressure * 0.7);
            const maxPressure = Math.min(supplyPressure * 0.9, 1000); // 1000 kPa max
            const vesselVolume = calculateVesselVolume(expansionVolume, preCharge, maxPressure);
            
            // Valve sizing based on heater rating
            const relievingRate = calculateRelievingRate(heaterRating);
            const valveSize = selectValveSize(relievingRate);
            const valvePressure = Math.min(systemPressure * 1.1, 1000);
            
            // Pipe size based on relief flow
            const pipeDN = selectPipeDN(relievingRate);
            
            // Warnings
            if (valvePressure >= 1000) {
                warnings.push('Warning: Relief pressure limited to 1000 kPa');
            }
            if (supplyPressure > 750) {
                warnings.push('Warning: High supply pressure - consider pressure reduction');
            }
            if (maxTemp > 85) {
                warnings.push('Warning: Very high temperature - verify system requirements');
            }
            
            return {
                valvePressure,
                valveSize,
                relievingRate,
                vesselVolume,
                preCharge,
                expansionVolume,
                pipeDN,
                warnings
            };
        }

        function calculateExpansionCoefficient(maxTemp, coldTemp) {
            // Simplified calculation - actual water expansion is non-linear
            const hotVolume = 1.0434; // Relative volume at 80°C
            const coldVolume = 1.0001; // Relative volume at 15°C
            return (hotVolume - coldVolume) * ((maxTemp - coldTemp) / 65);
        }

        function calculateVesselVolume(expansionVolume, preCharge, maxPressure) {
            // Use acceptance factor method
            const factor = (maxPressure - preCharge) / maxPressure;
            return expansionVolume / factor;
        }

        function calculateRelievingRate(heaterRating) {
            if (!heaterRating || heaterRating <= 0) {
                // Default based on tank volume if no rating provided
                return 20; // L/min
            }
            // Convert kW to L/min relief capacity
            return heaterRating * 1.5; // Typical conversion factor
        }

        function selectValveSize(flow) {
            // Simplified valve sizing based on flow rate
            if (flow <= 25) return 15;
            if (flow <= 50) return 20;
            if (flow <= 100) return 25;
            return 32;
        }

        function selectPipeDN(flow) {
            // Size discharge pipe based on flow rate
            if (flow <= 20) return 20;
            if (flow <= 40) return 25;
            if (flow <= 80) return 32;
            return 40;
        }

        function saveRecent(calculation) {
            const key = 'recentSafetyCalculations';
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
            const key = 'recentSafetyCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentSafetyCalculations');
            
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
