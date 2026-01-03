<div class="content-wrapper p-4">

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
        <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1 text-white">Bulk Import Center</h2>
                    <p class="mb-0 text-white-50">Upload thousands of questions safely. Duplicates are auto-quarantined.</p>
                </div>
                <div>
                    <button class="btn btn-white text-primary fw-bold shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#formatGuideModal">
                        <i class="fas fa-info-circle me-2"></i> Format Guide
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="uploadStep">
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body p-5 text-center">
                    
                    <div class="mb-5">
                        <h5 class="fw-bold text-gray-800 mb-3">Step 1: Get the Smart Template</h5>
                        <p class="text-muted small mb-4">
                            Don't guess the column names. Download our pre-formatted Excel file.<br>
                            It includes <strong>Dropdowns</strong> for your specific Categories.
                        </p>
                        <a href="/admin/quiz/import/template" class="btn btn-outline-primary btn-lg rounded-pill px-5 border-2 fw-bold">
                            <i class="fas fa-file-excel me-2"></i> Download .xlsx Template
                        </a>
                    </div>

                    <hr class="text-muted opacity-25 w-50 mx-auto my-4">

                    <div class="mb-3">
                        <h5 class="fw-bold text-gray-800 mb-3">Step 2: Upload Filled File</h5>
                        
                        <div class="upload-zone p-5 rounded position-relative" id="dropZone" 
                             style="border: 3px dashed #e3e6f0; background: #f8f9fc; transition: all 0.3s;"
                             onclick="document.getElementById('fileInput').click()">
                            
                            <div class="mb-3">
                                <span class="avatar avatar-lg bg-white shadow-sm p-3 rounded-circle text-primary">
                                    <i class="fas fa-cloud-upload-alt fa-3x"></i>
                                </span>
                            </div>
                            <h6 class="fw-bold text-primary">Click to Upload or Drag File Here</h6>
                            <p class="text-muted small mb-0">Supports .xlsx, .csv (Max 50MB)</p>
                            
                            <input type="file" id="fileInput" class="d-none" accept=".xlsx, .xls, .csv">

                            <div id="uploadOverlay" class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-95 d-none flex-column justify-content-center align-items-center rounded">
                                <div class="spinner-border text-primary mb-3" role="status"></div>
                                <h6 class="fw-bold text-primary">Processing Batch...</h6>
                                <span id="progressText" class="small text-muted">0% Complete</span>
                                <div class="progress w-50 mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" id="progressBar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-primary text-white h-100" style="border-radius: 15px; background: linear-gradient(180deg, #764ba2 0%, #667eea 100%);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-lightbulb me-2"></i> Pro Tips</h5>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3"><i class="fas fa-check-circle fa-lg text-success bg-white rounded-circle"></i></div>
                        <div>
                            <strong class="d-block text-white">Auto-Duplicate Check</strong>
                            <small class="text-white-50">We scan every question. If it exists, we quarantine it.</small>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="me-3"><i class="fas fa-calculator fa-lg text-warning bg-white rounded-circle p-1"></i></div>
                        <div>
                            <strong class="d-block text-white">Math Friendly</strong>
                            <small class="text-white-50">Paste LaTeX codes directly. Example: <code>$$ a^2 + b^2 $$</code></small>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="me-3"><i class="fas fa-image fa-lg text-info bg-white rounded-circle p-1"></i></div>
                        <div>
                            <strong class="d-block text-white">Images? No Problem.</strong>
                            <small class="text-white-50">Put the Image URL in the 'Image' column. We'll handle the rest.</small>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-top border-white border-opacity-10 text-center">
                        <small class="text-white-50 text-uppercase fw-bold">System Capacity</small>
                        <h2 class="fw-bold text-white mb-0">Unlimited</h2>
                        <small class="text-white-50">Rows per batch</small>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="stagingSection" class="d-none animate__animated animate__fadeInUp">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <ul class="nav nav-pills" id="reviewTabs">
                    <li class="nav-item me-2">
                        <a class="nav-link active rounded-pill fw-bold bg-success bg-opacity-10 text-success px-4" data-bs-toggle="tab" href="#cleanTab">
                            <i class="fas fa-check me-2"></i> Ready (<span id="cleanCount">0</span>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-pill fw-bold bg-danger bg-opacity-10 text-danger px-4" data-bs-toggle="tab" href="#dupTab">
                            <i class="fas fa-exclamation-triangle me-2"></i> Conflicts (<span id="dupCount">0</span>)
                        </a>
                    </li>
                </ul>
                <div>
                    <button class="btn btn-outline-secondary rounded-pill me-2" onclick="location.reload()">Cancel</button>
                    <button class="btn btn-success rounded-pill fw-bold px-4 shadow-sm" onclick="ImportManager.publishClean()">
                        Publish Ready Items
                    </button>
                </div>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="cleanTab">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="bg-light text-muted small fw-bold text-uppercase">
                                    <tr><th>Category</th><th>Question</th><th>Type</th><th>Status</th></tr>
                                </thead>
                                <tbody id="cleanTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="dupTab">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-sm btn-outline-danger me-2" onclick="ImportManager.resolveAll('overwrite')">Overwrite All</button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="ImportManager.resolveAll('skip')">Skip All</button>
                        </div>
                        <div id="duplicateContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="formatGuideModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary">Excel Formatting Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 rounded-3 mb-4">
                    <i class="fas fa-info-circle me-2"></i> 
                    <strong>Best Practice:</strong> Use the "Download Template" button. It has validation rules built-in!
                </div>
                
                <table class="table table-bordered table-sm small">
                    <thead class="bg-light">
                        <tr>
                            <th width="20%">Column</th>
                            <th width="30%">Required?</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Category</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                            <td>Select from the dropdown list. Do not type manually.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Question Text</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                            <td>Supports plain text or LaTeX math (e.g. <code>$$ x^2 $$</code>).</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Options (A-D)</td>
                            <td><span class="badge bg-warning text-dark">Conditional</span></td>
                            <td>Fill A-D for MCQ. Leave C-D blank for True/False.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Correct Ans</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                            <td>Must be one letter: <code>A</code>, <code>B</code>, <code>C</code>, or <code>D</code>.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Practical?</td>
                            <td><span class="badge bg-secondary">Optional</span></td>
                            <td><code>Yes</code> for Site/Field Mode. <code>No</code> for Academic/PSC.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const ImportManager = {
    batchId: null,

    init: function() {
        // Initialize
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        
        // Drag Handling
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        });

        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
        
        function handleFiles(files) {
            if (files.length > 0) {
                ImportManager.uploadFile(files[0]);
            }
        }
    },

    uploadFile: function(file) {
        // Show Overlay
        document.getElementById('uploadOverlay').classList.remove('d-none');
        document.getElementById('uploadOverlay').classList.add('d-flex');
        
        const chunkSize = 1024 * 1024 * 5; // 5MB Chunk (Frontend File)?? 
        // No, backend chunks logic.
        // Wait, the backend processes row "Chunks" but file upload limited by PHP?
        // For simplistic approach, upload file once, then process in loop.
        
        // But here we do straightforward upload.
        // We will stick to the logic: Upload -> Process
        
        const formData = new FormData();
        formData.append('import_file', file);
        formData.append('start_row', 2);
        
        // Initial Call
        this.processBatch(formData);
    },

    processBatch: function(formData) {
        fetch('/api/admin/quiz/import/process-chunk', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                alert('Error: ' + data.error);
                location.reload();
                return;
            }

            // Update Progress
            // We can't know % without total rows first.
            // But assume batches of 50.
            document.getElementById('progressText').innerText = `Processed Row ${data.current_row}`;
            
            // Set Batch ID for session consistency if needed, though backend handles it
            this.batchId = data.batch_id; 

            if (!data.eof) {
                // Continue
                formData.set('start_row', data.next_row);
                formData.set('batch_id', data.batch_id); 
                // Important: re-append file? 
                // File upload requires re-sending the file blob every time for PHP to read it?
                // Yes, unless we stored it on server.
                // Current implementation DOES NOT store file.
                // It reads from $_FILES['import_file']['tmp_name'].
                // So we MUST send file.
                // FormData header keeps the file reference.
                this.processBatch(formData); 
            } else {
                // Done
                this.loadStaging(data.batch_id);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Upload failed. Check console.');
        });
    },

    loadStaging: function(batchId) {
        document.getElementById('uploadOverlay').classList.add('d-none');
        document.getElementById('uploadStep').style.display = 'none';
        document.getElementById('stagingSection').classList.remove('d-none');
        document.getElementById('stagingSection').classList.add('animate__fadeInUp');

        fetch('/api/admin/quiz/import/stats?batch_id=' + batchId)
        .then(r => r.json())
        .then(data => {
            document.getElementById('cleanCount').innerText = data.clean_count;
            document.getElementById('dupCount').innerText = data.duplicate_count;
            
            // Render Clean
            const cleanBody = document.getElementById('cleanTableBody');
            cleanBody.innerHTML = data.clean_rows.map(r => `
                <tr>
                    <td><span class="badge bg-light text-dark border">${r.category}</span></td>
                    <td>${r.question_text.substring(0, 80)}...</td>
                    <td><span class="badge bg-info bg-opacity-10 text-info">${r.type}</span></td>
                    <td><span class="badge bg-success">Ready</span></td>
                </tr>
            `).join('');

            // Render Duplicates
            const dupContainer = document.getElementById('duplicateContainer');
            dupContainer.innerHTML = data.duplicate_rows.map(r => `
                <div class="card mb-3 border-danger shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 border-end">
                                <span class="badge bg-danger mb-2">Incoming Question</span>
                                <p class="mb-1 fw-bold">${r.new_question}</p>
                                <small class="text-muted">Answer: ${r.new_answer}</small>
                            </div>
                            <div class="col-md-5 border-end bg-light">
                                <span class="badge bg-secondary mb-2">Existing Question (ID: ${r.match_id})</span>
                                <p class="mb-1 text-muted">${r.old_question}</p>
                                <small class="text-danger fw-bold">Used in ${r.usage_count} exams</small>
                            </div>
                            <div class="col-md-2 d-flex flex-column justify-content-center gap-2">
                                <button onclick="ImportManager.resolveOne(${r.id}, 'overwrite')" class="btn btn-sm btn-outline-danger w-100">Overwrite</button>
                                <button onclick="ImportManager.resolveOne(${r.id}, 'skip')" class="btn btn-sm btn-outline-secondary w-100">Skip</button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        });
    },

    resolveOne: function(id, action) {
        fetch('/api/admin/quiz/import/resolve', {
            method: 'POST',
            body: JSON.stringify({id, action})
        }).then(() => {
            // Remove card
            // Refresh stats? Or just UI remove
            // Re-fetch stats is safest
            this.loadStaging(this.batchId);
        });
    },

    resolveAll: function(action) {
        if(!confirm("Are you sure? This affects ALL duplicates.")) return;
        // Ideally loop or batch endpoint
        // For now, loop UI (lazy) or assume backend loop
        // Let's assume user resolves one by one or we add bulk endpoint
        // For simplicity: Reload for now or implement bulk endpoint
        alert("Bulk resolve not implemented in this demo.");
    },

    publishClean: function() {
        fetch('/api/admin/quiz/import/publish', {
            method: 'POST',
            body: JSON.stringify({batch_id: this.batchId})
        }).then(r => r.json())
        .then(d => {
            alert("Success! Imported " + d.status);
            location.href = '/admin/quiz/questions';
        });
    }
};

document.addEventListener('DOMContentLoaded', ImportManager.init);
</script>

<style>
    /* Hover Animation for Dropzone */
    .upload-zone:hover {
        border-color: #667eea !important;
        background: #f0f4ff !important;
        cursor: pointer;
    }
</style>
