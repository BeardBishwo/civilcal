<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stormwater Storage Calculator - AEC Calculator</title>
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

        .tank-option {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tank-option:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .tank-option.selected {
            border: 1px solid var(--accent);
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h2 class="text-center mb-4">
                <i class="fas fa-water me-2"></i>Stormwater Storage Calculator
            </h2>
            
            <form id="storage-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('catchment')">
                            <h5><i class="fas fa-cloud-rain me-2"></i>Catchment Details</h5>
                        </div>
                        <div id="catchment-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="catchmentArea">Total Catchment Area (m²)</label>
                                <input type="number" class="form-control" id="catchmentArea" min="0" step="1">
                            </div>

                            <div class="form-group">
                                <label for="designStorm">Design Storm ARI (years)</label>
                                <select class="form-control" id="designStorm">
                                    <option value="1">1 year</option>
                                    <option value="2">2 year</option>
                                    <option value="5">5 year</option>
                                    <option value="10" selected>10 year</option>
                                    <option value="20">20 year</option>
                                    <option value="50">50 year</option>
                                    <option value="100">100 year</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="rainfallIntensity">Rainfall Intensity (mm/hr)</label>
                                <input type="number" class="form-control" id="rainfallIntensity" min="0" step="1" value="150">
                            </div>

                            <div class="form-group">
                                <label for="stormDuration">Storm Duration (hours)</label>
                                <input type="number" class="form-control" id="stormDuration" min="0.25" step="0.25" value="1">
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('requirements')">
                            <h5><i class="fas fa-list-check me-2"></i>Storage Requirements</h5>
                        </div>
                        <div id="requirements-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="detentionTime">Required Detention Time (hours)</label>
                                <input type="number" class="form-control" id="detentionTime" min="0" step="0.5" value="24">
                            </div>

                            <div class="form-group">
                                <label for="outflowRate">Maximum Outflow Rate (L/s)</label>
                                <input type="number" class="form-control" id="outflowRate" min="0" step="0.1" value="2">
                            </div>

                            <div class="form-group">
                                <label for="retentionVolume">Water Retention Volume (%)</label>
                                <input type="number" class="form-control" id="retentionVolume" min="0" max="100" step="1" value="20">
                                <small class="text-muted">Percentage of total volume for reuse</small>
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Storage</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Storage Analysis</h4>
                            <div id="result"></div>
                            
                            <div id="tank-options" class="mt-4">
                                <h5 class="mb-3">Recommended Tank Options</h5>
                                <!-- Tank options will be populated here -->
                            </div>
                        </div>

                        <div class="info-table">
                            <h6>Design Guidelines</h6>
                            <small>
                                Min. Detention: 24 hours<br>
                                Max. Outflow: 2-5 L/s/ha<br>
                                Retention: 15-25% for reuse<br>
                                Safety Factor: 1.2 
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
        // Standard tank sizes (m³)
        const standardTanks = [
            { size: 1, dimensions: '1.0m × 1.0m × 1.0m' },
            { size: 2, dimensions: '1.4m × 1.4m × 1.0m' },
            { size: 3, dimensions: '1.7m × 1.7m × 1.0m' },
            { size: 5, dimensions: '2.2m × 2.2m × 1.0m' },
            { size: 10, dimensions: '2.5m × 2.5m × 1.6m' },
            { size: 15, dimensions: '3.0m × 3.0m × 1.7m' },
            { size: 20, dimensions: '3.5m × 3.5m × 1.7m' },
            { size: 25, dimensions: '3.8m × 3.8m × 1.7m' },
            { size: 30, dimensions: '4.2m × 4.2m × 1.7m' },
            { size: 40, dimensions: '4.8m × 4.8m × 1.7m' },
            { size: 50, dimensions: '5.4m × 5.4m × 1.7m' }
        ];

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        document.getElementById('storage-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const catchmentArea = parseFloat(document.getElementById('catchmentArea').value);
            const rainfallIntensity = parseFloat(document.getElementById('rainfallIntensity').value);
            const stormDuration = parseFloat(document.getElementById('stormDuration').value);
            const designStorm = parseFloat(document.getElementById('designStorm').value);
            const detentionTime = parseFloat(document.getElementById('detentionTime').value);
            const outflowRate = parseFloat(document.getElementById('outflowRate').value);
            const retentionPercent = parseFloat(document.getElementById('retentionVolume').value);
            
            if (!catchmentArea || catchmentArea <= 0) {
                showNotification('Please enter catchment area', 'info');
                return;
            }
            
            // Calculate storage requirements
            const results = calculateStorage(
                catchmentArea,
                rainfallIntensity,
                stormDuration,
                designStorm,
                detentionTime,
                outflowRate,
                retentionPercent
            );
            
            let resultText = `<strong>Catchment Analysis:</strong><br>`;
            resultText += `Total Catchment: ${catchmentArea.toFixed(1)} m²<br>`;
            resultText += `Design Storm: ${designStorm} year ARI<br>`;
            resultText += `Peak Inflow: ${results.peakInflow.toFixed(1)} L/s<br><br>`;
            
            resultText += `<strong>Storage Requirements:</strong><br>`;
            resultText += `Total Volume: ${results.totalVolume.toFixed(1)} m³<br>`;
            resultText += `Detention Volume: ${results.detentionVolume.toFixed(1)} m³<br>`;
            resultText += `Retention Volume: ${results.retentionVolume.toFixed(1)} m³<br>`;
            resultText += `Safety Factor: ${results.safetyFactor.toFixed(1)}×<br>`;
            resultText += `Design Volume: ${results.designVolume.toFixed(1)} m³<br>`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            
            // Show tank options
            showTankOptions(results.designVolume);
            
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${catchmentArea}m² → ${results.designVolume.toFixed(1)}m³ storage`);
        });

        function calculateStorage(area, rainfall, duration, ari, detention, outflow, retention) {
            const warnings = [];
            
            // Calculate inflow
            const peakInflow = (area * rainfall) / (1000 * 3600); // L/s
            const totalRainfall = rainfall * duration; // mm
            const totalInflow = (area * totalRainfall) / 1000; // m³
            
            // Calculate volumes
            const detentionVolume = Math.max(
                (peakInflow * 3600 * detention) / 1000, // From peak flow
                totalInflow - (outflow * detention * 3.6) // From total volume
            );
            
            const retentionVolume = (totalInflow * retention) / 100;
            const baseVolume = detentionVolume + retentionVolume;
            
            // Apply safety factors
            let safetyFactor = 1.2; // Base safety factor
            
            if (ari >= 50) safetyFactor *= 1.1;
            if (detention > 48) safetyFactor *= 1.1;
            
            const designVolume = baseVolume * safetyFactor;
            
            // Check design criteria
            if (detention < 24) {
                warnings.push('Warning: Detention time below minimum 24 hours');
            }
            
            const outflowPerHectare = (outflow * 10000) / area;
            if (outflowPerHectare > 5) {
                warnings.push('Warning: Outflow rate exceeds 5 L/s/ha');
            }
            
            if (retention < 15) {
                warnings.push('Warning: Retention volume below recommended 15%');
            }
            
            return {
                peakInflow,
                totalVolume: totalInflow,
                detentionVolume,
                retentionVolume,
                safetyFactor,
                designVolume,
                warnings
            };
        }

        function showTankOptions(requiredVolume) {
            const container = document.getElementById('tank-options');
            let html = '';
            
            // Find suitable tank combinations
            const options = findTankCombinations(requiredVolume);
            
            options.forEach((option, index) => {
                html += `
                    <div class="tank-option" onclick="selectTankOption(${index})">
                        <strong>${option.tanks.length} × ${option.tanks[0].size}m³ Tanks</strong><br>
                        <small>
                            Total: ${option.totalVolume}m³<br>
                            Dimensions: ${option.tanks[0].dimensions}
                        </small>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        function findTankCombinations(volume) {
            const options = [];
            
            // Try each standard tank size
            for (const tank of standardTanks) {
                if (tank.size > volume) continue;
                
                const count = Math.ceil(volume / tank.size);
                if (count <= 6) { // Limit to reasonable combinations
                    options.push({
                        tanks: Array(count).fill(tank),
                        totalVolume: count * tank.size
                    });
                }
            }
            
            // Sort by efficiency (closest to required volume)
            options.sort((a, b) => a.totalVolume - b.totalVolume);
            
            return options.slice(0, 3); // Return top 3 options
        }

        function selectTankOption(index) {
            const options = document.querySelectorAll('.tank-option');
            options.forEach(opt => opt.classList.remove('selected'));
            options[index].classList.add('selected');
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

        // Show catchment section by default and load recent calculations
        document.getElementById('catchment-section').style.display = 'block';
        displayRecent();
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

