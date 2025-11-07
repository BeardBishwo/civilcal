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
    <title>Occupancy Assessment - Fire Protection Toolkit</title>
    <link rel="stylesheet" href="../../assets/css/fire.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="fire-container">
        <header class="fire-header">
            <h1><i class="fas fa-building"></i> Occupancy Assessment</h1>
            <nav>
                <a href="../fire.html" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Fire Protection
                </a>
            </nav>
        </header>

        <div class="calculator-section">
            <h2>NFPA 13 Occupancy Classification</h2>
            <p>Determine occupancy classification and hazard level for sprinkler system design requirements.</p>

            <form id="occupancyForm" class="fire-form">
                <div class="form-group">
                    <label for="primaryUse">
                        <i class="fas fa-home"></i> Primary Occupancy Type:
                    </label>
                    <select id="primaryUse" name="primaryUse" required>
                        <option value="">Select Occupancy</option>
                        <option value="assembly">Assembly</option>
                        <option value="business">Business</option>
                        <option value="detention">Detention & Correctional</option>
                        <option value="educational">Educational</option>
                        <option value="healthcare">Healthcare</option>
                        <option value="lodging">Lodging & Rooming</option>
                        <option value="mercantile">Mercantile</option>
                        <option value="office">Office</option>
                        <option value="residential">Residential</option>
                        <option value="storage">Storage</option>
                        <option value="industrial">Industrial</option>
                        <option value="mixed">Mixed Occupancy</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="floorArea">
                        <i class="fas fa-expand-arrows-alt"></i> Floor Area (sq ft):
                    </label>
                    <input type="number" id="floorArea" name="floorArea" step="1" min="0" required>
                    <div class="unit-display">sq ft</div>
                </div>

                <div class="form-group">
                    <label for="occupantLoad">
                        <i class="fas fa-users"></i> Maximum Occupant Load:
                    </label>
                    <input type="number" id="occupantLoad" name="occupantLoad" step="1" min="0" required>
                    <div class="unit-display">persons</div>
                    <div class="hint-text">Maximum number of occupants</div>
                </div>

                <div class="form-group">
                    <label for="storageHeight">
                        <i class="fas fa-arrows-alt-v"></i> Maximum Storage Height (ft):
                    </label>
                    <input type="number" id="storageHeight" name="storageHeight" step="0.5" min="0" max="40" value="0">
                    <div class="unit-display">ft</div>
                    <div class="hint-text">For storage occupancies only</div>
                </div>

                <div class="form-group">
                    <label for="commodityType">
                        <i class="fas fa-box"></i> Commodity Classification:
                    </label>
                    <select id="commodityType" name="commodityType">
                        <option value="">Select Commodity</option>
                        <option value="class-1">Class I - Noncombustible materials</option>
                        <option value="class-2">Class II - Class I in wood boxes</option>
                        <option value="class-3">Class III - Wood, paper, natural fibers</option>
                        <option value="class-4">Class IV - Flammable liquids, etc.</option>
                        <option value="plastics">Plastics (various categories)</option>
                        <option value="mixed">Mixed commodities</option>
                        <option value="unknown">Unknown/To be determined</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fireBrigade">
                        <i class="fas fa-fire-truck"></i> Fire Department Response:
                    </label>
                    <select id="fireBrigade" name="fireBrigade" required>
                        <option value="">Select Response Level</option>
                        <option value="urban">Urban (Response <4 minutes)</option>
                        <option value="suburban">Suburban (Response 4-8 minutes)</option>
                        <option value="rural">Rural (Response >8 minutes)</option>
                    </select>
                </div>

                <button type="submit" class="fire-btn">
                    <i class="fas fa-calculator"></i> Assess Occupancy
                </button>
            </form>

            <div id="occupancyResult" class="result-section" style="display: none;">
                <h3><i class="fas fa-clipboard-check"></i> Occupancy Assessment Results</h3>
                <div class="result-content">
                    <div class="occupancy-summary">
                        <div class="occupancy-metric">
                            <h4>Occupancy Classification</h4>
                            <div class="metric-value" id="occupancyClass">Light Hazard</div>
                        </div>
                        <div class="occupancy-metric">
                            <h4>Hazard Level</h4>
                            <div class="metric-value" id="hazardLevel">Normal</div>
                        </div>
                        <div class="occupancy-metric">
                            <h4>Sprinkler Required</h4>
                            <div class="metric-value" id="sprinklerRequired">Yes</div>
                        </div>
                    </div>

                    <div class="hazard-details">
                        <h4><i class="fas fa-info-circle"></i> Hazard Classification Details</h4>
                        <div class="hazard-grid">
                            <div class="hazard-item">
                                <h5>Sprinkler Density (gpm/sq ft)</h5>
                                <p id="densityValue">0.10</p>
                            </div>
                            <div class="hazard-item">
                                <h5>Area of Operation (sq ft)</h5>
                                <p id="areaValue">1500</p>
                            </div>
                            <div class="hazard-item">
                                <h5>Pipe Schedule</h5>
                                <p id="pipeSchedule">Light Hazard</p>
                            </div>
                            <div class="hazard-item">
                                <h5>Water Supply Duration</h5>
                                <p id="waterDuration">30 minutes</p>
                            </div>
                        </div>
                    </div>

                    <div class="requirements-section">
                        <h4><i class="fas fa-list"></i> NFPA 13 Requirements</h4>
                        <div class="requirements-grid">
                            <div class="requirement-card">
                                <h5><i class="fas fa-spray-can"></i> Sprinkler Requirements</h5>
                                <ul id="sprinklerRequirements">
                                </ul>
                            </div>
                            <div class="requirement-card">
                                <h5><i class="fas fa-water"></i> Water Supply</h5>
                                <ul id="waterSupply">
                                </ul>
                            </div>
                            <div class="requirement-card">
                                <h5><i class="fas fa-cogs"></i> System Features</h5>
                                <ul id="systemFeatures">
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="occupancy-specific" id="occupancySpecific">
                        <h4><i class="fas fa-star"></i> Special Considerations</h4>
                        <div id="specialConsiderations">
                        </div>
                    </div>

                    <div class="compliance-status" id="complianceStatus">
                        <h5><i class="fas fa-check-circle"></i> Code Compliance</h5>
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
        document.getElementById('occupancyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const primaryUse = document.getElementById('primaryUse').value;
            const floorArea = parseFloat(document.getElementById('floorArea').value);
            const occupantLoad = parseInt(document.getElementById('occupantLoad').value);
            const storageHeight = parseFloat(document.getElementById('storageHeight').value);
            const commodityType = document.getElementById('commodityType').value;
            const fireBrigade = document.getElementById('fireBrigade').value;

            let occupancyClass = 'Light Hazard';
            let hazardLevel = 'Normal';
            let sprinklerRequired = 'Yes';
            let densityValue = '0.10';
            let areaValue = '1500';
            let pipeSchedule = 'Light Hazard';
            let waterDuration = '30 minutes';
            let sprinklerRequirements = [];
            let waterSupply = [];
            let systemFeatures = [];
            let specialConsiderations = [];
            let recommendations = [];

            // Classification logic based on NFPA 13
            switch(primaryUse) {
                case 'assembly':
                    occupancyClass = 'Light Hazard';
                    hazardLevel = 'Elevated';
                    sprinklerRequired = 'Yes (Life Safety)';
                    densityValue = '0.10';
                    areaValue = '1500';
                    waterDuration = '30 minutes';
                    sprinklerRequirements = [
                        'Quick response sprinklers required',
                        'Life safety protection mandatory',
                        'Full coverage required'
                    ];
                    waterSupply = [
                        'Minimum 100 GPM supply',
                        '30-minute duration',
                        'Adequate residual pressure'
                    ];
                    systemFeatures = [
                        'Zoned system for large areas',
                        'Supervised control valve',
                        'Alarm check valve'
                    ];
                    specialConsiderations = [
                        'High occupant density',
                        'Egress consideration required',
                        'Partial suppression systems not allowed'
                    ];
                    break;

                case 'storage':
                    occupancyClass = 'Ordinary Hazard';
                    hazardLevel = 'High';
                    sprinklerRequired = 'Yes (Property Protection)';
                    
                    if (storageHeight <= 12) {
                        densityValue = '0.15';
                        areaValue = '2000';
                    } else if (storageHeight <= 20) {
                        densityValue = '0.20';
                        areaValue = '2500';
                    } else {
                        densityValue = '0.25';
                        areaValue = '3000';
                    }
                    pipeSchedule = 'Ordinary Hazard Group 1';
                    waterDuration = '60 minutes';
                    sprinklerRequirements = [
                        'K-factor suitable for storage',
                        'Temperature rating per commodity',
                        'Clearance to storage maintained'
                    ];
                    waterSupply = [
                        '60-minute duration minimum',
                        'K-factor appropriate for density',
                        'Loop or gridded system preferred'
                    ];
                    systemFeatures = [
                        'Large drop sprinklers if >20 ft',
                        'Deluge system for some commodities',
                        'Multiple hazard design areas'
                    ];
                    specialConsiderations = [
                        `Storage height: ${storageHeight} ft`,
                        'Commodity classification critical',
                        'Shelf arrangement affects design',
                        'High pile storage requires special design'
                    ];
                    break;

                case 'healthcare':
                    occupancyClass = 'Light Hazard';
                    hazardLevel = 'Elevated';
                    sprinklerRequired = 'Yes (Life Safety)';
                    densityValue = '0.10';
                    areaValue = '1500';
                    waterDuration = '30 minutes';
                    sprinklerRequirements = [
                        'Quick response sprinklers mandatory',
                        'Corridor sprinklers required',
                        'Partial suppression not permitted'
                    ];
                    waterSupply = [
                        'Reliable water supply essential',
                        'Multiple sources recommended',
                        'Emergency power for pumps'
                    ];
                    systemFeatures = [
                        'Zoned for smoke compartments',
                        'Supervised system',
                        'Life safety priorities'
                    ];
                    specialConsiderations = [
                        'Defend-in-place strategy',
                        'Evacuation challenges',
                        'Medical equipment protection',
                        'HVAC protection consideration'
                    ];
                    break;

                case 'office':
                case 'business':
                    occupancyClass = 'Light Hazard';
                    hazardLevel = 'Normal';
                    sprinklerRequired = 'Yes';
                    densityValue = '0.10';
                    areaValue = '1500';
                    waterDuration = '30 minutes';
                    sprinklerRequirements = [
                        'Standard response sprinklers acceptable',
                        'Ceiling coverage required',
                        'Concealed sprinklers preferred'
                    ];
                    waterSupply = [
                        '500 GPM minimum',
                        '30-minute duration',
                        'Adequate pressure available'
                    ];
                    systemFeatures = [
                        'Wet pipe system preferred',
                        'Dry systems where freezing',
                        'Economical design approach'
                    ];
                    specialConsiderations = [
                        'Open office concepts',
                        'IT/electrical room protection',
                        'Value engineering opportunities',
                        'Future layout changes'
                    ];
                    break;

                case 'residential':
                    occupancyClass = 'Light Hazard';
                    hazardLevel = 'Normal';
                    sprinklerRequired = 'Yes (Life Safety)';
                    densityValue = '0.05';
                    areaValue = '300';
                    pipeSchedule = 'Residential';
                    waterDuration = '10 minutes';
                    sprinklerRequirements = [
                        'Residential type sprinklers',
                        'One or two sprinklers per compartment',
                        'Quick response mandatory'
                    ];
                    waterSupply = [
                        '400 GPM minimum (1-family)',
                        '700 GPM minimum (multi-family)',
                        '10-minute duration'
                    ];
                    systemFeatures = [
                        'Smoke detection interlock',
                        'Individually rated sprinklers',
                        'Small area of operation'
                    ];
                    specialConsiderations = [
                        'Life safety focus',
                        'Concealed installation preferred',
                        'Water supply less critical',
                        'Quick response essential'
                    ];
                    break;

                default:
                    occupancyClass = 'Light Hazard';
                    hazardLevel = 'Normal';
                    sprinklerRequired = 'Yes';
                    densityValue = '0.10';
                    areaValue = '1500';
            }

            // Commodity classification adjustments
            if (commodityType && storageHeight > 0) {
                switch(commodityType) {
                    case 'class-3':
                        recommendations.push('Class III commodities - consider Ordinary Hazard design');
                        break;
                    case 'class-4':
                        recommendations.push('Class IV commodities - Extra Hazard design may be required');
                        break;
                    case 'plastics':
                        recommendations.push('Plastic commodities - verify specific classification');
                        break;
                }
            }

            // Fire department response adjustments
            if (fireBrigade === 'rural') {
                waterSupply.push('Rural response - enhance water supply requirements');
                recommendations.push('Rural fire department - consider increased system reliability');
            }

            // Size and occupancy adjustments
            if (floorArea > 10000 && occupantLoad > 300) {
                specialConsiderations.push('Large occupancy - enhanced life safety measures required');
                recommendations.push('Large building - consider multiple zones and redundancy');
            }

            if (storageHeight > 30) {
                recommendations.push('Very high storage - specialized ESFR systems may be required');
            }

            // Default recommendations
            if (recommendations.length === 0) {
                recommendations = ['Standard NFPA 13 design approach is acceptable'];
            }

            // Display results
            document.getElementById('occupancyClass').textContent = occupancyClass;
            document.getElementById('hazardLevel').textContent = hazardLevel;
            document.getElementById('sprinklerRequired').textContent = sprinklerRequired;
            document.getElementById('densityValue').textContent = densityValue;
            document.getElementById('areaValue').textContent = areaValue;
            document.getElementById('pipeSchedule').textContent = pipeSchedule;
            document.getElementById('waterDuration').textContent = waterDuration;

            // Update requirements lists
            const updateList = (listId, items) => {
                const list = document.getElementById(listId);
                list.innerHTML = '';
                items.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    list.appendChild(li);
                });
            };

            updateList('sprinklerRequirements', sprinklerRequirements);
            updateList('waterSupply', waterSupply);
            updateList('systemFeatures', systemFeatures);

            // Special considerations
            const specialEl = document.getElementById('specialConsiderations');
            specialEl.innerHTML = '';
            if (specialConsiderations.length > 0) {
                const ul = document.createElement('ul');
                specialConsiderations.forEach(consideration => {
                    const li = document.createElement('li');
                    li.textContent = consideration;
                    ul.appendChild(li);
                });
                specialEl.appendChild(ul);
            }

            // Compliance status
            const complianceEl = document.getElementById('complianceStatus');
            const complianceMsgEl = document.getElementById('complianceMessage');
            complianceEl.className = 'compliance-status compliant';
            complianceMsgEl.textContent = '✓ Occupancy classification and requirements identified';

            // Update recommendations
            const recList = document.getElementById('recommendationsList');
            recList.innerHTML = '';
            recommendations.forEach(rec => {
                const li = document.createElement('li');
                li.textContent = rec;
                recList.appendChild(li);
            });

            document.getElementById('occupancyResult').style.display = 'block';
            document.getElementById('occupancyResult').scrollIntoView({ behavior: 'smooth' });
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
        .occupancy-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .occupancy-metric {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border-radius: 12px;
            color: white;
        }

        .occupancy-metric h4 {
            margin: 0 0 10px 0;
            font-size: 0.9em;
            opacity: 0.9;
        }

        .metric-value {
            font-size: 1.5em;
            font-weight: bold;
        }

        .hazard-details {
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .hazard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .hazard-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }

        .hazard-item h5 {
            margin: 0 0 8px 0;
            color: #dc2626;
            font-size: 0.9em;
        }

        .hazard-item p {
            margin: 0;
            font-weight: bold;
            color: #374151;
            font-size: 1.1em;
        }

        .requirements-section {
            margin: 20px 0;
        }

        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .requirement-card {
            padding: 20px;
            background: #f1f5f9;
            border-radius: 8px;
        }

        .requirement-card h5 {
            margin: 0 0 15px 0;
            color: #dc2626;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .requirement-card ul {
            margin: 0;
            padding-left: 20px;
        }

        .requirement-card li {
            margin: 8px 0;
            color: #475569;
        }

        .occupancy-specific {
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .occupancy-specific h5 {
            margin: 0 0 15px 0;
            color: #dc2626;
        }

        .occupancy-specific ul {
            margin: 0;
            padding-left: 20px;
        }

        .occupancy-specific li {
            margin: 8px 0;
            color: #475569;
        }

        .compliance-status {
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            background: #dcfce7;
            color: #166534;
            border: 2px solid #16a34a;
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
