<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">
            <i class="fas fa-burst me-2"></i>Water Hammer Calculator
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
                    <form id="waterHammerForm">
                        <!-- System Details Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('systemDetails')">
                                <h3 class="section-title">System Details</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="systemDetails">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Flow Velocity (m/s)</label>
                                        <input type="number" class="form-control" id="hammerVelocity" step="0.1" min="0.1" required>
                                        <small class="text-white-50">Typical range: 0.5 - 3.0 m/s</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Pipe Length (m)</label>
                                        <input type="number" class="form-control" id="hammerLength" step="0.1" min="0.1" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Valve Closure Time (s)</label>
                                        <input type="number" class="form-control" id="closureTime" step="0.1" min="0.1" value="0.5" required>
                                        <small class="text-white-50">Time for valve to fully close</small>
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
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Parameters Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('advancedParams')">
                                <h3 class="section-title">Advanced Parameters</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="advancedParams" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Pipe Diameter (mm)</label>
                                        <input type="number" class="form-control" id="pipeDiameter" step="1" min="10" value="100">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Operating Pressure (bar)</label>
                                        <input type="number" class="form-control" id="operatingPressure" step="0.1" min="0" value="5">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Water Temperature (°C)</label>
                                        <input type="number" class="form-control" id="waterTemp" step="1" min="0" max="100" value="20">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Allowable Surge Pressure (bar)</label>
                                        <input type="number" class="form-control" id="allowableSurge" step="0.1" min="1" value="10">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-gradient" onclick="calculateWaterHammer()">
                                <i class="fas fa-calculator"></i> Calculate Water Hammer
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
                        <h4>Wave Speed by Material (m/s)</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Wave Speed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Steel</td>
                                    <td>1500</td>
                                </tr>
                                <tr>
                                    <td>Ductile Iron</td>
                                    <td>1200</td>
                                </tr>
                                <tr>
                                    <td>PVC</td>
                                    <td>240</td>
                                </tr>
                                <tr>
                                    <td>HDPE</td>
                                    <td>200</td>
                                </tr>
                                <tr>
                                    <td>Copper</td>
                                    <td>1200</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="reference-table mb-3">
                        <h4>Typical Flow Velocities</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Application</th>
                                    <th>Velocity (m/s)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Suction</td>
                                    <td>0.5 - 1.5</td>
                                </tr>
                                <tr>
                                    <td>Discharge</td>
                                    <td>1.0 - 2.5</td>
                                </tr>
                                <tr>
                                    <td>Distribution</td>
                                    <td>0.9 - 2.0</td>
                                </tr>
                                <tr>
                                    <td>Service</td>
                                    <td>1.0 - 3.0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <small>Water hammer occurs when flowing liquid is suddenly stopped, causing pressure waves that can damage pipes and fittings.</small>
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
// Wave speed by pipe material (m/s)
const waveSpeeds = {
    steel: 1500,
    'ductile-iron': 1200,
    pvc: 240,
    hdpe: 200,
    copper: 1200
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

function calculateWaterHammer() {
    const velocity = parseFloat(document.getElementById('hammerVelocity').value);
    const length = parseFloat(document.getElementById('hammerLength').value);
    const closureTime = parseFloat(document.getElementById('closureTime').value);
    const pipeMaterial = document.getElementById('pipeMaterial').value;
    const pipeDiameter = parseFloat(document.getElementById('pipeDiameter').value) || 100;
    const operatingPressure = parseFloat(document.getElementById('operatingPressure').value) || 5;
    const waterTemp = parseFloat(document.getElementById('waterTemp').value) || 20;
    const allowableSurge = parseFloat(document.getElementById('allowableSurge').value) || 10;
    
    if (!velocity || !length || !closureTime) {
        showNotification('Please enter velocity, pipe length, and valve closure time', 'info');
        return;
    }
    
    // Get wave speed for material
    const waveSpeed = waveSpeeds[pipeMaterial] || 1200;
    
    // Calculate critical closure time (Joukowsky equation)
    const criticalTime = (2 * length) / waveSpeed;
    
    // Calculate pressure surge
    let pressureSurge;
    let surgeType;
    let alertClass;
    
    if (closureTime <= criticalTime) {
        // Instantaneous closure - full Joukowsky equation
        pressureSurge = (1000 * velocity * waveSpeed) / 100000; // Convert to bar
        surgeType = 'Severe Water Hammer';
        alertClass = 'alert-danger';
    } else {
        // Gradual closure
        const ratio = criticalTime / closureTime;
        pressureSurge = (1000 * velocity * waveSpeed * ratio) / 100000;
        surgeType = ratio > 0.5 ? 'Moderate Water Hammer' : 'Low Water Hammer';
        alertClass = ratio > 0.5 ? 'alert-warning' : 'alert-info';
    }
    
    // Calculate total pressure
    const totalPressure = operatingPressure + pressureSurge;
    
    // Check against allowable surge
    const surgeRatio = pressureSurge / allowableSurge;
    
    // Display results
    const resultsHtml = `
        <div class="result-item">
            <div class="result-label">Pressure Surge</div>
            <div class="result-value">${pressureSurge.toFixed(2)} bar</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Critical Closure Time</div>
            <div class="result-value">${criticalTime.toFixed(3)} s</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Actual Closure Time</div>
            <div class="result-value">${closureTime} s</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Wave Speed (Material: ${pipeMaterial})</div>
            <div class="result-value">${waveSpeed} m/s</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Total Pressure (Operating + Surge)</div>
            <div class="result-value">${totalPressure.toFixed(2)} bar</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Surge Ratio (vs. Allowable)</div>
            <div class="result-value ${surgeRatio > 1 ? 'result-critical' : surgeRatio > 0.7 ? 'result-warning' : ''}">
                ${surgeRatio.toFixed(2)}x
            </div>
        </div>
        
        <div class="alert ${alertClass} mt-3">
            <i class="fas ${surgeType === 'Severe Water Hammer' ? 'fa-exclamation-triangle' : 'fa-info-circle'}"></i>
            <strong>${surgeType}</strong>
            <ul class="mb-0 mt-2">
                ${surgeRatio > 1 ? '<li class="text-warning">⚠️ Surge exceeds allowable limit - mitigation required</li>' : ''}
                ${surgeRatio > 0.7 && surgeRatio <= 1 ? '<li class="text-warning">⚠️ Surge approaching allowable limit</li>' : ''}
                ${surgeRatio <= 0.7 ? '<li class="text-success">✓ Surge within acceptable limits</li>' : ''}
                <li>Critical time: ${criticalTime.toFixed(3)}s | Actual time: ${closureTime}s</li>
                <li>Flow velocity: ${velocity} m/s | Pipe length: ${length}m</li>
            </ul>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-lightbulb"></i>
            <strong>Mitigation Recommendations:</strong>
            <ul class="mb-0 mt-2">
                ${closureTime < criticalTime ? '<li>Increase valve closure time above ' + criticalTime.toFixed(2) + 's</li>' : ''}
                ${pressureSurge > allowableSurge ? '<li>Install surge arrestors or water hammer arrestors</li>' : ''}
                ${pressureSurge > allowableSurge ? '<li>Consider slower-closing valve or soft-closing mechanism</li>' : ''}
                <li>${velocity > 2 ? 'Reduce flow velocity through pipe sizing' : 'Flow velocity is acceptable'}</li>
                <li>${pipeMaterial === 'hdpe' || pipeMaterial === 'pvc' ? 'Plastic pipes have lower wave speed - naturally better resistance' : 'Consider flexible connections or expansion joints'}</li>
            </ul>
        </div>
    `;
    
    document.getElementById('results').innerHTML = resultsHtml;
    document.getElementById('resultsCard').style.display = 'block';
    
    // Save to recent calculations
    saveRecentCalculation('Water Hammer', {
        velocity: velocity,
        length: length,
        closureTime: closureTime,
        pipeMaterial: pipeMaterial,
        pressureSurge: pressureSurge.toFixed(2),
        totalPressure: totalPressure.toFixed(2),
        surgeType: surgeType
    });
}

// Initialize - expand first section
document.addEventListener('DOMContentLoaded', function() {
    toggleSection('systemDetails');
});

function saveRecentCalculation(name, data) {
    let history = JSON.parse(localStorage.getItem('waterHammerHistory') || '[]');
    history.unshift({
        name: name,
        date: new Date().toLocaleString(),
        data: data
    });
    history = history.slice(0, 10); // Keep only 10 recent
    localStorage.setItem('waterHammerHistory', JSON.stringify(history));
}
</script>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

<style>
.calculator-form { background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 8px; }
.result-card { background: var(--success-color); color: white; padding: 1rem; border-radius: 8px; }
</style>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>
