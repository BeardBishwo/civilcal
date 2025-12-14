<?php
// modules/electrical/conduit-sizing/cable-tray-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cable Tray Sizing Calculator - AEC Toolkit</title>
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
            --yellow: #feca57;
        }

        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            color: var(--light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }

        .calculator-wrapper {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow);
            margin-top: 3rem;
        }

        .calculator-wrapper h1 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: var(--yellow);
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-col {
            flex: 1;
        }

        .form-group label {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--light);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #f093fb;
            box-shadow: 0 0 15px rgba(240, 147, 251, 0.3);
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
        }

        .btn-calculate:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .result-area {
            margin-top: 2rem;
            background: linear-gradient(45deg, #4ecdc4, #44a08d);
            padding: 2rem;
            border-radius: 10px;
            display: none;
            text-align: left;
        }

        .result-area h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 0.5rem;
        }

        .result-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .result-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
        }

        .result-item strong {
            color: var(--yellow);
        }

        .result-item.warning {
            background: rgba(255, 193, 7, 0.3);
        }

        .result-item.danger {
            background: rgba(244, 67, 54, 0.3);
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: #f093fb;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .tray-note {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-layer-group me-2"></i>Cable Tray Sizing</h1>
            <form id="tray-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="cable-diameter"><i class="fas fa-ruler me-2"></i>Cable Diameter (inches)</label>
                            <input type="number" id="cable-diameter" class="form-control" step="0.01" min="0.1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="cable-count"><i class="fas fa-list me-2"></i>Number of Cables</label>
                            <input type="number" id="cable-count" class="form-control" step="1" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="tray-type"><i class="fas fa-cog me-2"></i>Tray Type</label>
                            <select id="tray-type" class="form-control" required>
                                <option value="ladder">Ladder Tray</option>
                                <option value="trough">Solid Bottom Tray</option>
                                <option value="channel">Channel Tray</option>
                                <option value="ventilated">Ventilated Tray</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="fill-percentage"><i class="fas fa-percentage me-2"></i>Fill Percentage (%)</label>
                            <input type="number" id="fill-percentage" class="form-control" step="1" min="10" max="50" value="40" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="bundle-spacing"><i class="fas fa-arrows-alt-h me-2"></i>Bundle Spacing (inches)</label>
                            <input type="number" id="bundle-spacing" class="form-control" step="0.1" value="0.5" min="0" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="cable-type"><i class="fas fa-cable me-2"></i>Cable Type</label>
                            <select id="cable-type" class="form-control" required>
                                <option value="power">Power Cable</option>
                                <option value="control">Control Cable</option>
                                <option value="instrumentation">Instrumentation Cable</option>
                                <option value="communication">Communication Cable</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Tray Size</button>
            </form>
            
            <div class="tray-note">
                <i class="fas fa-info-circle me-2"></i>
                Cable tray sizing per NEMA standards for proper cable installation and heat dissipation.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Cable Tray Sizing Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateCableTraySizing(cableDiameter, cableCount, trayType, fillPercentage, bundleSpacing, cableType) {
            // Calculate cable cross-sectional area
            const cableArea = Math.PI * Math.pow(cableDiameter / 2, 2);
            
            // Total cable area
            const totalCableArea = cableArea * cableCount;
            
            // Required tray area based on fill percentage
            const requiredTrayArea = totalCableArea / (fillPercentage / 100);
            
            // Standard tray dimensions (width x depth in inches)
            const standardTrays = [
                { width: 6, depth: 3, area: 18 },
                { width: 9, depth: 3, area: 27 },
                { width: 12, depth: 3, area: 36 },
                { width: 18, depth: 3, area: 54 },
                { width: 24, depth: 3, area: 72 },
                { width: 30, depth: 3, area: 90 },
                { width: 36, depth: 3, area: 108 },
                { width: 18, depth: 4, area: 72 },
                { width: 24, depth: 4, area: 96 },
                { width: 30, depth: 4, area: 120 },
                { width: 36, depth: 4, area: 144 }
            ];
            
            // Tray type adjustments
            let typeMultiplier = 1.0;
            switch (trayType) {
                case 'ladder':
                    typeMultiplier = 0.8; // Ladder trays have better ventilation
                    break;
                case 'trough':
                    typeMultiplier = 1.0; // Standard reference
                    break;
                case 'channel':
                    typeMultiplier = 0.9; // Channel trays are more compact
                    break;
                case 'ventilated':
                    typeMultiplier = 0.85; // Ventilated trays allow better heat dissipation
                    break;
            }
            
            // Cable type adjustments
            let cableMultiplier = 1.0;
            switch (cableType) {
                case 'power':
                    cableMultiplier = 1.0; // Standard reference
                    break;
                case 'control':
                    cableMultiplier = 0.95; // Smaller cables
                    break;
                case 'instrumentation':
                    cableMultiplier = 0.9; // Even smaller cables
                    break;
                case 'communication':
                    cableMultiplier = 0.85; // Very small cables
                    break;
            }
            
            // Calculate adjusted required area
            const adjustedRequiredArea = requiredTrayArea * typeMultiplier * cableMultiplier;
            
            // Find appropriate tray size
            let recommendedTray = standardTrays[0];
            for (const tray of standardTrays) {
                if (tray.area >= adjustedRequiredArea) {
                    recommendedTray = tray;
                    break;
                }
            }
            
            // Calculate actual fill percentage
            const actualFillPercentage = (totalCableArea / recommendedTray.area) * 100;
            
            // Calculate cable spacing
            const cableSpacing = Math.sqrt(recommendedTray.width * recommendedTray.width / cableCount);
            
            // Assessment based on fill percentage
            let assessment = 'Excellent';
            let assessmentClass = '';
            
            if (actualFillPercentage > fillPercentage) {
                assessment = 'Overfilled';
                assessmentClass = 'danger';
            } else if (actualFillPercentage > fillPercentage * 0.9) {
                assessment = 'Good';
                assessmentClass = 'warning';
            }
            
            // Calculate weight considerations
            const cableWeightPerFoot = cableDiameter * cableCount * 0.5; // Simplified weight calculation
            const totalWeight = cableWeightPerFoot * 100; // Assume 100 feet
            
            return {
                cableDiameter: cableDiameter,
                cableCount: cableCount,
                cableArea: cableArea,
                totalCableArea: totalCableArea,
                requiredTrayArea: requiredTrayArea,
                adjustedRequiredArea: adjustedRequiredArea,
                recommendedTray: recommendedTray,
                actualFillPercentage: actualFillPercentage,
                cableSpacing: cableSpacing,
                cableWeightPerFoot: cableWeightPerFoot,
                totalWeight: totalWeight,
                assessment: assessment,
                assessmentClass: assessmentClass,
                trayType: trayType,
                cableType: cableType,
                fillPercentage: fillPercentage,
                bundleSpacing: bundleSpacing
            };
        }

        document.getElementById('tray-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const cableDiameter = parseFloat(document.getElementById('cable-diameter').value);
            const cableCount = parseInt(document.getElementById('cable-count').value);
            const trayType = document.getElementById('tray-type').value;
            const fillPercentage = parseFloat(document.getElementById('fill-percentage').value);
            const bundleSpacing = parseFloat(document.getElementById('bundle-spacing').value);
            const cableType = document.getElementById('cable-type').value;

            if (isNaN(cableDiameter) || isNaN(cableCount) || isNaN(fillPercentage) || isNaN(bundleSpacing)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const result = calculateCableTraySizing(cableDiameter, cableCount, trayType, fillPercentage, bundleSpacing, cableType);
            
            const trayTypeText = {
                'ladder': 'Ladder Tray',
                'trough': 'Solid Bottom Tray',
                'channel': 'Channel Tray',
                'ventilated': 'Ventilated Tray'
            };
            
            const cableTypeText = {
                'power': 'Power Cable',
                'control': 'Control Cable',
                'instrumentation': 'Instrumentation Cable',
                'communication': 'Communication Cable'
            };
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Cable Diameter:</strong><br>${result.cableDiameter} inches
                </div>
                <div class="result-item">
                    <strong>Number of Cables:</strong><br>${result.cableCount}
                </div>
                <div class="result-item">
                    <strong>Cable Type:</strong><br>${cableTypeText[cableType]}
                </div>
                <div class="result-item">
                    <strong>Tray Type:</strong><br>${trayTypeText[trayType]}
                </div>
                <div class="result-item">
                    <strong>Cable Cross-Section:</strong><br>${result.cableArea.toFixed(4)} in²
                </div>
                <div class="result-item">
                    <strong>Total Cable Area:</strong><br>${result.totalCableArea.toFixed(2)} in²
                </div>
                <div class="result-item">
                    <strong>Target Fill %:</strong><br>${result.fillPercentage}%
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Recommended Tray:</strong><br>${result.recommendedTray.width}" × ${result.recommendedTray.depth}"
                </div>
                <div class="result-item">
                    <strong>Tray Cross-Section:</strong><br>${result.recommendedTray.area} in²
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Actual Fill %:</strong><br>${result.actualFillPercentage.toFixed(1)}%
                </div>
                <div class="result-item">
                    <strong>Cable Spacing:</strong><br>${result.cableSpacing.toFixed(1)} inches
                </div>
                <div class="result-item">
                    <strong>Est. Weight/ft:</strong><br>${result.cableWeightPerFoot.toFixed(1)} lbs
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Assessment:</strong><br>${result.assessment}
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Cable Tray Sizing', `${cableCount}×${cableDiameter}" cables → ${result.recommendedTray.width}"×${result.recommendedTray.depth}" tray`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentCableTrayCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentCableTrayCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
