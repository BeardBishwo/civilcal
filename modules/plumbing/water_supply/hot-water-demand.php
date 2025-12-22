<?php
session_start();
require_once __DIR__ . '/../../../themes/default/views/partials/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">Hot Water Demand Calculator</h1>
        <a href="<?php echo function_exists('app_base_url') ? app_base_url('modules/plumbing/index.php') : '../modules/plumbing/index.php'; ?>" class="btn btn-outline-light btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Plumbing
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Calculator Card -->
            <div class="card calculator-card">
                <div class="card-body p-4">
                    <form id="hotWaterDemandForm">
                        <!-- Building Type Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('buildingType')">
                                <h3 class="section-title">Building Type & Occupancy</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="buildingType">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Building Type</label>
                                        <select class="form-select" id="buildingType" required>
                                            <option value="">Select building type...</option>
                                            <option value="residential">Residential</option>
                                            <option value="commercial">Commercial</option>
                                            <option value="industrial">Industrial</option>
                                            <option value="healthcare">Healthcare</option>
                                            <option value="educational">Educational</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Number of Occupants</label>
                                        <input type="number" class="form-control" id="occupants" min="1" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Peak Factor</label>
                                        <select class="form-select" id="peakFactor">
                                            <option value="1.0">Low (1.0)</option>
                                            <option value="1.5" selected>Normal (1.5)</option>
                                            <option value="2.0">High (2.0)</option>
                                            <option value="2.5">Peak (2.5)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fixture Details Section -->
                        <div class="section-card mb-4">
                            <div class="section-header" onclick="toggleSection('fixtures')">
                                <h3 class="section-title">Hot Water Fixtures</h3>
                                <i class="fas fa-chevron-down section-icon"></i>
                            </div>
                            <div class="section-content" id="fixtures">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Lavatories/Sinks</label>
                                        <input type="number" class="form-control" id="lavatories" min="0" value="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Showers</label>
                                        <input type="number" class="form-control" id="showers" min="0" value="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Bathtubs</label>
                                        <input type="number" class="form-control" id="bathtubs" min="0" value="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kitchen Sinks</label>
                                        <input type="number" class="form-control" id="kitchenSinks" min="0" value="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Dishwashers</label>
                                        <input type="number" class="form-control" id="dishwashers" min="0" value="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Washing Machines</label>
                                        <input type="number" class="form-control" id="washingMachines" min="0" value="0">
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="addCustomFixture()">
                                            <i class="fas fa-plus"></i> Add Custom Fixture
                                        </button>
                                        <div id="customFixtures"></div>
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
                            <div class="section-content" id="usagePattern">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Operating Hours per Day</label>
                                        <input type="number" class="form-control" id="operatingHours" min="1" max="24" value="8">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Usage Distribution</label>
                                        <select class="form-select" id="usageDistribution">
                                            <option value="uniform">Uniform</option>
                                            <option value="morning-peak" selected>Morning Peak</option>
                                            <option value="evening-peak">Evening Peak</option>
                                            <option value="random">Random</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Temperature Rise (°F)</label>
                                        <input type="number" class="form-control" id="tempRise" min="20" max="120" value="80">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-gradient" onclick="calculateHotWaterDemand()">
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
                        <h4>Fixture Demand Rates (GPH)</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fixture</th>
                                    <th>Residential</th>
                                    <th>Commercial</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Lavatory</td>
                                    <td>2</td>
                                    <td>3</td>
                                </tr>
                                <tr>
                                    <td>Shower</td>
                                    <td>5</td>
                                    <td>5</td>
                                </tr>
                                <tr>
                                    <td>Bathtub</td>
                                    <td>20</td>
                                    <td>15</td>
                                </tr>
                                <tr>
                                    <td>Kitchen Sink</td>
                                    <td>4</td>
                                    <td>6</td>
                                </tr>
                                <tr>
                                    <td>Dishwasher</td>
                                    <td>6</td>
                                    <td>8</td>
                                </tr>
                                <tr>
                                    <td>Washing Machine</td>
                                    <td>20</td>
                                    <td>30</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="reference-table">
                        <h4>Occupancy Factors</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Building Type</th>
                                    <th>GPH/Occupant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Residential</td>
                                    <td>12</td>
                                </tr>
                                <tr>
                                    <td>Apartment</td>
                                    <td>15</td>
                                </tr>
                                <tr>
                                    <td>Hotel</td>
                                    <td>10</td>
                                </tr>
                                <tr>
                                    <td>Office</td>
                                    <td>2</td>
                                </tr>
                                <tr>
                                    <td>School</td>
                                    <td>3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <small>Peak factor accounts for simultaneous usage patterns. Higher factors for facilities with concentrated usage periods.</small>
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

.custom-fixture {
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
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
// Fixture demand rates (GPH)
const fixtureDemandRates = {
    residential: {
        lavatory: 2,
        shower: 5,
        bathtub: 20,
        kitchenSink: 4,
        dishwasher: 6,
        washingMachine: 20
    },
    commercial: {
        lavatory: 3,
        shower: 5,
        bathtub: 15,
        kitchenSink: 6,
        dishwasher: 8,
        washingMachine: 30
    }
};

// Occupancy factors (GPH per occupant)
const occupancyFactors = {
    residential: 12,
    apartment: 15,
    hotel: 10,
    office: 2,
    school: 3,
    healthcare: 15,
    industrial: 8
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

function addCustomFixture() {
    const container = document.getElementById('customFixtures');
    const index = container.children.length;
    
    const fixtureHtml = `
        <div class="custom-fixture" id="customFixture_${index}">
            <div class="row g-2">
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" placeholder="Fixture name" 
                           id="customName_${index}">
                </div>
                <div class="col-3">
                    <input type="number" class="form-control form-control-sm" placeholder="Count" 
                           id="customCount_${index}" min="1" value="1">
                </div>
                <div class="col-3">
                    <input type="number" class="form-control form-control-sm" placeholder="GPH/unit" 
                           id="customRate_${index}" min="0" step="0.1">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeCustomFixture(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', fixtureHtml);
}

function removeCustomFixture(index) {
    document.getElementById(`customFixture_${index}`).remove();
}

function calculateHotWaterDemand() {
    const buildingType = document.getElementById('buildingType').value;
    const occupants = parseInt(document.getElementById('occupants').value) || 0;
    const peakFactor = parseFloat(document.getElementById('peakFactor').value) || 1.0;
    const operatingHours = parseInt(document.getElementById('operatingHours').value) || 8;
    const tempRise = parseInt(document.getElementById('tempRise').value) || 80;
    
    if (!buildingType || occupants <= 0) {
        showNotification('Please enter building type and number of occupants', 'info');
        return;
    }
    
    // Calculate fixture demand
    let totalFixtureDemand = 0;
    const fixtureTypes = ['lavatories', 'showers', 'bathtubs', 'kitchenSinks', 'dishwashers', 'washingMachines'];
    
    fixtureTypes.forEach(fixture => {
        const count = parseInt(document.getElementById(fixture).value) || 0;
        const demand = fixtureDemandRates[buildingType]?.[fixture] || 0;
        totalFixtureDemand += count * demand;
    });
    
    // Add custom fixtures
    const customFixtures = document.querySelectorAll('[id^="customFixture_"]');
    customFixtures.forEach((fixture, index) => {
        const count = parseInt(document.getElementById(`customCount_${index}`)?.value) || 0;
        const rate = parseFloat(document.getElementById(`customRate_${index}`)?.value) || 0;
        totalFixtureDemand += count * rate;
    });
    
    // Calculate occupancy demand
    const occupancyRate = occupancyFactors[buildingType] || 10;
    const occupancyDemand = occupants * occupancyRate;
    
    // Peak demand (highest of fixture or occupancy, times peak factor)
    const peakDemand = Math.max(totalFixtureDemand, occupancyDemand) * peakFactor;
    
    // Average demand
    const averageDemand = peakDemand / 1.5; // Assuming average is about 2/3 of peak
    
    // Storage requirements
    const storageCapacity = Math.max(averageDemand * 0.5, peakDemand * 0.25); // Min storage
    
    // Heat required (BTU/hr)
    const heatRequired = (storageCapacity * tempRise * 8.34) / 24;
    
    // Display results
    const resultsHtml = `
        <div class="result-item">
            <div class="result-label">Average Hot Water Demand</div>
            <div class="result-value">${averageDemand.toFixed(1)} GPH</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Peak Hot Water Demand</div>
            <div class="result-value">${peakDemand.toFixed(1)} GPH</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Required Storage Capacity</div>
            <div class="result-value">${storageCapacity.toFixed(1)} Gallons</div>
        </div>
        
        <div class="result-item">
            <div class="result-label">Heating Capacity Required</div>
            <div class="result-value">${heatRequired.toFixed(0)} BTU/hr</div>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-lightbulb"></i>
            <strong>Recommendations:</strong>
            <ul class="mb-0 mt-2">
                <li>Consider a water heater with ${Math.ceil(heatRequired / 1000)}k BTU/hr input rating</li>
                <li>${storageCapacity >= 40 ? 'Tank-type' : 'Tankless or compact'} water heater recommended</li>
                <li>${operatingHours > 12 ? 'Continuous operation' : 'Intermittent operation'} capacity</li>
            </ul>
        </div>
    `;
    
    document.getElementById('results').innerHTML = resultsHtml;
    document.getElementById('resultsCard').style.display = 'block';
    
    // Save to recent calculations
    saveRecentCalculation('Hot Water Demand', {
        buildingType: buildingType,
        occupants: occupants,
        peakDemand: peakDemand.toFixed(1),
        averageDemand: averageDemand.toFixed(1),
        storageCapacity: storageCapacity.toFixed(1),
        heatRequired: Math.ceil(heatRequired / 1000) + 'k BTU/hr'
    });
}

// Initialize - expand first section
document.addEventListener('DOMContentLoaded', function() {
    toggleSection('buildingType');
});

function saveRecentCalculation(name, data) {
    let history = JSON.parse(localStorage.getItem('hotWaterHistory') || '[]');
    history.unshift({
        name: name,
        date: new Date().toLocaleString(),
        data: data
    });
    history = history.slice(0, 10); // Keep only 10 recent
    localStorage.setItem('hotWaterHistory', JSON.stringify(history));
}
</script>

<?php require_once __DIR__ . '/../../../themes/default/views/partials/footer.php'; ?>

