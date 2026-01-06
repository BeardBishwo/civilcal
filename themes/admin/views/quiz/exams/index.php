<?php
/**
 * PREMIUM EXAM MANAGER
 * Professional, high-density layout with integrated creation form.
 */
$exams = $exams ?? [];
$total = $total ?? 0;
$limit = 20;
$page = $page ?? 1;

// Calculate Stats (Optional - calculated from current page data for visual fill)
$stats = [
    'total' => $total,
    'published' => count(array_filter($exams, fn($e) => $e['status'] === 'published')),
    'draft' => count(array_filter($exams, fn($e) => $e['status'] === 'draft'))
];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-file-signature"></i>
                    <h1>Exam Manager</h1>
                </div>
                <div class="header-subtitle"><?php echo $total; ?> Total Exams • <?php echo $stats['published']; ?> Published</div>
            </div>
            <!-- Stats in Header -->
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">PUBLISHED</span>
                    <span class="value"><?php echo $stats['published']; ?></span>
                </div>
                <div class="stat-pill warning">
                    <span class="label">DRAFTS</span>
                    <span class="value"><?php echo $stats['draft']; ?></span>
                </div>
            </div>
        </div>

        <!-- Single Row Creation Toolbar -->
        <div class="creation-toolbar">
            <h5 class="toolbar-title">Quick Create Exam</h5>
            <form id="addExamForm" class="creation-form">
                
                <!-- Title Input -->
                <div class="input-group-premium" style="flex: 3; min-width: 250px;">
                    <i class="fas fa-heading icon"></i>
                    <input type="text" name="title" class="form-input-premium" placeholder="Exam Title" required>
                </div>

                <!-- Type Select -->
                <div class="input-group-premium" style="flex: 1; min-width: 120px;">
                    <select name="type" class="form-input-premium" style="padding-left: 0.75rem;">
                        <option value="practice">Practice</option>
                        <option value="exam">Exam</option>
                        <option value="mock">Mock Test</option>
                    </select>
                </div>

                <!-- Duration Input -->
                <div class="input-group-premium" style="flex: 1; min-width: 100px;">
                    <i class="fas fa-clock icon"></i>
                    <input type="number" name="duration_minutes" class="form-input-premium" placeholder="Mins" min="0">
                </div>

                <!-- Marks Input -->
                <div class="input-group-premium" style="flex: 1; min-width: 100px;">
                    <i class="fas fa-star icon"></i>
                    <input type="number" name="total_marks" class="form-input-premium" placeholder="Marks" min="0">
                </div>
                
                <input type="hidden" name="status" value="draft">

                <button type="button" onclick="saveExam()" class="btn-create-premium">
                    <i class="fas fa-plus"></i> CREATE
                </button>
            </form>
        </div>

        <!-- Filter & Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="filter-group">
                    <span class="filter-label">FILTER:</span>
                    <form method="GET" style="margin:0; display:flex; gap:10px; flex-wrap: wrap;" id="filter-form">
                        <select name="status" class="filter-select w-32" onchange="this.form.submit()">
                            <option value="">Status</option>
                            <option value="published" <?php echo ($_GET['status'] ?? '') == 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo ($_GET['status'] ?? '') == 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="archived" <?php echo ($_GET['status'] ?? '') == 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>

                        <select name="course_id" class="select2-filter w-40" data-placeholder="Course">
                            <option value="">All Courses</option>
                            <?php foreach($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo ($_GET['course_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="education_level_id" class="select2-filter w-40" data-placeholder="Level">
                            <option value="">All Levels</option>
                            <?php foreach($educationLevels as $l): ?>
                                <option value="<?php echo $l['id']; ?>" <?php echo ($_GET['education_level_id'] ?? '') == $l['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($l['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="position_level_id" class="select2-filter w-40" data-placeholder="Position">
                            <option value="">All Positions</option>
                            <?php foreach($positionLevels as $p): ?>
                                <option value="<?php echo $p['id']; ?>" <?php echo ($_GET['position_level_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div class="search-compact">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" placeholder="Search exams..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        </div>
                        <button type="submit" class="hidden"></button>
                    </form>
                </div>
            </div>
            <div class="toolbar-right">
                <!-- No drag handle logic for exams yet -->
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 60px;" class="text-center">ID</th>
                            <th>Exam Info</th>
                            <th class="text-center" style="width: 150px;">Configuration</th>
                            <th class="text-center" style="width: 100px;">Status</th>
                            <th class="text-center" style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($exams)): ?>
                            <tr><td colspan="5" class="empty-cell">
                                <div class="empty-state-compact">
                                    <i class="fas fa-file-signature"></i>
                                    <h3>No exams found</h3>
                                    <p>Create a new exam using the form above.</p>
                                </div>
                            </td></tr>
                        <?php else: ?>
                            <?php foreach ($exams as $exam): ?>
                                <tr class="exam-item group">
                                    <td class="text-center">
                                        <span class="order-idx" style="color:#94a3b8; font-weight:700;"><?php echo $exam['id']; ?></span>
                                    </td>
                                    <td>
                                        <div class="item-info">
                                            <div class="item-icon" style="background: <?php echo $exam['is_premium'] ? '#fff7ed' : '#f1f5f9'; ?>; color: <?php echo $exam['is_premium'] ? '#ea580c' : '#94a3b8'; ?>; border-color: <?php echo $exam['is_premium'] ? '#ffedd5' : '#e2e8f0'; ?>;">
                                                <i class="fas <?php echo $exam['is_premium'] ? 'fa-crown' : 'fa-graduation-cap'; ?>"></i>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title">
                                                    <?php echo htmlspecialchars($exam['title']); ?>
                                                </div>
                                                <div class="item-slug">
                                                    <?php echo ucfirst($exam['type']); ?> • <?php echo ucfirst($exam['mode']); ?>
                                                    <?php if($exam['price'] > 0): ?>
                                                        <span style="color:#16a34a; margin-left:5px;"><i class="fas fa-coins" style="font-size:10px;"></i> <?php echo $exam['price']; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="config-badges">
                                            <span class="badge-pill" title="Duration">
                                                <i class="fas fa-clock" style="margin-right:3px;"></i> <?php echo $exam['duration_minutes'] > 0 ? $exam['duration_minutes'].'m' : '∞'; ?>
                                            </span>
                                            <span class="badge-pill" title="Total Marks">
                                                <i class="fas fa-star" style="margin-right:3px;"></i> <?php echo $exam['total_marks']; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            $sClass = 'secondary';
                                            if($exam['status'] == 'published') $sClass = 'success';
                                            elseif($exam['status'] == 'draft') $sClass = 'warning';
                                        ?>
                                        <span class="status-dot <?php echo $sClass; ?>">
                                            <?php echo ucfirst($exam['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="actions-compact justify-center">
                                            <a href="<?php echo app_base_url('admin/quiz/exams/builder/' . $exam['id']); ?>" class="action-btn-icon" title="Question Builder">
                                                <i class="fas fa-layer-group" style="color:#6366f1;"></i>
                                            </a>
                                            <a href="<?php echo app_base_url('admin/quiz/exams/edit/' . $exam['id']); ?>" class="action-btn-icon" title="Settings">
                                                <i class="fas fa-cog" style="color:#64748b;"></i>
                                            </a>
                                            <!-- Delete functionality would go here if controller supported it via API -->
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total > $limit): ?>
                <div class="pagination-compact">
                    <div class="pagination-info">Using pagination...</div>
                    <div class="pagination-controls">
                        <?php 
                            $totalPages = ceil($total / $limit); 
                            $qStr = "status=".($_GET['status']??'')."&search=".($_GET['search']??'');
                        ?>
                        <a href="?page=<?php echo max(1, $page - 1); ?>&<?php echo $qStr; ?>" class="page-btn <?php echo $page <= 1 ? 'disabled' : ''; ?>"><i class="fas fa-chevron-left"></i></a>
                        
                        <?php for($i=1; $i<=$totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&<?php echo $qStr; ?>" class="page-btn <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <a href="?page=<?php echo min($totalPages, $page + 1); ?>&<?php echo $qStr; ?>" class="page-btn <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"><i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
async function saveExam() {
    const form = document.getElementById('addExamForm');
    const title = form.querySelector('input[name="title"]').value;
    
    if(!title) { Swal.fire({ icon:'warning', title:'Missing Info', text:'Exam Title is required.', timer:2000, showConfirmButton: false}); return; }

    const formData = new FormData(form);
    try {
        const response = await fetch('<?php echo app_base_url('admin/quiz/exams/store'); ?>', { method: 'POST', body: formData });
        const d = await response.json();
        if(d.success) {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
            Toast.fire({ icon: 'success', title: 'Exam Created!' }).then(() => {
                if(d.redirect) window.location.href = d.redirect;
                else location.reload();
            });
        } else {
            Swal.fire('Error', d.error || 'Failed to create exam', 'error');
        }
    } catch(e) { Swal.fire('Error', 'Server Error', 'error'); }
}
</script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container .select2-selection--single {
        height: 32px !important; /* Compact functionality for filters */
        background-color: white !important;
        border-color: #93c5fd !important;
        border-radius: 6px !important;
        padding-top: 2px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px !important;
    }
    .compact-toolbar .select2-container { margin-right: 5px; }
</style>

<script>
    $(document).ready(function() {
        $('.select2-filter').select2({
            width: 'style'
        });
        
        // Auto-submit on change
        $('.select2-filter').on('change', function() {
            $('#filter-form').submit();
        });
    });
</script>

<style>
/* ========================================
   PREMIUM CORE STYLES (Standardized)
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

.admin-wrapper-container { padding: 1rem; background: var(--admin-gray-50); min-height: calc(100vh - 70px); }
.admin-content-wrapper { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; /* padding-bottom: 2rem; REMOVED FOR CLEANER UI */ }

/* Header */
.compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); color: white; }
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.25rem; opacity: 0.9; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 0.5rem 1rem; display: flex; flex-direction: column; align-items: center; min-width: 80px; }
.stat-pill.warning { background: rgba(252, 211, 77, 0.15); border-color: rgba(252, 211, 77, 0.3); }
.stat-pill .label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

.creation-toolbar { padding: 1rem 2rem; background: #f8fafc; border-bottom: 1px solid var(--admin-gray-200); }
.toolbar-title { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.75rem; }
.creation-form { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }

.input-group-premium { position: relative; }
.input-group-premium .icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; pointer-events: none; }
.form-input-premium { width: 100%; height: 40px; padding: 0 0.75rem 0 2.25rem; font-size: 0.875rem; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: all 0.2s; background: white; color: #334155; }
.form-input-premium:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }

.btn-create-premium { height: 40px; padding: 0 1.5rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: white; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.2s; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); white-space: nowrap; }
.btn-create-premium:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

.compact-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 2rem; background: #eff6ff; border-bottom: 1px solid #bfdbfe; }
.filter-group { display: flex; align-items: center; gap: 0.75rem; }
.filter-label { font-size: 0.7rem; font-weight: 700; color: #1e40af; letter-spacing: 0.5px; }
.filter-select { font-size: 0.85rem; font-weight: 600; color: #1e40af; border: 1px solid #93c5fd; border-radius: 6px; padding: 0.25rem 2rem 0.25rem 0.5rem; background: white; outline: none; height: 32px; }
.search-compact { position: relative; width: 100%; max-width: 300px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.85rem; }
.search-compact input { width: 100%; height: 36px; padding: 0 0.75rem 0 2.25rem; font-size: 0.85rem; border: 1px solid #bfdbfe; border-radius: 6px; outline: none; background: white; color: #1e40af; }

.table-compact { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.table-compact th { background: white; padding: 0.75rem 1.5rem; text-align: left; font-weight: 600; color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
.table-compact td { padding: 0.6rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.exam-item:hover { background: #f8fafc; }

.item-info { display: flex; align-items: center; gap: 0.75rem; }
.item-icon { width: 36px; height: 36px; border-radius: 8px; background: #f1f5f9; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden; color: #94a3b8; }
.item-title { font-weight: 600; color: #334155; line-height: 1.2; }
.item-slug { font-size: 0.75rem; color: #94a3b8; }

.badge-pill { background: #e0e7ff; color: #4338ca; padding: 2px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 700; border: 1px solid #c7d2fe; white-space: nowrap; margin-right: 4px; }
.config-badges { display: flex; gap: 4px; flex-wrap: wrap; justify-content: center; }

.status-dot { font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 10px; display: inline-flex; align-items: center; gap: 4px; }
.status-dot.success { background: #dcfce7; color: #166534; }
.status-dot.warning { background: #fef9c3; color: #854d0e; }
.status-dot.secondary { background: #f1f5f9; color: #64748b; }

.action-btn-icon { width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; margin: 0 2px; }
.action-btn-icon:hover { transform: translateY(-1px); border-color: #cbd5e1; }

.pagination-compact { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; border-top: 1px solid #e2e8f0; }
.pagination-info { font-size: 0.8rem; color: #64748b; }
.pagination-controls { display: flex; gap: 4px; }
.page-btn { min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; border-radius: 6px; background: white; color: #64748b; font-size: 0.8rem; cursor: pointer; text-decoration: none; }
.page-btn.active { background: #4f46e5; color: white; border-color: #4f46e5; }
.page-btn.disabled { opacity: 0.5; pointer-events: none; }

.empty-state-compact { padding: 3rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5; }
.empty-state-compact h3 { font-size: 1rem; font-weight: 600; color: #64748b; margin: 0; }
.empty-state-compact p { font-size: 0.8rem; margin: 0; }

@media (max-width: 1024px) {
    .creation-form { flex-direction: column; align-items: stretch; }
    .input-group-premium { width: 100% !important; }
}
</style>
