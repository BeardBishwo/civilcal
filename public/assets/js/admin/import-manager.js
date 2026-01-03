const ImportManager = {
    batchId: null,

    // 1. Initialize Upload Listener
    init: function () {
        const input = document.getElementById('fileInput');
        if (input) {
            input.addEventListener('change', this.handleFileSelect);
        }
    },

    // 2. Handle File & Start Chunking
    handleFileSelect: function (e) {
        const file = e.target.files[0];
        if (!file) return;

        // UI Reset
        document.getElementById('uploadProgress').classList.remove('d-none');
        document.getElementById('progressBar').style.width = '0%';

        // Start Chunked Upload (Recursive)
        ImportManager.uploadChunk(file, 2); // Start at row 2 (1 is header)
    },

    // 3. Recursive Chunk Uploader (The "Low Resource" Secret)
    uploadChunk: function (file, startRow) {
        const formData = new FormData();
        formData.append('import_file', file);
        formData.append('start_row', startRow);
        if (ImportManager.batchId) {
            formData.append('batch_id', ImportManager.batchId);
        }

        // AJAX Call
        fetch('/api/admin/quiz/import/process-chunk', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }

                // Sync batch ID from first chunk
                if (!ImportManager.batchId && data.batch_id) {
                    ImportManager.batchId = data.batch_id;
                }

                // Update Progress Bar
                const percent = Math.min(100, Math.round((data.current_row / data.total_rows) * 100));
                document.getElementById('progressBar').style.width = percent + '%';
                document.getElementById('progressText').innerText = percent + '%';

                if (!data.eof) {
                    // Not done? Call next chunk!
                    ImportManager.uploadChunk(file, data.next_row);
                } else {
                    // Done! Show Staging Area
                    ImportManager.loadStagingData(ImportManager.batchId);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Upload Error. Check console.');
            });
    },

    // 4. Load Data into Tables
    loadStagingData: function (batchId) {
        document.getElementById('uploadSection').classList.add('d-none');
        document.getElementById('stagingSection').classList.remove('d-none');

        // Fetch Clean & Duplicates
        fetch(`/api/admin/quiz/import/staging-stats/${batchId}`)
            .then(res => res.json())
            .then(data => {
                // Update Badges
                document.getElementById('cleanCount').innerText = data.clean_count;
                document.getElementById('btnCleanCount').innerText = data.clean_count;
                document.getElementById('dupCount').innerText = data.duplicate_count;

                // 1. RENDER CLEAN ROWS (Matches your Table Style)
                const cleanBody = document.getElementById('cleanTableBody');
                cleanBody.innerHTML = data.clean_rows.map(row => `
                <tr>
                    <td class="fw-bold text-primary">${row.category}</td>
                    <td class="text-dark">${row.question_text.substring(0, 80)}...</td>
                    <td><span class="badge bg-secondary rounded-pill px-3">${row.type}</span></td>
                    <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Ready</span></td>
                </tr>
            `).join('');

                // 2. RENDER DUPLICATE CARDS (Matches your Card Style)
                const dupContainer = document.getElementById('duplicateContainer');
                dupContainer.innerHTML = data.duplicate_rows.map(row => `
                <div class="card mb-3 shadow-sm border-0 duplicate-card" id="card-${row.id}" style="border-left: 5px solid #e74a3b;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="fw-bold text-danger m-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>Duplicate Found
                            </h6>
                            <span class="badge bg-light text-dark border">Ref ID: #${row.match_id}</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <div class="p-3 rounded bg-white border border-success border-2" style="border-style: dashed !important;">
                                    <span class="badge bg-success mb-2">INCOMING UPLOAD</span>
                                    <p class="mb-1 fw-bold text-dark">${row.new_question}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-list-ul me-1"></i> ${row.new_options_count} Options
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex flex-column justify-content-center">
                                <button class="btn btn-success w-100 mb-2 rounded-pill shadow-sm" onclick="ImportManager.resolveOne(${row.id}, 'overwrite')">
                                    Use New <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                                <button class="btn btn-light border w-100 rounded-pill" onclick="ImportManager.resolveOne(${row.id}, 'skip')">
                                    <i class="fas fa-times me-1"></i> Keep Old
                                </button>
                            </div>

                            <div class="col-md-5">
                                <div class="p-3 rounded bg-light border">
                                    <span class="badge bg-secondary mb-2">CURRENT DATABASE</span>
                                    <p class="mb-1 text-muted">${row.old_question}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-history me-1"></i> Used in ${row.usage_count} exams
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            });
    },

    // 5. Resolve Conflict Action
    resolveOne: function (stagingId, action) {
        fetch('/api/admin/quiz/import/resolve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: stagingId, action: action })
        })
            .then(res => res.json())
            .then(data => {
                // Remove the card visually with animation
                const card = document.getElementById(`card-${stagingId}`);
                card.style.transition = 'all 0.5s';
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 500);

                // Decrement counter
                let count = parseInt(document.getElementById('dupCount').innerText);
                document.getElementById('dupCount').innerText = count - 1;
            });
    },

    resolveAll: function (action) {
        if (!confirm(`Are you sure you want to ${action} ALL duplicates?`)) return;

        // Fetch all IDs currently visible
        const cards = document.querySelectorAll('.duplicate-card');
        cards.forEach(card => {
            const id = card.id.replace('card-', '');
            ImportManager.resolveOne(id, action);
        });
    },

    // 6. Bulk Publish Clean
    publishClean: function () {
        if (!confirm("Are you sure you want to publish all clean questions?")) return;

        fetch('/api/admin/quiz/import/publish-clean', {
            method: 'POST',
            body: JSON.stringify({ batch_id: this.batchId })
        }).then(() => {
            alert("Success! Questions are live.");
            window.location.reload();
        });
    }
};

// Start the Engine
document.addEventListener('DOMContentLoaded', () => ImportManager.init());
