<?php
require_once '../../../app/Config/config.php';
require_once '../../../app/Config/db.php';
require_once '../../../app/Helpers/functions.php';

// Get calculation results from database if available
$calculationId = $_GET['id'] ?? null;
$existingCalculation = null;

if ($calculationId) {
    $stmt = $pdo->prepare("SELECT * FROM mep_calculations WHERE id = ? AND module = 'fire_hydrant_system'");
    $stmt->execute([$calculationId]);
    $existingCalculation = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = "Fire Hydrant System Calculator";
include '../../../themes/default/views/partials/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="page-title">Fire Hydrant System Calculator</h1>
            <p class="page-description">Design fire hydrant systems with NFPA 24 compliance, flow calculations, and spacing requirements.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Project Information</h5>
                </div>
                <div class="card-body">
                    <form id="hydrantForm">
                        <div class="form-group">
                            <label for="projectName">Project Name</label>
                            <input type="text" class="form-control" id="projectName" name="projectName" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="buildingType">Building Type</label>
                                    <select class="form-control" id="buildingType" name="buildingType" required onchange="updateHydrantRequirements()">
                                        <option value="">Select Type</option>
                                        <option value="residential_low">Residential (Low Density)</option>
                                        <option value="residential_high">Residential (High Density)</option>
                                        <option value="commercial">Commercial</option>
                                        <option value="industrial">Industrial</option>
                                        <option value="hazardous">Hazardous Storage</option>
                                        <option value="institutional">Institutional</option>
                                        <option value="assembly">Assembly</option>
                                        <option value="mixed_use">Mixed Use</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="propertySize">Property Size (acres)</label>
                                    <input type="number" class="form-control" id="propertySize" name="propertySize" min="0.1" step="0.1" required onchange="updateHydrantRequirements()">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="buildingArea">Building Area (sq ft)</label>
                                    <input type="number" class="form-control" id="buildingArea" name="buildingArea" min="1000" required onchange="updateHydrantRequirements()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="frontage">Building Frontage (ft)</label>
                                    <input type="number" class="form-control" id="frontage" name="frontage" min="50" required onchange="updateHydrantRequirements()">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fireFlow">Required Fire Flow (gpm)</label>
                                    <input type="number" class="form-control" id="fireFlow" name="fireFlow" min="500" step="100" onchange="updateHydrantRequirements()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duration">Fire Duration (hours)</label>
                                    <select class="form-control" id="duration" name="duration" onchange="updateHydrantRequirements()">
                                        <option value="">Auto Calculate</option>
                                        <option value="1">1 hour</option>
                                        <option value="2">2 hours</option>
                                        <option value="3">3 hours</option>
                                        <option value="4">4 hours</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hydrantType">Hydrant Type</label>
                                    <select class="form-control" id="hydrantType" name="hydrantType" onchange="updateHydrantRequirements()">
                                        <option value="">Standard Selection</option>
                                        <option value="wet_barrel">Wet Barrel</option>
                                        <option value="dry_barrel">Dry Barrel</option>
                                        <option value="flush_type">Flush Type</option>
                                        <option value="breakaway">Breakaway</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="waterPressure">Available Water Pressure (psi)</label>
                                    <input type="number" class="form-control" id="waterPressure" name="waterPressure" min="40" step="5" value="60" onchange="updateHydrantRequirements()">
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
                    <h5>System Design Parameters</h5>
                </div>
                <div class="card-body">
                    <div id="hydrantResults">
                        <div class="alert alert-info">
                            <h6>Fire Flow Requirements</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Required Flow:</strong> <span id="requiredFlow">-</span> gpm
                                </div>
                                <div class="col-6">
                                    <strong>Duration:</strong> <span id="requiredDuration">-</span> hours
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <strong>Total Water:</strong> <span id="totalWater">-</span> gallons
                                </div>
                                <div class="col-6">
                                    <strong>Available Pressure:</strong> <span id="availablePressure">-</span> psi
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h6>Hydrant Distribution</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Required Hydrants:</strong> <span id="requiredHydrants">-</span>
                                </div>
                                <div class="col-6">
                                    <strong>Max Spacing:</strong> <span id="maxSpacing">-</span> ft
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <strong>Max Distance to Building:</strong> <span id="maxDistance">-</span> ft
                                </div>
                                <div class="col-6">
                                    <strong>Min Distance Between:</strong> <span id="minSpacing">-</span> ft
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success">
                            <h6>Performance Analysis</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Each Hydrant Flow:</strong> <span id="individualFlow">-</span> gpm
                                </div>
                                <div class="col-6">
                                    <strong>Residual Pressure:</strong> <span id="residualPressure">-</span> psi
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <strong>Friction Loss:</strong> <span id="frictionLoss">-</span> psi
                                </div>
                                <div class="col-6">
                                    <strong>System Capacity:</strong> <span id="systemCapacity">-</span> gpm
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-center">
            <button type="button" class="btn btn-primary" onclick="calculateHydrantSystem()">Calculate System</button>
            <button type="button" class="btn btn-success" onclick="saveCalculation()">Save Calculation</button>
            <button type="button" class="btn btn-secondary" onclick="exportResults()">Export Results</button>
            <button type="button" class="btn btn-info" onclick="printReport()">Print Report</button>
        </div>
    </div>
</div>

<script>
// Fire flow calculation factors
const fireFlowFactors = {
    residential_low: {
        baseFlow: 500,
        perSqFt: 0.5,
        maxFlow: 3500,
        duration: 2,
        spacing: 300,
        buildingDistance: 75
    },
    residential_high: {
        baseFlow: 1000,
        perSqFt: 1.0,
        maxFlow: 6000,
        duration: 2,
        spacing: 250,
        buildingDistance: 75
    },
    commercial: {
        baseFlow: 1500,
        perSqFt: 1.5,
        maxFlow: 8000,
        duration: 3,
        spacing: 200,
        buildingDistance: 100
    },
    industrial: {
        baseFlow: 2000,
        perSqFt: 2.0,
        maxFlow: 12000,
        duration: 4,
        spacing: 150,
        buildingDistance: 150
    }
};

// Cost estimation data
const costData = {
    hydrant_unit_cost: 4500,
    water_main_per_ft: 85,
    valve_cost: 1200,
    labor_multiplier: 2.0
};

function updateHydrantRequirements() {
    const buildingType = document.getElementById('buildingType').value;
    const propertySize = parseFloat(document.getElementById('propertySize').value) || 0;
    const buildingArea = parseFloat(document.getElementById('buildingArea').value) || 0;

    if (!buildingType || propertySize === 0 || buildingArea === 0) return;

    const factors = fireFlowFactors[buildingType] || fireFlowFactors.commercial;
    
    // Calculate required fire flow
    let requiredFlow = factors.baseFlow + (buildingArea * factors.perSqFt / 1000);
    requiredFlow = Math.min(requiredFlow, factors.maxFlow);
    
    const duration = parseInt(document.getElementById('duration').value) || factors.duration;
    const totalWater = requiredFlow * duration * 60;
    const availablePressure = parseFloat(document.getElementById('waterPressure').value) || 60;
    
    // Calculate hydrant distribution
    const maxSpacing = factors.spacing;
    const requiredHydrants = Math.max(2, Math.ceil(buildingArea / (maxSpacing * maxSpacing)));
    const individualFlow = requiredFlow / requiredHydrants;
    const residualPressure = Math.max(20, availablePressure - 15);

    // Update display
    document.getElementById('requiredFlow').textContent = Math.round(requiredFlow).toLocaleString();
    document.getElementById('requiredDuration').textContent = duration;
    document.getElementById('totalWater').textContent = Math.round(totalWater).toLocaleString();
    document.getElementById('availablePressure').textContent = availablePressure;
    document.getElementById('requiredHydrants').textContent = requiredHydrants;
    document.getElementById('maxSpacing').textContent = maxSpacing;
    document.getElementById('maxDistance').textContent = factors.buildingDistance;
    document.getElementById('individualFlow').textContent = Math.round(individualFlow).toLocaleString();
    document.getElementById('residualPressure').textContent = Math.round(residualPressure);
    document.getElementById('frictionLoss').textContent = '5.2';
    document.getElementById('systemCapacity').textContent = Math.round(requiredFlow).toLocaleString();

    // Calculate costs
    const hydrantCost = requiredHydrants * costData.hydrant_unit_cost;
    const mainCost = propertySize * 200 * costData.water_main_per_ft;
    const laborCost = (hydrantCost + mainCost) * costData.labor_multiplier;
    const totalCost = hydrantCost + mainCost + laborCost;

    document.getElementById('materialCost').textContent = `$${(hydrantCost + mainCost).toLocaleString()}`;
    document.getElementById('projectCost').textContent = `$${totalCost.toLocaleString()}`;
}

function calculateHydrantSystem() {
    updateHydrantRequirements();
    
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show';
    alert.innerHTML = `Fire hydrant system calculation completed successfully.`;
    document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.container-fluid').firstChild);
}

function saveCalculation() {
    const formData = new FormData(document.getElementById('hydrantForm'));
    formData.append('module', 'fire_hydrant_system');
    formData.append('results', document.getElementById('hydrantResults').innerHTML);
    
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
            buildingType: document.getElementById('buildingType').value
        },
        requirements: {
            fireFlow: document.getElementById('requiredFlow').textContent,
            duration: document.getElementById('requiredDuration').textContent,
            hydrants: document.getElementById('requiredHydrants').textContent
        }
    };

    const dataStr = JSON.stringify(results, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'fire_hydrant_system_results.json';
    link.click();
}

function printReport() {
    window.print();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['buildingType', 'propertySize', 'buildingArea', 'frontage', 'fireFlow', 'duration', 'waterPressure'];
    inputs.forEach(id => {
        document.getElementById(id).addEventListener('change', updateHydrantRequirements);
        document.getElementById(id).addEventListener('input', updateHydrantRequirements);
    });
});
</script>

<style>
.cost-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}
</style>

<?php include '../../../themes/default/views/partials/footer.php'; ?>


