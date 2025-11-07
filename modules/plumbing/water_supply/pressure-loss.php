<?php
session_start();
require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">
            <i class="fas fa-tachometer-alt me-2"></i>Pressure Loss Calculator
        </h1>
        <a href="/aec-calculator/modules/plumbing/index.php" class="btn btn-outline-light btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Plumbing
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Calculator Card -->
            <div class="card calculator-card">
                <div class="card-body p-4">
                    <form id="pressureLossForm">
                        <!-- Pipe Details Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('pipeDetails')">
                                <h3 class="section-title">Pipe Details</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="pipeDetails">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Pipe Material</label>
                                        <select class="form-select" id="pipeMaterial" required>
                                            <option value="">Select material...</option>
                                            <option value="steel">Steel (Sch 40)</option>
                                            <option value="copper">Copper (Type L)</option>
                                            <option value="pvc">PVC (Sch 40)</option>
                                            <option value="cpvc">CPVC (Sch 40)</option>
                                            <option value="pex">PEX</option>
                                            <option value="ductile-iron">Ductile Iron</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nominal Diameter</label>
                                        <select class="form-select" id="pipeDiameter" required>
                                            <option value="">Select diameter...</option>
                                            <option value="15">15 mm (1/2")</option>
                                            <option value="20">20 mm (3/4")</option>
                                            <option value="25">25 mm (1")</option>
                                            <option value="32">32 mm (1-1/4")</option>
                                            <option value="40">40 mm (1-1/2")</option>
                                            <option value="50">50 mm (2")</option>
                                            <option value="65">65 mm (2-1/2")</option>
                                            <option value="80">80 mm (3")</option>
                                            <option value="100">100 mm (4")</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Pipe Length (m)</label>
                                        <input type="number" class="form-control" id="pipeLength" step="0.1" min="0.1" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Flow Rate (L/min)</label>
                                        <input type="number" class="form-control" id="flowRate" step="1" min="0.1" required>
                                        <small class="text-white-50">Or use velocity below</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fittings Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('fittings')">
                                <h3 class="section-title">Fittings & Valves</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="fittings" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <small class="text-white-50">Enter number of each fitting type (typical K-values)</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Elbows 90°</label>
                                        <input type="number" class="form-control" id="elbows90" min="0" value="0">
                                        <small class="text-white-50">K = 0.9</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Elbows 45°</label>
                                        <input type="number" class="form-control" id="elbows45" min="0" value="0">
                                        <small class="text-white-50">K = 0.45</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tees (through run)</label>
                                        <input type="number" class="form-control" id="tees" min="0" value="0">
                                        <small class="text-white-50">K = 0.6</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Gate Valves</label>
                                        <input type="number" class="form-control" id="gateValves" min="0" value="0">
                                        <small class="text-white-50">K = 0.15</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Globe Valves</label>
                                        <input type="number" class="form-control" id="globeValves" min="0" value="0">
                                        <small class="text-white-50">K = 6.0</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Check Valves</label>
                                        <input type="number" class="form-control" id="checkValves" min="0" value="0">
                                        <small class="text-white-50">K = 2.5</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Entrances</label>
                                        <input type="number" class="form-control" id="entrances" min="0" value="0">
                                        <small class="text-white-50">K = 0.5</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Exits</label>
                                        <input type="number" class="form-control" id="exits" min="0" value="0">
                                        <small class="text-white-50">K = 1.0</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Custom K-value</label>
                                        <input type="number" class="form-control" id="customFitting" min="0" value="0" step="0.1">
                                        <small class="text-white-50">For other fittings</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Conditions Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('systemConditions')">
                                <h3 class="section-title">System Conditions</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="systemConditions" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Water Temperature (°C)</label>
                                        <input type="number" class="form-control" id="waterTemp" step="1" min="0" max="100" value="20">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Elevation Change (m)</label>
                                        <input type="number" class="form-control" id="elevationChange" step="0.1" value="0">
                                        <small class="text-white-50">Positive = uphill, Negative = downhill</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Starting Pressure (bar)</label>
                                        <input type="number" class="form-control" id="startingPressure" step="0.1" min="0" value="5">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Minimum Required Pressure (bar)</label>
                                        <input type="number" class="form-control" id="minRequiredPressure" step="0.1" min="0" value="2">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-gradient" onclick="calculatePressureLoss()">
                                <i class="fas fa-calculator"></i> Calculate Pressure Loss
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Card -->
            <div class="card calculator-card mt-4" id="resultsCard" style="display: none;">
                <div class="card-body p-4">
                    <h3 class="text-gradient mb-3">Calculation Results</h3>
                    <div id="results"></div>
                </div>
            </div>
        </div>

        <!-- Reference Panel -->
        <div class="col-lg-4">
            <div class="card calculator-card">
                <div class="card-body p-4">
                    <h3 class="text-gradient mb-3">Reference Tables</h3>
                    
                    <div class="reference-table mb-3">
                        <h4>Pipe Internal Diameters</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nominal</th>
                                    <th>Steel</th>
                                    <th>Copper</th>
                                    <th>PVC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1/2"</td>
                                    <td>15.8</td>
                                    <td>15.8</td>
                                    <td>15.4</td>
                                </tr>
                                <tr>
                                    <td>3/4"</td>
                                    <td>20.9</td>
                                    <td>20.9</td>
                                    <td>20.3</td>
                                </tr>
                                <tr>
                                    <td>1"</td>
                                    <td>26.6</td>
                                    <td>26.6</td>
                                    <td>26.1</td>
                                </tr>
                                <tr>
                                    <td>2"</td>
                                    <td>52.5</td>
                                    <td>52.5</td>
                                    <td>51.8</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="reference-table mb-3">
                        <h4>Fitting K-values</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fitting</th>
                                    <th>K-value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>90° Elbow</td>
                                    <td>0.9</td>
                                </tr>
                                <tr>
                                    <td>45° Elbow</td>
                                    <td>0.45</td>
                                </tr>
                                <tr>
                                    <td>Tee</td>
                                    <td>0.6</td>
                                </tr>
                                <tr>
                                    <td>Gate Valve</td>
                                    <td>0.15</td>
                                </tr>
                                <tr>
                                    <td>Globe Valve</td>
                                    <td>6.0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="reference-table">
                        <h4>Hazen-Williams C-factors</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>C-factor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Ductile Iron</td>
                                    <td>130</td>
                                </tr>
                                <tr>
                                    <td>Copper</td>
                                    <td>140</td>
                                </tr>
                                <tr>
                                    <td>PVC/PEX</td>
                                    <td>150</td>
                                </tr>
                                <tr>
                                    <td>Steel (new)</td>
                                    <td>130</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <small>Pressure loss includes friction losses (Darcy-Weisbach), fitting losses (K-method), and elevation changes. Total loss = friction + fittings + elevation.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.calculator-card {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 600;
}

.section-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.section-header {
    padding: 1rem 1.25rem;
    background: rgba(255, 255, 255, 0.05);
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s;
}

.section-header:hover {
    background: rgba(255, 255, 255, 0.08);
}

.section-title {
    margin: 0;
    font-size: 1.1rem;
    color: #fff;
}

.section-icon {
    color: #fff;
    transition: transform 0.3s;
}

.section-content {
    padding: 1.25rem;
}

.reference-table {
    background: rgba(0, 0, 0, 0.2);
    padding: 1rem;
    border-radius: 8px;
}

.reference-table h4 {
    font-size: 0.95rem;
    color: #fff;
    margin-bottom: 0.75rem;
}

.form-label {
    color: #fff;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    border-radius: 8px;
    padding: 0.6rem 0.75rem;
}

.form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #667eea;
    color: #fff;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.4);
}

.result-item {
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    border-left: 4px solid #667eea;
}

.result-label {
    color: #fff;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.result-value {
    color: #667eea;
    font-size: 1.5rem;
    font-weight: 600;
}

.result-warning {
    color: #ffc107;
    font-weight: 600;
}

.result-critical {
    color: #dc3545;
    font-weight: 600;
}

.btn-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

.alert-info {
    background: rgba(23, 162, 184, 0.15);
    border: 1px solid rgba(23, 162, 184, 0.3);
    color: #fff;
}

.alert-warning {
    background: rgba(255, 193, 7, 0.15);
    border: 1px solid rgba(255, 193, 7, 0.3);
    color: #fff;
}

.alert-danger {
    background: rgba(220, 53, 69, 0.15);
    border: 1px solid rgba(220, 53, 69, 0.3);
    color: #fff;
}

.table {
    color: #fff;
}

.table th {
    border-color: rgba(255, 255, 255, 0.1);
    font-weight: 600;
}

.table td {
    border-color: rgba(255, 255, 255, 0.05);
}
</style>

<script>
// Pipe inner diameters (mm)
const pipeDiameters = {
    '15': { steel: 15.8, copper: 15.8, pvc: 15.4, cpvc: 13.4, pex: 14.1, 'ductile-iron': 15.3 },
    '20': { steel: 20.9, copper: 20.9, pvc: 20.3, cpvc: 18.4, pex: 19.1, 'ductile-iron': 20.1 },
    '25': { steel: 26.6, copper: 26.6, pvc: 26.1, cpvc: 23.8, pex: 25.0, 'ductile-iron': 25.6 },
    '32': { steel: 35.0, copper: 34.8, pvc: 34.5, cpvc: 31.3, pex: 32.2, 'ductile-iron': 33.6 },
    '40': { steel: 40.9, copper: 40.9, pvc: 40.3, cpvc: 36.9, pex: 38.1, 'ductile-iron': 39.3 },
    '50': { steel: 52.5, copper: 52.5, pvc: 51.8, cpvc: 47.7, pex: 49.4, 'ductile-iron': 50.5 },
    '65': { steel: 62.7, copper: 62.6, pvc: 62.3, cpvc: 57.4, pex: 59.3, 'ductile-iron': 60.3 },
    '80': { steel: 78.0, copper: 77.9, pvc: 77.1, cpvc: 71.9, pex: 74.2, 'ductile-iron': 75.0 },
    '100': { steel: 102.3, copper: 102.3, pvc: 101.5, cpvc: 95.0, pex: 97.8, 'ductile-iron': 98.6 }
};

// Hazen-Williams C-factors
const cFactors = {
    'steel': 130,
    'copper': 140,
    'pvc': 150,
    'cpvc': 150,
    'pex': 150,
    'ductile-iron': 130
};

// Fitting K-values
const kValues = {
    elbows90: 0.9,
    elbows45: 0.45,
    tees: 0.6,
    gateValves: 0.15,
    globeValves: 6.0,
    checkValves: 2.5,
    entrances: 0.5,
    exits: 1.0
};

// Roughness (mm)
const roughness = {
    'steel': 0.045,
    'copper': 0.0015,
    'pvc': 0.007,
    'cpvc': 0.007,
    'pex': 0.007,
    'ductile-iron': 0.12
};

function toggleSection(sectionId) {
    const content = document.getElementById(sectionId);
    const icon = content.previousElementSibling.querySelector('.section-icon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(0deg)';
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(-90deg)';
    }
}

function calculatePressureLoss() {
    const pipeMaterial = document.getElementById('pipeMaterial').value;
    const pipeDiameter = document.getElementById('pipeDiameter').value;
    const pipeLength = parseFloat(document.getElementById('pipeLength').value) || 0;
    const flowRate = parseFloat(document.getElementById('flowRate').value) || 0;
    const waterTemp = parseFloat(document.getElementById('waterTemp').value) || 20;
    const elevationChange = parseFloat(document.getElementById('elevationChange').value) || 0;
    const startingPressure = parseFloat(document.getElementById('startingPressure').value) || 0;
    const minRequiredPressure = parseFloat(document.getElementById('minRequiredPressure').value) || 0;
    
    if (!pipeMaterial || !pipeDiameter || pipeLength <= 0 || flowRate <= 0) {
        alert('Please enter pipe material, diameter, length, and flow rate');
        return;
    }
    
    // Get pipe internal diameter
    const internalDiameter = pipeDiameters[pipeDiameter][pipeMaterial] / 1000; // Convert to meters
    
    // Convert flow rate to m³/s
    const flowM3s = flowRate / (1000 * 60);
    
    // Calculate velocity
    const velocity = (4 * flowM3s) / (Math.PI * Math.pow(internalDiameter, 2));
    
    // Get C-factor
    const cFactor = cFactors[pipeMaterial];
    
    // Calculate friction factor (simplified Darcy-Weisbach)
    const pipeRoughness = roughness[pipeMaterial] / 1000; // Convert mm to m
    const reynolds = (velocity * internalDiameter) / (1.004e-6); // At 20°C
    
    // Simplified friction factor calculation
    let f;
    if (reynolds < 2300) {
        // Laminar flow
        f = 64 / reynolds;
    } else {
        // Turbulent flow - Colebrook approximation
        const relativeRoughnesses = pipeRoughness / internalDiameter;
        f = Math.pow(-1.8 * Math.log10(Math.pow(relativeRoughnesses / 3.7, 1.11) + 6.9 / reynolds), -2);
    }
    
    // Calculate friction loss (Darcy-Weisbach equation)
    const frictionLoss = f * (pipeLength / internalDiameter) * Math.pow(velocity, 2) / (2 * 9.81);
    
    // Calculate fitting losses
    let totalK = 0;
    Object.keys(kValues).forEach(fitting => {
        const count = parseFloat(document.getElementById(fitting).value) || 0;
        totalK += count * kValues[fitting];
    });
    
    // Add custom fitting
    const customK = parseFloat(document.getElementById('customFitting').value) || 0;
    totalK += customK;
    
    const fittingLoss = totalK * Math.pow(velocity, 2) / (2 * 9.81);
    
    // Calculate elevation head
    const elevationLoss = elevationChange * -1; // Convert to head loss
    
    // Total head loss
    const totalHeadLoss = frictionLoss + fittingLoss + elevationLoss;
    
    // Convert to pressure (1 m head = 0.0981 bar)
    const totalPressureLoss = totalHeadLoss * 0.0981;
    
    // Calculate ending pressure
    const endingPressure = startingPressure - totalPressureLoss;
    
    // Calculate pressure margin
    const pressureMargin = endingPressure - minRequiredPressure;
    
    // Display results
    const alertClass = pressureMargin < 0 ? 'alert-danger' : (pressureMargin < 0.5 ? 'alert-warning' : 'alert-info');
    const alertIcon = pressureMargin < 0 ? 'fa-exclamation-triangle' : (pressureMargin < 0.5 ? 'fa-exclamation-circle' : 'fa-check-circle');
    
    const resultsHtml = `
        <div class="result-item">
            <div class="result-label">Friction Loss</div>
            <div class="result-value">${frictionLoss.toFixed(2)} m (${(frictionLoss * 0.0981).toFixed(2)} bar)</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Fitting Losses</div>
            <div class="result-value">${fittingLoss.toFixed(2)} m (${(fittingLoss * 0.0981).toFixed(2)} bar)</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Elevation Change</div>
            <div class="result-value">${Math.abs(elevationLoss).toFixed(2)} m (${Math.abs(elevationLoss * 0.0981).toFixed(2)} bar)</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Total Pressure Loss</div>
            <div class="result-value">${totalPressureLoss.toFixed(2)} bar</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Ending Pressure</div>
            <div class="result-value">${endingPressure.toFixed(2)} bar</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Flow Velocity</div>
            <div class="result-value">${velocity.toFixed(2)} m/s</div>
        </div>
        
        <div class="alert ${alertClass} mt-3">
            <i class="fas ${alertIcon}"></i>
            <strong>Pressure Analysis</strong>
            <ul class="mb-0 mt-2">
                <li>Starting pressure: ${startingPressure.toFixed(2)} bar</li>
                <li>Ending pressure: ${endingPressure.toFixed(2)} bar</li>
                <li>Required pressure: ${minRequiredPressure.toFixed(2)} bar</li>
                <li>${pressureMargin < 0 ? '⚠️ INSUFFICIENT PRESSURE' : pressureMargin < 0.5 ? '⚠️ Low pressure margin' : '✓ Adequate pressure available'}</li>
                <li>Pressure margin: ${pressureMargin.toFixed(2)} bar</li>
            </ul>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-lightbulb"></i>
            <strong>Recommendations:</strong>
            <ul class="mb-0 mt-2">
                ${velocity > 3 ? `<li>⚠️ High velocity (${velocity.toFixed(1)} m/s) - consider larger pipe diameter</li>` : `<li>✓ Velocity is acceptable (${velocity.toFixed(1)} m/s)</li>`}
                ${frictionLoss > (totalHeadLoss * 0.6) ? '<li>High friction loss - consider larger pipe or smoother material</li>' : ''}
                ${Math.abs(elevationChange) > 10 ? '<li>Significant elevation change - consider booster pump</li>' : ''}
                <li>${totalK > 10 ? 'High fitting losses - minimize fittings where possible' : 'Fitting losses are acceptable'}</li>
                ${pressureMargin < 0 ? `<li>Increase starting pressure to ${(minRequiredPressure + totalPressureLoss + 0.5).toFixed(1)} bar minimum</li>` : ''}
            </ul>
        </div>
    `;
    
    document.getElementById('results').innerHTML = resultsHtml;
    document.getElementById('resultsCard').style.display = 'block';
    
    // Save to recent calculations
    saveRecentCalculation('Pressure Loss', {
        pipeMaterial: pipeMaterial,
        diameter: pipeDiameter,
        flowRate: flowRate,
        totalLoss: totalPressureLoss.toFixed(2),
        endingPressure: endingPressure.toFixed(2),
        velocity: velocity.toFixed(2)
    });
}

// Initialize - expand first section
document.addEventListener('DOMContentLoaded', function() {
    toggleSection('pipeDetails');
});

function saveRecentCalculation(name, data) {
    let history = JSON.parse(localStorage.getItem('pressureLossHistory') || '[]');
    history.unshift({
        name: name,
        date: new Date().toLocaleString(),
        data: data
    });
    history = history.slice(0, 10); // Keep only 10 recent
    localStorage.setItem('pressureLossHistory', JSON.stringify(history));
}
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
