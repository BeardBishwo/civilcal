<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        
        <!-- Ultra-Compact Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-database"></i>
                    <h1>Question Bank</h1>
                </div>
            </div>
             <!-- Stats in Header (Inline) -->
            <div class="header-actions">
                <div class="stat-pill-row">
                     <span class="pill-item">
                        <span class="lbl">TOTAL</span>
                        <span class="val"><?php echo $stats['total']; ?></span>
                    </span>
                    <span class="pill-divider"></span>
                    <span class="pill-item warning">
                        <span class="lbl">MCQ</span>
                        <span class="val"><?php echo $stats['mcq']; ?></span>
                    </span>
                    <span class="pill-divider"></span>
                     <span class="pill-item info">
                        <span class="lbl">MULTI</span>
                        <span class="val"><?php echo $stats['multi']; ?></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Unified Compact Toolbar (Search + Filters + Actions) -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                 <!-- Search -->
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchQuestions" placeholder="Search questions..." value="<?php echo $search ?? ''; ?>">
                </div>

                <!-- Filters -->
                <select id="filterType" class="filter-compact" style="width: 130px;">
                    <option value="">All Types</option>
                    <option value="MCQ" <?php echo ($type ?? '') === 'MCQ' ? 'selected' : ''; ?>>MCQ</option>
                    <option value="True/False" <?php echo ($type ?? '') === 'True/False' ? 'selected' : ''; ?>>True/False</option>
                    <option value="Multi-Select" <?php echo ($type ?? '') === 'Multi-Select' ? 'selected' : ''; ?>>Multi-Select</option>
                    <option value="Order" <?php echo ($type ?? '') === 'Order' ? 'selected' : ''; ?>>Order</option>
                </select>

                <select id="filterTopic" class="filter-compact" style="width: 200px;">
                    <option value="">All Topics</option>
                    <?php if(!empty($mainCategories)): ?>
                        <?php foreach ($mainCategories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="toolbar-right">
                <!-- Export Dropdown -->
                 <div class="dropdown d-inline-block position-relative">
                    <button class="action-btn-icon" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Export">
                        <i class="fas fa-file-export text-primary"></i>
                    </button>
                    <!-- Enhanced Dropdown: Solid background, Fixed Position, Scrollable -->
                    <ul class="dropdown-menu dropdown-menu-end shadow p-2" aria-labelledby="exportDropdown" 
                        style="position: absolute; right: 0; top: 120%; z-index: 1060; min-width: 240px; border-radius: 12px; background: #ffffff !important; border: 1px solid #e2e8f0; max-height: 320px; overflow-y: auto; transform: none !important; inset: auto !important;">
                        
                        <!-- Sticky Header -->
                        <li style="position: sticky; top: -8px; background: white; z-index: 2; padding-top: 4px; border-bottom: 2px solid #f1f5f9; margin-bottom: 6px;">
                            <h6 class="dropdown-header text-uppercase small fw-bold text-muted mb-1">Backup Scope</h6>
                        </li>

                        <li><a class="dropdown-item rounded-3 mb-1" href="<?= app_base_url('admin/quiz/export?type=all') ?>" target="_blank">
                            <i class="fas fa-archive me-2 text-primary"></i> Full Backup
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php if(!empty($mainCategories)): ?>
                            <?php foreach($mainCategories as $cat): ?>
                                <li><a class="dropdown-item rounded-3 mb-1" href="<?= app_base_url('admin/quiz/export?category_id=' . ($cat['id'] ?? $cat->id)) ?>">
                                    <i class="fas fa-folder me-2 text-warning"></i> <?= htmlspecialchars($cat['title'] ?? $cat->title) ?>
                                </a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><span class="dropdown-item text-muted small">No root categories found</span></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- New Question Button -->
                <a href="<?php echo app_base_url('admin/quiz/questions/create'); ?>" class="btn-create-premium" style="text-decoration: none;">
                    <i class="fas fa-plus"></i> New
                </a>
            </div>
        </div>

        <!-- Questions Content -->
        <div class="pages-content">
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($questions)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-question-circle"></i>
                            <h3>No questions found</h3>
                            <p>Get started by adding your first question to the bank.</p>
                            <a href="<?php echo app_base_url('admin/quiz/questions/create'); ?>" class="btn btn-primary btn-compact">
                                Add Question
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-title" style="width: 45%;">Question</th>
                                        <th class="col-status">Type & Topic</th>
                                        <th class="col-status">Difficulty</th>
                                        <th class="col-status">Answer Key</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($questions as $q): ?>
                                        <?php 
                                            $content = json_decode($q['content'], true); 
                                            $text = strip_tags($content['text'] ?? '');
                                            if (strlen($text) > 80) $text = substr($text, 0, 80) . '...';
                                            
 
                                            // Handling both old and new types display
                                            $typeLabels = [
                                                'MCQ' => ['label' => 'MCQ', 'class' => 'primary', 'icon' => 'dot-circle'],
                                                'TF' => ['label' => 'T/F', 'class' => 'info', 'icon' => 'adjust'],
                                                'MULTI' => ['label' => 'Multi', 'class' => 'warning', 'icon' => 'check-double'],
                                                'ORDER' => ['label' => 'Seq', 'class' => 'danger', 'icon' => 'sort-amount-down'],
                                                'mcq_single' => ['label' => 'MCQ', 'class' => 'primary', 'icon' => 'dot-circle'],
                                                'mcq_multi' => ['label' => 'Multi', 'class' => 'warning', 'icon' => 'check-double'],
                                                'true_false' => ['label' => 'T/F', 'class' => 'info', 'icon' => 'adjust'],
                                                'numerical' => ['label' => 'Num', 'class' => 'white', 'icon' => 'hashtag']
                                            ];
                                            $typeInfo = $typeLabels[$q['type']] ?? ['label' => $q['type'], 'class' => 'secondary', 'icon' => 'question'];
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="page-info">
                                                    <div class="page-title-compact" title="<?php echo htmlspecialchars(strip_tags($content['text'] ?? '')); ?>"><?php echo htmlspecialchars($text); ?></div>
                                                    <div class="page-slug-compact">Code: <?php echo htmlspecialchars((string)($q['unique_code'] ?? '')); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display:flex; flex-direction:column; gap:4px;">
                                                    <span class="status-badge status-active" style="background: var(--admin-gray-100); color: var(--admin-gray-700); width: fit-content;">
                                                        <?php echo htmlspecialchars((string)($typeInfo['label'] ?? 'Unknown')); ?>
                                                    </span>
                                                    <span style="font-size: 11px; color: var(--admin-gray-500);">
                                                        <?php echo htmlspecialchars((string)($q['topic_name'] ?? 'General')); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="difficulty-stars" style="color: var(--admin-warning);">
                                                    <?php for($i=1; $i<=5; $i++): ?>
                                                        <i class="<?php echo $i <= $q['difficulty_level'] ? 'fas' : 'far'; ?> fa-star" style="font-size: 10px;"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Visual Answer Key -->
                                                <?php 
                                                    if ($q['type'] == 'MCQ' || $q['type'] == 'TF' || $q['type'] == 'mcq_single' || $q['type'] == 'true_false') {
                                                        $ans = $q['correct_answer'] ?? '-';
                                                        echo '<span style="font-weight:700; color:var(--admin-success); font-size:12px;">'.strtoupper($ans).'</span>';
                                                    } 
                                                    elseif ($q['type'] == 'MULTI') {
                                                        $json = json_decode($q['correct_answer_json'] ?? '[]', true);
                                                        if (is_array($json)) {
                                                            foreach($json as $k) echo '<span class="status-badge status-active" style="padding:2px 6px; font-size:10px; margin-right:2px;">'.$k.'</span>';
                                                        }
                                                    }
                                                    elseif ($q['type'] == 'ORDER') {
                                                        $json = json_decode($q['correct_answer_json'] ?? '[]', true);
                                                        if (is_array($json)) {
                                                            echo '<div style="display:flex; gap:4px; font-size:10px; color:#64748b; align-items:center;">';
                                                            foreach($json as $idx => $k) {
                                                                echo '<span style="font-weight:700; color:#1e293b;">'.$k.'</span>';
                                                                if($idx < count($json)-1) echo '<i class="fas fa-arrow-right" style="font-size:8px;"></i>';
                                                            }
                                                            echo '</div>';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $q['is_active'] ? 'active' : 'inactive'; ?>">
                                                    <i class="fas fa-<?php echo $q['is_active'] ? 'check-circle' : 'ban'; ?>"></i>
                                                    <?php echo $q['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                    <a href="<?php echo app_base_url('admin/quiz/questions/edit/' . $q['id']); ?>" class="action-btn-icon edit-btn" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="<?php echo app_base_url('admin/quiz/questions/delete/' . $q['id']); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this question?');">
                                                        <button type="submit" class="action-btn-icon delete-btn" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total > $limit): ?>
                            <div class="pagination-compact">
                                <div class="pagination-info">
                                    Showing <?php echo count($questions); ?> of <?php echo $total; ?> entries
                                </div>
                                <div class="pagination-controls">
                                    <?php 
                                        $totalPages = ceil($total / $limit);
                                        $page = $page ?? 1;
                                    ?>
                                    
                                    <a href="?page=<?php echo max(1, $page - 1); ?>&topic_id=<?php echo $_GET['topic_id'] ?? ''; ?>&type=<?php echo $_GET['type'] ?? ''; ?>&search=<?php echo $_GET['search'] ?? ''; ?>" 
                                       class="page-btn <?php echo $page <= 1 ? 'disabled' : ''; ?>"
                                       <?php echo $page <= 1 ? 'onclick="return false;"' : ''; ?>>
                                        <i class="fas fa-chevron-left"></i>
                                    </a>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>&topic_id=<?php echo $_GET['topic_id'] ?? ''; ?>&type=<?php echo $_GET['type'] ?? ''; ?>&search=<?php echo $_GET['search'] ?? ''; ?>" 
                                           class="page-btn <?php echo $i == $page ? 'active' : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <a href="?page=<?php echo min($totalPages, $page + 1); ?>&topic_id=<?php echo $_GET['topic_id'] ?? ''; ?>&type=<?php echo $_GET['type'] ?? ''; ?>&search=<?php echo $_GET['search'] ?? ''; ?>" 
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
    --admin-warning: #f59e0b;
    --admin-success: #10b981;
    --admin-danger: #ef4444;
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
    padding: 0.85rem 1.5rem; /* Reduced padding for compact look */
    background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
    color: white;
}
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.25rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.1rem; opacity: 0.9; }

/* Inline Stats Pill Row */
.stat-pill-row {
    display: flex; align-items: center; background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 4px 12px;
}
.pill-item { display: flex; align-items: center; gap: 6px; }
.pill-item .lbl { font-size: 0.65rem; font-weight: 700; opacity: 0.8; letter-spacing: 0.5px; }
.pill-item .val { font-size: 0.95rem; font-weight: 800; }
.pill-divider { height: 16px; width: 1px; background: rgba(255,255,255,0.3); margin: 0 12px; }

/* Filter Bar - Unified */
.compact-toolbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.75rem 1.5rem; background: #eff6ff; border-bottom: 1px solid #bfdbfe;
    gap: 1rem;
}
.toolbar-left, .toolbar-right { display: flex; align-items: center; gap: 8px; }

.search-compact { position: relative; width: 220px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.8rem; }
.search-compact input {
    width: 100%; height: 34px; padding: 0 0.75rem 0 2.25rem; font-size: 0.85rem;
    border: 1px solid #bfdbfe; border-radius: 6px; outline: none; background: white; color: #1e40af;
}

.filter-compact {
    height: 34px; padding: 0 0.5rem; border: 1px solid #bfdbfe; border-radius: 6px;
    background: white; font-size: 0.8rem; font-weight: 600; color: #1e40af; cursor: pointer;
}

/* Compact Button */
.btn-create-premium {
    height: 34px; padding: 0 1rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 600; font-size: 0.8rem; border: none; border-radius: 6px; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.2s; white-space: nowrap;
}

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
.table-compact tbody tr:hover { background: #f8fafc; }

.page-info .page-title-compact { font-weight: 600; color: #334155; font-size: 0.9rem; line-height: 1.4; }
.page-slug-compact { font-family: monospace; font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

.status-badge {
    padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px;
}
.status-active { background: #ecfdf5; color: #10b981; }
.status-inactive { background: #fef2f2; color: #ef4444; }

/* Actions */
.actions-compact { display: flex; gap: 8px; }
.action-btn-icon {
    width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px;
    background: white; color: #94a3b8; cursor: pointer; display: flex; align-items: center;
    justify-content: center; transition: 0.2s; text-decoration: none;
}
.action-btn-icon:hover { transform: translateY(-1px); }
.edit-btn:hover { background: #667eea; color: white; border-color: #667eea; }
.delete-btn:hover { background: #fee2e2; color: #ef4444; border-color: #fecaca; }

/* Pagination */
.pagination-compact { display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding: 0 2rem; }
.pagination-info { font-size: 0.85rem; font-weight: 600; color: #64748b; }
.pagination-controls { display: flex; gap: 8px; }
.page-btn {
    width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
    border-radius: 6px; background: white; border: 1px solid #e2e8f0;
    color: #64748b; font-weight: 700; text-decoration: none; transition: 0.2s;
}
.page-btn.active { background: #667eea; color: white; border-color: #667eea; }
.page-btn:hover:not(.disabled):not(.active) { background: #f8fafc; border-color: #cbd5e1; }
.page-btn.disabled { opacity: 0.5; cursor: not-allowed; }

/* Font Awesome Reset */
.admin-wrapper-container i.fas, 
.admin-wrapper-container i.fa-solid, 
.admin-wrapper-container i.fa-regular {
    font-family: "Font Awesome 6 Free" !important;
    font-weight: 900 !important;
}

/* Safety Reset for Dropdowns */
/* Safety Reset for Dropdowns */
.dropdown-menu { display: none; list-style: none !important; padding: 0.5rem !important; }
.dropdown-menu.show { display: block; }
.dropdown-menu li { list-style: none !important; }
.dropdown-menu li { list-style: none !important; }

/* Custom Overrides for this page */
.difficulty-stars i { margin-right: 1px; }

</style>
