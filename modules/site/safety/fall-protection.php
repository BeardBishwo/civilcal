<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fall Protection Calculator - Site Tools</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --accent-color: #f59e0b;
            --background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .btn-success {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-color), #475569);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.4);
        }

        .result-item {
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-item strong {
            color: var(--secondary-color);
            display: block;
            margin-bottom: 5px;
        }

        .result-item span {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        h5 {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-color);
        }

        .section-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
            margin: 30px 0;
            border: none;
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
                <span>Fall Protection Calculator</span>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-shield-alt me-2"></i>
                            Fall Protection Calculator
                        </h2>
                        <p class="card-text">Calculate fall protection requirements and safety factors for construction work at height</p>
                    </div>
                    <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>Work Height & Conditions</h5>
                            <div class="mb-3">
                                <label for="workHeight" class="form-label">Working Height (feet)</label>
                                <input type="number" class="form-control" id="workHeight" step="0.1" min="0" placeholder="Enter working height">
                            </div>
                            <div class="mb-3">
                                <label for="surfaceType" class="form-label">Surface Type</label>
                                <select class="form-select" id="surfaceType">
                                    <option value="">Select surface type</option>
                                    <option value="concrete">Concrete</option>
                                    <option value="steel">Steel</option>
                                    <option value="wood">Wood</option>
                                    <option value="membrane">Membrane Roof</option>
                                    <option value="gravel">Gravel</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="slopeAngle" class="form-label">Surface Slope (degrees)</label>
                                <input type="number" class="form-control" id="slopeAngle" step="0.1" min="0" max="90" placeholder="0 for flat surface">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h5>Protection System</h5>
                            <div class="mb-3">
                                <label for="systemType" class="form-label">Protection System</label>
                                <select class="form-select" id="systemType">
                                    <option value="">Select system type</option>
                                    <option value="personal_fall_arrest">Personal Fall Arrest System</option>
                                    <option value="guardrail">Guardrail System</option>
                                    <option value="safety_net">Safety Net System</option>
                                    <option value="warning_line">Warning Line System</option>
                                    <option value="controlled_access">Controlled Access Zone</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="fallDistance" class="form-label">Total Fall Distance (feet)</label>
                                <input type="number" class="form-control" id="fallDistance" step="0.1" min="0" placeholder="Calculate or enter manually">
                            </div>
                            <div class="mb-3">
                                <label for="clearanceHeight" class="form-label">Clearance Height (feet)</label>
                                <input type="number" class="form-control" id="clearanceHeight" step="0.1" min="0" placeholder="Height to obstruction below">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="calculateFallProtection()">
                                <i class="fas fa-calculator me-2"></i>
                                Calculate Fall Protection
                            </button>
                            <button type="button" class="btn btn-secondary ms-2" onclick="clearForm()">
                                <i class="fas fa-eraser me-2"></i>
                                Clear
                            </button>
                            <button type="button" class="btn btn-success ms-2" onclick="saveCalculation()">
                                <i class="fas fa-save me-2"></i>
                                Save
                            </button>
                        </div>
                    </div>
                    
                    <div id="results" class="mt-4" style="display: none;">
                        <h5>Calculation Results</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="result-item">
                                    <strong>Required System:</strong>
                                    <span id="requiredSystem"></span>
                                </div>
                                <div class="result-item">
                                    <strong>Safety Factor:</strong>
                                    <span id="safetyFactor"></span>
                                </div>
                                <div class="result-item">
                                    <strong>Clearance Check:</strong>
                                    <span id="clearanceCheck"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="result-item">
                                    <strong>Maximum Free Fall:</strong>
                                    <span id="maxFreeFall"></span>
                                </div>
                                <div class="result-item">
                                    <strong>Arrest Force:</strong>
                                    <span id="arrestForce"></span>
                                </div>
                                <div class="result-item">
                                    <strong>Compliance Status:</strong>
                                    <span id="complianceStatus"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fall Protection Calculator JavaScript
function calculateFallProtection() {
    const workHeight = parseFloat(document.getElementById('workHeight').value);
    const surfaceType = document.getElementById('surfaceType').value;
    const slopeAngle = parseFloat(document.getElementById('slopeAngle').value) || 0;
    const systemType = document.getElementById('systemType').value;
    const fallDistance = parseFloat(document.getElementById('fallDistance').value) || 0;
    const clearanceHeight = parseFloat(document.getElementById('clearanceHeight').value) || 0;
    
    if (!workHeight || !surfaceType || !systemType) {
        showNotification('Please fill in all required fields', 'info');
        return;
    }
    
    // Calculate fall protection requirements
    let requiredSystem = '';
    let safetyFactor = 0;
    let maxFreeFall = 0;
    let arrestForce = 0;
    let clearanceCheck = '';
    let complianceStatus = '';
    
    // Determine required system based on height and conditions
    if (workHeight >= 6) {
        if (workHeight <= 25) {
            requiredSystem = 'Personal Fall Arrest System or Guardrail';
            safetyFactor = 2.0;
            maxFreeFall = 6.0;
        } else if (workHeight <= 50) {
            requiredSystem = 'Personal Fall Arrest System with rescue plan';
            safetyFactor = 2.0;
            maxFreeFall = 6.0;
        } else {
            requiredSystem = 'Personal Fall Arrest System with advanced rescue';
            safetyFactor = 2.0;
            maxFreeFall = 6.0;
        }
    } else {
        requiredSystem = 'Fall protection may not be required (check local regulations)';
        safetyFactor = 1.0;
        maxFreeFall = 0;
    }
    
    // Calculate arrest force based on system type
    switch(systemType) {
        case 'personal_fall_arrest':
            arrestForce = 1800; // lbs, OSHA limit
            break;
        case 'guardrail':
            arrestForce = 0; // No fall occurs
            break;
        case 'safety_net':
            arrestForce = 1800; // lbs, OSHA limit
            break;
        default:
            arrestForce = 1800;
    }
    
    // Calculate fall distance if not provided
    let calculatedFallDistance = fallDistance;
    if (!fallDistance || fallDistance === 0) {
        calculatedFallDistance = workHeight * Math.sin(slopeAngle * Math.PI / 180);
    }
    
    // Check clearance requirements
    const requiredClearance = calculatedFallDistance + 6; // 6 ft safety margin
    if (clearanceHeight >= requiredClearance) {
        clearanceCheck = `Adequate (${clearanceHeight} ft ≥ ${requiredClearance.toFixed(1)} ft required)`;
    } else {
        clearanceCheck = `Insufficient (${clearanceHeight} ft < ${requiredClearance.toFixed(1)} ft required)`;
    }
    
    // Determine compliance status
    if (workHeight >= 6 && systemType && clearanceHeight >= requiredClearance) {
        complianceStatus = 'Compliant with OSHA regulations';
    } else if (workHeight >= 6 && !systemType) {
        complianceStatus = 'Non-compliant - Fall protection required';
    } else if (clearanceHeight < requiredClearance) {
        complianceStatus = 'Non-compliant - Insufficient clearance';
    } else {
        complianceStatus = 'Check local regulations';
    }
    
    // Display results
    document.getElementById('requiredSystem').textContent = requiredSystem;
    document.getElementById('safetyFactor').textContent = safetyFactor.toFixed(1);
    document.getElementById('clearanceCheck').textContent = clearanceCheck;
    document.getElementById('maxFreeFall').textContent = maxFreeFall.toFixed(1) + ' ft';
    document.getElementById('arrestForce').textContent = arrestForce + ' lbs';
    document.getElementById('complianceStatus').textContent = complianceStatus;
    
    document.getElementById('results').style.display = 'block';
    
    // Save to localStorage
    saveToLocalStorage('fall_protection', {
        workHeight: workHeight,
        surfaceType: surfaceType,
        slopeAngle: slopeAngle,
        systemType: systemType,
        fallDistance: calculatedFallDistance,
        clearanceHeight: clearanceHeight,
        results: {
            requiredSystem: requiredSystem,
            safetyFactor: safetyFactor,
            clearanceCheck: clearanceCheck,
            maxFreeFall: maxFreeFall,
            arrestForce: arrestForce,
            complianceStatus: complianceStatus
        }
    });
}

function clearForm() {
    document.getElementById('workHeight').value = '';
    document.getElementById('surfaceType').value = '';
    document.getElementById('slopeAngle').value = '';
    document.getElementById('systemType').value = '';
    document.getElementById('fallDistance').value = '';
    document.getElementById('clearanceHeight').value = '';
    document.getElementById('results').style.display = 'none';
}

function saveCalculation() {
    const calculationData = {
        type: 'Fall Protection',
        timestamp: new Date().toISOString(),
        inputs: {
            workHeight: document.getElementById('workHeight').value,
            surfaceType: document.getElementById('surfaceType').value,
            slopeAngle: document.getElementById('slopeAngle').value,
            systemType: document.getElementById('systemType').value,
            fallDistance: document.getElementById('fallDistance').value,
            clearanceHeight: document.getElementById('clearanceHeight').value
        }
    };
    
    if (document.getElementById('results').style.display !== 'none') {
        calculationData.results = {
            requiredSystem: document.getElementById('requiredSystem').textContent,
            safetyFactor: document.getElementById('safetyFactor').textContent,
            clearanceCheck: document.getElementById('clearanceCheck').textContent,
            maxFreeFall: document.getElementById('maxFreeFall').textContent,
            arrestForce: document.getElementById('arrestForce').textContent,
            complianceStatus: document.getElementById('complianceStatus').textContent
        };
    }
    
    saveToLocalStorage('saved_calculations', calculationData);
    showNotification('Calculation saved successfully!', 'info');
}

function saveToLocalStorage(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
}

function loadFromLocalStorage(key) {
    try {
        const data = localStorage.getItem(key);
        if (data) {
            const parsed = JSON.parse(data);
            // Populate form fields if they exist
            Object.keys(parsed).forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    element.value = parsed[field];
                }
            });
        }
    } catch (e) {
        console.log('Error loading from localStorage:', e);
    }
}

// Load saved data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadFromLocalStorage('fall_protection');
});
</script>

<script src="../../../public/assets/js/global-notifications.js"></script>
</body>
</html>
