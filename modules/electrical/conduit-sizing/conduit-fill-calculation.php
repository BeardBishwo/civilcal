<?php
// modules/electrical/conduit-sizing/conduit-fill-calculation.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conduit Fill Calculator - AEC Toolkit</title>
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

        .fill-note {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .wire-list {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .wire-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            margin: 0.25rem 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .add-wire-btn, .remove-wire-btn {
            background: rgba(240, 147, 251, 0.3);
            border: 1px solid rgba(240, 147, 251, 0.5);
            color: var(--light);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-wire-btn:hover, .remove-wire-btn:hover {
            background: rgba(240, 147, 251, 0.5);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-wrapper">
            <h1><i class="fas fa-pipe me-2"></i>Conduit Fill Calculation</h1>
            <form id="fill-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="conduit-type"><i class="fas fa-cog me-2"></i>Conduit Type</label>
                            <select id="conduit-type" class="form-control" required>
                                <option value="pvc">PVC Schedule 40</option>
                                <option value="pvc80">PVC Schedule 80</option>
                                <option value="emt">EMT</option>
                                <option value="rigid">Rigid Metal</option>
                                <option value="imc">IMC</option>
                                <option value="flex">Flexible Metal</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="conduit-size"><i class="fas fa-ruler-vertical me-2"></i>Conduit Size (inches)</label>
                            <select id="conduit-size" class="form-control" required>
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
                                <option value="5">5"</option>
                                <option value="6">6"</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="insulation-type"><i class="fas fa-layer-group me-2"></i>Wire Insulation Type</label>
                            <select id="insulation-type" class="form-control" required>
                                <option value="THHN">THHN</option>
                                <option value="THW">THW</option>
                                <option value="XHHW">XHHW</option>
                                <option value="UF">UF</option>
                                <option value="THWN">THWN</option>
                                <option value="MTW">MTW</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="fill-type"><i class="fas fa-percentage me-2"></i>Fill Type</label>
                            <select id="fill-type" class="form-control" required>
                                <option value="single">Single Conductor</option>
                                <option value="multiple">Multiple Conductors</option>
                                <option value="cable">Cable Assembly</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="wire-list">
                    <h4><i class="fas fa-list me-2"></i>Wires/Cables in Conduit</h4>
                    <div id="wire-list-container">
                        <!-- Wire entries will be added here -->
                    </div>
                    <button type="button" class="add-wire-btn" onclick="addWireEntry()">
                        <i class="fas fa-plus me-2"></i>Add Wire/Cable
                    </button>
                </div>

                <button type="submit" class="btn-calculate">Calculate Fill</button>
            </form>
            
            <div class="fill-note">
                <i class="fas fa-info-circle me-2"></i>
                Conduit fill calculations per NEC Article 352 for different conduit types and wire configurations.
            </div>
            
            <div class="result-area" id="result-area">
                <h3><i class="fas fa-calculator me-2"></i>Conduit Fill Results</h3>
                <div class="result-grid" id="result-grid"></div>
            </div>
        </div>
        <a href="../../../electrical.php" class="back-link">Back to Electrical Module</a>
    </div>

    <script>
        // Wire areas in square inches (nominal)
        const wireAreas = {
            '14': 0.0097, '12': 0.0133, '10': 0.0211, '8': 0.0366,
            '6': 0.0507, '4': 0.0824, '2': 0.1158, '1': 0.1562,
            '1/0': 0.1855, '2/0': 0.2223, '3/0': 0.2679, '4/0': 0.3237,
            '250': 0.3718, '300': 0.4348, '350': 0.4978, '400': 0.5608,
            '500': 0.6868, '600': 0.7928, '700': 0.8958, '750': 0.9528,
            '800': 0.9988, '900': 1.1048, '1000': 1.2108
        };

        // Cable areas (typical)
        const cableAreas = {
            '12-2': 0.128, '12-3': 0.168, '12-4': 0.209,
            '10-2': 0.194, '10-3': 0.258, '10-4': 0.322,
            '8-2': 0.310, '8-3': 0.415, '8-4': 0.520,
            '6-2': 0.485, '6-3': 0.650, '6-4': 0.815
        };

        // Conduit internal areas (square inches)
        const conduitAreas = {
            // PVC Schedule 40
            'pvc': {
                '0.5': 0.122, '0.75': 0.213, '1': 0.304, '1.25': 0.495,
                '1.5': 0.701, '2': 0.917, '2.5': 1.342, '3': 1.792,
                '3.5': 2.343, '4': 2.945, '5': 4.503, '6': 6.449
            },
            // PVC Schedule 80
            'pvc80': {
                '0.5': 0.104, '0.75': 0.184, '1': 0.268, '1.25': 0.443,
                '1.5': 0.632, '2': 0.832, '2.5': 1.240, '3': 1.690,
                '3.5': 2.241, '4': 2.843, '5': 4.401, '6': 6.347
            },
            // EMT
            'emt': {
                '0.5': 0.151, '0.75': 0.254, '1': 0.364, '1.25': 0.591,
                '1.5': 0.818, '2': 1.043, '2.5': 1.497, '3': 2.003,
                '3.5': 2.548, '4': 3.132, '5': 4.784, '6': 6.833
            },
            // Rigid Metal
            'rigid': {
                '0.5': 0.164, '0.75': 0.272, '1': 0.382, '1.25': 0.608,
                '1.5': 0.832, '2': 1.043, '2.5': 1.496, '3': 2.002,
                '3.5': 2.547, '4': 3.132, '5': 4.783, '6': 6.832
            },
            // IMC
            'imc': {
                '0.5': 0.164, '0.75': 0.272, '1': 0.382, '1.25': 0.608,
                '1.5': 0.832, '2': 1.043, '2.5': 1.496, '3': 2.002,
                '3.5': 2.547, '4': 3.132, '5': 4.783, '6': 6.832
            },
            // Flexible Metal
            'flex': {
                '0.5': 0.134, '0.75': 0.220, '1': 0.321, '1.25': 0.512,
                '1.5': 0.724, '2': 0.945, '2.5': 1.360, '3': 1.810
            }
        };

        // Maximum fill percentages
        const maxFillPercentages = {
            'single': 53,  // 53% for single conductor
            'multiple': 40, // 40% for multiple conductors
            'cable': 40     // 40% for cables
        };

        let wireCounter = 0;

        function addWireEntry() {
            wireCounter++;
            const container = document.getElementById('wire-list-container');
            
            const wireEntry = document.createElement('div');
            wireEntry.className = 'wire-item';
            wireEntry.id = `wire-${wireCounter}`;
            
            wireEntry.innerHTML = `
                <select class="form-control" style="width: 120px;" onchange="updateWireArea(${wireCounter})">
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
                    <option value="250">250 kcmil</option>
                    <option value="300">300 kcmil</option>
                    <option value="350">350 kcmil</option>
                    <option value="400">400 kcmil</option>
                    <option value="500">500 kcmil</option>
                    <option value="600">600 kcmil</option>
                    <option value="12-2">12/2 Cable</option>
                    <option value="12-3">12/3 Cable</option>
                    <option value="10-2">10/2 Cable</option>
                    <option value="10-3">10/3 Cable</option>
                    <option value="8-2">8/2 Cable</option>
                </select>
                <input type="number" class="form-control" style="width: 80px;" placeholder="Qty" value="1" min="1" max="50">
                <span id="area-${wireCounter}" style="width: 80px; text-align: right;">0.0097 in²</span>
                <button type="button" class="remove-wire-btn" onclick="removeWireEntry(${wireCounter})">
                    <i class="fas fa-minus"></i>
                </button>
            `;
            
            container.appendChild(wireEntry);
            updateWireArea(wireCounter);
        }

        function removeWireEntry(id) {
            const wireElement = document.getElementById(`wire-${id}`);
            if (wireElement) {
                wireElement.remove();
            }
        }

        function updateWireArea(id) {
            const wireSelect = document.querySelector(`#wire-${id} select`);
            const areaSpan = document.getElementById(`area-${id}`);
            const wireSize = wireSelect.value;
            
            let area = 0;
            if (wireAreas[wireSize]) {
                area = wireAreas[wireSize];
            } else if (cableAreas[wireSize]) {
                area = cableAreas[wireSize];
            }
            
            areaSpan.textContent = `${area.toFixed(4)} in²`;
        }

        function calculateConduitFill() {
            const conduitType = document.getElementById('conduit-type').value;
            const conduitSize = document.getElementById('conduit-size').value;
            const insulationType = document.getElementById('insulation-type').value;
            const fillType = document.getElementById('fill-type').value;
            
            // Get conduit internal area
            const conduitArea = conduitAreas[conduitType][conduitSize];
            const maxFill = maxFillPercentages[fillType];
            
            // Calculate total wire area
            let totalWireArea = 0;
            const wireEntries = document.querySelectorAll('#wire-list-container .wire-item');
            
            wireEntries.forEach(entry => {
                const wireSelect = entry.querySelector('select');
                const quantityInput = entry.querySelector('input[type="number"]');
                const wireSize = wireSelect.value;
                const quantity = parseInt(quantityInput.value) || 1;
                
                let wireArea = 0;
                if (wireAreas[wireSize]) {
                    wireArea = wireAreas[wireSize];
                } else if (cableAreas[wireSize]) {
                    wireArea = cableAreas[wireSize];
                }
                
                totalWireArea += wireArea * quantity;
            });
            
            // Calculate fill percentage
            const fillPercentage = (totalWireArea / conduitArea) * 100;
            const maxAllowedArea = conduitArea * (maxFill / 100);
            const remainingArea = maxAllowedArea - totalWireArea;
            
            // Assessment
            let assessment = 'Excellent';
            let assessmentClass = '';
            
            if (fillPercentage > maxFill) {
                assessment = 'Overfilled - NEC Violation';
                assessmentClass = 'danger';
            } else if (fillPercentage > maxFill * 0.9) {
                assessment = 'Full - Consider Larger Conduit';
                assessmentClass = 'warning';
            } else if (fillPercentage > maxFill * 0.75) {
                assessment = 'Good';
                assessmentClass = '';
            } else if (fillPercentage > maxFill * 0.5) {
                assessment = 'Fair';
                assessmentClass = '';
            } else {
                assessment = 'Conservative';
                assessmentClass = '';
            }
            
            // Alternative conduit sizes
            const alternativeSizes = [];
            const sizes = Object.keys(conduitAreas[conduitType]);
            const currentSizeIndex = sizes.indexOf(conduitSize);
            
            for (let i = currentSizeIndex + 1; i < sizes.length; i++) {
                const size = sizes[i];
                const altArea = conduitAreas[conduitType][size];
                const altFill = (totalWireArea / altArea) * 100;
                
                if (altFill <= maxFill * 0.8) { // 80% of max for good design
                    alternativeSizes.push({
                        size: size,
                        fill: altFill
                    });
                    if (alternativeSizes.length >= 2) break; // Show top 2 options
                }
            }
            
            return {
                conduitType: conduitType,
                conduitSize: conduitSize,
                insulationType: insulationType,
                fillType: fillType,
                conduitArea: conduitArea,
                maxFill: maxFill,
                maxAllowedArea: maxAllowedArea,
                totalWireArea: totalWireArea,
                fillPercentage: fillPercentage,
                remainingArea: remainingArea,
                assessment: assessment,
                assessmentClass: assessmentClass,
                alternativeSizes: alternativeSizes,
                wireCount: wireEntries.length
            };
        }

        document.getElementById('fill-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const result = calculateConduitFill();
            
            const conduitTypeText = {
                'pvc': 'PVC Schedule 40',
                'pvc80': 'PVC Schedule 80',
                'emt': 'EMT',
                'rigid': 'Rigid Metal',
                'imc': 'IMC',
                'flex': 'Flexible Metal'
            };
            
            const fillTypeText = {
                'single': 'Single Conductor (53% max)',
                'multiple': 'Multiple Conductors (40% max)',
                'cable': 'Cable Assembly (40% max)'
            };
            
            let alternativesHtml = '';
            if (result.alternativeSizes.length > 0) {
                alternativesHtml = result.alternativeSizes.map(alt => 
                    `<p><strong>${alt.size}" Alternative:</strong> ${alt.fill.toFixed(1)}% fill</p>`
                ).join('');
            }
            
            const resultHtml = `
                <div class="result-item">
                    <strong>Conduit Type:</strong><br>${conduitTypeText[result.conduitType]}
                </div>
                <div class="result-item">
                    <strong>Conduit Size:</strong><br>${result.conduitSize}" (${result.conduitArea.toFixed(3)} in²)
                </div>
                <div class="result-item">
                    <strong>Fill Type:</strong><br>${fillTypeText[result.fillType]}
                </div>
                <div class="result-item">
                    <strong>Number of Wires/Cables:</strong><br>${result.wireCount}
                </div>
                <div class="result-item">
                    <strong>Total Wire Area:</strong><br>${result.totalWireArea.toFixed(4)} in²
                </div>
                <div class="result-item">
                    <strong>Max Fill Area:</strong><br>${result.maxAllowedArea.toFixed(4)} in² (${result.maxFill}%)
                </div>
                <div class="result-item">
                    <strong>Remaining Capacity:</strong><br>${result.remainingArea.toFixed(4)} in²
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Actual Fill %:</strong><br>${result.fillPercentage.toFixed(2)}%
                </div>
                <div class="result-item ${result.assessmentClass}">
                    <strong>Assessment:</strong><br>${result.assessment}
                </div>
                ${alternativesHtml ? `<div class="result-item">${alternativesHtml}</div>` : ''}
            `;
            
            document.getElementById('result-grid').innerHTML = resultHtml;
            document.getElementById('result-area').style.display = 'block';
            
            // Save calculation
            saveCalculation('Conduit Fill', `${result.conduitSize}" ${conduitTypeText[result.conduitType]} → ${result.fillPercentage.toFixed(1)}% fill`);
        });

        function saveCalculation(type, calculation) {
            let recent = JSON.parse(localStorage.getItem('recentConduitFillCalculations') || '[]');
            recent.unshift({
                type: type,
                calculation: calculation,
                timestamp: new Date().toLocaleString()
            });
            
            recent = recent.slice(0, 5);
            localStorage.setItem('recentConduitFillCalculations', JSON.stringify(recent));
        }

        // Initialize with one wire entry
        document.addEventListener('DOMContentLoaded', function() {
            addWireEntry();
        });
    </script>
</body>
</html>
