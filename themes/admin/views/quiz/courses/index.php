<?php
/**
 * PREMIUM COURSES MANAGEMENT
 * Professional, high-density layout with integrated creation form.
 */
$courses = $courses ?? [];
$stats = $stats ?? ['total' => 0, 'active' => 0];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-university"></i>
                    <h1>Courses Manager</h1>
                </div>
                <div class="header-subtitle"><?php echo $stats['total']; ?> Courses • <?php echo $stats['active']; ?> Active</div>
            </div>
            <!-- Stats in Header for Space Efficiency -->
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

        <!-- Single Row Creation Toolbar -->
        <div class="creation-toolbar">
            <h5 class="toolbar-title">Create New Course</h5>
            <form id="addCourseForm" class="creation-form">
                
                <!-- Title Input -->
                <div class="input-group-premium" style="flex: 3; min-width: 200px;">
                    <i class="fas fa-heading icon"></i>
                    <input type="text" name="title" class="form-input-premium" placeholder="Course Name (e.g. Civil Engineering)" required>
                </div>
                
                <!-- Icon Input -->
                <div class="input-group-premium" style="flex: 2; min-width: 150px;">
                    <i class="fas fa-icons icon"></i>
                    <input type="text" name="icon" class="form-input-premium" placeholder="Icon (fa-graduation-cap)" value="fa-graduation-cap">
                </div>

                <!-- Image Input -->
                <div class="input-group-premium" style="flex: 2; min-width: 150px;">
                    <input type="text" name="image" id="catImage" class="form-input-premium" placeholder="Image URL (Optional)" style="padding-left: 10px;">
                    <button type="button" class="btn-icon-inside" onclick="MediaManager.open('catImage')">
                        <i class="fas fa-image"></i>
                    </button>
                </div>

                <button type="button" onclick="saveCourse()" class="btn-create-premium">
                    <i class="fas fa-plus"></i> ADD
                </button>
            </form>
        </div>

        <!-- Filter & Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search courses..." id="course-search" onkeyup="filterCourses()">
                </div>
            </div>
            <div class="toolbar-right">
                <div class="drag-hint"><i class="fas fa-arrows-alt"></i> Drag handle to reorder</div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th style="width: 60px;" class="text-center">ID</th>
                            <th style="width: 60px;" class="text-center">Order</th>
                            <th>Course Info</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="courseSortable">
                        <?php if (empty($courses)): ?>
                            <tr><td colspan="6" class="empty-cell">
                                <div class="empty-state-compact">
                                    <i class="fas fa-folder-open"></i>
                                    <h3>No courses found</h3>
                                    <p>Create your first course above.</p>
                                </div>
                            </td></tr>
                        <?php else: ?>
                            <?php foreach ($courses as $c): ?>
                                <tr class="course-item group" data-id="<?php echo $c['id']; ?>">
                                    <td class="text-center align-middle">
                                        <div class="handle"><i class="fas fa-grip-vertical"></i></div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="text-xs font-bold text-slate-400"><?php echo $c['id']; ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="text-xs font-bold text-slate-500"><?php echo $c['order_index']; ?></span>
                                    </td>
                                    <td>
                                        <div class="item-info">
                                            <div class="item-icon">
                                                <?php if (!empty($c['image_path'])): ?>
                                                    <img src="<?php echo htmlspecialchars($c['image_path']); ?>">
                                                <?php else: ?>
                                                    <i class="fas <?php echo $c['icon'] ?? 'fa-graduation-cap'; ?>"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?php echo htmlspecialchars($c['title']); ?></div>
                                                <div class="item-slug"><?php echo htmlspecialchars($c['slug']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="premium-control">
                                            <label class="switch scale-sm">
                                                <input type="checkbox" class="status-toggle" data-id="<?php echo $c['id']; ?>" <?php echo $c['is_active'] ? 'checked' : ''; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="actions-compact justify-center">
                                            <button onclick="editCourse(<?php echo $c['id']; ?>)" class="action-btn-icon edit-btn" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button onclick="deleteCourse(<?php echo $c['id']; ?>)" class="action-btn-icon delete-btn" title="Delete">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
async function saveCourse() {
    const form = document.getElementById('addCourseForm');
    const title = form.querySelector('input[name="title"]').value;
    
    if(!title) { Swal.fire({ icon:'warning', title:'Missing Info', text:'Course Name is required.', timer:2000, showConfirmButton: false}); return; }

    const formData = new FormData(form);
    try {
        const response = await fetch('<?php echo app_base_url('admin/quiz/courses/store'); ?>', { method: 'POST', body: formData });
        const d = await response.json();
        if(d.status === 'success') {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
            Toast.fire({ icon: 'success', title: 'Course Created' }).then(() => location.reload());
        } else {
            Swal.fire('Error', d.message, 'error');
        }
    } catch(e) { Swal.fire('Error', 'Server Error', 'error'); }
}

function deleteCourse(id) {
    Swal.fire({
        title: 'Delete Course?', text: "This will remove all linked educational levels.", icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#cbd5e1', confirmButtonText: 'Delete'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const res = await fetch('<?php echo app_base_url('admin/quiz/courses/delete/'); ?>' + id, {method:'POST'});
            const d = await res.json();
            if(d.status==='success') location.reload();
        }
    });
}

new Sortable(document.getElementById('courseSortable'), {
    animation: 150, handle: '.handle', ghostClass: 'bg-indigo-50',
    onEnd: function() {
        const rows = document.querySelectorAll('.course-item');
        const order = Array.from(rows).map(el => el.getAttribute('data-id'));
        
        rows.forEach((row, index) => {
            const orderCell = row.querySelectorAll('td')[2].querySelector('span'); 
            if(orderCell) orderCell.innerText = index + 1;
        });

        fetch('<?php echo app_base_url('admin/quiz/courses/reorder'); ?>', {
            method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({order: order})
        });
    }
});

document.querySelectorAll('.status-toggle').forEach(el => {
    el.addEventListener('change', async function() {
        const formData = new URLSearchParams();
        formData.append('id', this.dataset.id);
        formData.append('val', this.checked ? 1 : 0);
        await fetch('<?php echo app_base_url('admin/quiz/courses/toggle-status'); ?>', {
           method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:formData
        });
    });
});

function filterCourses() {
    const query = document.getElementById('course-search').value.toLowerCase();
    document.querySelectorAll('.course-item').forEach(el => {
        const text = el.innerText.toLowerCase();
        el.style.display = text.indexOf(query) > -1 ? '' : 'none';
    });
}

function editCourse(id) {
    Swal.fire('Information', 'Inline editing coming soon.', 'info');
}
</script>

<style>
/* ========================================
   PREMIUM CORE STYLES (MATCHING CATEGORIES)
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

/* Creation Toolbar (Single Row) */
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
.btn-icon-inside {
    position: absolute; right: 4px; top: 4px; bottom: 4px; width: 32px; 
    border: none; background: #f1f5f9; border-radius: 6px; color: #64748b; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: 0.2s;
}
.btn-icon-inside:hover { background: #e2e8f0; color: #4338ca; }

.btn-create-premium {
    height: 40px; padding: 0 1.5rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.2s;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); white-space: nowrap;
}
.btn-create-premium:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

/* Switch Toggle */
.premium-toggle-group {
    display: flex; align-items: center; gap: 0.5rem; background: white; border: 1px solid #cbd5e1;
    height: 40px; padding: 0 0.75rem; border-radius: 8px;
}
.switch { position: relative; display: inline-block; width: 34px; height: 18px; margin: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #4f46e5; }
input:checked + .slider:before { transform: translateX(16px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }

/* Filter Bar */
.compact-toolbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.75rem 2rem; background: #eff6ff; border-bottom: 1px solid #bfdbfe;
}
.search-compact { position: relative; width: 100%; max-width: 300px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.85rem; }
.search-compact input {
    width: 100%; height: 36px; padding: 0 0.75rem 0 2.25rem; font-size: 0.85rem;
    border: 1px solid #bfdbfe; border-radius: 6px; outline: none; background: white; color: #1e40af;
}
.drag-hint { font-size: 0.75rem; font-weight: 600; color: #64748b; display: flex; align-items: center; gap: 0.5rem; }

/* Table */
.table-compact { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.table-compact th {
    background: white; padding: 0.75rem 1.5rem; text-align: left; font-weight: 600;
    color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}
.table-compact td {
    padding: 0.6rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle;
}
.course-item:hover { background: #f8fafc; }

.item-info { display: flex; align-items: center; gap: 0.75rem; }
.item-icon {
    width: 36px; height: 36px; border-radius: 8px; background: #f1f5f9; border: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center; overflow: hidden; color: #94a3b8;
}
.item-icon img { width: 100%; height: 100%; object-fit: cover; }
.item-title { font-weight: 600; color: #334155; line-height: 1.2; }
.item-slug { font-size: 0.75rem; color: #94a3b8; font-family: monospace; }
.actions-compact { display: flex; gap: 0.5rem; }
.action-btn-icon {
    width: 28px; height: 28px; border-radius: 6px; border: none; background: transparent;
    color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;
}
.action-btn-icon:hover { background: #f1f5f9; color: #4f46e5; }
.edit-btn:hover { color: #3b82f6; background: #eff6ff; }
.delete-btn:hover { color: #ef4444; background: #fef2f2; }
.handle { cursor: grab; color: #cbd5e1; }
.handle:active { cursor: grabbing; }

.empty-state-compact { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
.empty-state-compact i { font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.5; }
.empty-state-compact h3 { margin: 0 0 0.5rem 0; color: #64748b; font-size: 1.1rem; }
.empty-state-compact p { font-size: 0.9rem; margin: 0; }
</style>
