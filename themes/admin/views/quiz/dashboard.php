<?php
/**
 * PREMIUM ADMIN DASHBOARD
 * Matching Categories Page Design
 */
?>
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-chart-pie"></i>
                    <h1>Quiz Dashboard</h1>
                </div>
                <div class="header-subtitle">Real-time Overview • <?php echo number_format($stats['active_exams'] ?? 0); ?> active exams</div>
            </div>
            <!-- Header Stats -->
            <div class="header-actions" style="display:flex; gap:10px;">
                 <div class="stat-pill">
                    <span class="label">QUESTIONS</span>
                    <span class="value"><?php echo number_format($stats['total_questions'] ?? 0); ?></span>
                </div>
                <div class="stat-pill warning">
                    <span class="label">ATTEMPTS</span>
                    <span class="value"><?php echo number_format($stats['total_attempts'] ?? 0); ?></span>
                </div>
            </div>
        </div>

        <!-- Toolbar / Filter Bar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="drag-hint" style="font-size: 0.9rem; color: #64748b;">
                    <i class="fas fa-history"></i> Recent Activity
                </div>
            </div>
            <div class="toolbar-right">
                 <a href="<?php echo app_base_url('admin/quiz/exams/create'); ?>" class="btn-create-premium" style="text-decoration:none;">
                    <i class="fas fa-plus"></i> NEW EXAM
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <?php if (empty($recent_attempts)): ?>
                    <div class="empty-state-compact">
                        <i class="fas fa-chart-line"></i>
                        <h3>No recent activity</h3>
                        <p>Student attempts will appear here automatically.</p>
                    </div>
                <?php else: ?>
                    <table class="table-compact">
                        <thead>
                            <tr>
                                <th style="width: 250px;">Student</th>
                                <th>Exam</th>
                                <th class="text-center" style="width: 100px;">Score</th>
                                <th class="text-center" style="width: 120px;">Status</th>
                                <th class="text-center" style="width: 150px;">Time</th>
                                <th class="text-center" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_attempts as $attempt): ?>
                                <tr class="category-item">
                                    <td>
                                        <div class="item-info">
                                            <div class="item-icon" style="background: #eff6ff; color: #667eea; border-color: #bfdbfe;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?php echo htmlspecialchars($attempt['email']); ?></div>
                                                <div class="item-slug">ID: #<?php echo rand(1000, 9999); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="item-title" style="color: #4b5563;"><?php echo htmlspecialchars($attempt['exam_title']); ?></div>
                                        <div class="item-slug"><i class="fas fa-tag"></i> Assessment</div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="metric-text" style="color: <?php echo $attempt['score'] >= 40 ? '#10b981' : '#ef4444'; ?>;">
                                            <?php echo number_format($attempt['score'], 1); ?>%
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="price-tag" style="justify-content:center; color: <?php echo $attempt['status'] == 'completed' ? '#059669' : '#d97706'; ?>; background: <?php echo $attempt['status'] == 'completed' ? '#ecfdf5' : '#fffbeb'; ?>; padding: 4px 8px; border-radius: 4px;">
                                            <?php echo strtoupper($attempt['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="item-slug"><?php echo date('M d, H:i', strtotime($attempt['started_at'])); ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="actions-compact justify-center">
                                            <button class="action-btn-icon edit-btn" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

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

/* Filter Bar */
.compact-toolbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.75rem 2rem; background: #eff6ff; border-bottom: 1px solid #bfdbfe;
    min-height: 60px;
}

.btn-create-premium {
    height: 36px; padding: 0 1.25rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 600; font-size: 0.8rem; border: none; border-radius: 6px; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.2s;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); white-space: nowrap;
}
.btn-create-premium:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

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
.item-title { font-weight: 600; color: #334155; line-height: 1.2; }
.item-slug { font-size: 0.75rem; color: #94a3b8; font-family: monospace; }
.metric-text { font-weight: 700; color: #64748b; font-size: 0.8rem; }

.action-btn-icon {
    width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px;
    background: white; color: #94a3b8; cursor: pointer; display: flex; align-items: center;
    justify-content: center; transition: 0.2s;
}
.action-btn-icon:hover { transform: translateY(-1px); background: #667eea; color: white; border-color: #667eea; }

.empty-state-compact { padding: 3rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5; }
.empty-state-compact h3 { font-size: 1rem; font-weight: 600; color: #64748b; margin: 0; }
.empty-state-compact p { font-size: 0.8rem; margin: 0; }
</style>
