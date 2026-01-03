<?php
// themes/admin/views/quiz/syllabus/index.php

function renderNodeRow($node, $level = 0) {
    $padding = $level * 40;
    
    // Icon based on type or level
    $icon = 'bi-circle';
    $color = 'text-secondary';
    if ($level == 0) { $icon = 'bi-folder-fill'; $color = 'text-primary'; }
    elseif ($level == 1) { $icon = 'bi-folder2-open'; $color = 'text-info'; }
    elseif ($level == 2) { $icon = 'bi-bookmarks'; $color = 'text-success'; }
    elseif ($level >= 3) { $icon = 'bi-file-text'; $color = 'text-muted'; }

    $hasChildren = !empty($node['children']);
    ?>
    <div class="syllabus-node position-relative mb-3" style="margin-left: <?= $padding ?>px;">
        <!-- Connector Line (Vertical & Horizontal) -->
        <?php if ($level > 0): ?>
            <div class="position-absolute border-start border-2" style="left: -20px; top: -20px; bottom: 50%; width: 0; border-color: #dee2e6 !important;"></div>
            <div class="position-absolute border-top border-2" style="left: -20px; top: 50%; width: 20px; border-color: #dee2e6 !important;"></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm transition-hover blueprint-card overflow-hidden" 
             style="border-radius: 10px; border-left: 4px solid <?= $level == 0 ? 'var(--admin-primary)' : ($level == 1 ? 'var(--admin-info)' : 'var(--admin-gray-300)') ?> !important;">
            <div class="card-body py-3 px-4 d-flex align-items-center">
                <!-- Drag Handle -->
                <div class="me-3 text-gray-300 cursor-move handle"><i class="bi bi-grip-vertical fs-5"></i></div>

                <!-- Icon -->
                <div class="me-3 fs-4 <?= $color ?> d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(var(--admin-primary-rgb), 0.05); border-radius: 8px;">
                    <i class="bi <?= $icon ?>"></i>
                </div>

                <!-- Content -->
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-0">
                        <span class="fw-bold text-dark fs-6 me-2"><?= htmlspecialchars($node['title']) ?></span>
                        
                        <?php if ($node['is_premium']): ?>
                            <span class="badge bg-warning text-dark border border-warning-subtle rounded-pill me-2 px-2 py-1" style="font-size: 10px;">
                                <i class="bi bi-gem me-1"></i> PREMIUM
                            </span>
                        <?php endif; ?>
                        
                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 10px;">
                            <?= $node['question_count'] ?? 0 ?> Qs
                        </span>
                    </div>
                </div>

                <!-- Actions (Visible on Hover) -->
                <div class="ms-3 node-actions opacity-0 transition-all">
                    <a href="<?= app_base_url('admin/quiz/categories/edit/'.$node['id']) ?>" class="btn btn-sm btn-white border shadow-sm rounded-circle"><i class="bi bi-pencil-fill text-primary"></i></a>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Recursion
    if ($hasChildren) {
        foreach ($node['children'] as $child) {
            renderNodeRow($child, $level + 1);
        }
    }
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="bi bi-diagram-3-fill text-primary me-2"></i> Syllabus Master
            </h1>
            <p class="text-muted small mb-0">Hierarchical overview of all Exam Streams & Units.</p>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <div class="text-end">
                <div class="h5 mb-0 fw-bold"><?= $stats['nodes'] ?></div>
                <div class="text-xs text-uppercase text-muted fw-bold">Total Nodes</div>
            </div>
            <a href="<?= app_base_url('admin/quiz/categories') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-gear-fill me-2"></i> Manage Root
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            
            <?php if (empty($tree)): ?>
                <div class="card border-0 shadow-sm py-5 text-center">
                    <div class="card-body">
                        <div class="mb-3 text-light"><i class="bi bi-tree-fill" style="font-size: 80px;"></i></div>
                        <h4 class="text-dark fw-bold">Your Syllabus Tree is Empty</h4>
                        <p class="text-muted">Start building your educational structure by defining main subjects.</p>
                        <a href="<?= app_base_url('admin/quiz/categories') ?>" class="btn btn-primary mt-3 px-5 rounded-pill">Define Root Category</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="syllabus-tree-container ps-2">
                    <?php foreach ($tree as $rootNode): ?>
                        <?= renderNodeRow($rootNode) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
    .transition-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .blueprint-card:hover { transform: translateX(8px); box-shadow: 0 10px 20px rgba(0,0,0,0.08)!important; }
    .blueprint-card:hover .node-actions { opacity: 1 !important; transform: translateX(0); }
    .node-actions { transform: translateX(10px); }
    .syllabus-node::before {
        content: '';
        position: absolute;
        left: -20px;
        top: 0;
        bottom: 0;
        border-left: 2px dashed #dee2e6;
        display: none; /* Can be enabled for deep vertical lines */
    }
    .syllabus-tree-container {
        border-left: 2px solid #f8f9fc;
        padding-left: 10px;
    }
</style>
