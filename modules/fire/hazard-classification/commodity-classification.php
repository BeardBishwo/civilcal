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
    <title>Commodity Classification - Fire Protection Toolkit</title>
    <link rel="stylesheet" href="../../assets/css/fire.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="fire-container">
        <header class="fire-header">
            <h1><i class="fas fa-boxes"></i> Commodity Classification</h1>
            <nav>
                <a href="../fire.html" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Fire Protection
                </a>
            </nav>
        </header>

        <div class="calculator-section">
            <h2>NFPA 13 Commodity Classification</h2>
            <p>Determine the appropriate commodity classification for storage applications based on material composition and packaging.</p>

            <form id="commodityForm" class="fire-form">
                <div class="form-group">
                    <label for="primaryMaterial">
                        <i class="fas fa-cube"></i> Primary Material:
                    </label>
                    <select id="primaryMaterial" name="primaryMaterial" required>
                        <option value="">Select Material Type</option>
                        <option value="metal">Metal (non-combustible)</option>
                        <option value="glass">Glass/Ceramics</option>
                        <option value="paper-cardboard">Paper/Cardboard</option>
                        <option value="wood">Wood products</option>
                        <option value="textiles">Textiles/Fibers</option>
                        <option value="plastics">Plastics (various)</option>
                        <option value="rubber">Rubber products</option>
                        <option value="chemicals">Chemicals/Flammable</option>
                        <option value="electronics">Electronics</option>
                        <option value="mixed">Mixed materials</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="packagingMaterial">
                        <i class="fas fa-shipping-fast"></i> Packaging Material:
                    </label>
                    <select id="packagingMaterial" name="packagingMaterial" required>
                        <option value="">Select Packaging</option>
                        <option value="none">No packaging</option>
                        <option value="cardboard">Cardboard boxes</option>
                        <option value="wood-crates">Wood crates/cases</option>
                        <option value="plastic">Plastic containers</option>
                        <option value="metal">Metal containers</option>
                        <option value="foil">Foil/specialty packaging</option>
                        <option value="mixed-packaging">Mixed packaging</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="storageConfiguration">
                        <i class="fas fa-layer-group"></i> Storage Configuration:
                    </label>
                    <select id="storageConfiguration" name="storageConfiguration" required>
                        <option value="">Select Configuration</option>
                        <option value="solid-pallet">Solid pallets</option>
                        <option value="slatted-pallet">Slatted pallets</option>
                        <option value="racks">Rack storage</option>
                        <option value="floor-stack">Floor stacked</option>
                        <option value="shelf-storage">Shelf storage</option>
                        <option value="bulk">Bulk storage</option>
                        <option value="shelf-racks">Open shelf racks</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="containerType">
                        <i class="fas fa-archive"></i> Container/Closure Type:
                    </label>
                    <select id="containerType" name="containerType" required>
                        <option value="">Select Container</option>
                        <option value="cartons">Cartons (no significant void space)</option>
                        <option value="bags">Bags (minimal void space)</option>
                        <option value="drums">Drums</option>
                        <option value="cylinders">Cylinders</option>
                        <option value="bottles">Bottles/Jars</option>
                        <option value="canister">Canisters</option>
                        <option value="containers">Open containers</option>
                        <option value="void-packaging">Significant void space packaging</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hazardRating">
                        <i class="fas fa-exclamation-triangle"></i> Material Hazard Rating:
                    </label>
                    <select id="hazardRating" name="hazardRating" required>
                        <option value="">Select Hazard Level</option>
                        <option value="non-combustible">Non-combustible</option>
                        <option value="limited-combustible">Limited combustible</option>
                        <option value="combustible">Combustible</option>
                        <option value="flammable">Flammable/Highly combustible</option>
                        <option value="unknown">Unknown/To be determined</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="plasticContent">
                        <i class="fas fa-recycle"></i> Plastic Content (% by weight):
                    </label>
                    <input type="number" id="plasticContent" name="plasticContent" min="0" max="100" step="1" value="0">
                    <div class="unit-display">%</div>
                    <div class="hint-text">Enter approximate plastic content if applicable</div>
                </div>

                <button type="submit" class="fire-btn">
                    <i class="fas fa-calculator"></i> Classify Commodity
                </button>
            </form>

            <div id="commodityResult" class="result-section" style="display: none;">
                <h3><i class="fas fa-clipboard-check"></i> Commodity Classification Results</h3>
                <div class="result-content">
                    <div class="classification-summary">
                        <div class="class-metric">
                            <h4>Commodity Class</h4>
                            <div class="metric-value" id="commodityClass">Class I</div>
                        </div>
                        <div class="class-metric">
                            <h4>Hazard Group</h4>
                            <div class="metric-value" id="hazardGroup">I</div>
                        </div>
                        <div class="class-metric">
                            <h4>Design Approach</h4>
                            <div class="metric-value" id="designApproach">Light Hazard</div>
                        </div>
                    </div>

                    <div class="classification-details">
                        <h4><i class="fas fa-info-circle"></i> Classification Details</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <h5>Sprinkler Density Required</h5>
                                <p id="requiredDensity">0.10 gpm/sq ft</p>
                            </div>
                            <div class="detail-item">
                                <h5>Area of Operation</h5>
                                <p id="requiredArea">1500 sq ft</p>
                            </div>
                            <div class="detail-item">
                                <h5>Pipe Schedule</h5>
                                <p id="pipeScheduleReq">Light Hazard</p>
                            </div>
                            <div class="detail-item">
                                <h5>Water Duration</h5>
                                <p id="waterDurationReq">30 minutes</p>
                            </div>
                        </div>
                    </div>

                    <div class="material-breakdown">
                        <h4><i class="fas fa-chart-pie"></i> Material Analysis</h4>
                        <div class="analysis-content" id="materialAnalysis">
                        </div>
                    </div>

                    <div class="design-implications">
                        <h4><i class="fas fa-drafting-compass"></i> Design Implications</h4>
                        <div class="implications-grid">
                            <div class="implication-card">
                                <h5><i class="fas fa-spray-can"></i> Sprinkler Selection</h5>
                                <ul id="sprinklerImplications">
                                </ul>
                            </div>
                            <div class="implication-card">
                                <h5><i class="fas fa-pipe"></i> System Configuration</h5>
                                <ul id="systemImplications">
                                </ul>
                            </div>
                            <div class="implication-card">
                                <h5><i class="fas fa-exclamation-triangle"></i> Special Precautions</h5>
                                <ul id="precautions">
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="alternate-considerations" id="alternateConsiderations">
                        <h4><i class="fas fa-external-link-alt"></i> Alternative Classifications</h4>
                        <div id="alternateOptions">
                        </div>
                    </div>

                    <div class="compliance-status" id="complianceStatus">
                        <h5><i class="fas fa-check-circle"></i> NFPA 13 Compliance</h5>
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
        document.getElementById('commodityForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const primaryMaterial = document.getElementById('primaryMaterial').value;
            const packagingMaterial = document.getElementById('packagingMaterial').value;
            const storageConfiguration = document.getElementById('storageConfiguration').value;
            const containerType = document.getElementById('containerType').value;
            const hazardRating = document.getElementById('hazardRating').value;
            const plasticContent = parseFloat(document.getElementById('plasticContent').value) || 0;

            let commodityClass = 'Class I';
            let hazardGroup = 'I';
            let designApproach = 'Light Hazard';
            let requiredDensity = '0.10';
            let requiredArea = '1500';
            let pipeScheduleReq = 'Light Hazard';
            let waterDurationReq = '30 minutes';
            let materialAnalysis = '';
            let sprinklerImplications = [];
            let systemImplications = [];
            let precautions = [];
            let alternateOptions = '';
            let recommendations = [];

            // Classification logic based on NFPA 13 commodity classifications
            if (hazardRating === 'non-combustible') {
                if (packagingMaterial === 'cardboard') {
                    commodityClass = 'Class II';
                    hazardGroup = 'I';
                    designApproach = 'Light Hazard';
                    requiredDensity = '0.10';
                    requiredArea = '1500';
                } else {
                    commodityClass = 'Class I';
                    hazardGroup = 'I';
                    designApproach = 'Light Hazard';
                    requiredDensity = '0.10';
                    requiredArea = '1500';
                }
            } else if (hazardRating === 'limited-combustible') {
                if (packagingMaterial === 'wood-crates') {
                    commodityClass = 'Class III';
                    hazardGroup = 'I';
                    designApproach = 'Ordinary Hazard Group 1';
                    requiredDensity = '0.15';
                    requiredArea = '2000';
                    pipeScheduleReq = 'Ordinary Hazard';
                    waterDurationReq = '60 minutes';
                } else {
                    commodityClass = 'Class II';
                    hazardGroup = 'I';
                    designApproach = 'Light Hazard';
                    requiredDensity = '0.10';
                    requiredArea = '1500';
                }
            } else if (hazardRating === 'combustible') {
                if (storageConfiguration === 'racks' && plasticContent > 10) {
                    commodityClass = 'Plastics (Group A)';
                    hazardGroup = 'III';
                    designApproach = 'Ordinary Hazard Group 2';
                    requiredDensity = '0.20';
                    requiredArea = '2500';
                    pipeScheduleReq = 'Ordinary Hazard Group 2';
                    waterDurationReq = '60 minutes';
                } else {
                    commodityClass = 'Class III';
                    hazardGroup = 'I';
                    designApproach = 'Ordinary Hazard Group 1';
                    requiredDensity = '0.15';
                    requiredArea = '2000';
                    pipeScheduleReq = 'Ordinary Hazard';
                    waterDurationReq = '60 minutes';
                }
            } else if (hazardRating === 'flammable') {
                commodityClass = 'Class IV';
                hazardGroup = 'II';
                designApproach = 'Extra Hazard Group 1';
                requiredDensity = '0.30';
                requiredArea = '3500';
                pipeScheduleReq = 'Extra Hazard';
                waterDurationReq = '90 minutes';
            }

            // Material-specific logic
            switch(primaryMaterial) {
                case 'metal':
                case 'glass':
                    materialAnalysis = 'Non-combustible materials in non-combustible packaging';
                    if (packagingMaterial === 'cardboard') {
                        materialAnalysis += ' classified as Class II due to cardboard packaging';
                    }
                    break;
                case 'paper-cardboard':
                    materialAnalysis = 'Combustible materials - classification depends on packaging and configuration';
                    if (packagingMaterial === 'none' && storageConfiguration === 'floor-stack') {
                        commodityClass = 'Class III';
                        designApproach = 'Ordinary Hazard Group 1';
                        requiredDensity = '0.15';
                        requiredArea = '2000';
                    }
                    break;
                case 'wood':
                    materialAnalysis = 'Combustible materials typically classified as Class III or higher';
                    if (packagingMaterial === 'wood-crates' || storageConfiguration === 'solid-pallet') {
                        materialAnalysis += ' - potentially Class IV due to solid wood construction';
                    }
                    break;
                case 'plastics':
                    materialAnalysis = `Plastic content: ${plasticContent}% by weight`;
                    if (plasticContent > 50) {
                        commodityClass = 'Plastics (Group A)';
                        hazardGroup = 'III';
                        designApproach = 'Ordinary Hazard Group 2';
                        requiredDensity = '0.20';
                        requiredArea = '2500';
                        pipeScheduleReq = 'Ordinary Hazard Group 2';
                        waterDurationReq = '90 minutes';
                    }
                    break;
                case 'textiles':
                    materialAnalysis = 'Combustible fibers - typically Class III or Class IV';
                    if (storageConfiguration === 'racks') {
                        materialAnalysis += ' - rack storage increases hazard classification';
                    }
                    break;
                case 'chemicals':
                case 'rubber':
                    commodityClass = 'Class IV';
                    hazardGroup = 'II';
                    designApproach = 'Extra Hazard Group 1';
                    requiredDensity = '0.30';
                    requiredArea = '3500';
                    pipeScheduleReq = 'Extra Hazard';
                    waterDurationReq = '90 minutes';
                    materialAnalysis = 'Flammable/hazardous materials require enhanced protection';
                    break;
                default:
                    materialAnalysis = 'Mixed or unknown materials require detailed analysis';
            }

            // Storage configuration implications
            if (storageConfiguration === 'racks') {
                sprinklerImplications.push('Large drop sprinklers may be required for rack heights >12 ft');
                systemImplications.push('Rack systems may require deluge or pre-action configuration');
                precautions.push('Rack storage significantly increases fire hazard');
            }

            if (storageConfiguration === 'bulk') {
                sprinklerImplications.push('ESFR sprinklers may be required for bulk storage');
                systemImplications.push('High pile storage requires special design considerations');
                precautions.push('Bulk storage presents unique suppression challenges');
            }

            // Container type implications
            if (containerType === 'void-packaging') {
                systemImplications.push('Void packaging increases ventilation and fire spread');
                precautions.push('Consider enhanced density requirements for void packaging');
            }

            // Material-specific implications
            if (primaryMaterial === 'plastics') {
                sprinklerImplications.push('Temperature rating must consider plastic melting characteristics');
                if (plasticContent > 25) {
                    systemImplications.push('High plastic content may require ESFR systems');
                    precautions.push('Plastic commodities have rapid fire development');
                }
            }

            if (hazardRating === 'flammable') {
                sprinklerImplications.push('Quick response sprinklers mandatory');
                systemImplications.push('Deluge systems typically required for flammable materials');
                precautions.push('Flammable materials require enhanced life safety measures');
            }

            // Alternate classifications
            if (packagingMaterial === 'cardboard' && primaryMaterial !== 'metal') {
                alternateOptions = 'Material may upgrade one classification level due to cardboard packaging';
            }

            if (storageConfiguration === 'racks' && primaryMaterial === 'plastics') {
                alternateOptions += ' Rack storage of plastics may require Group A classification';
            }

            // Default recommendations based on classification
            if (commodityClass.startsWith('Class I') || commodityClass.startsWith('Class II')) {
                recommendations.push('Standard light hazard design approach is appropriate');
            } else if (commodityClass.startsWith('Class III')) {
                recommendations.push('Consider Ordinary Hazard Group 1 design criteria');
            } else if (commodityClass.startsWith('Class IV')) {
                recommendations.push('Extra Hazard design criteria required - verify water supply capacity');
            }

            // Add specific recommendations based on findings
            if (primaryMaterial === 'plastics' && plasticContent > 20) {
                recommendations.push('Verify specific plastic classification per NFPA 13 Table A.4.3.2');
            }

            if (storageConfiguration === 'racks' && storageConfiguration !== 'floor-stack') {
                recommendations.push('Rack storage requires special hydraulic calculations');
            }

            // Display results
            document.getElementById('commodityClass').textContent = commodityClass;
            document.getElementById('hazardGroup').textContent = hazardGroup;
            document.getElementById('designApproach').textContent = designApproach;
            document.getElementById('requiredDensity').textContent = `${requiredDensity} gpm/sq ft`;
            document.getElementById('requiredArea').textContent = `${requiredArea} sq ft`;
            document.getElementById('pipeScheduleReq').textContent = pipeScheduleReq;
            document.getElementById('waterDurationReq').textContent = waterDurationReq;

            // Material analysis
            const analysisEl = document.getElementById('materialAnalysis');
            analysisEl.innerHTML = `<p>${materialAnalysis}</p>`;

            // Update implication lists
            const updateList = (listId, items) => {
                const list = document.getElementById(listId);
                list.innerHTML = '';
                items.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    list.appendChild(li);
                });
            };

            updateList('sprinklerImplications', sprinklerImplications);
            updateList('systemImplications', systemImplications);
            updateList('precautions', precautions);

            // Alternate considerations
            const alternateEl = document.getElementById('alternateOptions');
            if (alternateOptions) {
                alternateEl.innerHTML = `<p>${alternateOptions}</p>`;
                document.getElementById('alternateConsiderations').style.display = 'block';
            } else {
                document.getElementById('alternateConsiderations').style.display = 'none';
            }

            // Compliance status
            const complianceEl = document.getElementById('complianceStatus');
            const complianceMsgEl = document.getElementById('complianceMessage');
            complianceEl.className = 'compliance-status compliant';
            complianceMsgEl.textContent = `✓ Commodity classified as ${commodityClass} per NFPA 13`;

            // Update recommendations
            const recList = document.getElementById('recommendationsList');
            recList.innerHTML = '';
            recommendations.forEach(rec => {
                const li = document.createElement('li');
                li.textContent = rec;
                recList.appendChild(li);
            });

            document.getElementById('commodityResult').style.display = 'block';
            document.getElementById('commodityResult').scrollIntoView({ behavior: 'smooth' });
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
        .classification-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .class-metric {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border-radius: 12px;
            color: white;
        }

        .class-metric h4 {
            margin: 0 0 10px 0;
            font-size: 0.9em;
            opacity: 0.9;
        }

        .metric-value {
            font-size: 1.5em;
            font-weight: bold;
        }

        .classification-details {
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .detail-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }

        .detail-item h5 {
            margin: 0 0 8px 0;
            color: #dc2626;
            font-size: 0.9em;
        }

        .detail-item p {
            margin: 0;
            font-weight: bold;
            color: #374151;
            font-size: 1.1em;
        }

        .material-breakdown {
            margin: 20px 0;
            padding: 20px;
            background: #f1f5f9;
            border-radius: 8px;
        }

        .analysis-content {
            margin-top: 15px;
        }

        .analysis-content p {
            margin: 0;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border-left: 4px solid #dc2626;
            color: #475569;
        }

        .design-implications {
            margin: 20px 0;
        }

        .implications-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .implication-card {
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .implication-card h5 {
            margin: 0 0 15px 0;
            color: #dc2626;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .implication-card ul {
            margin: 0;
            padding-left: 20px;
        }

        .implication-card li {
            margin: 8px 0;
            color: #475569;
        }

        .alternate-considerations {
            margin: 20px 0;
            padding: 20px;
            background: #fef3c7;
            border-radius: 8px;
            border: 2px solid #f59e0b;
        }

        .alternate-considerations h5 {
            margin: 0 0 15px 0;
            color: #92400e;
        }

        .alternate-considerations p {
            margin: 0;
            color: #92400e;
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
<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
