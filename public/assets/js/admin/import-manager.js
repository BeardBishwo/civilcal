/**
 * IMPORT MANAGER - Enterprise Question Ingestion System
 * Handles chunked uploads, staging queue, and conflict resolution
 */

const ImportManager = {
    currentBatch: null,
    currentFile: null,
    totalRows: 0,
    processedRows: 0,

    /**
     * Initialize the import manager
     */
    init() {
        const fileInput = document.getElementById('fileInput');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    this.handleFileSelect(e.target.files[0]);
                }
            });
        }

        // Drag and drop support
        const uploadCard = document.querySelector('.upload-card');
        if (uploadCard) {
            uploadCard.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadCard.style.borderColor = '#6366f1';
                uploadCard.style.background = '#f5f3ff';
            });

            uploadCard.addEventListener('dragleave', () => {
                uploadCard.style.borderColor = '#e2e8f0';
                uploadCard.style.background = '#f8fafc';
            });

            uploadCard.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadCard.style.borderColor = '#e2e8f0';
                uploadCard.style.background = '#f8fafc';
                
                if (e.dataTransfer.files.length > 0) {
                    this.handleFileSelect(e.dataTransfer.files[0]);
                }
            });
        }
    },

    /**
     * Handle file selection
     */
    handleFileSelect(file) {
        // Validate file type
        const validTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'text/csv'
        ];

        if (!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
            Swal.fire('Invalid File', 'Please upload an Excel (.xlsx, .xls) or CSV file.', 'error');
            return;
        }

        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire('File Too Large', 'Maximum file size is 10MB. Please split your data into smaller files.', 'error');
            return;
        }

        this.currentFile = file;
        this.startUpload();
    },

    /**
     * Start chunked upload process
     */
    async startUpload() {
        // Show progress UI
        document.getElementById('uploadProgress').style.display = 'block';
        
        this.processedRows = 0;
        let startRow = 2; // Skip header
        let eof = false;

        try {
            while (!eof) {
                const result = await this.uploadChunk(startRow);
                
                if (result.error) {
                    throw new Error(result.error);
                }

                this.currentBatch = result.batch_id;
                this.processedRows = result.current_row - 1; // -1 because we skip header
                
                // Update progress
                const progress = Math.min(100, (this.processedRows / 1000) * 100); // Estimate
                this.updateProgress(progress, `Processed ${this.processedRows} rows...`);

                eof = result.eof;
                startRow = result.next_row;

                // Small delay to prevent overwhelming the server
                await new Promise(resolve => setTimeout(resolve, 100));
            }

            // Upload complete - load staging
            this.updateProgress(100, 'Upload complete! Loading staging queue...');
            
            setTimeout(() => {
                this.loadStaging(this.currentBatch);
            }, 1000);

        } catch (error) {
            console.error('Upload error:', error);
            Swal.fire('Upload Failed', error.message || 'An error occurred during upload', 'error');
            document.getElementById('uploadProgress').style.display = 'none';
        }
    },

    /**
     * Upload single chunk
     */
    async uploadChunk(startRow) {
        const formData = new FormData();
        formData.append('import_file', this.currentFile);
        formData.append('start_row', startRow);

        const response = await fetch(`${baseUrl}/admin/quiz/import/upload`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    },

    /**
     * Update progress bar
     */
    updateProgress(percent, statusText) {
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const statusTextEl = document.querySelector('.status-text');

        if (progressBar) progressBar.style.width = `${percent}%`;
        if (progressText) progressText.textContent = `${Math.round(percent)}%`;
        if (statusTextEl) statusTextEl.textContent = statusText;
    },

    /**
     * Load staging data
     */
    async loadStaging(batchId) {
        try {
            const response = await fetch(`${baseUrl}/admin/quiz/import/staging/${batchId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            // Update stats
            document.getElementById('cleanCount').textContent = data.clean_count;
            document.getElementById('dupCount').textContent = data.duplicate_count;
            document.getElementById('btnCleanCount').textContent = data.clean_count;
            document.getElementById('statsStaged').textContent = data.clean_count + data.duplicate_count;

            // Populate tables
            this.populateCleanTable(data.clean_rows);
            this.populateDuplicateCards(data.duplicate_rows);

            // Show staging section
            document.getElementById('uploadSection').style.display = 'none';
            document.getElementById('stagingSection').style.display = 'block';

        } catch (error) {
            console.error('Load staging error:', error);
            Swal.fire('Error', 'Failed to load staging data', 'error');
        }
    },

    /**
     * Populate clean questions table
     */
    populateCleanTable(rows) {
        const tbody = document.getElementById('cleanTableBody');
        tbody.innerHTML = '';

        if (rows.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem; color: #94a3b8;">
                        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No clean questions found
                    </td>
                </tr>
            `;
            return;
        }

        rows.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span class="cat-tag">${row.category}</span></td>
                <td><div class="q-preview">${row.question_text}</div></td>
                <td class="text-center"><span class="type-badge">${row.type}</span></td>
                <td class="text-center"><i class="fas fa-check-circle" style="color: #10b981;"></i></td>
            `;
            tbody.appendChild(tr);
        });
    },

    /**
     * Populate duplicate conflict cards
     */
    populateDuplicateCards(rows) {
        const container = document.getElementById('duplicateContainer');
        container.innerHTML = '';

        if (rows.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fas fa-check-double" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <h3 style="color: #64748b;">No Conflicts Found</h3>
                    <p>All questions are unique and ready to publish!</p>
                </div>
            `;
            return;
        }

        rows.forEach(row => {
            const card = document.createElement('div');
            card.className = 'conflict-card';
            card.innerHTML = `
                <div class="conflict-header">
                    <div class="conflict-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Duplicate Detected (Used in ${row.usage_count} exams)
                    </div>
                </div>
                <div class="conflict-body">
                    <div class="compare-box new">
                        <div class="compare-label">📥 New Import</div>
                        <div class="compare-text">${row.new_question}</div>
                        <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #64748b;">
                            ${row.new_options_count} options • Answer: ${row.new_answer}
                        </div>
                    </div>
                    <div class="conflict-ops">
                        <button class="arch-btn secondary ultra-sm" onclick="ImportManager.resolveConflict(${row.id}, 'skip')">
                            <i class="fas fa-times"></i> Skip New
                        </button>
                        <button class="arch-btn danger-outline ultra-sm" onclick="ImportManager.resolveConflict(${row.id}, 'overwrite')">
                            <i class="fas fa-sync"></i> Overwrite
                        </button>
                    </div>
                    <div class="compare-box old">
                        <div class="compare-label">💾 Existing Question</div>
                        <div class="compare-text">${row.old_question}</div>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    },

    /**
     * Switch between tabs
     */
    switchTab(tab) {
        // Update tab buttons
        document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
        document.getElementById(tab === 'clean' ? 'tabClean' : 'tabDup').classList.add('active');

        // Update content panes
        document.getElementById('paneClean').style.display = tab === 'clean' ? 'block' : 'none';
        document.getElementById('paneDuplicates').style.display = tab === 'duplicates' ? 'block' : 'none';

        // Update action buttons
        document.getElementById('cleanActions').style.display = tab === 'clean' ? 'block' : 'none';
        document.getElementById('dupActions').style.display = tab === 'duplicates' ? 'block' : 'none';
    },

    /**
     * Resolve single conflict
     */
    async resolveConflict(id, action) {
        try {
            const response = await fetch(`${baseUrl}/admin/quiz/import/resolve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id, action })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Reload staging
            await this.loadStaging(this.currentBatch);

            Swal.fire({
                icon: 'success',
                title: action === 'skip' ? 'Skipped' : 'Overwritten',
                timer: 1000,
                showConfirmButton: false
            });

        } catch (error) {
            console.error('Resolve error:', error);
            Swal.fire('Error', 'Failed to resolve conflict', 'error');
        }
    },

    /**
     * Publish all clean questions
     */
    async publishClean() {
        const cleanCount = parseInt(document.getElementById('btnCleanCount').textContent);
        
        if (cleanCount === 0) {
            Swal.fire('No Questions', 'There are no clean questions to publish', 'info');
            return;
        }

        const result = await Swal.fire({
            title: `Publish ${cleanCount} Questions?`,
            text: 'This will move all clean questions to the live question bank.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Publish All'
        });

        if (!result.isConfirmed) return;

        try {
            Swal.fire({
                title: 'Publishing...',
                text: `Moving ${cleanCount} questions to live database...`,
                didOpen: () => { Swal.showLoading(); },
                allowOutsideClick: false
            });

            const response = await fetch(`${baseUrl}/admin/quiz/import/publish`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ batch_id: this.currentBatch })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            Swal.fire({
                icon: 'success',
                title: 'Published!',
                text: `Successfully published ${cleanCount} questions to the question bank.`,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Reload page or redirect to question bank
                window.location.href = `${baseUrl}/admin/quiz/questions`;
            });

        } catch (error) {
            console.error('Publish error:', error);
            Swal.fire('Error', 'Failed to publish questions', 'error');
        }
    },

    /**
     * Resolve all conflicts with same action
     */
    async resolveAll(action) {
        const dupCount = parseInt(document.getElementById('dupCount').textContent);
        
        if (dupCount === 0) return;

        const result = await Swal.fire({
            title: `${action === 'skip' ? 'Skip' : 'Overwrite'} All ${dupCount} Conflicts?`,
            text: action === 'skip' ? 'New questions will be discarded' : 'Existing questions will be updated',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'skip' ? '#64748b' : '#ef4444',
            confirmButtonText: action === 'skip' ? 'Skip All' : 'Overwrite All'
        });

        if (!result.isConfirmed) return;

        // Get all duplicate IDs from the DOM
        const cards = document.querySelectorAll('.conflict-card');
        const promises = [];

        cards.forEach(card => {
            const button = card.querySelector(`button[onclick*="${action}"]`);
            if (button) {
                const onclick = button.getAttribute('onclick');
                const idMatch = onclick.match(/resolveConflict\((\d+),/);
                if (idMatch) {
                    const id = parseInt(idMatch[1]);
                    promises.push(
                        fetch(`${baseUrl}/admin/quiz/import/resolve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ id, action })
                        })
                    );
                }
            }
        });

        try {
            Swal.fire({
                title: 'Processing...',
                didOpen: () => { Swal.showLoading(); },
                allowOutsideClick: false
            });

            await Promise.all(promises);

            // Reload staging
            await this.loadStaging(this.currentBatch);

            Swal.fire({
                icon: 'success',
                title: 'Complete!',
                text: `All conflicts ${action === 'skip' ? 'skipped' : 'overwritten'}`,
                timer: 1500,
                showConfirmButton: false
            });

        } catch (error) {
            console.error('Resolve all error:', error);
            Swal.fire('Error', 'Failed to resolve all conflicts', 'error');
        }
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    ImportManager.init();
});
