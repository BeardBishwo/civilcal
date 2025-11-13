<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">
            <i class="fas fa-pump me-2"></i>Pump Sizing Calculator
        </h1>
        <a href="<?php echo function_exists('app_base_url') ? app_base_url('modules/plumbing/index.php') : '../modules/plumbing/index.php'; ?>" class="btn btn-outline-light btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Plumbing
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Calculator Card -->
            <div class="card calculator-card">
                <div class="card-body p-4">
                    <form id="pumpSizingForm">
                        <!-- System Requirements Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('systemRequirements')">
                                <h3 class="section-title">System Requirements</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="systemRequirements">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Required Flow Rate (L/s)</label>
                                        <input type="number" class="form-control" id="pumpFlowRate" step="0.1" min="0.1" required>
                                        <small class="text-white-50">Peak demand flow rate</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Total Head (m)</label>
                                        <input type="number" class="form-control" id="totalHead" step="0.1" min="0.1" required>
                                        <small class="text-white-50">Static + friction + velocity head</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Suction Head/Lift (m)</label>
                                        <input type="number" class="form-control" id="suctionHead" step="0.1" value="0">
                                        <small class="text-white-50">Positive if above pump, negative if below</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Discharge Head (m)</label>
                                        <input type="number" class="form-control" id="dischargeHead" step="0.1" value="0">
                                        <small class="text-white-50">Height of discharge point above pump</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pump Configuration Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('pumpConfig')">
                                <h3 class="section-title">Pump Configuration</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="pumpConfig" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Pump Efficiency (%)</label>
                                        <input type="number" class="form-control" id="pumpEfficiency" step="1" min="1" max="100" value="75">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Motor Efficiency (%)</label>
                                        <input type="number" class="form-control" id="motorEfficiency" step="1" min="1" max="100" value="85">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Safety Factor</label>
                                        <select class="form-select" id="safetyFactor">
                                            <option value="1.1">Low (1.1)</option>
                                            <option value="1.2" selected>Normal (1.2)</option>
                                            <option value="1.3">High (1.3)</option>
                                            <option value="1.5">Maximum (1.5)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Operating Hours/Day</label>
                                        <input type="number" class="form-control" id="operatingHours" min="1" max="24" value="8">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pipe Details Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('pipeDetails')">
                                <h3 class="section-title">Pipe Details</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="pipeDetails" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Suction Pipe Diameter (mm)</label>
                                        <input type="number" class="form-control" id="suctionDiameter" step="1" min="10" value="100">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Discharge Pipe Diameter (mm)</label>
                                        <input type="number" class="form-control" id="dischargeDiameter" step="1" min="10" value="100">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Suction Pipe Length (m)</label>
                                        <input type="number" class="form-control" id="suctionLength" step="0.1" min="0" value="5">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Discharge Pipe Length (m)</label>
                                        <input type="number" class="form-control" id="dischargeLength" step="0.1" min="0" value="50">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Pipe Material</label>
                                        <select class="form-select" id="pipeMaterial">
                                            <option value="steel">Steel</option>
                                            <option value="ductile-iron" selected>Ductile Iron</option>
                                            <option value="pvc">PVC</option>
                                            <option value="hdpe">HDPE</option>
                                            <option value="copper">Copper</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fluid Temperature (°C)</label>
                                        <input type="number" class="form-control" id="fluidTemp" step="1" min="0" max="100" value="20">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-gradient" onclick="calculatePumpSize()">
                                <i class="fas fa-calculator"></i> Calculate Pump Size
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
                        <h4>Pump Efficiencies</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Pump Type</th>
                                    <th>Efficiency</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Centrifugal (single stage)</td>
                                    <td>60-75%</td>
                                </tr>
                                <tr>
                                    <td>Centrifugal (multi-stage)</td>
                                    <td>65-80%</td>
                                </tr>
                                <tr>
                                    <td>Submersible</td>
                                    <td>70-85%</td>
                                </tr>
                                <tr>
                                    <td>Positive Displacement</td>
                                    <td>85-95%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="reference-table mb-3">
                        <h4>Typical Operating Conditions</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Application</th>
                                    <th>Hours/Day</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Domestic Water Supply</td>
                                    <td>4-8</td>
                                </tr>
                                <tr>
                                    <td>Commercial Building</td>
                                    <td>8-16</td>
                                </tr>
                                <tr>
                                    <td>Industrial Process</td>
                                    <td>16-24</td>
                                </tr>
                                <tr>
                                    <td>Irrigation</td>
                                    <td>6-12</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <small>Total head includes static head (elevation difference), friction head (pipe losses), and velocity head. Safety factor accounts for pump wear and system variations.</small>
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

function calculatePumpSize() {
    const flowRate = parseFloat(document.getElementById('pumpFlowRate').value);
    const totalHead = parseFloat(document.getElementById('totalHead').value);
    const suctionHead = parseFloat(document.getElementById('suctionHead').value) || 0;
    const dischargeHead = parseFloat(document.getElementById('dischargeHead').value) || 0;
    const pumpEfficiency = parseFloat(document.getElementById('pumpEfficiency').value) || 75;
    const motorEfficiency = parseFloat(document.getElementById('motorEfficiency').value) || 85;
    const safetyFactor = parseFloat(document.getElementById('safetyFactor').value) || 1.2;
    const operatingHours = parseInt(document.getElementById('operatingHours').value) || 8;
    const suctionDiameter = parseFloat(document.getElementById('suctionDiameter').value) || 100;
    const dischargeDiameter = parseFloat(document.getElementById('dischargeDiameter').value) || 100;
    const suctionLength = parseFloat(document.getElementById('suctionLength').value) || 0;
    const dischargeLength = parseFloat(document.getElementById('dischargeLength').value) || 0;
    const pipeMaterial = document.getElementById('pipeMaterial').value;
    const fluidTemp = parseFloat(document.getElementById('fluidTemp').value) || 20;
    
    if (!flowRate || !totalHead) {
        alert('Please enter flow rate and total head');
        return;
    }
    
    // Convert flow rate to m³/s
    const flowM3s = flowRate / 1000;
    
    // Calculate friction losses (simplified Darcy-Weisbach)
    const frictionFactor = {
        'steel': 0.02,
        'ductile-iron': 0.015,
        'pvc': 0.01,
        'hdpe': 0.012,
        'copper': 0.015
    };
    
    const f = frictionFactor[pipeMaterial] || 0.015;
    const suctionVelocity = (flowM3s * 4) / (Math.PI * Math.pow(suctionDiameter / 1000, 2));
    const dischargeVelocity = (flowM3s * 4) / (Math.PI * Math.pow(dischargeDiameter / 1000, 2));
    
    const suctionFriction = f * (suctionLength / (suctionDiameter / 1000)) * Math.pow(suctionVelocity, 2) / (2 * 9.81);
    const dischargeFriction = f * (dischargeLength / (dischargeDiameter / 1000)) * Math.pow(dischargeVelocity, 2) / (2 * 9.81);
    const totalFriction = suctionFriction + dischargeFriction;
    
    // Calculate power requirements
    const powerHydraulic = flowM3s * totalHead * 1000 * 9.81; // Watts
    const powerPump = powerHydraulic / (pumpEfficiency / 100); // Watts
    const powerMotor = powerPump / (motorEfficiency / 100); // Watts
    
    // Apply safety factor
    const powerMotorWithMargin = powerMotor * safetyFactor;
    
    // Convert to HP and kW
    const powerHP = powerMotorWithMargin / 746;
    const powerKW = powerMotorWithMargin / 1000;
    
    // Calculate NPSH available (simplified)
    const npshAvailable = suctionHead + 10.3 - (fluidTemp * 0.75 / 100); // Rough estimate
    const npshRequired = 3 + (totalHead / 100); // Typical requirement
    
    // Annual energy cost estimate
    const energyRate = 0.12; // $/kWh (typical commercial rate)
    const annualCost = powerKW * operatingHours * 365 * energyRate / 1000;
    
    // Display results
    const resultsHtml = `
        <div class="result-item">
            <div class="result-label">Required Pump Power</div>
            <div class="result-value">${powerKW.toFixed(2)} kW (${powerHP.toFixed(2)} HP)</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Hydraulic Power</div>
            <div class="result-value">${(powerHydraulic / 1000).toFixed(2)} kW</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Pump Efficiency</div>
            <div class="result-value">${pumpEfficiency}% | Motor: ${motorEfficiency}%</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Total Head</div>
            <div class="result-value">${totalHead.toFixed(1)} m</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Friction Losses</div>
            <div class="result-value">${totalFriction.toFixed(2)} m</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Annual Energy Cost</div>
            <div class="result-value">$${annualCost.toLocaleString(undefined, {maximumFractionDigits: 0})}</div>
        </div>
        
        <div class="alert ${npshAvailable < npshRequired ? 'alert-warning' : 'alert-info'} mt-3">
            <i class="fas ${npshAvailable < npshRequired ? 'fa-exclamation-triangle' : 'fa-info-circle'}"></i>
            <strong>NPSH Analysis</strong>
            <ul class="mb-0 mt-2">
                <li>Available NPSH: ${npshAvailable.toFixed(2)} m</li>
                <li>Required NPSH: ${npshRequired.toFixed(2)} m</li>
                <li>${npshAvailable < npshRequired ? '⚠️ Insufficient NPSH - increase suction head or reduce temperature' : '✓ NPSH margin adequate'}</li>
            </ul>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-lightbulb"></i>
            <strong>Recommendations:</strong>
            <ul class="mb-0 mt-2">
                <li>Select pump with ${Math.ceil(powerHP * 1.1 * 10) / 10} HP rating (includes 10% margin)</li>
                <li>${powerHP > 50 ? 'Consider high-efficiency premium efficiency motor' : 'Standard efficiency motor appropriate'}</li>
                <li>${totalFriction > totalHead * 0.3 ? 'High friction losses - consider larger pipe diameter' : 'Pipe sizing is adequate'}</li>
                <li>${operatingHours > 16 ? 'Continuous duty - select heavy-duty pump' : 'Intermittent duty - standard pump acceptable'}</li>
                <li>Install VFD for ${operatingHours > 12 ? 'energy savings and soft starting' : 'flow control and energy savings'}</li>
            </ul>
        </div>
    `;
    
    document.getElementById('results').innerHTML = resultsHtml;
    document.getElementById('resultsCard').style.display = 'block';
    
    // Save to recent calculations
    saveRecentCalculation('Pump Sizing', {
        flowRate: flowRate,
        totalHead: totalHead,
        powerKW: powerKW.toFixed(2),
        powerHP: powerHP.toFixed(2),
        efficiency: pumpEfficiency
    });
}

// Initialize - expand first section
document.addEventListener('DOMContentLoaded', function() {
    toggleSection('systemRequirements');
});

function saveRecentCalculation(name, data) {
    let history = JSON.parse(localStorage.getItem('pumpSizingHistory') || '[]');
    history.unshift({
        name: name,
        date: new Date().toLocaleString(),
        data: data
    });
    history = history.slice(0, 10); // Keep only 10 recent
    localStorage.setItem('pumpSizingHistory', JSON.stringify(history));
}
</script>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

