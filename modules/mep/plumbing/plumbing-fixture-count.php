<?php
require_once '../../../app/Config/config.php';
require_once '../../../app/Config/db.php';
require_once '../../../app/Helpers/functions.php';

// Get calculation results from database if available
$calculationId = $_GET['id'] ?? null;
$existingCalculation = null;

if ($calculationId) {
    $stmt = $pdo->prepare("SELECT * FROM mep_calculations WHERE id = ? AND module = 'plumbing_fixture_count'");
    $stmt->execute([$calculationId]);
    $existingCalculation = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = "Plumbing Fixture Count Calculator";
include '../../../themes/default/views/partials/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="page-title">Plumbing Fixture Count Calculator</h1>
            <p class="page-description">Calculate required plumbing fixtures based on occupancy type and occupant load with code compliance checking.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Project Information</h5>
                </div>
                <div class="card-body">
                    <form id="fixtureForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="projectName">Project Name</label>
                                    <input type="text" class="form-control" id="projectName" name="projectName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="buildingType">Building Type</label>
                                    <select class="form-control" id="buildingType" name="buildingType" required onchange="updateFixtureUnits()">
                                        <option value="">Select Building Type</option>
                                        <option value="office">Office Building</option>
                                        <option value="school">School</option>
                                        <option value="hospital">Hospital</option>
                                        <option value="restaurant">Restaurant</option>
                                        <option value="theater">Theater</option>
                                        <option value="retail">Retail Store</option>
                                        <option value="hotel">Hotel</option>
                                        <option value="dormitory">Dormitory</option>
                                        <option value="factory">Factory</option>
                                        <option value="stadium">Stadium</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="totalOccupants">Total Occupants</label>
                                    <input type="number" class="form-control" id="totalOccupants" name="totalOccupants" min="1" required onchange="updateFixtureUnits()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codeStandard">Code Standard</label>
                                    <select class="form-control" id="codeStandard" name="codeStandard" required onchange="updateFixtureUnits()">
                                        <option value="ipc">International Plumbing Code (IPC)</option>
                                        <option value="upc">Uniform Plumbing Code (UPC)</option>
                                        <option value="nsf">National Standard Plumbing Code</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="femaleOccupants">Female Occupants (%)</label>
                                    <input type="number" class="form-control" id="femaleOccupants" name="femaleOccupants" min="0" max="100" value="50" onchange="updateFixtureUnits()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="maleOccupants">Male Occupants (%)</label>
                                    <input type="number" class="form-control" id="maleOccupants" name="maleOccupants" min="0" max="100" value="50" onchange="updateFixtureUnits()">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="disabledRequired">ADA Accessibility Required</label>
                                    <select class="form-control" id="disabledRequired" name="disabledRequired" onchange="updateFixtureUnits()">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numberOfFloors">Number of Floors</label>
                                    <input type="number" class="form-control" id="numberOfFloors" name="numberOfFloors" min="1" value="1" onchange="updateFixtureUnits()">
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
                    <h5>Fixture Requirements</h5>
                </div>
                <div class="card-body">
                    <div id="fixtureResults">
                        <div class="alert alert-info">
                            <h6>Required Fixtures (Based on Code Standards)</h6>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Water Closets (Toilets)</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Male:</strong> <span id="maleWc">-</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Female:</strong> <span id="femaleWc">-</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Required: <span id="totalWc">-</span></small>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-6">
                                    <strong>Lavatories:</strong> <span id="lavatories">-</span>
                                </div>
                                <div class="col-6">
                                    <strong>Urinals:</strong> <span id="urinals">-</span>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Drinking Fountains</h6>
                                    <strong>Required:</strong> <span id="drinkingFountains">-</span>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Accessibility Features</h6>
                                    <strong>Accessible WC:</strong> <span id="accessibleWc">-</span><br>
                                    <strong>Accessible Lavatory:</strong> <span id="accessibleLav">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h6>Fixture Units Summary</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Total DFU:</strong> <span id="totalDfu">-</span>
                                </div>
                                <div class="col-6">
                                    <strong>Peak Demand:</strong> <span id="peakDemand">-</span> GPM
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
                    <h5>Cost Estimation</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="cost-item">
                                <strong>Fixture Installation</strong>
                                <span id="fixtureCost">$0</span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="cost-item">
                                <strong>Plumbing Rough-in</strong>
                                <span id="roughinCost">$0</span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="cost-item">
                                <strong>Accessibility Features</strong>
                                <span id="accessibilityCost">$0</span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="cost-item">
                                <strong>Total Estimated Cost</strong>
                                <span id="totalCost" class="text-primary">$0</span>
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
                    <h5>Code Compliance Notes</h5>
                </div>
                <div class="card-body">
                    <div id="complianceNotes">
                        <p class="text-muted">Select building type and enter occupant count to view code compliance requirements.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-center">
            <button type="button" class="btn btn-primary" onclick="calculateFixtures()">Calculate Requirements</button>
            <button type="button" class="btn btn-success" onclick="saveCalculation()">Save Calculation</button>
            <button type="button" class="btn btn-secondary" onclick="exportResults()">Export Results</button>
            <button type="button" class="btn btn-info" onclick="printReport()">Print Report</button>
        </div>
    </div>
</div>

<script>
// Fixture unit data based on building codes
const fixtureUnits = {
    ipc: {
        office: { wc_male: 1/15, wc_female: 1/15, lavatory: 1/15, urinal: 1/20, drinking: 1/100 },
        school: { wc_male: 1/35, wc_female: 1/35, lavatory: 1/35, urinal: 1/35, drinking: 1/50 },
        hospital: { wc_male: 1/15, wc_female: 1/15, lavatory: 1/15, urinal: 1/20, drinking: 1/75 },
        restaurant: { wc_male: 1/50, wc_female: 1/50, lavatory: 1/50, urinal: 1/25, drinking: 1/100 },
        theater: { wc_male: 1/100, wc_female: 1/100, lavatory: 1/100, urinal: 1/50, drinking: 1/200 },
        retail: { wc_male: 1/50, wc_female: 1/50, lavatory: 1/50, urinal: 1/25, drinking: 1/100 },
        hotel: { wc_male: 1/10, wc_female: 1/10, lavatory: 1/10, urinal: 1/20, drinking: 1/100 },
        dormitory: { wc_male: 1/10, wc_female: 1/10, lavatory: 1/10, urinal: 1/20, drinking: 1/50 },
        factory: { wc_male: 1/10, wc_female: 1/10, lavatory: 1/10, urinal: 1/10, drinking: 1/100 },
        stadium: { wc_male: 1/100, wc_female: 1/100, lavatory: 1/100, urinal: 1/50, drinking: 1/200 }
    },
    upc: {
        office: { wc_male: 1/15, wc_female: 1/15, lavatory: 1/15, urinal: 1/20, drinking: 1/100 },
        school: { wc_male: 1/35, wc_female: 1/35, lavatory: 1/35, urinal: 1/35, drinking: 1/50 },
        hospital: { wc_male: 1/15, wc_female: 1/15, lavatory: 1/15, urinal: 1/20, drinking: 1/75 },
        restaurant: { wc_male: 1/50, wc_female: 1/50, lavatory: 1/50, urinal: 1/25, drinking: 1/100 },
        theater: { wc_male: 1/100, wc_female: 1/100, lavatory: 1/100, urinal: 1/50, drinking: 1/200 },
        retail: { wc_male: 1/50, wc_female: 1/50, lavatory: 1/50, urinal: 1/25, drinking: 1/100 },
        hotel: { wc_male: 1/10, wc_female: 1/10, lavatory: 1/10, urinal: 1/20, drinking: 1/100 },
        dormitory: { wc_male: 1/10, wc_female: 1/10, lavatory: 1/10, urinal: 1/20, drinking: 1/50 },
        factory: { wc_male: 1/10, wc_female: 1/10, lavatory: 1/10, urinal: 1/10, drinking: 1/100 },
        stadium: { wc_male: 1/100, wc_female: 1/100, lavatory: 1/100, urinal: 1/50, drinking: 1/200 }
    }
};

// Cost estimation data
const costData = {
    wc_cost: 800,
    lavatory_cost: 400,
    urinal_cost: 600,
    drinking_fountain_cost: 1200,
    roughin_multiplier: 2.5,
    accessibility_multiplier: 1.5
};

function updateFixtureUnits() {
    const buildingType = document.getElementById('buildingType').value;
    const codeStandard = document.getElementById('codeStandard').value;
    const totalOccupants = parseInt(document.getElementById('totalOccupants').value) || 0;
    const maleOccupants = Math.floor(totalOccupants * (parseInt(document.getElementById('maleOccupants').value) / 100));
    const femaleOccupants = totalOccupants - maleOccupants;

    if (!buildingType || !codeStandard || totalOccupants === 0) return;

    const units = fixtureUnits[codeStandard][buildingType];
    
    // Calculate required fixtures
    let maleWc = Math.ceil(maleOccupants * units.wc_male);
    let femaleWc = Math.ceil(femaleOccupants * units.wc_female);
    let lavatories = Math.ceil(totalOccupants * units.lavatory);
    let urinals = Math.ceil(maleOccupants * units.urinal);
    let drinkingFountains = Math.ceil(totalOccupants * units.drinking);

    // Ensure minimum requirements
    maleWc = Math.max(maleWc, 1);
    femaleWc = Math.max(femaleWc, 1);
    lavatories = Math.max(lavatories, 1);

    // Update display
    document.getElementById('maleWc').textContent = maleWc;
    document.getElementById('femaleWc').textContent = femaleWc;
    document.getElementById('totalWc').textContent = maleWc + femaleWc;
    document.getElementById('lavatories').textContent = lavatories;
    document.getElementById('urinals').textContent = urinals;
    document.getElementById('drinkingFountains').textContent = drinkingFountains;

    // ADA accessibility requirements
    const disabledRequired = document.getElementById('disabledRequired').value;
    let accessibleWc = 0;
    let accessibleLav = 0;

    if (disabledRequired === 'yes') {
        accessibleWc = Math.ceil((maleWc + femaleWc) * 0.05); // 5% minimum
        accessibleLav = Math.ceil(lavatories * 0.05);
        accessibleWc = Math.max(accessibleWc, 1);
        accessibleLav = Math.max(accessibleLav, 1);
    }

    document.getElementById('accessibleWc').textContent = accessibleWc;
    document.getElementById('accessibleLav').textContent = accessibleLav;

    // Calculate total DFU ( Drainage Fixture Units )
    const totalDfu = (maleWc * 3.5) + (femaleWc * 3.5) + (lavatories * 1.0) + (urinals * 2.0);
    document.getElementById('totalDfu').textContent = totalDfu.toFixed(1);

    // Calculate peak demand using Hunter's curve (simplified)
    const peakDemand = Math.sqrt(totalDfu) * 2.0; // Simplified Hunter's curve
    document.getElementById('peakDemand').textContent = peakDemand.toFixed(1);

    // Calculate costs
    const fixtureCost = (maleWc + femaleWc) * costData.wc_cost + 
                       lavatories * costData.lavatory_cost + 
                       urinals * costData.urinal_cost + 
                       drinkingFountains * costData.drinking_fountain_cost;
    
    const roughinCost = fixtureCost * costData.roughin_multiplier;
    const accessibilityCost = disabledRequired === 'yes' ? (accessibleWc + accessibleLav) * 1000 : 0;
    const totalCost = fixtureCost + roughinCost + accessibilityCost;

    document.getElementById('fixtureCost').textContent = `$${fixtureCost.toLocaleString()}`;
    document.getElementById('roughinCost').textContent = `$${roughinCost.toLocaleString()}`;
    document.getElementById('accessibilityCost').textContent = `$${accessibilityCost.toLocaleString()}`;
    document.getElementById('totalCost').textContent = `$${totalCost.toLocaleString()}`;

    // Update compliance notes
    updateComplianceNotes(buildingType, codeStandard, totalOccupants);
}

function updateComplianceNotes(buildingType, codeStandard, totalOccupants) {
    const complianceDiv = document.getElementById('complianceNotes');
    let notes = '';

    switch (buildingType) {
        case 'office':
            notes = '<h6>Office Building Requirements</h6>';
            notes += '<ul>';
            notes += '<li>Minimum 1 toilet and 1 lavatory per 15 occupants for each sex</li>';
            notes += '<li>Separate facilities required for each sex if total occupants exceed 15</li>';
            notes += '<li>Drinking fountain required per 100 occupants</li>';
            notes += '</ul>';
            break;
        case 'school':
            notes = '<h6>School Requirements</h6>';
            notes += '<ul>';
            notes += '<li>Minimum 1 toilet and 1 lavatory per 35 students for each sex</li>';
            notes += '<li>Urinals may substitute for up to 1/3 of required toilets for males</li>';
            notes += '<li>Separate facilities for staff and students may be required</li>';
            notes += '</ul>';
            break;
        case 'restaurant':
            notes = '<h6>Restaurant Requirements</h6>';
            notes += '<ul>';
            notes += '<li>Minimum 1 toilet and 1 lavatory per 50 occupants for each sex</li>';
            notes += '<li>Additional facilities may be required based on floor area</li>';
            notes += '<li>Hand sinks required in food preparation areas</li>';
            notes += '</ul>';
            break;
        case 'hotel':
            notes = '<h6>Hotel Requirements</h6>';
            notes += '<ul>';
            notes += '<li>Minimum 1 toilet and 1 lavatory per 10 sleeping rooms for each sex</li>';
            notes += '<li>Separate guest and employee facilities required</li>';
            notes += '<li>Amenities required in guest rooms vary by star rating</li>';
            notes += '</ul>';
            break;
        default:
            notes = `<h6>${buildingType.charAt(0).toUpperCase() + buildingType.slice(1)} Requirements</h6>`;
            notes += '<p>Requirements vary based on specific occupancy classification and local amendments.</p>';
    }

    // Code standard specific notes
    notes += '<hr><h6>Code Standard Notes</h6>';
    if (codeStandard === 'ipc') {
        notes += '<p><strong>International Plumbing Code:</strong> Widely adopted with comprehensive fixture requirements.</p>';
    } else if (codeStandard === 'upc') {
        notes += '<p><strong>Uniform Plumbing Code:</strong> Used primarily in western US with specific calculation methods.</p>';
    } else {
        notes += '<p><strong>National Standard Plumbing Code:</strong> Regional code with local modifications.</p>';
    }

    complianceDiv.innerHTML = notes;
}

function calculateFixtures() {
    updateFixtureUnits();
    
    // Show success message
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show';
    alert.innerHTML = `
        Fixture count calculation completed successfully.
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.container-fluid').firstChild);
}

function saveCalculation() {
    const formData = new FormData(document.getElementById('fixtureForm'));
    formData.append('module', 'plumbing_fixture_count');
    formData.append('results', document.getElementById('fixtureResults').innerHTML);
    
    fetch('../../api/save_calculation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Calculation saved successfully!', 'info');
        } else {
            showNotification('Error saving calculation: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving calculation', 'danger');
    });
}

function exportResults() {
    const results = {
        project: {
            name: document.getElementById('projectName').value,
            type: document.getElementById('buildingType').value,
            totalOccupants: document.getElementById('totalOccupants').value,
            codeStandard: document.getElementById('codeStandard').value
        },
        fixtures: {
            maleWc: document.getElementById('maleWc').textContent,
            femaleWc: document.getElementById('femaleWc').textContent,
            totalWc: document.getElementById('totalWc').textContent,
            lavatories: document.getElementById('lavatories').textContent,
            urinals: document.getElementById('urinals').textContent,
            drinkingFountains: document.getElementById('drinkingFountains').textContent,
            accessibleWc: document.getElementById('accessibleWc').textContent,
            accessibleLav: document.getElementById('accessibleLav').textContent
        },
        calculations: {
            totalDfu: document.getElementById('totalDfu').textContent,
            peakDemand: document.getElementById('peakDemand').textContent
        },
        costs: {
            fixture: document.getElementById('fixtureCost').textContent,
            roughin: document.getElementById('roughinCost').textContent,
            accessibility: document.getElementById('accessibilityCost').textContent,
            total: document.getElementById('totalCost').textContent
        }
    };

    const dataStr = JSON.stringify(results, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'plumbing_fixture_count_results.json';
    link.click();
}

function printReport() {
    window.print();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Set initial values
    document.getElementById('maleOccupants').value = 50;
    document.getElementById('femaleOccupants').value = 50;
    
    // Auto-update when inputs change
    document.getElementById('buildingType').addEventListener('change', updateFixtureUnits);
    document.getElementById('totalOccupants').addEventListener('input', updateFixtureUnits);
    document.getElementById('maleOccupants').addEventListener('input', updateFixtureUnits);
    document.getElementById('femaleOccupants').addEventListener('input', updateFixtureUnits);
    document.getElementById('codeStandard').addEventListener('change', updateFixtureUnits);
    document.getElementById('disabledRequired').addEventListener('change', updateFixtureUnits);
});
</script>

<?php include '../../../themes/default/views/partials/footer.php'; ?>


