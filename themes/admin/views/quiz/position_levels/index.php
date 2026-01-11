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

        <!-- Single Row Creation Toolbar -->
        <div class="creation-toolbar">
            <h5 class="toolbar-title">Create New Position Level</h5>
            <form id="addLevelForm" class="creation-form" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: nowrap;">
                
                <!-- Course Select -->
                <div class="input-group-premium" style="flex: 1.5; min-width: 120px; flex-shrink: 1;">
                    <i class="fas fa-university icon"></i>
                    <select name="course_id" id="courseSelect" class="form-input-premium form-select-search" style="padding-left: 2.25rem;">
                        <option value="">Select Course...</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['title'] ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Education Level Select -->
                <div class="input-group-premium" style="flex: 1.5; min-width: 120px; flex-shrink: 1;">
                    <i class="fas fa-graduation-cap icon"></i>
                    <select name="education_level_id" id="educationLevelSelect" class="form-input-premium form-select-search" style="padding-left: 2.25rem;">
                        <option value="">Select Education Level...</option>
                        <?php foreach ($educationLevels as $el): ?>
                            <option value="<?php echo $el['id']; ?>"><?php echo htmlspecialchars($el['title'] ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Title Input -->
                <div class="input-group-premium" style="flex: 2; min-width: 150px; flex-shrink: 1;">
                    <i class="fas fa-heading icon"></i>
                    <input type="text" name="title" class="form-input-premium" placeholder="Level Title (e.g. Level 5 - Sub-Engineer)" required>
                </div>
                
                <!-- Level Number -->
                <div class="input-group-premium" style="flex: 0.5; min-width: 60px; flex-shrink: 1;">
                    <i class="fas fa-sort-numeric-up icon"></i>
                    <input type="number" name="level_number" class="form-input-premium" placeholder="Level #" min="0" value="0">
                </div>

                <!-- Color Picker -->
                <div class="input-group-premium" style="flex: 0.5; min-width: 50px; flex-shrink: 0;">
                    <i class="fas fa-palette icon"></i>
                    <input type="color" name="color" class="form-input-premium" value="#667eea" style="padding-left: 0.5rem;">
                </div>

                <!-- Icon -->
                <div class="input-group-premium" style="flex: 0.8; min-width: 80px; flex-shrink: 1;">
                    <i class="fas fa-icons icon"></i>
                    <input type="text" name="icon" class="form-input-premium" placeholder="fa-user" value="fa-user">
                </div>

                <div class="premium-toggle-group" style="flex-shrink: 0;">
                    <label class="switch scale-sm">
                        <input type="checkbox" name="is_premium" value="1">
                        <span class="slider round"></span>
                    </label>
                    <span class="toggle-label">PREMIUM</span>
                </div>

                <div class="input-group-premium" style="flex: 0.8; min-width: 80px; flex-shrink: 1;">
                    <i class="fas fa-coins icon"></i>
                    <input type="number" name="unlock_price" class="form-input-premium" placeholder="Price" min="0" value="0">
                </div>

                <button type="button" onclick="saveLevel()" class="btn-create-premium" style="flex-shrink: 0;">
                    <i class="fas fa-plus"></i> ADD
                </button>
            </form>
        </div>

        <!-- Filter & Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="filter-group">
                    <span class="filter-label">FILTER DATA:</span>
                    <form method="GET" id="filterForm" style="margin:0; display:flex; gap:0.5rem;">
                        <select name="course_id" id="courseFilter" class="filter-select-search" style="width: 250px;">
                            <option value="">All Courses</option>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $selectedCourse == $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['title'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="education_level_id" id="educationLevelFilter" class="filter-select-search" style="width: 250px;">
                            <option value="">All Education Levels</option>
                            <?php foreach ($educationLevels as $el): ?>
                                <option value="<?php echo $el['id']; ?>" <?php echo $selectedEducationLevel == $el['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($el['title'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <!-- Search Input -->
                <div class="search-compact" style="margin-left: 1rem;">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search levels..." id="level-search" onkeyup="filterLevels()">
                </div>
            </div>
            <div class="toolbar-right">
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th style="width: 40px;"></th>
                            <th style="width: 60px;">#</th>
                            <th style="width: 30%;">Title</th>
                            <th style="width: 30%;">Course</th>
                            <th style="width: 30%;">Education Level</th>
                            <th style="width: 80px;">Level #</th>
                            <th style="width: 80px;">Color</th>
                            <th style="width: 80px;">Icon</th>
                             <th style="width: 60px; text-align: center;">Premium</th>
                             <th style="width: 80px; text-align: center;">Status</th>
                             <th style="width: 100px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="levelSortable">
                        <?php if (empty($levels)): ?>
                             <tr><td colspan="12" class="empty-state-compact">
                                <i class="fas fa-layer-group"></i>
                                <h3>No Position Levels Yet</h3>
                                <p>Create your first position level above</p>
                            </td></tr>
                        <?php else: ?>
                            <?php 
                            $lastParent = null; 
                            foreach ($levels as $index => $l): 
                                $cActive = $l['course_status'] ?? 1;
                                $elActive = $l['education_level_status'] ?? 1;
                                $isFrozen = ($cActive == 0 || $elActive == 0);
                                $courseName = $l['course_title'] ?? 'Generic';
                                $eduName = $l['education_level_title'] ?? 'Core';
                                $parentTitle = htmlspecialchars("$courseName » $eduName");
                                
                                if ($lastParent !== $parentTitle): 
                                    $lastParent = $parentTitle;
                                    $sectionId = "section-" . md5($parentTitle);
                            ?>
                                <tr class="section-header-row" id="<?php echo $sectionId; ?>" data-section-name="<?php echo $parentTitle; ?>">
                                    <td colspan="12" style="background: #f1f5f9; padding: 10px 20px; border-left: 4px solid #667eea;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleSection('<?php echo $sectionId; ?>')">
                                            <div style="font-weight: 800; color: #1e293b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                                                <i class="fas fa-id-badge" style="margin-right: 8px; color: #667eea;"></i>
                                                <?php echo $parentTitle; ?>
                                                <?php if($isFrozen): ?><span style="margin-left: 10px; font-size: 0.65rem; background: #fee2e2; color: #ef4444; padding: 2px 8px; border-radius: 4px;">FROZEN (HIERARCHY DISABLED)</span><?php endif; ?>
                                            </div>
                                            <div style="color: #64748b; font-size: 0.75rem;">
                                                <i class="fas fa-chevron-down section-icon"></i>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                                <tr class="level-item group <?php echo $sectionId; ?>" data-id="<?php echo $l['id']; ?>">
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
                                                <div class="item-title"><?php echo htmlspecialchars($l['title'] ?? ''); ?></div>
                                                <?php if (!empty($l['description'])): ?>
                                                    <div class="item-slug"><?php echo htmlspecialchars($l['description'] ?? ''); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($l['course_title'])): ?>
                                            <span class="badge-pill"><?php echo htmlspecialchars($l['course_title'] ?? 'N/A'); ?></span>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 0.75rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($l['education_level_title'])): ?>
                                            <span class="badge-pill" style="background: #fef3c7; color: #92400e; border-color: #fde68a;"><?php echo htmlspecialchars($l['education_level_title'] ?? 'N/A'); ?></span>
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
                                    <td class="text-center align-middle">
                                        <div class="premium-control">
                                            <label class="switch scale-sm">
                                                <input type="checkbox" class="premium-toggle" data-id="<?php echo $l['id']; ?>" <?php echo $l['is_premium'] ? 'checked' : ''; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <?php if($l['is_premium']): ?>
                                                <span class="price-tag"><i class="fas fa-coins"></i> <?php echo $l['unlock_price']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $courseActive = $l['course_status'] ?? 1;
                                        $eduLevelActive = $l['education_level_status'] ?? 1;
                                        $isFrozen = ($courseActive == 0 || $eduLevelActive == 0);
                                        ?>
                                        <div class="premium-control" <?php if($isFrozen) echo 'style="opacity: 0.6; pointer-events: none;" title="Disabled by Parent Hierarchy"'; ?>>
                                            <label class="switch scale-sm">
                                                <input type="checkbox" class="status-toggle" data-id="<?php echo $l['id']; ?>" <?php echo $l['is_active'] ? 'checked' : ''; ?> <?php if($isFrozen) echo 'disabled'; ?>>
                                                <span class="slider round" <?php if($isFrozen && $l['is_active']) echo 'style="background-color: #94a3b8;"'; ?>></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="actions-compact justify-center">
                                            <button onclick="editLevel(<?php echo $l['id']; ?>)" class="action-btn-icon edit-btn" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button onclick="deleteLevel(<?php echo $l['id']; ?>)" class="action-btn-icon delete-btn" title="Delete">
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

        <!-- Floating Navigation Ball -->
        <div class="nav-ball-container">
            <div class="nav-ball-menu">
                <div class="nav-ball-header">QUICK JUMP</div>
                <div id="sectionLinks" class="nav-ball-links"></div>
            </div>
            <button class="nav-ball-toggle" title="Quick Navigation">
                <i class="fas fa-compass"></i>
            </button>
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
    // Select2 with Auto-Submit for Filters
    $('.filter-select-search').each(function() {
        const placeholder = $(this).find('option:first').text();
        $(this).select2({
            placeholder: placeholder,
            allowClear: true,
            width: 'resolve'
        }).on('select2:open', function() {
            setTimeout(() => {
                const searchField = document.querySelector('.select2-search__field');
                if (searchField) searchField.focus();
            }, 100);
        }).on('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
    
    // Creation form dropdowns with auto-focus
    $('#courseSelect, #parentCourseSelect').select2({
        placeholder: 'Select Course...',
        allowClear: true,
        dropdownParent: $('.creation-toolbar')
    }).on('select2:open', function() {
        setTimeout(() => {
            const searchField = document.querySelector('.select2-search__field');
            if (searchField) searchField.focus();
        }, 100);
    });
    
    $('#educationLevelSelect').select2({
        placeholder: 'Select Education Level...',
        allowClear: true,
        dropdownParent: $('.creation-toolbar')
    }).on('select2:open', function() {
        setTimeout(() => {
            const searchField = document.querySelector('.select2-search__field');
            if (searchField) searchField.focus();
        }, 100);
    });

    buildSectionSidebar();
});

// Nav Ball Interaction
$(document).on('click', '.nav-ball-toggle', function(e) {
    e.stopPropagation();
    $('.nav-ball-container').toggleClass('active');
});

$(document).on('click', function() {
    $('.nav-ball-container').removeClass('active');
});

$('.nav-ball-menu').on('click', function(e) {
    e.stopPropagation();
});

document.querySelectorAll('.premium-toggle').forEach(el => {
    el.addEventListener('change', async function() {
        const formData = new URLSearchParams();
        formData.append('id', this.dataset.id);
        formData.append('val', this.checked ? 1 : 0);
        await fetch('<?php echo app_base_url('admin/quiz/position-levels/toggle-premium'); ?>', {
           method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:formData
        });
        location.reload();
    });
});

// Section Management
function toggleSection(sectionId) {
    const rows = document.querySelectorAll('.' + sectionId);
    const header = document.getElementById(sectionId);
    const icon = header.querySelector('.section-icon');
    
    rows.forEach(r => {
        if (r.style.display === 'none') {
            r.style.display = '';
            icon.style.transform = 'rotate(0deg)';
        } else {
            r.style.display = 'none';
            icon.style.transform = 'rotate(-90deg)';
        }
    });
}

function buildSectionSidebar() {
    const sections = document.querySelectorAll('.section-header-row');
    const container = document.getElementById('sectionLinks');
    if (!container) return;
    
    container.innerHTML = '';
    sections.forEach(s => {
        const name = s.getAttribute('data-section-name');
        const id = s.id;
        const link = document.createElement('div');
        link.className = 'sidebar-link';
        link.innerText = name;
        link.onclick = () => {
            document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'center' });
            document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        };
        container.appendChild(link);
    });
}

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
    height: 34px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0 0.75rem;
    display: flex;
    align-items: center;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 34px;
    padding-left: 0;
    color: #334155;
    font-size: 0.875rem;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
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

.badge-pill {
    background: #e0e7ff; color: #4338ca; padding: 6px 12px; border-radius: 12px;
    font-size: 0.7rem; font-weight: 700; border: 1px solid #c7d2fe; 
    white-space: normal; line-height: 1.4; max-width: 100%; 
    display: inline-block; text-align: center; word-wrap: break-word;
    min-height: 24px; vertical-align: middle;
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
    /* padding-bottom: 2rem; REMOVED FOR CLEANER UI */
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
    padding: 0.4rem 2rem; background: #eff6ff; border-bottom: 1px solid #bfdbfe;
}
.filter-group { display: flex; align-items: center; gap: 0.75rem; }
.filter-label { font-size: 0.7rem; font-weight: 700; color: #1e40af; letter-spacing: 0.5px; }
.filter-select {
    font-size: 0.85rem; font-weight: 600; color: #1e40af; border: 1px solid #93c5fd;
    border-radius: 6px; padding: 0.25rem 2rem 0.25rem 0.5rem; background: white; outline: none; height: 32px;
}

.creation-toolbar {
    padding: 0.6rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid var(--admin-gray-200);
}
.toolbar-title {
    font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.4rem;
}
.creation-form {
    display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
}

.input-group-premium { position: relative; }
.input-group-premium .icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; pointer-events: none; }
.form-input-premium {
    width: 100%; height: 34px; padding: 0 0.75rem 0 2.25rem; font-size: 0.875rem; 
    border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: all 0.2s;
    background: white; color: #334155;
}
.form-input-premium:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }

.btn-create-premium {
    height: 34px; padding: 0 1.25rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
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
.price-tag { font-size: 0.65rem; font-weight: 700; color: #d97706; display: flex; align-items: center; gap: 2px; }
.premium-toggle-group {
    display: flex; align-items: center; gap: 0.5rem; background: white; border: 1px solid #cbd5e1;
    height: 40px; padding: 0 0.75rem; border-radius: 8px;
}
.toggle-label { font-size: 0.7rem; font-weight: 700; color: #64748b; }
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
    .creation-form { flex-direction: row; overflow-x: auto; padding-bottom: 5px; } /* Switched to row + scroll */
    .input-group-premium { width: auto !important; }
}
/* Floating Navigation Ball */
.nav-ball-container {
    position: fixed; bottom: 30px; right: 30px; z-index: 1000;
}
.nav-ball-toggle {
    width: 60px; height: 60px; border-radius: 50%;
    background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%);
    color: white; border: none; cursor: pointer;
    box-shadow: 0 10px 25px rgba(255, 8, 68, 0.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative; z-index: 1001;
}
.nav-ball-container.active .nav-ball-toggle { transform: rotate(45deg) scale(1.1); background: #ff0844; }

.nav-ball-menu {
    position: absolute; bottom: 80px; right: 0;
    width: 280px; background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    padding: 20px; opacity: 0; transform: translateY(20px) scale(0.9);
    pointer-events: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 500px; display: flex; flex-direction: column;
}
.nav-ball-container.active .nav-ball-menu {
    opacity: 1; transform: translateY(0) scale(1); pointer-events: all;
}
.nav-ball-header {
    font-size: 0.65rem; font-weight: 800; color: #94a3b8; letter-spacing: 1.5px;
    margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;
    text-transform: uppercase;
}
.nav-ball-links { overflow-y: auto; flex: 1; padding-right: 5px; }
.nav-ball-links::-webkit-scrollbar { width: 5px; }
.nav-ball-links::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

.sidebar-link {
    font-size: 0.8rem; font-weight: 600; color: #475569; padding: 10px 14px;
    border-radius: 10px; cursor: pointer; transition: 0.2s;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: 4px;
}
.sidebar-link:hover { background: #f1f5f9; color: #667eea; padding-left: 18px; }
.sidebar-link.active { background: #eff6ff; color: #2563eb; border-left: 4px solid #3b82f6; }

.section-icon { transition: transform 0.3s; }

@media (max-width: 768px) {
    .nav-ball-container { bottom: 20px; right: 20px; }
    .nav-ball-toggle { width: 50px; height: 50px; font-size: 1.25rem; }
    .nav-ball-menu { width: 240px; bottom: 65px; }
}
</style>
