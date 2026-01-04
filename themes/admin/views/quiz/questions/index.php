<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        
        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-database"></i>
                    <h1>Question Bank</h1>
                </div>
                <div class="header-subtitle">
                    <?php if(!empty($stats)): ?>
                        <span class="badge bg-white text-dark border me-2"><?= $stats['total'] ?> Total</span>
                        <span class="badge bg-opacity-10 bg-warning text-warning border-warning border me-2"><?= $stats['multi'] ?> Multi</span>
                        <span class="badge bg-opacity-10 bg-danger text-danger border-danger border"><?= $stats['order'] ?> Order</span>
                    <?php else: ?>
                        <?php echo $total; ?> questions available
                    <?php endif; ?>
                </div>
            </div>
            <div class="header-actions">
                <div class="dropdown d-inline-block me-2">
                    <button class="btn btn-white text-success fw-bold shadow-sm rounded-pill px-3 dropdown-toggle border" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-export me-2"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" aria-labelledby="exportDropdown" style="border-radius: 12px; min-width: 220px; z-index: 1060;">
                        <li><h6 class="dropdown-header text-uppercase small fw-bold text-muted mb-2">Select Scope</h6></li>
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

                <a href="<?php echo app_base_url('admin/quiz/questions/create'); ?>" class="btn btn-primary btn-compact">
                    <i class="fas fa-plus"></i>
                    <span>New Question</span>
                </a>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <form method="GET" action="<?php echo app_base_url('admin/quiz/questions'); ?>" class="d-flex align-items-center gap-2" id="filter-form">
                    <div class="search-compact">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search questions..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>
                    
                    <select name="type" class="filter-compact" onchange="document.getElementById('filter-form').submit()">
                        <option value="">All Types</option>
                        <option value="MCQ" <?= ($_GET['type']??'')=='MCQ'?'selected':'' ?>>Standard MCQ</option>
                        <option value="TF" <?= ($_GET['type']??'')=='TF'?'selected':'' ?>>True / False</option>
                        <option value="MULTI" <?= ($_GET['type']??'')=='MULTI'?'selected':'' ?>>☑ Multi-Select</option>
                        <option value="ORDER" <?= ($_GET['type']??'')=='ORDER'?'selected':'' ?>>🔃 Sequence</option>
                    </select>

                    <select name="topic_id" class="filter-compact" onchange="document.getElementById('filter-form').submit()">
                        <option value="">All Topics</option>
                        <!-- Topics would be populated here -->
                    </select>

                    <?php if (!empty($_GET['search']) || !empty($_GET['type']) || !empty($_GET['topic_id'])): ?>
                        <a href="<?php echo app_base_url('admin/quiz/questions'); ?>" class="btn btn-sm btn-outline-secondary" style="height: 38px; display: flex; align-items: center;">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="toolbar-right">
                <!-- View controls could go here -->
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
   PREMIUM ARCHITECT DESIGN SYSTEM
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-gray-50: #f8fbff;
    --admin-gray-100: #f1f5f9;
    --admin-gray-200: #e2e8f0;
    --admin-gray-300: #cbd5e1;
    --admin-gray-400: #94a3b8;
    --admin-gray-600: #475569;
    --admin-gray-800: #1e293b;
    --admin-warning: #f59e0b;
    --admin-success: #10b981;
    --admin-danger: #ef4444;
}

/* Base Font Override */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
.admin-wrapper-container *:not(i):not([class*="fa-"]) {
    font-family: 'Outfit', sans-serif !important;
}

/* Container & Layout */
.admin-wrapper-container { padding: 1.5rem; background: var(--admin-gray-50); min-height: calc(100vh - 70px); }
.admin-content-wrapper { background: white; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; padding-bottom: 2rem; border: 1px solid var(--admin-gray-200); }

/* Header Architect */
.compact-header { 
    display: flex; justify-content: space-between; align-items: center; padding: 2rem 2.5rem; 
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border-bottom: 4px solid rgba(0,0,0,0.1);
}
.header-left .header-title { display: flex; align-items: center; gap: 1rem; }
.header-title h1 { margin: 0; font-size: 1.85rem; font-weight: 800; color: white; letter-spacing: -0.5px; }
.header-title i { font-size: 1.5rem; opacity: 0.9; }
.header-subtitle { display: flex; align-items: center; margin-top: 8px; gap: 6px; }

/* Header Actions & Dropdown */
.header-actions { display: flex; align-items: center; gap: 12px; }
.header-actions ul { list-style: none !important; margin: 0; padding: 0.5rem !important; }
.header-actions li { list-style: none !important; }

.btn-compact {
    height: 42px; padding: 0 1.5rem; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);
    color: white; font-weight: 700; border-radius: 10px; display: inline-flex; align-items: center; gap: 8px;
    transition: 0.2s; cursor: pointer; text-decoration: none;
}
.btn-compact:hover { background: white; color: #4f46e5; transform: translateY(-1px); }
.btn-primary.btn-compact { background: white; color: #4f46e5; border: none; }

/* Filter Bar */
.compact-toolbar { 
    display: flex; justify-content: space-between; align-items: center; padding: 1rem 2.5rem; 
    background: #f8fafc; border-bottom: 1px solid var(--admin-gray-200);
}
.search-compact { position: relative; width: 300px; }
.search-compact i { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--admin-gray-400); }
.search-compact input {
    width: 100%; height: 40px; padding: 0 1rem 0 2.5rem; font-size: 0.9rem;
    border: 1px solid var(--admin-gray-300); border-radius: 10px; outline: none; transition: 0.2s;
}
.search-compact input:focus { border-color: var(--admin-primary); box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); }

.filter-compact {
    height: 40px; padding: 0 1rem; border: 1px solid var(--admin-gray-300); border-radius: 10px;
    background: white; font-size: 0.85rem; font-weight: 600; color: var(--admin-gray-600); cursor: pointer;
}

/* Table Architecture */
.table-container { padding: 1.5rem 2.5rem; }
.table-wrapper { border: 1px solid var(--admin-gray-200); border-radius: 12px; overflow: hidden; }
.table-compact { width: 100%; border-collapse: collapse; background: white; }
.table-compact th {
    background: #f1f5f9; padding: 12px 20px; font-size: 0.75rem; font-weight: 800;
    color: var(--admin-gray-400); text-transform: uppercase; letter-spacing: 1px; text-align: left;
}
.table-compact td { padding: 14px 20px; border-bottom: 1px solid #f8fafc; vertical-align: middle; }

.page-info .page-title-compact { font-weight: 700; color: var(--admin-gray-800); font-size: 1rem; line-height: 1.4; }
.page-slug-compact { font-family: monospace; font-size: 0.75rem; color: var(--admin-gray-400); margin-top: 2px; font-weight: 600; }

.status-badge {
    padding: 6px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 800;
    text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px;
}
.status-active { background: #ecfdf5; color: #10b981; }
.status-inactive { background: #fef2f2; color: #ef4444; }

/* Actions */
.actions-compact { display: flex; gap: 8px; }
.action-btn-icon {
    width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--admin-gray-200);
    background: white; color: var(--admin-gray-400); cursor: pointer; display: flex;
    align-items: center; justify-content: center; transition: 0.2s; text-decoration: none;
}
.action-btn-icon:hover { transform: translateY(-2px); border-color: var(--admin-primary); color: var(--admin-primary); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.delete-btn:hover { border-color: var(--admin-danger); color: var(--admin-danger); }

/* Pagination */
.pagination-compact { display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding: 0 0.5rem; }
.pagination-info { font-size: 0.85rem; font-weight: 600; color: var(--admin-gray-400); }
.pagination-controls { display: flex; gap: 8px; }
.page-btn {
    width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
    border-radius: 10px; background: white; border: 1px solid var(--admin-gray-200);
    color: var(--admin-gray-600); font-weight: 700; text-decoration: none; transition: 0.2s;
}
.page-btn.active { background: var(--admin-primary); color: white; border-color: var(--admin-primary); }
.page-btn:hover:not(.disabled):not(.active) { background: #f8fafc; border-color: var(--admin-gray-300); }
.page-btn.disabled { opacity: 0.5; cursor: not-allowed; }

/* Font Awesome Reset */
.admin-wrapper-container i.fas, 
.admin-wrapper-container i.fa-solid, 
.admin-wrapper-container i.fa-regular {
    font-family: "Font Awesome 6 Free" !important;
    font-weight: 900 !important;
}
</style>
