<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pervious Area Calculator - AEC Calculator</title>
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

        .surface-row {
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
            <h2 class="text-center mb-4">
                <i class="fas fa-water me-2"></i>Pervious Area Calculator
            </h2>
            
            <form id="pervious-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('site')">
                            <h5><i class="fas fa-map-marked-alt me-2"></i>Site Details</h5>
                        </div>
                        <div id="site-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="totalArea">Total Site Area (m²)</label>
                                <input type="number" class="form-control" id="totalArea" min="0" step="1">
                            </div>

                            <div class="form-group">
                                <label for="rainfallIntensity">Design Rainfall (mm/hr)</label>
                                <input type="number" class="form-control" id="rainfallIntensity" min="0" step="1" value="150">
                            </div>

                            <div class="form-group">
                                <label for="soilType">Soil Type</label>
                                <select class="form-control" id="soilType">
                                    <option value="sand">Sand (High Infiltration)</option>
                                    <option value="loamy">Loamy Soil (Medium)</option>
                                    <option value="clay">Clay Soil (Low)</option>
                                    <option value="rock">Rocky Ground (Very Low)</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('surface')">
                            <h5><i class="fas fa-layer-group me-2"></i>Surface Areas</h5>
                        </div>
                        <div id="surface-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="surfaceType">Add Surface Area</label>
                                <select class="form-control" id="surfaceType">
                                    <option value="">Select Surface Type...</option>
                                    <option value="roof">Roof Area (0%)</option>
                                    <option value="concrete">Concrete/Asphalt (0%)</option>
                                    <option value="pavers">Permeable Pavers (40%)</option>
                                    <option value="gravel">Gravel/Crushed Rock (60%)</option>
                                    <option value="grass">Grass/Landscaping (90%)</option>
                                    <option value="bioswale">Bioswale/Rain Garden (95%)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="surfaceArea">Area (m²)</label>
                                <input type="number" class="form-control" id="surfaceArea" min="0" step="0.1">
                            </div>

                            <button type="button" class="btn btn-primary mb-3" onclick="addSurface()">Add Surface</button>

                            <div id="surfaceList">
                                <!-- Surface areas will be added here -->
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Requirements</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Infiltration Analysis</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Infiltration Rates (mm/hr)</h6>
                            <small>
                                Sand: 50-200 mm/hr<br>
                                Loamy: 10-50 mm/hr<br>
                                Clay: 1-5 mm/hr<br>
                                Rocky: <1 mm/hr<br>
                                Min. Pervious: 15-25% of site
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentPerviousCalculations"></div>
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
        // Surface types and their infiltration rates
        const surfaces = {
            roof: { name: 'Roof Area', pervious: 0.00 },
            concrete: { name: 'Concrete/Asphalt', pervious: 0.00 },
            pavers: { name: 'Permeable Pavers', pervious: 0.40 },
            gravel: { name: 'Gravel/Crushed Rock', pervious: 0.60 },
            grass: { name: 'Grass/Landscaping', pervious: 0.90 },
            bioswale: { name: 'Bioswale/Rain Garden', pervious: 0.95 }
        };

        // Soil infiltration rates (mm/hr)
        const soilRates = {
            sand: { rate: 100, name: 'Sand' },
            loamy: { rate: 30, name: 'Loamy Soil' },
            clay: { rate: 3, name: 'Clay Soil' },
            rock: { rate: 0.5, name: 'Rocky Ground' }
        };

        let addedSurfaces = [];

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        function addSurface() {
            const type = document.getElementById('surfaceType').value;
            const area = parseFloat(document.getElementById('surfaceArea').value);
            
            if (!type || !area || area <= 0) {
                showNotification('Please select a surface type and enter valid area', 'info');
                return;
            }
            
            addedSurfaces.push({type, area});
            displaySurfaces();
        }

        function removeSurface(index) {
            addedSurfaces.splice(index, 1);
            displaySurfaces();
        }

        function displaySurfaces() {
            const container = document.getElementById('surfaceList');
            
            container.innerHTML = addedSurfaces.map((surface, index) => `
                <div class="surface-row">
                    <span class="float-end text-danger" style="cursor: pointer;" onclick="removeSurface(${index})">
                        <i class="fas fa-times"></i>
                    </span>
                    ${surfaces[surface.type].name}: ${surface.area}m²
                    <small class="text-muted">
                        (${surfaces[surface.type].pervious * 100}% pervious)
                    </small>
                </div>
            `).join('');
        }

        document.getElementById('pervious-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const totalArea = parseFloat(document.getElementById('totalArea').value);
            const rainfall = parseFloat(document.getElementById('rainfallIntensity').value);
            const soilType = document.getElementById('soilType').value;
            
            if (!totalArea || totalArea <= 0) {
                showNotification('Please enter total site area', 'info');
                return;
            }
            
            if (addedSurfaces.length === 0) {
                showNotification('Please add at least one surface area', 'info');
                return;
            }
            
            // Calculate requirements
            const results = calculateInfiltration(
                totalArea,
                rainfall,
                soilType,
                addedSurfaces
            );
            
            let resultText = `<strong>Site Analysis:</strong><br>`;
            resultText += `Total Site Area: ${totalArea.toFixed(1)} m²<br>`;
            resultText += `Pervious Area: ${results.perviousArea.toFixed(1)} m² (${(results.perviousRatio * 100).toFixed(1)}%)<br>`;
            resultText += `Impervious Area: ${results.imperviousArea.toFixed(1)} m² (${((1 - results.perviousRatio) * 100).toFixed(1)}%)<br><br>`;
            
            resultText += `<strong>Infiltration Capacity:</strong><br>`;
            resultText += `Total Runoff: ${results.totalRunoff.toFixed(1)} m³/hr<br>`;
            resultText += `Infiltration Rate: ${results.infiltrationRate.toFixed(1)} m³/hr<br>`;
            resultText += `Storage Required: ${results.storageRequired.toFixed(1)} m³<br>`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${totalArea}m² site → ${(results.perviousRatio * 100).toFixed(1)}% pervious`);
        });

        function calculateInfiltration(totalArea, rainfall, soilType, surfaces) {
            const warnings = [];
            
            // Calculate pervious and impervious areas
            let perviousArea = 0;
            let definedArea = 0;
            
            surfaces.forEach(surface => {
                perviousArea += surface.area * surfaces[surface.type].pervious;
                definedArea += surface.area;
            });
            
            const imperviousArea = definedArea - perviousArea;
            const perviousRatio = perviousArea / totalArea;
            
            // Check area totals
            if (definedArea > totalArea) {
                warnings.push('Warning: Defined areas exceed total site area');
            } else if (definedArea < totalArea) {
                warnings.push('Warning: Undefined area in site plan');
            }
            
            // Calculate runoff and infiltration
            const runoffCoeff = 1 - perviousRatio;
            const totalRunoff = (rainfall * totalArea * runoffCoeff) / 1000; // m³/hr
            
            const infiltrationRate = (soilRates[soilType].rate * perviousArea) / 1000; // m³/hr
            
            // Calculate storage requirements
            const storageRequired = Math.max(0, totalRunoff - infiltrationRate) * 2; // 2 hour storage
            
            // Check pervious ratio
            if (perviousRatio < 0.15) {
                warnings.push('Warning: Pervious area below recommended minimum (15%)');
            }
            
            // Check infiltration capacity
            if (infiltrationRate < totalRunoff) {
                warnings.push('Warning: Site infiltration capacity below peak runoff rate');
            }
            
            return {
                perviousArea,
                imperviousArea,
                perviousRatio,
                totalRunoff,
                infiltrationRate,
                storageRequired,
                warnings
            };
        }

        function saveRecent(calculation) {
            const key = 'recentPerviousCalculations';
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
            const key = 'recentPerviousCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentPerviousCalculations');
            
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

        // Show site section by default and load recent calculations
        document.getElementById('site-section').style.display = 'block';
        displayRecent();
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

