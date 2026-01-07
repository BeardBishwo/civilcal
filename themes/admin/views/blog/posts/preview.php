<?php
/**
 * PREMIUM BLOG POST PREVIEW
 * Preview auto-generated blog post before publishing
 */
$post = $post ?? [];
$questions = $questions ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-eye"></i>
                    <h1>Preview: <?php echo htmlspecialchars($post['title']); ?></h1>
                </div>
                <div class="header-subtitle"><?php echo count($questions); ?> Questions • <?php echo ucfirst($post['type']); ?> Post</div>
            </div>
            <div class="header-actions" style="display:flex; gap:10px;">
                <a href="<?php echo app_base_url('admin/blog/posts'); ?>" class="btn-secondary-compact">
                    <i class="fas fa-arrow-left"></i> Back to Posts
                </a>
                <a href="<?php echo app_base_url('blog/' . $post['slug']); ?>" 
                   target="_blank" 
                   class="btn-create-premium">
                    <i class="fas fa-external-link-alt"></i> VIEW LIVE
                </a>
            </div>
        </div>

        <!-- Post Info Card -->
        <div class="table-container">
            <div class="content-card-premium">
                <h5 class="card-title-premium">
                    <i class="fas fa-info-circle"></i> Post Information
                </h5>
                
                <div class="info-grid-premium">
                    <div class="info-item-premium">
                        <span class="info-label">Type</span>
                        <span class="info-value">
                            <?php 
                            $typeIcons = [
                                'popular' => 'fa-fire',
                                'category' => 'fa-folder',
                                'difficulty' => 'fa-chart-line',
                                'recent' => 'fa-clock',
                                'featured' => 'fa-star'
                            ];
                            ?>
                            <i class="fas <?php echo $typeIcons[$post['type']] ?? 'fa-file'; ?>"></i>
                            <?php echo ucfirst($post['type']); ?>
                        </span>
                    </div>
                    
                    <div class="info-item-premium">
                        <span class="info-label">Questions</span>
                        <span class="info-value"><?php echo count($questions); ?></span>
                    </div>
                    
                    <div class="info-item-premium">
                        <span class="info-label">Views</span>
                        <span class="info-value"><?php echo number_format($post['view_count']); ?></span>
                    </div>
                    
                    <div class="info-item-premium">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <?php if ($post['is_published']): ?>
                                <span class="status-badge status-active">
                                    <i class="fas fa-check-circle"></i> Published
                                </span>
                            <?php else: ?>
                                <span class="status-badge status-inactive">
                                    <i class="fas fa-clock"></i> Draft
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="info-item-premium" style="grid-column: 1 / -1;">
                        <span class="info-label">Public URL</span>
                        <span class="info-value">
                            <code class="url-code"><?php echo app_base_url('blog/' . $post['slug']); ?></code>
                        </span>
                    </div>
                    
                    <div class="info-item-premium">
                        <span class="info-label">Created</span>
                        <span class="info-value"><?php echo date('F d, Y H:i', strtotime($post['created_at'])); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Introduction -->
        <?php if (!empty($post['introduction'])): ?>
        <div class="table-container">
            <div class="content-card-premium">
                <h5 class="card-title-premium">
                    <i class="fas fa-align-left"></i> Introduction
                </h5>
                <div class="content-text-premium">
                    <?php echo nl2br(htmlspecialchars($post['introduction'])); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Questions List -->
        <div class="table-container">
            <div class="content-card-premium">
                <h5 class="card-title-premium">
                    <i class="fas fa-list"></i> Questions (<?php echo count($questions); ?>)
                </h5>
                
                <div class="questions-preview-list">
                    <?php foreach ($questions as $index => $q): ?>
                        <?php $content = json_decode($q['content'], true); ?>
                        <div class="question-preview-item">
                            <div class="question-number-badge"><?php echo $index + 1; ?></div>
                            <div class="question-preview-content">
                                <div class="question-preview-text">
                                    <?php echo htmlspecialchars($content['text'] ?? ''); ?>
                                </div>
                                <div class="question-preview-meta">
                                    <span class="meta-badge">
                                        <i class="fas fa-award"></i> <?php echo $q['default_marks']; ?> marks
                                    </span>
                                    <span class="meta-badge">
                                        <i class="fas fa-signal"></i> <?php echo ['Easy', 'Easy-Mid', 'Medium', 'Hard', 'Expert'][$q['difficulty_level'] - 1]; ?>
                                    </span>
                                    <?php if ($q['type'] === 'THEORY'): ?>
                                        <span class="meta-badge">
                                            <i class="fas fa-file-alt"></i> <?php echo $q['theory_type'] == 'short' ? 'Short' : 'Long'; ?> Answer
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Conclusion -->
        <?php if (!empty($post['conclusion'])): ?>
        <div class="table-container">
            <div class="content-card-premium">
                <h5 class="card-title-premium">
                    <i class="fas fa-flag-checkered"></i> Conclusion
                </h5>
                <div class="content-text-premium">
                    <?php echo nl2br(htmlspecialchars($post['conclusion'])); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<style>
.btn-secondary-compact {
    padding: 0.5rem 1rem;
    background: #f1f5f9;
    color: #475569;
    border: none;
    border-radius: 6px;
    font-size: 0.813rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-secondary-compact:hover {
    background: #e2e8f0;
}

.content-card-premium {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.card-title-premium {
    font-size: 0.875rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-title-premium i {
    color: #6366f1;
}

.info-grid-premium {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
}

.info-item-premium {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-label {
    font-size: 0.688rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 0.875rem;
    color: #1e293b;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.url-code {
    background: #f1f5f9;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    color: #6366f1;
    font-family: 'Courier New', monospace;
}

.content-text-premium {
    font-size: 0.875rem;
    line-height: 1.7;
    color: #475569;
}

.questions-preview-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.question-preview-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 3px solid #6366f1;
}

.question-number-badge {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.question-preview-content {
    flex: 1;
}

.question-preview-text {
    font-size: 0.875rem;
    color: #1e293b;
    margin-bottom: 0.75rem;
    line-height: 1.5;
}

.question-preview-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.meta-badge {
    background: white;
    color: #64748b;
    padding: 0.25rem 0.625rem;
    border-radius: 4px;
    font-size: 0.688rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fef3c7;
    color: #92400e;
}
</style>
