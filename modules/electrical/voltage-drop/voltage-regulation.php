<?php
// modules/electrical/voltage-drop/voltage-regulation.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voltage Regulation Calculator - AEC Toolkit</title>
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

        .result-item.excellent {
            background: rgba(76, 175, 80, 0.3);
        }

        .result-item.good {
            background: rgba(255, 193, 7, 0.3);
        }

        .result-item.fair {
            background: rgba(255, 152, 0, 0.3);
        }

        .result-item.poor {
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

        .regulation-note {
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
            <h1><i class="fas fa-sliders me-2"></i>Voltage Regulation</h1>
            <form id="regulation-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="source-voltage"><i class="fas fa-bolt me-2"></i>Source Voltage (V)</label>
                            <input type="number" id="source-voltage" class="form-control" step="0.1" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="load-voltage"><i class="fas fa-plug me-2"></i>Load Voltage (V)</label>
                            <input type="number" id="load-voltage" class="form-control" step="0.1" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="voltage-type"><i class="fas fa-cog me-2"></i>Voltage Type</label>
                            <select id="voltage-type" class="form-control" required>
                                <option value="line-to-line">Line-to-Line</option>
                                <option value="line-to-neutral">Line-to-Neutral</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="load-type"><i class="fas fa-industry me-2"></i>Load Type</label>
                            <select id="load-type" class="form-control" required>
                                <option value="resistive">Resistive</option>
                                <option value="inductive">Inductive</option>
                                <option value="capacitive">Capacitive</option>
                                <option value="mixed">Mixed Load</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-calculate">Calculate Regulation</button>
            </form>
            
            <div class="regulation-note">
                <i class="fas fa-info-circle me-2"></i>
                Voltage regulation indicates the change in voltage from no-load to full-load conditions. Lower values indicate better voltage regulation.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Voltage Regulation Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        function calculateVoltageRegulation(sourceVoltage, loadVoltage, voltageType, loadType) {
            // Calculate regulation percentage
            const regulationPercent = ((sourceVoltage - loadVoltage) / loadVoltage) * 100;
            const regulationAbsolute = sourceVoltage - loadVoltage;
            
            // Load regulation (change from no-load to full-load)
            const loadRegulationPercent = ((sourceVoltage - loadVoltage) / loadVoltage) * 100;
            
            // Source regulation (change due to source impedance)
            const sourceRegulationPercent = ((sourceVoltage - 480) / 480) * 100; // Assume 480V nominal
            
            // Line regulation (voltage change per load change)
            const lineRegulationPercent = (regulationPercent / 100); // Simplified calculation
            
            // Assessment based on typical standards
            let assessment = '';
            let assessmentClass = '';
            
            const absRegulation = Math.abs(regulationPercent);
            
            if (absRegulation <= 2) {
                assessment = 'Excellent';
                assessmentClass = 'excellent';
            } else if (absRegulation <= 5) {
                assessment = 'Good';
                assessmentClass = 'good';
            } else if (absRegulation <= 10) {
                assessment = 'Fair';
                assessmentClass = 'fair';
            } else {
                assessment = 'Poor';
                assessmentClass = 'poor';
            }
            
            // Load type adjustments
            let typicalRange = '';
            switch (loadType) {
                case 'resistive':
                    typicalRange = '1-3%';
                    break;
                case 'inductive':
                    typicalRange = '2-8%';
                    break;
                case 'capacitive':
                    typicalRange = '0.5-5%';
                    break;
                case 'mixed':
                    typicalRange = '2-6%';
                    break;
            }
            
            // Voltage quality assessment
            const voltageQuality = {
                excellent: absRegulation <= 2,
                good: absRegulation <= 5,
                acceptable: absRegulation <= 10,
                problematic: absRegulation > 10
            };
            
            return {
                sourceVoltage: sourceVoltage,
                loadVoltage: loadVoltage,
                regulationPercent: regulationPercent,
                regulationAbsolute: regulationAbsolute,
                loadRegulationPercent: loadRegulationPercent,
                sourceRegulationPercent: sourceRegulationPercent,
                lineRegulationPercent: lineRegulationPercent,
                assessment: assessment,
                assessmentClass: assessmentClass,
                voltageType: voltageType,
                loadType: loadType,
                typicalRange: typicalRange,
                voltageQuality: voltageQuality
            };
        }

        document.getElementById('regulation-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const sourceVoltage = parseFloat(document.getElementById('source-voltage').value);
            const loadVoltage = parseFloat(document.getElementById('load-voltage').value);
            const voltageType = document.getElementById('voltage-type').value;
            const loadType = document.getElementById('load-type').value;

            if (isNaN(sourceVoltage) || isNaN(loadVoltage)) {
                showNotification('Please enter valid voltages.', 'info');
                return;
            }
            
            if (loadVoltage === 0) {
                showNotification('Load voltage cannot be zero.', 'info');
                return;
            }
            
            const result = calculateVoltageRegulation(sourceVoltage, loadVoltage, voltageType, loadType);
            
            const voltageTypeText = voltageType === 'line-to-line' ? 'Line-to-Line' : 'Line-to-Neutral';
            const loadTypeText = {
                'resistive': 'Resistive',
                'inductive': 'Inductive',
                'capacitive': 'Capacitive',
                'mixed': 'Mixed Load'
            };
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Source Voltage:</strong><br>${result.sourceVoltage.toFixed(2)} V
                </div>
                <div class="result-item">
                    <strong>Load Voltage:</strong><br>${result.loadVoltage.toFixed(2)} V
                </div>
                <div class="result-item">
                    <strong>Voltage Type:</strong><br>${voltageTypeText}
                </div>
                <div class="result-item">
                    <strong>Load Type:</strong><br>${loadTypeText[loadType]}
                </div>
                <div class="result-item">
                    <strong>Voltage Drop:</strong><br>${result.regulationAbsolute.toFixed(2)} V
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Regulation (%):</strong><br>${result.regulationPercent.toFixed(2)}%
                </div>
                <div class="result-item">
                    <strong>Load Regulation:</strong><br>${result.loadRegulationPercent.toFixed(2)}%
                </div>
                <div class="result-item">
                    <strong>Source Regulation:</strong><br>${result.sourceRegulationPercent.toFixed(2)}%
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Assessment:</strong><br>${result.assessment}
                </div>
                <div class="result-item">
                    <strong>Typical Range:</strong><br>${result.typicalRange}
                </div>
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Voltage Regulation', `${sourceVoltage.toFixed(1)}V → ${loadVoltage.toFixed(1)}V = ${result.regulationPercent.toFixed(2)}%`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentVoltageRegulationCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentVoltageRegulationCalculations', JSON.stringify(recent));
        }
    </script>
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
