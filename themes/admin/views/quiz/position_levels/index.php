<?php
/**
 * PREMIUM POSITION LEVELS MANAGEMENT
 * With Course and Education Level filtering
 */
$levels = $levels ?? [];
$stats = $stats ?? ['total' => 0, 'active' => 0];
$courses = $courses ?? [];
$educationLevels = $educationLevels ?? [];
$selectedCourse = $selectedCourse ?? null;
$selectedEducationLevel = $selectedEducationLevel ?? null;
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-layer-group"></i>
                    <h1>Position Levels</h1>
                </div>
                <div class="header-subtitle"><?php echo $stats['total']; ?> Levels • <?php echo $stats['active']; ?> Active</div>
            </div>
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">TOTAL</span>
                    <span class="value"><?php echo $stats['total']; ?></span>
                </div>
                <div class="stat-pill warning">
                    <span class="label">ACTIVE</span>
                    <span class="value"><?php echo $stats['active']; ?></span>
                </div>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="filter-group">
                    <span class="filter-label">FILTER DATA:</span>
                    <form method="GET" id="filterForm" style="margin:0; display:flex; gap:0.5rem;">
                        <select name="course_id" id="courseFilter" class="filter-select-search" style="width: 250px;">
                            <option value="">All Courses</option>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $selectedCourse == $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="education_level_id" id="educationLevelFilter" class="filter-select-search" style="width: 350px;">
                            <option value="">All Education Levels</option>
                            <?php foreach ($educationLevels as $el): ?>
                                <option value="<?php echo $el['id']; ?>" <?php echo $selectedEducationLevel == $el['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($el['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Single Row Creation Toolbar -->
        <div class="creation-toolbar">
            <h5 class="toolbar-title">Create New Position Level</h5>
            <form id="addLevelForm" class="creation-form">
                
                <!-- Course Select -->
                <div class="input-group-premium" style="flex: 2; min-width: 150px;">
                    <i class="fas fa-university icon"></i>
                    <select name="course_id" id="courseSelect" class="form-input-premium form-select-search" style="padding-left: 2.25rem;">
                        <option value="">Select Course...</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Education Level Select -->
                <div class="input-group-premium" style="flex: 2; min-width: 150px;">
                    <i class="fas fa-graduation-cap icon"></i>
                    <select name="education_level_id" id="educationLevelSelect" class="form-input-premium form-select-search" style="padding-left: 2.25rem;">
                        <option value="">Select Education Level...</option>
                        <?php foreach ($educationLevels as $el): ?>
                            <option value="<?php echo $el['id']; ?>"><?php echo htmlspecialchars($el['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Title Input -->
                <div class="input-group-premium" style="flex: 3; min-width: 200px;">
                    <i class="fas fa-heading icon"></i>
                    <input type="text" name="title" class="form-input-premium" placeholder="Level Title (e.g. Level 5 - Sub-Engineer)" required>
                </div>
                
                <!-- Level Number -->
                <div class="input-group-premium" style="flex: 1; min-width: 80px;">
                    <i class="fas fa-sort-numeric-up icon"></i>
                    <input type="number" name="level_number" class="form-input-premium" placeholder="Level #" min="0" value="0">
                </div>

                <!-- Color Picker -->
                <div class="input-group-premium" style="flex: 1; min-width: 80px;">
                    <i class="fas fa-palette icon"></i>
                    <input type="color" name="color" class="form-input-premium" value="#667eea" style="padding-left: 0.5rem;">
                </div>

                <!-- Icon -->
                <div class="input-group-premium" style="flex: 1; min-width: 100px;">
                    <i class="fas fa-icons icon"></i>
                    <input type="text" name="icon" class="form-input-premium" placeholder="fa-user" value="fa-user">
                </div>

                <button type="button" onclick="saveLevel()" class="btn-create-premium">
                    <i class="fas fa-plus"></i> ADD
                </button>
            </form>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th style="width: 40px;"></th>
                            <th style="width: 60px;">#</th>
                            <th>Title</th>
                            <th style="min-width: 220px;">Course</th>
                            <th style="min-width: 350px;">Education Level</th>
                            <th style="width: 80px;">Level #</th>
                            <th style="width: 80px;">Color</th>
                            <th style="width: 80px;">Icon</th>
                            <th style="width: 80px; text-align: center;">Status</th>
                            <th style="width: 100px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="levelSortable">
                        <?php if (!empty($levels)): ?>
                            <?php foreach ($levels as $index => $l): ?>
                                <tr class="level-item" data-id="<?php echo $l['id']; ?>">
                                    <td>
                                        <input type="checkbox" class="row-checkbox" value="<?php echo $l['id']; ?>" onchange="updateBulkToolbar()">
                                    </td>
                                    <td>
                                        <i class="fas fa-grip-vertical handle"></i>
                                    </td>
                                    <td>
                                        <span class="order-idx"><?php echo $index + 1; ?></span>
                                    </td>
                                    <td>
                                        <div class="item-info">
                                            <div>
                                                <div class="item-title"><?php echo htmlspecialchars($l['title']); ?></div>
                                                <?php if (!empty($l['description'])): ?>
                                                    <div class="item-slug"><?php echo htmlspecialchars($l['description']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($l['course_title'])): ?>
                                            <span class="badge-pill"><?php echo htmlspecialchars($l['course_title']); ?></span>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 0.75rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($l['education_level_title'])): ?>
                                            <span class="badge-pill" style="background: #fef3c7; color: #92400e; border-color: #fde68a;"><?php echo htmlspecialchars($l['education_level_title']); ?></span>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 0.75rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-pill"><?php echo $l['level_number']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div style="display: inline-block; width: 30px; height: 30px; border-radius: 6px; background: <?php echo htmlspecialchars($l['color'] ?? '#667eea'); ?>; border: 2px solid #e2e8f0;"></div>
                                    </td>
                                    <td class="text-center">
                                        <i class="fas <?php echo htmlspecialchars($l['icon'] ?? 'fa-user'); ?>" style="font-size: 1.2rem; color: <?php echo htmlspecialchars($l['color'] ?? '#667eea'); ?>;"></i>
                                    </td>
                                    <td class="text-center">
                                        <div class="premium-control">
                                            <label class="switch scale-sm">
                                                <input type="checkbox" class="status-toggle" data-id="<?php echo $l['id']; ?>" <?php echo $l['is_active'] ? 'checked' : ''; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="actions-compact justify-center">
                                            <button onclick="deleteLevel(<?php echo $l['id']; ?>)" class="action-btn-icon delete-btn" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="empty-state-compact">
                                    <i class="fas fa-layer-group"></i>
                                    <h3>No Position Levels Yet</h3>
                                    <p>Create your first position level above</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Float Bulk Toolbar -->
        <div id="bulkToolbar" class="bulk-toolbar">
            <div class="bulk-info">
                <span class="bulk-count">0</span> Selected
            </div>
            <div class="bulk-actions">
                <button onclick="bulkDuplicate()" class="btn-bulk-duplicate">
                    <i class="fas fa-copy"></i> Duplicate
                </button>
                <button onclick="bulkDelete()" class="btn-bulk-delete">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Select2 for Searchable Dropdowns -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
// Initialize Select2 on page load
$(document).ready(function() {
    // Filter dropdowns with auto-focus
    $('#courseFilter').select2({
        placeholder: 'All Courses',
        allowClear: true,
        width: '250px'
    }).on('select2:open', function() {
        // Auto-focus search input when dropdown opens
        setTimeout(() => {
            document.querySelector('.select2-search__field').focus();
        }, 100);
    }).on('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    $('#educationLevelFilter').select2({
        placeholder: 'All Education Levels',
        allowClear: true,
        width: '350px'
    }).on('select2:open', function() {
        // Auto-focus search input when dropdown opens
        setTimeout(() => {
            document.querySelector('.select2-search__field').focus();
        }, 100);
    }).on('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    // Creation form dropdowns with auto-focus
    $('#courseSelect').select2({
        placeholder: 'Select Course...',
        allowClear: true,
        dropdownParent: $('.creation-toolbar')
    }).on('select2:open', function() {
        setTimeout(() => {
            document.querySelector('.select2-search__field').focus();
        }, 100);
    });
    
    $('#educationLevelSelect').select2({
        placeholder: 'Select Education Level...',
        allowClear: true,
        dropdownParent: $('.creation-toolbar')
    }).on('select2:open', function() {
        setTimeout(() => {
            document.querySelector('.select2-search__field').focus();
        }, 100);
    });
});

async function saveLevel() {
    const form = document.getElementById('addLevelForm');
    const title = form.querySelector('input[name="title"]').value;
    
    if(!title) { 
        Swal.fire({ icon:'warning', title:'Missing Info', text:'Title is required.', timer:2000, showConfirmButton:false}); 
        return; 
    }

    const formData = new FormData(form);
    try {
        const response = await fetch('<?php echo app_base_url('admin/quiz/position-levels/store'); ?>', { method: 'POST', body: formData });
        const d = await response.json();
        if(d.status === 'success') {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
            Toast.fire({ icon: 'success', title: 'Level Created' }).then(() => location.reload());
        } else {
            Swal.fire('Error', d.message, 'error');
        }
    } catch(e) { Swal.fire('Error', 'Server Error', 'error'); }
}

function deleteLevel(id) {
    Swal.fire({
        title: 'Delete Level?', text: "This cannot be undone.", icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#cbd5e1', confirmButtonText: 'Delete'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const res = await fetch('<?php echo app_base_url('admin/quiz/position-levels/delete/'); ?>' + id, {method:'POST'});
            const d = await res.json();
            if(d.status==='success') {
                location.reload();
            } else {
                Swal.fire('Error', d.message || 'Failed to delete', 'error');
            }
        }
    });
}

// Bulk Actions
function toggleSelectAll() {
    const checked = document.getElementById('selectAll').checked;
    document.querySelectorAll('.row-checkbox').forEach(el => el.checked = checked);
    updateBulkToolbar();
}

function updateBulkToolbar() {
    const selected = document.querySelectorAll('.row-checkbox:checked').length;
    const toolbar = document.getElementById('bulkToolbar');
    document.querySelector('.bulk-count').innerText = selected;
    
    if (selected > 0) {
        toolbar.classList.add('active');
    } else {
        toolbar.classList.remove('active');
    }
}

async function bulkDelete() {
    const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(el => el.value);
    if(ids.length === 0) return;

    Swal.fire({
        title: `Delete ${ids.length} Levels?`, text: "This cannot be undone.", icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Delete All'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const res = await fetch('<?php echo app_base_url('admin/quiz/position-levels/bulk-delete'); ?>', {
                method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ids: ids})
            });
            const d = await res.json();
            if(d.status === 'warning') {
                Swal.fire('Warning', d.message, 'warning').then(() => location.reload());
            } else {
                location.reload();
            }
        }
    });
}

async function bulkDuplicate() {
    const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(el => el.value);
    if(ids.length === 0) return;

    await fetch('<?php echo app_base_url('admin/quiz/position-levels/duplicate'); ?>', {
        method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ids: ids})
    });
    location.reload();
}

new Sortable(document.getElementById('levelSortable'), {
    animation: 150, handle: '.handle', ghostClass: 'bg-indigo-50',
    onEnd: function() {
        const rows = document.querySelectorAll('.level-item');
        const order = Array.from(rows).map(el => el.getAttribute('data-id'));
        
        rows.forEach((row, index) => {
            const orderCell = row.querySelectorAll('td')[2].querySelector('span');
            if(orderCell) orderCell.innerText = index + 1;
        });

        fetch('<?php echo app_base_url('admin/quiz/position-levels/reorder'); ?>', {
            method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({order: order})
        });
    }
});

document.querySelectorAll('.status-toggle').forEach(el => {
    el.addEventListener('change', async function() {
        const formData = new URLSearchParams();
        formData.append('id', this.dataset.id);
        formData.append('val', this.checked ? 1 : 0);
        await fetch('<?php echo app_base_url('admin/quiz/position-levels/toggle-status'); ?>', {
           method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:formData
        });
    });
});
</script>

<style>
/* Premium Core Styles */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-gray-50: #f8f9fa;
    --admin-gray-200: #e5e7eb;
    --admin-gray-300: #d1d5db;
    --admin-gray-400: #9ca3af;
    --admin-gray-600: #4b5563;
    --admin-gray-800: #1f2937;
}

/* Select2 Custom Styling */
.select2-container--default .select2-selection--single {
    height: 40px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0 0.75rem;
    display: flex;
    align-items: center;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px;
    padding-left: 0;
    color: #334155;
    font-size: 0.875rem;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px;
}

.select2-dropdown {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 0.5rem;
    font-size: 0.875rem;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #667eea;
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #e0e7ff;
    color: #4338ca;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
.stat-pill.warning { background: rgba(252, 211, 77, 0.15); border-color: rgba(252, 211, 77, 0.3); }
.stat-pill .label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

.compact-toolbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.75rem 2rem; background: #eff6ff; border-bottom: 1px solid #bfdbfe;
}
.filter-group { display: flex; align-items: center; gap: 0.75rem; }
.filter-label { font-size: 0.7rem; font-weight: 700; color: #1e40af; letter-spacing: 0.5px; }
.filter-select {
    font-size: 0.85rem; font-weight: 600; color: #1e40af; border: 1px solid #93c5fd;
    border-radius: 6px; padding: 0.25rem 2rem 0.25rem 0.5rem; background: white; outline: none; height: 32px;
}

.creation-toolbar {
    padding: 1rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid var(--admin-gray-200);
}
.toolbar-title {
    font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.75rem;
}
.creation-form {
    display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
}

.input-group-premium { position: relative; }
.input-group-premium .icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; pointer-events: none; }
.form-input-premium {
    width: 100%; height: 40px; padding: 0 0.75rem 0 2.25rem; font-size: 0.875rem; 
    border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: all 0.2s;
    background: white; color: #334155;
}
.form-input-premium:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }

.btn-create-premium {
    height: 40px; padding: 0 1.5rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.2s;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); white-space: nowrap;
}
.btn-create-premium:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

/* Switch Toggle */
.switch { position: relative; display: inline-block; width: 34px; height: 18px; margin: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #4f46e5; }
input:checked + .slider:before { transform: translateX(16px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }

.table-compact { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.table-compact th {
    background: white; padding: 0.75rem 1.5rem; text-align: left; font-weight: 600;
    color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}
.table-compact td {
    padding: 0.6rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle;
}
.level-item:hover { background: #f8fafc; }

.item-info { display: flex; align-items: center; gap: 0.75rem; }
.item-title { font-weight: 600; color: #334155; line-height: 1.2; }
.item-slug { font-size: 0.75rem; color: #94a3b8; }

.badge-pill {
    background: #e0e7ff; color: #4338ca; padding: 2px 10px; border-radius: 12px;
    font-size: 0.7rem; font-weight: 700; border: 1px solid #c7d2fe; white-space: nowrap;
}

.premium-control { display: flex; flex-direction: column; align-items: center; gap: 2px; }
.scale-sm { transform: scale(0.8); }

.handle { cursor: grab; color: #cbd5e1; }
.handle:hover { color: #667eea; }
.order-idx { color: #cbd5e1; font-size: 0.75rem; font-family: monospace; }

.action-btn-icon {
    width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px;
    background: white; color: #94a3b8; cursor: pointer; display: flex; align-items: center;
    justify-content: center; transition: 0.2s;
}
.action-btn-icon:hover { transform: translateY(-1px); }
.delete-btn:hover { background: #fee2e2; color: #ef4444; border-color: #fecaca; }

.empty-state-compact { padding: 3rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5; }
.empty-state-compact h3 { font-size: 1rem; font-weight: 600; color: #64748b; margin: 0; }
.empty-state-compact p { font-size: 0.8rem; margin: 0; }

/* Bulk Toolbar */
.bulk-toolbar {
    position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%) translateY(100px);
    background: #1e293b; color: white; padding: 0.75rem 1.5rem; border-radius: 50px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 2rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); opacity: 0; z-index: 100;
}
.bulk-toolbar.active { transform: translateX(-50%) translateY(0); opacity: 1; }
.bulk-info { font-weight: 600; font-size: 0.9rem; }
.bulk-count { color: #818cf8; font-weight: 800; }
.bulk-actions { display: flex; gap: 0.5rem; }
.btn-bulk-duplicate {
    background: #4f46e5; color: white; border: none; padding: 0.5rem 1rem; border-radius: 20px;
    font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;
    transition: 0.2s;
}
.btn-bulk-duplicate:hover { background: #4338ca; }
.btn-bulk-delete {
    background: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: 20px;
    font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;
    transition: 0.2s;
}
.btn-bulk-delete:hover { background: #dc2626; }

@media (max-width: 1024px) {
    .creation-form { flex-direction: column; align-items: stretch; }
    .input-group-premium { width: 100% !important; }
}
</style>
