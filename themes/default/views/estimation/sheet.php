<?php
// themes/default/views/estimation/sheet.php
// Enterprise JSpreadsheet Workbook (High-Performance Pivot)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <!-- Dependencies -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Local Fast Assets -->
    <script src="<?php echo app_base_url('/public/assets/vendor/jsuites.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo app_base_url('/public/assets/vendor/jsuites.min.css'); ?>" type="text/css" />
    <script src="<?php echo app_base_url('/public/assets/vendor/jspreadsheet.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo app_base_url('/public/assets/vendor/jspreadsheet.min.css'); ?>" type="text/css" />

    <style>
        :root {
            --primary-dark: #0f172a;
            --accent-blue: #3b82f6;
            --tab-active-bg: #fff;
            --tab-inactive-bg: #e2e8f0;
            --border-color: #cbd5e1;
        }
        body {
            margin: 0; padding: 0;
            width: 100%; height: 100vh;
            display: flex; flex-direction: column;
            overflow: hidden;
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
        }

        /* Header */
        .sheet-header {
            background: var(--primary-dark);
            color: white; height: 50px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 20px; z-index: 10;
        }

        /* Tabs Container */
        .workbook-tabs {
            background: #f1f5f9;
            border-bottom: 1px solid var(--border-color);
            display: flex; gap: 2px; padding: 4px 10px 0;
            user-select: none;
        }
        .wb-tab {
            padding: 8px 16px;
            background: var(--tab-inactive-bg);
            border: 1px solid var(--border-color);
            border-bottom: none;
            border-radius: 6px 6px 0 0;
            cursor: pointer;
            font-size: 0.85rem; font-weight: 500; color: #64748b;
            transition: all 0.1s ease;
        }
        .wb-tab.active {
            background: var(--tab-active-bg);
            color: var(--primary-dark);
            font-weight: 600;
            border-top: 3px solid var(--accent-blue);
            padding-top: 5px; /* Adjust for border */
        }
        .wb-tab:hover:not(.active) {
            background: #e9eff5;
        }

        /* Sheets Container */
        #sheets-viewport {
            flex: 1;
            position: relative;
            background: #fff;
            overflow: hidden;
        }
        .sheet-instance {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            display: none; /* Hidden by default */
            flex-direction: column;
        }
        .sheet-instance.active {
            display: flex;
        }
        
        /* JSpreadsheet Overrides for Full Height and Visibility */
        .jexcel_container { 
            width: 100% !important; 
            flex: 1; 
            overflow: auto;
            visibility: visible !important; /* Fail-safe */
        }
        .jexcel_content { 
            width: 100% !important; 
            min-height: 100% !important;
            padding-bottom: 50px; /* Scroll space */
        }
        .jexcel { 
            width: 100% !important;
            border-collapse: collapse;
        }

        /* Ensure Headers are Visible */
        .jexcel > thead > tr > td {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 600 !important;
        }

        /* Loader */
        #app-loader {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: white; z-index: 9999;
            display: flex; justify-content: center; align-items: center;
            opacity: 1; transition: opacity 0.3s;
        }

        /* Item Picker Modal */
        #item-picker-modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); z-index: 2000;
            display: none; justify-content: center; align-items: center;
            backdrop-filter: blur(2px);
        }
        .picker-content {
            background: white; width: 600px; max-height: 80vh;
            border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            display: flex; flex-direction: column; overflow: hidden;
            animation: modalSlide 0.2s ease-out;
        }
        @keyframes modalSlide { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .picker-header { padding: 16px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .picker-search { padding: 12px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
        .picker-list { flex: 1; overflow-y: auto; padding: 0; margin: 0; list-style: none; }
        
        .picker-item {
            padding: 12px 16px; border-bottom: 1px solid #f1f5f9; cursor: pointer;
            transition: background 0.1s; display: flex; justify-content: space-between; align-items: center;
        }
        .picker-item:hover { background: #f0f9ff; }
        .picker-item .code { font-size: 0.75rem; font-weight: 700; color: #64748b; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; margin-right: 10px; }
        .picker-item .desc { font-weight: 500; color: #334155; flex: 1; }
        .picker-item .unit { font-size: 0.85rem; color: #94a3b8; font-weight: 600; }
    </style>
</head>
<body>

    <div id="app-loader">
        <div class="text-center">
            <div class="spinner-border text-primary mb-2" role="status"></div>
            <div class="small fw-bold text-secondary">Loading Workbook...</div>
        </div>
    </div>

    <div class="sheet-header">
        <div class="d-flex align-items-center gap-2">
             <a href="<?php echo app_base_url('/dashboard'); ?>" class="text-white opacity-75 hover-opacity"><i class="bi bi-arrow-left"></i></a>
             <span class="fw-bold ms-2"><?php echo $project['name']; ?></span>
             <button class="btn btn-sm btn-outline-light border-0 ms-2" onclick="openLocationPicker()" title="Project Location">
                <i class="bi bi-geo-alt-fill text-warning"></i> <span id="location-label" class="small"><?php echo $project['location'] ?: 'Set Location'; ?></span>
             </button>
        </div>
        <div>
            <span id="save-status" class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-3 me-2">
                <i class="bi bi-cloud-check-fill"></i> Saved
            </span>
            <button class="btn btn-sm btn-success py-1 me-1" onclick="openImportModal()"><i class="bi bi-upload"></i> Import</button>
            <button class="btn btn-sm btn-light py-1 me-1" onclick="exportExcel()"><i class="bi bi-download"></i> Excel</button>
            <button class="btn btn-sm btn-danger py-1 me-1" onclick="exportPdf()"><i class="bi bi-file-pdf"></i> PDF</button>
            <button class="btn btn-sm btn-info py-1 me-1" onclick="openTemplateModal()"><i class="bi bi-folder-plus"></i> Templates</button>
            <button class="btn btn-sm btn-warning py-1" onclick="openHistoryModal()"><i class="bi bi-clock-history"></i> History</button>
        </div>
    </div>

    <!-- Tab Bar -->
    <div class="workbook-tabs">
        <div class="wb-tab active" onclick="switchTab('mb')">
            <i class="bi bi-ruler me-1 text-primary"></i> Measurement Book
        </div>
        <div class="wb-tab" onclick="switchTab('abstract')">
            <i class="bi bi-cash-stack me-1 text-success"></i> Abstract of Cost
        </div>
        <div class="wb-tab" onclick="switchTab('rate')">
            <i class="bi bi-calculator me-1 text-warning"></i> Rate Analysis
        </div>
    </div>

    <!-- Sheets Viewport -->
    <div id="sheets-viewport">
        <!-- Instance A: Measurement Book -->
        <div id="sheet-mb" class="sheet-instance active">
            <div id="grid-mb"></div>
        </div>
        
        <!-- Instance B: Abstract -->
        <div id="sheet-abstract" class="sheet-instance">
            <div id="grid-abstract"></div>
        </div>

        <!-- Instance C: Rate Analysis -->
        <div id="sheet-rate" class="sheet-instance">
            <div id="grid-rate"></div>
        </div>
    </div>

    <!-- Project Location Modal -->
    <div id="location-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-geo-alt-fill text-warning me-2"></i>Project Location</h5>
                    <div class="ms-auto" id="loc-lang-toggle">
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="loc-lang" id="lang-en" value="en" autocomplete="off" checked onchange="switchLocationLang('en')">
                            <label class="btn btn-outline-secondary" for="lang-en">ENG</label>
                            
                            <input type="radio" class="btn-check" name="loc-lang" id="lang-np" value="np" autocomplete="off" onchange="switchLocationLang('np')">
                            <label class="btn btn-outline-secondary" for="lang-np">NEP</label>
                        </div>
                    </div>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Select the project site to load accurate district rates.</p>
                    <div class="mb-3">
                        <label class="form-label">Province</label>
                        <select id="loc-province" class="form-select" onchange="loadDistricts()"></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">District</label>
                        <select id="loc-district" class="form-select" onchange="loadMunis()"></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Local Body (Muncipality/VDC)</label>
                        <select id="loc-muni" class="form-select"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="saveLocation()">Save Location</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Picker Modal -->
    <div id="item-picker-modal">
        <div class="picker-content">
            <div class="picker-header">
                <h5 class="m-0 fw-bold text-dark"><i class="bi bi-list-check me-2 text-primary"></i>Select Item</h5>
                <button class="btn-close" onclick="closePicker()"></button>
            </div>
            <div class="picker-search">
                <input type="text" id="picker-search-input" class="form-control" placeholder="Search items (e.g. 'c-01' or 'concrete')..." onkeyup="filterItems()">
            </div>
            <ul class="picker-list" id="picker-list-container">
                <!-- Items injected here -->
                <li class="p-4 text-center text-muted">Loading items...</li>
            </ul>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div class="modal fade" id="import-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-upload text-success me-2"></i>Import Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Upload an Excel file (.xlsx) to replace current data.</p>
                    <input type="file" id="excel-upload" class="form-control" accept=".xlsx">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="importExcel()"><i class="bi bi-upload"></i> Upload</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Modal -->
    <div class="modal fade" id="template-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-folder-plus text-info me-2"></i>Project Templates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#load-tab">Load Template</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#save-tab">Save as Template</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- Load Template Tab -->
                        <div class="tab-pane fade show active" id="load-tab">
                            <div id="templates-list" class="list-group"></div>
                        </div>
                        <!-- Save Template Tab -->
                        <div class="tab-pane fade" id="save-tab">
                            <div class="mb-3">
                                <label class="form-label">Template Name</label>
                                <input type="text" id="template-name" class="form-control" placeholder="e.g., Residential Building BOQ">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description (Optional)</label>
                                <textarea id="template-desc" class="form-control" rows="3" placeholder="Brief description of this template"></textarea>
                            </div>
                            <button class="btn btn-primary" onclick="saveTemplate()"><i class="bi bi-save me-1"></i> Save Template</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Version History Modal -->
    <div class="modal fade" id="history-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-clock-history text-warning me-2"></i>Version History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Recent changes to this project (last 20 versions)</p>
                    <div id="versions-list" class="list-group"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const appBase = "<?php echo rtrim(app_base_url(), '/'); ?>";
        const projectId = <?php echo $project['id']; ?>;
        const initialData = <?php echo $gridData ? $gridData : 'null'; ?>;
        
        // Globals
        let gridMB, gridAbstract, gridRate;
        let masterItems = []; 
        let nepalLocations = {}; 
        let currentLang = 'en';
        let projectRates = {}; // Stores { "C-01": 850 }
        let activeCell = null;

        document.addEventListener('DOMContentLoaded', function() {
            fetchItems();
            fetchLocations('en');
            fetchProjectRates(); // Fetch dynamic rates
            initWorkbook();
        });

        // --- Logic Stub ---
        async function fetchProjectRates() {
             try {
                 // Fetch rates for this project
                 const res = await fetch(appBase + '/estimation/api/get_project_rates?project_id=' + projectId);
                 projectRates = await res.json();
                 console.log('Project Rates Loaded:', projectRates);
                 
                 // Optional: Auto-refresh Abstract if rates changed
                 refreshAbstractRates();
             } catch(e) { console.error('Failed to load rates', e); }
        }
        
        function refreshAbstractRates() {
             // Loop through Abstract grid and update rates if they match
             if (!gridAbstract) return;
             const data = gridAbstract.getData();
             data.forEach((row, index) => {
                 const code = row[0]; // Item Code
                 if (projectRates[code] !== undefined) {
                     // Update Rate (Col 4)
                     gridAbstract.setValueFromCoords(4, index, projectRates[code]);
                 }
             });
        }

        // --- Location Logic ---
        async function fetchLocations(lang) {
             currentLang = lang;
             const file = lang === 'en' ? 'english_locations.json' : 'nepali_locations.json';
             try {
                // Determine label for loading state
                const pSelect = document.getElementById('loc-province');
                if (pSelect) pSelect.innerHTML = '<option>Loading...</option>';
                
                const response = await fetch(appBase + '/public/assets/data/' + file);
                nepalLocations = await response.json();
                
                // Refresh UI if modal is open
                if (document.getElementById('location-modal').classList.contains('show')) {
                    populateProvinces();
                    // Reset others
                    document.getElementById('loc-district').innerHTML = '<option value="">Select District</option>';
                    document.getElementById('loc-muni').innerHTML = '<option value="">Select Local Body</option>';
                }
             } catch(e) { console.error('Failed to load locations', e); }
        }
        
        function switchLocationLang(lang) {
            fetchLocations(lang);
        }

        function openLocationPicker() {
            const modal = new bootstrap.Modal(document.getElementById('location-modal'));
            modal.show();
            populateProvinces();
        }
        
        function populateProvinces() {
            const pSelect = document.getElementById('loc-province');
            pSelect.innerHTML = '<option value="">Select Province</option>';
            Object.keys(nepalLocations).forEach(p => {
                pSelect.innerHTML += `<option value="${p}">${p}</option>`;
            });
        }

        function loadDistricts() {
            const p = document.getElementById('loc-province').value;
            const dSelect = document.getElementById('loc-district');
            const mSelect = document.getElementById('loc-muni');
            
            dSelect.innerHTML = '<option value="">Select District</option>';
            mSelect.innerHTML = '<option value="">Select Local Body</option>';
            
            if (p && nepalLocations[p]) {
                Object.keys(nepalLocations[p]).forEach(d => {
                    dSelect.innerHTML += `<option value="${d}">${d}</option>`;
                });
            }
        }

        function loadMunis() {
            const p = document.getElementById('loc-province').value;
            const d = document.getElementById('loc-district').value;
            const mSelect = document.getElementById('loc-muni');
            
            mSelect.innerHTML = '<option value="">Select Local Body</option>';
            
            if (p && d && nepalLocations[p][d]) {
                // Munis are keys in the third level
                Object.keys(nepalLocations[p][d]).forEach(m => {
                    mSelect.innerHTML += `<option value="${m}">${m}</option>`;
                });
            }
        }

        function saveLocation() {
            const m = document.getElementById('loc-muni').value;
            const d = document.getElementById('loc-district').value;
            const p = document.getElementById('loc-province').value;
            
            if (!m) return alert('Please select a local body');
            
            // Format: "Muni, District"
            const fullLocation = `${m}, ${d}`;
            
            fetch(appBase + '/estimation/api/update_location', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ project_id: projectId, location: fullLocation, province: p, district: d, muni: m })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('location-label').innerText = fullLocation;
                    const modal = bootstrap.Modal.getInstance(document.getElementById('location-modal'));
                    modal.hide();
                    alert('Location updated! Fetching new rates...');
                    fetchProjectRates(); // Reload rates for new location
                }
            });
        }

        // --- Item Picker Logic ---
        async function fetchItems() {
            try {
                const response = await fetch(appBase + '/estimation/api/items');
                if (!response.ok) throw new Error('Network error');
                const result = await response.json();
                if (result.success) {
                    masterItems = result.data;
                    renderPickerList(masterItems);
                }
            } catch (e) {
                console.error('Failed to load items', e);
            }
        }

        function renderPickerList(items) {
            const list = document.getElementById('picker-list-container');
            list.innerHTML = items.map(item => `
                <li class="picker-item" onclick="selectItem('${item.code}')">
                    <div class="d-flex align-items-center flex-grow-1">
                        <span class="code">${item.code}</span>
                        <span class="desc">${item.description}</span>
                    </div>
                    <span class="unit badge bg-light text-dark border">${item.unit}</span>
                </li>
            `).join('');
        }

        function filterItems() {
            const term = document.getElementById('picker-search-input').value.toLowerCase();
            const filtered = masterItems.filter(i => 
                i.code.toLowerCase().includes(term) || 
                i.description.toLowerCase().includes(term)
            );
            renderPickerList(filtered);
        }

        function openPicker(instance, cell, x, y, value) {
            activeCell = { instance, x, y };
            document.getElementById('item-picker-modal').style.display = 'flex';
            document.getElementById('picker-search-input').value = '';
            document.getElementById('picker-search-input').focus();
            renderPickerList(masterItems);
        }

        function closePicker() {
            document.getElementById('item-picker-modal').style.display = 'none';
            activeCell = null;
        }

        function selectItem(code) {
            const item = masterItems.find(i => i.code === code);
            if (activeCell && item) {
                const y = activeCell.y;
                // Set Description (Col 1)
                activeCell.instance.setValueFromCoords(1, y, item.description);
                // Set Hidden Item Code (Col 8)
                activeCell.instance.setValueFromCoords(8, y, item.code);
                
                closePicker();
                
                // Trigger Abstract Update instantly
                MBStateManager.syncToAbstract(item.code);
            }
        }

        // --- State Manager (The Nervous System) ---
        const MBStateManager = {
            syncToAbstract: function(itemCode) {
                if (!itemCode) return;
                
                // 1. Calculate Total Qty from MB for this Item Code
                // We interact with the instance safely to get calculated values
                const rowCount = gridMB.rows.length; // or headers.length? gridMB.attributes.data.length? 
                // Best way in v4: gridMB.getData().length
                const dataLength = gridMB.getData().length;
                
                let totalQty = 0;
                
                for (let i = 0; i < dataLength; i++) {
                    const code = gridMB.getValueFromCoords(8, i); // Col 8: Item Code
                    if (code === itemCode) {
                        // Col 6: Qty (Calculated)
                        const val = gridMB.getValueFromCoords(6, i);
                        const qty = parseFloat(val) || 0;
                        totalQty += qty;
                    }
                }
                
                // 2. Find and Update Abstract Row
                const abstractData = gridAbstract.getData();
                // Abstract Col 0 is Item Code
                const rowIndex = abstractData.findIndex(row => row[0] === itemCode);
                
                if (rowIndex !== -1) {
                    // Update Quantity (Col 3)
                    gridAbstract.setValueFromCoords(3, rowIndex, totalQty);
                    
                    // Inject Dynamic Rate if available/updated
                    if (projectRates[itemCode] !== undefined) {
                         gridAbstract.setValueFromCoords(4, rowIndex, projectRates[itemCode]);
                    }
                } else {
                     console.warn(`Item ${itemCode} not found in Abstract`);
                }
            }
        };

        function handleMBChange(instance, cell, x, y, value) {
            // Check if Qty column (index 6) or inputs (2-5) changed
            if ([2,3,4,5,6].includes(parseInt(x))) {
                 // Get the Item Code for this row (Col 8)
                 const itemCode = instance.getValueFromCoords(8, y);
                 if (itemCode) {
                     MBStateManager.syncToAbstract(itemCode);
                 }
            }
        }

        function initWorkbook() {
            // 1. Initialize Measurement Book
            gridMB = jspreadsheet(document.getElementById('grid-mb'), {
                data: initialData?.mb || getDefaultMB(),
                minDimensions: [10, 20],
                defaultColWidth: 100,
                tableOverflow: true,
                tableWidth: '100%',
                tableHeight: '100%',
                columns: [
                    { type: 'text', title: 'S.N.', width: 50 },
                    { type: 'text', title: 'Description of Work', width: 300, wordWrap: true, editor: ItemPickerEditor }, 
                    { type: 'numeric', title: 'No.', width: 60 },
                    { type: 'numeric', title: 'L (m)', width: 70 },
                    { type: 'numeric', title: 'B (m)', width: 70 },
                    { type: 'numeric', title: 'H (m)', width: 70 },
                    { type: 'numeric', title: 'Qty', width: 80, readOnly: true }, // Formula
                    { type: 'text', title: 'Remarks', width: 150 },
                    { type: 'hidden', title: 'ItemCode', width: 0 } // Col 8: Hidden
                ],
                onchange: function(instance, cell, x, y, value) {
                    handleMBChange(instance, cell, x, y, value);
                    triggerAutoSave();
                },
            });

            // 2. Initialize Abstract
            gridAbstract = jspreadsheet(document.getElementById('grid-abstract'), {
                data: initialData?.abstract || getDefaultAbstract(),
                minDimensions: [8, 20],
                defaultColWidth: 100,
                tableOverflow: true,
                tableWidth: '100%',
                tableHeight: '100%',
                columns: [
                    { type: 'text', title: 'Item Code', width: 80 },
                    { type: 'text', title: 'Description', width: 350 },
                    { type: 'text', title: 'Unit', width: 60 },
                    { type: 'numeric', title: 'Quantity', width: 100, readOnly: true }, // Linked
                    { type: 'numeric', title: 'Rate', width: 100 },
                    { type: 'numeric', title: 'Amount', width: 120, readOnly: true }
                ]
            });

            // 3. Initialize Rate Analysis
            gridRate = jspreadsheet(document.getElementById('grid-rate'), {
                data: [],
                minDimensions: [5, 20],
                tableOverflow: true,
                tableWidth: '100%',
                tableHeight: '100%',
                columns: [
                     { type: 'text', title: 'Resource', width: 200 },
                     { type: 'numeric', title: 'Norm Coeff', width: 100 },
                     { type: 'numeric', title: 'Market Rate', width: 100 }
                ]
            });

            // Remove Loader instantly
            document.getElementById('app-loader').style.opacity = 0;
            setTimeout(() => document.getElementById('app-loader').remove(), 300);
        }

        function switchTab(tabName) {
            // Update Tab UI
            document.querySelectorAll('.wb-tab').forEach(t => t.classList.remove('active'));
            document.querySelector(`.wb-tab[onclick="switchTab('${tabName}')"]`).classList.add('active');

            // Switch Visibility
            document.querySelectorAll('.sheet-instance').forEach(el => el.classList.remove('active'));
            document.getElementById(`sheet-${tabName}`).classList.add('active');
        }

        // --- Data Defaults ---
        function getDefaultMB() {
            // Added 9th column: Item Code (Hidden) linked to Abstract
            return [
                ['1', 'Earthwork in excavation', '1', '10', '5', '1.5', '=C1*D1*E1*F1', 'Foundation', 'C-01'], 
                ['2', 'PCC 1:3:6', '1', '10', '5', '0.1', '=C2*D2*E2*F2', 'Soling', 'C-02']
            ];
        }

        function getDefaultAbstract() {
            return [
                ['C-01', 'Earthwork in excavation', 'Cum', '75', '850', '=D1*E1'],
                ['C-02', 'PCC for Foundation', 'Cum', '5', '4500', '=D2*E2']
            ];
        }

        // --- Logic Stub ---
        function handleMBChange(instance, cell, x, y, value) {
            // Future: Sync Quantity to Abstract based on item mapping
            console.log('MB Changed', x, y, value);
        }

        let saveTimer;
        function triggerAutoSave() {
            const badge = document.getElementById('save-status');
            badge.className = 'badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-3 me-2';
            badge.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Saving...';
            
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => {
                // Mock Save
                const data = {
                    mb: gridMB.getData(),
                    abstract: gridAbstract.getData(),
                    rate: gridRate.getData()
                };
                
                fetch(appBase + '/estimation/api/save', {
                    method: 'POST',
                    body: JSON.stringify({ project_id: projectId, data: data })
                }).then(() => {
                    badge.className = 'badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-3 me-2';
                    badge.innerHTML = '<i class="bi bi-cloud-check-fill"></i> Saved';
                });
            }, 1000);
        }

        function exportExcel() {
            alert('High-Performance Excel Export: Coming in Phase 20');
        }
        }

        function exportExcel() {
            window.location.href = appBase + '/estimation/export/excel?project_id=' + projectId;
        }

        function exportPdf() {
            window.location.href = appBase + '/estimation/export/pdf?project_id=' + projectId;
        }

        function openImportModal() {
            const modal = new bootstrap.Modal(document.getElementById('import-modal'));
            modal.show();
        }

        async function importExcel() {
            const fileInput = document.getElementById('excel-upload');
            if (!fileInput.files.length) {
                alert('Please select a file');
                return;
            }

            const formData = new FormData();
            formData.append('excel_file', fileInput.files[0]);
            formData.append('project_id', projectId);

            try {
                const res = await fetch(appBase + '/estimation/import/excel', {
                    method: 'POST',
                    body: formData
                });
                const json = await res.json();
                
                if (json.success) {
                    alert('Import successful! Reloading...');
                    location.reload();
                } else {
                    alert('Import failed: ' + json.error);
                }
            } catch(e) {
                alert('Import error: ' + e.message);
            }
        }

        function openTemplateModal() {
            loadTemplatesList();
            const modal = new bootstrap.Modal(document.getElementById('template-modal'));
            modal.show();
        }

        async function loadTemplatesList() {
            try {
                const res = await fetch(appBase + '/estimation/api/get_templates');
                const json = await res.json();
                
                if (json.success) {
                    const list = document.getElementById('templates-list');
                    list.innerHTML = '';
                    
                    if (json.templates.length === 0) {
                        list.innerHTML = '<p class="text-muted text-center py-4">No templates saved yet</p>';
                        return;
                    }
                    
                    json.templates.forEach(t => {
                        const item = document.createElement('a');
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">${t.name}</h6>
                                    <small class="text-muted">${t.description || 'No description'}</small>
                                </div>
                                <button class="btn btn-sm btn-primary" onclick="loadTemplate(${t.id})">Load</button>
                            </div>
                        `;
                        list.appendChild(item);
                    });
                }
            } catch(e) {
                console.error('Failed to load templates', e);
            }
        }

        async function saveTemplate() {
            const name = document.getElementById('template-name').value;
            const description = document.getElementById('template-desc').value;
            
            if (!name) {
                alert('Please enter template name');
                return;
            }
            
            const structure = {
                mb: gridMB.getData(),
                abstract: gridAbstract.getData(),
                rate: gridRate.getData()
            };
            
            try {
                const res = await fetch(appBase + '/estimation/api/save_template', {
                    method: 'POST',
                    body: JSON.stringify({
                        name: name,
                        description: description,
                        structure: structure
                    })
                });
                const json = await res.json();
                
                if (json.success) {
                    alert('Template saved successfully!');
                    document.getElementById('template-name').value = '';
                    document.getElementById('template-desc').value = '';
                    loadTemplatesList();
                } else {
                    alert('Failed to save template: ' + json.error);
                }
            } catch(e) {
                alert('Error: ' + e.message);
            }
        }

        async function loadTemplate(templateId) {
            if (!confirm('Loading a template will replace current data. Continue?')) return;
            
            try {
                const res = await fetch(appBase + '/estimation/api/load_template?template_id=' + templateId);
                const json = await res.json();
                
                if (json.success) {
                    // Clear quantities but keep structure
                    const structure = json.structure;
                    
                    // Load structure into grids
                    gridMB.setData(structure.mb || getDefaultMB());
                    gridAbstract.setData(structure.abstract || getDefaultAbstract());
                    gridRate.setData(structure.rate || []);
                    
                    alert('Template "' + json.name + '" loaded successfully!');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('template-modal'));
                    modal.hide();
                } else {
                    alert('Failed to load template: ' + json.error);
                }
            } catch(e) {
                alert('Error: ' + e.message);
            }
        }

        function openHistoryModal() {
            loadVersionHistory();
            const modal = new bootstrap.Modal(document.getElementById('history-modal'));
            modal.show();
        }

        async function loadVersionHistory() {
            try {
                const res = await fetch(appBase + '/estimation/api/get_versions?project_id=' + projectId);
                const json = await res.json();
                
                if (json.success) {
                    const list = document.getElementById('versions-list');
                    list.innerHTML = '';
                    
                    if (json.versions.length === 0) {
                        list.innerHTML = '<p class="text-muted text-center py-4">No version history yet</p>';
                        return;
                    }
                    
                    json.versions.forEach((v, index) => {
                        const item = document.createElement('a');
                        item.className = 'list-group-item list-group-item-action';
                        const date = new Date(v.created_at);
                        const timeAgo = getTimeAgo(date);
                        
                        item.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">${index === 0 ? '🟢 Current Version' : 'Version #' + v.id}</h6>
                                    <small class="text-muted">${timeAgo} - ${v.change_description || 'Auto-save'}</small>
                                </div>
                                ${index !== 0 ? `<button class="btn btn-sm btn-warning" onclick="restoreVersion(${v.id})">Restore</button>` : ''}
                            </div>
                        `;
                        list.appendChild(item);
                    });
                }
            } catch(e) {
                console.error('Failed to load versions', e);
            }
        }

        async function restoreVersion(versionId) {
            if (!confirm('Restore this version? Current work will be saved as a new version.')) return;
            
            try {
                const res = await fetch(appBase + '/estimation/api/restore_version', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ version_id: versionId })
                });
                const json = await res.json();
                
                if (json.success) {
                    gridMB.setData(json.data.mb || getDefaultMB());
                    gridAbstract.setData(json.data.abstract || getDefaultAbstract());
                    gridRate.setData(json.data.rate || []);
                    
                    alert('Version restored successfully!');
                    location.reload();
                } else {
                    alert('Failed to restore: ' + json.error);
                }
            } catch(e) {
                alert('Error: ' + e.message);
            }
        }

        function getTimeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            if (seconds < 60) return 'Just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + ' min ago';
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + ' hr ago';
            const days = Math.floor(hours / 24);
            return days + ' day' + (days > 1 ? 's' : '') + ' ago';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
