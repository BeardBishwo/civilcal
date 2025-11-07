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
    <title>Standpipe Pressure Calculations - Fire Protection Toolkit</title>
    <link rel="stylesheet" href="../../assets/css/fire.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="fire-container">
        <header class="fire-header">
            <h1><i class="fas fa-tachometer-alt"></i> Standpipe Pressure Calculations</h1>
            <nav>
                <a href="../fire.html" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Fire Protection
                </a>
            </nav>
        </header>

        <div class="calculator-section">
            <h2>NFPA 14 Pressure Analysis</h2>
            <p>Calculate pressure requirements and losses throughout standpipe systems at various elevations.</p>

            <form id="pressureCalcForm" class="fire-form">
                <div class="form-group">
                    <label for="buildingHeight">
                        <i class="fas fa-building"></i> Building Height (ft):
                    </label>
                    <input type="number" id="buildingHeight" name="buildingHeight" step="1" min="1" required>
                    <div class="unit-display">ft</div>
                </div>

                <div class="form-group">
                    <label for="targetFloor">
                        <i class="fas fa-layer-group"></i> Target Floor Height (ft):
                    </label>
                    <input type="number" id="targetFloor" name="targetFloor" step="1" min="1" required>
                    <div class="unit-display">ft</div>
                    <div class="hint-text">Height above ground level</div>
                </div>

                <div class="form-group">
                    <label for="standpipeDiameter">
                        <i class="fas fa-circle"></i> Standpipe Diameter (inches):
                    </label>
                    <select id="standpipeDiameter" name="standpipeDiameter" required>
                        <option value="">Select Diameter</option>
                        <option value="4">4 inches</option>
                        <option value="5">5 inches</option>
                        <option value="6">6 inches</option>
                        <option value="8">8 inches</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pipeMaterial">
                        <i class="fas fa-tools"></i> Pipe Material:
                    </label>
                    <select id="pipeMaterial" name="pipeMaterial" required>
                        <option value="">Select Material</option>
                        <option value="steel">Steel</option>
                        <option value="ductile-iron">Ductile Iron</option>
                        <option value="cpvc">CPVC</option>
                        <option value="cement-lined">Cement-Lined Steel</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="requiredFlow">
                        <i class="fas fa-water"></i> Required Flow (GPM):
                    </label>
                    <input type="number" id="requiredFlow" name="requiredFlow" step="1" min="50" max="2000" required>
                    <div class="unit-display">GPM</div>
                    <div class="hint-text">Total demand at top floor</div>
                </div>

                <div class="form-group">
                    <label for="availablePressure">
                        <i class="fas fa-arrow-down"></i> Available Pressure at Base (PSI):
                    </label>
                    <input type="number" id="availablePressure" name="availablePressure" step="1" min="50" max="300" required>
                    <div class="unit-display">PSI</div>
                    <div class="hint-text">Pressure from water supply/pump</div>
                </div>

                <button type="submit" class="fire-btn">
                    <i class="fas fa-calculator"></i> Calculate Pressures
                </button>
            </form>

            <div id="pressureResult" class="result-section" style="display: none;">
                <h3><i class="fas fa-chart-line"></i> Pressure Analysis Results</h3>
                <div class="result-content">
                    <div class="pressure-summary">
                        <div class="pressure-metric">
                            <h4>Required Pressure</h4>
                            <div class="metric-value" id="requiredPressure">0 PSI</div>
                        </div>
                        <div class="pressure-metric">
                            <h4>Available Pressure</h4>
                            <div class="metric-value" id="availablePressureResult">0 PSI</div>
                        </div>
                        <div class="pressure-metric">
                            <h4>Pressure Balance</h4>
                            <div class="metric-value" id="pressureBalance">0 PSI</div>
                        </div>
                    </div>

                    <div class="pressure-breakdown">
                        <h4><i class="fas fa-list"></i> Pressure Loss Breakdown</h4>
                        <div class="loss-grid">
                            <div class="loss-item">
                                <span class="loss-label">Elevation Loss:</span>
                                <span class="loss-value" id="elevationLoss">0.00 PSI</span>
                            </div>
                            <div class="loss-item">
                                <span class="loss-label">Friction Loss:</span>
                                <span class="loss-value" id="frictionLoss">0.00 PSI</span>
                            </div>
                            <div class="loss-item">
                                <span class="loss-label">Residual Pressure:</span>
                                <span class="loss-value" id="residualPressure">0.00 PSI</span>
                            </div>
                        </div>
                    </div>

                    <div class="pressure-profile">
                        <h4><i class="fas fa-chart-area"></i> Pressure at Different Elevations</h4>
                        <div class="profile-container">
                            <div class="profile-row">
                                <span class="profile-label">Ground Level:</span>
                                <span class="profile-pressure" id="groundPressure">0 PSI</span>
                                <span class="profile-status" id="groundStatus">OK</span>
                            </div>
                            <div class="profile-row">
                                <span class="profile-label">25% Height:</span>
                                <span class="profile-pressure" id="quarterPressure">0 PSI</span>
                                <span class="profile-status" id="quarterStatus">OK</span>
                            </div>
                            <div class="profile-row">
                                <span class="profile-label">50% Height:</span>
                                <span class="profile-pressure" id="halfPressure">0 PSI</span>
                                <span class="profile-status" id="halfStatus">OK</span>
                            </div>
                            <div class="profile-row">
                                <span class="profile-label">75% Height:</span>
                                <span class="profile-pressure" id="threeQuarterPressure">0 PSI</span>
                                <span class="profile-status" id="threeQuarterStatus">OK</span>
                            </div>
                            <div class="profile-row">
                                <span class="profile-label">Top Floor:</span>
                                <span class="profile-pressure" id="topPressure">0 PSI</span>
                                <span class="profile-status" id="topStatus">OK</span>
                            </div>
                        </div>
                    </div>

                    <div class="pump-requirements">
                        <h4><i class="fas fa-cog"></i> Pump Requirements</h4>
                        <div class="pump-grid">
                            <div class="pump-item">
                                <h5>Required Pump Pressure</h5>
                                <p id="pumpPressure">0 PSI</p>
                            </div>
                            <div class="pump-item">
                                <h5>Pump Capacity</h5>
                                <p id="pumpCapacity">0 GPM</p>
                            </div>
                            <div class="pump-item">
                                <h5>Horsepower Required</h5>
                                <p id="horsepower">0 HP</p>
                            </div>
                        </div>
                    </div>

                    <div class="compliance-status" id="complianceStatus">
                        <h5><i class="fas fa-check-circle"></i> NFPA 14 Compliance</h5>
                        <div id="complianceMessage">Checking compliance...</div>
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
        document.getElementById('pressureCalcForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const buildingHeight = parseFloat(document.getElementById('buildingHeight').value);
            const targetFloor = parseFloat(document.getElementById('targetFloor').value);
            const standpipeDiameter = parseFloat(document.getElementById('standpipeDiameter').value);
            const pipeMaterial = document.getElementById('pipeMaterial').value;
            const requiredFlow = parseFloat(document.getElementById('requiredFlow').value);
            const availablePressure = parseFloat(document.getElementById('availablePressure').value);

            // Hazen-Williams coefficients for different materials
            const cValues = {
                'steel': 100,
                'ductile-iron': 120,
                'cpvc': 150,
                'cement-lined': 130
            };

            const C = cValues[pipeMaterial];

            // Calculate losses
            const elevationLoss = 0.434 * targetFloor; // 0.434 PSI per foot
            
            // Friction loss using Hazen-Williams formula
            // Pf = (4.52 × Q^1.85 × L) / (C^1.85 × D^4.87)
            const frictionLoss = (4.52 * Math.pow(requiredFlow, 1.85) * targetFloor) / 
                               (Math.pow(C, 1.85) * Math.pow(standpipeDiameter, 4.87));
            
            const residualPressure = 65; // Minimum residual pressure per NFPA 14
            const totalRequiredPressure = elevationLoss + frictionLoss + residualPressure;

            // Calculate pressure at different elevations
            const elevations = [
                0, buildingHeight * 0.25, buildingHeight * 0.5, 
                buildingHeight * 0.75, buildingHeight
            ];

            const pressures = elevations.map(elev => {
                const elevLoss = 0.434 * elev;
                const frictionAtElev = (4.52 * Math.pow(requiredFlow, 1.85) * elev) / 
                                     (Math.pow(C, 1.85) * Math.pow(standpipeDiameter, 4.87));
                return availablePressure - elevLoss - frictionAtElev;
            });

            // Pump calculations
            const pumpPressure = totalRequiredPressure;
            const pumpCapacity = requiredFlow * 1.15; // 15% safety factor
            const horsepower = (pumpPressure * pumpCapacity) / (1714 * 0.85); // HP = (PSI × GPM) / (1714 × efficiency)

            // Display results
            document.getElementById('requiredPressure').textContent = `${totalRequiredPressure.toFixed(1)} PSI`;
            document.getElementById('availablePressureResult').textContent = `${availablePressure.toFixed(1)} PSI`;
            document.getElementById('pressureBalance').textContent = `${(availablePressure - totalRequiredPressure).toFixed(1)} PSI`;

            document.getElementById('elevationLoss').textContent = `${elevationLoss.toFixed(2)} PSI`;
            document.getElementById('frictionLoss').textContent = `${frictionLoss.toFixed(2)} PSI`;
            document.getElementById('residualPressure').textContent = `${residualPressure.toFixed(0)} PSI`;

            // Update pressure profile
            const profileIds = ['ground', 'quarter', 'half', 'threeQuarter', 'top'];
            profileIds.forEach((id, index) => {
                const pressure = pressures[index];
                const statusEl = document.getElementById(`${id}Status`);
                const pressureEl = document.getElementById(`${id}Pressure`);
                
                pressureEl.textContent = `${pressure.toFixed(1)} PSI`;
                
                if (pressure >= 65) {
                    statusEl.textContent = 'OK';
                    statusEl.className = 'profile-status status-ok';
                } else if (pressure >= 50) {
                    statusEl.textContent = 'LOW';
                    statusEl.className = 'profile-status status-low';
                } else {
                    statusEl.textContent = 'CRITICAL';
                    statusEl.className = 'profile-status status-critical';
                }
            });

            // Pump requirements
            document.getElementById('pumpPressure').textContent = `${pumpPressure.toFixed(1)} PSI`;
            document.getElementById('pumpCapacity').textContent = `${pumpCapacity.toFixed(0)} GPM`;
            document.getElementById('horsepower').textContent = `${horsepower.toFixed(1)} HP`;

            // Compliance checking
            const complianceEl = document.getElementById('complianceStatus');
            const complianceMsgEl = document.getElementById('complianceMessage');
            let complianceStatus = '';
            let recommendations = [];

            const criticalFloors = pressures.filter(p => p < 50).length;
            const lowFloors = pressures.filter(p => p >= 50 && p < 65).length;

            if (criticalFloors === 0 && lowFloors === 0) {
                complianceStatus = '✓ System meets all NFPA 14 pressure requirements';
                complianceEl.className = 'compliance-status compliant';
            } else if (criticalFloors === 0) {
                complianceStatus = '⚠ Some floors have marginal pressure - monitor performance';
                complianceEl.className = 'compliance-status warning';
                recommendations.push('Consider pump upgrades or additional standpipes');
            } else {
                complianceStatus = '✗ System fails NFPA 14 pressure requirements';
                complianceEl.className = 'compliance-status non-compliant';
                recommendations.push('System requires immediate upgrade to meet code requirements');
            }

            // Material-specific recommendations
            if (pipeMaterial === 'steel' && buildingHeight > 100) {
                recommendations.push('Consider cement-lined or larger diameter pipes for high buildings');
            }

            if (standpipeDiameter < 5 && requiredFlow > 500) {
                recommendations.push('Large flow demand may require 6" or 8" standpipes');
            }

            // Flow-specific recommendations
            if (requiredFlow > 1000 && buildingHeight > 150) {
                recommendations.push('High flow and height - consider multiple pump systems');
            }

            complianceMsgEl.textContent = complianceStatus;

            // Update recommendations
            const recList = document.getElementById('recommendationsList');
            recList.innerHTML = '';
            if (recommendations.length === 0) {
                recommendations = ['System design meets basic NFPA 14 requirements'];
            }
            recommendations.forEach(rec => {
                const li = document.createElement('li');
                li.textContent = rec;
                recList.appendChild(li);
            });

            document.getElementById('pressureResult').style.display = 'block';
            document.getElementById('pressureResult').scrollIntoView({ behavior: 'smooth' });
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
        .pressure-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .pressure-metric {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border-radius: 12px;
            color: white;
        }

        .pressure-metric h4 {
            margin: 0 0 10px 0;
            font-size: 0.9em;
            opacity: 0.9;
        }

        .metric-value {
            font-size: 1.8em;
            font-weight: bold;
        }

        .pressure-breakdown {
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .loss-grid {
            display: grid;
            gap: 10px;
            margin-top: 15px;
        }

        .loss-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: white;
            border-radius: 6px;
            border-left: 4px solid #dc2626;
        }

        .loss-label {
            font-weight: bold;
            color: #374151;
        }

        .loss-value {
            font-weight: bold;
            color: #dc2626;
        }

        .pressure-profile {
            margin: 20px 0;
            padding: 20px;
            background: #f1f5f9;
            border-radius: 8px;
        }

        .profile-container {
            margin-top: 15px;
        }

        .profile-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            padding: 12px;
            margin: 8px 0;
            background: white;
            border-radius: 6px;
            align-items: center;
        }

        .profile-label {
            font-weight: bold;
            color: #374151;
        }

        .profile-pressure {
            font-weight: bold;
            color: #dc2626;
        }

        .profile-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            text-align: center;
        }

        .status-ok {
            background: #dcfce7;
            color: #166534;
        }

        .status-low {
            background: #fef3c7;
            color: #92400e;
        }

        .status-critical {
            background: #fee2e2;
            color: #dc2626;
        }

        .pump-requirements {
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .pump-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .pump-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }

        .pump-item h5 {
            margin: 0 0 8px 0;
            color: #dc2626;
            font-size: 0.9em;
        }

        .pump-item p {
            margin: 0;
            font-weight: bold;
            color: #374151;
            font-size: 1.1em;
        }

        .compliance-status {
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
        }

        .compliance-status.compliant {
            background: #dcfce7;
            color: #166534;
            border: 2px solid #16a34a;
        }

        .compliance-status.warning {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #f59e0b;
        }

        .compliance-status.non-compliant {
            background: #fee2e2;
            color: #dc2626;
            border: 2px solid #ef4444;
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
