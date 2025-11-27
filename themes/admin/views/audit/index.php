<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-file-alt"></i> Audit Logs</h1>
            <p class="page-description">Security and activity trail with filters and export</p>
        </div>
        <div class="page-header-actions">
            <a class="btn btn-outline-primary" href="<?php echo app_base_url('/admin/audit-logs/download'); ?>?date=<?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>">
                <i class="fas fa-download"></i> Download <?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-filter"></i>
            Filter Options
        </h5>
    </div>
    <div class="card-content">
        <form method="GET" action="<?php echo app_base_url('/admin/audit-logs'); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <select class="form-select" name="date">
                        <?php foreach ($dates ?? [] as $d): ?>
                            <option value="<?php echo htmlspecialchars($d); ?>" <?php echo ($d === ($selectedDate ?? '')) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($d); ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if (empty($dates ?? [])): ?>
                            <option value="<?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>" selected>
                                <?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>
                            </option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Level</label>
                    <select class="form-select" name="level">
                        <option value="">All</option>
                        <option value="INFO" <?php echo (($level ?? '') === 'INFO' ? 'selected' : ''); ?>>INFO</option>
                        <option value="WARNING" <?php echo (($level ?? '') === 'WARNING' ? 'selected' : ''); ?>>WARNING</option>
                        <option value="ERROR" <?php echo (($level ?? '') === 'ERROR' ? 'selected' : ''); ?>>ERROR</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($q ?? ''); ?>" placeholder="Action, user, IP, details...">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Per page</label>
                    <input type="number" class="form-control" name="per_page" min="1" max="200" value="<?php echo htmlspecialchars((string)($perPage ?? 50)); ?>">
                </div>
            </div>
            
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a class="btn btn-outline-secondary" href="<?php echo app_base_url('/admin/audit-logs'); ?>">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-list"></i>
            Audit Entries
        </h5>
    </div>
    <div class="card-content p-0">
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="160">Timestamp</th>
                        <th width="100">Level</th>
                        <th width="220">Action</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entries ?? [])): ?>
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-8">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No audit entries found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($entries as $e): ?>
                            <tr>
                                <td>
                                    <code><?php echo htmlspecialchars($e['ts'] ?? ''); ?></code>
                                </td>
                                <td>
                                    <span class="badge <?php echo strtoupper($e['level'] ?? '') === 'ERROR' ? 'bg-danger' : (strtoupper($e['level'] ?? '') === 'WARNING' ? 'bg-warning' : 'bg-info'); ?>">
                                        <?php echo htmlspecialchars(strtoupper($e['level'] ?? '')); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($e['action'] ?? ''); ?></td>
                                <td>
                                    <pre class="text-xs"><?php echo htmlspecialchars(json_encode($e['details'] ?? [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)); ?></pre>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($entries ?? [])): ?>
            <div class="card-footer">
                <div class="flex justify-between items-center">
                    <div class="text-gray-600 text-sm">
                        Showing page <?php echo htmlspecialchars((string)($page ?? 1)); ?> of <?php echo htmlspecialchars((string)($pages ?? 1)); ?> 
                        (<?php echo htmlspecialchars((string)($total ?? 0)); ?> items)
                    </div>
                    <div class="btn-group">
                        <a class="btn btn-outline-secondary btn-sm <?php echo (($page ?? 1) <= 1 ? 'disabled' : ''); ?>" 
                           href="<?php echo app_base_url('/admin/audit-logs'); ?>?date=<?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>&level=<?php echo htmlspecialchars($level ?? ''); ?>&q=<?php echo htmlspecialchars($q ?? ''); ?>&per_page=<?php echo htmlspecialchars($perPage ?? 50); ?>&page=<?php echo max(1, ($page ?? 1) - 1); ?>">
                            <i class="fas fa-chevron-left"></i> Prev
                        </a>
                        <a class="btn btn-outline-secondary btn-sm <?php echo (($page ?? 1) >= ($pages ?? 1) ? 'disabled' : ''); ?>" 
                           href="<?php echo app_base_url('/admin/audit-logs'); ?>?date=<?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>&level=<?php echo htmlspecialchars($level ?? ''); ?>&q=<?php echo htmlspecialchars($q ?? ''); ?>&per_page=<?php echo htmlspecialchars($perPage ?? 50); ?>&page=<?php echo min(($pages ?? 1), ($page ?? 1) + 1); ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>