<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trap Sizing - AEC Calculator</title>
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

        .reference-table {
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
            <h2 class="text-center mb-4"><i class="fas fa-water me-2"></i>Trap & Arm Sizing</h2>
            
            <form id="trap-sizing-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fixtureType">Fixture Type</label>
                            <select class="form-control" id="fixtureType">
                                <option value="lavatory">Lavatory (1 DFU)</option>
                                <option value="sink">Kitchen Sink (2 DFU)</option>
                                <option value="bathtub">Bathtub (2 DFU)</option>
                                <option value="shower">Shower (2 DFU)</option>
                                <option value="toilet">Water Closet (4 DFU)</option>
                                <option value="urinal">Urinal (2 DFU)</option>
                                <option value="washer">Clothes Washer (3 DFU)</option>
                                <option value="floor">Floor Drain (2 DFU)</option>
                                <option value="custom">Custom Fixture</option>
                            </select>
                        </div>

                        <div id="customInputs" style="display: none;">
                            <div class="form-group">
                                <label for="customDFU">Custom Fixture Units (DFU)</label>
                                <input type="number" class="form-control" id="customDFU" min="0.5" step="0.5" value="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="armLength">Trap Arm Length (m)</label>
                            <input type="number" class="form-control" id="armLength" min="0.1" step="0.1" value="1.5">
                        </div>

                        <div class="form-group">
                            <label for="fallPerMeter">Fall per Meter (mm)</label>
                            <select class="form-control" id="fallPerMeter">
                                <option value="10">10mm (1:100)</option>
                                <option value="15">15mm (1:67)</option>
                                <option value="20" selected>20mm (1:50)</option>
                                <option value="25">25mm (1:40)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Trap Size</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Required Trap & Arm Size</h4>
                            <div id="result"></div>
                        </div>

                        <div class="reference-table">
                            <h6>Maximum Trap Arm Lengths</h6>
                            <small>
                                32mm (1¼"): 1.5m<br>
                                40mm (1½"): 1.8m<br>
                                50mm (2"): 2.4m<br>
                                75mm (3"): 3.6m<br>
                                100mm (4"): 4.8m
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentTrapCalculations"></div>
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
        // Toggle custom DFU input
        document.getElementById('fixtureType').addEventListener('change', function() {
            const customInputs = document.getElementById('customInputs');
            customInputs.style.display = this.value === 'custom' ? 'block' : 'none';
        });

        // Trap sizing calculation
        document.getElementById('trap-sizing-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fixtureType = document.getElementById('fixtureType').value;
            const armLength = parseFloat(document.getElementById('armLength').value);
            const fall = parseInt(document.getElementById('fallPerMeter').value);
            
            let dfu;
            if (fixtureType === 'custom') {
                dfu = parseFloat(document.getElementById('customDFU').value);
            } else {
                const dfuMap = {
                    'lavatory': 1,
                    'sink': 2,
                    'bathtub': 2,
                    'shower': 2,
                    'toilet': 4,
                    'urinal': 2,
                    'washer': 3,
                    'floor': 2
                };
                dfu = dfuMap[fixtureType];
            }
            
            if (!armLength || armLength <= 0) {
                showNotification('Please enter a valid trap arm length', 'info');
                return;
            }
            
            const size = calculateTrapSize(dfu, armLength);
            const fixture = fixtureType === 'custom' ? 'Custom Fixture' : document.getElementById('fixtureType').options[document.getElementById('fixtureType').selectedIndex].text;
            
            let resultText = `<strong>Fixture:</strong> ${fixture}<br>`;
            resultText += `<strong>Fixture Units:</strong> ${dfu} DFU<br>`;
            resultText += `<strong>Arm Length:</strong> ${armLength}m<br>`;
            resultText += `<strong>Fall:</strong> ${fall}mm/m<br><br>`;
            resultText += `<strong>Required Trap Size:</strong><br>${size.mm}mm (${size.inches} inches)<br>`;
            
            // Add compliance check
            if (armLength > size.maxLength) {
                resultText += `<br><div class="alert alert-warning">Warning: Arm length exceeds maximum ${size.maxLength}m allowed for ${size.mm}mm trap</div>`;
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${fixture} (${dfu} DFU) → ${size.mm}mm trap`);
        });

        function calculateTrapSize(dfu, length) {
            // Basic trap sizing based on DFU and length
            let mm, maxLength;
            
            if (dfu <= 1 && length <= 1.5) {
                mm = 32; maxLength = 1.5;
            }
            else if (dfu <= 2 && length <= 1.8) {
                mm = 40; maxLength = 1.8;
            }
            else if (dfu <= 3 && length <= 2.4) {
                mm = 50; maxLength = 2.4;
            }
            else if (dfu <= 6 && length <= 3.6) {
                mm = 75; maxLength = 3.6;
            }
            else {
                mm = 100; maxLength = 4.8;
            }
            
            return {
                mm: mm,
                inches: (mm / 25.4).toFixed(1),
                maxLength: maxLength
            };
        }

        function saveRecent(calculation) {
            const key = 'recentTrapCalculations';
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
            const key = 'recentTrapCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentTrapCalculations');
            
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

