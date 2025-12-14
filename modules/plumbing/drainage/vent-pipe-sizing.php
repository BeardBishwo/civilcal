<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vent Pipe Sizing - AEC Calculator</title>
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

        .section-toggle {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            cursor: pointer;
        }

        .section-content {
            display: none;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-wind me-2"></i>Vent Pipe Sizing
            </h2>
            
            <form id="vent-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('drain')">
                            <h5><i class="fas fa-pipe me-2"></i>Drain Details</h5>
                        </div>
                        <div id="drain-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="drainSize">Drain Pipe Size (mm)</label>
                                <select class="form-control" id="drainSize">
                                    <option value="40">40mm</option>
                                    <option value="50">50mm</option>
                                    <option value="65">65mm</option>
                                    <option value="80">80mm</option>
                                    <option value="100">100mm</option>
                                    <option value="150">150mm</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="ventFixtureUnits">Total Fixture Units</label>
                                <input type="number" class="form-control" id="ventFixtureUnits" min="0" step="1">
                            </div>

                            <div class="form-group">
                                <label for="branchInterval">Branch Interval</label>
                                <select class="form-control" id="branchInterval">
                                    <option value="single">Single Floor</option>
                                    <option value="multi">Multi-Floor Stack</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('vent')">
                            <h5><i class="fas fa-wind me-2"></i>Vent Configuration</h5>
                        </div>
                        <div id="vent-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="ventLength">Total Developed Length (m)</label>
                                <input type="number" class="form-control" id="ventLength" min="0" step="0.1">
                            </div>

                            <div class="form-group">
                                <label for="ventType">Vent Type</label>
                                <select class="form-control" id="ventType">
                                    <option value="individual">Individual Vent</option>
                                    <option value="branch">Branch Vent</option>
                                    <option value="stack">Stack Vent</option>
                                    <option value="wet">Wet Vent</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="stackHeight">Stack Height (if applicable)</label>
                                <input type="number" class="form-control" id="stackHeight" min="0" step="0.1">
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Vent Size</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Sizing Results</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Sizing Guidelines</h6>
                            <small>
                                40mm drain → 32mm vent<br>
                                50mm drain → 40mm vent<br>
                                80mm drain → 50mm vent<br>
                                100mm drain → 65mm vent<br>
                                150mm drain → 80mm vent<br>
                                Max length varies by size<br>
                                Min. size for stack vent: 50mm
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentVentCalculations"></div>
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
        // Vent sizing lookup tables
        const ventSizes = {
            individual: {
                40: { base: 32, max_length: 10 },
                50: { base: 40, max_length: 15 },
                80: { base: 50, max_length: 20 },
                100: { base: 65, max_length: 30 },
                150: { base: 80, max_length: 60 }
            },
            branch: {
                40: { base: 40, max_length: 15 },
                50: { base: 40, max_length: 20 },
                80: { base: 50, max_length: 30 },
                100: { base: 65, max_length: 45 },
                150: { base: 80, max_length: 90 }
            },
            stack: {
                40: { base: 50, max_length: 30 },
                50: { base: 50, max_length: 30 },
                80: { base: 65, max_length: 45 },
                100: { base: 80, max_length: 60 },
                150: { base: 100, max_length: 120 }
            }
        };

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        document.getElementById('vent-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const drainSize = parseInt(document.getElementById('drainSize').value);
            const fixtureUnits = parseInt(document.getElementById('ventFixtureUnits').value) || 0;
            const ventLength = parseFloat(document.getElementById('ventLength').value) || 0;
            const ventType = document.getElementById('ventType').value;
            const branchInterval = document.getElementById('branchInterval').value;
            const stackHeight = parseFloat(document.getElementById('stackHeight').value) || 0;
            
            if (!drainSize || !fixtureUnits || !ventLength) {
                showNotification('Please fill in all required fields', 'info');
                return;
            }
            
            // Calculate requirements
            const results = calculateVentSize(
                drainSize,
                fixtureUnits,
                ventLength,
                ventType,
                branchInterval,
                stackHeight
            );
            
            let resultText = `<strong>Vent Analysis:</strong><br>`;
            resultText += `Required Size: ${results.ventSize} mm<br>`;
            resultText += `Maximum Length: ${results.maxLength} m<br>`;
            resultText += `Design Length: ${ventLength} m<br><br>`;
            
            resultText += `<strong>System Details:</strong><br>`;
            resultText += `Drain Size: ${drainSize} mm<br>`;
            resultText += `Fixture Units: ${fixtureUnits} FU<br>`;
            resultText += `Configuration: ${ventType}<br>`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${drainSize}mm → ${results.ventSize}mm vent`);
        });

        function calculateVentSize(drain, fu, length, type, interval, height) {
            const warnings = [];
            let ventSize, maxLength;
            
            // Get base size from table
            const sizeInfo = ventSizes[type === 'wet' ? 'branch' : type][drain];
            ventSize = sizeInfo ? sizeInfo.base : Math.ceil(drain * 0.8);
            maxLength = sizeInfo ? sizeInfo.max_length : 30;
            
            // Adjustments based on configuration
            if (type === 'stack') {
                if (interval === 'multi') {
                    ventSize = Math.max(ventSize, 50); // Minimum 50mm for multi-floor
                    if (height > 30) {
                        ventSize += 15; // Increase size for tall stacks
                        warnings.push('Stack height requires increased vent size');
                    }
                }
            }
            
            // Fixture unit adjustments
            if (fu > 100) {
                ventSize = Math.max(ventSize, 65);
                warnings.push('High fixture unit load requires minimum 65mm vent');
            } else if (fu > 50) {
                ventSize = Math.max(ventSize, 50);
            }
            
            // Length adjustments
            if (length > maxLength) {
                warnings.push(`Vent length exceeds maximum ${maxLength}m for selected size`);
                ventSize += 15; // Increase size for long runs
            }
            
            // Wet vent specific rules
            if (type === 'wet') {
                ventSize = Math.max(ventSize, Math.ceil(drain * 0.7));
                warnings.push('Wet vent sized at minimum 70% of drain size');
            }
            
            return {
                ventSize,
                maxLength,
                warnings
            };
        }

        function saveRecent(calculation) {
            const key = 'recentVentCalculations';
            let recent = JSON.parse(localStorage.getItem(key) || '[]');
            recent.unshift({
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            recent = recent.slice(0, 5); // Keep last 5
            localStorage.setItem(key, JSON.stringify(recent));
            displayRecent();
        }

        function displayRecent() {
            const key = 'recentVentCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentVentCalculations');
            
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

        // Show drain section by default and load recent calculations
        document.getElementById('drain-section').style.display = 'block';
        displayRecent();
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

