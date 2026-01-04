<?php
/**
 * PREMIUM TERMINOLOGY MANAGER (WORD BANK)
 * Professional, high-density layout with integrated terminology entry.
 */
$words = $words ?? [];
$uniqueLangs = array_unique(array_column($words, 'language'));
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-book-medical"></i>
                    <h1>Terminology Vault</h1>
                </div>
                <div class="header-subtitle"><?php echo count($words); ?> Terms • <?php echo count($uniqueLangs); ?> Languages • Technical Concept Repository</div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">TERMS</span>
                    <span class="value"><?php echo count($words); ?></span>
                </div>
                <div class="stat-pill success">
                    <span class="label">LANGS</span>
                    <span class="value"><?php echo count($uniqueLangs); ?></span>
                </div>
            </div>
        </div>

        <!-- Single Row Term Creation Toolbar -->
        <div class="creation-toolbar">
            <h5 class="toolbar-title">Add Engineering Term</h5>
            <form id="wordForm" class="creation-form">
                
                <!-- Term Input -->
                <div class="input-group-premium" style="flex: 2; min-width: 150px;">
                    <i class="fas fa-terminal icon"></i>
                    <input type="text" name="term" class="form-input-premium" placeholder="Technical Term" required>
                </div>
                
                <!-- Definition Input -->
                <div class="input-group-premium" style="flex: 4; min-width: 250px;">
                    <i class="fas fa-align-left icon"></i>
                    <input type="text" name="definition" class="form-input-premium" placeholder="Definition / Explanation" required>
                </div>

                <!-- Difficulty Select -->
                <div class="input-group-premium" style="flex: 1; min-width: 100px;">
                    <select name="difficulty_level" class="form-input-premium px-3" style="padding-left: 10px;">
                        <option value="1">Easy</option>
                        <option value="2">Medium</option>
                        <option value="3">Hard</option>
                    </select>
                </div>

                <!-- Language Select -->
                <div class="input-group-premium" style="flex: 1; min-width: 80px;">
                    <select name="language" class="form-input-premium px-3" style="padding-left: 10px;">
                        <option value="en">EN</option>
                        <option value="np">NP</option>
                    </select>
                </div>

                <button type="submit" class="btn-create-premium">
                    <i class="fas fa-save"></i> SAVE
                </button>
            </form>
        </div>

        <!-- Search Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search terminology..." id="word-search" onkeyup="filterWords()">
                </div>
            </div>
            <div class="toolbar-right">
                <div class="drag-hint"><i class="fas fa-info-circle"></i> Terms are used for Blueprint Builder matches</div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th>Term / Concept</th>
                            <th>Contextual Definition</th>
                            <th class="text-center" style="width: 120px;">Level</th>
                            <th class="text-center" style="width: 80px;">Lang</th>
                            <th class="text-center" style="width: 80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="wordBankBody">
                        <?php if (empty($words)): ?>
                            <tr><td colspan="6" class="empty-cell">
                                <div class="empty-state-compact">
                                    <i class="fas fa-ghost"></i>
                                    <h3>Empty Vault</h3>
                                    <p>No technical terms recorded yet. Start building your bank above.</p>
                                </div>
                            </td></tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($words as $word): ?>
                                <tr class="word-item group">
                                    <td class="text-center align-middle">
                                        <span class="text-xs font-bold text-slate-400"><?php echo $i++; ?></span>
                                    </td>
                                    <td>
                                        <div class="item-info">
                                            <div class="item-icon">
                                                <i class="fas fa-microchip"></i>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?php echo htmlspecialchars($word['term']); ?></div>
                                                <div class="item-slug"><?php echo htmlspecialchars($word['synonyms'] ?: 'Standard Term'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="metric-text" style="max-width: 400px; line-height: 1.4; color: #64748b;">
                                            <?php echo htmlspecialchars($word['definition']); ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if($word['difficulty_level'] == 1): ?>
                                            <span class="diff-badge diff-easy">EASY</span>
                                        <?php elseif($word['difficulty_level'] == 2): ?>
                                            <span class="diff-badge diff-medium">MEDIUM</span>
                                        <?php else: ?>
                                            <span class="diff-badge diff-hard">HARD</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="lang-tag"><?php echo strtoupper($word['language']); ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="actions-compact justify-center">
                                            <button onclick="deleteWord(<?php echo $word['id']; ?>)" class="action-btn-icon delete-btn" title="Purge Record">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const baseUrl = '<?php echo app_base_url(); ?>';

document.getElementById('wordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    
    Swal.fire({
        title: 'Saving Term...',
        didOpen: () => Swal.showLoading(),
        allowOutsideClick: false
    });

    try {
        const resp = await fetch(`${baseUrl}/admin/quiz/word-bank/store`, { method: 'POST', body: fd });
        const data = await resp.json();
        if(data.success) {
            Swal.fire({ icon: 'success', title: 'Vault Updated', timer: 1000, showConfirmButton: false }).then(() => location.reload());
        } else {
            Swal.fire('Error', data.error, 'error');
        }
    } catch(e) { Swal.fire('Error', 'Server Communication Failure', 'error'); }
});

function deleteWord(id) {
    Swal.fire({
        title: 'Purge Term?',
        text: "This mapping will be permanently removed from the builder.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#cbd5e1',
        confirmButtonText: 'Purge'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch(`${baseUrl}/admin/quiz/word-bank/delete/${id}`, {method:'POST'});
                const d = await res.json();
                if(d.success) {
                    Swal.fire({ icon: 'success', title: 'Purged', timer: 1000, showConfirmButton: false }).then(() => location.reload());
                } else {
                    Swal.fire('Error', d.error, 'error');
                }
            } catch(e) { Swal.fire('Error', 'Network Failure', 'error'); }
        }
    });
}

function filterWords() {
    const query = document.getElementById('word-search').value.toLowerCase();
    document.querySelectorAll('.word-item').forEach(el => {
        const text = el.innerText.toLowerCase();
        el.style.display = text.indexOf(query) > -1 ? '' : 'none';
    });
}
</script>

<style>
/* ========================================
   PREMIUM CORE STYLES (Synchronized)
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-gray-50: #f8f9fa;
    --admin-gray-200: #e5e7eb;
    --admin-gray-300: #d1d5db;
    --admin-gray-400: #9ca3af;
    --admin-gray-600: #4b5563;
    --admin-gray-800: #1f2937;
    --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.admin-wrapper-container {
    padding: 1rem;
    background: var(--admin-gray-50);
    min-height: calc(100vh - 70px);
}

.admin-content-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    overflow: hidden;
    padding-bottom: 2rem;
}

/* Header */
.compact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
    color: white;
}
.header-left { display: flex; flex-direction: column; }
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.25rem; opacity: 0.9; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    display: flex; flex-direction: column; align-items: center;
    min-width: 80px;
}
.stat-pill.success { background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); }
.stat-pill .label { font-size: 0.65rem; font-weight: 700; opacity: 0.9; text-transform: uppercase; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

/* Creation Toolbar */
.creation-toolbar {
    padding: 1rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid var(--admin-gray-200);
}
.toolbar-title { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.75rem; }
.creation-form { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }

.input-group-premium { position: relative; }
.input-group-premium .icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; pointer-events: none; }
.form-input-premium {
    width: 100%; height: 40px; padding: 0 0.75rem 0 2.25rem; font-size: 0.875rem; 
    border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: 0.2s;
    background: white; color: #334155;
}
.form-input-premium:focus { border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }

.btn-create-premium {
    height: 40px; padding: 0 1.5rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 600; border: none; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; gap: 0.5rem; transition: 0.2s; white-space: nowrap;
}
.btn-create-premium:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

/* Table Compact */
.compact-toolbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.75rem 2rem; background: #eff6ff; border-bottom: 1px solid #bfdbfe;
}
.search-compact { position: relative; width: 300px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.85rem; }
.search-compact input {
    width: 100%; height: 36px; padding: 0 0.75rem 0 2.25rem; font-size: 0.85rem;
    border: 1px solid #bfdbfe; border-radius: 6px; outline: none;
}
.drag-hint { font-size: 0.75rem; color: #64748b; font-weight: 600; }

.table-compact { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.table-compact th {
    padding: 0.75rem 1.5rem; text-align: left; font-weight: 600; color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;
}
.table-compact td { padding: 0.8rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

.item-info { display: flex; align-items: center; gap: 0.75rem; }
.item-icon {
    width: 32px; height: 32px; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: var(--admin-primary);
}
.item-title { font-weight: 700; color: #1e293b; line-height: 1.2; }
.item-slug { font-size: 0.7rem; color: #94a3b8; font-family: monospace; }

.diff-badge { font-size: 0.6rem; font-weight: 800; padding: 2px 8px; border-radius: 10px; }
.diff-easy { background: #d1fae5; color: #065f46; }
.diff-medium { background: #fef3c7; color: #92400e; }
.diff-hard { background: #fee2e2; color: #991b1b; }

.lang-tag { font-size: 0.65rem; font-weight: 800; color: #334155; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0; }

.action-btn-icon {
    width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;
}
.delete-btn:hover { background: #fee2e2; color: #ef4444; border-color: #fecaca; transform: translateY(-1px); }

.empty-state-compact { padding: 3rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5; }

@media (max-width: 1024px) {
    .creation-form { flex-direction: column; align-items: stretch; }
}
</style>
