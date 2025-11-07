<?php
// Prevent direct access
if (!defined('ACCESS_ALLOWED')) {
    die('Direct access not permitted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standpipe Hose Demand Calculator - Fire Protection Toolkit</title>
    <link rel="stylesheet" href="../../assets/css/fire.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="fire-container">
        <header class="fire-header">
            <h1><i class="fas fa-fire-extinguisher"></i> Standpipe Hose Demand</h1>
            <nav>
                <a href="../fire.html" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Fire Protection
                </a>
            </nav>
        </header>

        <div class="calculator-section">
            <h2>NFPA 14 Hose Demand Calculations</h2>
            <p>Calculate water demand requirements for standpipe systems with simultaneous hose streams.</p>

            <form id="hoseDemandForm" class="fire-form">
                <div class="form-group">
                    <label for="buildingArea">
                        <i class="fas fa-expand-arrows-alt"></i> Total Building Area (sq ft):
                    </label>
                    <input type="number" id="buildingArea" name="buildingArea" step="1" min="0" required>
                    <div class="unit-display">sq ft</div>
                </div>

                <div class="form-group">
                    <label for="numberOfStreams">
                        <i class="fas fa-water"></i> Number of Simultaneous Hose Streams:
                    </label>
                    <input type="number" id="numberOfStreams" name="numberOfStreams" min="1" max="10" value="2" required>
                    <div class="hint-text">Typically 2-4 streams based on building size and hazard</div>
                </div>

                <div class="form-group">
                    <label for="hoseLength">
                        <i class="fas fa-ruler-horizontal"></i> Average Hose Length (ft):
                    </label>
                    <input type="number" id="hoseLength" name="hoseLength" min="50" max="150" step="5" value="100" required>
                    <div class="unit-display">ft</div>
                </div>

                <div class="form-group">
                    <label for="nozzleType">
                        <i class="fas fa-spray-can"></i> Nozzle Type:
                    </label>
                    <select id="nozzleType" name="nozzleType" required>
                        <option value="">Select Nozzle</option>
                        <option value="smooth-bore">Smooth Bore (1⅛")</option>
                        <option value="combination">Combination Nozzle</option>
                        <option value="fog">Fog Nozzle</option>
                        <option value="apartment-building">Apartment Building (100 GPM)</option>
                        <option value="high-rise">High-Rise Building (250 GPM)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hoseDiameter">
                        <i class="fas fa-circle"></i> Hose Diameter:
                    </label>
                    <select id="hoseDiameter" name="hoseDiameter" required>
                        <option value="">Select Diameter</option>
                        <option value="1.5">1½ inches</option>
                        <option value="1.75">1¾ inches</option>
                        <option value="2.5">2½ inches</option>
                    </select>
                    <div class="hint-text">Standard fire hose sizes</div>
                </div>

                <div class="form-group">
                    <label for="standpipeClass">
                        <i class="fas fa-water"></i> Standpipe Class:
                    </label>
                    <select id="standpipeClass" name="standpipeClass" required>
                        <option value="">Select Class</option>
                        <option value="class-i">Class I (2½")</option>
                        <option value="class-ii">Class II (1½")</option>
                        <option value="class-iii">Class III (Both)</option>
                    </select>
                </div>

                <button type="submit" class="fire-btn">
                    <i class="fas fa-calculator"></i> Calculate Hose Demand
                </button>
            </form>

            <div id="demandResult" class="result-section" style="display: none;">
                <h3><i class="fas fa-chart-line"></i> Hose Demand Results</h3>
                <div class="result-content">
                    <div class="demand-summary">
                        <div class="demand-metric">
                            <h4>Total Flow Required</h4>
                            <div class="metric-value" id="totalFlow">0 GPM</div>
                        </div>
                        <div class="demand-metric">
                            <h4>Required Pressure</h4>
                            <div class="metric-value" id="requiredPressure">0 PSI</div>
                        </div>
                        <div class="demand-metric">
                            <h4>Flow per Stream</h4>
                            <div class="metric-value" id="flowPerStream">0 GPM</div>
                        </div>
                    </div>

                    <div class="calculations-grid">
                        <div class="calc-item">
                            <h5><i class="fas fa-tachometer-alt"></i> Nozzle Pressure Loss</h5>
                            <p id="nozzleLoss">0 PSI</p>
                        </div>
                        <div class="calc-item">
                            <h5><i class="fas fa-long-arrow-down"></i> Hose Friction Loss</h5>
                            <p id="hoseLoss">0 PSI</p>
                        </div>
                        <div class="calc-item">
                            <h5><i class="fas fa-arrows-alt-v"></i> Elevation Loss</h5>
                            <p id="elevationLoss">0 PSI</p>
                        </div>
                        <div class="calc-item">
                            <h5><i class="fas fa-pipe"></i> Standpipe Friction Loss</h5>
                            <p id="standpipeLoss">0 PSI</p>
                        </div>
                    </div>

                    <div class="formula-display">
                        <h5><i class="fas fa-calculator"></i> Calculation Details</h5>
                        <div class="formula-section">
                            <strong>Nozzle Flow Formula:</strong>
                            <div class="formula">Q = K × √P</div>
                            <div id="nozzleFormula">K = 27 (1⅛" smooth bore)</div>
                        </div>
                        <div class="formula-section">
                            <strong>Hose Friction Loss:</strong>
                            <div class="formula">Pf = (4.52 × Q^1.85 × L) / (C^1.85 × D^4.87)</div>
                            <div id="hoseFormula">C = 120 (new fire hose)</div>
                        </div>
                        <div class="formula-section">
                            <strong>Elevation Loss:</strong>
                            <div class="formula">Pe = 0.434 × Height</div>
                            <div id="elevationFormula">Height = 100 ft average</div>
                        </div>
                    </div>

                    <div class="compliance-check" id="complianceCheck">
                        <h5><i class="fas fa-check-circle"></i> NFPA 14 Compliance</h5>
                        <div id="complianceStatus">Checking compliance...</div>
                    </div>

                    <div class="recommendations" id="recommendations">
                        <h5><i class="fas fa-lightbulb"></i> Recommendations</h5>
                        <ul id="recommendationsList">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('hoseDemandForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const buildingArea = parseFloat(document.getElementById('buildingArea').value);
            const numberOfStreams = parseInt(document.getElementById('numberOfStreams').value);
            const hoseLength = parseFloat(document.getElementById('hoseLength').value);
            const nozzleType = document.getElementById('nozzleType').value;
            const hoseDiameter = parseFloat(document.getElementById('hoseDiameter').value);
            const standpipeClass = document.getElementById('standpipeClass').value;

            // Nozzle K-factors and typical flows
            const nozzleData = {
                'smooth-bore': { kFactor: 27, pressure: 50, flow: 191 },
                'combination': { kFactor: 25, pressure: 75, flow: 217 },
                'fog': { kFactor: 30, pressure: 100, flow: 300 },
                'apartment-building': { kFactor: 21.5, pressure: 65, flow: 173 },
                'high-rise': { kFactor: 34, pressure: 100, flow: 340 }
            };

            const nozzle = nozzleData[nozzleType];
            const flowPerStream = nozzle.flow;
            const totalFlow = flowPerStream * numberOfStreams;

            // Calculate losses
            const nozzlePressureLoss = nozzle.pressure;
            
            // Hazen-Williams friction loss for hose
            // Pf = (4.52 × Q^1.85 × L) / (C^1.85 × D^4.87)
            const hoseFrictionLoss = (4.52 * Math.pow(flowPerStream, 1.85) * hoseLength) / 
                                    (Math.pow(120, 1.85) * Math.pow(hoseDiameter, 4.87));

            // Elevation loss (assuming average building height)
            const averageHeight = Math.min(buildingArea / 1000, 300); // Rough estimate
            const elevationLoss = 0.434 * (averageHeight / 2); // Half height on average

            // Standpipe friction loss (4" pipe, 100 ft main)
            const standpipeFrictionLoss = (4.52 * Math.pow(totalFlow, 1.85) * 100) / 
                                        (Math.pow(100, 1.85) * Math.pow(4, 4.87));

            const requiredPressure = nozzlePressureLoss + hoseFrictionLoss + elevationLoss + standpipeFrictionLoss;

            // Display results
            document.getElementById('totalFlow').textContent = `${totalFlow.toFixed(0)} GPM`;
            document.getElementById('requiredPressure').textContent = `${requiredPressure.toFixed(1)} PSI`;
            document.getElementById('flowPerStream').textContent = `${flowPerStream.toFixed(0)} GPM`;

            document.getElementById('nozzleLoss').textContent = `${nozzlePressureLoss.toFixed(1)} PSI`;
            document.getElementById('hoseLoss').textContent = `${hoseFrictionLoss.toFixed(1)} PSI`;
            document.getElementById('elevationLoss').textContent = `${elevationLoss.toFixed(1)} PSI`;
            document.getElementById('standpipeLoss').textContent = `${standpipeFrictionLoss.toFixed(1)} PSI`;

            // Update formulas
            document.getElementById('nozzleFormula').textContent = `K = ${nozzle.kFactor} (${nozzleType.replace('-', ' ')})`;
            document.getElementById('hoseFormula').textContent = `C = 120 (new fire hose), D = ${hoseDiameter}"`;
            document.getElementById('elevationFormula').textContent = `Height = ${(averageHeight / 2).toFixed(0)} ft average`;

            // Compliance checking
            const complianceEl = document.getElementById('complianceStatus');
            let complianceStatus = '';
            let recommendations = [];

            if (requiredPressure <= 100) {
                complianceStatus = '✓ System pressure requirements are acceptable';
                complianceEl.className = 'status-compliant';
            } else {
                complianceStatus = '⚠ System pressure requirements exceed NFPA 14 limits';
                complianceEl.className = 'status-warning';
                recommendations.push('Consider reducing hose length or using larger diameter standpipes');
            }

            if (totalFlow <= 500) {
                recommendations.push('Flow demand is within normal range');
            } else {
                recommendations.push('High flow demand - verify water supply capacity');
            }

            // Standpipe class recommendations
            if (standpipeClass === 'class-ii' && buildingArea > 10000) {
                recommendations.push('Consider Class I standpipes for large buildings');
            }

            if (numberOfStreams > 4 && buildingArea < 50000) {
                recommendations.push('Number of streams seems high for building size');
            }

            complianceEl.textContent = complianceStatus;

            // Update recommendations
            const recList = document.getElementById('recommendationsList');
            recList.innerHTML = '';
            if (recommendations.length === 0) {
                recommendations = ['System design meets basic requirements'];
            }
            recommendations.forEach(rec => {
                const li = document.createElement('li');
                li.textContent = rec;
                recList.appendChild(li);
            });

            document.getElementById('demandResult').style.display = 'block';
            document.getElementById('demandResult').scrollIntoView({ behavior: 'smooth' });
        });

        // Add visual feedback
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('change', function() {
                this.parentElement.classList.add('field-active');
                setTimeout(() => {
                    this.parentElement.classList.remove('field-active');
                }, 200);
            });
        });
    </script>

    <style>
        .demand-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .demand-metric {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border-radius: 12px;
            color: white;
        }

        .demand-metric h4 {
            margin: 0 0 10px 0;
            font-size: 0.9em;
            opacity: 0.9;
        }

        .metric-value {
            font-size: 1.8em;
            font-weight: bold;
        }

        .calculations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .calc-item {
            padding: 15px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
        }

        .calc-item h5 {
            margin: 0 0 8px 0;
            color: #dc2626;
            font-size: 0.9em;
        }

        .calc-item p {
            margin: 0;
            font-weight: bold;
            color: #475569;
        }

        .formula-display {
            margin: 20px 0;
            padding: 20px;
            background: #f1f5f9;
            border-radius: 8px;
        }

        .formula-section {
            margin: 15px 0;
        }

        .formula {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 8px;
            border-radius: 4px;
            margin: 5px 0;
            font-weight: bold;
            color: #1e293b;
        }

        .compliance-check {
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
        }

        .status-compliant {
            background: #dcfce7;
            color: #166534;
            border: 2px solid #16a34a;
        }

        .status-warning {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #f59e0b;
        }

        .hint-text {
            font-size: 0.85em;
            color: #6b7280;
            font-style: italic;
            margin-top: 5px;
        }

        .field-active {
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }
    </style>
</body>
</html>
