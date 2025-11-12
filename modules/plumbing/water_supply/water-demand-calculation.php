<?php
session_start();
require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">
            <i class="fas fa-tint me-2"></i>Water Demand Calculator
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
                    <form id="waterDemandForm">
                        <!-- Building Details Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('buildingDetails')">
                                <h3 class="section-title">Building Details</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="buildingDetails">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Building Type</label>
                                        <select class="form-select" id="buildingType" required>
                                            <option value="">Select building type...</option>
                                            <option value="residential">Residential</option>
                                            <option value="apartment">Apartment Building</option>
                                            <option value="commercial">Commercial Office</option>
                                            <option value="retail">Retail/Shopping</option>
                                            <option value="healthcare">Healthcare Facility</option>
                                            <option value="educational">Educational</option>
                                            <option value="hotel">Hotel/Hospitality</option>
                                            <option value="industrial">Industrial</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Calculation Method</label>
                                        <select class="form-select" id="calcMethod">
                                            <option value="occupants" selected>By Occupants</option>
                                            <option value="fixtures">By Fixtures</option>
                                            <option value="floor-area">By Floor Area</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Number of Occupants</label>
                                        <input type="number" class="form-control" id="occupants" min="1">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Floor Area (m²)</label>
                                        <input type="number" class="form-control" id="floorArea" min="1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fixture Details Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('fixtureDetails')">
                                <h3 class="section-title">Fixture Count</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="fixtureDetails" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Water Closets</label>
                                        <input type="number" class="form-control" id="waterClosets" min="0" value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Lavatories</label>
                                        <input type="number" class="form-control" id="lavatories" min="0" value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Showers</label>
                                        <input type="number" class="form-control" id="showers" min="0" value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Bathtubs</label>
                                        <input type="number" class="form-control" id="bathtubs" min="0" value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kitchen Sinks</label>
                                        <input type="number" class="form-control" id="kitchenSinks" min="0" value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Utility Sinks</label>
                                        <input type="number" class="form-control" id="utilitySinks" min="0" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Usage Pattern Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('usagePattern')">
                                <h3 class="section-title">Usage Pattern</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="usagePattern" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Operating Hours per Day</label>
                                        <input type="number" class="form-control" id="operatingHours" min="1" max="24" value="8">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Operating Days per Week</label>
                                        <input type="number" class="form-control" id="operatingDays" min="1" max="7" value="5">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Peak Factor</label>
                                        <select class="form-select" id="peakFactor">
                                            <option value="1.2">Low (1.2)</option>
                                            <option value="1.5" selected>Normal (1.5)</option>
                                            <option value="2.0">High (2.0)</option>
                                            <option value="2.5">Peak (2.5)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Diversity Factor</label>
                                        <input type="number" class="form-control" id="diversityFactor" step="0.1" min="0.1" max="1.0" value="0.5">
                                        <small class="text-white-50">0.1 = all fixtures used simultaneously, 1.0 = independent usage</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-gradient" onclick="calculateWaterDemand()">
                                <i class="fas fa-calculator"></i> Calculate Demand
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
                        <h4>Demand Rates (L/occupant/day)</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Building Type</th>
                                    <th>Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Residential</td>
                                    <td>150</td>
                                </tr>
                                <tr>
                                    <td>Apartment</td>
                                    <td>200</td>
                                </tr>
                                <tr>
                                    <td>Commercial</td>
                                    <td>50</td>
                                </tr>
                                <tr>
                                    <td>Healthcare</td>
                                    <td>300</td>
                                </tr>
                                <tr>
                                    <td>Educational</td>
                                    <td>75</td>
                                </tr>
                                <tr>
                                    <td>Hotel</td>
                                    <td>200</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="reference-table mb-3">
                        <h4>Fixture Units</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fixture</th>
                                    <th>WUFU</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Water Closet</td>
                                    <td>3.5</td>
                                </tr>
                                <tr>
                                    <td>Lavatory</td>
                                    <td>1.0</td>
                                </tr>
                                <tr>
                                    <td>Shower</td>
                                    <td>2.0</td>
                                </tr>
                                <tr>
                                    <td>Bathtub</td>
                                    <td>2.0</td>
                                </tr>
                                <tr>
                                    <td>Kitchen Sink</td>
                                    <td>1.5</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <small>Peak factor accounts for simultaneous demand. Diversity factor reflects probability of simultaneous fixture use.</small>
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
// Demand factors by building type (L/occupant/day)
const demandFactors = {
    residential: 150,
    apartment: 200,
    commercial: 50,
    retail: 75,
    healthcare: 300,
    educational: 75,
    hotel: 200,
    industrial: 100
};

// Fixture unit values (WUFU)
const fixtureUnits = {
    waterClosets: 3.5,
    lavatories: 1.0,
    showers: 2.0,
    bathtubs: 2.0,
    kitchenSinks: 1.5,
    utilitySinks: 1.5
};

// Floor area demand factors (L/m²/day)
const floorAreaFactors = {
    residential: 2.5,
    apartment: 3.0,
    commercial: 1.5,
    retail: 2.0,
    healthcare: 5.0,
    educational: 2.0,
    hotel: 3.5,
    industrial: 2.5
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

function calculateWaterDemand() {
    const buildingType = document.getElementById('buildingType').value;
    const calcMethod = document.getElementById('calcMethod').value;
    const occupants = parseInt(document.getElementById('occupants').value) || 0;
    const floorArea = parseInt(document.getElementById('floorArea').value) || 0;
    const operatingHours = parseInt(document.getElementById('operatingHours').value) || 8;
    const operatingDays = parseInt(document.getElementById('operatingDays').value) || 5;
    const peakFactor = parseFloat(document.getElementById('peakFactor').value) || 1.5;
    const diversityFactor = parseFloat(document.getElementById('diversityFactor').value) || 0.5;
    
    if (!buildingType) {
        alert('Please select a building type');
        return;
    }
    
    let totalDemand = 0;
    let calculationMethod = '';
    
    if (calcMethod === 'occupants' && occupants > 0) {
        const factor = demandFactors[buildingType] || 150;
        totalDemand = occupants * factor;
        calculationMethod = `${occupants} occupants × ${factor} L/occupant/day`;
    } else if (calcMethod === 'floor-area' && floorArea > 0) {
        const factor = floorAreaFactors[buildingType] || 2.0;
        totalDemand = floorArea * factor;
        calculationMethod = `${floorArea} m² × ${factor} L/m²/day`;
    } else if (calcMethod === 'fixtures') {
        let totalFixtureUnits = 0;
        Object.keys(fixtureUnits).forEach(fixture => {
            const count = parseInt(document.getElementById(fixture).value) || 0;
            totalFixtureUnits += count * fixtureUnits[fixture];
        });
        totalDemand = totalFixtureUnits * 100; // Average 100L per WUFU per day
        calculationMethod = `${totalFixtureUnits} WUFU × 100 L/WUFU/day`;
    } else {
        alert('Please enter valid data for the selected calculation method');
        return;
    }
    
    // Calculate peak demands
    const peakHourlyDemand = totalDemand * peakFactor / 24;
    const peakDailyDemand = totalDemand * peakFactor;
    const peakFlowRate = peakHourlyDemand / 3600; // L/s
    const avgFlowRate = (totalDemand / operatingHours) / 3600; // L/s
    
    // Calculate storage requirements
    const minStorageHours = 2; // Minimum 2 hours of average demand
    const recommendedStorage = Math.max(avgFlowRate * minStorageHours * 3600, peakHourlyDemand * 0.5);
    
    // Display results
    const resultsHtml = `
        <div class="result-item">
            <div class="result-label">Daily Water Demand</div>
            <div class="result-value">${totalDemand.toFixed(0)} L/day</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Average Flow Rate</div>
            <div class="result-value">${avgFlowRate.toFixed(3)} L/s</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Peak Hourly Demand</div>
            <div class="result-value">${peakHourlyDemand.toFixed(0)} L/hr</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Peak Flow Rate</div>
            <div class="result-value">${peakFlowRate.toFixed(3)} L/s</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Recommended Storage</div>
            <div class="result-value">${(recommendedStorage / 1000).toFixed(1)} m³</div>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i>
            <strong>Calculation Method:</strong> ${calculationMethod}
            <ul class="mb-0 mt-2">
                <li>Peak Factor: ${peakFactor}x (accounts for simultaneous demand)</li>
                <li>Diversity Factor: ${diversityFactor} (fixture usage probability)</li>
                <li>Operating Hours: ${operatingHours}h/day, ${operatingDays} days/week</li>
            </ul>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-lightbulb"></i>
            <strong>Recommendations:</strong>
            <ul class="mb-0 mt-2">
                <li>Design pipe sizing for ${peakFlowRate.toFixed(2)} L/s peak flow</li>
                <li>${peakFlowRate > 5 ? 'Consider pump or booster system' : 'Gravity-fed system likely sufficient'}</li>
                <li>${recommendedStorage > 10000 ? 'Large storage tank recommended' : 'Small storage or direct supply adequate'}</li>
                <li>Include ${(totalDemand * 0.1).toFixed(0)} L for emergency/fire reserve (10%)</li>
            </ul>
        </div>
    `;
    
    document.getElementById('results').innerHTML = resultsHtml;
    document.getElementById('resultsCard').style.display = 'block';
    
    // Save to recent calculations
    saveRecentCalculation('Water Demand', {
        buildingType: buildingType,
        calcMethod: calcMethod,
        totalDemand: totalDemand.toFixed(0),
        peakFlowRate: peakFlowRate.toFixed(3),
        avgFlowRate: avgFlowRate.toFixed(3)
    });
}

// Initialize - expand first section
document.addEventListener('DOMContentLoaded', function() {
    toggleSection('buildingDetails');
});

function saveRecentCalculation(name, data) {
    let history = JSON.parse(localStorage.getItem('waterDemandHistory') || '[]');
    history.unshift({
        name: name,
        date: new Date().toLocaleString(),
        data: data
    });
    history = history.slice(0, 10); // Keep only 10 recent
    localStorage.setItem('waterDemandHistory', JSON.stringify(history));
}
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
