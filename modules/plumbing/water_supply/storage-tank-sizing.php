<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">
            <i class="fas fa-database me-2"></i>Storage Tank Sizing Calculator
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
                    <form id="tankSizingForm">
                        <!-- Demand Details Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('demandDetails')">
                                <h3 class="section-title">Demand Requirements</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="demandDetails">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Daily Water Demand (L/day)</label>
                                        <input type="number" class="form-control" id="dailyDemand" step="10" min="0" required>
                                        <small class="text-white-50">Total daily consumption</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Storage Duration (hours)</label>
                                        <input type="number" class="form-control" id="storageHours" step="1" min="1" value="8" required>
                                        <small class="text-white-50">Typical: 4-24 hours</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Safety Factor (%)</label>
                                        <input type="number" class="form-control" id="safetyFactor" step="5" min="0" value="20" required>
                                        <small class="text-white-50">Accounts for variations and emergencies</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Peak Factor</label>
                                        <select class="form-select" id="peakFactor">
                                            <option value="1.0">Low (1.0)</option>
                                            <option value="1.2" selected>Normal (1.2)</option>
                                            <option value="1.5">High (1.5)</option>
                                            <option value="2.0">Peak (2.0)</option>
                                        </select>
                                        <small class="text-white-50">Peak demand multiplier</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tank Configuration Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('tankConfig')">
                                <h3 class="section-title">Tank Configuration</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="tankConfig" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tank Type</label>
                                        <select class="form-select" id="tankType">
                                            <option value="plastic" selected>Plastic/Polyethylene</option>
                                            <option value="fiberglass">Fiberglass</option>
                                            <option value="steel">Steel</option>
                                            <option value="concrete">Concrete</option>
                                            <option value="stainless">Stainless Steel</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Number of Tanks</label>
                                        <input type="number" class="form-control" id="tankCount" min="1" value="1">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Emergency Reserve (L)</label>
                                        <input type="number" class="form-control" id="emergencyReserve" step="100" min="0" value="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Dead Storage (%)</label>
                                        <input type="number" class="form-control" id="deadStorage" step="1" min="0" max="20" value="10">
                                        <small class="text-white-50">Unusable volume (sediment, etc.)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Operating Conditions Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('operatingConditions')">
                                <h3 class="section-title">Operating Conditions</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="operatingConditions" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Operating Hours per Day</label>
                                        <input type="number" class="form-control" id="operatingHours" min="1" max="24" value="24">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Min/Max Temperature (°C)</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="number" class="form-control" id="minTemp" placeholder="Min" value="5">
                                            </div>
                                            <div class="col-6">
                                                <input type="number" class="form-control" id="maxTemp" placeholder="Max" value="25">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-gradient" onclick="calculateTankSize()">
                                <i class="fas fa-calculator"></i> Calculate Tank Size
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
                        <h4>Typical Storage Durations</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Application</th>
                                    <th>Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Residential</td>
                                    <td>4-12</td>
                                </tr>
                                <tr>
                                    <td>Commercial</td>
                                    <td>8-24</td>
                                </tr>
                                <tr>
                                    <td>Industrial</td>
                                    <td>12-48</td>
                                </tr>
                                <tr>
                                    <td>Fire Protection</td>
                                    <td>60-120</td>
                                </tr>
                                <tr>
                                    <td>Emergency</td>
                                    <td>72+</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="reference-table mb-3">
                        <h4>Safety Factors</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>System Type</th>
                                    <th>Factor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Direct Supply</td>
                                    <td>10-15%</td>
                                </tr>
                                <tr>
                                    <td>Well Water</td>
                                    <td>15-25%</td>
                                </tr>
                                <tr>
                                    <td>Variable Source</td>
                                    <td>20-30%</td>
                                </tr>
                                <tr>
                                    <td>Critical System</td>
                                    <td>25-50%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <small>Storage tanks provide emergency reserve, pressure equalization, and demand peak shaving. Size for worst-case scenarios.</small>
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
    background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
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
    border-color: #ffffff;
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
    border-left: 4px solid #ffffff;
}

.result-label {
    color: #fff;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.result-value {
    color: #ffffff;
    font-size: 1.5rem;
    font-weight: 600;
}

.btn-gradient {
    background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
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

function calculateTankSize() {
    const dailyDemand = parseFloat(document.getElementById('dailyDemand').value);
    const storageHours = parseFloat(document.getElementById('storageHours').value);
    const safetyFactor = parseFloat(document.getElementById('safetyFactor').value) || 0;
    const peakFactor = parseFloat(document.getElementById('peakFactor').value) || 1.0;
    const tankCount = parseInt(document.getElementById('tankCount').value) || 1;
    const emergencyReserve = parseFloat(document.getElementById('emergencyReserve').value) || 0;
    const deadStorage = parseFloat(document.getElementById('deadStorage').value) || 0;
    const operatingHours = parseInt(document.getElementById('operatingHours').value) || 24;
    const tankType = document.getElementById('tankType').value;
    
    if (!dailyDemand || dailyDemand <= 0) {
        showNotification('Please enter a valid daily demand value', 'info');
        return;
    }
    
    // Calculate hourly demand
    const hourlyDemand = dailyDemand / 24;
    
    // Calculate storage volume
    const baseStorage = hourlyDemand * storageHours;
    
    // Apply peak factor
    const peakStorage = baseStorage * peakFactor;
    
    // Apply safety factor
    const safetyBuffer = peakStorage * (safetyFactor / 100);
    
    // Calculate total required volume
    let totalVolume = peakStorage + safetyBuffer + emergencyReserve;
    
    // Account for dead storage
    const usableVolume = totalVolume / (1 + deadStorage / 100);
    
    // Calculate per tank
    const volumePerTank = Math.ceil(usableVolume / tankCount);
    
    // Standard tank sizes (liters)
    const standardSizes = [500, 750, 1000, 1500, 2000, 2500, 3000, 5000, 7500, 10000, 15000, 20000, 25000, 30000, 50000];
    
    // Find recommended tank size
    let recommendedTankSize = standardSizes.find(size => size >= volumePerTank) || Math.ceil(volumePerTank / 1000) * 1000;
    let totalRecommendedSize = recommendedTankSize * tankCount;
    
    // Calculate dimensions (rough estimate for cylindrical tanks)
    const diameter = Math.sqrt((recommendedTankSize * 4) / (Math.PI * 3.5)); // Assuming height = 3.5 * diameter
    const height = diameter * 3.5;
    
    // Calculate costs (rough estimates per liter)
    const costPerLiter = {
        'plastic': 2.0,
        'fiberglass': 3.5,
        'steel': 4.0,
        'concrete': 3.0,
        'stainless': 8.0
    };
    
    const estimatedCost = recommendedTankSize * costPerLiter[tankType];
    
    // Display results
    const resultsHtml = `
        <div class="result-item">
            <div class="result-label">Required Storage Volume</div>
            <div class="result-value">${usableVolume.toFixed(0)} L (${(usableVolume / 1000).toFixed(2)} m³)</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Per Tank Capacity</div>
            <div class="result-value">${volumePerTank.toFixed(0)} L (${(volumePerTank / 1000).toFixed(2)} m³)</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Recommended Tank Size</div>
            <div class="result-value">${recommendedTankSize.toLocaleString()} L × ${tankCount} = ${totalRecommendedSize.toLocaleString()} L</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Storage Duration</div>
            <div class="result-value">${storageHours} hours @ ${hourlyDemand.toFixed(1)} L/hr</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Estimated Tank Dimensions</div>
            <div class="result-value">${diameter.toFixed(1)}m × ${height.toFixed(1)}m (diameter × height)</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Estimated Cost</div>
            <div class="result-value">$${estimatedCost.toLocaleString(undefined, {maximumFractionDigits: 0})}</div>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i>
            <strong>Calculation Breakdown:</strong>
            <ul class="mb-0 mt-2">
                <li>Base storage: ${baseStorage.toFixed(0)} L (${storageHours}h @ ${hourlyDemand.toFixed(1)} L/hr)</li>
                <li>Peak factor: ${peakFactor}x → ${peakStorage.toFixed(0)} L</li>
                <li>Safety buffer: ${safetyFactor}% → ${safetyBuffer.toFixed(0)} L</li>
                <li>Emergency reserve: ${emergencyReserve.toFixed(0)} L</li>
                <li>Dead storage: ${deadStorage}% → ${(totalVolume - usableVolume).toFixed(0)} L unusable</li>
            </ul>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-lightbulb"></i>
            <strong>Recommendations:</strong>
            <ul class="mb-0 mt-2">
                <li>${tankCount > 1 ? `${tankCount} tanks provides redundancy and flexibility` : 'Consider parallel tanks for redundancy'}</li>
                <li>${emergencyReserve > 0 ? 'Emergency reserve provides fire protection capacity' : 'Add 30-60 min emergency reserve for fire protection'}</li>
                <li>${tankType === 'plastic' ? 'Lightweight and cost-effective for small to medium sizes' : ` ${tankType} tanks suitable for this application`}</li>
                <li>Include overflow capacity for ${(totalRecommendedSize * 1.1).toFixed(0)} L</li>
                <li>${operatingHours < 24 ? 'Limited operating hours - consider pump controls' : '24/7 operation - monitoring required'}</li>
            </ul>
        </div>
    `;
    
    document.getElementById('results').innerHTML = resultsHtml;
    document.getElementById('resultsCard').style.display = 'block';
    
    // Save to recent calculations
    saveRecentCalculation('Storage Tank Sizing', {
        dailyDemand: dailyDemand,
        storageHours: storageHours,
        tankCount: tankCount,
        requiredVolume: usableVolume.toFixed(0),
        recommendedTankSize: recommendedTankSize,
        totalSize: totalRecommendedSize,
        estimatedCost: Math.round(estimatedCost)
    });
}

// Initialize - expand first section
document.addEventListener('DOMContentLoaded', function() {
    toggleSection('demandDetails');
});

function saveRecentCalculation(name, data) {
    let history = JSON.parse(localStorage.getItem('tankSizingHistory') || '[]');
    history.unshift({
        name: name,
        date: new Date().toLocaleString(),
        data: data
    });
    history = history.slice(0, 10); // Keep only 10 recent
    localStorage.setItem('tankSizingHistory', JSON.stringify(history));
}
</script>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

