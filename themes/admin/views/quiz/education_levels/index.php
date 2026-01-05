<?php
/**
 * EDUCATION LEVELS MANAGER - PREMIUM ADMIN VIEW
 */
?>

<div class="premium-dashboard-wrapper">
    <!-- Header -->
    <div class="premium-header">
        <div class="header-left">
            <div class="breadcrumb-item">
                <i class="fas fa-cubes text-indigo-400"></i>
                <span>Quiz System</span>
            </div>
            <h1>Education Levels</h1>
            <p>Define qualification levels (Bachelor, Diploma, TSLC) per course.</p>
        </div>
        <div class="header-actions">
            <div class="stat-pill-group">
                <div class="stat-pill">
                    <span class="label">TOTAL</span>
                    <span class="value"><?php echo $stats['total']; ?></span>
                </div>
            </div>
            
            <button class="btn-premium-primary" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> New Level
            </button>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="premium-content-body">
        
        <!-- Table Card -->
        <div class="glass-card table-section">
            <div class="card-header-row">
                <h3><i class="fas fa-graduation-cap"></i> Qualification Levels</h3>
                <div class="filter-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="levelSearch" placeholder="Search levels...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="premium-table" id="levelsTable">
                    <thead>
                        <tr>
                            <th width="50" class="text-center"><i class="fas fa-bars"></i></th>
                            <th>Level Name</th>
                            <th>Parent Course</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-levels">
                        <?php if (empty($levels)): ?>
                        <tr>
                            <td colspan="6" class="empty-cell">
                                <div class="empty-state">
                                    <i class="fas fa-layer-group"></i>
                                    <p>No education levels defined.</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($levels as $level): ?>
                            <tr data-id="<?php echo $level['id']; ?>">
                                <td class="drag-handle text-center"><i class="fas fa-grip-vertical"></i></td>
                                <td>
                                    <div class="cell-flex-row">
                                        <div class="cell-icon-box" style="background: #ecfdf5; color: #059669;">
                                            <i class="fas <?php echo $level['icon'] ?? 'fa-user-graduate'; ?>"></i>
                                        </div>
                                        <div class="cell-info">
                                            <span class="text-main fw-bold"><?php echo htmlspecialchars($level['title']); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-status" style="background: #eff6ff; color: #2563eb;">
                                        <?php echo htmlspecialchars($level['course_title'] ?? 'Unknown'); ?>
                                    </span>
                                </td>
                                <td><span class="badge-code"><?php echo $level['slug']; ?></span></td>
                                <td>
                                    <div class="toggle-switch-wrapper">
                                        <label class="toggle-switch">
                                            <input type="checkbox" onchange="toggleStatus(<?php echo $level['id']; ?>, this)" <?php echo $level['is_active'] ? 'checked' : ''; ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <button class="btn-icon-danger" onclick="deleteLevel(<?php echo $level['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Modal -->
<div id="createModal" class="premium-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Education Level</h3>
            <button class="close-btn" onclick="closeCreateModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="createForm" onsubmit="handleCreate(event)">
                <div class="form-group">
                    <label>Level Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Bachelor in Civil Engineering" required autofocus>
                </div>

                <div class="form-group">
                    <label>Select Course <span class="text-danger">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-book"></i>
                        <select name="course_id" class="form-control" required>
                            <option value="">-- Select Course --</option>
                            <?php foreach($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Icon Class</label>
                    <div class="input-with-icon">
                        <i class="fas fa-icons"></i>
                        <input type="text" name="icon" class="form-control" value="fa-user-graduate">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-ghost" onclick="closeCreateModal()">Cancel</button>
                    <button type="submit" class="btn-premium-primary">Create Level</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop" id="modalBackdrop" onclick="closeCreateModal()"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
const BASE_URL = '<?php echo app_base_url(); ?>';

function openCreateModal() {
    document.getElementById('createModal').classList.add('active');
    document.getElementById('modalBackdrop').classList.add('active');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.remove('active');
    document.getElementById('modalBackdrop').classList.remove('active');
}

function toggleStatus(id, checkbox) {
    const val = checkbox.checked ? 1 : 0;
    fetch(BASE_URL + 'admin/quiz/education-levels/toggle-status', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}&val=${val}`
    });
}

async function handleCreate(e) {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    
    if(!form.title.value || !form.course_id.value) return;

    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
    btn.disabled = true;

    try {
        const formData = new FormData(form);
        const res = await fetch(BASE_URL + 'admin/quiz/education-levels/store', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        
        if (data.status === 'success') {
            window.location.reload();
        } else {
            alert(data.message || 'Error creating level');
            btn.innerHTML = 'Create Level';
            btn.disabled = false;
        }
    } catch (err) {
        console.error(err);
        alert('System error');
    }
}

function deleteLevel(id) {
    if(!confirm('Are you sure? This will delete all data linked to this level!')) return;
    
    fetch(BASE_URL + 'admin/quiz/education-levels/delete/' + id, { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') window.location.reload();
            else alert('Error deleting');
        });
}

// Drag & Drop
new Sortable(document.getElementById('sortable-levels'), {
    handle: '.drag-handle',
    animation: 150,
    onEnd: function() {
        const order = Array.from(this.el.children).map(tr => tr.dataset.id);
        fetch(BASE_URL + 'admin/quiz/education-levels/reorder', {
            method: 'POST',
            body: JSON.stringify({order}),
            headers: {'Content-Type': 'application/json'}
        });
    }
});
</script>

<style>
/* PREMIUM STYLES */
.premium-dashboard-wrapper { font-family: 'Inter', sans-serif; padding: 2rem; background: #f8fafc; min-height: 100vh; }
.premium-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
.premium-header h1 { font-size: 2rem; font-weight: 800; color: #1e293b; margin: 0.5rem 0 0.5rem 0; background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.stat-pill { background: white; padding: 0.5rem 1rem; border-radius: 50px; font-weight: 600; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: inline-flex; align-items: center; gap: 0.5rem; }
.stat-pill .label { font-size: 0.7rem; color: #64748b; font-weight: 700; }
.stat-pill .value { font-size: 1.1rem; color: #10b981; }
.glass-card { background: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; overflow: hidden; }
.premium-table { width: 100%; border-collapse: collapse; }
.premium-table th { background: #f8fafc; padding: 1rem; text-align: left; font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
.premium-table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.btn-premium-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-premium-primary:hover { box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transform: translateY(-1px); }
.modal-backdrop { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 99; }
.modal-backdrop.active { display: block; }
.premium-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 100; min-width: 400px; }
.premium-modal.active { display: block; animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.modal-content { background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
@keyframes popIn { from { opacity: 0; transform: translate(-50%, -40%); } to { opacity: 1; transform: translate(-50%, -50%); } }
.cell-icon-box { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.cell-flex-row { display: flex; align-items: center; gap: 1rem; }
.badge-code { background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 0.9em; }
.badge-status { font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

/* Form Controls */
.form-group { margin-bottom: 1.25rem; }
.form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
.form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; transition: border-color 0.2s; }
.form-control:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
.input-with-icon { position: relative; }
.input-with-icon i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; }
.input-with-icon .form-control { padding-left: 2.75rem; }
.btn-ghost { background: transparent; color: #64748b; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; }
.btn-ghost:hover { background: #f1f5f9; color: #1e293b; }
.modal-footer { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; }
</style>
