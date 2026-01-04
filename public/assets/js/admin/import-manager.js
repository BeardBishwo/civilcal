/**
 * PREMIUM IMPORT MANAGER ENGINE
 * Optimized for high-density UI interactions and real-time conflict handling.
 */
const ImportManager = {
    batchId: null,
    currentTab: 'clean',

    init: function () {
        const input = document.getElementById('fileInput');
        if (input) {
            input.addEventListener('change', this.handleFileSelect.bind(this));
        }
    },

    handleFileSelect: function (e) {
        const file = e.target.files[0];
        if (!file) return;

        // UI Transition
        const progressContainer = document.getElementById('uploadProgress');
        if (progressContainer) progressContainer.style.display = 'block';

        document.getElementById('progressBar').style.width = '0%';

        Swal.fire({
            title: 'Initializing Ingestion',
            text: 'Analyzing engineering questionnaire structure...',
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false
        });

        this.uploadChunk(file, 2); // Start at row 2
    },

    uploadChunk: function (file, startRow) {
        const formData = new FormData();
        formData.append('import_file', file);
        formData.append('start_row', startRow);
        if (this.batchId) {
            formData.append('batch_id', this.batchId);
        }

        fetch(`${baseUrl}/api/admin/quiz/import/process-chunk`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    Swal.fire('Ingestion Failed', data.error, 'error');
                    return;
                }

                if (!this.batchId && data.batch_id) {
                    this.batchId = data.batch_id;
                }

                const percent = Math.min(100, Math.round((data.current_row / data.total_rows) * 100));
                document.getElementById('progressBar').style.width = percent + '%';
                document.getElementById('progressText').innerText = percent + '%';

                if (!data.eof) {
                    this.uploadChunk(file, data.next_row);
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Analysis Complete',
                        text: 'Questionnaire staged for verification.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        this.loadStagingData(this.batchId);
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Network Core Error', 'System handshake failed.', 'error');
            });
    },

    loadStagingData: function (batchId) {
        document.getElementById('uploadSection').style.display = 'none';
        document.getElementById('stagingSection').style.display = 'block';

        fetch(`${baseUrl}/api/admin/quiz/import/staging-stats/${batchId}`)
            .then(res => res.json())
            .then(data => {
                // Update Badge Counts
                document.getElementById('cleanCount').innerText = data.clean_count;
                document.getElementById('btnCleanCount').innerText = data.clean_count;
                document.getElementById('dupCount').innerText = data.duplicate_count;
                document.getElementById('statsStaged').innerText = data.clean_count + data.duplicate_count;

                // Render Clean Table
                const cleanBody = document.getElementById('cleanTableBody');
                cleanBody.innerHTML = data.clean_rows.length > 0
                    ? data.clean_rows.map(row => `
                        <tr>
                            <td><span class="cat-tag">${row.category}</span></td>
                            <td><div class="q-preview" title="${row.question_text}">${row.question_text}</div></td>
                            <td class="text-center"><span class="type-badge">${row.type.toUpperCase()}</span></td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill" style="font-size: 0.6rem;">
                                    <i class="fas fa-check-circle me-1"></i> VERIFIED
                                </span>
                            </td>
                        </tr>
                    `).join('')
                    : `<tr><td colspan="4" class="text-center py-5 text-muted small">No clean questions found in this batch.</td></tr>`;

                // Render Duplicates Grid
                const dupContainer = document.getElementById('duplicateContainer');
                dupContainer.innerHTML = data.duplicate_rows.length > 0
                    ? data.duplicate_rows.map(row => `
                        <div class="conflict-card" id="card-${row.id}">
                            <div class="conflict-header">
                                <span class="conflict-title"><i class="fas fa-exclamation-triangle"></i> System Collision Detected</span>
                                <span class="badge bg-white text-rose border border-rose-100 px-2 py-1 rounded small">REF ID: #${row.match_id}</span>
                            </div>
                            <div class="conflict-body">
                                <div class="compare-box new">
                                    <span class="compare-label">Incoming Ingestion</span>
                                    <div class="compare-text">${row.new_question}</div>
                                    <div class="mt-2 small text-emerald fw-bold"><i class="fas fa-list-ul"></i> ${row.new_options_count} Options</div>
                                </div>
                                <div class="conflict-ops">
                                    <button class="arch-btn primary success ultra-sm w-100" onclick="ImportManager.resolveOne(${row.id}, 'overwrite')">
                                        USE NEW <i class="fas fa-arrow-right"></i>
                                    </button>
                                    <button class="arch-btn secondary ultra-sm w-100" onclick="ImportManager.resolveOne(${row.id}, 'skip')">
                                        KEEP CURRENT
                                    </button>
                                </div>
                                <div class="compare-box old">
                                    <span class="compare-label">Current Database Record</span>
                                    <div class="compare-text text-muted">${row.old_question}</div>
                                    <div class="mt-2 small text-slate-400 font-italic"><i class="fas fa-history"></i> Used in ${row.usage_count} sessions</div>
                                </div>
                            </div>
                        </div>
                    `).join('')
                    : `<div class="text-center py-5 text-muted small"><i class="fas fa-check-circle fa-3x mb-3 opacity-20"></i><br>Zero system collisions detected. Batch is clean.</div>`;

                // Switch to duplicate tab if only duplicates exist
                if (data.clean_count === 0 && data.duplicate_count > 0) {
                    this.switchTab('duplicates');
                }
            });
    },

    switchTab: function (tab) {
        this.currentTab = tab;
        document.getElementById('tabClean').classList.toggle('active', tab === 'clean');
        document.getElementById('tabDup').classList.toggle('active', tab === 'duplicates');

        document.getElementById('paneClean').style.display = tab === 'clean' ? 'block' : 'none';
        document.getElementById('paneDuplicates').style.display = tab === 'duplicates' ? 'block' : 'none';

        document.getElementById('cleanActions').style.display = tab === 'clean' ? 'block' : 'none';
        document.getElementById('dupActions').style.display = tab === 'duplicates' ? 'block' : 'none';
    },

    resolveOne: function (stagingId, action) {
        fetch(`${baseUrl}/api/admin/quiz/import/resolve`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: stagingId, action: action })
        })
            .then(res => res.json())
            .then(data => {
                const card = document.getElementById(`card-${stagingId}`);
                if (card) {
                    card.style.transition = '0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.remove();
                        this.updateCounts();
                    }, 400);
                }
            });
    },

    resolveAll: function (action) {
        Swal.fire({
            title: `Batch Resolution: ${action.toUpperCase()}`,
            text: `Are you sure you want to apply this protocol to ALL staged conflicts?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Execute Protocol',
            confirmButtonColor: action === 'overwrite' ? '#e11d48' : '#6366f1'
        }).then((result) => {
            if (result.isConfirmed) {
                const cards = document.querySelectorAll('.conflict-card');
                cards.forEach(card => {
                    const id = card.id.replace('card-', '');
                    this.resolveOne(id, action);
                });
                Swal.fire('Protocol Executed', 'Conflicts are being resolved in the background.', 'success');
            }
        });
    },

    updateCounts: function () {
        const count = document.querySelectorAll('.conflict-card').length;
        document.getElementById('dupCount').innerText = count;
        if (count === 0) {
            document.getElementById('duplicateContainer').innerHTML = `<div class="text-center py-5 text-muted small"><i class="fas fa-check-circle fa-3x mb-3 opacity-20 text-emerald"></i><br>All collisions resolved.</div>`;
        }
    },

    publishClean: function () {
        Swal.fire({
            title: 'Deploy to Production?',
            text: "All verified questions will be moved to the live question bank.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Publish All',
            confirmButtonColor: '#10b981'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Deploying...', didOpen: () => Swal.showLoading() });
                fetch(`${baseUrl}/api/admin/quiz/import/publish-clean`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ batch_id: this.batchId })
                }).then(() => {
                    Swal.fire({ icon: 'success', title: 'Mission Success', text: 'Questions are now live.', timer: 1500, showConfirmButton: false })
                        .then(() => window.location.reload());
                });
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', () => ImportManager.init());
