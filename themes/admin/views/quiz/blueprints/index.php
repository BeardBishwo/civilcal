<?php
/**
 * PREMIUM BLUEPRINT VAULT (Exam Templates)
 * Professional, high-density layout for managing exam structures.
 */
$blueprints = $blueprints ?? [];
$totalBlueprints = count($blueprints);
$activeBlueprints = count(array_filter($blueprints, fn($b) => $b['is_active']));
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-drafting-compass"></i>
                    <h1>Blueprint Vault</h1>
                </div>
                <div class="header-subtitle">Manage exam structures and question distribution rules.</div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">VAULT SIZE</span>
                    <span class="value"><?php echo $totalBlueprints; ?></span>
                </div>
                <div class="stat-pill success">
                    <span class="label">ACTIVE</span>
                    <span class="value"><?php echo $activeBlueprints; ?></span>
                </div>
            </div>
        </div>

        <!-- Action Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search templates..." id="blueprint-search" onkeyup="filterBlueprints()">
                </div>
            </div>
            <div class="toolbar-right">
                <a href="<?= app_base_url('admin/quiz/blueprints/create') ?>" class="btn-create-premium">
                    <i class="fas fa-plus"></i> NEW BLUEPRINT
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 300px;">Template Info</th>
                            <th class="text-center">Level</th>
                            <th class="text-center">Composition</th>
                            <th class="text-center">Resource Rules</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="blueprintList">
                        <?php if (empty($blueprints)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state-compact">
                                        <i class="fas fa-box-open"></i>
                                        <h3>Vault is Empty</h3>
                                        <p>Click "New Blueprint" to start drafting your first exam template.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($blueprints as $blueprint): ?>
                                <tr class="blueprint-item">
                                    <td>
                                        <div class="item-info">
                                            <div class="blueprint-icon">
                                                <i class="fas fa-scroll"></i>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?= htmlspecialchars($blueprint['title']) ?></div>
                                                <div class="item-desc text-truncate" style="max-width: 200px;">
                                                    <?= !empty($blueprint['description']) ? htmlspecialchars($blueprint['description']) : 'No description provided.' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="level-badge"><?= htmlspecialchars($blueprint['level'] ?? 'Global') ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="metrics-row justify-center">
                                            <div class="metric-pill" title="Total Questions">
                                                <i class="fas fa-question-circle"></i> <?= $blueprint['total_questions'] ?>
                                            </div>
                                            <div class="metric-pill" title="Duration">
                                                <i class="fas fa-clock"></i> <?= $blueprint['duration_minutes'] ?>m
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="wildcard-text">
                                            <span class="val"><?= $blueprint['wildcard_percentage'] ?>%</span>
                                            <span class="lab">Wildcard</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="status-toggle">
                                            <label class="switch scale-sm">
                                                <input type="checkbox" <?= $blueprint['is_active'] ? 'checked' : '' ?> onchange="toggleStatus(<?= $blueprint['id'] ?>, this)">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="actions-compact justify-center">
                                            <a href="<?= app_base_url('admin/quiz/blueprints/edit/' . $blueprint['id']) ?>" class="action-btn-icon edit-btn" title="Edit Structure">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="generateExam(<?= $blueprint['id'] ?>)" class="action-btn-icon generate-btn" title="Deploy Live Exam">
                                                <i class="fas fa-bolt"></i>
                                            </button>
                                            <button onclick="deleteBlueprint(<?= $blueprint['id'] ?>)" class="action-btn-icon delete-btn" title="Purge Template">
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
async function generateExam(id) {
    const result = await Swal.fire({
        title: 'Generate Live Exam?',
        text: "This will create a new instance of an exam based on this template.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#cbd5e1',
        confirmButtonText: 'Yes, Deploy'
    });

    if (result.isConfirmed) {
        window.location = `<?= app_base_url('admin/quiz/blueprints/edit/') ?>${id}#generate`;
    }
}

async function deleteBlueprint(id) {
    const result = await Swal.fire({
        title: 'Purge Template?',
        text: "This action is irreversible and will remove the blueprint rules.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#cbd5e1',
        confirmButtonText: 'Purge Now'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`<?= app_base_url('admin/quiz/blueprints/delete/') ?>${id}`, { method: 'POST' });
            const data = await response.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Vault Updated', showConfirmButton: false, timer: 1000 }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.error || 'Failed to delete template.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Server Communication Failure', 'error');
        }
    }
}

async function toggleStatus(id, el) {
    // This could call an API to toggle status without full update if needed,
    // for now we show a placeholder alert since the backend might need an update
    Swal.fire({
        icon: 'info',
        title: 'Status Toggled',
        text: 'Persistence logic for quick-toggle will be added to controllers soon.',
        timer: 1000,
        showConfirmButton: false
    });
}

function filterBlueprints() {
    const query = document.getElementById('blueprint-search').value.toLowerCase();
    document.querySelectorAll('.blueprint-item').forEach(el => {
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
    --admin-gray-600: #4b5563;
}

.admin-wrapper-container { padding: 1rem; background: var(--admin-gray-50); min-height: calc(100vh - 70px); }
.admin-content-wrapper { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; /* padding-bottom: 2rem; REMOVED FOR CLEANER UI */ }

/* Header */
.compact-header {
    display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem;
    background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
    color: white;
}
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill {
    background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px; padding: 0.5rem 1rem; display: flex; flex-direction: column; align-items: center; min-width: 80px;
}
.stat-pill.success { background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); }
.stat-pill .label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

/* Toolbar */
.compact-toolbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1rem 2rem; background: #f8fafc; border-bottom: 1px solid var(--admin-gray-200);
}
.search-compact { position: relative; width: 300px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; }
.search-compact input {
    width: 100%; height: 38px; padding: 0 0.75rem 0 2.25rem; font-size: 0.875rem;
    border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: all 0.2s;
}
.btn-create-premium {
    height: 40px; padding: 0 1.25rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 600; font-size: 0.85rem; border: none; border-radius: 8px; text-decoration: none;
    display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
}

/* Table Design */
.table-compact { width: 100%; border-collapse: collapse; }
.table-compact th {
    background: white; padding: 0.75rem 1.5rem; text-align: left; font-weight: 600;
    color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;
}
.table-compact td { padding: 0.75rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.blueprint-item:hover { background: #f8fafc; }

/* Info Area */
.item-info { display: flex; align-items: center; gap: 1rem; }
.blueprint-icon {
    width: 40px; height: 40px; border-radius: 10px; background: #f0f7ff; color: var(--admin-primary);
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid #deeefb;
}
.item-title { font-weight: 700; color: #334155; font-size: 0.95rem; line-height: 1.2; }
.item-desc { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

/* Badges & Metrics */
.level-badge { padding: 4px 12px; background: #fef3c7; color: #92400e; font-size: 0.65rem; font-weight: 800; border-radius: 20px; text-transform: uppercase; }
.metrics-row { display: flex; gap: 8px; }
.metric-pill { padding: 4px 10px; background: #f1f5f9; border-radius: 6px; font-size: 0.75rem; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 4px; }
.metric-pill i { font-size: 0.7rem; color: #94a3b8; }

.wildcard-text { display: flex; flex-direction: column; align-items: center; line-height: 1; }
.wildcard-text .val { font-size: 0.95rem; font-weight: 800; color: var(--admin-primary); }
.wildcard-text .lab { font-size: 0.6rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-top: 2px; }

/* Actions */
.actions-compact { display: flex; gap: 6px; }
.action-btn-icon {
    width: 32px; height: 32px; border-radius: 8px; border: 1px solid #e2e8f0; background: white;
    color: #94a3b8; display: flex; align-items: center; justify-content: center; transition: 0.2s; cursor: pointer;
}
.action-btn-icon:hover { transform: translateY(-1px); }
.edit-btn:hover { background: #f0f7ff; color: var(--admin-primary); border-color: #deeefb; }
.generate-btn:hover { background: #ecfdf5; color: #10b981; border-color: #d1fae5; }
.delete-btn:hover { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }

/* Utils */
.justify-center { justify-content: center; }
.scale-sm { transform: scale(0.8); }

/* Empty State */
.empty-state-compact { padding: 3rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 3rem; opacity: 0.3; margin-bottom: 1rem; display: block; }
.empty-state-compact h3 { color: #64748b; font-size: 1.1rem; margin-bottom: 0.25rem; }

/* Switch Toggle (Standardized) */
.switch { position: relative; display: inline-block; width: 34px; height: 18px; margin: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #10b981; }
input:checked + .slider:before { transform: translateX(16px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }
</style>
