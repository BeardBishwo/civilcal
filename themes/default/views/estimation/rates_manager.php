<?php
// themes/default/views/estimation/rates_manager.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>District Rate Manager</title>
   
    <!-- Dependencies -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- JSpreadsheet/JSuites -->
    <script src="<?php echo app_base_url('/public/assets/vendor/jsuites.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo app_base_url('/public/assets/vendor/jsuites.min.css'); ?>" type="text/css" />
    <script src="<?php echo app_base_url('/public/assets/vendor/jspreadsheet.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo app_base_url('/public/assets/vendor/jspreadsheet.min.css'); ?>" type="text/css" />

    <style>
        body { background: #f8fafc; font-family: 'Inter', sans-serif; display: flex; flex-direction: column; height: 100vh; overflow: hidden; margin: 0; }
        .header { background: #fff; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .grid-container { flex: 1; margin: 20px; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; }
        .jexcel_container { width: 100% !important; flex: 1; overflow: auto; }
        .filters { display: flex; gap: 10px; align-items: center; }
        .loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 999; display: flex; justify-content: center; align-items: center; visibility: hidden; }
        .loading-overlay.active { visibility: visible; }
    </style>
</head>
<body>

    <div class="header">
        <div class="d-flex align-items-center gap-3">
             <a href="<?php echo app_base_url('/dashboard'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
             <h5 class="m-0 fw-bold">District Rate Manager</h5>
        </div>
        
        <div class="filters">
            <!-- Location Chain -->
            <select id="prov" class="form-select form-select-sm" style="width: 150px" onchange="loadDistricts()">
                <option value="">Province</option>
            </select>
            <select id="dist" class="form-select form-select-sm" style="width: 150px" onchange="loadMunis()">
                <option value="">District</option>
            </select>
            <select id="muni" class="form-select form-select-sm" style="width: 200px" onchange="loadRates()">
                <option value="">Local Body</option>
            </select>
            <button class="btn btn-primary btn-sm" onclick="saveRates()"><i class="bi bi-save"></i> Save Rates</button>
        </div>
    </div>

    <div class="grid-container">
        <div id="spreadsheet"></div>
    </div>
    
    <div class="loading-overlay" id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <script>
        const appBase = "<?php echo rtrim(app_base_url(), '/'); ?>";
        let nepalLocations = {};
        let grid;
        let currentLocationId = null;

        document.addEventListener('DOMContentLoaded', async function() {
            // Load Locations
            try {
                const res = await fetch(appBase + '/public/assets/data/english_locations.json');
                nepalLocations = await res.json();
                populateProvinces();
            } catch(e) { alert('Failed to load locations'); }
            
            // Init Grid
            grid = jspreadsheet(document.getElementById('spreadsheet'), {
                data: [],
                minDimensions: [10, 20],
                search: true,
                pagination: 20,
                tableOverflow: true,
                tableWidth: '100%',
                columns: [
                    { type: 'text', title: 'Code', width: 80, readOnly: true },
                    { type: 'text', title: 'Item Description', width: 400, readOnly: true },
                    { type: 'text', title: 'Unit', width: 60, readOnly: true },
                    { type: 'numeric', title: 'Current Rate (Rs.)', width: 120, mask: 'Rs #,##.00' },
                    { type: 'hidden', title: 'RateID', width: 0 }
                ]
            });
        });

        // --- Location Logic ---
        function populateProvinces() {
            const pSelect = document.getElementById('prov');
            pSelect.innerHTML = '<option value="">Select Province</option>';
            Object.keys(nepalLocations).forEach(p => pSelect.innerHTML += `<option value="${p}">${p}</option>`);
        }
        function loadDistricts() {
            const p = document.getElementById('prov').value;
            const dSelect = document.getElementById('dist');
            dSelect.innerHTML = '<option value="">Select District</option>';
            if(p) Object.keys(nepalLocations[p]).forEach(d => dSelect.innerHTML += `<option value="${d}">${d}</option>`);
        }
        function loadMunis() {
            const p = document.getElementById('prov').value;
            const d = document.getElementById('dist').value;
            const mSelect = document.getElementById('muni');
            mSelect.innerHTML = '<option value="">Select Local Body</option>';
            if(p && d) Object.keys(nepalLocations[p][d]).forEach(m => mSelect.innerHTML += `<option value="${m}">${m}</option>`);
        }

        // --- Rates Logic ---
        async function loadRates() {
            const m = document.getElementById('muni').value;
            const d = document.getElementById('dist').value;
            
            if (!m) return;
            
            showLoader(true);
            
            // Resolve ID on backend (simple fetch trick)
            // Ideally we'd have an API to get ID from name, but here we can rely on our knowledge
            // Actually, we need the location_id to fetch rates.
            // Let's first search for it.
            
            try {
                // Quick hack: Use a helper API or search via existing update_location logic?
                // Let's just fetch rates with names and let backend resolve?
                // No, existing API get_location_rates uses ID.
                // We need to resolve ID first.
                
                // Let's call the same 'update_location' endpoint but just to resolve ID?
                // Or build a resolver endpoint? 
                // Let's build a dedicated resolver later.
                // For now, I'll add text-based searching to get_location_rates.
                
                // Wait, I can't modify backend easily now inside this JS block.
                // I will add a frontend helper to find the ID if I had the full list of locations with IDs.
                // But nepal_locations.json doesn't have IDs.
                
                // Solution: Pass names to get_location_rates and let it resolve.
                
                const url = `${appBase}/estimation/api/get_location_rates?muni=${encodeURIComponent(m)}&district=${encodeURIComponent(d)}`;
                const res = await fetch(url);
                const json = await res.json();
                
                if (json.success) {
                    currentLocationId = json.location_id; // API should return this
                    const rows = json.data.map(i => [
                        i.dudbc_code,
                        i.item_name,
                        i.unit,
                        i.rate,
                        i.rate_id
                    ]);
                    grid.setData(rows);
                }
            } catch(e) { console.error(e); }
            
            showLoader(false);
        }

        async function saveRates() {
            if (!currentLocationId) return alert('Select a location first');
            
            const data = grid.getData();
            const rates = data.map(row => ({
                dudbc_code: row[0],
                rate: parseFloat(row[3]) || 0
            }));
            
            showLoader(true);
            await fetch(appBase + '/estimation/api/save_bulk_rates', {
                method: 'POST',
                body: JSON.stringify({ location_id: currentLocationId, rates: rates })
            });
            showLoader(false);
            alert('Rates Saved Successfully!');
        }

        function showLoader(show) {
            document.getElementById('loader').classList.toggle('active', show);
        }
    </script>
</body>
</html>
