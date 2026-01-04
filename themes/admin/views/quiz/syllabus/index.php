<!-- Load Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- SweetAlert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-layer-group"></i>
                    <h1 class="text-white">Syllabus Master</h1>
                </div>
                <div class="header-subtitle text-white-50"><?php echo $stats['total_positions']; ?> positions • <?php echo $stats['node_count']; ?> syllabus rules</div>
            </div>
            <div class="header-actions">
                <button onclick="openAddPositionModal()" class="btn btn-primary btn-compact bg-white text-indigo-600 border-0 fw-bold shadow-soft">
                    <i class="fas fa-plus"></i>
                    <span>New Syllabus</span>
                </button>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value text-indigo-700"><?php echo $stats['total_positions']; ?></div>
                    <div class="stat-label">Total Positions</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value text-emerald-700"><?php echo $stats['active_syllabuses']; ?></div>
                    <div class="stat-label">Active Syllabuses</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-sitemap"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value text-blue-700"><?php echo $stats['node_count']; ?></div>
                    <div class="stat-label">Global Items</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value text-amber-700"><?php echo $stats['total_questions']; ?></div>
                    <div class="stat-label">Target Weight</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Filter positions..." id="positionSearch">
                </div>
            </div>
            <div class="toolbar-right">
                <div class="view-controls">
                    <button class="view-btn active" title="Table View">
                        <i class="fas fa-list-ul"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="pages-content">
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($positions)): ?>
                        <div class="empty-state-compact py-12">
                            <i class="fas fa-folder-open text-slate-200" style="font-size: 4rem;"></i>
                            <h3 class="mt-4 text-slate-400">No syllabus structures found</h3>
                            <p class="text-slate-400">Initialize your first syllabus structure to get started</p>
                            <button onclick="openAddPositionModal()" class="btn btn-primary mt-4">
                                <i class="fas fa-plus"></i>
                                Initialize Fresh
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                        <thead>
                                            <tr>
                                                <th class="col-title ps-4">Position / Level</th>
                                                <th class="col-status text-center">Structure</th>
                                                <th class="col-status text-center">Active Nodes</th>
                                                <th class="col-status text-center">Total Weight</th>
                                                <th class="col-date text-center">Last Modified</th>
                                                <th class="col-actions text-center pe-4">Actions</th>
                                            </tr>
                                        </thead>
                                <tbody>
                                    <?php foreach ($positions as $pos): 
                                        $levelName = $pos['level'] ?? 'Unassigned / Draft';
                                        $safeLevel = urlencode($levelName);
                                    ?>
                                        <tr class="page-row cursor-pointer transition hover:bg-slate-50" onclick="window.location='<?php echo app_base_url('admin/quiz/syllabus/manage/' . $safeLevel); ?>'">
                                            <td class="ps-4">
                                                <div class="flex items-center gap-3 py-1">
                                                    <div class="icon-box shrink-0" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                                                        <i class="fas fa-user-graduate"></i>
                                                    </div>
                                                    <div class="flex items-center gap-2 overflow-hidden">
                                                        <div class="page-title-compact fw-bold whitespace-nowrap text-slate-800"><?php echo htmlspecialchars($levelName); ?></div>
                                                        <span class="text-slate-300 font-light mx-1">|</span>
                                                        <div class="small text-slate-400 font-medium italic truncate" style="font-size: 0.75rem;">PSC Standard Curriculum</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge status-active bg-indigo-50 text-indigo-700 border-indigo-100">
                                                    <?php echo $pos['total_nodes']; ?> Items
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="small fw-semibold text-slate-600">
                                                    <?php echo $pos['active_nodes']; ?> <span class="text-slate-300">/</span> <?php echo $pos['total_nodes']; ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-pill-fancy bg-white border-slate-200 text-slate-600">
                                                    <?php echo $pos['total_weight']; ?> Marks
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="date-compact text-slate-500 font-medium" style="font-size: 0.8rem;">
                                                    <?php echo date('M j, Y', strtotime($pos['last_modified'] ?? 'now')); ?>
                                                </div>
                                            </td>
                                            <td class="pe-4">
                                                <div class="actions-compact justify-center">
                                                    <a href="<?php echo app_base_url('admin/quiz/syllabus/manage/' . $safeLevel); ?>" class="action-btn-icon edit-btn" title="Edit Structure">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    <button class="action-btn-icon duplicate-btn text-amber-500 hover:bg-amber-50" onclick="event.stopPropagation(); duplicatePosition('<?php echo addslashes($levelName); ?>')" title="Duplicate Syllabus">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <button class="action-btn-icon delete-btn" onclick="event.stopPropagation(); deletePosition('<?php echo addslashes($levelName); ?>')" title="Delete Everything">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-box {
        width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s;
    }
    .page-row:hover .icon-box { transform: scale(1.1); }
    .text-muted-extra { opacity: 0.6; }
    .badge-pill-fancy {
        background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;
    }
    .shadow-soft { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
</style>

<script>
    // Search Filter
    document.getElementById('positionSearch').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('.page-row');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    function duplicatePosition(level) {
        Swal.fire({
            title: 'Duplicate Syllabus',
            text: `Create a copy of "${level}"?`,
            input: 'text',
            inputLabel: 'New Syllabus Name',
            inputValue: level + ' (Copy)',
            showCancelButton: true,
            confirmButtonText: 'Duplicate',
            confirmButtonColor: '#4f46e5',
            inputAttributes: { 'autocomplete': 'off' },
            preConfirm: (newLevel) => {
                if (!newLevel) return Swal.showValidationMessage('Name is required');
                const fd = new FormData();
                fd.append('level', level);
                fd.append('newLevel', newLevel);
                
                return fetch('<?= app_base_url("admin/quiz/syllabus/duplicate-level") ?>', {
                    method: 'POST',
                    body: fd
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') return data;
                    throw new Error(data.message);
                })
                .catch(err => Swal.showValidationMessage(err.message));
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Duplicated!',
                    text: result.value.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            }
        });
    }

    function deletePosition(level) {
        Swal.fire({
            title: 'Delete Syllabus?',
            html: `Are you sure you want to permanently delete <strong>${level}</strong> and all its structural nodes?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Delete All',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger px-4',
                cancelButton: 'btn btn-light px-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const fd = new FormData();
                fd.append('level', level);
                
                fetch('<?= app_base_url("admin/quiz/syllabus/delete-level") ?>', {
                    method: 'POST',
                    body: fd
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Communication failure', 'error'));
            }
        });
    }

    function openAddPositionModal() {
        Swal.fire({
            title: 'New Syllabus Structure',
            input: 'text',
            inputLabel: 'Level / Position Name',
            inputPlaceholder: 'e.g. Sub Engineer 5th Level',
            showCancelButton: true,
            confirmButtonText: 'Initialize Curriculum',
            confirmButtonColor: '#4f46e5',
            inputAttributes: {
                'autocomplete': 'off'
            },
            preConfirm: (level) => {
                if (!level) return Swal.showValidationMessage('Name of the level is required');
                window.location.href = '<?= app_base_url("admin/quiz/syllabus/manage/") ?>' + encodeURIComponent(level);
            }
        });
    }
</script>
