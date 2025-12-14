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
    <title>Design Density Calculator - Fire Protection Toolkit</title>
    <link rel="stylesheet" href="../../assets/css/fire.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="fire-container">
        <header class="fire-header">
            <h1><i class="fas fa-chart-bar"></i> Design Density Calculator</h1>
            <nav>
                <a href="../fire.html" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Fire Protection
                </a>
            </nav>
        </header>

        <div class="calculator-section">
            <h2>NFPA 13 Design Density Determination</h2>
            <p>Calculate required sprinkler density based on occupancy, commodity classification, and storage arrangement.</p>

            <form id="densityForm" class="fire-form">
                <div class="form-group">
                    <label for="occupancyType">
                        <i class="fas fa-building"></i> Occupancy Type:
                    </label>
                    <select id="occupancyType" name="occupancyType" required>
                        <option value="">Select Occupancy</option>
                        <option value="assembly">Assembly</option>
                        <option value="office">Office/Business</option>
                        <option value="residential">Residential</option>
                        <option value="mercantile">Mercantile</option>
                        <option value="educational">Educational</option>
                        <option value="healthcare">Healthcare</option>
                        <option value="storage">Storage</option>
                        <option value="industrial">Industrial</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="commodityClass">
                        <i class="fas fa-boxes"></i> Commodity Class:
                    </label>
                    <select id="commodityClass" name="commodityClass" required>
                        <option value="">Select Commodity</option>
                        <option value="class-i">Class I</option>
                        <option value="class-ii">Class II</option>
                        <option value="class-iii">Class III</option>
                        <option value="class-iv">Class IV</option>
                        <option value="plastics-a">Plastics Group A</option>
                        <option value="plastics-b">Plastics Group B</option>
                        <option value="plastics-c">Plastics Group C</option>
                        <option value="cartoned">Cartoned Unexpanded Plastic</option>
                        <option value="uncartoned">Uncartoned Unexpanded Plastic</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="storageHeight">
                        <i class="fas fa-arrows-alt-v"></i> Storage Height (ft):
                    </label>
                    <input type="number" id="storageHeight" name="storageHeight" step="0.5" min="0" max="40" required>
                    <div class="unit-display">ft</div>
                </div>

                <div class="form-group">
                    <label for="storageConfiguration">
                        <i class="fas fa-layer-group"></i> Storage Configuration:
                    </label>
                    <select id="storageConfiguration" name="storageConfiguration" required>
                        <option value="">Select Configuration</option>
                        <option value="floor-palletized">Floor palletized</option>
                        <option value="solid-pallet">Solid palletized</option>
                        <option value="slatted-pallet">Slatted palletized</option>
                        <option value="rack-storage">Rack storage</option>
                        <option value="shelf-storage">Shelf storage</option>
                        <option value="bulk">Bulk storage</option>
                        <option value="open-rack">Open rack</option>
                        <option value="closed-rack">Closed rack</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="commodityArray">
                        <i class="fas fa-th-large"></i> Commodity Array:
                    </label>
                    <select id="commodityArray" name="commodityArray" required>
                        <option value="">Select Array</option>
                        <option value="open">Open array (no solid shelves)</option>
                        <option value="semi-solid">Semi-solid array</option>
                        <option value="solid">Solid array (solid shelves)</option>
                        <option value="random">Random array</option>
                        <option value="row-by-row">Row-by-row array</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ceilingHeight">
                        <i class="fas fa-home"></i> Ceiling Height (ft):
                    </label>
                    <input type="number" id="ceilingHeight" name="ceilingHeight" step="1" min="10" max="60" required>
                    <div class="unit-display">ft</div>
                    <div class="hint-text">Height from floor to ceiling</div>
                </div>

                <div class="form-group">
                    <label for="esfr">
                        <i class="fas fa-fire"></i> ESFR Required:
                    </label>
                    <select id="esfr" name="esfr" required>
                        <option value="">Select Option</option>
                        <option value="no">No - Conventional sprinklers</option>
                        <option value="yes">Yes - ESFR sprinklers</option>
                    </select>
                </div>

                <button type="submit" class="fire-btn">
                    <i class="fas fa-calculator"></i> Calculate Design Density
                </button>
            </form>

            <div id="densityResult" class="result-section" style="display: none;">
                <h3><i class="fas fa-chart-line"></i> Design Density Results</h3>
                <div class="result-content">
                    <div class="density-summary">
                        <div class="density-metric">
                            <h4>Required Density</h4>
                            <div class="metric-value" id="requiredDensity">0.00 gpm/sq ft</div>
                        </div>
                        <div class="density-metric">
                            <h4>Area of Operation</h4>
                            <div class="metric-value" id="areaOperation">0 sq ft</div>
                        </div>
                        <div class="density-metric">
                            <h4>Sprinkler Type</h4>
                            <div class="metric-value" id="sprinklerType">Standard</div>
                        </div>
                    </div>

                    <div class="calculation-breakdown">
                        <h4><i class="fas fa-calculator"></i> Design Criteria</h4>
                        <div class="criteria-grid">
                            <div class="criteria-item">
                                <h5>Base Density</h5>
                                <p id="baseDensity">0.10 gpm/sq ft</p>
                            </div>
                            <div class="criteria-item">
                                <h5>Height Adjustment</h5>
                                <p id="heightAdjustment">0.00 gpm/sq ft</p>
                            </div>
                            <div class="criteria-item">
                                <h5>Commodity Adjustment</h5>
                                <p id="commodityAdjustment">0.00 gpm/sq ft</p>
                            </div>
                            <div class="criteria-item">
                                <h5>Configuration Adjustment</h5>
                                <p id="configAdjustment">0.00 gpm/sq ft</p>
                            </div>
                        </div>
                    </div>

                    <div class="design-requirements">
                        <h4><i class="fas fa-list"></i> Design Requirements</h4>
                        <div class="requirements-container">
                            <div class="requirement-section">
                                <h5><i class="fas fa-tint"></i> Water Supply</h5>
                                <ul id="waterSupply">
                                </ul>
                            </div>
                            <div class="requirement-section">
                                <h5><i class="fas fa-cogs"></i> System Configuration</h5>
                                <ul id="systemConfig">
                                </ul>
                            </div>
                            <div class="requirement-section">
                                <h5><i class="fas fa-ruler"></i> Spacing Requirements</h5>
                                <ul id="spacingReq">
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="density-profile" id="densityProfile">
                        <h4><i class="fas fa-chart-area"></i> Density Profile</h4>
                        <div class="profile-info" id="profileInfo">
                        </div>
                    </div>

                    <div class="compliance-check" id="complianceCheck">
                        <h5><i class="fas fa-check-circle"></i> NFPA 13 Compliance</h5>
                        <div id="complianceMessage">Checking compliance...</div>
                    </div>

                    <div class="recommendations" id="recommendations">
                        <h5><i class="fas fa-lightbulb"></i> Design Recommendations</h5>
                        <ul id="recommendationsList">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('densityForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const occupancyType = document.getElementById('occupancyType').value;
            const commodityClass = document.getElementById('commodityClass').value;
            const storageHeight = parseFloat(document.getElementById('storageHeight').value);
            const storageConfiguration = document.getElementById('storageConfiguration').value;
            const commodityArray = document.getElementById('commodityArray').value;
            const ceilingHeight = parseFloat(document.getElementById('ceilingHeight').value);
            const esfr = document.getElementById('esfr').value;

            let requiredDensity = 0.10;
            let areaOperation = 1500;
            let sprinklerType = 'Standard';
            let baseDensity = 0.10;
            let heightAdjustment = 0.00;
            let commodityAdjustment = 0.00;
            let configAdjustment = 0.00;
            let waterSupply = [];
            let systemConfig = [];
            let spacingReq = [];
            let profileInfo = '';
            let complianceMessage = '';
            let recommendations = [];

            // Base density by occupancy
            switch(occupancyType) {
                case 'assembly':
                case 'office':
                case 'educational':
                    baseDensity = 0.10;
                    areaOperation = 1500;
                    break;
                case 'residential':
                    baseDensity = 0.05;
                    areaOperation = 300;
                    sprinklerType = 'Residential';
                    break;
                case 'mercantile':
                    baseDensity = 0.15;
                    areaOperation = 2000;
                    break;
                case 'storage':
                    baseDensity = 0.15;
                    areaOperation = 2000;
                    break;
                case 'industrial':
                    baseDensity = 0.20;
                    areaOperation = 2500;
                    break;
                default:
                    baseDensity = 0.10;
                    areaOperation = 1500;
            }

            // Commodity adjustments
            switch(commodityClass) {
                case 'class-i':
                    // No adjustment needed for Class I
                    break;
                case 'class-ii':
                    commodityAdjustment = 0.02;
                    break;
                case 'class-iii':
                    commodityAdjustment = 0.05;
                    break;
                case 'class-iv':
                    commodityAdjustment = 0.10;
                    break;
                case 'plastics-a':
                    commodityAdjustment = 0.15;
                    break;
                case 'plastics-b':
                    commodityAdjustment = 0.12;
                    break;
                case 'plastics-c':
                    commodityAdjustment = 0.08;
                    break;
                case 'cartoned':
                    commodityAdjustment = 0.18;
                    break;
                case 'uncartoned':
                    commodityAdjustment = 0.25;
                    break;
            }

            // Height adjustments
            if (storageHeight > 12) {
                if (storageHeight <= 20) {
                    heightAdjustment = 0.05;
                } else if (storageHeight <= 30) {
                    heightAdjustment = 0.10;
                } else {
                    heightAdjustment = 0.15;
                }
            }

            // Configuration adjustments
            if (storageConfiguration === 'rack-storage' || storageConfiguration === 'closed-rack') {
                configAdjustment = 0.05;
                if (commodityClass.startsWith('plastics')) {
                    configAdjustment = 0.08;
                }
            } else if (storageConfiguration === 'bulk') {
                configAdjustment = 0.20;
                sprinklerType = 'ESFR';
                requiredDensity = 0.60;
                areaOperation = 4000;
            }

            // Array adjustments
            if (commodityArray === 'solid') {
                configAdjustment += 0.03;
            } else if (commodityArray === 'semi-solid') {
                configAdjustment += 0.02;
            }

            // Calculate total density
            requiredDensity = baseDensity + commodityAdjustment + heightAdjustment + configAdjustment;
            
            // ESFR consideration
            if (esfr === 'yes' || storageHeight > 30 || (storageHeight > 20 && commodityClass.startsWith('plastics'))) {
                sprinklerType = 'ESFR';
                requiredDensity = 0.60;
                areaOperation = 4000;
            }

            // Water supply requirements
            const totalFlow = requiredDensity * areaOperation;
            waterSupply.push(`Minimum flow: ${totalFlow.toFixed(0)} GPM`);
            waterSupply.push(`Minimum pressure: ${Math.max(50, requiredDensity * 175).toFixed(1)} PSI`);
            waterSupply.push(`Duration: ${occupancyType === 'storage' ? '60 minutes' : '30 minutes'}`);

            // System configuration requirements
            if (occupancyType === 'residential') {
                systemConfig.push('Residential sprinklers required');
                systemConfig.push('Individual sprinkler rating: 4.9 gpm');
            } else if (commodityClass.startsWith('plastics') || storageConfiguration === 'rack-storage') {
                systemConfig.push('Quick response sprinklers required');
                systemConfig.push('K-factor: 5.6 (minimum)');
            } else {
                systemConfig.push('Standard response sprinklers acceptable');
            }

            if (storageHeight > 20) {
                systemConfig.push('Large drop sprinklers may be required');
                systemConfig.push('Temperature rating: 286°F (Ceiling)');
            }

            // Spacing requirements
            if (occupancyType === 'residential') {
                spacingReq.push('Maximum 12 ft x 12 ft spacing');
                spacingReq.push('Maximum 144 sq ft per sprinkler');
            } else {
                spacingReq.push('Maximum 225 sq ft per sprinkler');
                spacingReq.push('Minimum 7 ft spacing (non-residential)');
            }

            if (storageHeight > 12) {
                spacingReq.push('Minimum 7 ft below ceiling');
                spacingReq.push('Maximum 12 ft to top of storage');
            }

            // Density profile
            if (esfr === 'yes' || sprinklerType === 'ESFR') {
                profileInfo = 'ESFR sprinklers provide 0.60 gpm/sq ft density over 4,000 sq ft design area. No remote area required.';
            } else {
                profileInfo = `Density of ${requiredDensity.toFixed(2)} gpm/sq ft required over ${areaOperation} sq ft design area. Remote area analysis required.`;
            }

            // Compliance checking
            if (requiredDensity <= 0.30 && areaOperation <= 3000) {
                complianceMessage = '✓ Design meets standard NFPA 13 criteria';
                complianceMessage += '\n✓ Density and area within normal limits';
            } else if (requiredDensity <= 0.50 && areaOperation <= 3500) {
                complianceMessage = '⚠ High hazard design - enhanced water supply required';
                complianceMessage += '\n⚠ Verify water supply capacity and pressure';
            } else {
                complianceMessage = '✗ Very high hazard - specialized system required';
                complianceMessage += '\n✗ Consider ESFR or deluge systems';
            }

            // Specific recommendations
            if (commodityClass.startsWith('plastics') && storageHeight > 20) {
                recommendations.push('High plastic storage requires ESFR consideration');
            }

            if (storageConfiguration === 'rack-storage' && storageHeight > 15) {
                recommendations.push('Rack storage >15 ft requires in-rack sprinklers');
            }

            if (commodityArray === 'solid' && storageHeight > 10) {
                recommendations.push('Solid array increases fire intensity - consider enhanced density');
            }

            if (occupancyType === 'storage' && ceilingHeight > 30) {
                recommendations.push('High ceiling storage - verify clearance and draft curtains');
            }

            if (commodityClass === 'class-iv' || commodityClass.startsWith('plastics')) {
                recommendations.push('High hazard commodities - implement enhanced maintenance program');
            }

            if (recommendations.length === 0) {
                recommendations = ['Standard design approach is acceptable'];
            }

            // Display results
            document.getElementById('requiredDensity').textContent = `${requiredDensity.toFixed(2)} gpm/sq ft`;
            document.getElementById('areaOperation').textContent = `${areaOperation} sq ft`;
            document.getElementById('sprinklerType').textContent = sprinklerType;

            document.getElementById('baseDensity').textContent = `${baseDensity.toFixed(2)} gpm/sq ft`;
            document.getElementById('heightAdjustment').textContent = `+${heightAdjustment.toFixed(2)} gpm/sq ft`;
            document.getElementById('commodityAdjustment').textContent = `+${commodityAdjustment.toFixed(2)} gpm/sq ft`;
            document.getElementById('configAdjustment').textContent = `+${configAdjustment.toFixed(2)} gpm/sq ft`;

            // Update requirement lists
            const updateList = (listId, items) => {
                const list = document.getElementById(listId);
                list.innerHTML = '';
                items.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    list.appendChild(li);
                });
            };

            updateList('waterSupply', waterSupply);
            updateList('systemConfig', systemConfig);
            updateList('spacingReq', spacingReq);

            // Profile info
            const profileEl = document.getElementById('profileInfo');
            profileEl.innerHTML = `<p>${profileInfo}</p>`;

            // Compliance check
            const complianceEl = document.getElementById('complianceCheck');
            const complianceMsgEl = document.getElementById('complianceMessage');
            if (complianceMessage.includes('✓')) {
                complianceEl.className = 'compliance-status compliant';
            } else if (complianceMessage.includes('⚠')) {
                complianceEl.className = 'compliance-status warning';
            } else {
                complianceEl.className = 'compliance-status non-compliant';
            }
            complianceMsgEl.textContent = complianceMessage;

            // Update recommendations
            const recList = document.getElementById('recommendationsList');
            recList.innerHTML = '';
            recommendations.forEach(rec => {
                const li = document.createElement('li');
                li.textContent = rec;
                recList.appendChild(li);
            });

            document.getElementById('densityResult').style.display = 'block';
            document.getElementById('densityResult').scrollIntoView({ behavior: 'smooth' });
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
        .density-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .density-metric {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border-radius: 12px;
            color: white;
        }

        .density-metric h4 {
            margin: 0 0 10px 0;
            font-size: 0.9em;
            opacity: 0.9;
        }

        .metric-value {
            font-size: 1.8em;
            font-weight: bold;
        }

        .calculation-breakdown {
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .criteria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .criteria-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }

        .criteria-item h5 {
            margin: 0 0 8px 0;
            color: #dc2626;
            font-size: 0.9em;
        }

        .criteria-item p {
            margin: 0;
            font-weight: bold;
            color: #374151;
            font-size: 1.1em;
        }

        .design-requirements {
            margin: 20px 0;
        }

        .requirements-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .requirement-section {
            padding: 20px;
            background: #f1f5f9;
            border-radius: 8px;
        }

        .requirement-section h5 {
            margin: 0 0 15px 0;
            color: #dc2626;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .requirement-section ul {
            margin: 0;
            padding-left: 20px;
        }

        .requirement-section li {
            margin: 8px 0;
            color: #475569;
        }

        .density-profile {
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .profile-info {
            margin-top: 15px;
        }

        .profile-info p {
            margin: 0;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border-left: 4px solid #dc2626;
            color: #475569;
        }

        .compliance-check {
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
        }

        .compliance-status {
            border: 2px solid;
        }

        .compliance-status.compliant {
            background: #dcfce7;
            color: #166534;
            border-color: #16a34a;
        }

        .compliance-status.warning {
            background: #fef3c7;
            color: #92400e;
            border-color: #f59e0b;
        }

        .compliance-status.non-compliant {
            background: #fee2e2;
            color: #dc2626;
            border-color: #ef4444;
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
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
