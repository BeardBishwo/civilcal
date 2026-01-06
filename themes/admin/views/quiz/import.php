<?php
/**
 * PREMIUM QUESTION IMPORT SUITE
 * High-performance bulk data ingestion with real-time conflict resolution.
 */
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Premium Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-file-import"></i>
                    <h1>Import Manager</h1>
                </div>
                <div class="header-subtitle">Bulk ingestion via Excel/CSV • Automated duplication check • Hash-based validation</div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">BATCHES</span>
                    <span class="value" id="statsBatches">--</span>
                </div>
                <div class="stat-pill success">
                    <span class="label">STAGED</span>
                    <span class="value" id="statsStaged">0</span>
                </div>
                <a href="<?= app_base_url('admin/quiz/import/template') ?>" class="btn btn-primary btn-compact" style="background:white; color:var(--admin-primary); text-decoration:none;">
                    <i class="fas fa-download"></i>
                    <span>TEMPLATE</span>
                </a>
            </div>
        </div>

        <!-- Hidden File Input -->
        <input type="file" id="fileInput" style="display: none !important;" accept=".xlsx, .xls, .csv">

        <!-- Dynamic Workflow Area -->
        <div class="import-workflow">
            
            <!-- Upload Dropzone (Visible by default) -->
            <div id="uploadSection" class="upload-suite">
                <div class="upload-card" onclick="document.getElementById('fileInput').click()">
                    <div class="upload-icon-orb">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h2>Secure Ingestion Zone</h2>
                    <p>Click or drag your engineering questionnaire (XLSX, CSV) here.</p>
                    <button class="btn btn-primary gradient mt-3" type="button">
                        <i class="fas fa-plus-circle"></i> CHOOSE SOURCE FILE
                    </button>
                    
                    <div id="uploadProgress" class="upload-progress-container" style="display: none;">
                        <div class="progress-details">
                            <span class="status-text">Analyzing Content...</span>
                            <span id="progressText" class="percent-text">0%</span>
                        </div>
                        <div class="premium-progress">
                            <div id="progressBar" class="progress-fill" style="width: 0%"></div>
                        </div>
                        <p class="progress-hint">Verifying row hashes and checking for system collisions.</p>
                    </div>
                </div>
            </div>

            <!-- Staging Dashboard (Hidden initially) -->
            <div id="stagingSection" class="staging-dashboard" style="display: none;">
                
                <!-- Staging Controls -->
                <div class="compact-toolbar">
                    <div class="toolbar-left">
                        <ul class="premium-tabs">
                            <li class="tab-item active" id="tabClean" onclick="ImportManager.switchTab('clean')">
                                <i class="fas fa-check-double"></i> 
                                Ready to Live <span class="tab-badge bg-emerald" id="cleanCount">0</span>
                            </li>
                            <li class="tab-item" id="tabDup" onclick="ImportManager.switchTab('duplicates')">
                                <i class="fas fa-radiation"></i> 
                                Conflicts <span class="tab-badge bg-rose" id="dupCount">0</span>
                            </li>
                        </ul>
                    </div>
                    <div class="toolbar-right">
                        <div id="cleanActions">
                            <button class="btn btn-primary success btn-compact" onclick="ImportManager.publishClean()">
                                <i class="fas fa-rocket"></i> PUBLISH ALL CLEAN ( <span id="btnCleanCount">0</span> )
                            </button>
                        </div>
                        <div id="dupActions" style="display: none;">
                            <button class="btn btn-secondary btn-compact" onclick="ImportManager.resolveAll('skip')">SKIP ALL NEW</button>
                            <button class="btn btn-danger btn-compact ms-2" onclick="ImportManager.resolveAll('overwrite')">OVERWRITE ALL OLD</button>
                        </div>
                    </div>
                </div>

                <!-- Staging Content -->
                <div class="staging-canvas">
                    
                    <!-- Clean Tab Content -->
                    <div id="paneClean" class="tab-pane-premium">
                        <div class="table-container">
                            <div class="table-wrapper">
                                <table class="table-compact">
                                    <thead>
                                        <tr>
                                            <th>Target Category</th>
                                            <th>Question Preview</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Integrity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cleanTableBody">
                                        <!-- Dynamic Rows -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Duplicates Tab Content -->
                    <div id="paneDuplicates" class="tab-pane-premium" style="display: none;">
                        <div id="duplicateContainer" class="conflict-grid">
                            <!-- Dynamic Cards -->
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const baseUrl = '<?= app_base_url() ?>';
</script>
<script src="<?= app_base_url('assets/js/admin/import-manager.js') ?>"></script>

<style>
/* ========================================
   PREMIUM IMPORT SUITE STYLES
   Matching Categories Page Design
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-success: #10b981;
    --admin-danger: #ef4444;
    --admin-warning: #f59e0b;
}

.admin-wrapper-container { 
    padding: 1rem; 
    background: #f8f9fa; 
    min-height: calc(100vh - 70px); 
}

.admin-content-wrapper { 
    background: white; 
    border-radius: 12px; 
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); 
    overflow: hidden; 
}

/* Premium Header */
.compact-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    padding: 1.5rem 2rem; 
    background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); 
    color: white; 
}

.header-title { 
    display: flex; 
    align-items: center; 
    gap: 0.75rem; 
}

.header-title h1 { 
    margin: 0; 
    font-size: 1.5rem; 
    font-weight: 700; 
    color: white; 
}

.header-title i { 
    font-size: 1.25rem; 
    opacity: 0.9; 
}

.header-subtitle { 
    font-size: 0.85rem; 
    opacity: 0.8; 
    margin-top: 4px; 
    font-weight: 500; 
}

.stat-pill { 
    background: rgba(255,255,255,0.15); 
    border: 1px solid rgba(255,255,255,0.2); 
    border-radius: 8px; 
    padding: 0.5rem 1rem; 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    min-width: 80px; 
}

.stat-pill.success { 
    background: rgba(16, 185, 129, 0.15); 
    border-color: rgba(16, 185, 129, 0.3); 
}

.stat-pill .label { 
    font-size: 0.65rem; 
    font-weight: 700; 
    letter-spacing: 0.5px; 
    opacity: 0.9; 
}

.stat-pill .value { 
    font-size: 1.1rem; 
    font-weight: 800; 
    line-height: 1.1; 
}

/* Upload Suite */
.upload-suite { 
    padding: 4rem 2rem; 
}

.upload-card { 
    max-width: 700px; 
    margin: 0 auto; 
    padding: 4rem 2rem; 
    border: 3px dashed #cbd5e1; 
    border-radius: 16px; 
    background: #f8fafc; 
    text-align: center; 
    cursor: pointer; 
    transition: all 0.3s ease;
}

.upload-card:hover { 
    border-color: var(--admin-primary); 
    background: #f5f3ff; 
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(102, 126, 234, 0.1);
}

.upload-icon-orb { 
    width: 90px; 
    height: 90px; 
    background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary)); 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    margin: 0 auto 2rem; 
    font-size: 2.5rem; 
    color: white; 
    box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.3);
}

.upload-card h2 { 
    font-weight: 800; 
    color: #1e293b; 
    margin-bottom: 0.75rem; 
    font-size: 1.75rem;
}

.upload-card p { 
    color: #64748b; 
    font-size: 0.95rem; 
}

/* Progress */
.upload-progress-container { 
    margin-top: 3rem; 
    text-align: left; 
    max-width: 500px; 
    margin-left: auto; 
    margin-right: auto; 
}

.progress-details { 
    display: flex; 
    justify-content: space-between; 
    margin-bottom: 0.75rem; 
    font-weight: 700; 
    font-size: 0.8rem; 
}

.status-text { 
    color: var(--admin-primary); 
    text-transform: uppercase; 
}

.percent-text { 
    color: #1e293b; 
}

.premium-progress { 
    height: 10px; 
    background: #e2e8f0; 
    border-radius: 10px; 
    overflow: hidden; 
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
}

.progress-fill { 
    height: 100%; 
    background: linear-gradient(90deg, var(--admin-primary), var(--admin-secondary)); 
    transition: width 0.5s ease; 
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
}

.progress-hint { 
    font-size: 0.75rem; 
    color: #94a3b8; 
    margin-top: 0.75rem; 
    text-align: center; 
}

/* Staging Dashboard */
.staging-dashboard { 
    animation: fadeIn 0.5s; 
}

.compact-toolbar { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    background: #eff6ff; 
    border-bottom: 1px solid #bfdbfe; 
    padding: 0.75rem 2rem; 
}

.premium-tabs { 
    list-style: none; 
    display: flex; 
    gap: 0.5rem; 
    margin: 0; 
    padding: 0; 
}

.tab-item { 
    padding: 0.6rem 1.25rem; 
    border-radius: 8px; 
    font-weight: 700; 
    font-size: 0.85rem; 
    color: #64748b; 
    cursor: pointer; 
    transition: all 0.2s ease; 
    display: flex; 
    align-items: center; 
    gap: 0.5rem;
}

.tab-item:hover {
    background: rgba(255,255,255,0.5);
}

.tab-item.active { 
    background: white; 
    color: var(--admin-primary); 
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
}

.tab-badge { 
    font-size: 0.7rem; 
    padding: 2px 8px; 
    border-radius: 20px; 
    color: white; 
    margin-left: 4px; 
}

.bg-emerald { background: var(--admin-success); }
.bg-rose { background: var(--admin-danger); }

/* Table Premium */
.table-container {
    padding: 0;
}

.table-wrapper {
    overflow-x: auto;
}

.table-compact { 
    width: 100%; 
    border-collapse: collapse; 
    font-size: 0.875rem;
}

.table-compact th { 
    background: #f8fafc; 
    padding: 0.75rem 1.5rem; 
    text-align: left; 
    font-weight: 600; 
    color: #94a3b8; 
    text-transform: uppercase; 
    font-size: 0.7rem; 
    letter-spacing: 0.5px; 
    border-bottom: 1px solid #e2e8f0; 
}

.table-compact td { 
    padding: 0.75rem 1.5rem; 
    border-bottom: 1px solid #f1f5f9; 
    vertical-align: middle; 
}

.table-compact tbody tr:hover {
    background: #f8fafc;
}

.cat-tag { 
    font-weight: 700; 
    color: var(--admin-primary); 
    background: #eef2ff; 
    padding: 4px 10px; 
    border-radius: 6px; 
    font-size: 0.75rem; 
    display: inline-block;
}

.q-preview { 
    font-weight: 500; 
    color: #1e293b; 
    max-width: 500px; 
    white-space: nowrap; 
    overflow: hidden; 
    text-overflow: ellipsis; 
    font-size: 0.9rem; 
}

.type-badge { 
    font-size: 0.65rem; 
    font-weight: 800; 
    color: #64748b; 
    border: 1px solid #e2e8f0; 
    padding: 4px 10px; 
    border-radius: 10px; 
    background: white;
}

/* Conflict Grid */
.conflict-grid { 
    padding: 2rem; 
    display: flex; 
    flex-direction: column; 
    gap: 1.5rem; 
    max-width: 1000px; 
    margin: 0 auto; 
}

.conflict-card { 
    background: white; 
    border-radius: 12px; 
    border: 2px solid #fee2e2; 
    overflow: hidden; 
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
    transition: all 0.2s ease;
}

.conflict-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.conflict-header { 
    background: linear-gradient(135deg, #fff1f2, #ffe4e6); 
    padding: 1rem 1.5rem; 
    display: flex; 
    justify-content: space-between; 
    border-bottom: 2px solid #fecaca; 
}

.conflict-title { 
    color: #e11d48; 
    font-weight: 800; 
    font-size: 0.85rem; 
    display: flex; 
    align-items: center; 
    gap: 0.5rem; 
}

.conflict-body { 
    padding: 1.5rem; 
    display: grid; 
    grid-template-columns: 1fr 180px 1fr; 
    gap: 1.5rem; 
    align-items: center; 
}

.compare-box { 
    padding: 1.25rem; 
    border-radius: 12px; 
    height: 100%; 
    display: flex; 
    flex-direction: column; 
    gap: 0.75rem; 
}

.compare-box.new { 
    background: #ecfdf5; 
    border: 2px dashed var(--admin-success); 
}

.compare-box.old { 
    background: #f8fafc; 
    border: 2px solid #e2e8f0; 
}

.compare-label { 
    font-size: 0.65rem; 
    font-weight: 800; 
    color: #94a3b8; 
    text-transform: uppercase; 
}

.compare-text { 
    font-weight: 600; 
    color: #1e293b; 
    font-size: 0.9rem; 
    line-height: 1.5; 
}

.conflict-ops { 
    display: flex; 
    flex-direction: column; 
    gap: 0.75rem; 
}

/* Buttons */
.btn { 
    height: 40px; 
    padding: 0 1.25rem; 
    border-radius: 8px; 
    font-weight: 700; 
    font-size: 0.8rem; 
    cursor: pointer; 
    display: inline-flex; 
    align-items: center; 
    justify-content: center;
    gap: 0.5rem; 
    border: none; 
    transition: all 0.2s ease; 
    text-decoration: none;
}

.btn-primary { 
    background: var(--admin-primary); 
    color: white; 
}

.btn-primary.gradient { 
    background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); 
}

.btn-primary.success { 
    background: var(--admin-success); 
}

.btn-secondary { 
    background: #f1f5f9; 
    color: #475569; 
    border: 1px solid #e2e8f0;
}

.btn-danger { 
    background: white; 
    color: var(--admin-danger); 
    border: 2px solid var(--admin-danger); 
}

.btn-compact { 
    height: 36px; 
    padding: 0 0.75rem; 
    font-size: 0.75rem; 
}

.btn:hover { 
    transform: translateY(-1px); 
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}

.btn:active {
    transform: translateY(0);
}

.mt-3 { margin-top: 1rem; }
.ms-2 { margin-left: 0.5rem; }
.text-center { text-align: center; }

@keyframes fadeIn { 
    from { opacity: 0; transform: translateY(10px); } 
    to { opacity: 1; transform: translateY(0); } 
}

@media (max-width: 768px) {
    .conflict-body {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .compact-toolbar {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
