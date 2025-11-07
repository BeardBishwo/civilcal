<?php
require_once '../../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../../includes/functions.php';

// Get calculation results from database if available
$calculationId = $_GET['id'] ?? null;
$existingCalculation = null;

if ($calculationId) {
    $stmt = $pdo->prepare("SELECT * FROM mep_calculations WHERE id = ? AND module = 'pump_selection'");
    $stmt->execute([$calculationId]);
    $existingCalculation = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = "Pump Selection Calculator";
include '../../../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="page-title">Pump Selection Calculator</h1>
            <p class="page-description">Select and size pumps for water supply, drainage, and HVAC applications with comprehensive performance analysis.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>System Requirements</h5>
                </div>
                <div class="card-body">
                    <form id="pumpForm">
                        <div class="form-group">
                            <label for="projectName">Project Name</label>
                            <input type="text" class="form-control" id="projectName" name="projectName" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pumpType">Pump Type</label>
                                    <select class="form-control" id="pumpType" name="pumpType" required onchange="updateCalculation()">
                                        <option value="">Select Pump Type</option>
                                        <option value="centrifugal">Centrifugal (Single Stage)</option>
                                        <option value="multistage">Centrifugal (Multi-Stage)</option>
                                        <option value="submersible">Submersible</option>
                                        <option value="self_priming">Self-Priming</option>
                                        <option value="turbine">Vertical Turbine</option>
                                        <option value="booster">Booster Pump</option>
                                        <option value="sump">Sump Pump</option>
                                        <option value="circulating">Circulating Pump</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="application">Application</label>
                                    <select class="form-control" id="application" name="application" required onchange="updateCalculation()">
                                        <option value="">Select Application</option>
                                        <option value="water_supply">Water Supply</option>
                                        <option value="fire_protection">Fire Protection</option>
                                        <option value="hvac_circulation">HVAC Circulation</option>
                                        <option value="drainage">Drainage</option>
                                        <option value="irrigation">Irrigation</option>
                                        <option value="boiler_feed">Boiler Feed</option>
                                        <option value="cooling_tower">Cooling Tower</option>
                                        <option value="well_water">Well Water</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="flowRate">Flow Rate (GPM)</label>
                                    <input type="number" class="form-control" id="flowRate" name="flowRate" min="0.1" step="0.1" required onchange="updateCalculation()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="totalHead">Total Head (ft)</label>
                                    <input type="number" class="form-control" id="totalHead" name="totalHead" min="1" step="0.1" required onchange="updateCalculation()">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="staticHead">Static Head (ft)</label>
                                    <input type="number" class="form-control" id="staticHead" name="staticHead" min="0" step="0.1" value="0" onchange="updateCalculation()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="suctionHead">Suction Head (ft)</label>
                                    <input type="number" class="form-control" id="suctionHead" name="suctionHead" step="0.1" value="0" onchange="updateCalculation()">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pipeDiameter">Pipe Diameter (inches)</label>
                                    <select class="form-control" id="pipeDiameter" name="pipeDiameter" onchange="updateCalculation()">
                                        <option value="">Select Diameter</option>
                                        <option value="1">1"</option>
                                        <option value="1.25">1.25"</option>
                                        <option value="1.5">1.5"</option>
                                        <option value="2">2"</option>
                                        <option value="2.5">2.5"</option>
                                        <option value="3">3"</option>
                                        <option value="4">4"</option>
                                        <option value="6">6"</option>
                                        <option value="8">8"</option>
                                        <option value="10">10"</option>
                                        <option value="12">12"</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pipeLength">Total Pipe Length (ft)</label>
                                    <input type="number" class="form-control" id="pipeLength" name="pipeLength" min="1" value="100" onchange="updateCalculation()">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fluidViscosity">Fluid Viscosity (cp)</label>
                                    <input type="number" class="form-control" id="fluidViscosity" name="fluidViscosity" step="0.1" value="1" onchange="updateCalculation()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="operatingHours">Operating Hours/Day</label>
                                    <input type="number" class="form-control" id="operatingHours" name="operatingHours" min="1" max="24" value="8" onchange="updateCalculation()">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Pump Performance Analysis</h5>
                </div>
                <div class="card-body">
                    <div id="pumpResults">
                        <div class="alert alert-info">
                            <h6>Required Pump Performance</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Horsepower:</strong> <span id="requiredHp">-</span> HP
                                </div>
                                <div class="col-6">
                                    <strong>Efficiency:</strong> <span id="pumpEfficiency">-</span> %
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <strong>Motor Size:</strong> <span id="motorSize">-</span> HP
                                </div>
                                <div class="col-6">
                                    <strong>NPSH Required:</strong> <span id="npshRequired">-</span> ft
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h6>Hydraulic Analysis</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Friction Loss:</strong> <span id="frictionLoss">-</span> ft
                                </div>
                                <div class="col-6">
                                    <strong>Velocity Head:</strong> <span id="velocityHead">-</span> ft
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <strong>Total Dynamic Head:</strong> <span id="totalDynamicHead">-</span> ft
                                </div>
                                <div class="col-6">
                                    <strong>Shutoff Head:</strong> <span id="shutoffHead">-</span> ft
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success">
                            <h6>Power Consumption</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Water Horsepower:</strong> <span id="waterHp">-</span> HP
                                </div>
                                <div class="col-6">
                                    <strong>Brake Horsepower:</strong> <span id="brakeHp">-</span> HP
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <strong>Daily Energy:</strong> <span id="dailyEnergy">-</span> kWh
                                </div>
                                <div class="col-6">
                                    <strong>Annual Cost:</strong> <span id="annualCost">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Recommended Pumps</h5>
                </div>
                <div class="card-body">
                    <div id="pumpRecommendations">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Manufacturer</th>
                                        <th>Model</th>
                                        <th>Type</th>
                                        <th>Flow (GPM)</th>
                                        <th>Head (ft)</th>
                                        <th>Efficiency (%)</th>
                                        <th>HP</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody id="pumpTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Enter system requirements to view pump recommendations</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Cost Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="cost-item">
                                <strong>Pump Cost</strong>
                                <span id="pumpCost">$0</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cost-item">
                                <strong>Motor Cost</strong>
                                <span id="motorCost">$0</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="cost-item">
                                <strong>Installation</strong>
                                <span id="installationCost">$0</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cost-item">
                                <strong>Piping & Controls</strong>
                                <span id="pipingCost">$0</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <strong>Total Equipment Cost:</strong>
                        </div>
                        <div class="col-6">
                            <span id="equipmentCost" class="text-primary">$0</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <strong>Annual Operating Cost:</strong>
                        </div>
                        <div class="col-6">
                            <span id="operatingCost" class="text-danger">$0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Safety & Installation Notes</h5>
                </div>
                <div class="card-body">
                    <div id="safetyNotes">
                        <p class="text-muted">Enter system requirements to view safety and installation recommendations.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-center">
            <button type="button" class="btn btn-primary" onclick="calculatePump()">Calculate Pump Requirements</button>
            <button type="button" class="btn btn-success" onclick="saveCalculation()">Save Calculation</button>
            <button type="button" class="btn btn-secondary" onclick="exportResults()">Export Results</button>
            <button type="button" class="btn btn-info" onclick="printReport()">Print Report</button>
        </div>
    </div>
</div>

<script>
// Pump database with sample data
const pumpDatabase = [
    { manufacturer: "Grundfos", model: "CR5-18", type: "Multistage Centrifugal", flow: 25, head: 400, efficiency: 65, hp: 5, price: 3500 },
    { manufacturer: "Grundfos", model: "CR10-22", type: "Multistage Centrifugal", flow: 50, head: 500, efficiency: 68, hp: 10, price: 4500 },
    { manufacturer: "Grundfos", model: "CR20-15", type: "Multistage Centrifugal", flow: 100, head: 300, efficiency: 72, hp: 15, price: 6200 },
    { manufacturer: "Barnes", model: "3SE5122L", type: "Submersible", flow: 60, head: 50, efficiency: 58, hp: 3, price: 1800 },
    { manufacturer: "Barnes", model: "4SE5122L", type: "Submersible", flow: 120, head: 40, efficiency: 60, hp: 5, price: 2800 },
    { manufacturer: "Pentair", model: "F50FL", type: "Self-Priming", flow: 80, head: 80, efficiency: 62, hp: 5, price: 2200 },
    { manufacturer: "Pentair", model: "F80FL", type: "Self-Priming", flow: 150, head: 70, efficiency: 65, hp: 7.5, price: 3200 },
    { manufacturer: "KSB", model: "Etanorm 050-200", type: "Single Stage", flow: 200, head: 120, efficiency: 70, hp: 10, price: 2800 },
    { manufacturer: "KSB", model: "Etanorm 080-250", type: "Single Stage", flow: 400, head: 100, efficiency: 73, hp: 15, price: 4200 },
    { manufacturer: "Flowserve", model: "Durco Mark 3", type: "Single Stage", flow: 300, head: 150, efficiency: 75, hp: 20, price: 6500 }
];

// Cost estimation data
const costData = {
    pump_multiplier: 1.0,
    motor_multiplier: 0.6,
    installation_multiplier: 0.4,
    piping_multiplier: 0.3,
    energy_rate: 0.12, // $/kWh
    annual_multiplier: 365
};

// Pipe friction factors (Darcy-Weisbach approximation)
const pipeRoughness = {
    steel: 0.00015,
    plastic: 0.000005,
    copper: 0.000005,
    cast_iron: 0.00085
};

function updateCalculation() {
    const flowRate = parseFloat(document.getElementById('flowRate').value) || 0;
    const totalHead = parseFloat(document.getElementById('totalHead').value) || 0;
    const staticHead = parseFloat(document.getElementById('staticHead').value) || 0;
    const pipeDiameter = parseFloat(document.getElementById('pipeDiameter').value) || 0;
    const pipeLength = parseFloat(document.getElementById('pipeLength').value) || 0;
    const fluidViscosity = parseFloat(document.getElementById('fluidViscosity').value) || 1;

    if (flowRate === 0 || totalHead === 0) return;

    // Calculate basic hydraulic parameters
    const velocityHead = calculateVelocityHead(flowRate, pipeDiameter);
    const frictionLoss = calculateFrictionLoss(flowRate, pipeDiameter, pipeLength, fluidViscosity);
    const npshRequired = calculateNPSH(flowRate, totalHead, staticHead);
    
    // Calculate pump power requirements
    const waterHp = (flowRate * totalHead) / (3960 * 0.85); // Using water specific gravity = 0.85
    const pumpEfficiency = estimatePumpEfficiency(flowRate, totalHead);
    const brakeHp = waterHp / (pumpEfficiency / 100);
    const requiredHp = Math.ceil(brakeHp * 1.15); // Add 15% safety factor
    const motorSize = requiredHp; // Standard motor size

    // Calculate energy consumption and costs
    const dailyEnergy = (brakeHp * 0.746 * parseFloat(document.getElementById('operatingHours').value)) / pumpEfficiency * 100;
    const annualEnergy = dailyEnergy * costData.annual_multiplier;
    const annualOperatingCost = annualEnergy * costData.energy_rate;

    // Update display
    document.getElementById('requiredHp').textContent = requiredHp.toFixed(1);
    document.getElementById('pumpEfficiency').textContent = pumpEfficiency.toFixed(1);
    document.getElementById('motorSize').textContent = motorSize;
    document.getElementById('npshRequired').textContent = npshRequired.toFixed(1);
    document.getElementById('frictionLoss').textContent = frictionLoss.toFixed(1);
    document.getElementById('velocityHead').textContent = velocityHead.toFixed(2);
    document.getElementById('totalDynamicHead').textContent = totalHead.toFixed(1);
    document.getElementById('shutoffHead').textContent = (totalHead * 1.25).toFixed(1);
    document.getElementById('waterHp').textContent = waterHp.toFixed(2);
    document.getElementById('brakeHp').textContent = brakeHp.toFixed(2);
    document.getElementById('dailyEnergy').textContent = (dailyEnergy / 1000).toFixed(1);
    document.getElementById('annualCost').textContent = `$${annualOperatingCost.toLocaleString()}`;

    // Update costs
    const pumpCost = requiredHp * 500 * costData.pump_multiplier;
    const motorCost = motorSize * 300 * costData.motor_multiplier;
    const installationCost = pumpCost * costData.installation_multiplier;
    const pipingCost = (pumpCost + motorCost) * costData.piping_multiplier;
    const equipmentCost = pumpCost + motorCost + installationCost + pipingCost;

    document.getElementById('pumpCost').textContent = `$${pumpCost.toLocaleString()}`;
    document.getElementById('motorCost').textContent = `$${motorCost.toLocaleString()}`;
    document.getElementById('installationCost').textContent = `$${installationCost.toLocaleString()}`;
    document.getElementById('pipingCost').textContent = `$${pipingCost.toLocaleString()}`;
    document.getElementById('equipmentCost').textContent = `$${equipmentCost.toLocaleString()}`;
    document.getElementById('operatingCost').textContent = `$${annualOperatingCost.toLocaleString()}`;

    // Update pump recommendations
    updatePumpRecommendations(flowRate, totalHead, requiredHp);

    // Update safety notes
    updateSafetyNotes();
}

function calculateVelocityHead(flowRate, diameter) {
    if (diameter === 0) return 0;
    const velocity = (flowRate * 0.4085) / (Math.PI * Math.pow(diameter/2, 2)); // ft/s
    return Math.pow(velocity, 2) / (2 * 32.174); // ft
}

function calculateFrictionLoss(flowRate, diameter, length, viscosity) {
    if (diameter === 0 || length === 0) return 0;
    const velocity = (flowRate * 0.4085) / (Math.PI * Math.pow(diameter/2, 2)); // ft/s
    const reynolds = (velocity * diameter * 12) / (0.0006 * viscosity); // Simplified Reynolds number
    const frictionFactor = 0.02 + (0.0005 / diameter); // Simplified friction factor
    return frictionFactor * (length / (diameter * 12)) * Math.pow(velocity, 2) / (2 * 32.174);
}

function calculateNPSH(flowRate, head, staticHead) {
    // Simplified NPSH calculation - actual calculation depends on pump type
    return Math.max(3, head * 0.1 + staticHead * 0.05);
}

function estimatePumpEfficiency(flowRate, head) {
    // Simplified pump efficiency estimation based on flow and head
    let efficiency = 70; // Base efficiency
    
    // Adjust based on flow rate (higher flow = higher efficiency up to a point)
    if (flowRate < 50) efficiency -= 15;
    else if (flowRate < 100) efficiency -= 5;
    else if (flowRate > 500) efficiency -= 10;
    
    // Adjust based on head (very high head reduces efficiency)
    if (head > 500) efficiency -= 10;
    else if (head > 300) efficiency -= 5;
    
    return Math.max(40, Math.min(85, efficiency));
}

function updatePumpRecommendations(flowRate, totalHead, requiredHp) {
    const tbody = document.getElementById('pumpTableBody');
    tbody.innerHTML = '';

    // Filter pumps based on requirements
    const suitablePumps = pumpDatabase.filter(pump => 
        pump.flow >= flowRate * 0.8 && 
        pump.flow <= flowRate * 1.2 &&
        pump.head >= totalHead * 0.9 &&
        pump.head <= totalHead * 1.1 &&
        pump.hp >= requiredHp * 0.8
    );

    // Add top 5 recommendations or all suitable if less than 5
    const recommendations = suitablePumps
        .sort((a, b) => Math.abs(a.flow - flowRate) + Math.abs(a.head - totalHead) - 
                          (Math.abs(b.flow - flowRate) + Math.abs(b.head - totalHead)))
        .slice(0, 5);

    recommendations.forEach(pump => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${pump.manufacturer}</td>
            <td>${pump.model}</td>
            <td>${pump.type}</td>
            <td>${pump.flow}</td>
            <td>${pump.head}</td>
            <td>${pump.efficiency}</td>
            <td>${pump.hp}</td>
            <td>$${pump.price.toLocaleString()}</td>
        `;
    });

    if (recommendations.length === 0) {
        const row = tbody.insertRow();
        row.innerHTML = '<td colspan="8" class="text-center text-warning">No exact matches found. Consider modifying requirements.</td>';
    }
}

function updateSafetyNotes() {
    const pumpType = document.getElementById('pumpType').value;
    const application = document.getElementById('application').value;
    const flowRate = parseFloat(document.getElementById('flowRate').value) || 0;
    
    let notes = '<h6>Installation Recommendations</h6><ul>';

    if (pumpType === 'centrifugal') {
        notes += '<li>Ensure adequate suction head to prevent cavitation</li>';
        notes += '<li>Install pressure gauge on discharge side</li>';
    } else if (pumpType === 'submersible') {
        notes += '<li>Ensure proper sealing and cable management</li>';
        notes += '<li>Check minimum submergence requirements</li>';
    }

    if (application === 'fire_protection') {
        notes += '<li>Comply with NFPA 20 standards</li>';
        notes += '<li>Install backup power supply</li>';
        notes += '<li>Regular testing and maintenance required</li>';
    }

    if (flowRate > 500) {
        notes += '<li>Consider multiple smaller pumps for redundancy</li>';
        notes += '<li>Install surge protection devices</li>';
    }

    notes += '<li>Install check valve and isolation valves</li>';
    notes += '<li>Provide adequate ventilation for motor cooling</li>';
    notes += '<li>Include pressure relief valves in system design</li>';
    notes += '</ul>';

    document.getElementById('safetyNotes').innerHTML = notes;
}

function calculatePump() {
    updateCalculation();
    
    // Show success message
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show';
    alert.innerHTML = `
        Pump selection calculation completed successfully.
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.container-fluid').firstChild);
}

function saveCalculation() {
    const formData = new FormData(document.getElementById('pumpForm'));
    formData.append('module', 'pump_selection');
    formData.append('results', document.getElementById('pumpResults').innerHTML);
    
    fetch('../../api/save_calculation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Calculation saved successfully!');
        } else {
            alert('Error saving calculation: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving calculation');
    });
}

function exportResults() {
    const results = {
        project: {
            name: document.getElementById('projectName').value,
            pumpType: document.getElementById('pumpType').value,
            application: document.getElementById('application').value
        },
        requirements: {
            flowRate: document.getElementById('flowRate').value,
            totalHead: document.getElementById('totalHead').value,
            staticHead: document.getElementById('staticHead').value,
            pipeDiameter: document.getElementById('pipeDiameter').value,
            pipeLength: document.getElementById('pipeLength').value
        },
        performance: {
            requiredHp: document.getElementById('requiredHp').textContent,
            pumpEfficiency: document.getElementById('pumpEfficiency').textContent,
            motorSize: document.getElementById('motorSize').textContent,
            npshRequired: document.getElementById('npshRequired').textContent,
            waterHp: document.getElementById('waterHp').textContent,
            brakeHp: document.getElementById('brakeHp').textContent
        },
        costs: {
            equipment: document.getElementById('equipmentCost').textContent,
            annualOperating: document.getElementById('operatingCost').textContent
        }
    };

    const dataStr = JSON.stringify(results, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'pump_selection_results.json';
    link.click();
}

function printReport() {
    window.print();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Set default values
    document.getElementById('fluidViscosity').value = 1; // Water viscosity in centipoise
    document.getElementById('operatingHours').value = 8;
    
    // Auto-update when inputs change
    const inputs = ['flowRate', 'totalHead', 'staticHead', 'pipeDiameter', 'pipeLength', 'fluidViscosity', 'operatingHours'];
    inputs.forEach(id => {
        document.getElementById(id).addEventListener('input', updateCalculation);
        document.getElementById(id).addEventListener('change', updateCalculation);
    });
    
    document.getElementById('pumpType').addEventListener('change', updateCalculation);
    document.getElementById('application').addEventListener('change', updateCalculation);
});
</script>

<?php include '../../../includes/footer.php'; ?>
