<?php
/**
 * PREMIUM TOPICS MANAGEMENT
 * Professional, high-density layout with integrated creation form.
 */
$topics = $topics ?? [];
$courses = $courses ?? [];
$educationLevels = $educationLevels ?? [];
$categories = $categories ?? [];
$subCategories = $subCategories ?? [];
$selectedCourse = $selectedCourse ?? null;
$selectedEducationLevel = $selectedEducationLevel ?? null;
$selectedCategory = $selectedCategory ?? null;
$selectedSubCategory = $selectedSubCategory ?? null;
$stats = $stats ?? ['total' => 0, 'premium' => 0, 'total_questions' => 0];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-layer-group"></i>
                    <h1>Topics</h1>
                </div>
                <div class="header-subtitle"><?php echo $stats['total']; ?> Topics • <?php echo $stats['premium']; ?> Premium • <?php echo $stats['total_questions']; ?> Questions</div>
            </div>
            <!-- Stats in Header for Space Efficiency -->
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">TOTAL</span>
                    <span class="value"><?php echo $stats['total']; ?></span>
                </div>
                <div class="stat-pill warning">
                    <span class="label">PREMIUM</span>
                    <span class="value"><?php echo $stats['premium']; ?></span>
                </div>
            </div>
        </div>

        <!-- Single Row Creation Toolbar -->
        <div class="creation-toolbar">
            <h5 class="toolbar-title">Create New Topic</h5>
            <form id="addTopicForm" class="creation-form" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: nowrap;">
                
                <!-- Sub Category Select -->
                <div class="input-group-premium" style="flex: 1.5; min-width: 150px; flex-shrink: 1;">
                    <i class="fas fa-folder icon"></i>
                    <select name="parent_id" id="parentSubCategorySelect" class="form-input-premium" required style="padding-left: 2.25rem;">
                        <option value="" disabled <?php echo !$selectedSubCategory ? 'selected' : ''; ?>>Select Sub Category...</option>
                        <?php foreach ($subCategories as $sc): ?>
                            <option value="<?php echo $sc['id']; ?>" <?php echo $selectedSubCategory == $sc['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sc['title'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Title Input -->
                <div class="input-group-premium" style="flex: 2; min-width: 150px; flex-shrink: 1;">
                    <i class="fas fa-heading icon"></i>
                    <input type="text" name="title" class="form-input-premium" placeholder="Topic Name" required>
                </div>
                
                <!-- Slug Input -->
                <div class="input-group-premium" style="flex: 1.2; min-width: 120px; flex-shrink: 1;">
                    <i class="fas fa-link icon"></i>
                    <input type="text" name="slug" class="form-input-premium" placeholder="Slug (Optional)">
                </div>

                <!-- Image Input -->
                <div class="input-group-premium" style="flex: 1.2; min-width: 120px; flex-shrink: 1;">
                    <input type="text" name="image" id="topicImage" class="form-input-premium" placeholder="Image URL" style="padding-left: 10px;">
                    <button type="button" class="btn-icon-inside" onclick="MediaManager.open('topicImage')">
                        <i class="fas fa-image"></i>
                    </button>
                </div>

                <!-- Premium Toggle -->
                <div class="premium-toggle-group" style="flex-shrink: 0;">
                    <label class="switch">
                        <input type="checkbox" name="is_premium" id="is_premium" onchange="togglePrice(this)">
                        <span class="slider round"></span>
                    </label>
                    <span class="toggle-label">PREMIUM</span>
                </div>

                <!-- Price Input -->
                <div id="price_input" class="input-group-premium" style="flex: 1; min-width: 80px; display:none;">
                    <input type="number" name="unlock_price" class="form-input-premium" placeholder="Coins" min="0">
                </div>

                <button type="button" onclick="saveTopic()" class="btn-create-premium" style="flex-shrink: 0;">
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
                        <select name="course_id" id="courseFilter" class="filter-select-search" style="width: 180px;">
                            <option value="">All Courses</option>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $selectedCourse == $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['title'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="education_level_id" id="educationLevelFilter" class="filter-select-search" style="width: 220px;">
                            <option value="">All Levels</option>
                            <?php foreach ($educationLevels as $el): ?>
                                <option value="<?php echo $el['id']; ?>" <?php echo $selectedEducationLevel == $el['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($el['title'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="category_id" id="categoryFilter" class="filter-select-search" style="width: 220px;">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $selectedCategory == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['title'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="subcategory_id" id="subCategoryFilter" class="filter-select-search" style="width: 250px;">
                            <option value="">All Sub Categories</option>
                            <?php foreach ($subCategories as $sc): ?>
                                <option value="<?php echo $sc['id']; ?>" <?php echo $selectedSubCategory == $sc['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($sc['title'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <!-- Search Input -->
                <div class="search-compact" style="margin-left: 1rem;">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search topics..." id="topic-search" onkeyup="filterTopics()">
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
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                            </th>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th style="width: 60px;" class="text-center">ID</th>
                            <th style="width: 60px;" class="text-center">Order</th>
                            <th style="width: 35%;">Topic Info</th>
                            <th class="text-center" style="width: 35%;">Sub Category</th>
                            <th class="text-center" style="width: 150px;">Questions</th>
                            <th class="text-center" style="width: 100px;">Premium</th>
                            <th class="text-center" style="width: 100px;">Status</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categorySortable">
                        <?php if (empty($topics)): ?>
                            <tr><td colspan="7" class="empty-cell">No topics found</td></tr>
                        <?php else: ?>
                            <?php 
                            $lastParent = null; 
                            foreach ($topics as $topic): 
                                $pActive = $topic['parent_active'] ?? 1;
                                $gpActive = $topic['grandparent_active'] ?? 1;
                                $isFrozen = ($pActive == 0 || $gpActive == 0);
                                $parentTitle = htmlspecialchars($topic['parent_title'] ?? 'Unassigned');
                                
                                if ($lastParent !== $parentTitle): 
                                    $lastParent = $parentTitle;
                                    $sectionId = "section-" . md5($parentTitle);
                            ?>
                                <tr class="section-header-row" id="<?php echo $sectionId; ?>" data-section-name="<?php echo $parentTitle; ?>">
                                    <td colspan="10" style="background: #f1f5f9; padding: 10px 20px; border-left: 4px solid #667eea;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleSection('<?php echo $sectionId; ?>')">
                                            <div style="font-weight: 800; color: #1e293b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                                                <i class="fas fa-layer-group" style="margin-right: 8px; color: #667eea;"></i>
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
                                <tr class="category-item group <?php echo $sectionId; ?>" data-id="<?php echo $topic['id']; ?>">
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="row-checkbox" value="<?php echo $topic['id']; ?>" onchange="updateBulkToolbar()">
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="handle"><i class="fas fa-grip-vertical"></i></div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="text-xs font-bold text-slate-400"><?php echo $topic['id']; ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="text-xs font-bold text-slate-500"><?php echo $topic['order_index']; ?></span>
                                    </td>
                                    <td>
                                        <div class="item-info">
                                            <div class="item-icon">
                                                <?php if (!empty($topic['image_path'])): ?>
                                                    <img src="<?php echo htmlspecialchars($topic['image_path'] ?? ''); ?>">
                                                <?php else: ?>
                                                    <i class="fas fa-folder"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?php echo htmlspecialchars($topic['title'] ?? ''); ?></div>
                                                <div class="item-slug"><?php echo htmlspecialchars($topic['slug'] ?? ''); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-pill">
                                            <?php echo htmlspecialchars($topic['parent_title'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="metric-text"><?php echo $topic['question_count'] ?? 0; ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="premium-control">
                                            <label class="switch scale-sm">
                                                <input type="checkbox" class="premium-toggle" data-id="<?php echo $topic['id']; ?>" <?php echo $topic['is_premium'] ? 'checked' : ''; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <?php if($topic['is_premium']): ?>
                                                <span class="price-tag"><i class="fas fa-coins"></i> <?php echo $topic['unlock_price']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php 
                                        // Freeze if Parent (Sub Category) OR Grandparent (Main Category) is inactive
                                        $parentActive = $topic['parent_active'] ?? 1;
                                        $grandActive = $topic['grandparent_active'] ?? 1;
                                        $isFrozen = ($parentActive == 0 || $grandActive == 0);
                                        ?>
                                        <div class="premium-control" <?php if($isFrozen) echo 'style="opacity: 0.6; pointer-events: none;" title="Disabled by Hierarchy"'; ?>>
                                            <label class="switch scale-sm">
                                                <input type="checkbox" class="status-toggle" data-id="<?php echo $topic['id']; ?>" <?php echo ($topic['is_active'] ?? 1) ? 'checked' : ''; ?> <?php if($isFrozen) echo 'disabled'; ?>>
                                                <span class="slider round" <?php if($isFrozen && ($topic['is_active'] ?? 1)) echo 'style="background-color: #94a3b8;"'; ?>></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="actions-compact justify-center">
                                            <button onclick="editTopic(<?php echo $topic['id']; ?>)" class="action-btn-icon edit-btn" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button onclick="deleteTopic(<?php echo $topic['id']; ?>)" class="action-btn-icon delete-btn" title="Delete">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
function togglePrice(el) {
    document.getElementById('price_input').style.display = el.checked ? 'block' : 'none';
}

async function saveTopic() {
    const form = document.getElementById('addTopicForm');
    const parent = form.querySelector('select[name="parent_id"]').value;
    const title = form.querySelector('input[name="title"]').value;
    
    if(!parent) { Swal.fire({ icon:'warning', title:'Missing Info', text:'Select Sub Category first.', timer:2000, showConfirmButton:false}); return; }
    if(!title) { Swal.fire({ icon:'warning', title:'Missing Info', text:'Topic Name is required.', timer:2000, showConfirmButton: false}); return; }

    const formData = new FormData(form);
    try {
        const response = await fetch('<?php echo app_base_url('admin/quiz/topics/store'); ?>', { method: 'POST', body: formData });
        const d = await response.json();
        if(d.status === 'success') {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
            Toast.fire({ icon: 'success', title: 'Topic Created' }).then(() => location.reload());
        } else {
            Swal.fire('Error', d.message, 'error');
        }
    } catch(e) { Swal.fire('Error', 'Server Error', 'error'); }
}

// Custom Delete Logic with Selective Cascade (Universal)
async function deleteTopic(id) {
    try {
        // 1. Fetch Stats First
        const res = await fetch('<?php echo app_base_url('admin/quiz/topics/delete-stats/'); ?>' + id);
        const data = await res.json();
        
        if(data.status !== 'success') {
            Swal.fire('Error', 'Could not fetch topic data', 'error');
            return;
        }

        const counts = data.counts;
        const hasChildren = Object.keys(counts).length > 0;

        // 2. Prepare Modal Content
        let html = `<div class="text-left mb-4 text-sm text-slate-600">
            This topic contains the following items. Uncheck items you want to <b>PRESERVE</b> (keep). 
            Checked items will be <b>DELETED</b> forever.
        </div>`;
        
        html += `<table style="width: 100%; border-collapse: collapse; border: 2px solid #94a3b8; margin-top: 10px; background: white;">`;
        
        // Downward only: Position
        const typeConfig = [
            { key: 'position', label: 'Position Levels' }
        ];

        typeConfig.forEach(item => {
            let count = counts[item.key] || 0;
            
            // Merge counts from alternative keys
            if(item.altKeys) {
                item.altKeys.forEach(k => { count += (counts[k] || 0); });
            }

            // Always show all types as requested
            const isZero = count === 0;
            const opacityClass = isZero ? 'opacity-50 grayscale' : '';
            const bgClass = isZero ? 'bg-slate-50' : 'bg-white hover:bg-slate-50';
            
            html += `
            <tr style="background: ${isZero ? '#f8fafc' : 'white'}; opacity: ${isZero ? '0.6' : '1'}; transition: background 0.2s;">
                <td style="padding: 15px 12px; border: 1.5px solid #cbd5e1; width: 50px; text-align: center;">
                    <input type="checkbox" class="delete-type-check" value="${item.key}" ${isZero ? 'disabled' : 'checked'} style="width: 18px; height: 18px; cursor: pointer;">
                </td>
                <td style="padding: 15px 15px; border: 1.5px solid #cbd5e1; font-weight: 700; color: #1e293b; font-size: 0.9rem;">
                    ${item.label}
                </td>
                <td style="padding: 15px 15px; border: 1.5px solid #cbd5e1; text-align: right;">
                    <span style="background: #f1f5f9; padding: 5px 14px; border-radius: 20px; border: 1px solid #cbd5e1; font-weight: 800; font-size: 0.75rem; color: #64748b; display: inline-block;">
                        ${count} items
                    </span>
                </td>
            </tr>`;
        });
        html += `</table>`;

        // 3. Show Modal
        Swal.fire({
            title: 'Delete Topic?',
            html: html,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Delete Selected'
        }).then(async (result) => {
            if (result.isConfirmed) {
                const deleteTypes = [];
                document.querySelectorAll('.delete-type-check:checked').forEach(el => deleteTypes.push(el.value));
                
                const payload = {
                    delete_types: deleteTypes
                };

                const delRes = await fetch('<?php echo app_base_url('admin/quiz/topics/delete/'); ?>' + id, {
                    method:'POST', 
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(payload)
                });
                
                const d = await delRes.json();
                if(d.status==='success') {
                    Swal.fire({icon:'success', title:'Deleted', showConfirmButton:false, timer:1000})
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', 'Deletion failed', 'error');
                }
            }
        });

    } catch(e) {
        console.error(e);
        Swal.fire('Error', 'Network Error', 'error');
    }
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
        title: `Delete ${ids.length} Topics?`, text: "This cannot be undone.", icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Delete All'
    }).then(async (result) => {
        if (result.isConfirmed) {
            await fetch('<?php echo app_base_url('admin/quiz/topics/bulk-delete'); ?>', {
                method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ids: ids})
            });
            location.reload();
        }
    });
}

async function bulkDuplicate() {
    const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(el => el.value);
    if(ids.length === 0) return;

    await fetch('<?php echo app_base_url('admin/quiz/topics/duplicate'); ?>', {
        method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ids: ids})
    });
    location.reload();
}

new Sortable(document.getElementById('categorySortable'), {
    animation: 150, handle: '.handle', ghostClass: 'bg-indigo-50',
    onEnd: function() {
        const rows = document.querySelectorAll('.category-item');
        const order = Array.from(rows).map(el => el.getAttribute('data-id'));
        
        rows.forEach((row, index) => {
            const orderCell = row.querySelectorAll('td')[3].querySelector('span'); // Index 3 is Order (Checkbox=0, Handle=1, ID=2, Order=3)
            if(orderCell) orderCell.innerText = index + 1;
        });

        fetch('<?php echo app_base_url('admin/quiz/topics/reorder'); ?>', {
            method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({order: order})
        });
    }
});

document.querySelectorAll('.premium-toggle').forEach(el => {
    el.addEventListener('change', async function() {
        const formData = new URLSearchParams();
        formData.append('id', this.dataset.id);
        formData.append('val', this.checked ? 1 : 0);
        await fetch('<?php echo app_base_url('admin/quiz/topics/toggle-premium'); ?>', {
           method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:formData
        });
        location.reload();
    });
});

    document.querySelectorAll('.status-toggle').forEach(el => {
        el.addEventListener('change', async function() {
            const formData = new URLSearchParams();
            formData.append('id', this.dataset.id);
            formData.append('val', this.checked ? 1 : 0);
            await fetch('<?php echo app_base_url('admin/quiz/topics/toggle-status'); ?>', {
               method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:formData
            });
            // location.reload(); // Optional
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

buildSectionSidebar();

function filterTopics() {
    const query = document.getElementById('topic-search').value.toLowerCase();
    document.querySelectorAll('.category-item').forEach(el => {
        const text = el.innerText.toLowerCase();
        el.style.display = text.indexOf(query) > -1 ? '' : 'none';
    });
}

function editTopic(id) {
    Swal.fire('Information', 'Inline editing coming soon.', 'info');
}
</script>

<style>
/* ========================================
   PREMIUM CORE STYLES (Extracted from Users)
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

/* Creation Toolbar (Single Row) */
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
.btn-icon-inside {
    position: absolute; right: 4px; top: 4px; bottom: 4px; width: 32px; 
    border: none; background: #f1f5f9; border-radius: 6px; color: #64748b; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: 0.2s;
}
.btn-icon-inside:hover { background: #e2e8f0; color: #4338ca; }

.btn-create-premium {
    height: 34px; padding: 0 1.25rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.2s;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); white-space: nowrap;
}
.btn-create-premium:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

/* Switch Toggle */
.premium-toggle-group {
    display: flex; align-items: center; gap: 0.5rem; background: white; border: 1px solid #cbd5e1;
    height: 34px; padding: 0 0.75rem; border-radius: 8px;
}
.switch { position: relative; display: inline-block; width: 34px; height: 18px; margin: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #4f46e5; }
input:checked + .slider:before { transform: translateX(16px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }
.toggle-label { font-size: 0.7rem; font-weight: 700; color: #64748b; }

/* Filter Bar */
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
.search-compact { position: relative; width: 100%; max-width: 300px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.85rem; }
.search-compact input {
    width: 100%; height: 32px; padding: 0 0.75rem 0 2.25rem; font-size: 0.85rem;
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
.category-item:hover { background: #f8fafc; }

.item-info { display: flex; align-items: center; gap: 0.75rem; }
.item-icon {
    width: 36px; height: 36px; border-radius: 8px; background: #f1f5f9; border: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center; overflow: hidden; color: #94a3b8;
}
.item-icon img { width: 100%; height: 100%; object-fit: cover; }
.item-title { font-weight: 600; color: #334155; line-height: 1.2; }
.item-slug { font-size: 0.75rem; color: #94a3b8; font-family: monospace; }
.badge-pill {
    background: #e0e7ff; color: #4338ca; padding: 6px 12px; border-radius: 12px;
    font-size: 0.7rem; font-weight: 700; border: 1px solid #c7d2fe; 
    white-space: normal; line-height: 1.4; max-width: 100%; 
    display: inline-block; text-align: center; word-wrap: break-word;
    min-height: 24px; vertical-align: middle;
}

.metric-text { font-weight: 700; color: #64748b; font-size: 0.8rem; }

.premium-control { display: flex; flex-direction: column; align-items: center; gap: 2px; }
.price-tag { font-size: 0.65rem; font-weight: 700; color: #d97706; display: flex; align-items: center; gap: 2px; }
.scale-sm { transform: scale(0.8); }

.handle { cursor: grab; color: #cbd5e1; }
.handle:hover { color: #667eea; }

.action-btn-icon {
    width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px;
    background: white; color: #94a3b8; cursor: pointer; display: flex; align-items: center;
    justify-content: center; transition: 0.2s;
}
.action-btn-icon:hover { transform: translateY(-1px); }
.edit-btn:hover { color: #3b82f6; background: #eff6ff; }
.delete-btn:hover { color: #ef4444; background: #fef2f2; }

.empty-state-compact { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
.empty-state-compact i { font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.5; }
.empty-state-compact h3 { margin: 0 0 0.5rem 0; color: #64748b; font-size: 1.1rem; }
.empty-state-compact p { font-size: 0.9rem; margin: 0; }

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
    .creation-form { flex-direction: row; overflow-x: auto; padding-bottom: 5px; }
    .input-group-premium { width: auto !important; }
}
</style>

<!-- Select2 for Searchable Dropdowns -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
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
    
    $('#parentSubCategorySelect').select2({
        placeholder: 'Select Sub Category...',
        dropdownParent: $('.creation-toolbar')
    }).on('select2:open', function() {
        setTimeout(() => {
            const searchField = document.querySelector('.select2-search__field');
            if (searchField) searchField.focus();
        }, 100);
    });
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
</script>
<style>
.select2-container--default .select2-selection--single {
    height: 34px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 0.75rem; display: flex; align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 34px; padding-left: 0; color: #334155; font-size: 0.875rem;
}
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 32px; }
.select2-dropdown { border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.5rem; font-size: 0.875rem;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #667eea; }
.select2-container--default .select2-results__option[aria-selected=true] { background-color: #e0e7ff; color: #4338ca; }
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
