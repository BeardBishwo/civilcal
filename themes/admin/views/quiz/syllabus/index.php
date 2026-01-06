<?php
/**
 * PREMIUM SYLLABUS MASTER
 * High-density layout for managing curriculum structures.
 */
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-sitemap"></i>
                    <h1>Syllabus Master</h1>
                </div>
                <div class="header-subtitle"><?php echo $stats['total_positions']; ?> Positions • <?php echo $stats['node_count']; ?> Global Items</div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px; align-items:center;">
                <!-- Header Stats -->
                <div class="stat-pill">
                    <span class="label">ACTIVE</span>
                    <span class="value"><?php echo $stats['active_syllabuses']; ?></span>
                </div>
                <div class="stat-pill info">
                    <span class="label">ITEMS</span>
                    <span class="value"><?php echo $stats['node_count']; ?></span>
                </div>
                
                <div style="width:1px; height:24px; background:rgba(255,255,255,0.2); margin:0 8px;"></div>

                <button onclick="openAddPositionModal()" class="btn btn-primary btn-compact" style="background:white; color:var(--admin-primary);">
                    <i class="fas fa-plus"></i>
                    <span>New Structure</span>
                </button>
            </div>
        </div>

        <!-- Filter & Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" id="positionSearch" placeholder="Search positions...">
                </div>
            </div>
            <div class="toolbar-right">
                <!-- View toggles or additional filters could go here -->
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 400px;">Position / Structure Name</th>
                            <th class="text-center" style="width: 150px;">Composition</th>
                            <th class="text-center" style="width: 150px;">Weightage</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th class="text-center" style="width: 150px;">Last Modified</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($positions)): ?>
                            <tr><td colspan="6" class="empty-cell">
                                <div class="empty-state-compact">
                                    <i class="fas fa-layer-group"></i>
                                    <h3>No Syllabus Structures</h3>
                                    <p>Initialize your first curriculum level to get started.</p>
                                    <button onclick="openAddPositionModal()" class="btn-create-premium mt-3">
                                        <i class="fas fa-plus"></i> Create First
                                    </button>
                                </div>
                            </td></tr>
                        <?php else: ?>
                            <?php foreach ($positions as $pos): 
                                $levelName = $pos['level'];
                                $safeLevel = urlencode($levelName);
                                $completeness = ($pos['active_nodes'] / max(1, $pos['total_nodes'])) * 100;
                            ?>
                                <tr class="syllabus-item group page-row">
                                    <td>
                                        <div class="item-info">
                                            <div class="item-icon" style="background: #fdf4ff; color: #d946ef; border-color: #fce7f3;">
                                                <i class="fas fa-user-graduate"></i>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title highlightable">
                                                    <?php echo htmlspecialchars($levelName); ?>
                                                </div>
                                                <div class="item-slug">PSC Standard Curriculum</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="config-badges">
                                            <span class="badge-pill" title="Total Nodes">
                                                <i class="fas fa-list-ul" style="margin-right:3px;"></i> <?php echo $pos['total_nodes']; ?> items
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-pill" style="background:#fff7ed; color:#ea580c; border-color:#ffedd5;">
                                            <i class="fas fa-star" style="margin-right:3px;"></i> <?php echo $pos['total_weight']; ?> Marks
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                                            <div style="font-size:0.7rem; font-weight:700; color:<?php echo $completeness == 100 ? '#16a34a' : '#64748b'; ?>">
                                                <?php echo round($completeness); ?>% Active
                                            </div>
                                            <div style="width:40px; height:4px; background:#e2e8f0; border-radius:2px; overflow:hidden;">
                                                <div style="width:<?php echo $completeness; ?>%; height:100%; background:<?php echo $completeness == 100 ? '#22c55e' : '#6366f1'; ?>;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span style="font-size:0.75rem; color:#94a3b8; font-weight:500;">
                                            <?php echo date('M j, Y', strtotime($pos['last_modified'] ?? 'now')); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="actions-compact justify-center">
                                            <a href="<?php echo app_base_url('admin/quiz/syllabus/manage/' . $safeLevel); ?>" class="action-btn-icon" title="Edit Structure" style="color: #6366f1;">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                             <button class="action-btn-icon" onclick="duplicatePosition('<?php echo addslashes($levelName); ?>')" title="Duplicate" style="color: #f59e0b;">
                                                 <i class="fas fa-copy"></i>
                                             </button>
                                             <button class="action-btn-icon" onclick="deletePosition('<?php echo addslashes($levelName); ?>')" title="Delete" style="color: #ef4444;">
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

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Search
    document.getElementById('positionSearch').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('.page-row');
        rows.forEach(row => {
            const text = row.querySelector('.highlightable').innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    function openAddPositionModal() {
        Swal.fire({
            title: 'New Syllabus Structure',
            input: 'text',
            inputLabel: 'Position Name',
            inputPlaceholder: 'e.g. Sub Engineer 5th Level',
            showCancelButton: true,
            confirmButtonText: 'Create',
            confirmButtonColor: '#4f46e5',
            preConfirm: (level) => {
                if (!level) return Swal.showValidationMessage('Name is required');
                window.location.href = '<?= app_base_url("admin/quiz/syllabus/manage/") ?>' + encodeURIComponent(level);
            }
        });
    }

    function duplicatePosition(level) {
        Swal.fire({
            title: 'Duplicate Syllabus',
            text: `Copy layout from "${level}"?`,
            input: 'text',
            inputValue: level + ' (Copy)',
            showCancelButton: true,
            confirmButtonText: 'Duplicate',
            confirmButtonColor: '#4f46e5',
            preConfirm: (newLevel) => {
                const fd = new FormData();
                fd.append('level', level);
                fd.append('newLevel', newLevel);
                return fetch('<?= app_base_url("admin/quiz/syllabus/duplicate-level") ?>', { method:'POST', body:fd })
                    .then(r => r.json())
                    .then(d => { if(d.status !== 'success') throw new Error(d.message); return d; })
                    .catch(e => Swal.showValidationMessage(e.message));
            }
        }).then((result) => {
            if(result.isConfirmed) {
                Swal.fire({ icon:'success', title:'Duplicated!', timer:1000, showConfirmButton:false }).then(() => location.reload());
            }
        });
    }

    function deletePosition(level) {
        Swal.fire({
            title: 'Delete Syllabus?',
            html: `This will delete the structural <strong>${level}</strong> syllabus.<br><br>Master filters (Sections/Units) will be preserved.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Delete Syllabus',
        }).then((result) => {
            if(result.isConfirmed) {
                const fd = new FormData(); fd.append('level', level);
                fetch('<?= app_base_url("admin/quiz/syllabus/delete-level") ?>', { method:'POST', body:fd })
                    .then(r => r.json())
                    .then(d => {
                        if(d.status === 'success') Swal.fire({ icon:'success', title:'Deleted', timer:1000, showConfirmButton:false }).then(() => location.reload());
                        else Swal.fire('Error', d.message, 'error');
                    });
            }
        });
    }
</script>

<style>
/* ========================================
   PREMIUM CORE STYLES (Consolidated)
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-gray-50: #f8f9fa;
    --admin-gray-200: #e5e7eb;
    --admin-gray-600: #4b5563;
    --admin-gray-800: #1f2937;
}

.admin-wrapper-container { padding: 1rem; background: var(--admin-gray-50); min-height: calc(100vh - 120px); }
.admin-content-wrapper { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; /* padding-bottom: 2rem; REMOVED FOR CLEANER BOTTOM */ }

/* Header */
.compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); color: white; }
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.25rem; opacity: 0.9; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 0.5rem 1rem; display: flex; flex-direction: column; align-items: center; min-width: 80px; }
.stat-pill.info { background: rgba(59, 130, 246, 0.2); border-color: rgba(59, 130, 246, 0.3); }
.stat-pill .label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

.btn-create-premium { height: 40px; padding: 0 1.5rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: white; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.2s; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); white-space: nowrap; }
.btn-create-premium:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

.compact-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 2rem; background: #eff6ff; border-bottom: 1px solid #bfdbfe; }
.search-compact { position: relative; width: 100%; max-width: 300px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.85rem; }
.search-compact input { width: 100%; height: 36px; padding: 0 0.75rem 0 2.25rem; font-size: 0.85rem; border: 1px solid #bfdbfe; border-radius: 6px; outline: none; background: white; color: #1e40af; }

.table-compact { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.table-compact th { background: white; padding: 0.75rem 1.5rem; text-align: left; font-weight: 600; color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
.table-compact td { padding: 0.6rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.syllabus-item:hover { background: #f8fafc; }

.item-info { display: flex; align-items: center; gap: 0.75rem; }
.item-icon { width: 36px; height: 36px; border-radius: 8px; background: #f1f5f9; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden; color: #94a3b8; }
.item-title { font-weight: 600; color: #334155; line-height: 1.2; }
.item-slug { font-size: 0.75rem; color: #94a3b8; }

.badge-pill { background: #e0e7ff; color: #4338ca; padding: 2px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 700; border: 1px solid #c7d2fe; white-space: nowrap; margin-right: 4px; }
.config-badges { display: flex; gap: 4px; flex-wrap: wrap; justify-content: center; }

.action-btn-icon { width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; margin: 0 2px; }
.action-btn-icon:hover { transform: translateY(-1px); border-color: #cbd5e1; }

.empty-state-compact { padding: 3rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5; }
.empty-state-compact h3 { font-size: 1rem; font-weight: 600; color: #64748b; margin: 0; }
</style>
