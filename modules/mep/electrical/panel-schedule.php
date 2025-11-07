<?php
require_once '../../../includes/config.php';
require_once '../../../includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Panel Schedule Generator</h1>
        <button class="btn btn-primary btn-sm" id="generateBtn">
            <i class="fas fa-file-pdf fa-sm"></i> Generate PDF
        </button>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Panel Information</h6>
                </div>
                <div class="card-body">
                    <form id="panelForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Panel Name</label>
                                    <input type="text" class="form-control" id="panelName" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Location</label>
                                    <input type="text" class="form-control" id="location" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Voltage</label>
                                    <select class="form-control" id="voltage">
                                        <option value="208/120">208/120V</option>
                                        <option value="240/120">240/120V</option>
                                        <option value="480/277">480/277V</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Phases</label>
                                    <select class="form-control" id="phases">
                                        <option value="1">Single Phase</option>
                                        <option value="3">Three Phase</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Main Breaker Size (A)</label>
                                    <input type="number" class="form-control" id="mainBreaker" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Bus Rating (A)</label>
                                    <input type="number" class="form-control" id="busRating" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>AIC Rating (kA)</label>
                                    <input type="number" class="form-control" id="aicRating" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Mounting</label>
                                    <select class="form-control" id="mounting">
                                        <option value="Surface">Surface</option>
                                        <option value="Flush">Flush</option>
                                        <option value="Free Standing">Free Standing</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Circuit Schedule</h6>
                    <button class="btn btn-success btn-sm" id="addCircuitBtn">
                        <i class="fas fa-plus fa-sm"></i> Add Circuit
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="circuitTable">
                            <thead>
                                <tr>
                                    <th>Circuit #</th>
                                    <th>Description</th>
                                    <th>Load Type</th>
                                    <th>Breaker Size (A)</th>
                                    <th>Poles</th>
                                    <th>Load (VA)</th>
                                    <th>Phase</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Circuits will be added here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Load Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Connected Load</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalLoad">0 VA</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bolt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Phase Balance</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="phaseBalance">Balanced</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Panel Capacity Used</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="capacityUsed">0%</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Phase</th>
                                            <th>Connected Load (VA)</th>
                                            <th>Current (A)</th>
                                            <th>% Load</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Phase A</td>
                                            <td id="phaseALoad">0</td>
                                            <td id="phaseACurrent">0</td>
                                            <td id="phaseAPercent">0%</td>
                                        </tr>
                                        <tr>
                                            <td>Phase B</td>
                                            <td id="phaseBLoad">0</td>
                                            <td id="phaseBCurrent">0</td>
                                            <td id="phaseBPercent">0%</td>
                                        </tr>
                                        <tr>
                                            <td>Phase C</td>
                                            <td id="phaseCLoad">0</td>
                                            <td id="phaseCCurrent">0</td>
                                            <td id="phaseCPercent">0%</td>
                                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

<script>
let circuits = [];
let nextCircuitId = 1;

document.getElementById('addCircuitBtn').addEventListener('click', function() {
    const circuitHtml = `
        <tr data-circuit-id="${nextCircuitId}">
            <td>${nextCircuitId}</td>
            <td><input type="text" class="form-control" required></td>
            <td>
                <select class="form-control">
                    <option value="Lighting">Lighting</option>
                    <option value="Receptacle">Receptacle</option>
                    <option value="HVAC">HVAC</option>
                    <option value="Equipment">Equipment</option>
                </select>
            </td>
            <td><input type="number" class="form-control" required></td>
            <td>
                <select class="form-control">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </td>
            <td><input type="number" class="form-control" required></td>
            <td>
                <select class="form-control">
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </td>
            <td>
                <button class="btn btn-danger btn-sm deleteCircuit">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    document.querySelector('#circuitTable tbody').insertAdjacentHTML('beforeend', circuitHtml);
    nextCircuitId++;
    updateLoadSummary();
});

document.querySelector('#circuitTable').addEventListener('click', function(e) {
    if (e.target.classList.contains('deleteCircuit') || e.target.parentElement.classList.contains('deleteCircuit')) {
        const row = e.target.closest('tr');
        row.remove();
        updateLoadSummary();
    }
});

document.querySelector('#circuitTable').addEventListener('change', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') {
        updateLoadSummary();
    }
});

function updateLoadSummary() {
    let phaseLoads = { A: 0, B: 0, C: 0 };
    let totalLoad = 0;
    
    document.querySelectorAll('#circuitTable tbody tr').forEach(row => {
        const load = parseFloat(row.querySelector('input[type="number"]:last-of-type').value) || 0;
        const phase = row.querySelector('select:last-of-type').value;
        
        phaseLoads[phase] += load;
        totalLoad += load;
    });
    
    // Update phase loads
    for (let phase in phaseLoads) {
        document.getElementById(`phase${phase}Load`).textContent = phaseLoads[phase].toFixed(0);
        const voltage = parseInt(document.getElementById('voltage').value.split('/')[0]);
        const current = phaseLoads[phase] / voltage;
        document.getElementById(`phase${phase}Current`).textContent = current.toFixed(2);
        
        const busRating = parseFloat(document.getElementById('busRating').value) || 1;
        const percent = (current / busRating) * 100;
        document.getElementById(`phase${phase}Percent`).textContent = percent.toFixed(1) + '%';
    }
    
    // Update total load
    document.getElementById('totalLoad').textContent = totalLoad.toFixed(0) + ' VA';
    
    // Check phase balance
    const maxPhase = Math.max(...Object.values(phaseLoads));
    const minPhase = Math.min(...Object.values(phaseLoads));
    const imbalance = ((maxPhase - minPhase) / maxPhase) * 100;
    
    document.getElementById('phaseBalance').textContent = 
        imbalance <= 20 ? 'Balanced' : 'Unbalanced';
    document.getElementById('phaseBalance').style.color = 
        imbalance <= 20 ? 'green' : 'red';
        
    // Update capacity used
    const mainBreaker = parseFloat(document.getElementById('mainBreaker').value) || 1;
    const voltage = parseInt(document.getElementById('voltage').value.split('/')[0]);
    const capacityUsed = (totalLoad / (mainBreaker * voltage * Math.sqrt(3))) * 100;
    document.getElementById('capacityUsed').textContent = capacityUsed.toFixed(1) + '%';
}
</script>

<?php require_once '../../../includes/footer.php'; ?>
