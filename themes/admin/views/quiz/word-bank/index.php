<?php

/**
 * PREMIUM TERMINOLOGY VAULT (WORD BANK)
 * Professional, high-density layout with integrated terminology entry.
 */
$words = $words ?? [];
$categories = $categories ?? [];
$uniqueLangs = array_unique(array_column($words, 'language'));
?>

<!-- Alpine.js Plugins -->
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
<!-- Alpine.js Core -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="wordbank-admin-root"
    x-data="wordBankManager({
        baseUrl: '<?php echo app_base_url(); ?>',
        words: <?php echo json_encode($words); ?>,
        categories: <?php echo json_encode($categories); ?>
     })"
    @keydown.escape="resetForm()">

    <div class="admin-wrapper-container">
        <div class="admin-content-wrapper">

            <!-- Compact Page Header -->
            <div class="compact-header ripple-effect">
                <div class="header-left">
                    <div class="header-title">
                        <div class="title-icon">
                            <i class="fas fa-microchip animate-pulse"></i>
                        </div>
                        <div>
                            <h1>Terminology Vault</h1>
                            <div class="header-subtitle"><span x-text="filteredWords.length"></span> Terms Managed • Professional Engineering Meta-Data</div>
                        </div>
                    </div>
                </div>

                <div class="header-right">
                    <div class="stat-pills">
                        <div class="stat-pill primary">
                            <span class="label">TERMS</span>
                            <span class="value" x-text="words.length"></span>
                        </div>
                        <div class="stat-pill success">
                            <span class="label">LANGS</span>
                            <span class="value"><?php echo count($uniqueLangs); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Flex Container for Forms and Content -->
            <div class="vault-interface">

                <!-- Sidebar Form Area (Sticky Desktop Layout) -->
                <div class="vault-controls">

                    <!-- Term Creation / Edit Form -->
                    <div class="control-card highlight-card">
                        <div class="card-header">
                            <i class="fas fa-plus-circle"></i>
                            <h3 x-text="isEdit ? 'Update Technical Term' : 'Add New Term'"></h3>
                        </div>
                        <form @submit.prevent="saveWord" class="vault-form">
                            <div class="form-grid">
                                <div class="input-field full-width">
                                    <label>Term / Concept</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-font"></i>
                                        <input type="text" x-model="form.term" placeholder="e.g. Shear Force" required>
                                    </div>
                                </div>
                                <div class="input-field full-width">
                                    <label>Definition / Contextual Explanation</label>
                                    <div class="input-wrapper">
                                        <textarea x-model="form.definition" placeholder="Detailed technical explanation..." required rows="3"></textarea>
                                    </div>
                                </div>

                                <!-- Synonyms & Language -->
                                <div class="input-field">
                                    <label>Synonyms</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-tags"></i>
                                        <input type="text" x-model="form.synonyms" placeholder="Comma separated...">
                                    </div>
                                </div>
                                <div class="input-field">
                                    <label>Language</label>
                                    <select x-model="form.language" class="vault-select">
                                        <option value="en">English (default)</option>
                                        <option value="ne">Nepali</option>
                                        <option value="hi">Hindi</option>
                                    </select>
                                </div>

                                <div class="input-field">
                                    <label>Complexity</label>
                                    <select x-model="form.difficulty_level" class="vault-select">
                                        <option value="1">🟢 Easy</option>
                                        <option value="2">🟢 Easy-Mid</option>
                                        <option value="3">🟡 Medium</option>
                                        <option value="4">🟠 Hard</option>
                                        <option value="5">🔴 Expert</option>
                                    </select>
                                </div>
                                <div class="input-field">
                                    <label>Category</label>
                                    <select x-model="form.category_id" class="vault-select">
                                        <option value="">Uncategorized</option>
                                        <template x-for="cat in categories" :key="cat.id">
                                            <option :value="cat.id" x-text="cat.title"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="input-field full-width">
                                    <label>Usage Example (Optional)</label>
                                    <div class="input-wrapper">
                                        <textarea x-model="form.usage_example" placeholder="How is this term used in a sentence?" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions space-y-2">
                                <button type="submit" class="btn-vault-primary" :disabled="loading">
                                    <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
                                    <span x-text="isEdit ? 'UPDATE TERM' : 'SAVE TO VAULT'"></span>
                                </button>
                                <button type="button" x-show="isEdit" @click="resetForm()" class="btn-vault-ghost">
                                    CANCEL
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Bulk Import Form -->
                    <div class="control-card">
                        <div class="card-header">
                            <i class="fas fa-file-import"></i>
                            <h3>Bulk Terminal Import</h3>
                        </div>
                        <form @submit.prevent="importBulk" class="vault-form">
                            <div class="input-field full-width">
                                <div class="file-drop-zone">
                                    <input type="file" id="bulk_csv" @change="handleFile" accept=".csv" class="hidden">
                                    <label for="bulk_csv" class="drop-zone-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span x-text="fileName || 'Click to upload terminology CSV'"></span>
                                    </label>
                                </div>
                                <small class="format-hint">Format: term, definition, level, category_id</small>
                            </div>
                            <button type="submit" class="btn-vault-secondary" :disabled="!fileName || loading">
                                <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-rocket'"></i>
                                IMPORT RECORDS
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Terminology List Area -->
                <div class="vault-content">

                    <!-- List Header with Search & Filter -->
                    <div class="vault-toolbar">
                        <div class="search-bar">
                            <i class="fas fa-search"></i>
                            <input type="text" x-model="search" placeholder="Search terminology database...">
                        </div>
                        <div class="filter-group">
                            <select x-model="filterCategory" class="vault-select-toolbar">
                                <option value="">All Categories</option>
                                <template x-for="cat in categories" :key="cat.id">
                                    <option :value="cat.id" x-text="cat.title"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Compact Data Grid -->
                    <div class="vault-table-container">
                        <table class="vault-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Terminology</th>
                                    <th>Category</th>
                                    <th class="hidden-mobile">Complexity</th>
                                    <th class="sticky-actions text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="filteredWords.length === 0">
                                    <tr>
                                        <td colspan="5" class="empty-cell">
                                            <div class="empty-state">
                                                <i class="fas fa-database"></i>
                                                <p>No records matching your search were found in the vault.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="(word, index) in filteredWords" :key="word.id">
                                    <tr class="vault-row-group" :class="{'expanded': expandedId === word.id}">
                                        <td><span class="row-num" x-text="index + 1"></span></td>
                                        <td>
                                            <div class="term-brief" @click="toggleRow(word.id)">
                                                <div class="term-name" x-text="word.term"></div>
                                                <div class="term-synonym" x-text="word.synonyms || 'Standard Engineering Term'"></div>
                                                <div class="term-preview visible-mobile" x-text="word.definition"></div>
                                                <div class="expand-label" x-text="expandedId === word.id ? 'Click to collapse' : 'Click to expand details'"></div>
                                            </div>
                                            <!-- Expandable Definition -->
                                            <div class="expandable-definition" x-show="expandedId === word.id" x-collapse>
                                                <div class="full-definition">
                                                    <h5>COMPREHENSIVE DEFINITION</h5>
                                                    <p x-text="word.definition"></p>

                                                    <template x-if="word.usage_example">
                                                        <div class="usage-block mt-3">
                                                            <h5>USAGE EXAMPLE</h5>
                                                            <blockquote class="italic text-slate-500 text-sm border-l-4 border-slate-200 pl-3" x-text="'\'' + word.usage_example + '\''"></blockquote>
                                                        </div>
                                                    </template>

                                                    <div class="meta-tags">
                                                        <span class="tag"><i class="fas fa-tag"></i> ID: <span x-text="word.id"></span></span>
                                                        <span class="tag"><i class="fas fa-globe"></i> LANG: <span x-text="word.language.toUpperCase()"></span></span>
                                                        <template x-if="word.synonyms">
                                                            <span class="tag"><i class="fas fa-tags"></i> SYN: <span x-text="word.synonyms"></span></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cat-cell">
                                            <span class="cat-badge" x-text="getCategoryName(word.category_id)"></span>
                                        </td>
                                        <td class="hidden-mobile">
                                            <span :class="'diff-badge level-' + word.difficulty_level" x-text="getLevelName(word.difficulty_level)"></span>
                                        </td>
                                        <td class="sticky-actions">
                                            <div class="vault-row-actions">
                                                <button @click="editWord(word)" class="btn-action edit" title="Modify Term">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <button @click="deleteWord(word.id)" class="btn-action delete" title="Purge Record">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function wordBankManager(config) {
        return {
            baseUrl: config.baseUrl,
            words: config.words,
            categories: config.categories,
            search: '',
            filterCategory: '',
            loading: false,
            isEdit: false,
            expandedId: null,
            fileName: '',
            form: {
                id: null,
                term: '',
                definition: '',
                synonyms: '',
                usage_example: '',
                language: 'en',
                difficulty_level: 3,
                category_id: ''
            },

            get filteredWords() {
                return this.words.filter(w => {
                    const searchMatch = w.term.toLowerCase().includes(this.search.toLowerCase()) ||
                        w.definition.toLowerCase().includes(this.search.toLowerCase());
                    const categoryMatch = !this.filterCategory || w.category_id == this.filterCategory;
                    return searchMatch && categoryMatch;
                });
            },

            toggleRow(id) {
                this.expandedId = this.expandedId === id ? null : id;
            },

            getCategoryName(id) {
                const cat = this.categories.find(c => c.id == id);
                return cat ? cat.title : 'Uncategorized';
            },

            getLevelName(level) {
                const levels = {
                    1: 'EASY',
                    2: 'EASY-MID',
                    3: 'MEDIUM',
                    4: 'HARD',
                    5: 'EXPERT'
                };
                return levels[level] || 'MEDIUM';
            },

            resetForm() {
                this.isEdit = false;
                this.form = {
                    id: null,
                    term: '',
                    definition: '',
                    synonyms: '',
                    usage_example: '',
                    language: 'en',
                    difficulty_level: 3,
                    category_id: ''
                };
            },

            editWord(word) {
                this.isEdit = true;
                this.form = {
                    id: word.id,
                    term: word.term,
                    definition: word.definition,
                    synonyms: word.synonyms || '',
                    usage_example: word.usage_example || '',
                    language: word.language || 'en',
                    difficulty_level: word.difficulty_level,
                    category_id: word.category_id || ''
                };
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            },

            async saveWord() {
                this.loading = true;
                const url = this.isEdit ? `${this.baseUrl}/admin/quiz/word-bank/update/${this.form.id}` : `${this.baseUrl}/admin/quiz/word-bank/store`;

                const formData = new FormData();
                formData.append('term', this.form.term);
                formData.append('definition', this.form.definition);
                formData.append('synonyms', this.form.synonyms);
                formData.append('usage_example', this.form.usage_example);
                formData.append('language', this.form.language);
                formData.append('difficulty_level', this.form.difficulty_level);
                formData.append('category_id', this.form.category_id);

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Vault Updated',
                            timer: 1000,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 1100);
                    } else {
                        Swal.fire('Error', data.error || 'Request failed', 'error');
                    }
                } catch (e) {
                    Swal.fire('Critical Error', 'Network or Server Failure', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async deleteWord(id) {
                const confirmed = await Swal.fire({
                    title: 'Purge Record?',
                    text: "This mapping will be permanently removed from the builder.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ff4d4d',
                    confirmButtonText: 'PURGE'
                });

                if (confirmed.isConfirmed) {
                    try {
                        const res = await fetch(`${this.baseUrl}/admin/quiz/word-bank/delete/${id}`, {
                            method: 'POST'
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.words = this.words.filter(w => w.id !== id);
                            Swal.fire({
                                icon: 'success',
                                title: 'Purged',
                                timer: 1000,
                                showConfirmButton: false
                            });
                        }
                    } catch (e) {
                        Swal.fire('Error', 'Communication failed', 'error');
                    }
                }
            },

            handleFile(e) {
                const file = e.target.files[0];
                this.fileName = file ? file.name : '';
            },

            async importBulk() {
                const fileInput = document.getElementById('bulk_csv');
                if (!fileInput.files[0]) return;

                this.loading = true;
                const formData = new FormData();
                formData.append('csv_file', fileInput.files[0]);

                try {
                    const res = await fetch(`${this.baseUrl}/admin/quiz/word-bank/bulk-import`, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Successful',
                            text: `${data.imported} records added.`
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Import Error', data.error, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Bulk operation failed', 'error');
                } finally {
                    this.loading = false;
                }
            }
        };
    }
</script>

<style>
    /* ========================================
   WORD BANK PREMIUM STYLES
   ======================================== */
    :root {
        --vb-primary: #6366f1;
        --vb-secondary: #4f46e5;
        --vb-emerald: #10b981;
        --vb-slate-50: #f8fafc;
        --vb-slate-100: #f1f5f9;
        --vb-slate-200: #e2e8f0;
        --vb-slate-300: #cbd5e1;
        --vb-slate-600: #475569;
        --vb-slate-700: #334155;
        --vb-slate-800: #1e293b;
        --vb-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .wordbank-admin-root {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--vb-slate-800);
        padding: 1.5rem;
        background: #f1f4f9;
    }

    .admin-content-wrapper {
        background: white;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: var(--vb-shadow);
    }

    /* Header Section */
    .compact-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        padding: 2rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .title-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .header-title h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.025em;
    }

    .header-subtitle {
        font-size: 0.95rem;
        opacity: 0.9;
        margin-top: 2px;
    }

    .stat-pills {
        display: flex;
        gap: 1rem;
    }

    .stat-pill {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 100px;
    }

    .stat-pill .label {
        font-size: 0.7rem;
        font-weight: 700;
        opacity: 0.8;
    }

    .stat-pill .value {
        font-size: 1.25rem;
        font-weight: 800;
    }

    /* Main Interface Grid */
    .vault-interface {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 1px;
        background: var(--vb-slate-200);
    }

    .vault-controls {
        background: var(--vb-slate-50);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .vault-content {
        background: white;
        padding: 2rem;
    }

    /* Control Cards */
    .control-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .highlight-card {
        border: 2px solid var(--vb-slate-100);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        color: var(--vb-slate-700);
    }

    .card-header i {
        font-size: 1.1rem;
        color: var(--vb-primary);
    }

    .card-header h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
    }

    /* Forms */
    .vault-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .full-width {
        grid-column: span 2;
    }

    .input-field label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--vb-slate-600);
        margin-bottom: 0.4rem;
        text-transform: uppercase;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--vb-slate-400);
        font-size: 0.9rem;
    }

    .input-wrapper input,
    .input-wrapper textarea,
    .vault-select {
        width: 100%;
        border: 1.5px solid var(--vb-slate-200);
        border-radius: 0.75rem;
        padding: 0.6rem 0.75rem;
        font-size: 0.9rem;
        font-weight: 500;
        outline: none;
        transition: 0.2s;
        background: var(--vb-slate-50);
    }

    .input-wrapper input {
        padding-left: 2.25rem;
    }

    .input-wrapper textarea {
        resize: none;
        overflow-y: auto;
    }

    .input-wrapper input:focus,
    .input-wrapper textarea:focus,
    .vault-select:focus {
        border-color: var(--vb-primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    /* Buttons */
    .btn-vault-primary,
    .btn-vault-secondary {
        padding: 0.8rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: 0.2s;
    }

    .btn-vault-primary {
        background: var(--vb-primary);
        color: white;
        width: 100%;
    }

    .btn-vault-primary:hover {
        background: var(--vb-secondary);
        transform: translateY(-1px);
    }

    .btn-vault-secondary {
        background: var(--vb-slate-800);
        color: white;
        width: 100%;
        margin-top: 1rem;
    }

    .btn-vault-secondary:hover {
        transform: translateY(-1px);
    }

    .btn-vault-ghost {
        background: none;
        border: none;
        color: var(--vb-slate-600);
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        margin-top: 0.5rem;
        width: 100%;
    }

    /* File Drop Zone */
    .file-drop-zone {
        border: 2px dashed var(--vb-slate-200);
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        transition: 0.2s;
        background: white;
    }

    .file-drop-zone:hover {
        border-color: var(--vb-primary);
        background: var(--vb-slate-50);
    }

    .drop-zone-content {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .drop-zone-content i {
        font-size: 1.5rem;
        color: var(--vb-primary);
    }

    .drop-zone-content span {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--vb-slate-700);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .format-hint {
        font-size: 0.7rem;
        color: var(--vb-slate-400);
        display: block;
        margin-top: 0.5rem;
        text-align: center;
    }

    /* Toolbar */
    .vault-toolbar {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .search-bar {
        position: relative;
        flex: 1;
    }

    .search-bar i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--vb-slate-400);
    }

    .search-bar input {
        width: 100%;
        padding: 0.8rem 1rem 0.8rem 2.75rem;
        border-radius: 1rem;
        border: 1.5px solid var(--vb-slate-100);
        background: var(--vb-slate-50);
        outline: none;
        font-size: 0.95rem;
    }

    .search-bar input:focus {
        border-color: var(--vb-primary);
        background: white;
    }

    .vault-select-toolbar {
        padding: 0.8rem 1.5rem;
        border-radius: 1rem;
        border: 1.5px solid var(--vb-slate-100);
        background: var(--vb-slate-50);
        outline: none;
        font-weight: 600;
    }

    /* Data Table */
    .vault-table-container {
        background: white;
        border: 1px solid var(--vb-slate-100);
        border-radius: 1rem;
        overflow-x: auto;
    }

    .vault-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .vault-table th {
        background: var(--vb-slate-50);
        padding: 1rem 1.25rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--vb-slate-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--vb-slate-100);
    }

    .vault-table td {
        padding: 1.25rem;
        vertical-align: top;
        border-bottom: 1px solid var(--vb-slate-50);
    }

    .vault-row-group {
        transition: background 0.2s;
    }

    .vault-row-group:hover {
        background: var(--vb-slate-50);
    }

    .vault-row-group.expanded {
        background: #f5f7ff;
    }

    .row-num {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--vb-slate-300);
    }

    .term-brief {
        cursor: pointer;
        position: relative;
        padding-right: 2rem;
    }

    .term-name {
        font-weight: 800;
        font-size: 1.05rem;
        color: var(--vb-slate-800);
    }

    .term-synonym {
        font-size: 0.7rem;
        color: var(--vb-slate-400);
        font-family: 'JetBrains Mono', monospace;
    }

    .expand-label {
        font-size: 0.6rem;
        font-weight: 700;
        color: var(--vb-primary);
        opacity: 0;
        transition: 0.2s;
        text-transform: uppercase;
        margin-top: 4px;
    }

    .term-brief:hover .expand-label {
        opacity: 0.8;
    }

    .visible-mobile {
        display: none;
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: var(--vb-slate-500);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Expandable Area */
    .expandable-definition {
        margin-top: 1rem;
        border-top: 1px dashed var(--vb-slate-200);
        padding-top: 1rem;
    }

    .full-definition h5 {
        font-size: 0.65rem;
        font-weight: 900;
        color: var(--vb-primary);
        margin: 0 0 0.5rem 0;
        letter-spacing: 0.1em;
    }

    .full-definition p {
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--vb-slate-700);
        margin: 0;
        white-space: pre-wrap;
    }

    .meta-tags {
        display: flex;
        gap: 0.75rem;
        margin-top: 0.75rem;
        flex-wrap: wrap;
    }

    .tag {
        font-size: 0.65rem;
        font-weight: 800;
        padding: 0.25rem 0.6rem;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 0.5rem;
        color: var(--vb-slate-600);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Badges */
    .cat-cell {
        vertical-align: middle !important;
    }

    .cat-badge {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--vb-primary);
        background: rgba(99, 102, 241, 0.1);
        padding: 0.4rem 0.8rem;
        border-radius: 0.7rem;
        white-space: nowrap;
    }

    .diff-badge {
        font-size: 0.7rem;
        font-weight: 800;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        white-space: nowrap;
    }

    .level-1 {
        background: #d1fae5;
        color: #065f46;
    }

    .level-2 {
        background: #ecfccb;
        color: #3f6212;
    }

    .level-3 {
        background: #fef3c7;
        color: #92400e;
    }

    .level-4 {
        background: #ffedd5;
        color: #9a3412;
    }

    .level-5 {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Actions */
    .sticky-actions {
        position: sticky;
        right: 0;
        background: inherit;
        z-index: 5;
        vertical-align: middle !important;
    }

    .vault-row-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .btn-action {
        width: 38px;
        height: 38px;
        border-radius: 0.75rem;
        border: 1px solid var(--vb-slate-200);
        background: white;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--vb-slate-400);
    }

    .btn-action:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .btn-action.edit:hover {
        background: var(--vb-primary);
        border-color: var(--vb-primary);
        color: white;
    }

    .btn-action.delete:hover {
        background: #fee2e2;
        border-color: #fecaca;
        color: #ef4444;
    }

    .empty-state {
        padding: 4rem;
        text-align: center;
        color: var(--vb-slate-300);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .vault-interface {
            grid-template-columns: 1fr;
        }

        .vault-controls {
            border-top: 1px solid var(--vb-slate-200);
            position: relative;
        }

        .vault-content {
            border-bottom: 1px solid var(--vb-slate-100);
        }
    }

    @media (max-width: 768px) {
        .wordbank-admin-root {
            padding: 0.5rem;
        }

        .admin-content-wrapper {
            border-radius: 1rem;
        }

        .compact-header {
            flex-direction: column;
            gap: 1.5rem;
            text-align: center;
            padding: 1.5rem;
        }

        .header-title {
            flex-direction: column;
        }

        .vault-content,
        .vault-controls {
            padding: 1.25rem;
        }

        .vault-toolbar {
            flex-direction: column;
        }

        .hidden-mobile {
            display: none;
        }

        .visible-mobile {
            display: block;
        }

        .vault-row-group td {
            padding: 1rem 0.75rem;
        }

        .cat-badge {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
        }

        .stat-pills {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    }

    .hidden {
        display: none;
    }
</style>