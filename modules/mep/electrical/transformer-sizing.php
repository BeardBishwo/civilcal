<?php
require_once '../../../../includes/config.php';
require_once '../../../../includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Transformer Sizing Calculator</h1>
        <button class="btn btn-primary btn-sm" id="calculateBtn">
            <i class="fas fa-calculator fa-sm"></i> Calculate
        </button>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Input Parameters</h6>
                </div>
                <div class="card-body">
                    <form id="transformerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Total Connected Load (kVA)</label>
                                    <input type="number" class="form-control" id="connectedLoad" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Diversity Factor</label>
                                    <input type="number" class="form-control" id="diversityFactor" value="0.8" step="0.1" min="0" max="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Power Factor</label>
                                    <input type="number" class="form-control" id="powerFactor" value="0.9" step="0.1" min="0" max="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Future Expansion Factor</label>
                                    <input type="number" class="form-control" id="expansionFactor" value="1.2" step="0.1" min="1" required>
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Results</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Required Transformer Size</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="requiredSize">-- kVA</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bolt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Recommended Standard Size</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="standardSize">-- kVA</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="font-weight-bold">Calculation Details</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td>Maximum Demand</td>
                                            <td id="maxDemand">-- kVA</td>
                                        </tr>
                                        <tr>
                                            <td>Actual Power</td>
                                            <td id="actualPower">-- kW</td>
                                        </tr>
                                        <tr>
                                            <td>Future Load</td>
                                            <td id="futureLoad">-- kVA</td>
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
document.getElementById('calculateBtn').addEventListener('click', function() {
    const connectedLoad = parseFloat(document.getElementById('connectedLoad').value);
    const diversityFactor = parseFloat(document.getElementById('diversityFactor').value);
    const powerFactor = parseFloat(document.getElementById('powerFactor').value);
    const expansionFactor = parseFloat(document.getElementById('expansionFactor').value);

    // Calculate maximum demand
    const maxDemand = connectedLoad * diversityFactor;
    
    // Calculate actual power
    const actualPower = maxDemand * powerFactor;
    
    // Calculate future load with expansion factor
    const futureLoad = maxDemand * expansionFactor;
    
    // Calculate required transformer size
    const requiredSize = futureLoad / powerFactor;
    
    // Determine standard transformer size
    const standardSizes = [25, 50, 100, 160, 200, 250, 315, 400, 500, 630, 800, 1000, 1250, 1600, 2000];
    const standardSize = standardSizes.find(size => size >= requiredSize) || 'Custom';

    // Update results
    document.getElementById('maxDemand').textContent = maxDemand.toFixed(2) + ' kVA';
    document.getElementById('actualPower').textContent = actualPower.toFixed(2) + ' kW';
    document.getElementById('futureLoad').textContent = futureLoad.toFixed(2) + ' kVA';
    document.getElementById('requiredSize').textContent = requiredSize.toFixed(2) + ' kVA';
    document.getElementById('standardSize').textContent = standardSize + ' kVA';
});
</script>

<?php require_once '../../../../includes/footer.php'; ?>