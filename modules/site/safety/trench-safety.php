<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trench Safety Calculator - Site Tools</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --accent-color: #f59e0b;
            --background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--background);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header .breadcrumb {
            background: var(--glass-bg);
            border-radius: 10px;
            padding: 10px 20px;
            display: inline-block;
            margin-top: 15px;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            border: none;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .card-text {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .card-body {
            padding: 30px;
        }

        .form-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .alert {
            border-radius: 8px;
            padding: 15px;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-danger {
            background: #dc2626;
            color: white;
        }

        .badge-warning {
            background: #f59e0b;
            color: white;
        }

        .badge-info {
            background: var(--primary-color);
            color: white;
        }

        .border-primary {
            border-color: var(--primary-color) !important;
        }

        .border-success {
            border-color: #059669 !important;
        }

        .border-warning {
            border-color: #f59e0b !important;
        }

        .border-info {
            border-color: var(--accent-color) !important;
        }

        .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            border-color: #dc2626;
            color: #dc2626;
        }

        .text-success {
            color: #059669 !important;
        }

        .text-danger {
            color: #dc2626 !important;
        }

        .small {
            font-size: 0.85rem;
        }

        h5, h6 {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .list-unstyled li {
            margin-bottom: 5px;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .card-body {
                padding: 20px;
            }
        }
    </style>
<link rel="stylesheet" href="../../../public/assets/css/global-notifications.css">
</head>
<body>
    <div class="container-fluid">
        <div class="header">
            <h1><i class="fas fa-tools me-3"></i>Site Tools</h1>
            <div class="breadcrumb">
                <a href="../../index.php">Home</a> / 
                <a href="../../modules/site/index.php">Site Tools</a> / 
                <span>Trench Safety Calculator</span>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-hard-hat me-2"></i>
                            Trench Safety Calculator
                        </h2>
                        <p class="card-text">Calculate trench safety requirements and protection systems</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="soilType" class="form-label">Soil Type</label>
                                    <select class="form-select" id="soilType" required>
                                        <option value="">Select Soil</option>
                                        <option value="stable-rock">Stable Rock</option>
                                        <option value="type-a">Type A (Dense/Competent)</option>
                                        <option value="type-b">Type B (Medium Dense)</option>
                                        <option value="type-c">Type C (Loose/Sandy)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="trenchDepth" class="form-label">Trench Depth (ft)</label>
                                    <input type="number" class="form-control" id="trenchDepth" step="0.5" placeholder="8" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="trenchWidth" class="form-label">Trench Width (ft)</label>
                                    <input type="number" class="form-control" id="trenchWidth" step="0.5" placeholder="4" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="protectionMethod" class="form-label">Protection Method</label>
                                    <select class="form-select" id="protectionMethod" required>
                                        <option value="">Select Method</option>
                                        <option value="sloping">Sloping/Shoring</option>
                                        <option value="shielding">Trench Box/Shield</option>
                                        <option value="shoring">Hydraulic Shoring</option>
                                        <option value="bench">Benching</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="groundwater" class="form-label">Groundwater Present</label>
                                    <select class="form-select" id="groundwater">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                        <option value="seasonal">Seasonal</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Excavation Duration (days)</label>
                                    <input type="number" class="form-control" id="duration" step="1" placeholder="14" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="trafficLoading" class="form-label">Surface Traffic Loading</label>
                                    <select class="form-select" id="trafficLoading">
                                        <option value="none">None (Open Field)</option>
                                        <option value="light">Light (Pedestrian Only)</option>
                                        <option value="medium">Medium (Cars/Light Trucks)</option>
                                        <option value="heavy">Heavy (Heavy Trucks/Equipment)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary" onclick="calculateTrenchSafety()">
                            <i class="fas fa-calculator me-2"></i>
                            Calculate Trench Safety
                        </button>

                    <div id="results" class="mt-4" style="display: none;">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-chart-line"></i> Trench Safety Results</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Required Slope Ratio:</strong> <span id="slopeRatio">0</span></p>
                                    <p><strong>Top Width Required:</strong> <span id="topWidth">0</span> ft</p>
                                    <p><strong>Protection Level:</strong> <span id="protectionLevel" class="badge">-</span></p>
                                    <p><strong>Safety Classification:</strong> <span id="safetyClass">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Excavation Volume:</strong> <span id="excavationVolume">0</span> CY</p>
                                    <p><strong>Backfill Volume:</strong> <span id="backfillVolume">0</span> CY</p>
                                    <p><strong>Protection Cost:</strong> $<span id="protectionCost">0</span></p>
                                    <p><strong>Compliance Status:</strong> <span id="complianceStatus">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card glass-card mt-3">
    <div class="card-header">
        <h5><i class="fas fa-info-circle"></i> OSHA Trench Safety Requirements</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Maximum Depths Without Protection</h6>
                <ul class="list-unstyled">
                    <li>• Type A Soil: 20 feet</li>
                    <li>• Type B Soil: 12 feet</li>
                    <li>• Type C Soil: 6 feet</li>
                    <li>• Stable Rock: 25 feet</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Required Safety Measures</h6>
                <ul class="list-unstyled">
                    <li>• Competent person on site</li>
                    <li>• Daily inspections required</li>
                    <li>• Access/egress every 25 feet</li>
                    <li>• Spoil pile minimum 2 feet back</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card glass-card mt-3">
    <div class="card-header">
        <h5><i class="fas fa-tools"></i> Protection System Selection</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <h6><i class="fas fa-angle-right"></i> Sloping</h6>
                        <ul class="list-unstyled small">
                            <li>• Type A: 3/4H:1V</li>
                            <li>• Type B: 1H:1V</li>
                            <li>• Type C: 1½H:1V</li>
                            <li>• No equipment in trench</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <h6><i class="fas fa-shield-alt"></i> Shielding</h6>
                        <ul class="list-unstyled small">
                            <li>• Trench boxes/shields</li>
                            <li>• Workers protected inside</li>
                            <li>• Limited depth capability</li>
                            <li>• Requires lifting equipment</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <h6><i class="fas fa-compress-arrows-alt"></i> Shoring</h6>
                        <ul class="list-unstyled small">
                            <li>• Hydraulic/pressure systems</li>
                            <li>• Adjustable sizing</li>
                            <li>• More complex installation</li>
                            <li>• Good for utility work</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body">
                        <h6><i class="fas fa-layer-group"></i> Benching</h6>
                        <ul class="list-unstyled small">
                            <li>• Excavated in steps</li>
                            <li>• Type A soil only</li>
                            <li>• Limited to 20 feet</li>
                            <li>• No vertical cuts >5 ft</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card glass-card mt-3">
    <div class="card-header">
        <h5><i class="fas fa-exclamation-triangle"></i> Critical Safety Alerts</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-danger">
            <h6><i class="fas fa-warning"></i> Immediate Excavation Stop Conditions</h6>
            <div class="row">
                <div class="col-md-6">
                    <strong>Soil/Poor Weather:</strong>
                    <ul>
                        <li>• Cracks, bulging, or spalling observed</li>
                        <li>• Water seeping into trench</li>
                        <li>• Heavy rain or freezing temperatures</li>
                        <li>• Vibrations from nearby traffic/equipment</li>
                        <li>• Fence posts or utilities leaning into trench</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <strong>Equipment/Failure:</strong>
                    <ul>
                        <li>• Hydraulic system failures</li>
                        <li>• Shield or shoring displacement</li>
                        <li>• Spoil pile too close to edge</li>
                        <li>• Confined space atmosphere concerns</li>
                        <li>• Rescue equipment not readily available</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Trench Safety Calculator JavaScript
function calculateTrenchSafety() {
    const soilType = document.getElementById('soilType').value;
    const trenchDepth = parseFloat(document.getElementById('trenchDepth').value);
    const trenchWidth = parseFloat(document.getElementById('trenchWidth').value);
    const protectionMethod = document.getElementById('protectionMethod').value;
    const groundwater = document.getElementById('groundwater').value;
    const duration = parseFloat(document.getElementById('duration').value);
    const trafficLoading = document.getElementById('trafficLoading').value;
    
    // Validate required fields
    if (!soilType || !trenchDepth || !trenchWidth || !protectionMethod || !duration) {
        showNotification('Please fill in all required fields', 'info');
        return;
    }
    
    // Soil slope ratios
    const slopeRatios = {
        'stable-rock': { ratio: '1/2H:1V', multiplier: 0.5, maxDepth: 25 },
        'type-a': { ratio: '3/4H:1V', multiplier: 0.75, maxDepth: 20 },
        'type-b': { ratio: '1H:1V', multiplier: 1.0, maxDepth: 12 },
        'type-c': { ratio: '1½H:1V', multiplier: 1.5, maxDepth: 6 }
    };
    
    const soilData = slopeRatios[soilType] || slopeRatios['type-b'];
    const requiredSlopeRatio = soilData.ratio;
    
    // Calculate required dimensions
    const horizontalSetback = trenchDepth * soilData.multiplier;
    const topWidthRequired = trenchWidth + (2 * horizontalSetback);
    const excavationVolume = trenchDepth * trenchWidth * 100 / 27; // Per 100 LF
    const backfillVolume = excavationVolume * 0.8; // 80% of excavated volume
    
    // Determine protection requirements
    let protectionLevel = '';
    let safetyClass = '';
    let complianceStatus = '';
    let protectionCost = 0;
    
    if (trenchDepth > soilData.maxDepth) {
        protectionLevel = 'MANDATORY';
        safetyClass = 'CRITICAL - Depth exceeds safe limit';
        complianceStatus = 'NON-COMPLIANT';
        protectionCost = duration * 500; // High protection cost
    } else if (trenchDepth > soilData.maxDepth * 0.75) {
        protectionLevel = 'REQUIRED';
        safetyClass = 'HIGH RISK';
        complianceStatus = 'COMPLIANT';
        protectionCost = duration * 300;
    } else if (trenchDepth > 5) {
        protectionLevel = 'RECOMMENDED';
        safetyClass = 'MODERATE RISK';
        complianceStatus = 'COMPLIANT';
        protectionCost = duration * 150;
    } else {
        protectionLevel = 'OPTIONAL';
        safetyClass = 'LOW RISK';
        complianceStatus = 'COMPLIANT';
        protectionCost = duration * 50;
    }
    
    // Adjust for groundwater and traffic
    if (groundwater === 'yes' || groundwater === 'seasonal') {
        protectionLevel = 'MANDATORY';
        protectionCost *= 1.5;
    }
    
    if (trafficLoading === 'heavy') {
        protectionCost *= 1.3;
    } else if (trafficLoading === 'medium') {
        protectionCost *= 1.1;
    }
    
    // Display results
    document.getElementById('slopeRatio').textContent = requiredSlopeRatio;
    document.getElementById('topWidth').textContent = topWidthRequired.toFixed(1);
    
    const protectionLevelElement = document.getElementById('protectionLevel');
    protectionLevelElement.textContent = protectionLevel;
    protectionLevelElement.className = `badge ${protectionLevel === 'MANDATORY' ? 'badge-danger' : protectionLevel === 'REQUIRED' ? 'badge-warning' : 'badge-info'}`;
    
    document.getElementById('safetyClass').textContent = safetyClass;
    document.getElementById('excavationVolume').textContent = excavationVolume.toFixed(1);
    document.getElementById('backfillVolume').textContent = backfillVolume.toFixed(1);
    document.getElementById('protectionCost').textContent = Math.round(protectionCost).toLocaleString();
    
    const complianceStatusElement = document.getElementById('complianceStatus');
    complianceStatusElement.textContent = complianceStatus;
    complianceStatusElement.className = complianceStatus === 'COMPLIANT' ? 'text-success' : 'text-danger';
    
    document.getElementById('results').style.display = 'block';
    
    // Save to localStorage
    const calculation = {
        type: 'Trench Safety',
        timestamp: new Date().toISOString(),
        inputs: {
            soilType, trenchDepth, trenchWidth, protectionMethod, 
            groundwater, duration, trafficLoading
        },
        results: {
            requiredSlopeRatio, topWidthRequired, protectionLevel, safetyClass, 
            excavationVolume, backfillVolume, protectionCost, complianceStatus
        }
    };
    
    saveToLocalStorage('trench_safety', calculation);
}

function saveToLocalStorage(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
}

function saveCalculation(calculation) {
    const savedCalculations = JSON.parse(localStorage.getItem('saved_calculations') || '[]');
    savedCalculations.push(calculation);
    localStorage.setItem('saved_calculations', JSON.stringify(savedCalculations));
    showNotification('Calculation saved successfully!', 'info');
}

function loadCalculations(key) {
    try {
        const data = localStorage.getItem(key);
        if (data) {
            const calculation = JSON.parse(data);
            // Populate form fields if they exist
            Object.keys(calculation.inputs || {}).forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    element.value = calculation.inputs[field];
                }
            });
        }
    } catch (e) {
        console.log('Error loading from localStorage:', e);
    }
}

// Load saved data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCalculations('trench_safety');
});
</script>

<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
