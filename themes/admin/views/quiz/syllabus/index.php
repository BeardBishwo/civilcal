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
    <div class="syllabus-node position-relative mb-2" style="margin-left: <?= $padding ?>px;">
        <!-- Connector Line (Vertical) -->
        <?php if ($level > 0): ?>
            <div class="position-absolute border-start border-2" style="left: -24px; top: -15px; bottom: 50%; width: 0; border-color: #e3e6f0 !important;"></div>
            <div class="position-absolute border-top border-2" style="left: -24px; top: 50%; width: 20px; border-color: #e3e6f0 !important;"></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm transition-hover">
            <div class="card-body py-2 px-3 d-flex align-items-center">
                <!-- Drag Handle -->
                <div class="me-3 text-gray-300 cursor-move"><i class="bi bi-grip-vertical"></i></div>

                <!-- Icon -->
                <div class="me-3 fs-5 <?= $color ?>"><i class="bi <?= $icon ?>"></i></div>

                <!-- Content -->
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <span class="fw-bold text-dark me-2"><?= htmlspecialchars($node['title']) ?></span>
                        
                        <?php if ($node['is_premium']): ?>
                            <span class="badge bg-warning text-dark border border-warning-subtle rounded-pill me-1" title="Premium">
                                <i class="bi bi-gem"></i>
                            </span>
                        <?php endif; ?>
                        
                        <small class="text-muted ms-2 bg-light px-2 rounded-pill border">
                            Q: <?= $node['question_count'] ?? 0 ?>
                        </small>
                    </div>
                    <?php if(!empty($node['slug'])): ?>
                        <div class="text-xs text-muted font-monospace"><?= $node['slug'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="ms-3 op-0 hover-op-100">
                    <button class="btn btn-sm btn-light text-primary"><i class="bi bi-pencil"></i></button>
                    <!-- Add Child Button based on level logic could go here -->
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

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-diagram-3-fill text-primary me-2"></i> Syllabus Master
        </h1>
        <div>
            <span class="badge bg-white text-secondary border shadow-sm p-2"><i class="bi bi-layers me-1"></i> Total Nodes: <?= $stats['nodes'] ?></span>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            
            <?php if (empty($tree)): ?>
                <div class="text-center py-5">
                    <div class="mb-3 text-gray-300"><i class="bi bi-tree fs-1"></i></div>
                    <h5 class="text-muted">Syllabus Tree is Empty</h5>
                    <a href="<?= app_base_url('admin/quiz/categories') ?>" class="btn btn-primary mt-3">Create Root Category</a>
                </div>
            <?php else: ?>
                <div class="syllabus-tree-container py-3">
                    <?php foreach ($tree as $rootNode): ?>
                        <?= renderNodeRow($rootNode) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
    .transition-hover { transition: all 0.2s; }
    .transition-hover:hover { transform: translateX(5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
    .op-0 { opacity: 0; transition: opacity 0.2s; }
    .syllabus-node:hover .op-0 { opacity: 1; }
</style>
