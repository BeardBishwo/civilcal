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
                    <button class="btn btn-white text-success fw-bold shadow-sm rounded-pill px-3 dropdown-toggle border" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-file-export me-2"></i> Export
                    </button>
                    <ul class="dropdown-menu shadow border-0" style="border-radius: 12px;">
                        <li><h6 class="dropdown-header text-uppercase small fw-bold">Select Scope</h6></li>
                        <li><a class="dropdown-item" href="/admin/quiz/export?type=all" target="_blank">Full Backup (All Questions)</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php if(isset($mainCategories) && is_array($mainCategories)): ?>
                            <?php foreach($mainCategories as $cat): ?>
                                <li><a class="dropdown-item" href="/admin/quiz/export?category_id=<?= $cat->id ?>">Only <?= $cat->title ?></a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><span class="dropdown-item text-muted">Categories not loaded</span></li>
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
