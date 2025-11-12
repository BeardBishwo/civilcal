<?php
session_start();
require_once __DIR__ . '/../../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toilet Flow & Demand - AEC Calculator</title>
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
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-toilet me-2"></i>Toilet Flow & Demand
            </h2>
            
            <form id="toilet-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('usage')">
                            <h5><i class="fas fa-faucet me-2"></i>Usage Details</h5>
                        </div>
                        <div id="usage-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="toiletType">Toilet Type</label>
                                <select class="form-control" id="toiletType">
                                    <option value="standard">Standard (6L)</option>
                                    <option value="dual">Dual Flush (3/4.5L)</option>
                                    <option value="efficient">Water Efficient (4.8L)</option>
                                    <option value="urinal">Urinal (2.5L)</option>
                                    <option value="custom">Custom Volume</option>
                                </select>
                            </div>

                            <div id="customVolume" class="form-group" style="display:none;">
                                <label for="flushVolume">Custom Flush Volume (L)</label>
                                <input type="number" class="form-control" id="flushVolume" min="0" step="0.1">
                            </div>

                            <div class="form-group">
                                <label for="flushesPerDay">Flushes per Day (per fixture)</label>
                                <input type="number" class="form-control" id="flushesPerDay" min="0" step="1" value="5">
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('occupancy')">
                            <h5><i class="fas fa-users me-2"></i>Occupancy</h5>
                        </div>
                        <div id="occupancy-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="fixtureCount">Number of Fixtures</label>
                                <input type="number" class="form-control" id="fixtureCount" min="1" step="1" value="1">
                            </div>

                            <div class="form-group">
                                <label for="occupancyType">Building Type</label>
                                <select class="form-control" id="occupancyType">
                                    <option value="office">Office</option>
                                    <option value="retail">Retail</option>
                                    <option value="restaurant">Restaurant</option>
                                    <option value="school">School</option>
                                    <option value="residential">Residential</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Water Usage</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Usage Analysis</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Reference Values</h6>
                            <small>
                                Standard WC: 4.0 FU<br>
                                Dual Flush: 3.5 FU<br>
                                Urinal: 2.0 FU<br>
                                Max Daily: 30 flushes<br>
                                Design Rate: 70%
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentToiletCalculations"></div>
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
        // Standard flush volumes by type
        const toiletTypes = {
            standard: { volume: 6.0, fu: 4.0 },
            dual: { volume: 4.5, fu: 3.5 },
            efficient: { volume: 4.8, fu: 3.5 },
            urinal: { volume: 2.5, fu: 2.0 }
        };

        // Usage patterns by building type (flushes per person per day)
        const buildingPatterns = {
            office: { rate: 3, peakFactor: 2.0 },
            retail: { rate: 2, peakFactor: 2.5 },
            restaurant: { rate: 6, peakFactor: 3.0 },
            school: { rate: 4, peakFactor: 4.0 },
            residential: { rate: 5, peakFactor: 1.5 }
        };

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        document.getElementById('toiletType').addEventListener('change', function() {
            const customDiv = document.getElementById('customVolume');
            customDiv.style.display = this.value === 'custom' ? 'block' : 'none';
        });

        document.getElementById('toilet-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const type = document.getElementById('toiletType').value;
            const flushes = parseInt(document.getElementById('flushesPerDay').value) || 0;
            const fixtures = parseInt(document.getElementById('fixtureCount').value) || 1;
            const buildingType = document.getElementById('occupancyType').value;
            
            let volume;
            if (type === 'custom') {
                volume = parseFloat(document.getElementById('flushVolume').value);
                if (!volume) {
                    alert('Please enter custom flush volume');
                    return;
                }
            } else {
                volume = toiletTypes[type].volume;
            }
            
            if (!flushes) {
                alert('Please enter number of flushes per day');
                return;
            }
            
            // Calculate usage
            const results = calculateUsage(
                volume,
                flushes,
                fixtures,
                type,
                buildingType
            );
            
            let resultText = `<strong>Daily Usage:</strong><br>`;
            resultText += `Water Volume: ${results.dailyVolume.toFixed(1)} L/day<br>`;
            resultText += `Peak Flow: ${results.peakFlow.toFixed(2)} L/s<br>`;
            resultText += `Average Flow: ${results.avgFlow.toFixed(2)} L/s<br><br>`;
            
            resultText += `<strong>Design Values:</strong><br>`;
            resultText += `Total Fixture Units: ${results.totalFU.toFixed(1)} FU<br>`;
            resultText += `Design Flow: ${results.designFlow.toFixed(2)} L/s<br>`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${fixtures}× ${type} → ${results.dailyVolume.toFixed(0)}L/day`);
        });

        function calculateUsage(volume, flushes, fixtures, type, building) {
            const warnings = [];
            
            // Calculate daily volume
            const dailyVolume = volume * flushes * fixtures;
            
            // Get fixture units
            let fuPerFixture = type === 'custom' ? 4.0 : toiletTypes[type].fu;
            const totalFU = fuPerFixture * fixtures;
            
            // Flow calculations
            const pattern = buildingPatterns[building];
            const peakFactor = pattern.peakFactor;
            const avgFlow = dailyVolume / (24 * 3600); // L/s
            const peakFlow = avgFlow * peakFactor;
            
            // Design flow based on fixture units
            const designFlow = 0.7 * Math.sqrt(totalFU); // Hunter's curve approximation
            
            // Check for high usage
            if (flushes > 30) {
                warnings.push('Warning: Usage rate exceeds typical maximum');
            }
            
            // Check for oversizing
            if (fixtures > 20 && building !== 'school') {
                warnings.push('Consider splitting into multiple bathrooms');
            }
            
            return {
                dailyVolume,
                peakFlow,
                avgFlow,
                designFlow,
                totalFU,
                warnings
            };
        }

        function saveRecent(calculation) {
            const key = 'recentToiletCalculations';
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
            const key = 'recentToiletCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentToiletCalculations');
            
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

        // Show usage section by default and load recent calculations
        document.getElementById('usage-section').style.display = 'block';
        displayRecent();
    </script>
</body>
</html>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
