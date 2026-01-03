<div class="container-fluid p-4">

    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 15px;">
        <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1 text-white">Import Questions</h2>
                    <p class="mb-0 text-white-50">Bulk upload questions via Excel/CSV and review duplicates.</p>
                </div>
                <div>
                    <a href="/admin/quiz/import/template" class="btn btn-light text-primary fw-bold shadow-sm me-2 rounded-pill px-4">
                        <i class="fas fa-file-download me-2"></i>Template
                    </a>
                    <button class="btn btn-light text-primary fw-bold shadow-sm rounded-pill px-4" onclick="document.getElementById('fileInput').click()">
                        <i class="fas fa-plus me-2"></i>New Upload
                    </button>
                </div>
            </div>
        </div>
    </div>

    <input type="file" id="fileInput" class="d-none" accept=".xlsx, .xls, .csv">

    <div class="card border-0 shadow-sm mb-4" id="uploadSection" style="border-radius: 15px;">
        <div class="card-body p-5">
            <div class="text-center p-5" style="border: 2px dashed #e3e6f0; border-radius: 15px; background-color: #f8f9fc; cursor: pointer;" onclick="document.getElementById('fileInput').click()">
                
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: #eaecf4;">
                    <i class="fas fa-cloud-upload-alt fa-3x text-secondary"></i>
                </div>
                
                <h4 class="text-gray-800 fw-bold">No file selected</h4>
                <p class="text-muted mb-4">Get started by uploading your Excel or CSV file.</p>
                
                <button class="btn btn-primary px-4 py-2 rounded-pill shadow-sm" style="background: #764ba2; border:none;">
                    Select File
                </button>

                <div id="uploadProgress" class="d-none mt-4 w-50 mx-auto">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold text-primary">Processing...</span>
                        <span id="progressText" class="small fw-bold text-dark">0%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar" role="progressbar" id="progressBar" style="width: 0%; background: #764ba2;"></div>
                    </div>
                    <small class="text-muted mt-2 d-block">Analyzing content & checking hashes...</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm d-none" id="stagingSection" style="border-radius: 15px;">
        
        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
            <ul class="nav nav-pills card-header-pills" id="importTabs" role="tablist">
                <li class="nav-item me-3">
                    <a class="nav-link active fw-bold px-4 py-2 rounded-pill" id="clean-tab" data-bs-toggle="tab" href="#clean" role="tab" style="background-color: #1cc88a; color: white; box-shadow: 0 4px 6px rgba(28, 200, 138, 0.2);">
                        <i class="fas fa-check-circle me-2"></i>Clean Questions 
                        <span class="badge bg-white text-success ms-2 rounded-pill" id="cleanCount">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold px-4 py-2 rounded-pill" id="duplicate-tab" data-bs-toggle="tab" href="#duplicates" role="tab" style="background-color: #e74a3b; color: white; box-shadow: 0 4px 6px rgba(231, 74, 59, 0.2);">
                        <i class="fas fa-exclamation-triangle me-2"></i>Conflicts 
                        <span class="badge bg-white text-danger ms-2 rounded-pill" id="dupCount">0</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="clean" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded" style="background-color: #f0fdf4; border-left: 5px solid #1cc88a;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success fa-2x me-3"></i>
                            <div>
                                <h6 class="fw-bold text-success mb-0">Ready for Import</h6>
                                <small class="text-muted">These rows have no duplicates. Safe to publish.</small>
                            </div>
                        </div>
                        <button class="btn btn-success fw-bold rounded-pill px-4 shadow-sm" onclick="ImportManager.publishClean()">
                            Publish All (<span id="btnCleanCount">0</span>)
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="bg-light">
                                <tr class="text-uppercase small text-muted font-weight-bold">
                                    <th style="border-top:0;">Category</th>
                                    <th style="border-top:0;">Question Preview</th>
                                    <th style="border-top:0;">Type</th>
                                    <th style="border-top:0;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="cleanTableBody">
                                </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="duplicates" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="text-gray-800 fw-bold m-0">Resolve Conflicts</h5>
                        <div>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="ImportManager.resolveAll('skip')">Skip All New</button>
                            <button class="btn btn-outline-danger btn-sm rounded-pill px-3 ms-2" onclick="ImportManager.resolveAll('overwrite')">Overwrite All Old</button>
                        </div>
                    </div>

                    <div id="duplicateContainer">
                        </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="/assets/js/admin/import-manager.js"></script>
<style>
    /* Gradient Text / Purple Colors */
    .text-purple { color: #6f42c1 !important; }
    .bg-purple-100 { background-color: #f3eefc !important; }
    
    /* Card Hover Effect */
    .hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; transition: box-shadow .3s; }
    
    /* Nav Pills Override */
    .nav-pills .nav-link.active { box-shadow: 0 4px 6px rgba(50, 50, 93, .11), 0 1px 3px rgba(0, 0, 0, .08); }
    
    /* Button Tweaks */
    .btn-light { background: #fff; border: 1px solid #e3e6f0; }
    .btn-light:hover { background: #f8f9fa; }
    
    /* Table Tweaks */
    .table thead th { border-top: 0; border-bottom: 2px solid #e3e6f0; }
</style>
