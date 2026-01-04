<!-- Optimized Syllabus Dashboard (Mirroring Users UI) -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-layer-group"></i>
                    <h1 class="text-white">Syllabus Master</h1>
                </div>
                <div class="header-subtitle text-white-50"><?php echo $stats['total_positions']; ?> positions • <?php echo $stats['node_count']; ?> syllabus rules</div>
            </div>
            <div class="header-actions">
                <button onclick="openAddPositionModal()" class="btn btn-primary btn-compact bg-white text-primary border-0 fw-bold">
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
                    <div class="stat-value"><?php echo $stats['total_positions']; ?></div>
                    <div class="stat-label">Total Positions</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $stats['active_syllabuses']; ?></div>
                    <div class="stat-label">Active Syllabuses</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-drafting-compass"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $stats['node_count']; ?></div>
                    <div class="stat-label">Global Items</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $stats['total_questions']; ?></div>
                    <div class="stat-label">Target Questions</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search positions..." id="positionSearch">
                </div>
            </div>
            <div class="toolbar-right">
                <div class="view-controls">
                    <button class="view-btn active" title="Table View">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="view-btn" title="Grid View">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="pages-content">
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($positions)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-folder-open"></i>
                            <h3>No syllabus structures found</h3>
                            <p>Initialize your first syllabus structure to get started</p>
                            <button onclick="openAddPositionModal()" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Initialize
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-title ps-4">Position Title</th>
                                        <th class="col-status text-center">Structure</th>
                                        <th class="col-status text-center">Nodes</th>
                                        <th class="col-status text-center">Q Weight</th>
                                        <th class="col-date">Modified</th>
                                        <th class="col-actions pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($positions as $pos): 
                                        $levelName = $pos['level'] ?? 'Unassigned / Draft';
                                        $safeLevel = urlencode($levelName);
                                    ?>
                                        <tr class="page-row" onclick="window.location='<?php echo app_base_url('admin/quiz/syllabus/manage?level=' . $safeLevel); ?>'" style="cursor: pointer;">
                                            <td class="ps-4">
                                                <div class="user-info-compact" style="display:flex; align-items:center; gap:0.75rem;">
                                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--admin-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight:600;">
                                                        <i class="fas fa-hard-hat"></i>
                                                    </div>
                                                    <div class="page-info">
                                                        <div class="page-title-compact fw-bold"><?php echo htmlspecialchars($levelName); ?></div>
                                                        <div class="small text-muted" style="font-size: 0.7rem;">PSC Technical Standard</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge status-active">
                                                    <?php echo $pos['total_nodes']; ?> Items
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="small fw-bold">
                                                    <?php echo $pos['active_nodes']; ?> / <?php echo $pos['total_nodes']; ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-primary px-3 py-1">
                                                    <?php echo $pos['total_weight']; ?> Qs
                                                </span>
                                            </td>
                                            <td>
                                                <div class="date-compact">
                                                    <?php echo date('M j, Y', strtotime($pos['last_modified'] ?? 'now')); ?>
                                                </div>
                                            </td>
                                            <td class="pe-4">
                                                <div class="actions-compact">
                                                    <a href="<?php echo app_base_url('admin/quiz/syllabus/manage?level=' . $safeLevel); ?>" class="action-btn-icon" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="action-btn-icon text-danger" onclick="event.stopPropagation(); deletePosition('<?php echo addslashes($levelName); ?>')" title="Delete">
                                                        <i class="fas fa-trash"></i>
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

<script>
    // Search Filter
    document.getElementById('positionSearch').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('.page-row');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    function deletePosition(level) {
        Swal.fire({
            title: 'Delete Syllabus?',
            text: `This will remove the entire structure for "${level}". This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Delete Everything'
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
                        Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            }
        });
    }

    function openAddPositionModal() {
        Swal.fire({
            title: 'New Syllabus Structure',
            input: 'text',
            inputLabel: 'Position Name',
            placeholder: 'e.g. Sub Engineer 5th Level',
            showCancelButton: true,
            confirmButtonText: 'Create structure',
            preConfirm: (level) => {
                if (!level) return Swal.showValidationMessage('Name is required');
                window.location.href = '<?= app_base_url("admin/quiz/syllabus/manage?level=") ?>' + encodeURIComponent(level);
            }
        });
    }
</script>
