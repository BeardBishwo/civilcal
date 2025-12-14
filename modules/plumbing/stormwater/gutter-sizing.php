<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gutter Sizing - AEC Calculator</title>
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
                <i class="fas fa-water me-2"></i>Gutter Sizing Calculator
            </h2>
            
            <form id="gutter-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-toggle" onclick="toggleSection('roof')">
                            <h5><i class="fas fa-home me-2"></i>Roof Area</h5>
                        </div>
                        <div id="roof-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="roofLength">Roof Length (m)</label>
                                <input type="number" class="form-control" id="roofLength" min="0" step="0.1">
                            </div>

                            <div class="form-group">
                                <label for="roofWidth">Roof Width (m)</label>
                                <input type="number" class="form-control" id="roofWidth" min="0" step="0.1">
                            </div>

                            <div class="form-group">
                                <label for="roofPitch">Roof Pitch (degrees)</label>
                                <input type="number" class="form-control" id="roofPitch" min="0" max="45" step="1" value="20">
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('rainfall')">
                            <h5><i class="fas fa-cloud-rain me-2"></i>Rainfall Data</h5>
                        </div>
                        <div id="rainfall-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="rainfallIntensity">Rainfall Intensity (mm/hr)</label>
                                <input type="number" class="form-control" id="rainfallIntensity" min="0" step="1" value="150">
                            </div>

                            <div class="form-group">
                                <label for="runoffCoeff">Runoff Coefficient</label>
                                <select class="form-control" id="runoffCoeff">
                                    <option value="1.0">Metal/Tile Roof (1.0)</option>
                                    <option value="0.95">Asphalt Shingles (0.95)</option>
                                    <option value="0.9">Built-up Roof (0.9)</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-toggle" onclick="toggleSection('gutter')">
                            <h5><i class="fas fa-grip-lines me-2"></i>Gutter Details</h5>
                        </div>
                        <div id="gutter-section" class="section-content mb-4">
                            <div class="form-group">
                                <label for="gutterType">Gutter Profile</label>
                                <select class="form-control" id="gutterType">
                                    <option value="box">Box Gutter</option>
                                    <option value="quad">Quad/Square</option>
                                    <option value="round">Half Round</option>
                                    <option value="ogee">Ogee/K-Style</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="gutterSlope">Gutter Slope (%)</label>
                                <input type="number" class="form-control" id="gutterSlope" min="0.5" max="2" step="0.1" value="1">
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">Calculate Gutter Size</button>
                    </div>

                    <div class="col-md-6">
                        <div class="result-area" id="result-area">
                            <h4 class="mb-3">Gutter Requirements</h4>
                            <div id="result"></div>
                        </div>

                        <div class="info-table">
                            <h6>Design Guidelines</h6>
                            <small>
                                Min. Slope: 1:200 (0.5%)<br>
                                Max. Slope: 1:50 (2%)<br>
                                Typical Rainfall: 100-150mm/hr<br>
                                Add 20% safety factor
                            </small>
                        </div>

                        <div class="recent-calculations">
                            <h5>Recent Calculations</h5>
                            <div id="recentGutterCalculations"></div>
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
        // Gutter capacity coefficients (L/s per mm² of cross-section)
        const gutterCoeff = {
            box: 0.0139,
            quad: 0.0127,
            round: 0.0115,
            ogee: 0.0133
        };

        // Standard gutter sizes (width × depth in mm)
        const standardSizes = {
            box: [
                { width: 100, depth: 75 },
                { width: 150, depth: 100 },
                { width: 200, depth: 150 },
                { width: 300, depth: 200 }
            ],
            quad: [
                { width: 115, depth: 65 },
                { width: 125, depth: 85 },
                { width: 150, depth: 100 },
                { width: 175, depth: 125 }
            ],
            round: [
                { width: 125, depth: 65 },
                { width: 150, depth: 75 },
                { width: 175, depth: 87 },
                { width: 200, depth: 100 }
            ],
            ogee: [
                { width: 125, depth: 75 },
                { width: 150, depth: 90 },
                { width: 175, depth: 105 },
                { width: 200, depth: 120 }
            ]
        };

        function toggleSection(section) {
            const content = document.getElementById(section + '-section');
            const currentDisplay = content.style.display;
            content.style.display = currentDisplay === 'block' ? 'none' : 'block';
        }

        document.getElementById('gutter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const length = parseFloat(document.getElementById('roofLength').value);
            const width = parseFloat(document.getElementById('roofWidth').value);
            const pitch = parseFloat(document.getElementById('roofPitch').value);
            const rainfall = parseFloat(document.getElementById('rainfallIntensity').value);
            const runoff = parseFloat(document.getElementById('runoffCoeff').value);
            const gutterType = document.getElementById('gutterType').value;
            const slope = parseFloat(document.getElementById('gutterSlope').value);
            
            if (!length || !width) {
                showNotification('Please enter roof dimensions', 'info');
                return;
            }
            
            // Calculate requirements
            const results = calculateGutter(
                length,
                width,
                pitch,
                rainfall,
                runoff,
                gutterType,
                slope
            );
            
            let resultText = `<strong>Catchment Area:</strong><br>`;
            resultText += `Projected Area: ${results.projectedArea.toFixed(1)} m²<br>`;
            resultText += `Effective Area: ${results.effectiveArea.toFixed(1)} m²<br><br>`;
            
            resultText += `<strong>Flow Requirements:</strong><br>`;
            resultText += `Design Flow: ${results.designFlow.toFixed(1)} L/s<br>`;
            resultText += `With Safety: ${(results.designFlow * 1.2).toFixed(1)} L/s<br><br>`;
            
            resultText += `<strong>Recommended Gutter:</strong><br>`;
            resultText += `${results.recommendedSize.width}mm × ${results.recommendedSize.depth}mm<br>`;
            resultText += `Capacity: ${results.capacity.toFixed(1)} L/s<br>`;
            resultText += `Water Depth: ${results.waterDepth.toFixed(0)}mm`;
            
            if (results.warnings.length > 0) {
                resultText += '<br><br><div class="alert alert-warning">';
                resultText += results.warnings.join('<br>');
                resultText += '</div>';
            }
            
            document.getElementById('result').innerHTML = resultText;
            document.getElementById('result-area').style.display = 'block';
            
            // Save to recent calculations
            saveRecent(`${results.effectiveArea.toFixed(1)}m² → ${results.recommendedSize.width}×${results.recommendedSize.depth}mm`);
        });

        function calculateGutter(length, width, pitch, rainfall, runoff, type, slope) {
            const warnings = [];
            
            // Calculate effective roof area
            const projectedArea = length * width;
            const pitchFactor = 1 + (pitch / 90); // Increase area based on pitch
            const effectiveArea = projectedArea * pitchFactor;
            
            // Calculate design flow
            const designFlow = (effectiveArea * rainfall * runoff) / (3600 * 1000);
            
            // Select gutter size based on flow and slope
            let recommendedSize;
            let capacity = 0;
            
            for (const size of standardSizes[type]) {
                // Calculate capacity using Manning's equation modified for gutters
                const area = size.width * size.depth / 1000000; // m²
                const wetPerimeter = (size.width + 2 * size.depth) / 1000; // m
                const hydraulicRadius = area / wetPerimeter;
                
                capacity = gutterCoeff[type] * area * Math.pow(slope/100, 0.5) * 1000;
                
                if (capacity >= designFlow * 1.2) { // 20% safety factor
                    recommendedSize = size;
                    break;
                }
            }
            
            if (!recommendedSize) {
                recommendedSize = standardSizes[type][standardSizes[type].length - 1];
                warnings.push('Warning: Flow exceeds largest standard size capacity');
            }
            
            // Calculate water depth at design flow
            const waterDepth = (designFlow / capacity) * recommendedSize.depth;
            
            if (waterDepth > recommendedSize.depth * 0.7) {
                warnings.push('Warning: High water level - consider larger gutter');
            }
            
            if (slope < 0.5) {
                warnings.push('Warning: Slope below minimum recommended 0.5%');
            }
            
            return {
                projectedArea,
                effectiveArea,
                designFlow,
                recommendedSize,
                capacity,
                waterDepth,
                warnings
            };
        }

        function saveRecent(calculation) {
            const key = 'recentGutterCalculations';
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
            const key = 'recentGutterCalculations';
            const recent = JSON.parse(localStorage.getItem(key) || '[]');
            const container = document.getElementById('recentGutterCalculations');
            
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

        // Show roof section by default and load recent calculations
        document.getElementById('roof-section').style.display = 'block';
        displayRecent();
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

