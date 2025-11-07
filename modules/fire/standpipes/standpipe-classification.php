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
    <title>Standpipe Classification Calculator - Fire Protection Toolkit</title>
    <link rel="stylesheet" href="../../assets/css/fire.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="fire-container">
        <header class="fire-header">
            <h1><i class="fas fa-water"></i> Standpipe Classification</h1>
            <nav>
                <a href="../fire.html" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Fire Protection
                </a>
            </nav>
        </header>

        <div class="calculator-section">
            <h2>NFPA 14 Standpipe Classification System</h2>
            <p>Determine the appropriate standpipe class based on building height and usage requirements.</p>

            <form id="standpipeClassificationForm" class="fire-form">
                <div class="form-group">
                    <label for="buildingHeight">
                        <i class="fas fa-building"></i> Building Height (feet):
                    </label>
                    <input type="number" id="buildingHeight" name="buildingHeight" step="0.1" min="0" required>
                    <div class="unit-display">ft</div>
                </div>

                <div class="form-group">
                    <label for="occupancyType">
                        <i class="fas fa-home"></i> Occupancy Type:
                    </label>
                    <select id="occupancyType" name="occupancyType" required>
                        <option value="">Select Occupancy</option>
                        <option value="residential">Residential</option>
                        <option value="assembly">Assembly</option>
                        <option value="business">Business</option>
                        <option value="mercantile">Mercantile</option>
                        <option value="industrial">Industrial</option>
                        <option value="storage">Storage</option>
                        <option value="high-rise">High-Rise (>75 ft)</option>
                        <option value="healthcare">Healthcare</option>
                        <option value="educational">Educational</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for=" sprinklerSystem">
                        <i class="fas fa-spray-can"></i> Sprinkler System Present:
                    </label>
                    <select id="sprinklerSystem" name="sprinklerSystem" required>
                        <option value="">Select Option</option>
                        <option value="full">Full Sprinkler System</option>
                        <option value="partial">Partial Sprinkler System</option>
                        <option value="none">No Sprinkler System</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fireDepartmentAccess">
                        <i class="fas fa-fire-truck"></i> Fire Department Access:
                    </label>
                    <select id="fireDepartmentAccess" name="fireDepartmentAccess" required>
                        <option value="">Select Access Level</option>
                        <option value="full">Full Fire Department Access</option>
                        <option value="limited">Limited Fire Department Access</option>
                        <option value="poor">Poor Fire Department Access</option>
                    </select>
                </div>

                <button type="submit" class="fire-btn">
                    <i class="fas fa-calculator"></i> Classify Standpipe System
                </button>
            </form>

            <div id="classificationResult" class="result-section" style="display: none;">
                <h3><i class="fas fa-clipboard-check"></i> Standpipe Classification Results</h3>
                <div class="result-content">
                    <div class="classification-card">
                        <h4>Required Standpipe Class</h4>
                        <div class="class-badge" id="classBadge">
                            <span id="classType">Class I</span>
                        </div>
                        <div class="class-description" id="classDescription">
                            2½-inch outlets for fire department use
                        </div>
                    </div>

                    <div class="requirements-grid">
                        <div class="requirement-item">
                            <h5><i class="fas fa-ruler"></i> Pipe Sizing Requirements</h5>
                            <p id="pipeSizing">4-inch minimum for main risers</p>
                        </div>
                        <div class="requirement-item">
                            <h5><i class="fas fa-tachometer-alt"></i> Pressure Requirements</h5>
                            <p id="pressureRequirements">100 psi maximum at top floor</p>
                        </div>
                        <div class="requirement-item">
                            <h5><i class="fas fa-map-marker-alt"></i> Location Requirements</h5>
                            <p id="locationRequirements">Within 5 feet of exits</p>
                        </div>
                    </div>

                    <div class="compliance-status" id="complianceStatus">
                        <i class="fas fa-check-circle"></i> System meets NFPA 14 requirements
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
        document.getElementById('standpipeClassificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const buildingHeight = parseFloat(document.getElementById('buildingHeight').value);
            const occupancyType = document.getElementById('occupancyType').value;
            const sprinklerSystem = document.getElementById('sprinklerSystem').value;
            const fireDepartmentAccess = document.getElementById('fireDepartmentAccess').value;

            let classType = 'Class I';
            let classDescription = '2½-inch outlets for fire department use';
            let pipeSizing = '4-inch minimum for main risers';
            let pressureRequirements = '100 psi maximum at top floor';
            let locationRequirements = 'Within 5 feet of exits';
            let complianceStatus = 'System meets NFPA 14 requirements';
            let recommendations = [];

            // Classification logic based on NFPA 14
            if (buildingHeight <= 30) {
                classType = 'Class I';
                classDescription = '2½-inch outlets for fire department use';
                pipeSizing = '4-inch minimum for main risers';
                pressureRequirements = '65 psi minimum at top floor';
                locationRequirements = 'Within 5 feet of exits';
            } else if (buildingHeight <= 75) {
                classType = 'Class II';
                classDescription = '1½-inch outlets with 1¾-inch hose';
                pipeSizing = '4-inch minimum for main risers';
                pressureRequirements = '65 psi minimum at top floor';
                locationRequirements = 'Within 100 feet of any point';
                
                if (occupancyType === 'high-rise' || occupancyType === 'healthcare') {
                    classType = 'Class I';
                    classDescription = '2½-inch outlets for fire department use';
                    recommendations.push('High-rise and healthcare require Class I systems');
                }
            } else {
                classType = 'Class I';
                classDescription = '2½-inch outlets for fire department use';
                pipeSizing = '6-inch minimum for main risers above 75 feet';
                pressureRequirements = '100 psi maximum at top floor';
                locationRequirements = 'Within 5 feet of exits';
                recommendations.push('High-rise buildings require Class I systems');
                
                if (sprinklerSystem === 'none') {
                    recommendations.push('Consider installing sprinkler system for additional protection');
                }
            }

            // Access level adjustments
            if (fireDepartmentAccess === 'poor') {
                classType = 'Class I';
                recommendations.push('Poor fire department access requires robust Class I system');
                pressureRequirements = '100 psi minimum at top floor';
            }

            // Occupancy-specific requirements
            if (occupancyType === 'assembly' && buildingHeight > 50) {
                recommendations.push('Assembly occupancies >50 ft require enhanced water supply');
            }

            if (occupancyType === 'industrial' && sprinklerSystem === 'none') {
                recommendations.push('Industrial occupancies without sprinklers require special consideration');
            }

            // Display results
            document.getElementById('classType').textContent = classType;
            document.getElementById('classDescription').textContent = classDescription;
            document.getElementById('pipeSizing').textContent = pipeSizing;
            document.getElementById('pressureRequirements').textContent = pressureRequirements;
            document.getElementById('locationRequirements').textContent = locationRequirements;
            
            // Compliance status styling
            const complianceEl = document.getElementById('complianceStatus');
            complianceEl.className = 'compliance-status compliant';
            complianceEl.innerHTML = '<i class="fas fa-check-circle"></i> System meets NFPA 14 requirements';
            
            // Update recommendations
            const recList = document.getElementById('recommendationsList');
            recList.innerHTML = '';
            if (recommendations.length === 0) {
                recommendations = ['Standard NFPA 14 requirements apply'];
            }
            recommendations.forEach(rec => {
                const li = document.createElement('li');
                li.textContent = rec;
                recList.appendChild(li);
            });

            document.getElementById('classificationResult').style.display = 'block';
            document.getElementById('classificationResult').scrollIntoView({ behavior: 'smooth' });
        });

        // Add some visual feedback
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
        .classification-card {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border-radius: 12px;
            color: white;
        }

        .class-badge {
            font-size: 2em;
            font-weight: bold;
            margin: 10px 0;
            padding: 15px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
        }

        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .requirement-item {
            padding: 15px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
        }

        .requirement-item h5 {
            margin: 0 0 10px 0;
            color: #dc2626;
        }

        .requirement-item p {
            margin: 0;
            color: #475569;
        }

        .compliance-status {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-weight: bold;
        }

        .compliance-status.compliant {
            background: #dcfce7;
            color: #166534;
            border: 2px solid #16a34a;
        }

        .field-active {
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }
    </style>
</body>
</html>
