<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        
        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-file-signature"></i>
                    <h1>Exam Manager</h1>
                </div>
                <div class="header-subtitle"><?php echo $total; ?> exams available</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('admin/quiz/exams/create'); ?>" class="btn btn-primary btn-compact">
                    <i class="fas fa-plus"></i>
                    <span>Create Exam</span>
                </a>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <form method="GET" action="<?php echo app_base_url('admin/quiz/exams'); ?>" class="d-flex align-items-center gap-2" id="filter-form">
                    <div class="search-compact">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search exams..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>

                    <select name="status" class="filter-compact" onchange="document.getElementById('filter-form').submit()">
                        <option value="">All Status</option>
                        <option value="draft" <?php echo ($_GET['status'] ?? '') == 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo ($_GET['status'] ?? '') == 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="archived" <?php echo ($_GET['status'] ?? '') == 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Exams Content -->
        <div class="pages-content">
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($exams)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-file-alt"></i>
                            <h3>No exams created</h3>
                            <p>Create your first exam to start assessing users.</p>
                            <a href="<?php echo app_base_url('admin/quiz/exams/create'); ?>" class="btn btn-primary btn-compact">
                                Create Exam
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-title" style="width: 35%;">Title</th>
                                        <th class="col-status">Details</th>
                                        <th class="col-status">Metrics</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($exams as $exam): ?>
                                        <tr>
                                            <td>
                                                <div class="page-info">
                                                    <div class="page-title-compact">
                                                        <?php echo htmlspecialchars($exam['title']); ?>
                                                        <?php if($exam['is_premium']): ?>
                                                            <i class="fas fa-crown text-warning" title="Premium Exam" style="margin-left:5px; font-size: 0.8em;"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="page-slug-compact"><?php echo ucfirst(str_replace('_', ' ', $exam['type'])); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display:flex; gap: 8px;">
                                                    <span class="status-badge" style="background: var(--admin-gray-100); color: var(--admin-gray-700);">
                                                        <?php echo ucfirst($exam['mode']); ?>
                                                    </span>
                                                    <span class="status-badge" style="background: var(--admin-gray-100); color: var(--admin-gray-700);">
                                                        <i class="fas fa-clock" style="margin-right:4px;"></i>
                                                        <?php echo $exam['duration_minutes'] > 0 ? $exam['duration_minutes'] . ' m' : '∞'; ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size: 13px; font-weight: 600; color: var(--admin-gray-800);">
                                                    <?php echo $exam['total_marks']; ?> <span style="font-size: 11px; font-weight: 400; color: var(--admin-gray-500);">Marks</span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                    $statusClass = 'secondary';
                                                    if ($exam['status'] == 'published') $statusClass = 'active';
                                                    elseif ($exam['status'] == 'archived') $statusClass = 'inactive';
                                                    elseif ($exam['status'] == 'draft') $statusClass = 'warning';
                                                ?>
                                                <span class="status-badge status-<?php echo $statusClass; ?>">
                                                    <i class="fas fa-circle" style="font-size: 6px; margin-right: 4px;"></i>
                                                    <?php echo ucfirst($exam['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                    <a href="<?php echo app_base_url('admin/quiz/exams/builder/' . $exam['id']); ?>" class="btn btn-sm btn-outline-primary" style="font-size: 11px; padding: 2px 8px; border-radius: 4px; display: flex; align-items: center; gap: 4px; border: 1px solid var(--admin-primary); background: transparent; color: var(--admin-primary); margin-right: 5px;" title="Questions Builder">
                                                        <i class="fas fa-list-ol"></i> Builder
                                                    </a>
                                                    
                                                    <a href="<?php echo app_base_url('admin/quiz/exams/edit/' . $exam['id']); ?>" class="action-btn-icon edit-btn" title="Settings">
                                                        <i class="fas fa-cog"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                         <!-- Pagination -->
<?php $limit = $limit ?? 10; if ($total > $limit): ?>
                            <div class="pagination-compact">
                                <div class="pagination-info">
                                    Showing <?php echo count($exams); ?> of <?php echo $total; ?> entries
                                </div>
                                <div class="pagination-controls">
                                    <?php 
                                        $totalPages = ceil($total / $limit);
                                        $page = $page ?? 1;
                                    ?>
                                    
                                    <a href="?page=<?php echo max(1, $page - 1); ?>&status=<?php echo $_GET['status'] ?? ''; ?>&search=<?php echo $_GET['search'] ?? ''; ?>" 
                                       class="page-btn <?php echo $page <= 1 ? 'disabled' : ''; ?>"
                                       <?php echo $page <= 1 ? 'onclick="return false;"' : ''; ?>>
                                        <i class="fas fa-chevron-left"></i>
                                    </a>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>&status=<?php echo $_GET['status'] ?? ''; ?>&search=<?php echo $_GET['search'] ?? ''; ?>" 
                                           class="page-btn <?php echo $i == $page ? 'active' : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <a href="?page=<?php echo min($totalPages, $page + 1); ?>&status=<?php echo $_GET['status'] ?? ''; ?>&search=<?php echo $_GET['search'] ?? ''; ?>" 
                                       class="page-btn <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"
                                       <?php echo $page >= $totalPages ? 'onclick="return false;"' : ''; ?>>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
