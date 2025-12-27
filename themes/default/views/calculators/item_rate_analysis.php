<?php
$page_title = $title ?? 'Item Rate Analysis';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 0; }
        .calculator-card { background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 1200px; margin: 0 auto; }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0; }
        .breakdown-table { font-size: 0.9rem; }
        .breakdown-table th { background: #f8f9fa; font-weight: 600; }
        .result-card { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 12px; padding: 20px; margin-top: 20px; }
        .result-value { font-size: 2rem; font-weight: 700; }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-card">
            <div class="card-header">
                <h2 class="mb-0"><i class="bi bi-calculator-fill me-2"></i><?php echo $page_title; ?></h2>
                <p class="mb-0 mt-2 opacity-75">Calculate construction item rates using DUDBC norms</p>
            </div>
            <div class="card-body p-4">
                <!-- Item Selection -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Select Work Category</label>
                        <select id="category" class="form-select" onchange="loadItems()">
                            <option value="">-- Select Category --</option>
                            <?php foreach ($norms as $key => $cat): ?>
                                <option value="<?php echo $key; ?>"><?php echo ucfirst($key); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Select Item</label>
                        <select id="item" class="form-select" onchange="loadNorm()">
                            <option value="">-- Select Item --</option>
                        </select>
                    </div>
                </div>

                <!-- Material Rates -->
                <div id="material-section" style="display:none;">
                    <h5 class="mb-3"><i class="bi bi-box-seam me-2 text-primary"></i>Material Rates</h5>
                    <div id="material-inputs" class="row g-3 mb-4"></div>
                </div>

                <!-- Labor Rates -->
                <div id="labor-section" style="display:none;">
                    <h5 class="mb-3"><i class="bi bi-people-fill me-2 text-success"></i>Labor Rates</h5>
                    <div id="labor-inputs" class="row g-3 mb-4"></div>
                </div>

                <!-- Overhead -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Overhead & Profit (Rs.)</label>
                        <input type="number" id="overhead" class="form-control" value="0" step="0.01">
                    </div>
                </div>

                <!-- Calculate Button -->
                <button class="btn btn-primary btn-lg px-5" onclick="calculateRate()">
                    <i class="bi bi-calculator me-2"></i>Calculate Rate
                </button>

                <!-- Results -->
                <div id="results" style="display:none;">
                    <div class="result-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-2">Total Rate per Unit</h4>
                                <div class="result-value" id="total-rate">Rs. 0.00</div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-light btn-sm" onclick="saveToLocation()">
                                    <i class="bi bi-save me-1"></i>Save to Location
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Breakdown -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Material Breakdown</h6>
                            <table class="table table-sm breakdown-table">
                                <thead><tr><th>Material</th><th>Coeff</th><th>Rate</th><th>Cost</th></tr></thead>
                                <tbody id="material-breakdown"></tbody>
                                <tfoot><tr><th colspan="3">Subtotal</th><th id="material-total">0.00</th></tr></tfoot>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Labor Breakdown</h6>
                            <table class="table table-sm breakdown-table">
                                <thead><tr><th>Labor</th><th>Coeff</th><th>Rate</th><th>Cost</th></tr></thead>
                                <tbody id="labor-breakdown"></tbody>
                                <tfoot><tr><th colspan="3">Subtotal</th><th id="labor-total">0.00</th></tr></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const appBase = "<?php echo rtrim(app_base_url(), '/'); ?>";
        const norms = <?php echo json_encode($norms); ?>;
        let currentNorm = null;

        function loadItems() {
            const category = document.getElementById('category').value;
            const itemSelect = document.getElementById('item');
            itemSelect.innerHTML = '<option value="">-- Select Item --</option>';
            
            if (category && norms[category]) {
                Object.keys(norms[category]).forEach(key => {
                    const option = document.createElement('option');
                    option.value = key;
                    option.textContent = norms[category][key].name;
                    itemSelect.appendChild(option);
                });
            }
            
            // Hide sections
            document.getElementById('material-section').style.display = 'none';
            document.getElementById('labor-section').style.display = 'none';
            document.getElementById('results').style.display = 'none';
        }

        function loadNorm() {
            const category = document.getElementById('category').value;
            const item = document.getElementById('item').value;
            
            if (!category || !item) return;
            
            currentNorm = norms[category][item];
            
            // Load material inputs
            if (currentNorm.materials) {
                const container = document.getElementById('material-inputs');
                container.innerHTML = '';
                Object.keys(currentNorm.materials).forEach(mat => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4';
                    col.innerHTML = `
                        <label class="form-label">${mat.replace(/_/g, ' ').toUpperCase()} (Coeff: ${currentNorm.materials[mat]})</label>
                        <input type="number" class="form-control material-rate" data-mat="${mat}" placeholder="Rate per unit" step="0.01">
                    `;
                    container.appendChild(col);
                });
                document.getElementById('material-section').style.display = 'block';
            }
            
            // Load labor inputs
            if (currentNorm.labor) {
                const container = document.getElementById('labor-inputs');
                container.innerHTML = '';
                Object.keys(currentNorm.labor).forEach(lab => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4';
                    col.innerHTML = `
                        <label class="form-label">${lab.replace(/_/g, ' ').toUpperCase()} (Coeff: ${currentNorm.labor[lab]})</label>
                        <input type="number" class="form-control labor-rate" data-lab="${lab}" placeholder="Daily wage" step="0.01">
                    `;
                    container.appendChild(col);
                });
                document.getElementById('labor-section').style.display = 'block';
            }
        }

        async function calculateRate() {
            const category = document.getElementById('category').value;
            const item = document.getElementById('item').value;
            
            if (!category || !item) {
                alert('Please select category and item');
                return;
            }
            
            // Collect material rates
            const materialRates = {};
            document.querySelectorAll('.material-rate').forEach(input => {
                materialRates[input.dataset.mat] = parseFloat(input.value) || 0;
            });
            
            // Collect labor rates
            const laborRates = {};
            document.querySelectorAll('.labor-rate').forEach(input => {
                laborRates[input.dataset.lab] = parseFloat(input.value) || 0;
            });
            
            const overhead = parseFloat(document.getElementById('overhead').value) || 0;
            
            const res = await fetch(appBase + '/rate-analysis/calculate', {
                method: 'POST',
                body: JSON.stringify({
                    norm_key: category + '.' + item,
                    material_rates: materialRates,
                    labor_rates: laborRates,
                    overhead: overhead
                })
            });
            
            const json = await res.json();
            
            if (json.success) {
                // Display results
                document.getElementById('total-rate').textContent = 'Rs. ' + json.total_rate.toFixed(2);
                document.getElementById('material-total').textContent = json.material_cost.toFixed(2);
                document.getElementById('labor-total').textContent = json.labor_cost.toFixed(2);
                
                // Material breakdown
                const matBody = document.getElementById('material-breakdown');
                matBody.innerHTML = '';
                json.material_breakdown.forEach(m => {
                    matBody.innerHTML += `<tr>
                        <td>${m.name}</td>
                        <td>${m.coefficient}</td>
                        <td>${m.rate.toFixed(2)}</td>
                        <td>${m.cost.toFixed(2)}</td>
                    </tr>`;
                });
                
                // Labor breakdown
                const labBody = document.getElementById('labor-breakdown');
                labBody.innerHTML = '';
                json.labor_breakdown.forEach(l => {
                    labBody.innerHTML += `<tr>
                        <td>${l.name}</td>
                        <td>${l.coefficient}</td>
                        <td>${l.rate.toFixed(2)}</td>
                        <td>${l.cost.toFixed(2)}</td>
                    </tr>`;
                });
                
                document.getElementById('results').style.display = 'block';
            }
        }

        function saveToLocation() {
            alert('Save to Location feature coming soon! This will link to the Rate Manager.');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
