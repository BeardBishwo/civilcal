<?php
/**
 * PREMIUM QUESTION IMPORT SUITE
 * High-performance bulk data ingestion with real-time conflict resolution.
 */
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Phase 1: Interactive Header -->
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
                <a href="<?= app_base_url('admin/quiz/import/template') ?>" class="arch-btn secondary ultra-sm">
                    <i class="fas fa-download"></i> TEMPLATE
                </a>
            </div>
        </div>

        <!-- Hidden File Input -->
        <input type="file" id="fileInput" style="display: none !important;" accept=".xlsx, .xls, .csv">

        <!-- Phase 2: Dynamic Workflow Area -->
        <div class="import-workflow">
            
            <!-- Upload Dropzone (Visible by default) -->
            <div id="uploadSection" class="upload-suite">
                <div class="upload-card" onclick="document.getElementById('fileInput').click()">
                    <div class="upload-icon-orb">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h2>Secure Ingestion Zone</h2>
                    <p>Click or drag your engineering questionnaire (XLSX, CSV) here.</p>
                    <button class="arch-btn primary gradient mt-3">
                        <i class="fas fa-plus-circle"></i> CHOOSE SOURCE FILE
                    </button>
                    
                    <div id="uploadProgress" class="upload-progress-container" style="display: none;">
                        <div class="progress-details">
                            <span class="status-text">Analyzing Content...</span>
                            <span id="progressText" class="percent-text">0%</span>
                        </div>
                        <div class="arch-progress">
                            <div id="progressBar" class="progress-fill" style="width: 0%"></div>
                        </div>
                        <p class="progress-hint">Verifying row hashes and checking for system collisions.</p>
                    </div>
                </div>
            </div>

            <!-- Staging Dashboard (Hidden initially) -->
            <div id="stagingSection" class="staging-dashboard" style="display: none;">
                
                <!-- Staging Controls -->
                <div class="staging-toolbar">
                    <div class="toolbar-left">
                        <ul class="arch-tabs">
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
                            <button class="arch-btn primary success ultra-sm" onclick="ImportManager.publishClean()">
                                <i class="fas fa-rocket"></i> PUBLISH ALL CLEAN ( <span id="btnCleanCount">0</span> )
                            </button>
                        </div>
                        <div id="dupActions" style="display: none;">
                            <button class="arch-btn secondary ultra-sm" onclick="ImportManager.resolveAll('skip')">SKIP ALL NEW</button>
                            <button class="arch-btn danger-outline ultra-sm ms-2" onclick="ImportManager.resolveAll('overwrite')">OVERWRITE ALL OLD</button>
                        </div>
                    </div>
                </div>

                <!-- Staging Content -->
                <div class="staging-canvas">
                    
                    <!-- Clean Tab Content -->
                    <div id="paneClean" class="tab-pane-premium">
                        <div class="table-container">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th>Target Category</th>
                                        <th>Question Preview</th>
                                        <th class="text-center">Complexity</th>
                                        <th class="text-center">Integrity</th>
                                    </tr>
                                </thead>
                                <tbody id="cleanTableBody">
                                    <!-- Dynamic Rows -->
                                </tbody>
                            </table>
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

<script>
const baseUrl = '<?= app_base_url() ?>';
</script>
<script src="<?= app_base_url('assets/js/admin/import-manager.js') ?>"></script>

<style>
/* ========================================
   IMPORT SUITE STYLES
   ======================================== */
.admin-wrapper-container { padding: 1rem; background: #f1f5f9; min-height: calc(100vh - 70px); }
.admin-content-wrapper { background: white; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; /* padding-bottom: 2rem; REMOVED FOR CLEANER UI */ }

/* Header Sync */
.compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; }
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 800; color: white; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }
.stat-pill { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 0.5rem 1.25rem; display: flex; flex-direction: column; align-items: center; min-width: 90px; }
.stat-pill.success { background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); }
.stat-pill .label { font-size: 0.65rem; font-weight: 700; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }
.stat-pill .value { font-size: 1.15rem; font-weight: 800; }

/* Upload Suite */
.upload-suite { padding: 4rem 2rem; }
.upload-card { 
    max-width: 700px; margin: 0 auto; padding: 4rem 2rem; border: 3px dashed #e2e8f0; border-radius: 20px; 
    background: #f8fafc; text-align: center; cursor: pointer; transition: 0.3s;
}
.upload-card:hover { border-color: #6366f1; background: #f5f3ff; }
.upload-icon-orb { 
    width: 90px; height: 90px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; 
    margin: 0 auto 2rem; font-size: 2.5rem; color: #6366f1; box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.2);
}
.upload-card h2 { font-weight: 800; color: #1e293b; margin-bottom: 0.75rem; }
.upload-card p { color: #64748b; font-size: 0.95rem; }

/* Progress */
.upload-progress-container { margin-top: 3rem; text-align: left; max-width: 500px; margin-left: auto; margin-right: auto; }
.progress-details { display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-weight: 700; font-size: 0.8rem; }
.status-text { color: #6366f1; text-transform: uppercase; }
.percent-text { color: #1e293b; }
.arch-progress { height: 8px; background: #e2e8f0; border-radius: 10px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #6366f1, #7c3aed); transition: 0.5s; }
.progress-hint { font-size: 0.75rem; color: #94a3b8; margin-top: 0.75rem; text-align: center; }

/* Staging Dashboard */
.staging-dashboard { animation: fadeIn 0.5s; }
.staging-toolbar { display: flex; justify-content: space-between; align-items: center; background: #eff6ff; border-bottom: 1px solid #bfdbfe; padding: 1rem 2rem; }
.arch-tabs { list-style: none; display: flex; gap: 0.5rem; margin: 0; padding: 0; }
.tab-item { 
    padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 700; font-size: 0.85rem; color: #64748b; 
    cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 0.5rem;
}
.tab-item.active { background: white; color: #4f46e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.tab-badge { font-size: 0.7rem; padding: 2px 8px; border-radius: 20px; color: white; margin-left: 4px; }
.bg-emerald { background: #10b981; }
.bg-rose { background: #f43f5e; }

/* Table Premium */
.table-compact { width: 100%; border-collapse: collapse; }
.table-compact th { background: #f8fafc; padding: 1rem 2rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
.table-compact td { padding: 1rem 2rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.cat-tag { font-weight: 700; color: #4f46e5; background: #eef2ff; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; }
.q-preview { font-weight: 500; color: #1e293b; max-width: 500px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.9rem; }
.type-badge { font-size: 0.65rem; font-weight: 800; color: #64748b; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 10px; }

/* Conflict Grid */
.conflict-grid { padding: 2rem; display: flex; flex-direction: column; gap: 1.5rem; max-width: 1000px; margin: 0 auto; }
.conflict-card { background: white; border-radius: 16px; border: 1px solid #fee2e2; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
.conflict-header { background: #fff1f2; padding: 1rem 1.5rem; display: flex; justify-content: space-between; border-bottom: 1px solid #fecaca; }
.conflict-title { color: #e11d48; font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; }
.conflict-body { padding: 1.5rem; display: grid; grid-template-columns: 1fr 180px 1fr; gap: 1.5rem; align-items: center; }

.compare-box { padding: 1rem; border-radius: 12px; height: 100%; display: flex; flex-direction: column; gap: 0.5rem; }
.compare-box.new { background: #ecfdf5; border: 1px dashed #10b981; }
.compare-box.old { background: #f8fafc; border: 1px solid #e2e8f0; }
.compare-label { font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
.compare-text { font-weight: 600; color: #1e293b; font-size: 0.9rem; line-height: 1.4; }

.conflict-ops { display: flex; flex-direction: column; gap: 0.75rem; }

/* Buttons Custom */
.arch-btn { height: 40px; padding: 0 1.25rem; border-radius: 10px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; border: none; transition: 0.2s; }
.arch-btn.primary { background: #6366f1; color: white; }
.arch-btn.primary.gradient { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.arch-btn.secondary { background: #f1f5f9; color: #475569; }
.arch-btn.success { background: #10b981; color: white; }
.arch-btn.danger-outline { background: white; color: #e11d48; border: 1px solid #e11d48; }
.arch-btn.ultra-sm { height: 32px; padding: 0 0.75rem; font-size: 0.75rem; }
.arch-btn:hover { transform: translateY(-1px); opacity: 0.9; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
