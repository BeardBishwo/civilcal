<?php
$base = defined('APP_BASE') ? rtrim(APP_BASE, '/') : '/aec-calculator';
require_once rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . $base . '/modules/mep/bootstrap.php';
include AEC_ROOT . '/themes/default/views/partials/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Conduit Sizing Calculator</h1>
        <button class="btn btn-primary btn-sm" id="calculateBtn">
            <i class="fas fa-calculator fa-sm"></i> Calculate
        </button>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cable Configuration</h6>
                </div>
                <div class="card-body">
                    <form id="conduitForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cable Type</label>
                                    <select class="form-control" id="cableType">
                                        <option value="THHN">THHN</option>
                                        <option value="XHHW">XHHW</option>
                                        <option value="THW">THW</option>
                                        <option value="TW">TW</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Number of Conductors</label>
                                    <input type="number" class="form-control" id="conductorCount" min="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Wire Size (AWG/kcmil)</label>
                                    <select class="form-control" id="wireSize">
                                        <option value="14">14 AWG</option>
                                        <option value="12">12 AWG</option>
                                        <option value="10">10 AWG</option>
                                        <option value="8">8 AWG</option>
                                        <option value="6">6 AWG</option>
                                        <option value="4">4 AWG</option>
                                        <option value="2">2 AWG</option>
                                        <option value="1">1 AWG</option>
                                        <option value="1/0">1/0</option>
                                        <option value="2/0">2/0</option>
                                        <option value="3/0">3/0</option>
                                        <option value="4/0">4/0</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Conduit Type</label>
                                    <select class="form-control" id="conduitType">
                                        <option value="EMT">EMT</option>
                                        <option value="IMC">IMC</option>
                                        <option value="RMC">RMC</option>
                                        <option value="PVC">PVC Schedule 40</option>
                                        <option value="PVC80">PVC Schedule 80</option>
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Results</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Minimum Conduit Size</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="minSize">-- inches</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-ruler fa-2x text-gray-300"></i>
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
                                                Trade Size</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="tradeSize">--</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tape fa-2x text-gray-300"></i>
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
                                                Fill Percentage</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="fillPercentage">--%</div>
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
                                            <th>Detail</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Total Cable Area</td>
                                            <td id="totalCableArea">-- sq.in</td>
                                        </tr>
                                        <tr>
                                            <td>Required Conduit Area</td>
                                            <td id="requiredArea">-- sq.in</td>
                                        </tr>
                                        <tr>
                                            <td>Maximum Fill Allowed</td>
                                            <td id="maxFill">-- %</td>
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
</div>

<script>
// Wire size to area lookup (sq. inches)
const wireAreas = {
    '14': 0.0097,
    '12': 0.0133,
    '10': 0.0211,
    '8': 0.0366,
    '6': 0.0507,
    '4': 0.0817,
    '2': 0.1325,
    '1': 0.1666,
    '1/0': 0.2133,
    '2/0': 0.2666,
    '3/0': 0.3333,
    '4/0': 0.4166
};

// Standard conduit sizes (trade size to actual ID in inches)
const conduitSizes = {
    '1/2': 0.622,
    '3/4': 0.824,
    '1': 1.049,
    '1-1/4': 1.380,
    '1-1/2': 1.610,
    '2': 2.067,
    '2-1/2': 2.469,
    '3': 3.068,
    '3-1/2': 3.548,
    '4': 4.026
};

document.getElementById('calculateBtn').addEventListener('click', function() {
    const wireSize = document.getElementById('wireSize').value;
    const conductorCount = parseInt(document.getElementById('conductorCount').value);
    
    // Calculate total cable area
    const singleWireArea = wireAreas[wireSize];
    const totalCableArea = singleWireArea * conductorCount;
    
    // Determine maximum fill percentage based on conductor count
    let maxFill = 0;
    if (conductorCount <= 2) maxFill = 31;
    else if (conductorCount <= 3) maxFill = 40;
    else maxFill = 40;
    
    // Calculate required conduit area
    const requiredArea = (totalCableArea * 100) / maxFill;
    
    // Find minimum conduit size
    let selectedSize = '';
    let actualFill = 100;
    
    for (const [size, id] of Object.entries(conduitSizes)) {
        const area = Math.PI * Math.pow(id/2, 2);
        const fillPercent = (totalCableArea / area) * 100;
        
        if (fillPercent <= maxFill) {
            selectedSize = size;
            actualFill = fillPercent;
            break;
        }
    }
    
    // Update results
    document.getElementById('minSize').textContent = selectedSize + ' inches';
    document.getElementById('tradeSize').textContent = selectedSize;
    document.getElementById('fillPercentage').textContent = actualFill.toFixed(1) + '%';
    document.getElementById('totalCableArea').textContent = totalCableArea.toFixed(4) + ' sq.in';
    document.getElementById('requiredArea').textContent = requiredArea.toFixed(4) + ' sq.in';
    document.getElementById('maxFill').textContent = maxFill + '%';
});
</script>

<?php include AEC_ROOT . '/themes/default/views/partials/footer.php'; ?>
