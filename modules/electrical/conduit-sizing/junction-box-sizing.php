<?php
// modules/electrical/conduit-sizing/junction-box-sizing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Junction Box Sizing Calculator - AEC Toolkit</title>
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
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
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

        .box-note {
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
            <h1><i class="fas fa-cube me-2"></i>Junction Box Sizing</h1>
            <form id="box-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="conductor-count"><i class="fas fa-list me-2"></i>Number of Conductors</label>
                            <input type="number" id="conductor-count" class="form-control" step="1" min="1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="wire-size"><i class="fas fa-wire me-2"></i>Largest Wire Size (AWG)</label>
                            <select id="wire-size" class="form-control" required>
                                <option value="14">14 AWG</option>
                                <option value="12">12 AWG</option>
                                <option value="10">10 AWG</option>
                                <option value="8">8 AWG</option>
                                <option value="6">6 AWG</option>
                                <option value="4">4 AWG</option>
                                <option value="2">2 AWG</option>
                                <option value="1">1 AWG</option>
                                <option value="1/0">1/0 AWG</option>
                                <option value="2/0">2/0 AWG</option>
                                <option value="3/0">3/0 AWG</option>
                                <option value="4/0">4/0 AWG</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="largest-conduit"><i class="fas fa-pipe me-2"></i>Largest Conduit Size (inches)</label>
                            <select id="largest-conduit" class="form-control" required>
                                <option value="0.5">1/2"</option>
                                <option value="0.75">3/4"</option>
                                <option value="1">1"</option>
                                <option value="1.25">1 1/4"</option>
                                <option value="1.5">1 1/2"</option>
                                <option value="2">2"</option>
                                <option value="2.5">2 1/2"</option>
                                <option value="3">3"</option>
                                <option value="3.5">3 1/2"</option>
                                <option value="4">4"</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="insulation-type"><i class="fas fa-layer-group me-2"></i>Insulation Type</label>
                            <select id="insulation-type" class="form-control" required>
                                <option value="THHN">THHN</option>
                                <option value="THW">THW</option>
                                <option value="XHHW">XHHW</option>
                                <option value="UF">UF</option>
                                <option value="THWN">THWN</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Box Size</button>
            </form>
            
            <div class="box-note">
                <i class="fas fa-info-circle me-2"></i>
                Junction box sizing per NEC Article 314 for proper conductor capacity and working space.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Junction Box Sizing Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateJunctionBoxSizing(conductorCount, wireSize, largestConduit, insulationType) {
            // NEC box sizing calculations per 314.16
            const conductorAllowance = {
                '14': 2.0, '12': 2.25, '10': 2.5, '8': 3.0,
                '6': 5.0, '4': 6.0, '2': 8.25, '1': 9.5,
                '1/0': 10.75, '2/0': 12.0, '3/0': 13.5, '4/0': 15.0
            };
            
            const conduitAllowance = {
                '0.5': 5.25, '0.75': 8.0, '1': 8.0, '1.25': 10.0,
                '1.5': 10.0, '2': 12.0, '2.5': 14.0, '3': 18.0,
                '3.5': 21.0, '4': 24.0
            };
            
            // Get allowances
            const conductorAllow = conductorAllowance[wireSize] || 2.0;
            const conduitAllow = conduitAllowance[largestConduit] || 5.25;
            
            // Calculate total volume required
            const totalVolume = (conductorCount * conductorAllow) + conduitAllow;
            
            // Standard box sizes (in cubic inches)
            const standardBoxes = [
                { size: '4 × 4', volume: 18, width: 4, height: 4, depth: 4 },
                { size: '4 1/8 × 4 1/8', volume: 20, width: 4.125, height: 4.125, depth: 4 },
                { size: '4 1/2 × 4 1/2', volume: 22, width: 4.5, height: 4.5, depth: 4 },
                { size: '5 × 5', volume: 30, width: 5, height: 5, depth: 4.5 },
                { size: '6 × 6', volume: 45, width: 6, height: 6, depth: 4.5 },
                { size: '8 × 8', volume: 75, width: 8, height: 8, depth: 4.5 },
                { size: '10 × 10', volume: 120, width: 10, height: 10, depth: 4.5 },
                { size: '12 × 12', volume: 180, width: 12, height: 12, depth: 4.5 }
            ];
            
            // Find appropriate box size
            let recommendedBox = standardBoxes[0];
            for (const box of standardBoxes) {
                if (box.volume >= totalVolume) {
                    recommendedBox = box;
                    break;
                }
            }
            
            // Calculate fill percentage
            const fillPercentage = (totalVolume / recommendedBox.volume) * 100;
            
            // Assessment based on fill percentage
            let assessment = 'Excellent';
            let assessmentClass = '';
            
            if (fillPercentage > 100) {
                assessment = 'Overfilled - Use Larger Box';
                assessmentClass = 'danger';
            } else if (fillPercentage > 85) {
                assessment = 'Full - Consider Larger Box';
                assessmentClass = 'warning';
            } else if (fillPercentage > 70) {
                assessment = 'Good';
                assessmentClass = '';
            }
            
            return {
                conductorCount: conductorCount,
                wireSize: wireSize,
                largestConduit: largestConduit,
                insulationType: insulationType,
                conductorAllow: conductorAllow,
                conduitAllow: conduitAllow,
                totalVolume: totalVolume,
                recommendedBox: recommendedBox,
                fillPercentage: fillPercentage,
                assessment: assessment,
                assessmentClass: assessmentClass
            };
        }

        document.getElementById('box-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const conductorCount = parseInt(document.getElementById('conductor-count').value);
            const wireSize = document.getElementById('wire-size').value;
            const largestConduit = document.getElementById('largest-conduit').value;
            const insulationType = document.getElementById('insulation-type').value;

            if (isNaN(conductorCount)) {
                showNotification('Please enter valid numbers.', 'info');
                return;
            }
            
            const result = calculateJunctionBoxSizing(conductorCount, wireSize, largestConduit, insulationType);
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Number of Conductors:</strong><br>${result.conductorCount}
                </div>
                <div class="result-item">
                    <strong>Largest Wire Size:</strong><br>${result.wireSize} AWG
                </div>
                <div class="result-item">
                    <strong>Largest Conduit:</strong><br>${result.largestConduit}"
                </div>
                <div class="result-item">
                    <strong>Insulation Type:</strong><br>${result.insulationType}
                </div>
                <div class="result-item">
                    <strong>Conductor Allowance:</strong><br>${result.conductorAllow.toFixed(2)} in³
                </div>
                <div class="result-item">
                    <strong>Conduit Allowance:</strong><br>${result.conduitAllow.toFixed(2)} in³
                </div>
                <div class="result-item">
                    <strong>Total Volume Required:</strong><br>${result.totalVolume.toFixed(2)} in³
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Recommended Box:</strong><br>${result.recommendedBox.size}
                </div>
                <div class="result-item">
                    <strong>Box Volume:</strong><br>${result.recommendedBox.volume} in³
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Fill Percentage:</strong><br>${result.fillPercentage.toFixed(1)}%
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Assessment:</strong><br>${result.assessment}
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Junction Box', `${conductorCount} conductors → ${result.recommendedBox.size} box`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentJunctionBoxCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentJunctionBoxCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
